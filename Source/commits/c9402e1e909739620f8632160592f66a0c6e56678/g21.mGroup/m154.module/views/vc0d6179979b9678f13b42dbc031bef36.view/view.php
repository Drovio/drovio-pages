<?php
//#section#[header]
// Module Declaration
$moduleID = 154;

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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Content");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Profile\person;
use \API\Profile\team;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Content\HTMLContent;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Get credentials
	$teamID = $_POST['team_id'];
	$username = person::getUsername();
	$password = $_POST['password'];

	// Validate account
	$valid = account::authenticate($username, $password, $accountID);
	if (!$valid)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("password");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalid"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Switch to given team
	$result = team::switchTeam($teamID, $password);
	
	// If there is an error in switching account, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_switchAccount_header");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error switching team..."));
		return $errFormNtf->getReport();
	}
	
	// Set team as default (if checked)
	if (isset($_POST['set_default']))
		team::setDefaultTeam($teamID);
	
	// Reload page
	$content = new HTMLContent();
	$actionFactory = $content->getActionFactory();
	return $actionFactory->getReportReload($formSubmit = TRUE);
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_switchTeam_header");
$frame->build($title)->engageModule($moduleID, "switchTeam");
$sForm = $frame->getFormFactory();

// Subtitle
$teams = team::getAccountTeams();
foreach ($teams as $team)
	if ($team['id'] == $_GET['tid'])
		$teamName = $team['name'];


$attr = array();
$attr['name'] = $teamName;
$title = moduleLiteral::get($moduleID, "lbl_switchTeam", $attr);
$subtitle = DOM::create("p", $title);
$frame->append($subtitle);

// Hidden account id
$input = $sForm->getInput($type = "hidden", $name = "team_id", $value = $_GET['tid'], $class = "", $autofocus = FALSE);
$frame->append($input);

// Account Password
$title = moduleLiteral::get($moduleID, "lbl_accountPassword");
$input = $sForm->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

$defaultTeam = team::getDefaultTeam();
$defaultTeamID = $defaultTeam['id'];
if ($defaultTeamID != $_GET['tid'])
{
	// Set this team as default (if not)
	$title = moduleLiteral::get($moduleID, "lbl_setDefault");
	$input = $sForm->getInput($type = "checkbox", $name = "set_default", $value = "", $class = "", $autofocus = FALSE);
	$inputRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
	$frame->append($inputRow);
}

// Return the report
return $frame->getFrame();
//#section_end#
?>