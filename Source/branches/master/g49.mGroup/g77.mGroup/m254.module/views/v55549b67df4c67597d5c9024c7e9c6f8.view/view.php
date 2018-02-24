<?php
//#section#[header]
// Module Declaration
$moduleID = 254;

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
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("DEV", "Projects");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \API\Resources\archive\zipManager;
use \API\Resources\filesystem\directory;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Projects\project;

// Initialize project
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');
$project = new project($projectID, $projectName);

// Get project info
$projectInfo = $project->info();

// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

if (engine::isPost())
{
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	$has_error = FALSE;
	$pwd = $_POST['pwd'];
	
	
	// Authenticate account
	$username = account::getUsername();
	$status = account::authenticate($username, $pwd);
	if (!$status)
	{
		$has_error = TRUE;
		
		// Header
		$err = $errFormNtf->addHeader("Authentication");
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.authenticate"));
	}

	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
		
	// Create project backup
	$info = $project->info();
	$projectTitle = $info["title"];
	$projectTitle = str_replace(" ", "_", $projectTitle);
	
	// Get project contents to backup
	$rootFolder = $project->getRootFolder();
	$contents = directory::getContentList(systemRoot."/".$rootFolder, TRUE);
	
	// Set extended time limit
	set_time_limit(600);
	
	// Set archive file name
	$time = intval(date("G"));
	if ($time < 7)
		$time = "_night";
	else if ($time < 12)
		$time = "_start";
	else if ($time < 19)
		$time = "_noon";
	else
		$time = "_end";
	$archiveName = strtolower($projectTitle."_".date("Y_m_d").$time);
	
	// Create archive (example: 2013_06_29_start) into project's resources folder
	$resourcesFolder = $project->getResourcesFolder();
	$archive = systemRoot.$resourcesFolder."/backup/".$archiveName.".zip";
	$bStatus = zipManager::create($archive, $contents, TRUE, TRUE);
	if (!$bStatus)
	{
		$err = $errFormNtf->addHeader("Backup Status");
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_backupProject");
$frame->build($title, $action = "", $background = TRUE, $type = dialogFrame::TYPE_OK_CANCEL)->engageModule($moduleID, "backupProject");
$form = $frame->getFormFactory();

$desc = moduleLiteral::get($moduleID, "lbl_backup_desc");
$p = DOM::create("p", $desc, "", "backup_p");
$frame->append($p);

// Project ID
$input= $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "", $required = TRUE, $autofocus = FALSE);
$form->append($input);

// Password Field
$title = moduleLiteral::get($moduleID, "lbl_authenticate");
$input= $form->getInput("password", "pwd", "", "", TRUE, TRUE);
$form->insertRow($title, $input,TRUE);

return $frame->getFrame();
//#section_end#
?>