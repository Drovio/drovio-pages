<?php
//#section#[header]
// Module Declaration
$moduleID = 393;

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
importer::import("DEV", "Websites");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Developer\editors\WViewEditor;
use \UI\Developer\devTabber;
use \UI\Presentation\notification;
use \DEV\Websites\templates\wsTemplatePage;

// Initialize Template
$websiteID = engine::getVar('id');
$templateName = engine::getVar('tname');
$pageName = engine::getVar('name');
$templatePage = new wsTemplatePage($websiteID, $templateName, $pageName);


if (engine::isPost())
{
	// Get code
	$htmlCode = $_POST['pageHTML'];
	$cssCode = $_POST['pageCSS'];
	
	// Update HTML + CSS
	$HTMLStatus = $templatePage->updateHTML($htmlCode);
	$CSSStatus = $templatePage->updateCSS($cssCode);
	$status = ($HTMLStatus && $CSSStatus);
	
	// Build Notification
	$reportNtf = new notification();
	if ($status === TRUE)
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
	$notification = $reportNtf->get();
	
	return devTabber::getNotificationResult($notification, ($status === TRUE));
}

// Create object id
$objID = str_replace(".", "_", $websiteID."_template_".$templateName."_page_".$pageName);


// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
// Create Global Container
$globalContainer = $pageContent->build("", "tepmlatePageEditor")->get();

// Create form object
$form = new simpleForm();

// Source Code Form 
$sourceForm = $form->build("", FALSE)->engageModule($moduleID, "editPage")->get();
$pageContent->append($sourceForm);

$outerContainer = DOM::create("div", "", "", "outerEditorContainer");
$form->append($outerContainer);

// Hidden Values
// Website id
$input = $form->getInput($type = "hidden", $name = "id", $value = $websiteID, $class = "", $autofocus = FALSE);
$form->append($input);
// Template name
$input = $form->getInput($type = "hidden", $name = "tname", $value = $templateName, $class = "", $autofocus = FALSE);
$form->append($input);
// Page name
$input = $form->getInput($type = "hidden", $name = "name", $value = $pageName, $class = "", $autofocus = FALSE);
$form->append($input);

// Toolbar
$objMgrToolbar = new navigationBar();
$toolbar = $objMgrToolbar->build($dock = navigationBar::TOP, $outerContainer)->get();
DOM::append($outerContainer, $toolbar);

// Save button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$objMgrToolbar->insertToolbarItem($saveTool);

// Delete button
$delTool = DOM::create("div", "", "", "objTool delete");
$objMgrToolbar->insertToolbarItem($delTool);
$attr = array();
$attr['id'] = $websiteID;
$attr['tname'] = $templateName;
$attr['name'] = $pageName;
$actionFactory->setModuleAction($delTool, $moduleID, "deletePage", "", $attr);

// Create Code Container
$objModelContainer = DOM::create("div", "", "", "pageDesignerContainer");
DOM::append($outerContainer, $objModelContainer);

// CSS Editor
$html = $templatePage->getHTML();
$css = trim($templatePage->getCSS());
$editor = new WViewEditor("pageCSS", "pageHTML");
$viewDesigner = $editor->build($html, $css)->get();
DOM::append($objModelContainer, $viewDesigner);

// Send devTabber Tab
$devTabber = new devTabber();
return $devTabber->getReportContent($objID, $pageName.".page", $pageContent->get());
//#section_end#
?>