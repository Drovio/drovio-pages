<?php
//#section#[header]
// Module Declaration
$moduleID = 74;

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
importer::import("API", "Geoloc");
importer::import("API", "Model");
importer::import("API", "Resources");
//#section_end#
//#section#[code]
use \API\Geoloc\lang\mlgContent;
use \API\Resources\documentParser;
use \API\Model\layout\components\modulePage;

$pageTitle = mlgContent::get_moduleLiteral($policyCode, "title", FALSE);
$mdlPage = new modulePage($pageTitle, "simpleOneColumnCenter");

//$document = documentParser::load("--FAQ file path--", $text = FALSE);

$default = reporter::get("success", "info", "info.page_default");
$mdlPage->append_to_section("mainContent", $default);

report::clear();
report::add_content($mdlPage->get_page_body());
return report::get();
//#section_end#
?>