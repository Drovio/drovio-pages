<?php
//#section#[header]
// Module Declaration
$moduleID = 211;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("DEV", "Projects");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Model\modules\mMail;
use \API\Profile\account;
use \API\Profile\team;
use \UI\Forms\formReport\formNotification;
use \UI\Modules\MContent;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$projectID = engine::getVar('context');
$project = new project($projectID);
$projectInfo = $project->info();
$email = engine::getVar('email');

if (engine::isPost())
{
	// Send invitation email
	$attr = array();
	$attr['project_title'] = $projectInfo['title'];
	$attr['account_title'] = account::getAccountTitle();
	$attr['email'] = $email;
	$status = mMail::send("/resources/mail/invitations/project_invitation_guest.html", "Project Invitation", $email, $attr);
	
	if ($status)
	{
		// Show notification result
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = formNotification::SUCCESS, $header = FALSE, $timeout = TRUE, $disposable = TRUE);
		
		// Notification Message
		$errorMessage = DOM::create("h2", "Email sent.");
		$succFormNtf->append($errorMessage);
		
		return $succFormNtf->getReport();
	}
	else
	{
		// Show notification result
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = formNotification::ERROR, $header = FALSE, $timeout = FALSE, $disposable = TRUE);
		
		// Notification Message
		$errorMessage = DOM::create("h2", "Error sending invitation email.");
		$succFormNtf->append($errorMessage);
		
		return $succFormNtf->getReport();
	}
}
//#section_end#
?>