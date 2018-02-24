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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("DEV", "Profiler");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Profile\tester;
use \API\Profile\person;
use \API\Security\account;
use \API\Resources\archive\zipManager;
use \API\Resources\filesystem\directory;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \DEV\Profiler\log\publishLogger;
use \DEV\Profiler\status;
use \DEV\Version\vcs;
use \DEV\Projects\project;

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

if ($hasError)
	return $errorNtf->getReport();



// Set project version in platform status
$projects = array();
$projects[] = 1;
$projects[] = 2;
$projects[] = 3;

$pStatus = new status();

// Set project versions
foreach ($projects as $projectID)
{
	$project = new project($projectID);
	$repository = $project->getRepository();
	$projectInfo = $project->info();
	$vcs = new vcs($repository);
	$releases = $vcs->getReleases();
	$version = $releases['master']['current'];
	$build = $releases['master']['packages']['v'.$version]['build'];
	
	// Update version
	if (!empty($version))
	{
		$name = ($projectInfo['id'] == 1 ? "Redback SDK" : "Redback Pages");
		$pStatus->updateProject($name, $version, status::PROJECT_PUBLISH);
	}
}

// Set platform status
$pStatus->setStatus(status::STATUS_OK);


// Create Backup
$suffix = "";
$time = intval(date("G"));
if ($time < 7)
	$time = "_dawn";
else if ($time < 12)
	$time = "_start";
else if ($time < 17)
	$time = "_noon";
else
	$time = "_night";

// example: 29jun2013_start
$name = strtolower(date("dMY").$time);

// Get ZipFile Name
$trunkReleaseName = systemRoot.tester::getTrunk()."/release/".$name.".zip";

// Get Directory Contents
$contents = directory::getContentList(systemRoot."/", FALSE);

// Set time limit
set_time_limit(100);

// release
zipManager::create($trunkReleaseName, $contents, TRUE, TRUE);


// Log activity
$logDescription = "Redback published as healthy at ".date("F j, Y, G:i (T)");
$pl = new publishLogger(publishLogger::PUBLISH);
$pl->log($logDescription);


$status = TRUE;

$succFormNtf = new formNotification();
$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);

// Notification Message
$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
$succFormNtf->append($errorMessage);
return $succFormNtf->getReport();
//#section_end#
?>