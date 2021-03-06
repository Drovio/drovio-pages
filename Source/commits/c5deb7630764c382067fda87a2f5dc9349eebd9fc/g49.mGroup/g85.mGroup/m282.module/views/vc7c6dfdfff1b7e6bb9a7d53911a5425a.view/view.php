<?php
//#section#[header]
// Module Declaration
$moduleID = 282;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("DEV", "Modules");
importer::import("DEV", "Profiler");
importer::import("SYS", "Comm");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Model\modules\mGroup;
use \API\Profile\account;
use \UI\Modules\MContent;
use \UI\Interactive\forms\switchButton;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Forms\templates\simpleForm;
use \DEV\Profiler\debugger;
use \DEV\Profiler\ui\logViewer;
use \DEV\Modules\modulesProject;
use \DEV\Modules\test\moduleTester;
use \DEV\Modules\test\moduleSecurityTester;


// Testing Controller Container
$pageContent = new MContent($moduleID);
$pageContent->build($id = "", $class = "modulesConfigurator", TRUE)->get();


$targetContainer = "modulesConfigTabs";
$targetGroup = "modulesConfigSelector";
$navGroup = "pageNavGroup_modules";
$navDisplay = "none";

// Navigation
$navHeader = HTML::select(".navTab.tester")->item(0);
$pageContent->setStaticNav($navHeader, "modulesTesterConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$navHeader = HTML::select(".navTab.debugger")->item(0);
$pageContent->setStaticNav($navHeader, "coreDebuggerConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$navHeader = HTML::select(".navTab.profiler")->item(0);
$pageContent->setStaticNav($navHeader, "modulesResourceProfiler", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$navHeader = HTML::select(".navTab.support")->item(0);
$pageContent->setStaticNav($navHeader, "modulesPublishSupport", $targetContainer, $targetGroup, $navGroup, $navDisplay);


// Group Selectors
$navPage = HTML::select(".page.testerPanel")->item(0);
$pageContent->setNavigationGroup($navPage, $targetGroup);

$navPage = HTML::select(".page.debuggerPanel")->item(0);
$pageContent->setNavigationGroup($navPage, $targetGroup);

$navPage = HTML::select(".page.profilerPanel")->item(0);
$pageContent->setNavigationGroup($navPage, $targetGroup);

$navPage = HTML::select(".page.supportPanel")->item(0);
$pageContent->setNavigationGroup($navPage, $targetGroup);


// Tester switch
$switchRow = HTML::select(".testerPanel .switchRow.mtester")->item(0);
$switch = new switchButton("mtestSwitch");
$attr = array();
$attr['id'] = modulesProject::PROJECT_ID;
$testingSwitch = $switch->build("", moduleTester::status())->engageModule($moduleID, "moduleTester", $attr)->get();
DOM::append($switchRow, $testingSwitch);

// Security Tester switch
$switchRow = HTML::select(".testerPanel .switchRow.mstester")->item(0);
$switch = new switchButton("mstestSwitch");
$attr = array();
$attr['id'] = modulesProject::PROJECT_ID;
$testingSwitch = $switch->build("", moduleSecurityTester::status())->engageModule($moduleID, "securityTester", $attr)->get();
DOM::append($switchRow, $testingSwitch);

// Create form
$moduleConfigurator = HTML::select(".moduleConfigurator")->item(0);
$form = new simpleForm("moduleSelectorForm");
$testerForm = $form->build()->engageModule($moduleID, "mdlTestingList")->get();
DOM::append($moduleConfigurator, $testerForm);

// Project ID
$input = $form->getInput($type = "hidden", $name = "id", $value = modulesProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

// Package Selector container
$modulesList = DOM::create("div", "", "", "modulesList");
$form->append($modulesList);

$gridList = new dataGridList();
$moduleGrid = $gridList->build($id = "moduleGrid", $checkable = TRUE)->get();
DOM::append($modulesList, $moduleGrid);

// Ratios
$ratios = array();
$ratios[] = 0.2;
$ratios[] = 0.8;
$gridList->setColumnRatios($ratios);

$headers = array();
$headers[] = "ID";
$headers[] = "Module";
$gridList->setHeaders($headers);



// Get developer modules
$dbc = new dbConnection();
$dbq = module::getQuery($moduleID, "get_developer_modules");
$attr = array();
$attr['aid'] = account::getAccountID();
$result = $dbc->execute($dbq, $attr);
$devModules = $dbc->fetch($result, TRUE);

// Get Tester Modules
$dbq = module::getQuery($moduleID, "get_tester_modules");
$attr = array();
$attr['aid'] = account::getAccountID();
$result = $dbc->execute($dbq, $attr);
$testerModules = $dbc->fetch($result, TRUE);


$modules = array();
foreach ($devModules as $module)
	$modules[] = $module['id'];
	
foreach ($testerModules as $module)
	$modules[] = $module['id'];

foreach ($modules as $module_id)
{
	$row = array();
	$row[] = "".$module_id;
	
	// Get module path/trail
	$moduleInfo = module::info($module_id);
	$modulePath = mGroup::getTrail($moduleInfo['group_id']);
	$modulePath .= module::getDirectoryName($module_id);
	$row[] = $modulePath;
	
	// Insert row
	$gridList->insertRow($row, "mdl[".$module_id."]", moduleTester::status() != TRUE && moduleTester::status($module_id));
}



// Logger Switch
$switchRow = HTML::select(".switchRow.logger")->item(0);
$switch = new switchButton("mloggerSwitch");
$attr = array();
$attr['id'] = modulesProject::PROJECT_ID;
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
$switch = new switchButton("mdebuggerSwitch");
$attr = array();
$attr['id'] = modulesProject::PROJECT_ID;
$debuggerSwitch = $switch->build("", debugger::status())->engageModule($moduleID, "debugger", $attr)->get();
DOM::append($switchRow, $debuggerSwitch);

return $pageContent->getReport();
//#section_end#
?>