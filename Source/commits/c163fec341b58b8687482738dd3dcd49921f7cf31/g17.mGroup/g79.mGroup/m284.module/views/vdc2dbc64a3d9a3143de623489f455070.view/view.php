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
use \DEV\Version\vcs;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;


// Get website id
$websiteID = $_REQUEST['id'];
$srvID = $_REQUEST['srvID'];
$branchName = $_REQUEST['branch'];
$releaseVer = $_REQUEST['releaseVer'];
$releaseTitle = $_REQUEST['releaseTitle'];
if ($_SERVER['REQUEST_METHOD'] == "POST" && !$innerCall)
{	
	// Commits And Releases the project repository
	// Init (and return) the 'projectReleaser' modules
	// To initiate in turn the project release/publish project
	
	// Create Module Page
	$pageContent = new MContent($moduleID);
	$actionFactory = $pageContent->getActionFactory();
	
	// Build the module content
	$pageContent->build("", "uc");
	
	// Commit The Selected Items
	// Check commit summary
	$commitSummary = trim($_POST['releaseTitle']);
	if (empty($commitSummary))
	{
		// TODO
		// For just put A default value
		$commitSummary = trim("[Default] - Website Commit");
	}
	
	// Initialize vcsControl
	$vcs = new vcs($websiteID);
	
	// Commit Data
	$postItems = json_decode($_POST['citem_ser']);
	if (is_array($postItems))
	{
		// Commit if only there are new objects
	
		foreach ($postItems as $id => $content)
			$commitItems[] = $id;
	
		$commitDescription = trim($_POST['releaseSummary']);
		$status = $vcs->commit($commitSummary, $commitDescription, $commitItems);
		
		if ($status)
		{
			
		}
		else
		{
			
		}
	}	
	
	// Release the project anyaway 
	// To make sure we have latest repository version
	
	// Release the repository
	
	// TODO
	// This is temporary version should be calculated before
	$releases = $vcs->getReleases();
	$releaseVersion = $releases['master']['current'];
		
	$releaseDescription = trim($_POST['releaseSummary']);
	$releaseVersion = str_replace(",", ".", $releaseVersion);
	$status = $vcs->release($branchName, $releaseVersion, $commitSummary, $releaseDescription);
	
	if (!$status)
	{
		
	}


	// Reset the $_SERVER['REQUEST_METHOD'], in order 
	// for the called module to load correctly
	$_SERVER['REQUEST_METHOD'] = "GET";
	$hw = module::loadView($moduleID, 'projectReleaser');
	$pageContent->append($hw);
 	
	// Add Action	
	$pageContent->addReportAction('website.publish.release');
	
	// Return output
	return $pageContent->getReport('.sequenceHolder');	
	
}

// Assuming Module will be loaded using load:view

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "repoManagerContent", TRUE);

// Add a hello world dynamic content
//$hw = DOM::create("p", "Commiting and Releasing Repository");
//$pageContent->append($hw);

//$hw = DOM::create("p", "Project Id : ".$websiteID);
//$pageContent->append($hw);

// Build Form
$form = new simpleForm();
$form->build('', FALSE)->engageModule($moduleID, "repoManager");
$formHolder = HTML::select('.section.formHolder')->item(0);
DOM::append($formHolder, $form->get());
//$pageContent->append($form->get());

// Set website id
$input = $form->getInput("hidden", "id", $websiteID, "", TRUE, TRUE);
$form->append($input);

// Set selected server id
$input = $form->getInput("hidden", "srvID", $srvID, "", TRUE, TRUE);
$form->append($input);

// Set selected branch
$input = $form->getInput("hidden", "branch", $branchName , "", TRUE, TRUE); 
$form->append($input);

// Set selected release title
$input = $form->getInput("hidden", "releaseTitle", $releaseTitle, "", TRUE, TRUE); 
$form->append($input);

// Set Release Version
$input = $form->getInput("hidden", "releaseVer", $releaseVer, "", TRUE, TRUE);
$form->append($input);

// The Items selected for commit
$commitItems = array();
	$postItems = $_POST['citem'];
	if (is_array($postItems))
		foreach ($postItems as $id => $content)
			$commitItems[] = $id;
$serializable = json_encode($commitItems);
$input = $form->getInput("hidden", "citem_ser", $serializable, "", TRUE, TRUE);
$form->append($input);


// Return output
return $pageContent->getReport();
//#section_end#
?>