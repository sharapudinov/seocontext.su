<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

global $sFortisColSidebar, $sFortisColContent;

?>

<div class="content">
	<div class="container wrapper-container">
		<div class="row">

			<div class="<?=$sFortisColSidebar?>">

                <?$APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    "",
                    Array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_DIR . "includes/sidebar_top.php",
                        "EDIT_TEMPLATE" => ""
                    ),
                    false
                );?>

				<?if($arParams["USE_RSS"]=="Y"):?>
					<h4>
						<?=GetMessage("RSS_LABEL")?>
						<?
						if(method_exists($APPLICATION, 'addheadstring'))
							$APPLICATION->AddHeadString('<link rel="alternate" type="application/rss+xml" title="'.$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["rss"].'" href="'.$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["rss"].'" />');
						?>
						<a href="<?=$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["rss"]?>" class="news-rss-link" title="rss" target="_self">
							<img alt="RSS" src="<?=$templateFolder?>/images/gif-light/feed-icon-16x16.gif" border="0" align="right" />
						</a>
					</h4>
				<?endif?>

				<?$APPLICATION->IncludeComponent("bitrix:menu", "left_multilevel", Array(
					"ROOT_MENU_TYPE" => "left",	// ��� ���� ��� ������� ������
					"MENU_CACHE_TYPE" => "A",	// ��� �����������
					"MENU_CACHE_TIME" => "3600",	// ����� ����������� (���.)
					"MENU_CACHE_USE_GROUPS" => "Y",	// ��������� ����� �������
					"MENU_CACHE_GET_VARS" => array(	// �������� ���������� �������
						0 => "",
					),
					"MAX_LEVEL" => "4",	// ������� ����������� ����
					"CHILD_MENU_TYPE" => "leftchild",	// ��� ���� ��� ��������� �������
					"USE_EXT" => "Y",	// ���������� ����� � ������� ���� .���_����.menu_ext.php
					"DELAY" => "N",	// ����������� ���������� ������� ����
					"ALLOW_MULTI_SELECT" => "N",	// ��������� ��������� �������� ������� ������������
				),
					false
				);?>
				<br />

				<?if($arParams["USE_SEARCH"]=="Y"):?>
					<h4><?=GetMessage("SEARCH_LABEL")?></h4>
					<?$APPLICATION->IncludeComponent(
						"bitrix:search.form",
						"flat",
						Array(
							"PAGE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["search"]
						),
						$component
					);?>
					<br />
				<?endif?>
				<?if($arParams["USE_FILTER"]=="Y"):?>
					<?$APPLICATION->IncludeComponent(
						"bitrix:catalog.filter",
						"",
						Array(
							"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"FILTER_NAME" => $arParams["FILTER_NAME"],
							"FIELD_CODE" => $arParams["FILTER_FIELD_CODE"],
							"PROPERTY_CODE" => $arParams["FILTER_PROPERTY_CODE"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
						),
						$component
					);
					?>
					<br />
				<?endif?>

                <?$APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    "",
                    Array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_DIR . "includes/sidebar_bottom.php",
                        "EDIT_TEMPLATE" => ""
                    ),
                    false
                );?>
                
			</div>

			<div class="<?=$sFortisColContent?>">

				<?$ElementID = $APPLICATION->IncludeComponent(
					"bitrix:news.detail",
					"",
					Array(
						"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
						"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
						"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
						"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
						"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
						"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
						"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
						"META_KEYWORDS" => $arParams["META_KEYWORDS"],
						"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
						"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
						"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
						"SET_TITLE" => $arParams["SET_TITLE"],
						"SET_STATUS_404" => $arParams["SET_STATUS_404"],
						"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
						"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
						"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
						"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
						"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
						"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
						"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
						"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
						"PAGER_SHOW_ALWAYS" => "N",
						"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
						"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
						"CHECK_DATES" => $arParams["CHECK_DATES"],
						"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
						"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
						"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
						"USE_SHARE" 			=> $arParams["USE_SHARE"],
						"SHARE_HIDE" 			=> $arParams["SHARE_HIDE"],
						"SHARE_TEMPLATE" 		=> $arParams["SHARE_TEMPLATE"],
						"SHARE_HANDLERS" 		=> $arParams["SHARE_HANDLERS"],
						"SHARE_SHORTEN_URL_LOGIN"	=> $arParams["SHARE_SHORTEN_URL_LOGIN"],
						"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
						"ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : '')
					),
					$component
				);?>
				<?if($arParams["USE_RATING"]=="Y" && $ElementID):?>
					<?$APPLICATION->IncludeComponent(
						"bitrix:iblock.vote",
						"",
						Array(
							"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"ELEMENT_ID" => $ElementID,
							"MAX_VOTE" => $arParams["MAX_VOTE"],
							"VOTE_NAMES" => $arParams["VOTE_NAMES"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
						),
						$component
					);?>
				<?endif?>
				<?if($arParams["USE_CATEGORIES"]=="Y" && $ElementID):
					global $arCategoryFilter;
					$obCache = new CPHPCache;
					$strCacheID = $componentPath.LANG.$arParams["IBLOCK_ID"].$ElementID.$arParams["CATEGORY_CODE"];
					if(($tzOffset = CTimeZone::GetOffset()) <> 0)
						$strCacheID .= "_".$tzOffset;
					if($arParams["CACHE_TYPE"] == "N" || $arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N")
						$CACHE_TIME = 0;
					else
						$CACHE_TIME = $arParams["CACHE_TIME"];
					if($obCache->StartDataCache($CACHE_TIME, $strCacheID, $componentPath))
					{
						$rsProperties = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $ElementID, "sort", "asc", array("ACTIVE"=>"Y","CODE"=>$arParams["CATEGORY_CODE"]));
						$arCategoryFilter = array();
						while($arProperty = $rsProperties->Fetch())
						{
							if(is_array($arProperty["VALUE"]) && count($arProperty["VALUE"])>0)
							{
								foreach($arProperty["VALUE"] as $value)
									$arCategoryFilter[$value]=true;
							}
							elseif(!is_array($arProperty["VALUE"]) && strlen($arProperty["VALUE"])>0)
								$arCategoryFilter[$arProperty["VALUE"]]=true;
						}
						$obCache->EndDataCache($arCategoryFilter);
					}
					else
					{
						$arCategoryFilter = $obCache->GetVars();
					}
					if(count($arCategoryFilter)>0):

                        if ( ! empty($arCategoryFilter[$ElementID]))
                            unset($arCategoryFilter[$ElementID]);

						$arCategoryFilter = array(
							"ID" => array_keys($arCategoryFilter)
						);

						?>
						<br /><br />
                        <div class="page-header">
                            <h3><?=GetMessage("CATEGORIES")?></h3>
                        </div>
						<?foreach($arParams["CATEGORY_IBLOCK"] as $iblock_id):?>
						<?$APPLICATION->IncludeComponent(
							"bitrix:news.list",
							'f_' . $arParams["CATEGORY_THEME_".$iblock_id],
							Array(
								"IBLOCK_ID" => $iblock_id,
								"NEWS_COUNT" => $arParams["CATEGORY_ITEMS_COUNT"],
								"SET_TITLE" => "N",
								"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
								"CACHE_TYPE" => $arParams["CACHE_TYPE"],
								"CACHE_TIME" => $arParams["CACHE_TIME"],
								"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
								"FILTER_NAME" => "arCategoryFilter",
								"CACHE_FILTER" => "Y",
								"DISPLAY_TOP_PAGER" => "N",
								"DISPLAY_BOTTOM_PAGER" => "N",
							),
							$component
						);?>
					<?endforeach?>
					<?endif?>
				<?endif?>
				<?if($arParams["USE_REVIEW"]=="Y" && IsModuleInstalled("forum") && $ElementID):?>
					<hr />
					<?$APPLICATION->IncludeComponent(
						"bitrix:forum.topic.reviews",
						"",
						Array(
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],
							"USE_CAPTCHA" => $arParams["USE_CAPTCHA"],
							"PATH_TO_SMILE" => $arParams["PATH_TO_SMILE"],
							"FORUM_ID" => $arParams["FORUM_ID"],
							"URL_TEMPLATES_READ" => $arParams["URL_TEMPLATES_READ"],
							"SHOW_LINK_TO_FORUM" => $arParams["SHOW_LINK_TO_FORUM"],
							"DATE_TIME_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
							"ELEMENT_ID" => $ElementID,
							"AJAX_POST" => $arParams["REVIEW_AJAX_POST"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"URL_TEMPLATES_DETAIL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
						),
						$component
					);?>
				<?endif?>


				<div>
					<a href="<?=$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"]?>" class="btn btn-link back">
						<i class="fa fa-long-arrow-left"></i>
						<?=GetMessage("T_NEWS_DETAIL_BACK")?>
					</a>
				</div>

			</div>

		</div>
	</div>
</div>