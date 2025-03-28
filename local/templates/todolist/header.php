<?php
if (!defined ('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
    die();
}
use Bitrix\Main\Page\Asset;
/** @var \CMain $APPLICATION */

if (!\Bitrix\Main\Loader::includeModule('landing'))
{
    return;
}

$language = \Bitrix\Landing\Manager::getLangISO();
global $APPLICATION;
?>
<!DOCTYPE html>
<html xml:lang="<?= $language;?>" lang="<?= $language;?>" class="<?php $APPLICATION->ShowProperty('HtmlClass');?>">
<head>
    <?php $APPLICATION->ShowHead();?>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
    <meta name="HandheldFriendly" content="true" >
    <meta name="MobileOptimized" content="width">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title><?php $APPLICATION->ShowTitle();?></title>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />

    <?php
        Asset::getInstance()->addString('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>');
    ?>
</head>
<body>
<div id="panel">
    <?php $APPLICATION->ShowPanel();?>
</div>