<?php

require_once $_SERVER['DOCUMENT_ROOT']."/config.php";
require_once $_SERVER['DOCUMENT_ROOT']."/functions.php";

$update = file_get_contents('php://input');

$update = json_decode($update,TRUE);

$chatId = $update["message"]["chat"]["id"];

$message = $update["message"]["text"];

if ($message === '/start') {
    send_answer(Config::$textBeforeEnterPrice,$chatId);
    header("HTTP/1.1 200 OK");
    exit;
} else {
    $dataForApi = [
        'amount'            => $message,
        'merchant_order_id' => 'optional',
        'api_key'           => Config::$apiSecret,
        'notice_url'        => "https://".$_SERVER['SERVER_NAME']."/action/callback.php",
    ];

    try {
        send_answer("\xE2\x8C\x9B"."Ожидаем реквизиты...", $chatId);

        $resultOrderData = json_decode(getRequisitesEpay($dataForApi),true);

        if (!empty($resultOrderData['error_desc'])) {
            send_answer("Ошибка! " . $resultOrderData['error_desc'], $chatId,false);
        } else {
            $orderData = [date("Y-m-d H:i:s"), $resultOrderData['order_id'], $chatId, $dataForApi['amount']];
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/orders.txt', implode('|', $orderData). "\r\n", FILE_APPEND);

            $receiverWalletStr = " *Номер карты для оплаты*: `".$resultOrderData['card_number']."`";

            if ((!empty($resultOrderData['qr_sbp_url']) || !empty($resultOrderData['card_form_url'])) && empty($resultOrderData['card_number'])) {
                $url = !empty($resultOrderData['qr_sbp_url']) ? $resultOrderData['qr_sbp_url'] : $resultOrderData['card_form_url'];

                $receiverWalletStr = " *Ссылка на оплату*: [".$url."](".$url.") ";
            }

            send_answer(Config::$codeSymbolOrder." *Создан заказ*: №".$resultOrderData['order_id']."\n\n".Config::$codeSymbolCard.$receiverWalletStr." \n".Config::$codeSymbolAmount." *Сумма платежа*: `" . $resultOrderData['amount']. "` ".Config::$codeSymbolRUB."\n\n".Config::$codeSymbolTimer." *Время на оплату*: ".Config::$timeMinutesForPayment." мин", $chatId);

            if (!empty(Config::$textAfterRequisites)) {
                send_answer(Config::$textAfterRequisites, $chatId);
            }
        }

        header("HTTP/1.1 200 OK");
        exit;
    } catch (Exception $exception) {
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/response_tg_errors.txt', "DATE: ". date("Y-m-d H:i:s")." RESPONSE: ".$exception->getMessage(). "\r\n", FILE_APPEND);
    }
}

header("HTTP/1.1 200 OK");
exit;
