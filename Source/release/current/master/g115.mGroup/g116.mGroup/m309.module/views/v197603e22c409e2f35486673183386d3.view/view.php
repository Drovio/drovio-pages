<?php
//#section#[header]
// Module Declaration
$moduleID = 309;

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
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Login");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Comm\mail\mailer;
use \ESS\Environment\url;
use \API\Model\sql\dbQuery;
use \API\Model\modules\module;
use \API\Profile\account;
use \API\Profile\person;
use \API\Literals\moduleLiteral;
use \UI\Login\loginDialog;
use \UI\Modules\MContent;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("", "projectRequestInvite", TRUE);

// Get projectID
$projectID = engine::getVar("id");

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// If account not logged in, load login dialog
if (!account::validate())
{
	// Get project url
	if (!empty($projectName))
		$return_url = url::resolve("open", "/projects/".$projectName);
	else
	{
		$params = array();
		$params['id'] = $projectID;
		$return_url = url::resolve("open", "/projects/project.php", $params);
	}
	
	// Create and return login dialog
	$ld = new loginDialog();
	return $ld->build($username = "", $logintype = loginDialog::LGN_TYPE_PAGE, $return_url)->getReport($background = TRUE, $fade = TRUE);
}

if (engine::isPost())
{
	// Get all project admins
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "get_project_admins");
	$attr = array();
	$attr['id'] = $projectID;
	$result = $dbc->execute($q, $attr);
	$admins = $dbc->fetch($result, TRUE);
	
	// Create mail
	$mailer = new mailer("contact");
	
	// Add recipients
	foreach ($admins as $admin)
		$mailer->AddAddress($admin['mail']);
	
	// Reply to person
	$personInfo = person::info();
	$mailer->AddReplyTo($personInfo['mail'], $personInfo['firstname']." ".$personInfo['lastname']);
	
	// Normalize subject
	$subject = "Redback Open Project Request for Invite";
	$mailer->subject($subject);
 	
	// Set message
	$message = "<pre>";
	$message .= "<h4>There is a Redback account requesting an invite for your project [".$projectID."]:[".$projectTitle."].</h4>";
	
	$inviteContext = moduleLiteral::get($moduleID, "lbl_inviteContext", array(), FALSE);
	$message .= "<p>".$inviteContext."</p>";
	$message .= "<p>".engine::getVar("comments")."</p>";
	
	// Add account email
	$accountInfo = account::info();
	$message .= "
<b>".$accountInfo['accountTitle']." (".$personInfo['firstname']." ".$personInfo['lastname'].")\n".$personInfo['mail']."</b>";
	$message .= "</pre>";
	$mailer->MsgHTML($message);
	
	// Send message
	$sender = array();
	$sender["contact@redback.io"] = 'Redback Contact Engine';
	$mailer->send($subject, $sender);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = moduleLiteral::get($moduleID, "lbl_inviteSuccess");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Initialize frame
$title = moduleLiteral::get($moduleID, "hd_requestInvite");
$frame = new dialogFrame();
$frame->build($title)->engageModule($moduleID, "requestInvite");
$form = $frame->getFormFactory();

// Append initial content
$frame->append($pageContent->get());

// Project ID
$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Extra comments for the invite
$ph = moduleLiteral::get($moduleID, "lbl_inviteComments_ph", array(), FALSE);
$input = $form->getTextarea($name = "comments", $value = "", $class = "cmtxt", $autofocus = FALSE, $required = TRUE);
HTML::attr($input, "placeholder", $ph);
$frame->append($input);

return $frame->getFrame();
//#section_end#
?>