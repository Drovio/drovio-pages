<?php
//#section#[header]
// Module Declaration
$moduleID = 95;

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
	$directory = $_POST['dir'];
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
		$reportPopup->timeout(FALSE);
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
$directory = $_GET['dir'];
$pageName = $_GET['name'];
$ajaxPage = new ajaxPage($pageName, $directory);


// Create Module Page
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();
// Initialize Editor Form
$form->build($moduleID, "ajaxPageEditor", $controls = FALSE);
// Append form to Content
$pageForm = $form->get();
$HTMLContent->buildElement($pageForm);



// Page Hidden Values
$hidden = $form->getInput($type = "hidden", $name = "dir", $value = $_GET['dir']);
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
$savePage = DOM::create("button", "", "", "sideTool save");
DOM::attr($savePage, "type", "submit");
$tlb->insertToolbarItem($savePage);


// Load Page Code
$editor = new codeEditor();
$content = $ajaxPage->getSourceCode();
$ajaxEditor = $editor->build("php", $content, "ajaxCode")->get();
DOM::append($globalContainer, $ajaxEditor);

// Get Content
$obj_id = str_replace("/", "_", $_GET['dir'])."_".$_GET['name'];
$header = $_GET['name'].".php";
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($obj_id, $header, $HTMLContent->get());
//#section_end#
?>