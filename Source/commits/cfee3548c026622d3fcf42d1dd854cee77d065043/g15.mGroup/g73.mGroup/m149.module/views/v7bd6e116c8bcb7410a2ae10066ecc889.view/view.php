<?php
//#section#[header]
// Module Declaration
$moduleID = 149;

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
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \UI\Html\HTMLModulePage;
use \API\Resources\archive\zipManager;
use \API\Resources\filesystem\directory;
use \API\Developer\profiler\tester;

// Create Module Page
$page = new HTMLModulePage("OneColumnFullscreen");

// Build the module
$page->build("Backup Site", "backup");

$suffix = "";
$time = intval(date("G"));
if ($time < 7)
	$time = "_night";
else if ($time < 12)
	$time = "_start";
else if ($time < 19)
	$time = "_noon";
else
	$time = "_end";

// example: 29jun2013_start
$name = strtolower(date("dMY").$time);

// Get ZipFile Name
$trunkBackupName = systemRoot.tester::getTrunk()."/backup/".$name.".zip";
// Get Directory Contents
$contents = directory::getContentList(systemRoot."/", TRUE);

// Backup
//zipManager::create($trunkBackupName, $contents, TRUE);

//_____ Place Your Code Here
// Create the under development notification
$udNotification = reporter::get("success", "info", "info.page_default");
$page->appendToSection("mainContent", $udNotification);



// Return output
return $page->getReport();
//#section_end#
?>