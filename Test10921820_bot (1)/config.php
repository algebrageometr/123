<?php

class Config {
    public static $telegramBotToken = "8259877162:AAHQmYUve6jS4ipGZa6HxYFr9cqxFgVD3Os"; //телеграм бот токен
    public static $telegramBotUrl = "https://t.me/Test10921820_bot"; //телеграм бот url
    public static $apiSecret = "754fc9b3bb03cb719d76a18d8c83dcfd2d7758bc595585002c10355a415143d9"; // ключ АПИ от епея
    public static $epayRequisiteUrl = "https://infopayments.click/api/request/requisites"; //url epay для получения карт на оплату
    public static $textBeforeEnterPrice = "Введите точную сумму"; //текст перед вводом суммы заказа
    public static $textAfterRequisites = "На оплату дается 20 минут, после чего ссылка будет неактивна"; //текст после выдачи реквизитов на оплату
    public static $timeMinutesForPayment = "20"; //время на оплату клиенту
    public static $codeSymbolOrder = "\xF0\x9F\x93\x84";
    public static $codeSymbolTimer = "\xF0\x9F\x95\x91";
    public static $codeSymbolCard = "\xF0\x9F\x92\xB3";
    public static $codeSymbolAmount = "\xF0\x9F\x92\xB0";
    public static $codeSymbolRUB = "₽";
}
