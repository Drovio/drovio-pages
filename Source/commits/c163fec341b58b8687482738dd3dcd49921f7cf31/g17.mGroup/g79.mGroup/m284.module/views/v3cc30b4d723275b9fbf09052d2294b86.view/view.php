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
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("DEV", "Websites");
//#section_end#
//#section#[code]
use \API\Model\modules\module;
use \DEV\Websites\website;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;

// Get website id
$websiteID = $_REQUEST['id'];
$srvID = $_REQUEST['srvID'];
$releaseVer = $_REQUEST['releaseVer'];
$branchName = $_REQUEST['branch'];
$releaseTitle = $_REQUEST['releaseTitle'];
if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	// Releses / publishes the project
	// Init (and return) the 'websiteUploader' modules
	// To initiate in turn the website packing / uploading / and installing
	
	// Create Module Page
	$pageContent = new MContent($moduleID);
	$actionFactory = $pageContent->getActionFactory();
	
	// Build the module content
	$pageContent->build("", "uc");
	
	$website = new website($websiteID);
	// Create project release
	$changelog = '';
	$status = $website->release($releaseVer, $releaseTitle, $changelog);
	if ($status)
	{
		$website->publish($releaseVer, $branchName);
	}
	
	
	
	if ($status)
	{
		
	}
	
	// Reset the $_SERVER['REQUEST_METHOD'], in order 
	// for the called module to load correctly
	$_SERVER['REQUEST_METHOD'] = "GET";
	$hw = module::loadView($moduleID, 'websiteUploader');
	$pageContent->append($hw);
 	
	// Add Action	
	$pageContent->addReportAction('website.publish.upload');
	
	// Return output
	return $pageContent->getReport('.sequenceHolder');	
}

// Assuming Module will be loaded using load:view

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "projectReleaserContent", TRUE);

// Add a hello world dynamic content
//$hw = DOM::create("p", "Relesing / publishing  the project");
//$pageContent->append($hw);

//$hw = DOM::create("p", "Project Id : ".$websiteID);
//$pageContent->append($hw);

// Build Form
$form = new simpleForm();
$form->build('', FALSE)->engageModule($moduleID, "projectReleaser");
$formHolder = HTML::select('.section.formHolder')->item(0);
DOM::append($formHolder, $form->get());
//$pageContent->append($form->get());

// Set website id
$input = $form->getInput("hidden", "id", $websiteID, "", TRUE, TRUE);
$form->append($input);

// Set selected server id
$input = $form->getInput("hidden", "srvID", $srvID, "", TRUE, TRUE);
$form->append($input);

// Set selected release title
$input = $form->getInput("hidden", "releaseTitle", $releaseTitle, "", TRUE, TRUE); 
$form->append($input);

// Set release version
$input = $form->getInput("hidden", "releaseVer", $releaseVer, "", TRUE, TRUE);
$form->append($input);

// Set selected branch name
$input = $form->getInput("hidden", "branch", $branchName, "", TRUE, TRUE);
$form->append($input);

// Return output
return $pageContent->getReport();
//#section_end#
?>