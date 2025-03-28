<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

Loader::includeModule('iblock');

$arComponentParameters = [
    "GROUPS" => [],
    "PARAMETERS" => [
        "IBLOCK_ID_TASKS" => [
            "PARENT"  => "BASE",
            "NAME"    => "ID инфоблока с задачами",
            "TYPE"    => "STRING",
            "DEFAULT" => "",
        ],
        "IBLOCK_ID_TAGS" => [
            "PARENT"  => "BASE",
            "NAME"    => "ID инфоблока с тегами",
            "TYPE"    => "STRING",
            "DEFAULT" => "",
        ],
        "PAGE_SIZE" => [
            "PARENT"  => "BASE",
            "NAME"    => "Количество задач на странице",
            "TYPE"    => "STRING",
            "DEFAULT" => "5",
        ],
        "CACHE_TIME" => ["DEFAULT" => 3600],
    ]
];
