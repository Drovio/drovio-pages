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
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Resources\literals\literal;
use \API\Resources\literals\moduleLiteral;
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

// Append form to project wizard container
$projectWizardContainer = HTML::select(".newProjectWizard")->item(0);
HTML::append($projectWizardContainer, $wizardForm);

// Append slides to form
$slides = HTML::select(".newProjectWizard .slides")->item(0);
$form->append($slides);


// Radio button hidden container
$radioContainer = DOM::create("div", "", "", "radioContainer");
$form->append($radioContainer);

// Radio buttons selector for project type
$input = $form->getInput($type = "radio", $name = "type", $value = "4", $class = "", $autofocus = FALSE, $required = FALSE);
$inputID = "ptype1";
DOM::attr($input, "id", $inputID);
DOM::append($radioContainer, $input);
$ptSelector = HTML::select(".pt.app")->item(0);
DOM::attr($ptSelector, "for", $inputID);

$input = $form->getInput($type = "radio", $name = "type", $value = "6", $class = "", $autofocus = FALSE, $required = FALSE);
$inputID = "ptype2";
DOM::attr($input, "id", $inputID);
DOM::append($radioContainer, $input);
$ptSelector = HTML::select(".pt.wt")->item(0);
DOM::attr($ptSelector, "for", $inputID);

$input = $form->getInput($type = "radio", $name = "type", $value = "7", $class = "", $autofocus = FALSE, $required = FALSE);
$inputID = "ptype3";
DOM::attr($input, "id", $inputID);
DOM::append($radioContainer, $input);
$ptSelector = HTML::select(".pt.we")->item(0);
DOM::attr($ptSelector, "for", $inputID);



// Project type description helpers
$desc = moduleLiteral::get($moduleID, "lbl_pDesc_initHeader");
$descHeader = HTML::select("h4.descHeader")->item(0);
DOM::append($descHeader, $desc);

$desc = moduleLiteral::get($moduleID, "lbl_pDesc_initP");
$descP = HTML::select(".selectorControls p")->item(0);
DOM::append($descP, $desc);


$pControls = HTML::select(".selectorControls")->item(0);

// Next button
$title = literal::dictionary("next");
$nextBtn = $form->getButton($title, $name = "nextBtn", $class = "nextBtn disabled positive");
DOM::attr($nextBtn, "disabled", "disabled");
DOM::append($pControls, $nextBtn);


$projectDetails = HTML::select(".projectDetails.project")->item(0);
$projectInfoControls = HTML::select(".projectInfo .infoControls")->item(0);

// Project information page
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
$title = literal::dictionary("back");
$backBtn = $form->getButton($title, $name = "backBtn", $class = "backBtn");
DOM::append($projectInfoControls, $backBtn);

$title = literal::dictionary("submit");
$submitBtn = $form->getSubmitButton($title, $id = "submitBtn");
DOM::append($projectInfoControls, $submitBtn);


// Website redirection page
$wBuilderLink = HTML::select("a.wBuilderLink")->item(0);
$url = url::resolve("web", "/wizard.php");
HTML::attr($wBuilderLink, "href", $url);

$title = moduleLiteral::get($moduleID, "lbl_wBuilderTextRedir");
$wBuilderText = HTML::select("h3.wBuilderText")->item(0);
HTML::append($wBuilderText, $title);




// Create frame
$frame = new windowFrame();
$title = moduleLiteral::get($moduleID, "lbl_projectWizard_newProjectTitle");
$frame->build($title, "projectWizardFrame");

// Append the page content
$frame->append($pageContent->get());

// Return the report
return $frame->getFrame();
//#section_end#
?>