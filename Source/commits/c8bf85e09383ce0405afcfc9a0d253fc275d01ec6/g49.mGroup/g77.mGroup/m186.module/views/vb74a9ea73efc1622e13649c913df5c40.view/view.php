<?php
//#section#[header]
// Module Declaration
$moduleID = 186;

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
importer::import("API", "Developer");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
importer::import("DEV", "Apps");
importer::import("DEV", "Core");
importer::import("DEV", "Modules");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Resources\pages\sitemap;
use \API\Resources\layoutManager;
use \API\Profile\person;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Security\account;
use \UI\Modules\MContent;
use \UI\Presentation\frames\dialogFrame;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \DEV\Version\vcs;
use \DEV\Projects\project;
use \DEV\Core\ajax\ajaxDirectory;
use \DEV\Core\sql\sqlDomain;
use \DEV\Core\sdk\sdkLibrary;
use \DEV\Modules\moduleManager;
use \DEV\Apps\appManager;

// Build pageContent
$pageContent = new MContent($moduleID);
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
			
			// Publish Media and Resources
			$project->publishResources("/Library/Media/c/", $innerFolder = "/media/");
			$project->publishResources("/System/Resources/SDK/", $innerFolder = "/resources/");
			
			// Set next status at will
			$nextStatus = 3;
			break;
		case 2: // Publish Redback Modules
			moduleManager::publish();
			
			// Generate sitemap
			sitemap::generate();
			
			// Publish Resources
			$project->publishResources("/Library/Media/m/");
			
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

$title = literal::dictionary("password");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($authBox, $inputRow);


// Return the report
return $frame->append($pageContent->get())->getFrame();
//#section_end#
?>