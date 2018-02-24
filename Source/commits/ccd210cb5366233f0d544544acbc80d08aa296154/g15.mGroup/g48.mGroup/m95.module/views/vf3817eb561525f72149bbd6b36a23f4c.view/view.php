<?php
//#section#[header]
// Module Declaration
$moduleID = 95;

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
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
importer::import("INU", "Developer");
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLContent;
use \UI\Navigation\navigationBar;
use \UI\Presentation\notification;
use \INU\Developer\codeEditor;
use \INU\Developer\redWIDE;
use \DEV\Core\ajax\ajaxPage;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$directory = $_POST['d'];
	$pageName = $_POST['name'];
	$ajaxPage = new ajaxPage($pageName, $directory);
	
	$status = $ajaxPage->update($_POST['ajaxCode']);
	
	// Build Notification
	$reportNtf = new notification();
	if ($status === TRUE)
	{
		// TEMP
		$message = "success.save_success";
		$reportNtf->build($type = "success", $header = FALSE, $footer = FALSE);
		$reportMessage = $reportNtf->getMessage("success", $message);
	}
	else if ($status === FALSE)
	{
		// TEMP
		$message = "err.save_error";
		$reportNtf->build($type = "error", $header = TRUE, $footer = FALSE);
		$reportMessage = $reportNtf->getMessage("error", $message);
	}
	else
	{
		$message = "err.save_error";
		$reportNtf->build($type = "warning", $header = TRUE, $footer = FALSE);
		$reportMessage = DOM::create("span", "There are syntax errors in this document.");
	}
	
	
	$reportNtf->append($reportMessage);
	$notification = $reportNtf->get();
	
	return redWIDE::getNotificationResult($notification, ($status === TRUE));
}

$form = new simpleForm();
$directory = $_GET['d'];
$pageName = $_GET['name'];
$ajaxPage = new ajaxPage($pageName, $directory);


// Create Module Page
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("", "ajaxPageEditor");

// Initialize Editor Form
$pageForm = $form->build($moduleID, "ajaxPageEditor", $controls = FALSE)->get();
$pageContent->append($pageForm);



// Page Hidden Values
$hidden = $form->getInput($type = "hidden", $name = "d", $value = $_GET['d']);
$form->append($hidden);
$hidden = $form->getInput($type = "hidden", $name = "name", $value = $_GET['name']);
$form->append($hidden);


// Create Global Container
$tlb = new navigationBar();
$globalContainer = DOM::create("div", "", "objectGlobalContainer");
$form->append($globalContainer);

// Build Toolbar
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
$attr['dir'] = $directory;
$attr['name'] = $pageName;
$actionFactory->setModuleAction($deleteTool, $moduleID, "deletePage", "", $attr);


// Load Page Code
$editor = new codeEditor();
$content = $ajaxPage->getSourceCode();
$ajaxEditor = $editor->build("php", $content, "ajaxCode")->get();
DOM::append($globalContainer, $ajaxEditor);

// Get Content
$obj_id = str_replace("/", "_", $_GET['d'])."_".$_GET['name'];
$header = $_GET['name'].".php";
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($obj_id, $header, $pageContent->get());
//#section_end#
?>