<?php
//#section#[header]
// Module Declaration
$moduleID = 186;

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
importer::import("API", "Developer");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "Profiler");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Developer\components\sdkManager;
use \API\Developer\components\moduleManager;
use \API\Developer\components\ajaxManager;
use \API\Developer\components\sql\dvbLib;
use \API\Developer\components\pages\sitemap;
use \API\Resources\layoutManager;

use \API\Profile\person;
use \API\Resources\literals\literal;
use \API\Resources\literals\moduleLiteral;
use \API\Security\account;
use \UI\Html\HTMLContent;
use \UI\Presentation\frames\dialogFrame;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

use \DEV\Profiler\log\publishLogger;
use \DEV\Profiler\status;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Build pageContent
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("", "publisherWizard", TRUE);


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Check requirements
	$errorNtf = new formErrorNotification();
	$errorNtf->build();
	$hasError = FALSE;
	
	// Authenticate account
	$username = person::getUsername();
	$password = $_POST['password'];
	if (!account::authenticate($username, $password))
	{
		$hasError = TRUE;
		$hd = moduleLiteral::get($moduleID, "authentication_error_header");
		$header = $errorNtf->addErrorHeader("err", $hd);
		$desc = moduleLiteral::get($moduleID, "authentication_error_msg");
		$errorNtf->addErrorDescription($header, "errDesc", $desc, $extra = "");
	}
	
	// Get project id and name
	$projectID = $_POST['pid'];
	
	// Get project info
	$project = new project($projectID);
	$projectInfo = $project->info();
	if (is_null($projectInfo))
		$hasError = TRUE;
	
	// Check if account is valid for project
	$valid = $project->validate();
	if (!$valid)
		$hasError = TRUE;
	
	if ($hasError)
		return $errorNtf->getReport();
	
	// Initialize platform status manager
	$pStatus = new status();
	
	// Publish project according to project type
	switch ($projectInfo['projectType'])
	{
		case 1: // Publish Redback Core
			// Deploy SQL Library
			dvbLib::deploy();
			
			// Deploy SDK
			sdkManager::deploy();
			
			// Deploy ajax pages
			ajaxManager::deploy();
			
			// Export layouts
			layoutManager::export();
			
			// Publish Resources
			$project->publishResources("/Library/Media/c/");
			
			// Set project version in platform status
			$repository = $project->getRepository();
			$vcs = new vcs($repository);
			$releases = $vcs->getReleases();
			$version = $releases['master']['current'];
			$build = $releases['master']['packages']['v'.$version]['build'];
			
			// Update version
			if (!empty($version))
				$pStatus->updateProject($projectInfo['name'], $version.".".$build, status::PROJECT_DEPLOY);
			
			// Log activity
			$pl = new publishLogger(publishLogger::DEPLOY);
			$pl->log("Redback Deploy: Redback Core | Layouts");
			
			break;
		case 2: // Publish Redback Modules
			moduleManager::deploy();
			
			// Generate sitemap
			sitemap::generate();
			
			// Publish Resources
			$project->publishResources("/Library/Media/m/");
		
			// Set project version in platform status
			$repository = $project->getRepository();
			$vcs = new vcs($repository);
			$releases = $vcs->getReleases();
			$version = $releases['master']['current'];
			$build = $releases['master']['packages']['v'.$version]['build'];
			
			// Update version
			if (!empty($version))
				$pStatus->updateProject($projectInfo['name'], $version.".".$build, status::PROJECT_DEPLOY);
			
			// Log activity
			$pl = new publishLogger(publishLogger::DEPLOY);
			$pl->log("Redback Deploy: Redback Modules | Sitemap");
			
			break;
		case 3:
			// Publish Redback Web Engine SDK
			break;
		case 4:
			// Publish Application
			break;
		case 5:
			// Publish Website
			break;
		case 6:
			// Publish Website Template
			break;
		case 7:
			// Publish Website Extension
			break;
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Create frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_projectPublisherTitle");
$frame->build($title, $moduleID, "publishProject");

// Get project id and name
$projectID = $_REQUEST['id'];

// Get project info
$project = new project($projectID);
$projectInfo = $project->info();

// If project is invalid, return error page
if (is_null($projectInfo))
{
	// Add notification
	
	// Return report
	return $frame->append($pageContent->get())->getFrame();
}
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];



// Check if account is valid for project
$valid = $project->validate();
if (!$valid)
{
	// Add notification
	
	// Return report
	return $frame->append($pageContent->get())->getFrame();
}

// Create form for getting inputs
$form = new simpleForm();

// Hidden project id
$input = $form->getInput($type = "hidden", $name = "pid", $value = $projectID, $class = "", $autofocus = FALSE, $required = TRUE);
$frame->append($input);


$title = moduleLiteral::get($moduleID, "lbl_publisher_promptTitle");
$header = HTML::select("h3.title")->item(0);
DOM::append($header, $title);

// Authentication box
$authBox = HTML::select(".authBox")->item(0);

// Header
$literal = moduleLiteral::get($moduleID, "lbl_authenticate");
$hd = DOM::create("h4", $literal);
DOM::append($authBox, $hd);

$title = literal::dictionary("password");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($authBox, $inputRow);


// Return the report
return $frame->append($pageContent->get())->getFrame();
//#section_end#
?>