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
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("DEV", "Literals");
//#section_end#
//#section#[code]
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
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

// Build module page
$page->build("Settings | ".$projectTitle, "projectSettingsPage", TRUE);


//Settings Form
$settingsFormContainer = HTML::select(".settingsContainer.settings .formContainer")->item(0);

//Create Form
$form = new simpleForm();
$settingsForm = $form->build("", TRUE)->engageModule($moduleID, "updateSettings")->get();
DOM::append($settingsFormContainer , $settingsForm);

// Project ID
$input= $form->getInput("hidden", "pid", $projectID, "",TRUE,TRUE);
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


// Backup Form
$backupFormContainer = HTML::select(".settingsContainer.backup .formContainer")->item(0);

// Create Form
$form = new simpleForm();
$backupForm = $form->build("", TRUE)->engageModule($moduleID, "backupProject")->get();
DOM::append($backupFormContainer , $backupForm);

// Project ID
$input= $form->getInput("hidden", "pid", $projectID, "", TRUE, TRUE);
$form->append($input);

// Password Field
$title = moduleLiteral::get($moduleID,"lbl_authenticate");
$input= $form->getInput("password", "pwd", "", "", TRUE, TRUE);
$form->insertRow($title, $input,TRUE);

// Delete Form
$deleteFormContainer = HTML::select(".settingsContainer.delete .formContainer")->item(0);

// Create Form
$form = new simpleForm();
$deleteForm = $form->build("", TRUE)->engageModule($moduleID, "deleteProject")->get();
DOM::append($deleteFormContainer , $deleteForm);

// Project ID
$input= $form->getInput("hidden", "pid", $projectID, "", TRUE, TRUE);
$form->append($input);

// Password Field
$title = moduleLiteral::get($moduleID, "lbl_authenticate");
$input= $form->getInput("password", "pwd", "", "", TRUE, TRUE);
$form->insertRow($title, $input,TRUE);



// Return output
return $page->getReport();
//#section_end#
?>