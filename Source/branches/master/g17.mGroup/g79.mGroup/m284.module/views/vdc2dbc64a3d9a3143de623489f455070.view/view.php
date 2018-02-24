<?php
//#section#[header]
// Module Declaration
$moduleID = 284;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("DEV", "Version");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \DEV\Version\vcs;

// Get website ID
$websiteID = engine::getVar("id");
if (engine::isPost())
{
	// Set step number
	$step = 2;
	
	// Create Module Content
	$pageContent = new MContent($moduleID);
	
	// Build the module content
	$pageContent->build("", "projectPublisher");
	
	// Set step count
	$pageContent->addReportAction('website.setStep', $step);
	
	// Validate form post
	if (!simpleForm::validate())
	{
		// Add error action
		$pageContent->addReportAction("website.error", $step);
		
		// Add error content
		$errorContent = moduleLiteral::get($moduleID, "hd_formValidateError");
		$pageContent->append($errorContent);
		
		// Return output
		return $pageContent->getReport(".wsPublisher .errorHolder", "replace");
	}
	
	
	// Commit The Selected Items
	$vcs = new vcs($websiteID);
	
	// Commit Website items (if any)
	$postItems = json_decode($_POST['citem_ser']);
	$commitItems = array();
	foreach ($postItems as $id => $content)
		$commitItems[] = $id;

	$commitSummary = "[Website Auto Commit Summary] ".engine::getVar("title");
	$commitDescription = "[Website Auto Commit Changelog]\n".engine::getVar("changelog");
	$vcs->commit($commitSummary, $commitDescription, $commitItems);
	
	// Release the repository
	$releaseTitle = "[Website Auto Release Title] ".engine::getVar("title");
	$releaseDescription = "[Website Auto Release Changelog]\n".engine::getVar("changelog");
	$status = $vcs->release(engine::getVar("branch"), engine::getVar("version"), $releaseTitle, $releaseDescription);
	if ($status == FALSE)
	{
		// Add error action
		$pageContent->addReportAction("website.error", $step);
		
		// Add error content
		$errorContent = moduleLiteral::get($moduleID, "lbl_repositoryReleaseError");
		$pageContent->append($errorContent);
		
		// Return output
		return $pageContent->getReport(".wsPublisher .errorHolder", "replace");
	}
	
	
	// Get the user input that configures the publish process
	// Create form to start the project release process
	
	// Build Form
	$form = new simpleForm();
	$releaseSourceForm = $form->build("", FALSE)->engageModule($moduleID, "releaseProject")->get();
	$pageContent->append($releaseSourceForm);
	
	// Set website id
	$input = $form->getInput("hidden", "id", $websiteID, "", FALSE, FALSE);
	$form->append($input);
	
	// Set selected server id
	$input = $form->getInput("hidden", "srvid", engine::getVar("srvid"), "", FALSE, FALSE);
	$form->append($input);
	
	// Set selected branch
	$input = $form->getInput("hidden", "branch", engine::getVar("branch"), "", FALSE, FALSE); 
	$form->append($input);
	
	// Set selected release title
	$input = $form->getInput("hidden", "title", engine::getVar("title"), "", FALSE, FALSE);
	$form->append($input);
	
	// Set selected release title
	$input = $form->getInput("hidden", "changelog", engine::getVar("changelog"), "", FALSE, FALSE); 
	$form->append($input);
	
	// Set Release Version
	$input = $form->getInput("hidden", "version", engine::getVar("version"), "", FALSE, FALSE);
	$form->append($input);
	
	// Add action to add title
	$title = moduleLiteral::get($moduleID, "lbl_status_releaseProject", array(), FALSE);
	$pageContent->addReportAction('website.addStatusTitle', $title);
	
	// Set step ok and proceed to next form
	$pageContent->addReportAction("website.stepOK", $step);
	
	
	// Return output
	return $pageContent->getReport(".wsPublisher .formsHolder", "replace");
	
}
//#section_end#
?>