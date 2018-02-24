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
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \API\Security\accountKey;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Projects\project;

$dbc = new dbConnection();
$projectID = engine::getVar("id");
$memberID = engine::getVar("aid");
if (engine::isPost())
{
	// Remove member from team and remove any relative keys
	$q = module::getQuery($moduleID, "remove_project_member");
	$attr = array();
	$attr['aid'] = $memberID;
	$attr['pid'] = $projectID;
	$dbc->execute($q, $attr);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	
	$succFormNtf->addReportAction("members.reload");
	return $succFormNtf->getReport();
}

// Get team members
$project = new project($projectID);
$members = $project->getProjectAccounts();
foreach ($members as $member)
	if ($member['accountID'] == $_GET['aid'])
		$currentMember = $member;
		
// Get project info
$project = new project($projectID);
$projectInfo = $project->info();

// Build the frame
$frame = new dialogFrame();
$frame->build("Remove Member", $moduleID, "removeMember", FALSE, dialogFrame::TYPE_YES_NO);
$form = new simpleForm();

// Header
$attr = array();
$attr['member'] = $currentMember['title'];
$attr['project'] = $projectInfo['title'];
$title = moduleLiteral::get($moduleID, "lbl_removeAgree", $attr);
$hd = DOM::create("h3", $title);
$frame->append($hd);

// Project id
$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID);
$frame->append($input);

// Account id
$input = $form->getInput($type = "hidden", $name = "aid", $value = $currentMember['accountID']);
$frame->append($input);

// Return the report
return $frame->getFrame();
//#section_end#
?>