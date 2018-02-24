<?php
//#section#[header]
// Module Declaration
$moduleID = 185;

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
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Resources\url;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\HTML\HTMLContent;
use \UI\Presentation\frames\windowFrame;

use \DEV\Projects\project;
use \DEV\Projects\projectCategory;

$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Project Title
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_projectTitle");
		$err = $errFormNtf->addErrorHeader("projectTitle_h", $err_header);
		$errFormNtf->addErrorDescription($err, "projectTitle_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check project name (if not empty)
	if (!empty($_POST['name']))
	{
		// Get projects by name
		$pr = new project("", $_POST['name']);
		$prInfo = $pr->info();
		if (!is_null($prInfo))
		{
			$has_error = TRUE;
		
			// Header
			$err_header = moduleLiteral::get($moduleID, "lbl_projectName");
			$err = $errFormNtf->addErrorHeader("projectName_h", $err_header);
			$errFormNtf->addErrorDescription($err, "projectName_desc", $errFormNtf->getErrorMessage("err.exists"));
		}
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create project
	$pr = new project();
	$projectID = $pr->create($_POST['title'], $_POST['type'], $_POST['desc']);
	
	if (!$projectID)
	{
		// Error occurred
		$err_header = moduleLiteral::get($moduleID, "lbl_newProject");
		$err = $errFormNtf->addErrorHeader("project_h", $err_header);
		$errFormNtf->addErrorDescription($err, "project_desc", DOM::create("span", "Error creating project..."));
		return $errFormNtf->getReport();
	}
	
	// Add account to project
	$pr->addAccountToProject(account::getAccountID());
	
	// Redirect to project main page
	return $actionFactory->getReportRedirect("/projects/project.php?id=".$projectID, "developer", TRUE);
}

// Build content
$pageContent->build("", "projectWizard", TRUE);


// Header
$title = moduleLiteral::get($moduleID, "lbl_projectWizard_newDevProject");
$header = HTML::select("h2.title")->item(0);
DOM::append($header, $title);


// Create main form
$form = new simpleForm();
$wizardForm = $form->build($moduleID, "projectWizard", FALSE)->get();


$projectDetails = HTML::select(".projectDetails")->item(0);
$projectInfoControls = HTML::select(".projectInfo .infoControls")->item(0);

// Project information page
$apps[] = $form->getOption("Application", 4, TRUE);
$appGroup = $form->getOptionGroup("App Engine", $apps);

$web[] = $form->getOption("Website", 5);
$web[] = $form->getOption("Web Template", 6);
$web[] = $form->getOption("Web Extension", 7);
$webGroup = $form->getOptionGroup("Web Engine", $web);

$input = $form->getSelect($name = "type", $multiple = FALSE, $class = "", $options = array());
DOM::append($input, $appGroup);
DOM::append($input, $webGroup);

$title = moduleLiteral::get($moduleID, "lbl_projectType");
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($projectDetails, $inputRow);



$title = moduleLiteral::get($moduleID, "lbl_projectTitle");
$input = $form->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = TRUE, $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($projectDetails, $inputRow);

$notes = moduleLiteral::get($moduleID, "lbl_projectName_notes");
$title = moduleLiteral::get($moduleID, "lbl_projectName");
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes);
DOM::append($projectDetails, $inputRow);

$title = moduleLiteral::get($moduleID, "lbl_projectDesc");
$input = $form->getTextarea($name = "desc", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($projectDetails, $inputRow);

// Controls
$title = moduleLiteral::get($moduleID, "lbl_createProject");
$submitBtn = $form->getSubmitButton($title, $id = "submitBtn");
DOM::append($projectInfoControls, $submitBtn);


// Append slides to form
$newProjectWizard = HTML::select(".newProjectWizard")->item(0);
$form->append($newProjectWizard);


// Create frame
$frame = new windowFrame();
$title = moduleLiteral::get($moduleID, "lbl_projectWizard_newProjectTitle");
$frame->build($title, "projectWizardFrame");

// Append the page content
$frame->append($wizardForm);

// Return the report
return $frame->getFrame();
//#section_end#
?>