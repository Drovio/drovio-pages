<?php
//#section#[header]
// Module Declaration
$moduleID = 209;

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
importer::import("API", "Developer");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("DEV", "Profiler");
//#section_end#
//#section#[code]
use \DEV\Profiler\debugger;
use \DEV\Profiler\test\sdkTester;
use \DEV\Profiler\test\sqlTester;
use \DEV\Profiler\test\ajaxTester;
use \API\Developer\components\sdk\sdkLibrary;
use \API\Resources\literals\moduleLiteral;
use \API\Security\account;
use \UI\Developer\logController;
use \UI\Html\HTMLContent;
use \UI\Interactive\forms\switchButton;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Forms\templates\simpleForm;


// Testing Controller Container
$pageContent = new HTMLContent();
$pageContent->build($id = "", $class = "coreConfigurator")->get();



// Logger Switch
$logSwitchRow = DOM::create("div", "", "", "switchRow logger");
$pageContent->append($logSwitchRow);

$title = moduleLiteral::get($moduleID, "lbl_logger");
DOM::append($logSwitchRow, $title);

$switch = new switchButton("loggerSwitch");
$loggerSwitch = $switch->build(logger::status())->setAction($moduleID, "logger")->get();
DOM::append($logSwitchRow, $loggerSwitch);


// Debugger Switch
$dbgSwitchRow = DOM::create("div", "", "", "switchRow");
$pageContent->append($dbgSwitchRow);

$title = moduleLiteral::get($moduleID, "lbl_debugger");
DOM::append($dbgSwitchRow, $title);

$switch = new switchButton("debuggerSwitch");
$debuggerSwitch = $switch->build(debugger::status())->setAction($moduleID, "debugger")->get();
DOM::append($dbgSwitchRow, $debuggerSwitch);




// Logger log
$loggerDataContainer = DOM::create("div", "", "", "loggerDataContainer".(logger::status() ? "" : " noDisplay"));
$pageContent->append($loggerDataContainer);

$logger = new logController();
$loggerViewer = $logger->build()->get();
DOM::append($loggerDataContainer, $loggerViewer);




$tabber = new tabControl();
$testTabber = $tabber->build("coreConfigTab", TRUE)->get();
$pageContent->append($testTabber);


// Tester Tab
$header = moduleLiteral::get($moduleID, "lbl_tabHeader_tester");
$testerPanel = DOM::create("div", "", "", "testerPanel");
$tabber->insertTab("testManager", $header, $testerPanel, TRUE);

// Resource Profiler Tab
$header = moduleLiteral::get($moduleID, "lbl_tabHeader_resourceProfiler");
$resourceProfilerPanel = DOM::create("div", "", "", "resourceProfilerPanel");
$tabber->insertTab("resourceProfiler", $header, $resourceProfilerPanel, FALSE);



// SQL switch
$switchRow = DOM::create("div", "", "", "switchRow f-right");
DOM::append($testerPanel, $switchRow);

$title = moduleLiteral::get($moduleID, "lbl_sqlTestCenter");
DOM::append($switchRow, $title);

$switch = new switchButton("sqlTestSwitch");
$switchObject = $switch->build(sqlTester::status())->setAction($moduleID, "sqlTesting")->get();
DOM::append($switchRow, $switchObject);

// Ajax switch
$switchRow = DOM::create("div", "", "", "switchRow");
DOM::append($testerPanel, $switchRow);

$title = moduleLiteral::get($moduleID, "lbl_ajaxTestCenter");
DOM::append($switchRow, $title);

$switch = new switchButton("sqlTestSwitch");
$switchObject = $switch->build(ajaxTester::status())->setAction($moduleID, "ajaxTesting")->get();
DOM::append($switchRow, $switchObject);



// SDK Packages Configurator
$testerParameters = DOM::create("div", "", "", "packageConfigurator");
DOM::append($testerPanel, $testerParameters);

$title = moduleLiteral::get($moduleID, "lbl_sdkPackages");
$pkgLstTitle = DOM::create("h4", $title);
DOM::append($testerParameters, $pkgLstTitle);

// Create form
$form = new simpleForm("packageSelectorForm");
$testerForm = $form->build($moduleID, "sdkTesting")->get();
DOM::append($testerParameters, $testerForm);

// Package Selector container
$packageList = DOM::create("div", "", "", "packageList");
$form->append($packageList);

$gridList = new dataGridList();
$packageGrid = $gridList->build($id = "packageGrid", $checkable = TRUE)->get();
$headers = array();
$headers[] = "Library";
$headers[] = "Package";
$gridList->setHeaders($headers);
DOM::append($packageList, $packageGrid);

// Get All Packages
$sdkLib = new sdkLibrary();
$libraries = $sdkLib->getList();
$packageList = array();
foreach ($libraries as $library)
	$packageList[$library] = $sdkLib->getPackageList($library);

foreach ($packageList as $libName => $packages)
	foreach ($packages as $packageName)
	{
		$row = array();
		$row[] = $libName;
		$row[] = $packageName;
		$checkName = $libName."_".$packageName;
		$gridList->insertRow($row, "pkg[".$checkName."]", sdkTester::libPackageStatus($libName, $packageName));
	}
	

// Resource Profiler
$resourceListContainer = DOM::create("div", "", "", "resourceListContainer");
DOM::append($resourceProfilerPanel, $resourceListContainer);

$cssResources = DOM::create("div", "", "", "cssResources");
DOM::append($resourceListContainer, $cssResources);

$jsResources = DOM::create("div", "", "", "jsResources");
DOM::append($resourceListContainer, $jsResources);
	
	
return $pageContent->getReport();
//#section_end#
?>