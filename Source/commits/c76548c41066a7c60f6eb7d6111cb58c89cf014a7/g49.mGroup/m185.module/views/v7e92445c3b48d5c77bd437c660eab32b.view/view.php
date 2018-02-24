<?php
//#section#[header]
// Module Declaration
$moduleID = 185;

// Inner Module Codes
$innerModules = array();
$innerModules['accountInfo'] = 154;

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
use \API\Profile\team;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\popups\popup;

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$popupContent = $pageContent->build("", "noTeamPopup", TRUE)->get();

// Create new team form
$formContainer = HTML::select(".create_new.formContainer")->item(0);
$form = new simpleForm();
$newTeamForm = $form->build("", FALSE)->engageModule($moduleID, "createTeam")->get();
DOM::append($formContainer, $newTeamForm);

// Team name
$ph = moduleLiteral::get($moduleID, "lbl_teamName_placeholder", array(), FALSE);
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "tinput tname", $autofocus = TRUE, $required = TRUE);
HTML::attr($input, "placeholder", $ph);
$form->append($input);

// Account password
$ph = moduleLiteral::get($moduleID, "lbl_password_placeholder", array(), FALSE);
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "tinput", $autofocus = FALSE, $required = TRUE);
HTML::attr($input, "placeholder", $ph);
$form->append($input);

// Create button
$title = moduleLiteral::get($moduleID, "lbl_createNewTeam");
$button = $form->getSubmitButton($title, $id = "", $name = "");
HTML::addClass($button, "tbutton");
$form->append($button);


// Get all account teams
$teamList = HTML::select(".noTeam .teams .tlist")->item(0);
$accountTeams = team::getAccountTeams();
foreach ($accountTeams as $ateam)
{
	$tile = DOM::create("div", "", "", "ttile");
	DOM::append($teamList, $tile);
	
	// Ico
	$img = NULL;
	if (!empty($ateam['profile_image_url']))
	{
		$img = DOM::create("img");
		DOM::attr($img, "src", $ateam['profile_image_url']);
	}
	$ico = DOM::create("div", $img, "", "ico");
	DOM::append($tile, $ico);
	
	// Team name
	$teamName = DOM::create("h3", $ateam['name'], "", "tn");
	DOM::append($tile, $teamName);
	
	$attr = array();
	$attr['teamID'] = $ateam['id'];
	$attr['tid'] = $ateam['id'];
	$actionFactory->setModuleAction($tile, $innerModules['accountInfo'], "switchTeam", "", $attr, $loading = TRUE);
}

// If user has no teams, remove the team list container
if (empty($accountTeams))
{
	$teamContainer = HTML::select(".noTeam .teams")->item(0);
	HTML::replace($teamContainer, NULL);
}

// Create popup
$popup = new popup();
$popup->type(popup::TP_PERSISTENT, FALSE);
$popup->background(TRUE);
$popup->fade(TRUE);
$popup->build($popupContent);

// Get popup report
return $popup->getReport();
//#section_end#
?>