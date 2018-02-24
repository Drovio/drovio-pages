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
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("ESS", "Protocol");
importer::import("DEV", "Profiler");
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Literals\moduleLiteral;
use \API\Security\account;
use \UI\Developer\logController;
use \UI\Html\HTMLContent;
use \UI\Interactive\forms\switchButton;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Forms\templates\simpleForm;
use \DEV\Profiler\debugger;
use \DEV\Profiler\test\sdkTester;
use \DEV\Profiler\test\sqlTester;
use \DEV\Profiler\test\ajaxTester;
use \DEV\Core\sdk\sdkLibrary;


// Testing Controller Container
$pageContent = new HTMLContent();
$pageContent->build($id = "", $class = "coreConfigurator", TRUE)->get();

$targetContainer = "coreConfigTabs";
$targetGroup = "coreConfigSelector";
$navGroup = "pageNavGroup_core";
$navDisplay = "none";

// Navigation
$title = moduleLiteral::get($moduleID, "lbl_navHeader_tester");
$navHeader = HTML::select(".navTab.tester")->item(0);
DOM::append($navHeader, $title);
NavigatorProtocol::staticNav($navHeader, "coreTesterConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$title = moduleLiteral::get($moduleID, "lbl_navHeader_debugger");
$navHeader = HTML::select(".navTab.debugger")->item(0);
DOM::append($navHeader, $title);
NavigatorProtocol::staticNav($navHeader, "coreDebuggerConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$title = moduleLiteral::get($moduleID, "lbl_navHeader_profiler");
$navHeader = HTML::select(".navTab.profiler")->item(0);
DOM::append($navHeader, $title);
NavigatorProtocol::staticNav($navHeader, "coreResourceProfiler", $targetContainer, $targetGroup, $navGroup, $navDisplay);


// Group Selectors
$navPage = HTML::select(".page.testerPanel")->item(0);
NavigatorProtocol::selector($navPage, $targetGroup);

$navPage = HTML::select(".page.debuggerPanel")->item(0);
NavigatorProtocol::selector($navPage, $targetGroup);

$navPage = HTML::select(".page.profilerPanel")->item(0);
NavigatorProtocol::selector($navPage, $targetGroup);



// SQL switch
$switchRow = HTML::select(".switchRow.sql")->item(0);

$title = moduleLiteral::get($moduleID, "lbl_sqlTestCenter");
DOM::append($switchRow, $title);

$switch = new switchButton("sqlTestSwitch");
$switchObject = $switch->build(sqlTester::status())->setAction($moduleID, "sqlTesting")->get();
DOM::append($switchRow, $switchObject);

// Ajax switch
$switchRow = HTML::select(".switchRow.ajax")->item(0);

$title = moduleLiteral::get($moduleID, "lbl_ajaxTestCenter");
DOM::append($switchRow, $title);

$switch = new switchButton("sqlTestSwitch");
$switchObject = $switch->build(ajaxTester::status())->setAction($moduleID, "ajaxTesting")->get();
DOM::append($switchRow, $switchObject);



// SDK Packages Configurator
$packageConfigurator = HTML::select(".packageConfigurator")->item(0);

$title = moduleLiteral::get($moduleID, "lbl_sdkPackages");
$header = HTML::select(".packageConfigurator .title")->item(0);
DOM::append($header, $title);

// Create form
$form = new simpleForm("packageSelectorForm");
$testerForm = $form->build($moduleID, "sdkTesting")->get();
DOM::append($packageConfigurator, $testerForm);

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
	
	
	
	
// Logger Switch
$switchRow = HTML::select(".switchRow.logger")->item(0);

$title = moduleLiteral::get($moduleID, "lbl_logger");
DOM::append($switchRow, $title);

$switch = new switchButton("cloggerSwitch");
$loggerSwitch = $switch->build(logger::status())->setAction($moduleID, "logger")->get();
DOM::append($switchRow, $loggerSwitch);


// Debugger Switch
$switchRow = HTML::select(".switchRow.debugger")->item(0);

$title = moduleLiteral::get($moduleID, "lbl_debugger");
DOM::append($switchRow, $title);

$switch = new switchButton("debuggerSwitch");
$debuggerSwitch = $switch->build(debugger::status())->setAction($moduleID, "debugger")->get();
DOM::append($switchRow, $debuggerSwitch);

// Logger log
$loggerDataContainer = HTML::select(".loggerDataContainer")->item(0);
if (!logger::status())
	HTML::addClass($loggerDataContainer, "noDisplay");

$logger = new logController();
$loggerViewer = $logger->build()->get();
DOM::append($loggerDataContainer, $loggerViewer);


// Resource Profiler title
$title = moduleLiteral::get($moduleID, "lbl_resourceProfilerTitle");
$header = HTML::select(".resourceProfiler .title")->item(0);
DOM::append($header, $title);

	
	
return $pageContent->getReport();
//#section_end#
?>