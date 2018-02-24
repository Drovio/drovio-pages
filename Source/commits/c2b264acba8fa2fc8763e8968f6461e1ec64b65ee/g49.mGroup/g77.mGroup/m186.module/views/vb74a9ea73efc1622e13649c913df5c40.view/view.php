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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "Profiler");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
importer::import("DEV", "Apps");
importer::import("DEV", "Core");
importer::import("DEV", "Modules");
//#section_end#
//#section#[code]
use \API\Resources\pages\sitemap;
use \API\Resources\layoutManager;

use \API\Profile\person;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
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
use \DEV\Core\ajax\ajaxDirectory;
use \DEV\Core\sql\sqlDomain;
use \DEV\Core\sdk\sdkLibrary;
use \DEV\Modules\moduleManager;

use \DEV\Apps\appManager;

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
	
	// Set current and next project status
	$currentStatus = $projectInfo['projectStatus'];
	$nextStatus = $currentStatus;
	if ($currentStatus == 1)
		$nextStatus = 2;
	else if ($currentStatus == 3)
		$nextStatus = 4;
	
	// Publish project according to project type
	switch ($projectInfo['projectType'])
	{
		case 1: // Publish Redback Core
			// Deploy SQL Library
			sqlDomain::publish();
			
			// Deploy SDK
			sdkLibrary::publish();
			
			// Publish ajax pages
			ajaxDirectory::publish();
			
			// Export layouts
			layoutManager::export();
			
			// Publish Resources
			$project->publishResources("/Library/Media/c/");
			
			// Set project version in platform status
			$vcs = new vcs($projectID);
			$releases = $vcs->getReleases();
			$version = $releases['master']['current'];
			$build = $releases['master']['packages']['v'.$version]['build'];
			
			// Update version
			if (!empty($version))
				$pStatus->updateProject("Redback SDK", $version.".".$build, status::PROJECT_DEPLOY);
			
			// Log activity
			$pl = new publishLogger(publishLogger::DEPLOY);
			$pl->log("Redback Deploy: Redback Core | Layouts");
			
			// Set next status at will
			$nextStatus = 3;
			break;
		case 2: // Publish Redback Modules
			moduleManager::publish();
			
			// Generate sitemap
			sitemap::generate();
			
			// Publish Resources
			$project->publishResources("/Library/Media/m/");
		
			// Set project version in platform status
			$vcs = new vcs($projectID);
			$releases = $vcs->getReleases();
			$version = $releases['master']['current'];
			$build = $releases['master']['packages']['v'.$version]['build'];
			
			// Update version
			if (!empty($version))
				$pStatus->updateProject("Redback Pages", $version.".".$build, status::PROJECT_DEPLOY);
			
			// Log activity
			$pl = new publishLogger(publishLogger::DEPLOY);
			$pl->log("Redback Deploy: Redback Modules | Sitemap");
			
			// Set next status at will
			$nextStatus = 3;
			
			break;
		case 3:
			// Publish Redback Web Engine SDK
			// Set next status at will
			$nextStatus = 3;
			break;
		case 4:
			// Publish Application
			appManager::publishApp($projectID);
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
	
	// Update project status
	$project->updateStatus($nextStatus);
	
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

$title = moduleLiteral::get($moduleID, "lbl_publishDescription");
$header = HTML::select("h4.description")->item(0);
DOM::append($header, $title);


// Legend
$title = moduleLiteral::get($moduleID, "lbl_projectStatus_current");
$header = HTML::select(".legend .currentStatus .title")->item(0);
DOM::append($header, $title);

$title = moduleLiteral::get($moduleID, "lbl_projectStatus_next");
$header = HTML::select(".legend .nextStatus .title")->item(0);
DOM::append($header, $title);

// Steps
for ($i=1; $i<5; $i++)
{
	$title = moduleLiteral::get($moduleID, "lbl_stepStatus".$i);
	$header = HTML::select(".step.status".$i." .title")->item(0);
	DOM::append($header, $title);
}

// Activate current status
$currentStatus = $projectInfo['projectStatus'];
$active = HTML::select(".step.status".$currentStatus)->item(0);
HTML::addClass($active, "active");

// Set next status
$nextStatus = $currentStatus + 1;
$nextStatus = ($nextStatus > 4 ? 4 : $nextStatus);
$next = HTML::select(".step.status".$nextStatus)->item(0);
HTML::addClass($next, "next");


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