<?php
//#section#[header]
// Module Declaration
$moduleID = 283;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("DEV", "Core");
importer::import("DEV", "Profiler");
importer::import("ESS", "Protocol");
importer::import("UI", "Forms");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \UI\Modules\MContent;
use \UI\Interactive\forms\switchButton;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Forms\templates\simpleForm;
use \DEV\Profiler\debugger;
use \DEV\Profiler\ui\logViewer;
use \DEV\Core\test\sdkTester;
use \DEV\Core\test\sqlTester;
use \DEV\Core\test\ajaxTester;
use \DEV\Core\test\rsrcTester;
use \DEV\Core\sdk\sdkLibrary;
use \DEV\Core\coreProject;


// Testing Controller Container
$pageContent = new MContent($moduleID);
$pageContent->build($id = "", $class = "coreConfigurator", TRUE);

$targetContainer = "coreConfigTabs";
$targetGroup = "coreConfigSelector";
$navGroup = "pageNavGroup_core";
$navDisplay = "none";

// Navigation
$navHeader = HTML::select(".navTab.tester")->item(0);
NavigatorProtocol::staticNav($navHeader, "coreTesterConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$navHeader = HTML::select(".navTab.debugger")->item(0);
NavigatorProtocol::staticNav($navHeader, "coreDebuggerConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$navHeader = HTML::select(".navTab.profiler")->item(0);
NavigatorProtocol::staticNav($navHeader, "coreResourceProfiler", $targetContainer, $targetGroup, $navGroup, $navDisplay);


// Group Selectors
$navPage = HTML::select(".page.testerPanel")->item(0);
NavigatorProtocol::selector($navPage, $targetGroup);

$navPage = HTML::select(".page.debuggerPanel")->item(0);
NavigatorProtocol::selector($navPage, $targetGroup);

$navPage = HTML::select(".page.profilerPanel")->item(0);
NavigatorProtocol::selector($navPage, $targetGroup);


// Ajax switch
$switchRow = HTML::select(".switchRow.ajax")->item(0);
$switch = new switchButton("ajxTestSwitch");
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;
$switchObject = $switch->build("", ajaxTester::status())->engageModule($moduleID, "ajaxTesting", $attr)->get();
DOM::append($switchRow, $switchObject);

// SQL switch
$switchRow = HTML::select(".switchRow.sql")->item(0);
$switch = new switchButton("sqlTestSwitch");
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;
$switchObject = $switch->build("", sqlTester::status())->engageModule($moduleID, "sqlTesting", $attr)->get();
DOM::append($switchRow, $switchObject);

// Resources switch
$switchRow = HTML::select(".switchRow.rsrc")->item(0);
$switch = new switchButton("rsrcTestSwitch");
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;
$switchObject = $switch->build("", rsrcTester::status())->engageModule($moduleID, "rsrcTesting", $attr)->get();
DOM::append($switchRow, $switchObject);



// SDK Packages Configurator
$packageConfigurator = HTML::select(".packageConfigurator")->item(0);

// Create form
$form = new simpleForm("packageSelectorForm");
$testerForm = $form->build($moduleID, "sdkTesting")->get();
DOM::append($packageConfigurator, $testerForm);

// Core project id
$input = $form->getInput("hidden", "id", coreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

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
{
	$packageList[$library] = $sdkLib->getPackageList($library);
	asort($packageList[$library]);
}
ksort($packageList);

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
$switch = new switchButton("cloggerSwitch");
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;
$loggerSwitch = $switch->build("", logger::status())->engageModule($moduleID, "logger", $attr)->get();
DOM::append($switchRow, $loggerSwitch);

// Logger log
$loggerDataContainer = HTML::select(".loggerDataContainer")->item(0);
if (!logger::status())
	HTML::addClass($loggerDataContainer, "noDisplay");

$logger = new logViewer();
$loggerViewer = $logger->build()->get();
DOM::append($loggerDataContainer, $loggerViewer);

// Debugger Switch
$switchRow = HTML::select(".switchRow.debugger")->item(0);
$switch = new switchButton("cdebuggerSwitch");
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;
$debuggerSwitch = $switch->build("", debugger::status())->engageModule($moduleID, "debugger", $attr)->get();
DOM::append($switchRow, $debuggerSwitch);
	
	
return $pageContent->getReport();
//#section_end#
?>