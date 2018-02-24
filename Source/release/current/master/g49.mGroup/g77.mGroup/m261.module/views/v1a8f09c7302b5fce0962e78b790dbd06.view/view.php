<?php
//#section#[header]
// Module Declaration
$moduleID = 261;

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
importer::import("DEV", "Version");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Get project ID
$projectID = engine::getVar('id');
$project = new project($projectID);
if (engine::isPost())
{
	// Set step number
	$step = 2;
	
	// Create Module Content
	$pageContent = new MContent($moduleID);
	
	// Build the module content
	$pageContent->build("", "projectReleaser");
	
	// Set step count
	$pageContent->addReportAction('prj.publisher.setStep', $step);
	
	// Get project online
	if (engine::getVar('new_repo_release'))
	{
		// Initialize vcs and create repository release
		$vcs = new vcs($projectID);
		$status = $vcs->release($_POST['new_repo_branch'], $_POST['new_repo_version'], $_POST['title'], $_POST['changelog']);
		
		if ($status == FALSE)
		{
			// Add error action
			$pageContent->addReportAction("prj.publisher.error", $step);
			
			// Add error content
			$errorContent = moduleLiteral::get($moduleID, "lbl_repositoryReleaseError");
			$pageContent->append($errorContent);
			
			// Return output
			return $pageContent->getReport(".projectPublisherContainer .errorHolder", "replace");
		}
	}
	
	// Get the user input that configures the publish process
	// Create form to start the project publish process
	
	// Build Form
	$form = new simpleForm();
	$repositoryReleaseForm = $form->build("", FALSE)->engageModule($moduleID, "projectRelease")->get();
	$pageContent->append($repositoryReleaseForm);
	
	// Set website id
	$input = $form->getInput("hidden", "id", $projectID, "", FALSE, FALSE);
	$form->append($input);
	
	// Set selected release title
	$input = $form->getInput("hidden", "title", engine::getVar("title"), "", FALSE, FALSE);
	$form->append($input);
	
	// Set Release Version
	$input = $form->getInput("hidden", "version", engine::getVar("version"), "", FALSE, FALSE);
	$form->append($input);
	
	// Set Release changelog
	$input = $form->getInput("hidden", "changelog", engine::getVar("changelog"), "", FALSE, FALSE);
	$form->append($input);
	
	// Set package to release
	if (engine::getVar('new_repo_release'))
	{
		// Set to create new repository release
		$input = $form->getInput("hidden", "new_repo_release", 1, "", FALSE, FALSE);
		$form->append($input);
		
		// Set repository release branch
		$input = $form->getInput("hidden", "new_repo_branch", engine::getVar("new_repo_branch"), "", FALSE, FALSE);
		$form->append($input);
	}
	else
	{
		// Set repository release package
		$input = $form->getInput("hidden", "repo_package", engine::getVar("repo_package"), "", FALSE, FALSE);
		$form->append($input);
	}
	
	// Add action for status title
	$statusTitle = moduleLiteral::get($moduleID, "lbl_status_projectPublish", array(), FALSE);
	$pageContent->addReportAction('prj.publisher.addStatusTitle', $statusTitle);
	
	// Set step ok and proceed to next form
	$pageContent->addReportAction("prj.publisher.stepOK", $step);
	
	// Return output
	return $pageContent->getReport(".projectPublisherContainer .formsHolder", "replace");
}
//#section_end#
?>