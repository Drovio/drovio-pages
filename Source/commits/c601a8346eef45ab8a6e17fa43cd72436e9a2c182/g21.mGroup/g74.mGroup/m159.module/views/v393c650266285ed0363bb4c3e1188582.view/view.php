<?php
//#section#[header]
// Module Declaration
$moduleID = 159;

// Inner Module Codes
$innerModules = array();
$innerModules['generalSettings'] = 160;
$innerModules['securitySettings'] = 161;
$innerModules['keySettings'] = 317;
$innerModules['managedAccounts'] = 328;
$innerModules['personalSettings'] = 160;
$innerModules['accountSettings'] = 329;
$innerModules['profilePage'] = 367;

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
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \UI\Modules\MPage;

$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "mySettingsPage", TRUE);

// Get account information
$accountInfo = account::info();

// Set image
if (isset($accountInfo['profile_image_url']))
{
	$accountImg = HTML::select(".back .image")->item(0);
	$img = DOM::create("img");
	DOM::attr($img, "src", $accountInfo['profile_image_url']);
	DOM::append($accountImg, $img);
}

// Set title
$accountTitle = HTML::select(".back .title")->item(0);
HTML::innerHTML($accountTitle, $accountInfo['accountTitle']);

// Set back url and action
$accountName = $accountInfo['username'];
if (!empty($accountName))
	$url = url::resolve("www", "/profile/".$accountName);
else
{
	$params = array();
	$params['id'] = $accountInfo['id'];
	$url = url::resolve("www", "/profile/index.php", $params);
}
$backA = HTML::select(".back a")->item(0);
HTML::attr($backA, "href", $url);
$attr = array();
$attr['id'] = $accountInfo['id'];
$attr['name'] = $accountName;
$actionFactory->setModuleAction($backA, $innerModules['profilePage'], "", "", $attr);

$settings = array();
$settings['personal'] = "personalSettings";
$settings['account'] = "accountSettings";
$settings['security'] = "securitySettings";
$settings['managed'] = "managedAccounts";
$settings['keys'] = "keySettings";

// Set menu actions
foreach ($settings as $class => $moduleRefID)
{
	$menuItem = HTML::select(".mySettingsPage .side_menu .menu_item.".$class)->item(0);
	$actionFactory->setModuleAction($menuItem, $innerModules[$moduleRefID], "", ".settingsContainer", array(), $loading = TRUE);
	$page->setStaticNav($menuItem, "", "", "sideNavGroup", $display = "none");
}


// Check if account is locked and remove personal settings
if (account::isLocked())
{
	$psets = HTML::select(".mySettings .pset");
	foreach ($psets as $pset)
		HTML::replace($pset, NULL);
}

// Check if account is managed and remove account settings
if (!account::isAdmin())
{
	$asets = HTML::select(".mySettings .aset");
	foreach ($asets as $aset)
		HTML::replace($aset, NULL);
}

// Set initial startup settings
$selectedMenu = engine::getVar('menu');
if (!account::isLocked())
	$selectedMenu = (empty($selectedMenu) ? "personal" : $selectedMenu);
else if (account::isAdmin())
	$selectedMenu = (empty($selectedMenu) || $selectedMenu == "personal" ? "account" : $selectedMenu);
else
	$selectedMenu = "keys";

// Set startup selected menu item
$menu_item = HTML::select(".menu_item.".$selectedMenu)->item(0);
HTML::addClass($menu_item, "selected");

// Initialize settings
$settingsContainer = HTML::select(".settingsContainer")->item(0);
$personalContainer = $page->getModuleContainer($innerModules[$settings[$selectedMenu]], $action = "", $attr = array(), $startup = TRUE, $containerID = "", $loading = FALSE, $preload = TRUE);
DOM::append($settingsContainer, $personalContainer);


return $page->getReport();
//#section_end#
?>