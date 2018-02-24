<?php
//#section#[header]
// Module Declaration
$moduleID = 275;

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
importer::import("API", "Security");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Model\modules\mMail;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \API\Security\akeys\apiKey;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \DEV\Projects\project;
use \DEV\Projects\projectLibrary;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Get project id
	$projectID = $_POST['pid'];
	if (empty($projectID))
		$has_error = TRUE;
	
	// Check Theme
	if (empty($_POST['status']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_projectStatus");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, "You must select a status!");
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update project status
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "review_project");
	$attr = array();
	$attr['pid'] = $projectID;
	$attr['version'] = $_POST['version'];
	$attr['comments'] = $_POST['comments'];
	$attr['status'] = $_POST['status'];
	$attr['raid'] = account::getAccountID();
	$attr['time'] = time();
	$result = $dbc->execute($q, $attr);
	
	// If there is an error in creating the folder, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_projectStatus");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error updating project status..."));
		return $errFormNtf->getReport();
	}
	
	// Send notification mail for review
	if ($_POST['status'] == 2)
		$mailPath = "/mail/notifications/projects/project_review_pass.html";
	else if ($_POST['status'] == 3)
		$mailPath = "/mail/notifications/projects/project_review_fail.html";
	
	// Get project admins as recipients
	$projectAdmins = array();
	$project = new project($projectID);
	$projectAccounts = $project->getProjectAccounts();
	foreach ($projectAccounts as $account)
	{
		// Check if account is project admin
		$accountID = $account['account_id'];
		if (apiKey::validateGroup($groupID = 7, $accountID, $teamID = NULL, $projectID))
		{
			$accountInfo = account::getInstance()->info($accountID);
			$projectAdmins[$accountInfo['mail']] = $accountInfo['title'];
		}
	}
	
	// Get project release info
	$releaseInfo = projectLibrary::getProjectReleaseInfo($projectID, $_POST['version']);
	
	// Send review notification mail
	$attr = array();
	$attr['project_title'] = $releaseInfo['title'];
	$attr['version'] = $_POST['version'];
	$attr['release_date'] = date("M d, Y", $releaseInfo['time_created']);
	$attr['review_comments'] = $_POST['comments'];
	$subject = "Drovio Project Review";
	mMail::send($mailPath, $subject, $projectAdmins, $attr);
	
	// Create notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	
	// Report action
	$errFormNtf->addReportAction("rvProjects.reload");
	
	return $succFormNtf->getReport();
}
//#section_end#
?>