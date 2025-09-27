<?php

function send_answer($text, $chat_id, $markdown = true) {
    $sendData = [
        'chat_id' => $chat_id,
        'text'    => $text,
    ];

    if ($markdown) {
        $sendData['parse_mode'] = 'markdown';
    }

    $ch = curl_init();
    curl_setopt_array(
        $ch,
        [
            CURLOPT_URL => 'https://api.telegram.org/bot' . Config::$telegramBotToken . '/sendMessage',
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => $sendData,
        ]
    );

    $res = curl_exec($ch);

    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/response_tg_errors.txt', "DATE: ". date("Y-m-d H:i:s")." RESPONSE: ".$res. "\r\n", FILE_APPEND);
}

function getRequisitesEpay($data) {
    $ch = curl_init();
    curl_setopt_array(
        $ch,
        [
            CURLOPT_URL => Config::$epayRequisiteUrl,
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POSTFIELDS => $data,
        ]
    );

    return curl_exec($ch);
}

function handleSuccessCallback($orderId) {
    $rows    = file($_SERVER['DOCUMENT_ROOT'] . '/orders.txt');
    $needRow = null;

    if (!empty($rows)) {
        foreach ($rows as $row) {
            $rowData = explode('|', $row);

            if ((int)$orderId === (int)$rowData[1]) {
                $needRow = $rowData;
                break;
            }
        }
    }

    if ($needRow && is_array($needRow)) {
        $sendData = [
            'chat_id' => (int)$needRow[2],
            'text'    => 'Получена оплата по заказу ' . $orderId . '!',
        ];

        $ch = curl_init();
        curl_setopt_array(
            $ch,
            [
                CURLOPT_URL => 'https://api.telegram.org/bot' . Config::$telegramBotToken . '/sendMessage',
                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_POSTFIELDS => $sendData,
            ]
        );

        curl_exec($ch);
    }
}

function checkWebhookIsSet($botToken) {
    $ch = curl_init();
    curl_setopt_array(
        $ch,
        [
            CURLOPT_URL => "https://api.telegram.org/bot" . Config::$telegramBotToken . "/getWebhookInfo",
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 30,
        ]
    );

    $result = json_decode(curl_exec($ch),true);

    return !empty($result['result']['url']);
}