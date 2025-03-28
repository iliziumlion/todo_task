<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
    "NAME"        => "Todo Manager",
    "DESCRIPTION" => "Простой todo-менеджер",
    "PATH"        => [
        "ID"   => "my_components",
        "CHILD" => [
            "ID"   => "todo_manager",
            "NAME" => "Todo Manager"
        ]
    ],
];
