<?php
//#section#[header]
// Module Declaration
$moduleID = 268;

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("INU", "Developer");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Navigation\sideBar;
use \UI\Presentation\tabControl;
use \UI\Presentation\notification;
use \INU\Developer\codeEditor;
use \INU\Developer\cssEditor;
use \INU\Developer\redWIDE;
use \DEV\Apps\library\appScript;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Initialize application script
$appID = $_GET['appID'];
$scriptName = $_GET['name'];
$appScript = new appScript($appID, $scriptName);

// Create object id
$objID = $appID."_script_".$scriptName;


// Create Global Container
$globalContainer = $pageContent->build("", "objectGlobalContainer")->get();

// Create Code Form
$form = new simpleForm();
$formElement = $form->build($moduleID, "updateScript", FALSE)->get();
$pageContent->append($formElement);

// Create navigation bar
$tlb = new navigationBar();
$navBar = $tlb->build($dock = navigationBar::TOP, $globalContainer)->get();
DOM::append($formElement, $navBar);

// Save Tool
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertToolbarItem($saveTool);
$deleteTool = DOM::create("span", "", "", "objTool delete");
$tool = $tlb->insertToolbarItem($deleteTool);
$attr = array();
$attr['appID'] = $appID;
$attr['name'] = $scriptName;
$actionFactory->setModuleAction($deleteTool, $moduleID, "deleteScript", "", $attr);

// Hidden form values
$input = $form->getInput($type = "hidden", $name = "appID", $value = $appID, $class = "", $autofocus = FALSE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "name", $value = $scriptName, $class = "", $autofocus = FALSE);
$form->append($input);

// Source Code Editor
$editor = new codeEditor();
$scriptCode = $appScript->get();
$scriptEditor = $editor->build("js", $scriptCode, "scriptCode")->get();
$form->append($scriptEditor);


// Send redWIDE Report Content
$wide = new redWIDE();
return $wide->getReportContent($objID, $scriptName.".js", $pageContent->get());
//#section_end#
?>