<?php
//#section#[header]
// Module Declaration
$moduleID = 113;

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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("DEV", "Profiler");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Developer\components\sdkManager;
use \API\Developer\components\appcenter\appLibrary;
use \API\Developer\components\moduleManager;
use \API\Developer\components\ajaxManager;
use \API\Developer\components\sql\dvbLib;
use \API\Developer\components\pages\sitemap;
use \API\Profile\person;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\layoutManager;
use \API\Security\account;
use \UI\Html\HTMLContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

use \DEV\Profiler\log\publishLogger;
use \DEV\Profiler\status;
use \DEV\Version\vcs;
use \DEV\Projects\project;

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	// Check requirements
	$errorNtf = new formErrorNotification();
	$errorNtf->build();
	$hasError = FALSE;
	
	$pStatus = new status();
	
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
	
	if ($hasError)
		return $errorNtf->getReport();
	
	// Set time limit
	set_time_limit(300);
		
	// Log description
	$logDescription = "Redback Deploy : ";
	
	// Export Core SDK
	if (isset($_POST['core']))
	{
		$logDescription .= "Redback Core | ";
		
		// Deploy SQL Library
		dvbLib::deploy();
		
		// Deploy SDK
		sdkManager::deploy();
		
		// Deploy ajax pages
		ajaxManager::deploy();
		
		// Set project version in platform status
		$project = new project(1);
		$repository = $project->getRepository();
		$projectInfo = $project->info();
		$vcs = new vcs($repository);
		$releases = $vcs->getReleases();
		logger::log("", logger::DEBUG, $releases);
		$version = $releases['master']['current'];
		$build = $releases['master']['packages']['v'.$version]['build'];
		
		// Update version
		if (!empty($version))
			$pStatus->updateProject($projectInfo['name'], $version.".".$build, status::PROJECT_DEPLOY);
	}
	
	// Export Modules
	if (isset($_POST['modules']))
	{
		$logDescription .= "Redback Modules | ";
		moduleManager::deploy();
		
		// Set project version in platform status
		$project = new project(2);
		$repository = $project->getRepository();
		$projectInfo = $project->info();
		$vcs = new vcs($repository);
		$releases = $vcs->getReleases();
		$version = $releases['master']['current'];
		$build = $releases['master']['packages']['v'.$version]['build'];
		
		// Update version
		if (!empty($version))
			$pStatus->updateProject($projectInfo['name'], $version.".".$build, status::PROJECT_DEPLOY);
	}
	
	// Export Web Engine SDK
	if (isset($_POST['webengine']))
	{
		$logDescription .= "Web Engine SDK | ";
		
		// Set project version in platform status
		$project = new project(3);
		$repository = $project->getRepository();
		$projectInfo = $project->info();
		$vcs = new vcs($repository);
		$releases = $vcs->getReleases();
		$version = $releases['master']['current'];
		$build = $releases['master']['packages']['v'.$version]['build'];
		
		// Update version
		if (!empty($version))
			$pStatus->updateProject($projectInfo['name'], $version.".".$build, status::PROJECT_DEPLOY);
	}
	
	// Create sitemap
	if (isset($_POST['pages']))
	{
		$logDescription .= "Pages | ";
		sitemap::generate();
	}
	
	// Export Layouts
	$logDescription .= "Layouts";
	layoutManager::export();
	
	// Log activity
	$pl = new publishLogger(publishLogger::DEPLOY);
	$pl->log($logDescription);
		
	$status = TRUE;
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the page content
$pageContent = new HTMLContent();
$pageContent->build();


// Header
$headerContent = moduleLiteral::get($moduleID, "lbl_siteDeployHeader");
$header = DOM::create("h2", $headerContent);
$pageContent->append($header);

// Description
$title = moduleLiteral::get($moduleID, "lbl_subtitle");
$desc = DOM::create("p", $title);
$pageContent->append($desc);


// Build publisher's form
$form = new simpleForm("publisher");
$publisherForm = $form->build($moduleID, "siteDeploy")->get();
$pageContent->append($publisherForm);

$title = moduleLiteral::get($moduleID, "lbl_core");
$input = $form->getInput($type = "checkbox", $name = "core", $value = "", $class = "", $autofocus = FALSE);
DOM::attr($input, "checked", "checked");
$form->insertRow($title, $input, $required = TRUE, $notes = "");

$title = moduleLiteral::get($moduleID, "lbl_modules");
$input = $form->getInput($type = "checkbox", $name = "modules", $value = "", $class = "", $autofocus = FALSE);
DOM::attr($input, "checked", "checked");
$form->insertRow($title, $input, $required = TRUE, $notes = "");

$title = moduleLiteral::get($moduleID, "lbl_webEngine");
$input = $form->getInput($type = "checkbox", $name = "webengine", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

$title = moduleLiteral::get($moduleID, "lbl_pages");
$input = $form->getInput($type = "checkbox", $name = "pages", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");


// Header
$literal = moduleLiteral::get($moduleID, "lbl_authenticate");
$hd = DOM::create("h4", $literal);
$form->append($hd);

$title = literal::dictionary("password");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Return content report
return $pageContent->getReport();
//#section_end#
?>