<?php
//#section#[header]
// Module Declaration
$moduleID = 186;

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Presentation\popups\popup;
use \DEV\Projects\project;

// Init pageContent
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	
	// Get project information
	$project = new project($_POST['pid']);
	$projectInfo = $project->info();
	
	
	// Check Project Title
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("title");
		$err = $errFormNtf->addErrorHeader("projectTitle_h", $err_header);
		$errFormNtf->addErrorDescription($err, "projectTitle_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check project name (if not empty)
	$emptyName = TRUE;
	if (!empty($_POST['name']) && $_POST['name'] != $projectInfo['name'])
	{
		// Get projects by name
		$pr = new project("", $_POST['name']);
		$prInfo = $pr->info();
		if (!is_null($prInfo))
		{
			$has_error = TRUE;
		
			// Header
			$err_header = literal::dictionary("name");
			$err = $errFormNtf->addErrorHeader("projectName_h", $err_header);
			$errFormNtf->addErrorDescription($err, "projectName_desc", $errFormNtf->getErrorMessage("err.exists"));
		}
		
		$emptyName = FALSE;
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update Project Information
	$project->updateInfo($_POST['title'], $_POST['desc']);
	if (!$emptyName)
		$project->setName($_POST['name']);
	
	// Redirect to project main page
	return $actionFactory->getReportReload(TRUE);
}


// Build pageContent
$pageContent->build("", "projectInfoEditorContent");

// Get project id and name
$projectID = $_REQUEST['id'];

// Get project info
$project = new project($projectID);
$projectInfo = $project->info();

// If project is invalid, return error page
if (is_null($projectInfo))
{
	// Add notification
	
	// Return report
	return $frame->append($pageContent->get())->getFrame();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $frame->append($pageContent->get())->getFrame();
}


// Set header
$title = moduleLiteral::get($moduleID, "lbl_projectInfoEditorTitle");
$header = DOM::create("h3", $title);
$pageContent->append($header);


// Information Editor
$form = new simpleForm();
$editorForm = $form->build($moduleID, "projectInfoEditor")->get();
$pageContent->append($editorForm);

$input = $form->getInput($type = "hidden", $name = "pid", $value = $projectID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Project Title
$title = literal::dictionary("title");
$input = $form->getInput($type = "text", $name = "title", $value = $projectTitle, $class = "", $autofocus = TRUE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Project Title
$notes = moduleLiteral::get($moduleID, "lbl_projectName_notes");
$title = literal::dictionary("name");
$input = $form->getInput($type = "text", $name = "name", $value = $projectName, $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($title, $input, $required = FALSE, $notes);

// Project Description
$title = literal::dictionary("description");
$input = $form->getTextarea($name = "desc", $value = $projectInfo['description'], $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = FALSE, $notes = "");



// Create the popup and set settings
$popup = new popup();
$popup->position($position = "bottom", $alignment = "right");

// Build and add content
$popup->build($pageContent->get());

// Return the report
return $popup->getReport();
//#section_end#
?>