<?php
//#section#[header]
// Module Declaration
$moduleID = 267;

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
use \DEV\Apps\library\appStyle;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Initialize application script
$appID = $_GET['appID'];
$styleName = $_GET['name'];
$appStyle = new appStyle($appID, $styleName);

// Create object id
$objID = $appID."_style_".$styleName;


// Create Global Container
$globalContainer = $pageContent->build("", "objectGlobalContainer")->get();

// Create Code Form
$form = new simpleForm();
$formElement = $form->build($moduleID, "updateStyle", FALSE)->get();
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
$attr['name'] = $styleName;
$actionFactory->setModuleAction($deleteTool, $moduleID, "deleteStyle", "", $attr);

// Hidden form values
$input = $form->getInput($type = "hidden", $name = "appID", $value = $appID, $class = "", $autofocus = FALSE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "name", $value = $styleName, $class = "", $autofocus = FALSE);
$form->append($input);

// Source Code Editor
$editor = new codeEditor();
$styleCode = $appStyle->get();
$scriptEditor = $editor->build("css", $styleCode, "styleCode")->get();
$form->append($scriptEditor);


// Send redWIDE Report Content
$wide = new redWIDE();
return $wide->getReportContent($objID, $styleName.".css", $pageContent->get());
//#section_end#
?>