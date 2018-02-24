<?php
//#section#[header]
// Module Declaration
$moduleID = 390;

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
importer::import("DEV", "WebTemplates");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Developer\devTabber;
use \UI\Developer\codeEditor;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Presentation\notification;
use \DEV\WebTemplates\templateThemeJS;

$templateID = engine::getVar("id");
$themeName = engine::getVar("tname");
$jsName = engine::getVar("js_name");
$themeJS = new templateThemeJS($templateID, $themeName, $jsName);
if (engine::isPost())
{
	// Update js
	$status = $themeJS->update($_POST['jsCode']);
	
	// Build Notification
	$reportNtf = new notification();
	if ($status)
	{
		$reportNtf->build($type = notification::SUCCESS, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("success", "success.save_success");
	}
	else
	{
		$reportNtf->build($type = notification::ERROR, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("error", "err.save_error");
	}
	
	$reportNtf->append($reportMessage);
	$reportNtf->append($extraContainer);
	$notification = $reportNtf->get();
	
	return devTabber::getNotificationResult($notification, ($status === TRUE));
}

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("", "themeJsEditor");

// Initialize Editor Form
$form = new simpleForm("", TRUE);
$pageForm = $form->build("", FALSE)->engageModule($moduleID, "editJS")->get();
$pageContent->append($pageForm);

// Template id
$input = $form->getInput($type = "hidden", $name = "id", $value = $templateID, $class = "", $autofocus = FALSE);
$form->append($input);
// Template name
$hidden = $form->getInput($type = "hidden", $name = "tname", $value = $themeName);
$form->append($hidden);
// Css name
$hidden = $form->getInput($type = "hidden", $name = "js_name", $value = $jsName);
$form->append($hidden);


// Create Global Container
$globalContainer = DOM::create("div", "", "objectGlobalContainer");
$form->append($globalContainer);

// Build Toolbar
$tlb = new navigationBar();
$navToolbar = $tlb->build(navigationBar::TOP, $globalContainer)->get();
DOM::append($globalContainer, $navToolbar);

// Save Button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertToolbarItem($saveTool);

// Delete query
$deleteTool = DOM::create("span", "", "", "objTool delete");
$tool = $tlb->insertToolbarItem($deleteTool);
$attr = array();
$attr['id'] = $templateID;
$attr['tname'] = $themeName;
$attr['js_name'] = $jsName;
$actionFactory->setModuleAction($deleteTool, $moduleID, "deleteJS", "", $attr);


// Load Page Code
$editor = new codeEditor();
$content = $themeJS->get($jsName);
$ajaxEditor = $editor->build("js", $content, "jsCode")->get();
DOM::append($globalContainer, $ajaxEditor);

// Get Content
$obj_id = str_replace(".", "_", $templateID."_".$themeName."_js_".$jsName);
$header = $themeName."/".$jsName.".js";
$devTabber = new devTabber();
return $devTabber->getReportContent($obj_id, $header, $pageContent->get());
//#section_end#
?>