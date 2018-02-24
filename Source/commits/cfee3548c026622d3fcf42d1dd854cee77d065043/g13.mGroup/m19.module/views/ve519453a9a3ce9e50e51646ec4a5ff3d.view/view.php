<?php
//#section#[header]
// Module Declaration
$moduleID = 19;

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
importer::import("API", "Geoloc");
importer::import("API", "Model");
importer::import("API", "Platform");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Geoloc\lang\mlgContent;
use \API\Platform\state\url;
use \API\Model\protocol\ajax\ascop;
use \API\Content\resources\documentParser;
use \API\Model\layout\components\modulePage;
use \UI\Presentation\gridView;
use \UI\Presentation\userConnectControls;

// Build Module Page
$pageTitle = mlgContent::get_moduleLiteral($moduleID, "title", FALSE);
$pageBld = new modulePage($pageTitle, "freeLayout");

$layoutBase = DOM::create("div", "", "deliverynet");
$pageBld->append_to_section("main", $layoutBase);

$mainHead = DOM::create("div", "", "", "mainHead");
DOM::append($layoutBase, $mainHead);

$mainHeadContent = DOM::create("div", "", "", "mainHeadContent");
DOM::append($mainHead, $mainHeadContent);

// Build Page Head
$pageHead = DOM::create("div", "", "", "pageHead");
DOM::append($mainHeadContent, $pageHead);

// H1 Logo
$logo = DOM::create("h1", "", "pageLogo");
DOM::append($pageHead, $logo);

// Logo Image
$logoImg = DOM::create("img");
DOM::attr($logoImg, "src", "/Library/Media/images/logos/medium/RB_deliveryNet_logo_medium.svg");
DOM::append($logo, $logoImg);

// DeliveryNet Quotes
$quotesWrapper = DOM::create("h1", "", "", "deliveryNetQuotesWrapper");
DOM::append($pageHead, $quotesWrapper);

$quote = mlgContent::get_moduleLiteral($moduleID, "lbl_deliveryNetSubQuote");
DOM::append($quotesWrapper, $quote);

$openingDateWrapper = DOM::create("h3", "", "", "oepningDate deliveryNetQuotesWrapper");
DOM::append($pageHead, $openingDateWrapper);

$openingDate = mlgContent::get_literal("global.temp", "lbl_openingDate");
DOM::append($openingDateWrapper, $openingDate);

// User Controls Tile
$userControlsWrapper = DOM::create("div", "", "", "userControlsWrapper");
DOM::append($pageHead, $userControlsWrapper);
$userControls = userConnectControls::get();
DOM::append($userControlsWrapper, $userControls);

$mainBody = DOM::create("div", "", "", "mainBody");
DOM::append($layoutBase, $mainBody);

$mainBodyContent = DOM::create("div", "", "", "mainBodyContent");
DOM::append($mainBody, $mainBodyContent);

// Build Page Content
$pageContent = DOM::create("div", "", "", "pageContent");
DOM::append($mainBodyContent, $pageContent);

// Page Content Sub Title
$pageSubTitle = DOM::create("h2", "", "", "deliveryNetQuotesWrapper");
DOM::append($pageContent, $pageSubTitle);

$title = mlgContent::get_moduleLiteral($moduleID, "lbl_subTitle");
DOM::append($pageSubTitle, $title);

// Create Top Grid View
$gridView = new gridView();
$gridViewElement = $gridView->create(3, 1);
DOM::append($mainBodyContent, $gridViewElement);

// Set Main Body Columnts
$location = DOM::create("div", "", "", "dlvBanner pin");
$gridView->set_content($location, 0, 0);

$search = DOM::create("div", "", "", "dlvBanner search");
$gridView->set_content($search, 0, 1);

$privileges = DOM::create("div", "", "", "dlvBanner gear");
$gridView->set_content($privileges, 0, 2);

report::clear();
report::add_content($pageBld->get_page_body(), modulePage::HOLDER);
return report::get();
//#section_end#
?>