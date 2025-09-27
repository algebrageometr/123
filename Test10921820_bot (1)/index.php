<?php

require_once $_SERVER['DOCUMENT_ROOT']."/config.php";
require_once $_SERVER['DOCUMENT_ROOT']."/functions.php";

if (!checkWebhookIsSet(Config::$telegramBotToken)) {
    echo '<a href="https://api.telegram.org/bot'.Config::$telegramBotToken.'/setWebhook?url=https://'.$_SERVER['SERVER_NAME'].'/action/tg.php">Активировать бота</a>';
} else {
    echo 'Бот активирован! Ссылка на Ваш бот - <a href="'.Config::$telegramBotUrl.'">'.Config::$telegramBotUrl.'</a>';
}
