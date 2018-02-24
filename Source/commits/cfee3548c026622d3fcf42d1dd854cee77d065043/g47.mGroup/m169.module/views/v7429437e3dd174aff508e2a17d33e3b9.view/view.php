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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
//#section_end#
//#section#[code]
use \API\Developer\appcenter\appPlayer;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Interactive\forms\switchButton;

// Create page Content
$pageContent = new HTMLContent();
$pageContent->build();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$status = appPlayer::testerStatus();
	if (appPlayer::testerStatus() === FALSE)
	{
		appPlayer::activateTester();
		$status = TRUE;
	}
	else
	{
		appPlayer::deactivateTester();
		$status = FALSE;
	}
	return switchButton::getReport($status);
}

// Tester switch
$testerSwitchRow = DOM::create("div", "", "", "switchRow appTester");
$pageContent->append($testerSwitchRow);

$title = moduleLiteral::get($moduleID, "lbl_testingMessage");
$header = DOM::create("h4", $title);
DOM::append($testerSwitchRow, $header);

$switch = new switchButton("appTesterSwitch");
$testingSwitch = $switch->build(appPlayer::testerStatus())->setAction($moduleID, "appTester")->get();
DOM::append($testerSwitchRow, $testingSwitch);


// Return output
return $pageContent->getReport();
//#section_end#
?>