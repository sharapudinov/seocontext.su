<?
$iFortisSkipContentContainer = true;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Новости сайта");
$APPLICATION->SetTitle("Новости сайта");

?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news", 
	".default", 
	array(
		"IBLOCK_TYPE" => "fortis_content",
		"IBLOCK_ID" => "6",
		"NEWS_COUNT" => "10",
		"USE_SEARCH" => "Y",
		"TAGS_CLOUD_ELEMENTS" => "150",
		"PERIOD_NEW_TAGS" => "",
		"USE_RSS" => "Y",
		"USE_RATING" => "N",
		"USE_CATEGORIES" => "Y",
		"USE_FILTER" => "N",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"CHECK_DATES" => "Y",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/informatsiya/novosti/",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "Y",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "Y",
		"USE_PERMISSIONS" => "N",
		"PREVIEW_TRUNCATE_LEN" => "",
		"LIST_ACTIVE_DATE_FORMAT" => "j F Y",
		"LIST_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"LIST_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"HIDE_LINK_WHEN_NO_DETAIL" => "Y",
		"DISPLAY_NAME" => "Y",
		"META_KEYWORDS" => "-",
		"META_DESCRIPTION" => "-",
		"BROWSER_TITLE" => "-",
		"DETAIL_ACTIVE_DATE_FORMAT" => "j F Y",
		"DETAIL_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
		"DETAIL_PAGER_TITLE" => "Страница",
		"DETAIL_PAGER_TEMPLATE" => "",
		"DETAIL_PAGER_SHOW_ALL" => "N",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"DISPLAY_AS_RATING" => "rating",
		"FONT_MAX" => "50",
		"FONT_MIN" => "10",
		"COLOR_NEW" => "3E74E6",
		"COLOR_OLD" => "C0C0C0",
		"TAGS_CLOUD_WIDTH" => "100%",
		"USE_SHARE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"NUM_NEWS" => "20",
		"NUM_DAYS" => "30",
		"YANDEX" => "N",
		"MAX_VOTE" => "5",
		"VOTE_NAMES" => array(
			0 => "1",
			1 => "2",
			2 => "3",
			3 => "4",
			4 => "5",
			5 => "",
		),
		"FILTER_NAME" => "",
		"FILTER_FIELD_CODE" => array(
			0 => "NAME",
			1 => "DATE_ACTIVE_FROM",
			2 => "",
		),
		"FILTER_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"SHARE_HIDE" => "N",
		"SHARE_TEMPLATE" => "",
		"SHARE_HANDLERS" => array(
			0 => "delicious",
			1 => "facebook",
			2 => "lj",
			3 => "mailru",
			4 => "twitter",
			5 => "vk",
		),
		"SHARE_SHORTEN_URL_LOGIN" => "",
		"SHARE_SHORTEN_URL_KEY" => "",
		"CATEGORY_IBLOCK" => array("6"),
		"CATEGORY_CODE" => "RELATED",
		"CATEGORY_ITEMS_COUNT" => "5",
		"CATEGORY_THEME_6" => "list",
		"SEF_URL_TEMPLATES" => array(
			"news" => "",
			"section" => "",
			"detail" => "#ELEMENT_CODE#/",
			"search" => "poisk/",
			"rss" => "rss/",
			"rss_section" => "#SECTION_CODE_PATH#/rss/",
		)
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>