<?php
//#section#[header]
// Module Declaration
$moduleID = 169;

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
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\appcenter\appPlayer;
use \API\Resources\literals\moduleLiteral;
use \API\Security\account;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\HTMLRibbon;
use \ACL\Platform\importer as ACLImporter;

// Init application player
appPlayer::init();

// Create Module Page
$page = new HTMLModulePage("OneColumnFullscreen");
$actionFactory = $page->getActionFactory();

// Get application id
$appID = $_GET['id'];

if (empty($appID))
{
	// Application id doesn't exist, return to home page
	return $actionFactory->getReportRedirect("/", "apps");
}

// Load application data
$application = appPlayer::getApplicationData($appID);
if (is_null($application))
{
	// Return Error Page
	$page->build("Application Error", "applicationError");
	return $page->getReport();
}

// Build the module for a valid application
$appName = $application['fullName'];
$page->build($appName, "applicationPlayer");

// If account is application author, set the tester controls
$appTester = appPlayer::isTester($appID);
if ($appTester)
{
	$collection = HTMLRibbon::getCollection("testerCol", $moduleID, $action = "appTester", $startup = TRUE);
	$subItem = $page->addToolbarNavItem("testerSub", $title = "Application Tester", $class = "tester", $collection, $ribbonType = "inline", $type = "obedient", $pinnable = FALSE, $index = 0, $ico = TRUE);
}

$appContainer = DOM::create("div", "", "applicationContainer");
$page->appendToSection("mainContent", $appContainer);

// Return output
return $page->getReport();
//#section_end#
?>