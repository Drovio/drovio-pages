<?php
//#section#[header]
// Module Declaration
$moduleID = 154;

// Inner Module Codes
$innerModules = array();
$innerModules['login'] = 66;
$innerModules['imageEditor'] = 374;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("DRVC", "Profile");
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Profile\team;
use \API\Profile\account;
use \API\Literals\moduleLiteral;
use \DRVC\Profile\managedAccount;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;


// Create HTMLContent
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("userNavigation", "accountToolbarInfo", TRUE);
$profileMenuContainer = HTML::select(".accountInfo .profileMenu")->item(0);

// Get current account info
$currentAccountInfo = account::info();

// Set account image
$imageContainer = HTML::select(".accountInfo .profileImage")->item(0);
if (isset($currentAccountInfo['profile_image_url']))
{
	$img = DOM::create("img");
	DOM::attr($img, "src", $currentAccountInfo['profile_image_url']);
	DOM::append($imageContainer, $img);
}
// Set profile image editor action
$attr = array();
$attr['type'] = 1;
$actionFactory->setModuleAction($imageContainer, $innerModules['imageEditor'], "", "", $attr);

// Get Managed accounts
$accounts = array();
$accounts[] = account::info();
$managedAccounts = managedAccount::getManagedAccounts();
$accounts = array_merge($accounts, $managedAccounts);
$accountContainer = HTML::select(".profileMenu .accounts")->item(0);
// Set account list
if (count($accounts) > 1 && ($currentAccountInfo['administrator'] || !$currentAccountInfo['locked']))
{
	$accountList = HTML::select(".profileMenu .accounts .list")->item(0);
	$currentAccountID = account::getAccountID();
	foreach ($accounts as $account)
	{
		$listItem = DOM::create("li", $account['title'], "", "mitem");
		DOM::append($accountList, $listItem);
		
		// Set default
		if ($account['administrator'])
			HTML::addClass($listItem, "default");
		
		// Set Action
		if ($currentAccountID == $account['id'])
			HTML::addClass($listItem, "selected");
		else
		{
			$attr = array();
			$attr['aid'] = $account['id'];
			$actionFactory->setModuleAction($listItem, $moduleID, "switchAccount", "", $attr);
		}
	}
}
if (count($accounts) <= 1)
	DOM::replace($accountContainer, NULL);

// Get teams
$teamContainer = HTML::select(".profileMenu .teams")->item(0);
$teams = team::getAccountTeams();
$teamList = HTML::select(".profileMenu .teams .list")->item(0);
$currentTeamID = team::getTeamID();
$defaultTeam = team::getDefaultTeam();
$defaultTeamID = $defaultTeam['id'];
foreach ($teams as $team)
{
	$teamName = $team['name'].($defaultTeamID == $team['id'] ? " [D]" : "");
	$listItem = DOM::create("li", $team['name'], "", "mitem");
	DOM::append($teamList, $listItem);
	
	// Set default
	if ($defaultTeamID == $team['id'])
		HTML::addClass($listItem, "default");
	
	// Set Action
	if ($currentTeamID == $team['id'])
		HTML::addClass($listItem, "selected");
	else
	{
		$attr = array();
		$attr['tid'] = $team['id'];
		$actionFactory->setModuleAction($listItem, $moduleID, "switchTeam", "", $attr);
	}
}
if (count($teams) == 0)
	DOM::replace($teamContainer, NULL);

// Create logout form
$logoutContainer = HTML::select(".scitem.logout")->item(0);
$form = new simpleForm("logoutForm");
$logoutForm = $form->build("", FALSE)->engageModule($moduleID, "logoutAccount")->get();
DOM::append($logoutContainer, $logoutForm);

$logoutSubmit = $form->getInput($type = "submit", $name = "logout", $value = " ", $class = "logoutButton", $required = FALSE, $autofocus = FALSE);
$form->append($logoutSubmit);


// Get edit view panels
$usernameViewPanel = HTML::select(".accountInfo .viewPanel.username")->item(0);
$skipUsername = engine::getVar("_profile_info_skip_username");
if (empty($currentAccountInfo['username']) && !$skipUsername)
{
	// Build form
	$form = new simpleForm();
	$usernameForm = $form->build("", FALSE)->engageModule($moduleID, "updateUsername")->get();
	DOM::append($usernameViewPanel, $usernameForm);
	
	// Get username input and label
	$input = $form->getInput($type = "text", $name = "username", $value = " ", $class = "cinput", $required = TRUE, $autofocus = TRUE);
	$inputID = DOM::attr($input, "id");
	
	$title = $pageContent->getLiteral("lbl_editUsername");
	$label = $form->getLabel($title, $for = $inputID, $class = "clabel");
	$form->append($label);
	$form->append($input);
	
	// Reset/skip button
	$title = $pageContent->getLiteral("lbl_skipEdit");
	$cancelButton = $form->getResetButton($title, $id = "", $class = "bbtn skip");
	$form->appendControl($cancelButton);
	DOM::data($cancelButton, "skip", "_profile_info_skip_username");
	
	// Submit/next button
	$title = $pageContent->getLiteral("lbl_done");
	$nextButton = $form->getSubmitButton($title, $id = "", $name = "", $class = "bbtn next");
	$form->appendControl($nextButton);
	
	// Enable view panel
	HTML::addClass($usernameViewPanel, "active");
}
else
	HTML::remove($usernameViewPanel);

$profileImageViewPanel = HTML::select(".accountInfo .viewPanel.profile_image")->item(0);
$skipProfileImage = engine::getVar("_profile_info_skip_profileimage");
if (empty($currentAccountInfo['profile_image_url']) && !$skipProfileImage)
{
	// Build form
	$form = new simpleForm();
	$avatarForm = $form->build($action = "", $defaultButtons = FALSE, $async = TRUE, $fileUpload = TRUE)->engageModule($moduleID, "updateProfileImage")->get();
	DOM::append($profileImageViewPanel, $avatarForm);
	
	// Avatar container
	$imageContainer = DOM::create("div", "", "", "imageContainer");
	$form->append($imageContainer);
	
	// Get image input and label
	$input = $form->getFileInput($name = "profile_image", $class = "cinput", $required = TRUE, $accept = ".png");
	$inputID = DOM::attr($input, "id");
	
	$title = $pageContent->getLiteral("lbl_uploadProfileImage");
	$label = $form->getLabel($title, $for = $inputID, $class = "clabel");
	$form->append($label);
	$form->append($input);
	
	// Reset/skip button
	$title = $pageContent->getLiteral("lbl_skipEdit");
	$cancelButton = $form->getResetButton($title, $id = "", $class = "bbtn skip");
	$form->appendControl($cancelButton);
	DOM::data($cancelButton, "skip", "_profile_info_skip_profileimage");
	
	// Submit/next button
	$title = $pageContent->getLiteral("lbl_done");
	$nextButton = $form->getSubmitButton($title, $id = "", $name = "", $class = "bbtn next");
	$form->appendControl($nextButton);
	
	// Enable view panel
	HTML::addClass($profileImageViewPanel, "active");
}
else
	HTML::remove($profileImageViewPanel);


// Add action to notify that loaded
$pageContent->addReportAction($name = "account.toolbar.loaded");

// Return output
return $pageContent->getReport();
//#section_end#
?>