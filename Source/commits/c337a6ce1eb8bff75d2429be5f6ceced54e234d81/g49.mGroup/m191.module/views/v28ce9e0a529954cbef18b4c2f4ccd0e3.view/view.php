<?php
//#section#[header]
// Module Declaration
$moduleID = 191;

// Inner Module Codes
$innerModules = array();
$innerModules['developerHome'] = 100;

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
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\account;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get account profile id and username
$accountID = engine::getVar('id');
$accountName = engine::getVar('name');
if (empty($accountID) && empty($accountName))
{
	// Redirect to proper url
	$accountName = account::getUsername();
	$accountID = account::getAccountID();
	if (!empty($accountName))
		$url = url::resolve("developer", "/profile/".$accountName);
	else
	{
		$params = array();
		$params['id'] = $accountID;
		$url = url::resolve("developer", "/profile/index.php", $params);
	}
	
	// Return redirect report
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Get account information
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_account_info");
$attr = array();
$attr['id'] = $accountID;
$attr['name'] = $accountName;
$result = $dbc->execute($q, $attr);
$accountInfo = $dbc->fetch($result);
$accountID = $accountInfo['accountID'];
if (empty($accountName) && !empty($accountInfo['accountName']))
{
	$accountName = $accountInfo['accountName'];
	$url = url::resolve("developer", "/profile/".$accountName);
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Check if is a locked/shared account (not admin)
if (!$accountInfo['administrator'])
{
	// Go back to developer home
	$url = url::resolve("developer", "/");
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Build the page content
$page->build($accountInfo['accountTitle'], "devProfilePage", TRUE);

// Set account title
$accTitle = HTML::select(".devProfile .pheader .accTitle")->item(0);
HTML::innerHTML($accTitle, $accountInfo['accountTitle']);


// Contact developer dialog button
$contactDeveloperButton = HTML::select(".pheader .pinfo .rbutton.contact")->item(0);
if ($accountID == account::getAccountID())
	HTML::replace($contactDeveloperButton, NULL);
else
{
	$attr = array();
	$attr['aid'] = $accountID;
	$actionFactory->setModuleAction($contactDeveloperButton, $moduleID, "contactDialog", "", $attr);
}

$sections = array();
$sections["about"] = "aboutDeveloper";
$sections["projects"] = "publicProjects";
foreach ($sections as $section => $moduleView)
{
	// Set panel target group
	$panel = HTML::select(".panels #".$section)->item(0);
	$page->setNavigationGroup($panel, "navGroup");
	
	// Set navigation item action
	$navItem = HTML::select(".pnavigation .navitem.".$section)->item(0);
	$page->setStaticNav($navItem, $section, "sectionContainer", "navGroup", "navItemsGroup", $display = "none");
	
	// Load repository main view
	if (!empty($moduleView))
	{
		$content = module::loadView($moduleID, $moduleView);
		$container = HTML::select(".panels #".$section)->item(0);
		DOM::append($container, $content);
	}
}


// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['developerHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$devProfile = HTML::select(".devProfile")->item(0);
$footerMenu = module::loadView($innerModules['developerHome'], "footerMenu");
DOM::append($devProfile, $footerMenu);

// Return output
return $page->getReport();
//#section_end#
?>