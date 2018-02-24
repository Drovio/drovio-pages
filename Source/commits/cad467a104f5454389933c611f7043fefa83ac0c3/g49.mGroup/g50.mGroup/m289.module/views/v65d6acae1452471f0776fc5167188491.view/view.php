<?php
//#section#[header]
// Module Declaration
$moduleID = 289;

// Inner Module Codes
$innerModules = array();
$innerModules['devHome'] = 100;

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
importer::import("BSS", "WebDocs");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Profile\account;
use \API\Profile\team;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "docViewerPage", TRUE);

$targetcontainer = "sideMenu";
$targetgroup = "sideGroup";
$navgroup = "topNavGroup";

// Set top navigation
$navItem = HTML::select(".docViewer .topNav .hdNav")->item(0);
$page->setStaticNav($navItem, "genericGroup", $targetcontainer, $targetgroup, $navgroup, $display = "none");

$navItem = HTML::select(".docViewer .topNav .hdNav")->item(1);
$page->setStaticNav($navItem, "sdkGroup", $targetcontainer, $targetgroup, $navgroup, $display = "none");

$container = HTML::select("#genericGroup")->item(0);
$page->setNavigationGroup($container, $targetgroup);

$container = HTML::select("#sdkGroup")->item(0);
$page->setNavigationGroup($container, $targetgroup);


// Get selected name
$selectedDoc = $_GET['name'];


// Set actions
$actions = array();

// App Development
$actions["apps"] = "Public:Developer/Apps:GettingStarted";
$actions["files"] = "Public:Developer/Apps:AppStorage";
$actions["manifest"] = "Public:Developer/Apps:AppManifest";
$actions["premium"] = "Public:Developer/Apps:PremiumSDK";
$actions["app_center"] = "Public:Developer/Apps:ApplicationCenter";

// Enterprise Development
$actions["relations_sdk"] = "Public:Developer/Enterprise:RelationsSDK";
$actions["retail_sdk"] = "Public:Developer/Enterprise:RetailSDK";
$actions["app_store"] = "Public:Developer/Enterprise:AppStore";

// Website Development
$actions["website"] = "Public:Developer/Websites:GettingStarted";
//$actions["template"] = "Public:Developer/Guides:WebTemplate";
//$actions["extension"] = "Public:Developer/Guides:WebExtension";

// APIs
$actions["platform_api"] = "Public:Developer/APIs:Platform";
$actions["profile_api"] = "Public:Developer/APIs:Profile";
$actions["comm_api"] = "Public:Developer/APIs:Communications";
$actions["geoloc_api"] = "Public:Developer/APIs:Geoloc";

$actions["html_ui_api"] = "Public:Developer/APIs/UI:Html";
$actions["content_ui_api"] = "Public:Developer/APIs/UI:Content";
$actions["forms_ui_api"] = "Public:Developer/APIs/UI:Forms";
$actions["presentation_ui_api"] = "Public:Developer/APIs/UI:Presentation";
$actions["navigation_ui_api"] = "Public:Developer/APIs/UI:Navigation";
$actions["dev_ui_api"] = "Public:Developer/APIs/UI:Developer";

$actions["literals_api"] = "Public:Developer/APIs:Literals";
$actions["bugs_api"] = "Public:Developer/APIs:BugTracker";
$actions["analytics_api"] = "Public:Developer/APIs:Analytics";

$actions["web_docs"] = "Public:Developer/APIs/Other:WebDocs";

$actions["projects_api"] = "Developer/APIs:Projects";
$actions["version_api"] = "Developer/APIs:VersionControl";
$actions["security_api"] = "Developer/APIs:Security";

$actions["advanced_intro"] = "Developer:Basics";
$actions["advanced_structure"] = "Developer:Structure";
$actions["advanced_modules"] = "Developer/Projects:Modules";
$actions["advanced_security"] = "System/Security:Modules";

// Platform
$actions["intro"] = "Public:Developer/Basics:Introduction";
$actions["basics"] = "Public:Developer/Basics:devBasics";
$actions["essentials"] = "Public:Developer/Basics:devEssentials";
$actions["mvc"] = "Public:Developer/Basics:devMVC";
$actions["platform"] = "Public:Developer/Basics:Platform";
$actions["projects"] = "Public:Developer/Basics:devProjects";
$actions["hints"] = "Public:Developer/Basics:devProjectHints";
$actions["version"] = "Public:Developer/Basics:devVersion";
$actions["architecture"] = "Public:Developer/Platform:Architecture";

// Community
$actions["community"] = "Public:Developer/Community:devCommunity";
$actions["shared_projects"] = "Public:Developer/Community:sharedProjects";
$actions["open_projects"] = "Public:Developer/Community:openProjects";
$actions["translators"] = "Public:Developer/Community:translations";

foreach ($actions as $action => $document)
{
	// Get document info
	$docParts = explode(":", $document);
	$startIndex = 0;
	$publicDoc = 0;
	if (count($docParts) == 3)
	{
		$publicDoc = 1;
		unset($docParts[0]);
		$startIndex = 1;
	}
	$docFolder = $docParts[$startIndex];
	$docName = $docParts[$startIndex+1];
	
	// Get item
	$navItem = HTML::select(".docViewer .sideMenu .navItem.".$action)->item(0);
	if (empty($navItem))
		continue;
	HTML::removeClass($navItem, $action);
	
	// Set selected
	if ($action == $selectedDoc)
	{
		// Set selected item
		HTML::addClass($navItem, "selected");
		
		// Set document attributes
		$_GET['public'] = $publicDoc;
		$_GET['f'] = $docFolder;
		$_GET['doc'] = $docName;
	}
	
	// Set static nav
	$page->setStaticNav($navItem, "", "", "", "navGroup", $display = "none");
	
	// Set weblink navigation
	$navWeblink = HTML::select("a", $navItem)->item(0);
	$url = url::resolve("developers", "/docs/".$action);
	DOM::attr($navWeblink, "href", $url);
	DOM::attr($navWeblink, "target", "_self");
	
	// Set module action
	$attr = array();
	$attr['public'] = $publicDoc;
	$attr['f'] = $docFolder;
	$attr['doc'] = $docName;
	$actionFactory->setModuleAction($navWeblink, $moduleID, "docViewer", ".docViewer .contentHolder .docHolder", $attr);
}

// Remove advanced items if not in team 6 (Redback)
$teamID = team::getTeamID();
if ($teamID != 6)
{
	$advanced = HTML::select(".docViewer .sideMenu .advanced");
	foreach ($advanced as $advancedItem)
		HTML::replace($advancedItem, NULL);
}

// Load start screen
$contentHolder = HTML::select(".docViewer .middleContainer .contentHolder")->item(0);
$startScreen = $page->loadView($moduleID, "startScreen");
DOM::append($contentHolder, $startScreen);


if (!empty($selectedDoc))
{
	$docContainer = HTML::select(".docViewer .middleContainer .contentHolder .docHolder")->item(0);
	HTML::innerHTML($docContainer, "");
	$docViewer = $page->loadView($moduleID, "docViewer");
	DOM::append($docContainer, $docViewer);
}

// Check account and remove dashboard button
if (!account::validate())
{
	$dashboardButton = HTML::select(".sidebar .myDashboard")->item(0);
	HTML::replace($dashboardButton, NULL);
}

// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['devHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Return output
return $page->getReport();
//#section_end#
?>