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
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("DEV", "Websites");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \DEV\Websites\website;
use \DEV\Websites\wsServer;

// Get website ID
$websiteID = engine::getVar("id");
$serverID = engine::getVar("srvid");
if (engine::isPost())
{
	// Set step number
	$step = 3;
	
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
	
	// Create project release
	$website = new website($websiteID);
	$releaseTitle = "[Website Auto Release Title] ".engine::getVar("title");
	$releaseChangelog = "[Website Auto Release Changelog]\n".engine::getVar("changelog");
	$status = $website->release(engine::getVar("version"), $releaseTitle, $releaseChangelog);
	if ($status)
		$status = $website->publish(engine::getVar("version"), engine::getVar("branch"));
	
	if ($status == FALSE)
	{
		// Add error action
		$pageContent->addReportAction("website.error", $step);
		
		// Add error content
		$errorContent = moduleLiteral::get($moduleID, "lbl_projectReleaseError");
		$pageContent->append($errorContent);
		
		// Return output
		return $pageContent->getReport(".wsPublisher .errorHolder", "replace");
	}

	
	// Get the user input that configures the publish process
	// Create form to start the project release process
	
	// Build Form
	$form = new simpleForm();
	$releaseSourceForm = $form->build("", FALSE)->engageModule($moduleID, "uploadWebsite")->get();
	$pageContent->append($releaseSourceForm);
	
	// Set website id
	$input = $form->getInput("hidden", "id", $websiteID, "", FALSE, FALSE);
	$form->append($input);
	
	// Set selected server id
	$input = $form->getInput("hidden", "srvid", $serverID, "", FALSE, FALSE);
	$form->append($input);
	
	// Set Release Version
	$input = $form->getInput("hidden", "version", engine::getVar("version"), "", FALSE, FALSE);
	$form->append($input);
	
	// Add action to add title
	$title = moduleLiteral::get($moduleID, "lbl_status_uploadWebsite", array(), FALSE);
	$pageContent->addReportAction('website.addStatusTitle', $title);
	
	// Set step ok and proceed to next form
	$pageContent->addReportAction("website.stepOK", $step);
	
	
	// Return output
	return $pageContent->getReport(".wsPublisher .formsHolder", "replace");
}
//#section_end#
?>