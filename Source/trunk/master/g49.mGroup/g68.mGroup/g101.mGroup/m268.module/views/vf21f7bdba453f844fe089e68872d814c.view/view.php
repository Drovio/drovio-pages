<?php
//#section#[header]
// Module Declaration
$moduleID = 268;

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
importer::import("API", "Literals");
importer::import("DEV", "Apps");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Navigation\sideBar;
use \UI\Presentation\tabControl;
use \UI\Presentation\notification;
use \UI\Developer\codeEditor;
use \UI\Developer\codeMirror;
use \UI\Developer\devTabber;
use \DEV\Apps\library\appScript;

// Initialize application script
$appID = engine::getVar('id');
$scriptName = engine::getVar('name');
$appScript = new appScript($appID, $scriptName);

if (engine::isPost())
{
	// Update ScriptCode
	$status = $appScript->update($_POST['scriptCode']);
	
	// Build Notification
	$reportNtf = new notification();
	if ($status === TRUE)
	{
		$reportNtf->build($type = notification::SUCCESS, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("success", "success.save_success");
	}
	else if ($status === FALSE)
	{
		$reportNtf->build($type = notification::ERROR, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("error", "err.save_error");
	}
	else
	{
		$reportNtf->build($type = notification::WARNING, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = DOM::create("span", "There are syntax errors in this document.");
	}
	
	$reportNtf->append($reportMessage);
	$notification = $reportNtf->get();
	
	return devTabber::getNotificationResult($notification, ($status === TRUE));
}

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();


// Create Global Container
$globalContainer = $pageContent->build("", "objectGlobalContainer")->get();

// Create Code Form
$form = new simpleForm();
$formElement = $form->build("", FALSE)->engageModule($moduleID)->get();
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
$attr['id'] = $appID;
$attr['name'] = $scriptName;
$actionFactory->setModuleAction($deleteTool, $moduleID, "deleteScript", "", $attr);

// Hidden form values
$input = $form->getInput($type = "hidden", $name = "id", $value = $appID, $class = "", $autofocus = FALSE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "name", $value = $scriptName, $class = "", $autofocus = FALSE);
$form->append($input);

// Source Code Editor
$editor = new codeMirror($type = codeMirror::JS, "scriptCode");
$scriptCode = $appScript->get();
$scriptEditor = $editor->build($scriptCode)->get();
$form->append($scriptEditor);


// Send redWIDE Report Content
$wide = new devTabber();
$objID = $appID."_script_".$scriptName;
return $wide->getReportContent($objID, $scriptName.".js", $pageContent->get());
//#section_end#
?>