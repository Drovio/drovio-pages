<?php
//#section#[header]
// Module Declaration
$moduleID = 260;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

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

$dbc = new dbConnection();
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	if (!simpleForm::validate())
	{
		$hasError = TRUE;
		
		// Header
		$err_header = DOM::create("div", "ERROR");
		$err = $errFormNtf->addErrorHeader("lblDesc_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblDesc_desc", $errFormNtf->getErrorMessage("err.invalid"));
	}
	if ($hasError)
		return $errFormNtf->getReport();
	
	// Remove member from team and remove any relative keys
	$q = module::getQuery($moduleID, "remove_team_member");
	$attr = array();
	$attr['aid'] = $_POST['aid'];
	$attr['tid'] = team::getTeamID();
	$dbc->execute($q, $attr);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	
	$succFormNtf->addReportAction("members.reload");
	return $succFormNtf->getReport();
}

// Get team members
$q = module::getQuery($moduleID, "get_team_members");
$attr = array();
$attr['tid'] = team::getTeamID();
$result = $dbc->execute($q, $attr);
$members = $dbc->fetch($result, TRUE);
foreach ($members as $member)
	if ($member['id'] == $_GET['aid'])
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

// Module id
$input = $form->getInput($type = "hidden", $name = "aid", $value = $currentMember['id']);
$frame->append($input);

// Return the report
return $frame->getFrame();
//#section_end#
?>