<?php
//#section#[header]
// Module Declaration
$moduleID = 39;

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
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\environment\Url;
use \API\Resources\literals\moduleLiteral;
use \API\Security\account;
use \UI\Html\HTMLModulePage;

$page = new HTMLModulePage("OneColumnCentered");
$pageTitle = moduleLiteral::get($moduleID, "lbl_homeTitle", FALSE);
$page->build($pageTitle, "myHomePage");

// Title
$welcomeTitle = moduleLiteral::get($moduleID, "lbl_welcomeTitle");
$firstName = DOM::create("span", account::getFirstname(), "", "ident");
$header = DOM::create("h2", "", "", "pageHeader");
DOM::append($header, $welcomeTitle);
DOM::append($header, $firstName);
$page->appendToSection("mainContent", $header);


// Programs Container
$programContainer = DOM::create("div", "", "", "pContainer");
$page->appendToSection("mainContent", $programContainer);

// Developer's
$devTitle = moduleLiteral::get($moduleID, "lbl_joinDev");
$devPromo = DOM::create("h3", "", "", "promo");
$devProgramsUrl = Url::resolve("developer", "/programs/");
$devPromoA = DOM::create("a");
DOM::attr($devPromoA, "href", $devProgramsUrl);
DOM::attr($devPromoA, "target", "_blank");
DOM::append($devPromoA, $devTitle);
DOM::append($devPromo, $devPromoA);
DOM::append($programContainer, $devPromo);

// Support Page
$supportTitle = moduleLiteral::get($moduleID, "lbl_supportPrompt");
$devPromo = DOM::create("h3", "", "", "promo");
$devProgramsUrl = Url::resolve("support", "/");
$devPromoA = DOM::create("a");
DOM::attr($devPromoA, "href", $devProgramsUrl);
DOM::attr($devPromoA, "target", "_blank");
DOM::append($devPromoA, $supportTitle);
DOM::append($devPromo, $devPromoA);
DOM::append($programContainer, $devPromo);


return $page->getReport();
//#section_end#
?>