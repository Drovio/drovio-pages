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
importer::import("ESS", "Protocol");
importer::import("SYS", "Resources");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \SYS\Resources\url;
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
NavigatorProtocol::staticNav($navItem, "genericGroup", $targetcontainer, $targetgroup, $navgroup, $display = "none");

$navItem = HTML::select(".docViewer .topNav .hdNav")->item(1);
NavigatorProtocol::staticNav($navItem, "sdkGroup", $targetcontainer, $targetgroup, $navgroup, $display = "none");

$container = HTML::select("#genericGroup")->item(0);
NavigatorProtocol::selector($container, $targetgroup);

$container = HTML::select("#sdkGroup")->item(0);
NavigatorProtocol::selector($container, $targetgroup);


// Get selected name
$selectedDoc = $_GET['name'];


// Set actions
$actions = array();
$actions["intro"] = "Public:Developer/Basics:Introduction";
$actions["basics"] = "Public:Developer/Basics:devBasics";
$actions["essentials"] = "Public:Developer/Basics:devEssentials";
$actions["mvc"] = "Public:Developer/Basics:devMVC";
$actions["platform"] = "Public:Developer/Basics:Platform";
$actions["projects"] = "Public:Developer/Basics:devProjects";
$actions["hints"] = "Public:Developer/Basics:devProjectHints";
$actions["version"] = "Public:Developer/Basics:devVersion";

$actions["apps"] = "Public:Developer/Guides:Application";
$actions["website"] = "Public:Developer/Guides:Website";
$actions["template"] = "Public:Developer/Guides:WebTemplate";
$actions["extension"] = "Public:Developer/Guides:WebExtension";
$actions["market"] = "Public:Developer/Guides:Market";

$actions["advanced_intro"] = "Developer:Basics";
$actions["advanced_structure"] = "Developer:Structure";
$actions["advanced_modules"] = "Developer/Projects:Modules";
$actions["advanced_security"] = "System/Security:Modules";

$actions["architecture"] = "Public:Developer/Platform:Architecture";

$actions["community"] = "Public:Developer/Community:devCommunity";
$actions["shared_projects"] = "Public:Developer/Community:sharedProjects";
$actions["open_projects"] = "Public:Developer/Community:openProjects";
$actions["translators"] = "Public:Developer/Community:translations";

$actions["sdk_intro"] = "Public:Developer/Engine:sdkIntroduction";
$actions["red_sdk"] = "Public:Developer/Engine:coreSDK";
$actions["web_sdk"] = "Public:Developer/Engine:webSDK";
$actions["rap"] = "Public:Developer/Engine:RAProtocol";

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
	NavigatorProtocol::staticNav($navItem, "", "", "", "navGroup", $display = "none");
	
	// Set weblink navigation
	$navWeblink = HTML::select("a", $navItem)->item(0);
	$url = url::resolve("developer", "/docs/index.php");
	$params = array();
	$params['name'] = $action;
	$url = url::get($url, $params);
	NavigatorProtocol::web($navWeblink, $url, "_self");
	
	// Set module action
	$attr = array();
	$attr['public'] = $publicDoc;
	$attr['f'] = $docFolder;
	$attr['doc'] = $docName;
	$actionFactory->setModuleAction($navWeblink, $moduleID, "docViewer", ".docViewer .docHolder", $attr);
}

// Remove advanced items if not in team 6 (Redback)
$teamID = team::getTeamID();
if ($teamID != 6)
{
	$advanced = HTML::select(".docViewer .sideMenu li.advanced");
	foreach ($advanced as $advancedItem)
		HTML::replace($advancedItem, NULL);
}



// Load initial document viewer
$docContainer = HTML::select(".docViewer .middleContainer .docHolder")->item(0);
if (empty($selectedDoc))
{
	$startScreen = module::loadView($moduleID, "startScreen");
	DOM::append($docContainer, $startScreen);
}
else
{
	$docViewer = module::loadView($moduleID, "docViewer");
	DOM::append($docContainer, $docViewer);
}

// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['devHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$discoverPage = HTML::select(".docViewer")->item(0);
$footerMenu = module::loadView($innerModules['devHome'], "footerMenu");
DOM::append($discoverPage, $footerMenu);

// Return output
return $page->getReport();
//#section_end#
?>