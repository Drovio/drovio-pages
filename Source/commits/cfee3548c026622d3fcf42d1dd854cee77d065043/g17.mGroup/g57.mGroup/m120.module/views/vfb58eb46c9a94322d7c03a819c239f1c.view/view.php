<?php
//#section#[header]
// Module Declaration
$moduleID = 120;

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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\template;
use \API\Resources\literals\moduleLiteral;
use \INU\Developer\redWIDE;

$groupId = $_GET['id'];

$globalObjectWhapper = DOM::create("div");
DOM::attr($globalObjectWhapper, "style", "height:100%;");


$info = DOM::create("div");
DOM::append($globalObjectWhapper, $info);
$infoArray = template::getTemplateGroupInfo($groupId);

$groupTitle = DOM::create("div", $infoArray['groupTitle']);
DOM::append($info, $groupTitle);
$groupDescription = DOM::create("div", $infoArray['groupDescription']);
DOM::append($info, $groupDescription);


// Send redWIDE Tab
$obj_id = "templateGrp_".$groupId;
$header = "Group:".$infoArray['groupTitle'];
$WIDETab = redWIDE::getContent($obj_id, $header, $globalObjectWhapper);

report::clear();
report::add_content($WIDETab, redWIDE::POOL, "append");
return report::get();
//#section_end#
?>