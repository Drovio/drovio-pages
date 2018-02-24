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
importer::import("API", "Model");
importer::import("DEV", "Apps");
importer::import("DEV", "Core");
importer::import("DEV", "Modules");
importer::import("DEV", "Projects");
importer::import("DEV", "Version");
importer::import("DEV", "WebEngine");
importer::import("DEV", "Websites");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \DEV\Version\vcs;
use \DEV\Projects\project;
use \DEV\Core\coreProject;
use \DEV\Modules\modulesProject;
use \DEV\Apps\application;
use \DEV\Websites\website;
use \DEV\WebEngine\webCoreProject;

// Get project ID
$projectID = engine::getVar('id');
$project = new project($projectID);
if (engine::isPost())
{
	// Set step number
	$step = 3;
	
	// Create Module Content
	$pageContent = new MContent($moduleID);
	
	// Build the module content
	$pageContent->build("", "releaseCompleted");
	
	// Set step count
	$pageContent->addReportAction('prj.publisher.setStep', $step);
	
	// Create project release
	$branchName = engine::getVar('new_repo_branch');
	$releaseVersion = engine::getVar('version');
	$status = $project->release($releaseVersion, $_POST['title'], $_POST['changelog']);
	
	if ($status == FALSE)
	{
		// Add error action
		$pageContent->addReportAction("prj.publisher.error", $step);
		
		// Add error content
		$errorContent = moduleLiteral::get($moduleID, "lbl_projectReleaseError");
		$pageContent->append($errorContent);
		
		// Return output
		return $pageContent->getReport(".projectPublisherContainer .errorHolder", "replace");
	}
	
	// Publish project according to project type
	$releaseStatus = FALSE;
	$projectInfo = $project->info();
	switch ($projectInfo['projectType'])
	{
		case 1: // Publish Redback Core
			// Set red project release as published
			$dbc = new dbConnection();
			$q = module::getQuery($moduleID, "publish_red_project");
			$attr = array();
			$attr['pid'] = $projectID;
			$attr['version'] = $releaseVersion;
			$attr['time'] = time();
			$attr['comments'] = "[AUTO REVIEW RED PROJECT] OK";
			$dbc->execute($q, $attr);
			
			// Publish project
			$cProject = new coreProject();
			$releaseStatus = $cProject->publish($releaseVersion, $branchName);
			
			break;
		case 2: // Publish Redback Modules
			// Set red project release as published
			$dbc = new dbConnection();
			$q = module::getQuery($moduleID, "publish_red_project");
			$attr = array();
			$attr['pid'] = $projectID;
			$attr['version'] = $releaseVersion;
			$attr['time'] = time();
			$attr['comments'] = "[AUTO REVIEW RED PROJECT] OK";
			$dbc->execute($q, $attr);
			
			// Publish project
			$mProject = new modulesProject();
			$releaseStatus = $mProject->publish($releaseVersion, $branchName);
			
			break;
		case 3: // Publish Redback Web Engine SDK
			// Set red project release as published
			$dbc = new dbConnection();
			$q = module::getQuery($moduleID, "publish_red_project");
			$attr = array();
			$attr['pid'] = $projectID;
			$attr['version'] = $releaseVersion;
			$attr['time'] = time();
			$attr['comments'] = "[AUTO REVIEW RED PROJECT] OK";
			$dbc->execute($q, $attr);

			// Publish project
			$webCoreProject = new webCoreProject();		
			$releaseStatus = $webCoreProject->publish($releaseVersion, $branchName);
			
			break;
		case 4: // Publish Application
			$app = new application($projectID);
			$releaseStatus = $app->publish($releaseVersion, $branchName);
			
			break;
		case 5:	// Publish Website
			$website = new website($projectID);
			$releaseStatus = $website->publish($releaseVersion, $branchName);
			
			break;
		case 6:
			// Publish Website Template
			break;
		case 7:
			// Publish Website Extension
			break;
	}
	
	// Check release status and rollback if needed
	if (!$releaseStatus)
	{
		// Rollback
		$project->unrelease($releaseVersion);
		
		// Add error action
		$pageContent->addReportAction("prj.publisher.error", $step);
		
		// Add error content
		$errorContent = moduleLiteral::get($moduleID, "lbl_projectReleaseError");
		$pageContent->append($errorContent);
		
		// Return output
		return $pageContent->getReport(".projectPublisherContainer .errorHolder", "replace");
	}
	
	// Add action for status title
	$statusTitle = moduleLiteral::get($moduleID, "lbl_status_publishCompleted", array(), FALSE);
	$pageContent->addReportAction('prj.publisher.addStatusTitle', $statusTitle);
	
	// Current process is completed
	$pageContent->addReportAction("prj.publisher.stepOK", $step);
	
	// Project publisher completed
	$pageContent->addReportAction("prj.publisher.setStep", $step+1);
	$pageContent->addReportAction("prj.publisher.stepOK", $step+1);
	
	// Return output
	return $pageContent->getReport(".projectPublisherContainer .formsHolder", "replace");
}
//#section_end#
?>