<?php
//#section#[header]
// Module Declaration
$moduleID = 211;

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
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Comm\mail\rbMailer;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \DEV\Projects\project;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get posted email
	$personEmail = $_POST['invitationEmail'];
	
	// Init project
	$project = new project($_POST['pid']);
	
	// Get account if exists
	$dbc = new interDbConnection();
	$dbq = new dbQuery("28161120613015", "profile.account");
	
	// Set attributes and execute
	$attr = array();
	$attr['mail'] = $personEmail;
	$result = $dbc->execute($dbq, $attr);
	if ($dbc->get_num_rows($result) > 0)
	{
		// Get account id
		$account = $dbc->fetch($result);
		$accountID = $account['id'];
		
		// Add to project
		$project->addAccountToProject($accountID);
		$status = TRUE;
	}
	else
		$status = FALSE;
	
	/*
	// Sent invitation email
	$mailer = new rbMailer("contact");
	
	// Normalize subject
	$projectInfo = $project->info();
	$subject = "Invitation to project '".$projectInfo['title']."' - Redback Developer Projects";
	$mailer->setSubject($subject);
 	
	// Set message
	$message = "Invitation to project ".$projectInfo['title'];
	$mailer->MsgHTML($message);
	
	// Send message 		
	$sender = array();
	$sender['no-reply@redback.gr'] = 'Redback Developer Projects';
	//$mailer->send($subject, $sender, $personEmail);
	*/
	
	if ($status)
	{
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE, $timeout = TRUE);
		
		// Notification Message
		$errorMessage = DOM::create("p", "Member successfully added.");
		$succFormNtf->append($errorMessage);
		return $succFormNtf->getReport();
	}
	else
	{
		$errorNtf = new formNotification();
		$errorNtf->build($type = "error", $header = TRUE, $footer = FALSE);
		
		$title = moduleLiteral::get($moduleID, "lbl_personNotRegistered");
		$hd = DOM::create("p", $title);
		$errorNtf->append($hd);
 		$title = moduleLiteral::get($moduleID, "lbl_noInvitationSupported");
		$hd = DOM::create("p", $title);
		$errorNtf->append($hd);
		return $errorNtf->getReport();
	}
}

return FALSE;
//#section_end#
?>