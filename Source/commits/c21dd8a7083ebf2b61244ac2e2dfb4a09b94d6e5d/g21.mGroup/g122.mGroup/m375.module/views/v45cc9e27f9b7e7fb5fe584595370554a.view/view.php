<?php
//#section#[header]
// Module Declaration
$moduleID = 375;

// Inner Module Codes
$innerModules = array();
$innerModules['imageEditor'] = 374;
$innerModules['accountSettings'] = 159;
$innerModules['accountInfo'] = 368;
$innerModules['accountInvitations'] = 369;
$innerModules['publicProjects'] = 379;

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
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get account profile id and username
$profileID = engine::getVar("id");
$profileName = engine::getVar("name");
$currentAccountID = account::getAccountID();
if (empty($profileID) && empty($profileName))
{
	// Redirect to proper url
	$profileName = account::getUsername();
	$profileID = account::getAccountID();
	if (!empty($profileName))
		$url = url::resolve("www", "/profile/".$profileName);
	else
	{
		$params = array();
		$params['id'] = $profileID;
		$url = url::resolve("www", "/profile/index.php", $params);
	}
	
	// Return redirect report
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Get account information
$dbc = new dbConnection();
$q = $page->getQuery("get_account_info");
$attr = array();
$attr['id'] = $profileID;
$attr['name'] = $profileName;
$result = $dbc->execute($q, $attr);
$accountInfo = $dbc->fetch($result);
$profileID = $accountInfo['accountID'];
if (empty($profileName) && !empty($accountInfo['username']))
{
	$profileName = $accountInfo['username'];
	$url = url::resolve("www", "/profile/".$profileName);
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Build the page content
$page->build($accountInfo['accountTitle'], "accountProfilePage", TRUE);

// Set account title
$accTitle = HTML::select(".accountProfile .accTitle")->item(0);
HTML::innerHTML($accTitle, $accountInfo['accountTitle']);

// Set account image
$publicAccountInfo = account::info($profileID);
if (isset($publicAccountInfo['profile_image_url']))
{
	$imageContainer = HTML::select(".accountProfile .profileImage")->item(0);
	$img = DOM::create("img");
	DOM::attr($img, "src", $publicAccountInfo['profile_image_url']);
	DOM::append($imageContainer, $img);
}

$sections = array();
$sections["about"] = "accountInfo";
$sections["invitations"] = "accountInvitations";
$sections["projects"] = "publicProjects";

$privateSections = array();
$privateSections[] = "invitations";
if ($profileID != $currentAccountID)
{
	// Remove private sections fom list
	foreach ($privateSections as $section)
		unset($sections[$section]);
	
	// Remove navitems
	$privateNavItems = HTML::select(".navitem.private");
	foreach ($privateNavItems as $item)
		HTML::replace($item, NULL);
}
else
{
	// Set profile image editor action
	$editProfileImageButton = HTML::select(".accountProfile .sidebar .pr_image_editor")->item(0);
	$attr = array();
	$attr['type'] = 1;
	$actionFactory->setModuleAction($editProfileImageButton, $innerModules['imageEditor'], "", "", $attr);
	
	// Set account settings action
	$settingsButton = HTML::select(".accountProfile .sidebar .stbutton")->item(0);
	$attr = array();
	$attr['name'] = $accountInfo['username'];
	$actionFactory->setModuleAction($settingsButton, $innerModules['accountSettings'], "", "", $attr);
}
$sectionbody = HTML::select(".sectionbody")->item(0);
foreach ($sections as $section => $iModuleID)
{
	// Set navigation item action
	$ref = $section."_ref";
	$navItem = HTML::select(".pnavigation .navitem.".$section)->item(0);
	$page->setStaticNav($navItem, $ref, "sectionContainer", "navGroup", "navItemsGroup", $display = "none");
	
	// Set panel target group
	$panelContainer = HTML::select(".sectionbody #".$section)->item(0);
	$page->setNavigationGroup($panelContainer, "navGroup");
	
	// Avoid empty modules
	if (empty($innerModules[$iModuleID]))
		continue;
	
	// Get module container
	$attr = array();
	$attr['id'] = $profileID;
	$attr['name'] = $profileName;
	$mContainer = $page->getModuleContainer($innerModules[$iModuleID], $viewName = "", $attr, $startup = TRUE, $containerID = $ref, $loading = TRUE, $preload = TRUE);
	$page->setNavigationGroup($mContainer, "navGroup");
	HTML::append($sectionbody, $mContainer);
}

// Return output
return $page->getReport();
//#section_end#
?>