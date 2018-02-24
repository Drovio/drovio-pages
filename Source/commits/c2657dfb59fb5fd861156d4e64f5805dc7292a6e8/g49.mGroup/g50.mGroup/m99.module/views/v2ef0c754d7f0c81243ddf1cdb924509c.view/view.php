<?php
//#section#[header]
// Module Declaration
$moduleID = 99;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\url;
use \UI\Html\HTMLModulePage;

$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

// Build Module Page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "devDocs", TRUE);


// Sidebar section
$title = moduleLiteral::get($moduleID, "lbl_sideHeader_guides");
$sectionHeader = HTML::select(".sideSection.guides .header")->item(0);
DOM::append($sectionHeader, $title);

$title = moduleLiteral::get($moduleID, "lbl_guide_start");
$url = url::resolve("developer", "/docs/guides/starting/");
$wl = $page->getWeblink($url, $title, "_self");
$attr = array();
$attr['url'] = "/docs/guides/starting/";
$actionFactory->setModuleAction($wl, $moduleID, "loadContent", ".docHolder", $attr);
$litem = HTML::select(".sideSection.guides .listItem")->item(0);
DOM::append($litem, $wl);

$title = moduleLiteral::get($moduleID, "lbl_guide_projects");
$url = url::resolve("developer", "/docs/guides/projects/");
$wl = $page->getWeblink($url, $title, "_self");
$attr = array();
$attr['url'] = "/docs/guides/projects/";
$actionFactory->setModuleAction($wl, $moduleID, "loadContent", ".docHolder", $attr);
$litem = HTML::select(".sideSection.guides .listItem")->item(1);
DOM::append($litem, $wl);

$title = moduleLiteral::get($moduleID, "lbl_sideHeader_protocol");
$sectionHeader = HTML::select(".sideSection.prot .header")->item(0);
DOM::append($sectionHeader, $title);

$title = moduleLiteral::get($moduleID, "lbl_prot_vcs");
$url = url::resolve("developer", "/docs/reference/vcs");
$wl = $page->getWeblink($url, $title, "_self");
$attr = array();
$attr['url'] = "/docs/reference/vcs/";
$actionFactory->setModuleAction($wl, $moduleID, "loadContent", ".docHolder", $attr);
$litem = HTML::select(".sideSection.prot .listItem")->item(0);
DOM::append($litem, $wl);

$title = moduleLiteral::get($moduleID, "lbl_sideHeader_sdks");
$sectionHeader = HTML::select(".sideSection.sdks .header")->item(0);
DOM::append($sectionHeader, $title);

$title = moduleLiteral::get($moduleID, "lbl_man_sdk");
$url = url::resolve("developer", "/docs/manuals/sdk/");
$wl = $page->getWeblink($url, $title, "_self");
$attr = array();
$attr['url'] = "/docs/manuals/sdk/";
$actionFactory->setModuleAction($wl, $moduleID, "loadContent", ".docHolder", $attr);
$litem = HTML::select(".sideSection.sdks .listItem")->item(0);
DOM::append($litem, $wl);


$title = moduleLiteral::get($moduleID, "lbl_man_webSdk");
$url = url::resolve("developer", "/docs/manuals/web/");
$wl = $page->getWeblink($url, $title, "_self");
$attr = array();
$attr['url'] = "/docs/manuals/web/";
$actionFactory->setModuleAction($wl, $moduleID, "loadContent", ".docHolder", $attr);
$litem = HTML::select(".sideSection.sdks .listItem")->item(1);
DOM::append($litem, $wl);


$title = moduleLiteral::get($moduleID, "lbl_sideHeader_general");
$sectionHeader = HTML::select(".sideSection.general .header")->item(0);
DOM::append($sectionHeader, $title);

$title = moduleLiteral::get($moduleID, "lbl_general_terms");
$url = url::resolve("developer", "/docs/terms/");
$wl = $page->getWeblink($url, $title, "_self");
$attr = array();
$attr['url'] = "/docs/terms/";
$actionFactory->setModuleAction($wl, $moduleID, "loadContent", ".docHolder", $attr);
$litem = HTML::select(".sideSection.general .listItem")->item(0);
DOM::append($litem, $wl);


return $page->getReport();
//#section_end#
?>