<?php
//#section#[header]
// Module Declaration
$moduleID = 106;

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

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
//#section_end#
//#section#[code]
use \API\Developer\profiler\debugger;
use \API\Developer\profiler\tester;
use \API\Developer\components\sdk\sdkLibrary;
use \API\Developer\components\sdk\sdkPackage;
use \API\Resources\literals\moduleLiteral;
use \UI\Developer\logger as UILogger;
use \UI\Html\HTMLContent;
use \UI\Interactive\forms\switchButton;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Forms\templates\simpleForm;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	if (tester::status() === FALSE)
	{
		tester::activate();
		$status = TRUE;
	}
	else
	{
		tester::deactivate();
		$status = FALSE;
	}
	
	// Return switchButton report
	return switchButton::getReport($status);
}

// Testing Controller Container
$content = new HTMLContent();
$content->build($id = "", $class = "debuggerControlPanel")->get();

$tabber = new tabControl();
$testTabber = $tabber->build()->get();
$content->append($testTabber);


// Tester Tab
$header = moduleLiteral::get($moduleID, "lbl_testingCenter");
$page = DOM::create("div", "", "testerController");
$tabber->insertTab("testManager", $header, $page, TRUE);

$cPanel = DOM::create("div", "", "", "cPanel");
DOM::append($page, $cPanel);

// Tester switch
$testerSwitchRow = DOM::create("div", "", "", "switchRow");
DOM::append($cPanel, $testerSwitchRow);

$title = moduleLiteral::get($moduleID, "lbl_testingCenter");
DOM::append($testerSwitchRow, $title);

$switch = new switchButton("testSwitch");
$testingSwitch = $switch->build(tester::status())->setAction($moduleID)->get();
DOM::append($testerSwitchRow, $testingSwitch);


// SQL switch
$SQLSwitchRow = DOM::create("div", "", "", "switchRow");
DOM::append($cPanel, $SQLSwitchRow);

$title = moduleLiteral::get($moduleID, "lbl_sqlTestCenter");
DOM::append($SQLSwitchRow, $title);

$switch = new switchButton("sqlTestSwitch");
$sqlTestingSwitch = $switch->build(tester::SQLStatus())->setAction($moduleID, "sqlTesting")->get();
DOM::append($SQLSwitchRow, $sqlTestingSwitch);

// SDK Configurator
$testerParameters = DOM::create("div", "", "testerConfig", (tester::status() ? "" : " noDisplay"));
DOM::append($page, $testerParameters);

$title = moduleLiteral::get($moduleID, "lbl_sdkPackages");
$pkgLstTitle = DOM::create("h3");
DOM::append($pkgLstTitle, $title);
DOM::append($testerParameters, $pkgLstTitle);

// Create form
$form = new simpleForm("packageSelectorForm", TRUE);
$testerForm = $form->build($moduleID, "packageConfig")->get();
DOM::append($testerParameters, $testerForm);

// Package Selector container
$packageList = DOM::create("div", "", "packageList");
$form->append($packageList);

$gridList = new dataGridList();
$packageGrid = $gridList->build($id = "packageGrid", $checkable = TRUE)->get();
$headers = array();
$headers[] = "Library";
$headers[] = "Package";
$gridList->setHeaders($headers);
DOM::append($packageList, $packageGrid);

// Get Packages
$sdkLib = new sdkLibrary();
$packageList = $sdkLib->getPackageList("", FALSE);
foreach ($packageList as $libName => $packages)
	foreach ($packages as $packageName)
	{
		$row = array();
		$row[] = $libName;
		$row[] = $packageName;
		$checkName = $libName."_".$packageName;
		$gridList->insertRow($row, "pkg[".$checkName."]", sdkPackage::getTesterStatus($libName, $packageName));
	}



// Debugger Tab
$header = moduleLiteral::get($moduleID, "lbl_debugger");
$page = DOM::create("div", "", "debuggerController");
$tabber->insertTab("debugManager", $header, $page, FALSE);

$cPanel = DOM::create("div", "", "", "cPanel");
DOM::append($page, $cPanel);

// Debugger Switch
$dbgSwitchRow = DOM::create("div", "", "", "switchRow");
DOM::append($cPanel, $dbgSwitchRow);

$title = moduleLiteral::get($moduleID, "lbl_debugger");
DOM::append($dbgSwitchRow, $title);

$switch = new switchButton("debuggerSwutch");
$debuggerSwitch = $switch->build(debugger::status())->setAction($moduleID, "debugger")->get();
DOM::append($dbgSwitchRow, $debuggerSwitch);

// System Logger
$logSwitchRow = DOM::create("div", "", "", "switchRow");
DOM::append($cPanel, $logSwitchRow);

$title = moduleLiteral::get($moduleID, "lbl_logger");
DOM::append($logSwitchRow, $title);

$switch = new switchButton("loggerSwitch");
$loggerSwitch = $switch->build(logger::status())->setAction($moduleID, "logger")->get();
DOM::append($logSwitchRow, $loggerSwitch);
	
// Logger log
$loggerDataContainer = DOM::create("div", "", "loggerDataContainer", (logger::status() ? "" : " noDisplay"));
DOM::append($page, $loggerDataContainer);

$title = moduleLiteral::get($moduleID, "lbl_loggerTitle");
$logTitle = DOM::create("h3");
DOM::append($logTitle, $title);
DOM::append($loggerDataContainer, $logTitle);

$logger = new UILogger();
$loggerViewer = $logger->build()->get();
DOM::append($loggerDataContainer, $loggerViewer);

// Resource Profiler Tab
$header = moduleLiteral::get($moduleID, "lbl_resourceProfiler");
$page = DOM::create("div", "", "resourceProfiler");
$tabber->insertTab("resourceProfiler", $header, $page, FALSE);

$title = DOM::create("h3", moduleLiteral::get($moduleID, "lbl_handleResources"));
DOM::append($page, $title);

$resourceListContainer = DOM::create("div", "", "", "resourceListContainer");
DOM::append($page, $resourceListContainer);

$cssResources = DOM::create("div", "", "", "cssResources");
DOM::append($resourceListContainer, $cssResources);

$jsResources = DOM::create("div", "", "", "jsResources");
DOM::append($resourceListContainer, $jsResources);

return $content->getReport();
//#section_end#
?>