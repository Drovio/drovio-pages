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
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dBConnection;
use \SYS\Resources\url;
use \API\Model\units\sql\dbQuery;
use \API\Security\account;
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;


// Create HTMLContent
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("userNavigation", "", TRUE);
$profileSnippet = HTML::select(".accountInfo")->item(0);
if (!account::validate())
	HTML::addClass($profileSnippet, "guest");

$profileMenuContainer = HTML::select(".accountInfo .profileMenu")->item(0);

// Get current account info
$currentAccountInfo = account::info();

// Get Accounts
$dbc = new dbConnection();
$q = new dbQuery("979174393", "profile.account");
$attr = array();
$attr['pid'] = account::getPersonID();
$result = $dbc->execute($q, $attr);
$accounts = $dbc->fetch($result, TRUE);
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
		
		// Set Action
		if ($currentAccountID == $account['id'])
			DOM::appendAttr($listItem, "class", "selected");
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
$teams = array();
foreach ($teams as $team)
{
	// Add team and action to switch
	$teamList = HTML::select(".profileMenu .teams .list")->item(0);
}
if (count($teams) == 0)
	DOM::replace($teamContainer, NULL);

// Create logout form
$logoutContainer = HTML::select(".mitem.logout")->item(0);
$form = new simpleForm("logoutForm");
$logoutForm = $form->build($moduleID, "logoutAccount", FALSE)->get();
DOM::append($logoutContainer, $logoutForm);

$title = moduleLiteral::get($moduleID, "lbl_logout", array(), FALSE);
$logoutSubmit = $form->getInput($type = "submit", $name = "logout", $value = $title, $class = "logoutButton", $autofocus = FALSE);
$form->append($logoutSubmit);

// Return output
return $pageContent->getReport();
//#section_end#
?>