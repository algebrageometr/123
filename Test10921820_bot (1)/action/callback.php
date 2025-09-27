<?php

require_once $_SERVER['DOCUMENT_ROOT']."/config.php";
require_once $_SERVER['DOCUMENT_ROOT']."/functions.php";

$update = file_get_contents('php://input');

file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/action/epay_callbacks_log.txt', "DATE: ". date("Y-m-d H:i:s")." DATA: ". $update . "\r\n", FILE_APPEND);

$update = json_decode($update,true);

if (!empty($update['status']) && $update['status'] == 'successful_payment') {
    handleSuccessCallback($update['transaction_id']);
    exit('success');
} else {
    exit('fail');
}