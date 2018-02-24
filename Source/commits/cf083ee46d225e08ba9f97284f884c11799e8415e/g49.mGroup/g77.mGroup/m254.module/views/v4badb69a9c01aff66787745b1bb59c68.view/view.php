<?php
//#section#[header]
// Module Declaration
$moduleID = 254;

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
importer::import("DEV", "Projects");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \DEV\Projects\project;

// Initialize project
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');
$project = new project($projectID, $projectName);

if (engine::isPost())
{
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	$has_error = FALSE;
	
	// Check Title
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("title");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}

	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update project info
	$openProject = ($_POST['open'] || $_POST['open'] == "on" ? 1 : 0);
	$publicProject = ($_POST['public'] || $_POST['public'] == "on" ? 1 : 0);
	$project->updateInfo($_POST['title'], $_POST['description'], $openProject, $publicProject);
	
	// Set project name
	$name = $_POST['name'];
	$name = str_replace(" ", "_", $name);
	$project->setName($name);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
		
}

// Create Module Page
$pageContent = new MContent($moduleID);

// Get project info
$projectInfo = $project->info();

// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Build module page
$pageContent->build("", "projectInfoContainer", TRUE);
$formContainer = HTML::select(".projectInfo .formContainer")->item(0);

// Create Form
$form = new simpleForm();
$settingsForm = $form->build("", TRUE)->engageModule($moduleID, "projectInfo")->get();
DOM::append($formContainer , $settingsForm);

// Project ID
$input= $form->getInput("hidden", "id", $projectID, "", TRUE, TRUE);
$form->append($input);

// Project ID visual
$title = moduleLiteral::get($moduleID, "lbl_projectID");
$label = $form->getLabel($projectID, "", "inputLabel");
$form->insertRow($title, $label, FALSE);

// Title Field
$title = literal::dictionary("title");
$input = $form->getInput("text", "title", $projectTitle, "", TRUE, TRUE);
$form->insertRow($title, $input, TRUE);

// Name Field
$title = literal::dictionary("name");
$notes = moduleLiteral::get($moduleID, "lbl_projectName_notes", array(), FALSE);
$input = $form->getInput("text", "name", $projectName, "",FALSE);
$form->insertRow($title, $input, FALSE, $notes);

// Project Info Field
$title = literal::dictionary("description");
$tArea = $form->getTextarea("description", $projectInfo['description'], "",FALSE);
$form->insertRow($title, $tArea);

// Public project
$title = moduleLiteral::get($moduleID,"lbl_publicProject");
$notes = moduleLiteral::get($moduleID, "lbl_publicProject_notes", array(), FALSE);
$input = $form->getInput("checkbox", "public", $projectInfo['public'] == 1, "", FALSE);
$form->insertRow($title, $input, FALSE, $notes);

// Open project
$title = moduleLiteral::get($moduleID,"lbl_openProject");
$notes = moduleLiteral::get($moduleID, "lbl_openProject_notes", array(), FALSE);
$input = $form->getInput("checkbox", "open", $projectInfo['open'] == 1, "", FALSE);
$form->insertRow($title, $input, FALSE, $notes);

// Return report
return $pageContent->getReport();
//#section_end#
?>