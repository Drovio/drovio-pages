<?php
//#section#[header]
// Module Declaration
$moduleID = 210;

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
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("ESS", "Protocol");
importer::import("DEV", "Profiler");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \API\Security\account;
use \UI\Developer\logController;
use \UI\Html\HTMLContent;
use \UI\Interactive\forms\switchButton;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Forms\templates\simpleForm;
use \DEV\Profiler\debugger;
use \DEV\Profiler\test\moduleTester;


// Testing Controller Container
$pageContent = new HTMLContent();
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
$title = moduleLiteral::get($moduleID, "lbl_navHeader_allModules");
$navHeader = HTML::select(".navTab.tester")->item(0);
DOM::append($navHeader, $title);
NavigatorProtocol::staticNav($navHeader, "modulesTesterConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$title = moduleLiteral::get($moduleID, "lbl_navHeader_debugger");
$navHeader = HTML::select(".navTab.debugger")->item(0);
DOM::append($navHeader, $title);
NavigatorProtocol::staticNav($navHeader, "coreDebuggerConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$title = moduleLiteral::get($moduleID, "lbl_navHeader_profiler");
$navHeader = HTML::select(".navTab.profiler")->item(0);
DOM::append($navHeader, $title);
NavigatorProtocol::staticNav($navHeader, "modulesResourceProfiler", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$title = moduleLiteral::get($moduleID, "lbl_navHeader_support");
$navHeader = HTML::select(".navTab.support")->item(0);
DOM::append($navHeader, $title);
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

$title = moduleLiteral::get($moduleID, "lbl_allModuleTester");
DOM::append($switchRow, $title);

$switch = new switchButton("testSwitch");
$testingSwitch = $switch->build(moduleTester::status())->setAction($moduleID)->get();
DOM::append($switchRow, $testingSwitch);


// Module Configurator
$title = moduleLiteral::get($moduleID, "lbl_testerModules");
$header = HTML::select(".moduleConfigurator .title")->item(0);
DOM::append($header, $title);

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
$dbc = new interDbConnection();
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

$title = moduleLiteral::get($moduleID, "lbl_logger");
DOM::append($switchRow, $title);

$switch = new switchButton("mloggerSwitch");
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


// Modules Support
$title = moduleLiteral::get($moduleID, "lbl_supportModulesTitle");
$header = HTML::select(".supportPanel .title")->item(0);
DOM::append($header, $title);

return $pageContent->getReport();
//#section_end#
?>