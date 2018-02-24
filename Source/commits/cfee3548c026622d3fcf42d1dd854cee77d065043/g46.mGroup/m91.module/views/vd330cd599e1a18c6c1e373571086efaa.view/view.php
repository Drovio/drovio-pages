<?php
//#section#[header]
// Module Declaration
$moduleID = 91;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Content");
importer::import("API", "Model");
importer::import("API", "Platform");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Content\literals\literal;
use \API\Content\literals\moduleLiteral;
use \API\Platform\state\url;
use \API\Model\protocol\ajax\ascop;
use \API\Content\resources\documentParser;
use \API\Model\layout\components\modulePage;
use \UI\Presentation\heading;
use \UI\Presentation\gridView;
use \UI\Presentation\layoutContainer;
use \UI\Presentation\userConnectControls;
use \UI\forms\simpleForm;

// Build Module Page
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$pageBld = new modulePage($pageTitle, "freeLayout");

$layoutBase = DOM::create("div", "", "medicalpage");
$pageBld->append_to_section("main", $layoutBase);

$mainHead = DOM::create("div", "", "", "mainHead");
DOM::append($layoutBase, $mainHead);

$mainHeadContent = DOM::create("div", "", "", "mainHeadContent");
DOM::append($mainHead, $mainHeadContent);

// Build Page Head
$pageHead = DOM::create("div", "", "", "pageHead");
DOM::append($mainHeadContent, $pageHead);

//_____ H1 Logo
$logo = DOM::create("h1", "", "pageLogo");
DOM::append($pageHead, $logo);
//_____ Logo Image
$logoImg = DOM::create("img");
DOM::attr($logoImg, "src", url::resolve("www", "/Library/Media/images/logos/medium/RB_logo_medium.svg"));
DOM::append($logo, $logoImg);

$pageHeadTitle = DOM::create("h2", "", "", "headTitle");
DOM::append($pageHead, $pageHeadTitle);

//_____ Header Message
$title = moduleLiteral::get($moduleID, "msg_headMessage");
DOM::append($pageHeadTitle, $title);

$openingDateWrapper = DOM::create("h3", "", "", "oepningDate ebuilderTitle");
DOM::append($pageHead, $openingDateWrapper);

$openingDate = literal::get("global.temp", "lbl_openingDate");
DOM::append($openingDateWrapper, $openingDate);

// User Connection Kit
$userConnect = userConnectControls::get();
DOM::append($pageHead, $userConnect);

$mainBody = DOM::create("div", "", "", "mainBody");
DOM::append($layoutBase, $mainBody);

$mainBodyContent = DOM::create("div", "", "", "mainBodyContent");
DOM::append($mainBody, $mainBodyContent);

$bodyContainer = DOM::create("div", "", "", "bodyContainer");
DOM::append($mainBodyContent, $bodyContainer);

// Main Body Title
$pageBodyTitle = DOM::create("h2", "", "", "bodyTitle");
DOM::append($bodyContainer, $pageBodyTitle);

$title = moduleLiteral::get($moduleID, "lbl_promoTitle");
DOM::append($pageBodyTitle, $title);

// Main Body Subtitle
$pageSubBodyTitle = DOM::create("h3", "", "", "bodySubTitle");
DOM::append($bodyContainer, $pageSubBodyTitle);

$title = moduleLiteral::get($moduleID, "lbl_promoSubTitle");
DOM::append($pageSubBodyTitle, $title);


report::clear();
report::add_content($pageBld->get_page_body(), modulePage::HOLDER);
return report::get();
//#section_end#
?>