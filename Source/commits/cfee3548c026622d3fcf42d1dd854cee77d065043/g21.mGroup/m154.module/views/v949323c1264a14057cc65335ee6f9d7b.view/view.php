<?php
//#section#[header]
// Module Declaration
$moduleID = 154;

// Inner Module Codes
$innerModules = array();
$innerModules['login'] = 66;

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\environment\Url;
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Security\account;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLContent;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Navigation\sideMenu;


// Create HTMLContent
$content = new HTMLContent();
$content->build("userNavigation", "profileSnippet".(account::validate() ? " guest" : ""));
$actionFactory = $content->getActionFactory();

if (!account::validate())
{
	// Login
	$ribbonPanel = new ribbonPanel();
	$loginPanel = $ribbonPanel->build()->get();
	
	$title = literal::get("global::dictionary", "login");
	$imgURL = Url::resource("/Library/Media/images/icons/46x46/login_nav.svg");
	$loginItem = $ribbonPanel->insertPanelItem($type = "big", $title, $imgURL);
	NavigatorProtocol::web($loginItem, Url::resolve("login"), "_blank");
	$content->append($loginPanel);
	
	// Register
	$ribbonPanel = new ribbonPanel();
	$registerPanel = $ribbonPanel->build()->get();
	
	$title = literal::get("global::dictionary", "register");
	$imgURL = Url::resource("/Library/Media/images/icons/46x46/register_nav.svg");
	$registerItem = $ribbonPanel->insertPanelItem($type = "big", $title, $imgURL);
	NavigatorProtocol::web($registerItem, Url::resolve("my", "/register/"), "_blank");
	$content->append($registerPanel);
	
	return $content->getReport();
}

$profileHeader = DOM::create("div", "", "", "profileHeader");
$content->append($profileHeader);

// Create user's Image
$userImgContainer = DOM::create("div", "", "", "userImage");
$content->append($userImgContainer);

$imgURL = Url::resource("/Library/Media/images/common/emptyProfilePicture.svg");
$imgElement = DOM::create("img");
DOM::attr($imgElement, "width", "140px");
DOM::attr($imgElement, "height", "140px");
DOM::attr($imgElement, "src", $imgURL);
DOM::append($userImgContainer, $imgElement);

$profileMenuContainer = DOM::create("div", "", "", "profileMenu");
$content->append($profileMenuContainer);

// Right Sidebar
$sideMenu = new sideMenu();

// Get Accounts
$accountInfo = account::info();
$dbc = new interDbConnection();
$q = new dbQuery("979174393", "profile.account");
$attr = array();
$attr['pid'] = account::getPersonID();
$result = $dbc->execute($q, $attr);
$accounts = $dbc->toFullArray($result);
if (count($accounts) > 1)
{
	// Account menu
	$header = moduleLiteral::get($moduleID, "lbl_accounts");
	$accountsSideMenu = $sideMenu->build("accMenu", $header)->get();
	DOM::append($profileMenuContainer, $accountsSideMenu);
	
	// personalAccount literal
	$lbl_personalAccount = moduleLiteral::get($moduleID, "lbl_personalAccount");
	
	if ($accountInfo['administrator'] || !$accountInfo['locked'])
	{
		$currentAccountID = account::getAccountID();
		foreach ($accounts as $account)
		{
			if ($account['administrator'])
				$menuItemContent = $lbl_personalAccount;
			else
				$menuItemContent = DOM::create("span", $account['title']);
			$listItem = $sideMenu->insertListItem($id = "", $menuItemContent);
			
			// Set Action
			if ($currentAccountID == $account['id'])
				DOM::appendAttr($listItem, "class", "selected");
			else
			{
				$attr = array();
				$attr['accID'] = $account['id'];
				$actionFactory->setPopupAction($listItem, $moduleID, "switchAccount", $attr);
			}
		}
	}
	else
	{
		if ($accountInfo['administrator'])
			$menuItemContent = $lbl_personalAccount;
		else
			$menuItemContent = DOM::create("span", $accountInfo['title']);
		$listItem = $sideMenu->insertListItem($id = "", $menuItemContent);
		DOM::appendAttr($listItem, "class", "selected");
	}
}


// Profile Menu
$header = moduleLiteral::get($moduleID, "lbl_profile");
$profileSideMenu = $sideMenu->build("prMenu", $header)->get();
DOM::append($profileMenuContainer, $profileSideMenu);

// Settings List Item
$menuItemContent = DOM::create("a");
DOM::attr($menuItemContent, "href", Url::resolve("my", "/settings/"));
DOM::attr($menuItemContent, "target", "_blank");
$itemHeader = moduleLiteral::get($moduleID, "lbl_settings");
DOM::append($menuItemContent, $itemHeader);
$sideMenu->insertListItem($id = "", $menuItemContent);

// Create logout form
$lForm = new simpleForm("logout");
$logoutForm = $lForm->build($innerModules['login'], "logout", FALSE)->get();

$header = moduleLiteral::get($moduleID, "lbl_logout", FALSE);
$logoutSubmit = $lForm->getInput($type = "submit", $name = "logout", $value = $header, $class = "logoutButton", $autofocus = FALSE);
$lForm->append($logoutSubmit);
$sideMenu->insertListItem($id = "", $logoutForm);


// Help Menu
$header = moduleLiteral::get($moduleID, "lbl_helpCenter");
$helpSideMenu = $sideMenu->build("prMenu", $header)->get();
DOM::append($profileMenuContainer, $helpSideMenu);

// Support Center
$menuItemContent = DOM::create("a");
DOM::attr($menuItemContent, "href", Url::resolve("support", "/"));
DOM::attr($menuItemContent, "target", "_blank");
$itemHeader = moduleLiteral::get($moduleID, "lbl_supportCenter");
DOM::append($menuItemContent, $itemHeader);
$sideMenu->insertListItem($id = "", $menuItemContent);

// Problem Reporter
$menuItemContent = DOM::create("a");
DOM::attr($menuItemContent, "href", Url::resolve("support", "/help/"));
DOM::attr($menuItemContent, "target", "_blank");
$itemHeader = moduleLiteral::get($moduleID, "lbl_reportProblem");
DOM::append($menuItemContent, $itemHeader);
$sideMenu->insertListItem($id = "", $menuItemContent);

// Return output
return $content->getReport();
//#section_end#
?>