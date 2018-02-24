<?php
//#section#[header]
// Module Declaration
$moduleID = 39;

// Inner Module Codes
$innerModules = array();
$innerModules['accountInfo'] = 368;
$innerModules['accountInvitations'] = 369;

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
importer::import("API", "Connect");
importer::import("API", "Geoloc");
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
$profileID = engine::getVar("id");
$profileName = engine::getVar("name");
if (empty($accountID) && empty($profileName))
{
	// Redirect to proper url
	$accountName = account::getUsername();
	$accountID = account::getAccountID();
	if (!empty($accountName))
		$url = url::resolve("www", "/profile/".$accountName);
	else
	{
		$params = array();
		$params['id'] = $accountID;
		$url = url::resolve("www", "/profile/index.php", $params);
	}
	
	// Return redirect report
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Get account information
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_account_info");
$attr = array();
$attr['id'] = $profileID;
$attr['name'] = $profileName;
$result = $dbc->execute($q, $attr);
$accountInfo = $dbc->fetch($result);
$accountID = $accountInfo['accountID'];
if (empty($profileName) && !empty($accountInfo['accountName']))
{
	$accountName = $accountInfo['accountName'];
	$url = url::resolve("www", "/profile/".$accountName);
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Build the page content
$page->build($accountInfo['accountTitle'], "accountProfilePage", TRUE);

// Set account title
$accTitle = HTML::select(".accountProfile .pheader .accTitle")->item(0);
HTML::innerHTML($accTitle, $accountInfo['accountTitle']);

// Set account image
$publicAccountInfo = account::info($accountID);
if (isset($publicAccountInfo['profile_image_url']))
{
	$imageContainer = HTML::select(".accountProfile .photoContainer")->item(0);
	$img = DOM::create("img");
	DOM::attr($img, "src", $publicAccountInfo['profile_image_url']);
	DOM::append($imageContainer, $img);
}

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
$sections["about"] = "accountInfo";
$sections["invitations"] = "accountInvitations";
foreach ($sections as $section => $iModuleID)
{
	// Set panel target group
	$panel = HTML::select(".panels #".$section)->item(0);
	$page->setNavigationGroup($panel, "navGroup");
	
	// Set navigation item action
	$navItem = HTML::select(".pnavigation .navitem.".$section)->item(0);
	$page->setStaticNav($navItem, $section, "sectionContainer", "navGroup", "navItemsGroup", $display = "none");
	
	// Load repository main view
	if (!empty($iModuleID))
	{
		$content = $page->loadView($innerModules[$iModuleID]);
		$container = HTML::select(".panels #".$section)->item(0);
		DOM::append($container, $content);
	}
}

// Return output
return $page->getReport();
//#section_end#
?>