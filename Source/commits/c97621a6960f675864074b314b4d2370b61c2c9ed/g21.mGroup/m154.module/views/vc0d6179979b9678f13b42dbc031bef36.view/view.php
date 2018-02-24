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
importer::import("API", "Literals");
importer::import("API", "Profile");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Profile\person;
use \API\Profile\team;
use \API\Profile\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Modules\MContent;

$teamID = engine::getVar('tid');
if (engine::isPost())
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Switch to given team
	$password = $_POST['password'];
	$result = team::switchTeam($teamID, $password);
	
	// If there is an error in switching account, show it
	if (!$result)
	{
		// Header
		$err_header = literal::dictionary("password");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalid"));
		return $errFormNtf->getReport();
	}
	
	// Set team as default (if checked)
	if (isset($_POST['set_default']))
		team::setDefaultTeam($teamID);
	
	// Reload page
	$content = new MContent();
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
	if ($team['id'] == $teamID)
		$teamName = $team['name'];


$attr = array();
$attr['name'] = $teamName;
$title = moduleLiteral::get($moduleID, "lbl_switchTeam", $attr);
$subtitle = DOM::create("p", $title);
$frame->append($subtitle);

// Hidden account id
$input = $sForm->getInput($type = "hidden", $name = "tid", $value = $teamID, $class = "", $autofocus = FALSE);
$frame->append($input);

// Account Password
$title = moduleLiteral::get($moduleID, "lbl_accountPassword");
$input = $sForm->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

$defaultTeam = team::getDefaultTeam();
$defaultTeamID = $defaultTeam['id'];
if ($defaultTeamID != $teamID)
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