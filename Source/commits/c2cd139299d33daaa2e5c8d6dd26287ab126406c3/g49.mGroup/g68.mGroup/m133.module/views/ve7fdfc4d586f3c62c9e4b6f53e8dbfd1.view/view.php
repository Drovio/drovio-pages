<?php
//#section#[header]
// Module Declaration
$moduleID = 133;

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
importer::import("API", "Literals");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Interactive\forms\switchButton;
use \DEV\Apps\test\appTester;

// Create page Content
$pageContent = new HTMLContent();
$pageContent->build("", "appConfigurator");

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get application id
	$appID = $_POST['appID'];
	
	// (De)Activate tester for this application
	$status = appTester::status($appID);
	if ($status === FALSE)
		appTester::activate($appID);
	else
		appTester::deactivate($appID);
	
	// Return switchButton action report
	return switchButton::getReport(!$status);
}

// Tester switch
$testerSwitchRow = DOM::create("div", "", "", "switchRow appTester");
$pageContent->append($testerSwitchRow);

$title = moduleLiteral::get($moduleID, "lbl_appTester");
DOM::append($testerSwitchRow, $title);

// Create switchButton
$switch = new switchButton("appTesterSwitch");
$switch->build(appTester::status());

// Set action for ths module
$attr = array();
$attr['appID'] = $_GET['projectID'];
$testingSwitch = $switch->setAction($moduleID, "", $attr)->get();
DOM::append($testerSwitchRow, $testingSwitch);


// Return output
return $pageContent->getReport();
//#section_end#
?>