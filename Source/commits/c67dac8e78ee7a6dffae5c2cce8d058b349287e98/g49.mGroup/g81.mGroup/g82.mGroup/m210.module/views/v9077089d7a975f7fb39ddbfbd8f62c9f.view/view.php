<?php
//#section#[header]
// Module Declaration
$moduleID = 210;

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
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("DEV", "Profiler");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \SYS\Comm\db\dbConnection;
use \API\Model\sql\dbQuery;
use \API\Security\account;
use \UI\Developer\logController;
use \UI\Modules\MContent;
use \UI\Interactive\forms\switchButton;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Forms\templates\simpleForm;
use \DEV\Profiler\debugger;
use \DEV\Profiler\test\moduleTester;


// Testing Controller Container
$pageContent = new MContent($moduleID);
$pageContent->build($id = "", $class = "modulesConfigurator", TRUE)->get();

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


$targetContainer = "modulesConfigTabs";
$targetGroup = "modulesConfigSelector";
$navGroup = "pageNavGroup_modules";
$navDisplay = "none";

// Navigation
$navHeader = HTML::select(".navTab.tester")->item(0);
NavigatorProtocol::staticNav($navHeader, "modulesTesterConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$navHeader = HTML::select(".navTab.debugger")->item(0);
NavigatorProtocol::staticNav($navHeader, "coreDebuggerConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$navHeader = HTML::select(".navTab.profiler")->item(0);
NavigatorProtocol::staticNav($navHeader, "modulesResourceProfiler", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$navHeader = HTML::select(".navTab.support")->item(0);
NavigatorProtocol::staticNav($navHeader, "modulesPublishSupport", $targetContainer, $targetGroup, $navGroup, $navDisplay);


// Group Selectors
$navPage = HTML::select(".page.testerPanel")->item(0);
NavigatorProtocol::selector($navPage, $targetGroup);

$navPage = HTML::select(".page.debuggerPanel")->item(0);
NavigatorProtocol::selector($navPage, $targetGroup);

$navPage = HTML::select(".page.profilerPanel")->item(0);
NavigatorProtocol::selector($navPage, $targetGroup);

$navPage = HTML::select(".page.supportPanel")->item(0);
NavigatorProtocol::selector($navPage, $targetGroup);


// Tester switch
$switchRow = HTML::select(".testerPanel .switchRow")->item(0);
$switch = new switchButton("mtestSwitch");
$testingSwitch = $switch->build(moduleTester::status())->setAction($moduleID)->get();
DOM::append($switchRow, $testingSwitch);

// Create form
$moduleConfigurator = HTML::select(".moduleConfigurator")->item(0);
$form = new simpleForm("moduleSelectorForm");
$testerForm = $form->build($moduleID, "testerModules")->get();
DOM::append($moduleConfigurator, $testerForm);

// Package Selector container
$modulesList = DOM::create("div", "", "", "modulesList");
$form->append($modulesList);

$gridList = new dataGridList();
$moduleGrid = $gridList->build($id = "moduleGrid", $checkable = TRUE)->get();
$headers = array();
$headers[] = "ID";
$headers[] = "Module";
$gridList->setHeaders($headers);
DOM::append($modulesList, $moduleGrid);


// Get developer modules
$dbc = new dbConnection();
$dbq = new dbQuery("564007386", "security.privileges.developer");
$attr = array();
$attr['aid'] = account::getAccountID();
$result = $dbc->execute($dbq, $attr);
$devModules = $dbc->fetch($result, TRUE);

// Get Tester Modules
$dbq = new dbQuery("1747706539", "security.privileges.tester");
$attr = array();
$attr['aid'] = account::getAccountID();
$result = $dbc->execute($dbq, $attr);
$testerModules = $dbc->fetch($result, TRUE);


$modules = array();
foreach ($devModules as $module)
	$modules[$module['id']] = $module['title'];
	
foreach ($testerModules as $module)
	$modules[$module['id']] = $module['title'];

foreach ($modules as $module_id => $module_title)
{
	$row = array();
	$row[] = "".$module_id;
	$row[] = $module_title;
	$gridList->insertRow($row, "mdl[".$module_id."]", moduleTester::status() != "all" && moduleTester::status($module_id));
}



// Logger Switch
$switchRow = HTML::select(".switchRow.logger")->item(0);
$switch = new switchButton("mloggerSwitch");
$loggerSwitch = $switch->build(logger::status())->setAction($moduleID, "logger")->get();
DOM::append($switchRow, $loggerSwitch);

// Logger log
$loggerDataContainer = HTML::select(".loggerDataContainer")->item(0);
if (!logger::status())
	HTML::addClass($loggerDataContainer, "noDisplay");

$logger = new logController();
$loggerViewer = $logger->build()->get();
DOM::append($loggerDataContainer, $loggerViewer);


// Debugger Switch
$switchRow = HTML::select(".switchRow.debugger")->item(0);
$switch = new switchButton("mdebuggerSwitch");
$debuggerSwitch = $switch->build(debugger::status())->setAction($moduleID, "debugger")->get();
DOM::append($switchRow, $debuggerSwitch);

return $pageContent->getReport();
//#section_end#
?>