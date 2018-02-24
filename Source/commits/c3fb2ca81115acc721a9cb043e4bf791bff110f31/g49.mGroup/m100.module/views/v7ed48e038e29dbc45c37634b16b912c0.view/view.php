<?php
//#section#[header]
// Module Declaration
$moduleID = 100;

// Inner Module Codes
$innerModules = array();
$innerModules['developerDoc'] = 398;

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
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "docMenuContainer", TRUE);

// Get if there is a selected doc name
$selectedDocName = engine::getVar("doc_name");

// Initialize menu
$menu = array();

// Set sdk api
$menu["sdk_dom_html"] = "APIs/UI/Html";
$menu["sdk_forms"] = "APIs/UI/Forms";
$menu["sdk_content"] = "APIs/UI/Content";
$menu["sdk_presentation"] = "APIs/UI/Presentation";
$menu["sdk_navigation"] = "APIs/UI/Navigation";
$menu["sdk_developer"] = "APIs/UI/Developer";
$menu["sdk_platform"] = "APIs/Platform";
$menu["sdk_profile"] = "APIs/Profile";
$menu["sdk_comm"] = "APIs/Communications";
$menu["sdk_geoloc"] = "APIs/Geoloc";
$menu["sdk_literals"] = "APIs/Literals";
$menu["sdk_wdocs"] = "APIs/Other/WebDocs";
$menu["sdk_issues"] = "APIs/BugTracker";
$menu["sdk_analytics"] = "APIs/Analytics";

// Set Tutorials
$menu['tutorial_helloworld'] = "Tutorials/HelloWorld";

// Set App Development
$menu['apps_gettingstarted'] = "Apps/GettingStarted";
$menu['apps_storage'] = "Apps/AppStorage";
$menu['apps_permissions'] = "Apps/AppManifest";
$menu['apps_premium'] = "Apps/PremiumSDK";
$menu['apps_appcenter'] = "Apps/ApplicationCenter";

// Set Projects
$menu['projects_intro'] = "Basics/devProjects";
$menu['projects_hints'] = "Basics/devProjectHints";
$menu['projects_vcs'] = "Basics/devVersion";

// Set api menu
$menu['api_protocol'] = "APIs/public/ProtocolArchitecture";
$menu['api_account'] = "APIs/public/LoginAPI";
$menu['api_application'] = "APIs/public/ConnectApps";

// Set platform menu
$menu['platform_basics'] = "Basics/devBasics";
$menu['platform_essentials'] = "Basics/devEssentials";
$menu['platform_mvc'] = "Basics/devMVC";
$menu['platform_programming'] = "Basics/Platform";
$menu['platform_architecture'] = "Platform/Architecture";

// Set community menu
$menu['community_intro'] = "Community/devCommunity";
$menu['community_translations'] = "Community/translations";

// Show all menus
foreach ($menu as $menuClass => $docName)
{
	// Find weblink
	$weblink = HTML::select(".docMenu ul.menu .menu-item.".$menuClass." a")->item(0);
	
	// Set selected
	if ($selectedDocName == $docName)
		HTML::addClass($weblink, "selected");
	
	// Set href
	$href = url::resolve("developers", "/docs/".$docName);
	HTML::attr($weblink, "href", $href);
	
	// Set static nav
	$pageContent->setStaticNav($weblink, $ref = "", $targetcontainer = "", $targetgroup = "", $navgroup = "sdgroup", $display = "none");
	
	// Set action
	$attr = array();
	$attr['doc_name'] = $docName;
	$actionFactory->setModuleAction($weblink, $innerModules['developerDoc'], "", ".docContainer", $attr);
}

if (!empty($selectedDocName))
{
	// Load document viewer
	$docContainer = HTML::select(".docContainer")->item(0);
	$document = $pageContent->loadView($innerModules['developerDoc']);
	DOM::append($docContainer, $document);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>