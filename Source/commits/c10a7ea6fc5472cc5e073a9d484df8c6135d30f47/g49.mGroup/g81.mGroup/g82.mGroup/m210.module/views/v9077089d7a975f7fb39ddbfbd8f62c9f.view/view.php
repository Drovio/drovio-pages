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
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Developer\profiler\moduleTester;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \API\Security\account;
use \UI\Html\HTMLContent;
use \UI\Interactive\forms\switchButton;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Forms\templates\simpleForm;


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


$moduleTester = DOM::create("div", "", "", "moduleTester panel");
$pageContent->append($moduleTester);

// Tester switch
$testerSwitchRow = DOM::create("div", "", "", "switchRow");
DOM::append($moduleTester, $testerSwitchRow);

$title = moduleLiteral::get($moduleID, "lbl_allModuleTester");
DOM::append($testerSwitchRow, $title);

$switch = new switchButton("testSwitch");
$testingSwitch = $switch->build(moduleTester::status())->setAction($moduleID)->get();
DOM::append($testerSwitchRow, $testingSwitch);


// Module Configurator
$moduleConfigurator = DOM::create("div", "", "", "moduleConfigurator");
DOM::append($moduleTester, $moduleConfigurator);

$title = moduleLiteral::get($moduleID, "lbl_testerModules");
$mdlTitle = DOM::create("h4", $title);
DOM::append($moduleConfigurator, $mdlTitle);

// Create form
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


return $pageContent->getReport();
//#section_end#
?>