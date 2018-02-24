<?php
//#section#[header]
// Module Declaration
$moduleID = 260;

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
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
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

// Get team id
$teamID = engine::getVar('tid');
$memberID = engine::getVar('aid');

$dbc = new dbConnection();
if (engine::isPost())
{
	// Remove member from team and remove any relative keys
	$q = module::getQuery($moduleID, "remove_team_member");
	$attr = array();
	$attr['aid'] = $memberID;
	$attr['tid'] = $teamID;
	$dbc->execute($q, $attr);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	
	// Reload members action
	$succFormNtf->addReportAction("members.reload");
	return $succFormNtf->getReport();
}

// Get team members
$q = module::getQuery($moduleID, "get_team_members");
$attr = array();
$attr['tid'] = $teamID;
$result = $dbc->execute($q, $attr);
$members = $dbc->fetch($result, TRUE);
foreach ($members as $member)
	if ($member['id'] == $memberID)
		$currentMember = $member;


// Build the frame
$frame = new dialogFrame();
$frame->build("Remove Member", $moduleID, "removeMember", FALSE, dialogFrame::TYPE_YES_NO);
$form = new simpleForm();

// Header
$attr = array();
$attr['member'] = $currentMember['title'];
$attr['team'] = team::getTeamName();
$title = moduleLiteral::get($moduleID, "lbl_removeAgree", $attr);
$hd = DOM::create("h3", $title);
$frame->append($hd);

// Account id
$input = $form->getInput($type = "hidden", $name = "aid", $value = $memberID);
$frame->append($input);

// Team id
$input = $form->getInput($type = "hidden", $name = "tid", $value = $teamID);
$frame->append($input);

// Return the report
return $frame->getFrame();
//#section_end#
?>