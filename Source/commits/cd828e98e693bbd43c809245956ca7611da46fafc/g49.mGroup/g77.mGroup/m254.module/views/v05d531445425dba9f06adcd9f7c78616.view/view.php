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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
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
	
	// Authenticate account password
	$username = account::getUsername();
	$status = account::authenticate($username, $_POST['pwd']);
	if (!$status)
	{
		$has_error = TRUE;
		
		// Header
		$err = $errFormNtf->addHeader("Authentication");
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.authenticate"));
	}
	
	// Check that it is not a red (system's) project
	$projectInfo = $project->info();
	$redProject = ($projectInfo['projectType'] == 1 || $projectInfo['projectType'] == 2 || $projectInfo['projectType'] == 3);
	if ($redProject)
	{
		$has_error = TRUE;
		
		// Header
		$err = $errFormNtf->addHeader("Red Project");
		$errFormNtf->addDescription($err, "You cannot delete a Red Project!");
	}

	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
		
	// Delete project from db
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "delete_project");
	$attr = array();
	$attr['pid'] = $projectID;
	$result = $dbc->execute($q, $attr);
	if ($result)
	{
		// Remove keys relative to project
		$q = module::getQuery($moduleID, "remove_project_keys");
		$attr = array();
		$attr['pid'] = $pid;
		$result = $dbc->execute($q, $attr);
		
		// Remove project folder
		$project->remove();
		
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE);
		
		// Notification Message
		$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
		$succFormNtf->append($errorMessage);
		return $succFormNtf->getReport();
	}
	else
	{
		// Header
		$err_header = "Project";
		$err = $errFormNtf->addHeader("Project");
		$errFormNtf->addDescription($err, "Your project has at least 1 release. You cannot delete it!");
		
		return $errFormNtf->getReport();
	}
}

$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_deleteProject");
$frame->build($title, $action = "", $background = TRUE, $type = dialogFrame::TYPE_OK_CANCEL)->engageModule($moduleID, "deleteProject");
$form = $frame->getFormFactory();

$desc = moduleLiteral::get($moduleID, "lbl_delete_desc");
$p = DOM::create("p", $desc, "", "delete_p");
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