<?php
//#section#[header]
// Module Declaration
$moduleID = 366;

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
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Login");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \ESS\Environment\session;
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\pages\domain;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\account;
use \API\Security\accountKey;
use \API\Security\privileges;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Login\loginDialog;
use \UI\Modules\MContent;
use \UI\Presentation\popups\popup;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "teamCreatorDialogContainer", TRUE);

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check team name
	$teamName = trim($_POST['tname']);
	$teamName = str_replace(" ", "_", $teamName);
	if (empty($teamName))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_teamName");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check reserved words for team name
	$system_domains = domain::getAllDomains();
	foreach ($system_domains as $domainInfo)
		if ($domainInfo['domain'] == $teamName)
		{
			$has_error = TRUE;
		
			// Header
			$err_header = moduleLiteral::get($moduleID, "lbl_teamName");
			$err = $errFormNtf->addHeader($err_header);
			$errFormNtf->addDescription($err, "Team name is a reserved word. Please try another.");
			
			break;
		}
	
	// Check if there is a team with the same name
	$dbc = new dbConnection();
	$dbq = module::getQuery($moduleID, "get_team");
	
	// Set attributes and execute
	$attr = array();
	$attr['uname'] = $teamName;
	$result = $dbc->execute($dbq, $attr);
	if ($dbc->get_num_rows($result) > 0)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_teamName");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, "Team name already exists. Please try another.");
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	if (account::validate())
	{
		// Create a new team and add this account to it
		$dbc = new dbConnection();
		$dbq = module::getQuery($moduleID, "create_team");
		
		// Set attributes and execute
		$attr = array();
		$attr['uname'] = $teamName;
		$attr['name'] = $teamName;
		$attr['aid'] = account::getAccountID();
		$result = $dbc->execute($dbq, $attr);
		
		// If there is an error in creating team, show it
		if (!$result)
		{
			$err_header = moduleLiteral::get($moduleID, "hd_createTeam");
			$err = $errFormNtf->addHeader($err_header);
			$errFormNtf->addDescription($err, DOM::create("span", "Error creating team..."));
			return $errFormNtf->getReport();
		}
		
		// Add account to TEAM_ADMIN and add key for the same group
		$teamInfo = $dbc->fetch($result);
		$teamID = $teamInfo['id'];
		privileges::addAccountToGroup(account::getAccountID(), "TEAM_ADMIN");
		accountKey::create(6, accountKey::TEAM_KEY_TYPE, $teamID);
		
		// Create module content
		$pageContent = new MContent($moduleID);
		$actionFactory = $pageContent->getActionFactory();
		
		// Redirect to team dashboard
		return $actionFactory->getReportRedirect("/", $teamName, $formSubmit = TRUE);
	}
	else
	{
		// Create session value to create team
		session::set("create_new_team", $teamName, "team_creator");
		
		// Return login dialog
		$lg = new loginDialog();
		return $lg->build()->getReport($background = TRUE, $fade = TRUE);
	}
}

// Add new member form
$formContainer = HTML::select(".teamCreatorDialog .formContainer")->item(0);
$form = new simpleForm();
$createTeamForm = $form->build("", FALSE)->engageModule($moduleID, "createTeam")->get();
DOM::append($formContainer, $createTeamForm);

$container = DOM::create("div", "", "", "tmcnt");
$form->append($container);

$ph = moduleLiteral::get($moduleID, "lbl_teamName", array(), FALSE);
$input = $form->getInput($type = "text", $name = "tname", $value = "", $class = "tminp", $autofocus = TRUE, $required = TRUE);
DOM::attr($input, "placeholder", $ph);
DOM::append($container, $input);

$inputID = DOM::attr($personMail, "id");
$label = $form->getLabel($text = ".drov.io", $for = $inputID, $class = "tmlbl");
DOM::append($container, $label);

$title = moduleLiteral::get($moduleID, "lbl_create");
$addMember = $form->getSubmitButton($title, $id = "btn_create");
HTML::addClass($addMember, "tmbtn");
$form->append($addMember);


// Set placeholder for input
$ph = moduleLiteral::get($moduleID, "lbl_teamName", array(), FALSE);
$rfInput = HTML::select(".teamCreatorDialog .rfContainer .tminp")->item(0);
DOM::attr($rfInput, "placeholder", $ph);

// Create popup
$pp = new popup();
$pp->type($type = popup::TP_PERSISTENT, $toggle = FALSE);
$pp->background(TRUE);
$pp->build($pageContent->get());

return $pp->getReport();
//#section_end#
?>