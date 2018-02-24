<?php
//#section#[header]
// Module Declaration
$moduleID = 133;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

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
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("DEV", "Profiler");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \UI\Modules\MContent;
use \UI\Interactive\forms\switchButton;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Forms\templates\simpleForm;
use \DEV\Profiler\debugger;
use \DEV\Apps\application;
use \DEV\Apps\test\appTester;
use \DEV\Apps\test\sourceTester;
use \DEV\Apps\test\viewTester;
use \DEV\Apps\source\srcPackage;

// Get application id
$appID = $_GET['projectID'];

// Testing Controller Container
$pageContent = new MContent($moduleID);
$pageContent->build($id = "", $class = "appConfigurator", TRUE);

$targetContainer = "appConfigTabs_".$appID;
$targetGroup = "appConfigSelector_".$appID;
$navGroup = "pageNavGroup_app_".$appID;
$navDisplay = "none";

$targetContainerElement = HTML::select(".appConfigurator .configTabs")->item(0);
DOM::attr($targetContainerElement, "id", $targetContainer);

// Navigation
$navHeader = HTML::select(".navTab.tester")->item(0);
NavigatorProtocol::staticNav($navHeader, "appTesterConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$navHeader = HTML::select(".navTab.debugger")->item(0);
NavigatorProtocol::staticNav($navHeader, "appDebuggerConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$navHeader = HTML::select(".navTab.profiler")->item(0);
NavigatorProtocol::staticNav($navHeader, "appResourceProfiler", $targetContainer, $targetGroup, $navGroup, $navDisplay);


// Group Selectors
$navPage = HTML::select(".page.testerPanel")->item(0);
NavigatorProtocol::selector($navPage, $targetGroup);

$navPage = HTML::select(".page.debuggerPanel")->item(0);
NavigatorProtocol::selector($navPage, $targetGroup);

$navPage = HTML::select(".page.profilerPanel")->item(0);
NavigatorProtocol::selector($navPage, $targetGroup);



// Views switch
$switchRow = HTML::select(".switchRow.views")->item(0);

$switch = new switchButton("viewsTestSwitch_app".$appID);
$attr = array();
$attr['id'] = $appID;
$switchObject = $switch->build(viewTester::status($appID))->setAction($moduleID, "testAllViews", $attr)->get();
DOM::append($switchRow, $switchObject);

// App View Configurator
$viewConfigurator = HTML::select(".viewConfigurator")->item(0);

// Create form
$form = new simpleForm("viewSelectorForm_app".$appID);
$testerForm = $form->build($moduleID, "viewsTesting")->get();
DOM::append($viewConfigurator, $testerForm);

// Application id
$input = $form->getInput($type = "hidden", $name = "id", $value = $appID);
$form->append($input);

// View Selector container
$viewList = DOM::create("div", "", "", "viewList");
$form->append($viewList);

$gridList = new dataGridList();
$viewGrid = $gridList->build($id = "viewGrid", $checkable = TRUE)->get();
$headers = array();
$headers[] = "View Name";
$gridList->setHeaders($headers);
DOM::append($viewList, $viewGrid);

// Get All Packages
$app = new application($appID);
$views = $app->getViews();
foreach ($views as $viewName)
{
	$row = array();
	$row[] = $viewName;
	$gridList->insertRow($row, "view[".$viewName."]", viewTester::status($appID, $viewName));
}



// Application Source Packages Configurator
$packageConfigurator = HTML::select(".packageConfigurator")->item(0);

// Create form
$form = new simpleForm("packageSelectorForm_app".$appID);
$testerForm = $form->build($moduleID, "srcTesting")->get();
DOM::append($packageConfigurator, $testerForm);

// Application id
$input = $form->getInput($type = "hidden", $name = "id", $value = $appID);
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
$srcp = new srcPackage($appID);
$packages = $srcp->getList();
foreach ($packages as $packageName)
{
	$row = array();
	$row[] = srcPackage::LIB_NAME;
	$row[] = $packageName;
	$gridList->insertRow($row, "pkg[".$packageName."]", sourceTester::status($appID, $packageName));
}

// Debugger Switch
$switchRow = HTML::select(".switchRow.debugger")->item(0);
$switch = new switchButton("appdebuggerSwitch_".mt_rand());
$debuggerSwitch = $switch->build(debugger::status())->setAction($moduleID, "debugger")->get();
DOM::append($switchRow, $debuggerSwitch);
	
	
return $pageContent->getReport();
//#section_end#
?>