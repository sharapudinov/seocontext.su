<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */


if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 300;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
	$arParams["IBLOCK_TYPE"] = "news";
if($arParams["IBLOCK_TYPE"]=="-")
	$arParams["IBLOCK_TYPE"] = "";

if(!is_array($arParams["IBLOCKS"]))
	$arParams["IBLOCKS"] = array($arParams["IBLOCKS"]);
foreach($arParams["IBLOCKS"] as $k=>$v)
	if(!$v)
		unset($arParams["IBLOCKS"][$k]);

if(!is_array($arParams["FIELD_CODE"]))
	$arParams["FIELD_CODE"] = array();
foreach($arParams["FIELD_CODE"] as $key=>$val)
	if(!$val)
		unset($arParams["FIELD_CODE"][$key]);

$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
if(strlen($arParams["SORT_BY1"])<=0)
	$arParams["SORT_BY1"] = "ACTIVE_FROM";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
	$arParams["SORT_ORDER1"]="DESC";

if(strlen($arParams["SORT_BY2"])<=0)
	$arParams["SORT_BY2"] = "SORT";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
	$arParams["SORT_ORDER2"]="ASC";

if($this->StartResultCache(false, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups())))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	$arSelect = array_merge($arParams["FIELD_CODE"], array(
		"ID",
		"IBLOCK_ID",
		"ACTIVE_FROM",
		"DETAIL_PAGE_URL",
		"NAME",
	));
	$arFilter = array (
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID"=> $arParams["IBLOCKS"],
		"ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
	);
	$arOrder = array(
		$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
		$arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"],
	);
	if(!array_key_exists("ID", $arOrder))
		$arOrder["ID"] = "DESC";
	$arResult=array(
		"ITEMS"=>array(),
	);
	$rsItems = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
	while($arItem = $rsItems->GetNext())
	{
		$arButtons = CIBlock::GetPanelButtons(
			$arItem["IBLOCK_ID"],
			$arItem["ID"],
			0,
			array("SECTION_BUTTONS"=>false, "SESSID"=>false)
		);
		$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
		$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

		$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arItem["IBLOCK_ID"], $arItem["ID"]);
		$arItem["IPROPERTY_VALUES"] = $ipropValues->getValues();

		if(isset($arItem["PREVIEW_PICTURE"]))
		{
			$arItem["PREVIEW_PICTURE"] = (0 < $arItem["PREVIEW_PICTURE"] ? CFile::GetFileArray($arItem["PREVIEW_PICTURE"]) : false);
			if ($arItem["PREVIEW_PICTURE"])
			{
				$arItem["PREVIEW_PICTURE"]["ALT"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"];
				if ($arItem["PREVIEW_PICTURE"]["ALT"] == "")
					$arItem["PREVIEW_PICTURE"]["ALT"] = $arItem["NAME"];
				$arItem["PREVIEW_PICTURE"]["TITLE"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"];
				if ($arItem["PREVIEW_PICTURE"]["TITLE"] == "")
					$arItem["PREVIEW_PICTURE"]["TITLE"] = $arItem["NAME"];
			}
		}
		if(isset($arItem["DETAIL_PICTURE"]))
		{
			$arItem["DETAIL_PICTURE"] = (0 < $arItem["DETAIL_PICTURE"] ? CFile::GetFileArray($arItem["DETAIL_PICTURE"]) : false);
			if ($arItem["DETAIL_PICTURE"])
			{
				$arItem["DETAIL_PICTURE"]["ALT"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"];
				if ($arItem["DETAIL_PICTURE"]["ALT"] == "")
					$arItem["DETAIL_PICTURE"]["ALT"] = $arItem["NAME"];
				$arItem["DETAIL_PICTURE"]["TITLE"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"];
				if ($arItem["DETAIL_PICTURE"]["TITLE"] == "")
					$arItem["DETAIL_PICTURE"]["TITLE"] = $arItem["NAME"];
			}
		}

		$arResult["ITEMS"][]=$arItem;
		$arResult["LAST_ITEM_IBLOCK_ID"]=$arItem["IBLOCK_ID"];
	}
	$this->SetResultCacheKeys(array(
		"LAST_ITEM_IBLOCK_ID",
	));
	$this->IncludeComponentTemplate();
}

if(
	$arResult["LAST_ITEM_IBLOCK_ID"] > 0
	&& $USER->IsAuthorized()
	&& $APPLICATION->GetShowIncludeAreas()
	&& CModule::IncludeModule("iblock")
)
{
	$arButtons = CIBlock::GetPanelButtons($arResult["LAST_ITEM_IBLOCK_ID"], 0, 0, array("SECTION_BUTTONS"=>false));
	$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));
}
?>
