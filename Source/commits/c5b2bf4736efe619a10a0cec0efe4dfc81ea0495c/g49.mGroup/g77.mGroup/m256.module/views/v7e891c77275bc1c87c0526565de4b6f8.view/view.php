<?php
//#section#[header]
// Module Declaration
$moduleID = 256;

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
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);


// Get project id and name
$projectID = $_GET['id'];
$projectName = $_GET['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];
$projectType = $projectInfo['type'];

// Build module page
$page->build("Market Settings | ".$projectTitle, "projectMarketSettings", TRUE);


//Settings Form
$settingsFormContainer = HTML::select(".settingsContainer.settings .formContainer")->item(0);

//Create Form
$form = new simpleForm();
$settingsForm = $form->build()->engageModule($moduleID, "updateSettings")->get();
DOM::append($settingsFormContainer, $settingsForm);


// Project ID
$input= $form->getInput("hidden", "id", $projectID);
$form->append($input);

// If project is of application type, show choices for appstore and boss market

$title = moduleLiteral::get($moduleID, "lbl_appCenter");
$label = $form->getLabel($title);
$input= $form->getInput("checkbox", "app_center", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($label, $input, $required = TRUE, $notes = "");

// Set price (if any)

/*
//Title Field
$title = literal::dictionary("title");
$label = $form->getLabel($title);
$input= $form->getInput("text", "title", $projectTitle, "",TRUE,TRUE);
$form->insertRow($label, $input, TRUE);

//Name Field
$title = literal::dictionary("name");
$notes = moduleLiteral::get($moduleID, "lbl_projectName_notes", array(), FALSE);
$label = $form->getLabel($title);
$input= $form->getInput("text", "name", $projectName, "",FALSE);
$form->insertRow($label, $input, FALSE, $notes);

//Project Info Field
$title = literal::dictionary("description");
$label = $form->getLabel($title);
$tArea = $form->getTextarea("description", $projectInfo['description'], "",FALSE);
$form->insertRow($label, $tArea);
*/

// Privileges Form
$privilegesFormContainer = HTML::select(".settingsContainer.privileges .formContainer")->item(0);

//Create Form
$form = new simpleForm();
$privilegesForm = $form->build()->engageModule($moduleID, "updatePrivileges")->get();
DOM::append($privilegesFormContainer , $privilegesForm);

// Project ID
$input= $form->getInput("hidden", "id", $projectID);
$form->append($input);

/*
//Password Field
$title = moduleLiteral::get($moduleID,"lbl_authenticate");
$label = $form->getLabel($title);
$input= $form->getInput("password", "pwd", "","",TRUE,TRUE);
$form->insertRow($label, $input,TRUE);

//Delete Form
$deleteFormContainer = HTML::select(".settingsContainer.delete .formContainer")->item(0);

//Create Form
$form = new simpleForm();
$deleteForm = $form->build($moduleID,"deleteProject", TRUE,FALSE)->get();
DOM::append($deleteFormContainer , $deleteForm);

// Project ID
$input= $form->getInput("hidden", "pid", $projectID, "",TRUE,TRUE);
$form->append($input);

//Password Field
$title = moduleLiteral::get($moduleID,"lbl_authenticate");
$label = $form->getLabel($title);
$input= $form->getInput("password", "pwd", "","",TRUE,TRUE);
$form->insertRow($label, $input,TRUE);
*/


// Return output
return $page->getReport();
//#section_end#
?>