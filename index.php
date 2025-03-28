<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
global $APPLICATION;
$APPLICATION->SetTitle('Главная');
?> 
<?php
$APPLICATION->IncludeComponent(
	"ilizium:todo.manager", 
	".default", 
	array(
		"IBLOCK_ID_TASKS"    => "2",
		"IBLOCK_ID_TAGS"     => "3",
		"PAGE_SIZE"          => "5",
		"CACHE_TYPE"         => "N",
		"COMPONENT_TEMPLATE" => ".default",
		"CACHE_TIME"         => "3600"
	),
	false
);
?>
<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>