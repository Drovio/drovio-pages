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
use \API\Developer\profiler\moduleTester;
use \API\Developer\profiler\sdkTester;
use \API\Developer\profiler\sqlTester;
use \API\Developer\profiler\ajaxTester;
use \API\Developer\components\sdk\sdkLibrary;
use \API\Developer\components\sdk\sdkPackage;
use \API\Resources\literals\moduleLiteral;
use \UI\Developer\logController;
use \UI\Html\HTMLContent;
use \UI\Interactive\forms\switchButton;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Forms\templates\simpleForm;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	if (moduleTester::status() === FALSE)
	{
		moduleTester::activate();
		$status = TRUE;
	}
	else
	{
		moduleTester::deactivate();
		$status = FALSE;
	}
	
	// Return switchButton report
	return switchButton::getReport($status);
}

// Testing Controller Container
$content = new HTMLContent();
$content->build($id = "", $class = "developerControlPanel")->get();

$tabber = new tabControl();
$testTabber = $tabber->build()->get();
$content->append($testTabber);


// Tester Tab
$header = moduleLiteral::get($moduleID, "lbl_testingCenter");
$testerPanel = DOM::create("div", "", "", "testerPanel");
$tabber->insertTab("testManager", $header, $testerPanel, TRUE);

// Debugger Tab
$header = moduleLiteral::get($moduleID, "lbl_debugger");
$debuggerPanel = DOM::create("div", "", "", "debuggerPanel");
$tabber->insertTab("debugManager", $header, $debuggerPanel, FALSE);

// Resource Profiler Tab
$header = moduleLiteral::get($moduleID, "lbl_resourceProfiler");
$resourceProfilerPanel = DOM::create("div", "", "", "resourceProfilerPanel");
$tabber->insertTab("resourceProfiler", $header, $resourceProfilerPanel, FALSE);





// Tester Panel
$container = DOM::create("div", "", "", "testerPanels");
DOM::append($testerPanel, $container);

$moduleTester = DOM::create("div", "", "", "moduleTester panel");
DOM::append($container, $moduleTester);

$title = moduleLiteral::get($moduleID, "lbl_moduleTester_header");
$header = DOM::create("h3", $title);
DOM::append($moduleTester, $header);

// Tester switch
$testerSwitchRow = DOM::create("div", "", "", "switchRow");
DOM::append($moduleTester, $testerSwitchRow);

$title = moduleLiteral::get($moduleID, "lbl_moduleTester");
DOM::append($testerSwitchRow, $title);

$switch = new switchButton("testSwitch");
$testingSwitch = $switch->build(moduleTester::status())->setAction($moduleID)->get();
DOM::append($testerSwitchRow, $testingSwitch);




$coreTester = DOM::create("div", "", "", "coreTester panel");
DOM::append($container, $coreTester);

$title = moduleLiteral::get($moduleID, "lbl_coreTester_header");
$header = DOM::create("h3", $title);
DOM::append($coreTester, $header);




// SQL switch
$switchRow = DOM::create("div", "", "", "switchRow f-right");
DOM::append($coreTester, $switchRow);

$title = moduleLiteral::get($moduleID, "lbl_sqlTestCenter");
DOM::append($switchRow, $title);

$switch = new switchButton("sqlTestSwitch");
$switchObject = $switch->build(sqlTester::status())->setAction($moduleID, "sqlTesting")->get();
DOM::append($switchRow, $switchObject);

// SDK switch
$switchRow = DOM::create("div", "", "", "switchRow");
DOM::append($coreTester, $switchRow);

$title = moduleLiteral::get($moduleID, "lbl_sdkTestCenter");
DOM::append($switchRow, $title);

$switch = new switchButton("sdkTestSwitch");
$switchObject = $switch->build(sdkTester::status())->get();//setAction($moduleID, "sdkTesting")->get();
DOM::append($switchRow, $switchObject);

// Ajax switch
$switchRow = DOM::create("div", "", "", "switchRow");
DOM::append($coreTester, $switchRow);

$title = moduleLiteral::get($moduleID, "lbl_ajaxTestCenter");
DOM::append($switchRow, $title);

$switch = new switchButton("sqlTestSwitch");
$switchObject = $switch->build(ajaxTester::status())->get();//->setAction($moduleID, "ajaxTesting")->get();
DOM::append($switchRow, $switchObject);



// SDK Packages Configurator
$testerParameters = DOM::create("div", "", "", "packageConfigurator".(sdkTester::status() ? "" : " noDisplay"));
DOM::append($coreTester, $testerParameters);

$title = moduleLiteral::get($moduleID, "lbl_sdkPackages");
$pkgLstTitle = DOM::create("h4");
DOM::append($pkgLstTitle, $title);
DOM::append($testerParameters, $pkgLstTitle);

// Create form
$form = new simpleForm("packageSelectorForm", TRUE);
$testerForm = $form->build($moduleID, "packageConfig")->get();
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





$title = moduleLiteral::get($moduleID, "lbl_debugger");
$header = DOM::create("h3", $title);
DOM::append($debuggerPanel, $header);


$cPanel = DOM::create("div", "", "", "cPanel");
DOM::append($debuggerPanel, $cPanel);

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
DOM::append($debuggerPanel, $loggerDataContainer);

$title = moduleLiteral::get($moduleID, "lbl_loggerTitle");
$logTitle = DOM::create("h3");
DOM::append($logTitle, $title);
DOM::append($loggerDataContainer, $logTitle);

$logger = new logController();
$loggerViewer = $logger->build()->get();
DOM::append($loggerDataContainer, $loggerViewer);






$title = DOM::create("h3", moduleLiteral::get($moduleID, "lbl_handleResources"));
DOM::append($resourceProfilerPanel, $title);

$resourceListContainer = DOM::create("div", "", "", "resourceListContainer");
DOM::append($resourceProfilerPanel, $resourceListContainer);

$cssResources = DOM::create("div", "", "", "cssResources");
DOM::append($resourceListContainer, $cssResources);

$jsResources = DOM::create("div", "", "", "jsResources");
DOM::append($resourceListContainer, $jsResources);

return $content->getReport();
//#section_end#
?>