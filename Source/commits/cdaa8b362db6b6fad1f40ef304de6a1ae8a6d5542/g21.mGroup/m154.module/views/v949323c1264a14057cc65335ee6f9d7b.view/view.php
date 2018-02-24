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
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Security\account;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Resources\url;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLContent;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Navigation\sideMenu;


// Create HTMLContent
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("userNavigation", "", TRUE);
$profileSnippet = HTML::select(".profileSnippet")->item(0);
if (!account::validate())
	HTML::addClass($profileSnippet, "guest");

$profileMenuContainer = HTML::select(".profileMenu")->item(0);

// Right Sidebar
$sideMenu = new sideMenu();

// Get current account info
$accountInfo = account::info();

// Get Accounts
$dbc = new interDbConnection();
$q = new dbQuery("979174393", "profile.account");
$attr = array();
$attr['pid'] = account::getPersonID();
$result = $dbc->execute($q, $attr);
$accounts = $dbc->fetch($result, TRUE);
if (count($accounts) > 1)
{
	// Account menu
	$header = moduleLiteral::get($moduleID, "lbl_accounts");
	$accountsSideMenu = $sideMenu->build("accMenu", $header)->get();
	DOM::append($profileMenuContainer, $accountsSideMenu);
	
	// Set account menu
	if ($accountInfo['administrator'] || !$accountInfo['locked'])
	{
		$currentAccountID = account::getAccountID();
		foreach ($accounts as $account)
		{
			$menuItemContent = DOM::create("span", $account['title']);
			$listItem = $sideMenu->insertListItem($id = "", $menuItemContent);
			
			// Set Action
			if ($currentAccountID == $account['id'])
				DOM::appendAttr($listItem, "class", "selected");
			else
			{
				$attr = array();
				$attr['accID'] = $account['id'];
				$actionFactory->setModuleAction($listItem, $moduleID, "switchAccount", "", $attr);
			}
		}
	}
	else
	{
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
$header = moduleLiteral::get($moduleID, "lbl_support");
$helpSideMenu = $sideMenu->build("prMenu", $header)->get();
DOM::append($profileMenuContainer, $helpSideMenu);

// Support Center
$title = moduleLiteral::get($moduleID, "lbl_helpCenter");
$url = url::resolve("www", "/help/");
$wl = $pageContent->getWeblink($url, $title, "_blank");
$sideMenu->insertListItem($id = "", $wl);

// Problem Reporter
$title = moduleLiteral::get($moduleID, "lbl_reportProblem");
$url = url::resolve("www", "/help/");
$wl = $pageContent->getWeblink($url, $title, "_blank");
$sideMenu->insertListItem($id = "", $wl);

// Return output
return $pageContent->getReport();
//#section_end#
?>