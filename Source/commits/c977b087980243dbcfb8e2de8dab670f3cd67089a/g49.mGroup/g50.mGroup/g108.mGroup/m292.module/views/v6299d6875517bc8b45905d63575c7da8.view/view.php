<?php
//#section#[header]
// Module Declaration
$moduleID = 292;

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
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \ESS\Environment\url;
use \API\Profile\team;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "sdkDocViewerPage", TRUE);

// Get selected document name
$selectedDoc = engine::getVar('doc');

// Set actions
$actions = array();
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
	$navItem = HTML::select(".sdkDocViewer .sideMenu .navItem.".$action)->item(0);
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
	$url = url::resolve("developer", "/docs/sdk/".$action);
	NavigatorProtocol::web($navWeblink, $url, "_self");
	
	// Set module action
	$attr = array();
	$attr['public'] = $publicDoc;
	$attr['f'] = $docFolder;
	$attr['doc'] = $docName;
	$actionFactory->setModuleAction($navWeblink, $moduleID, "docViewer", ".sdkDocViewer .docHolder", $attr);
}

// Remove advanced items if not in team 6 (Redback)
$teamID = team::getTeamID();
if ($teamID != 6)
{
	$advanced = HTML::select(".sdkDocViewer .sideMenu li.advanced");
	foreach ($advanced as $advancedItem)
		HTML::replace($advancedItem, NULL);
}

// Load initial selected document
if (!empty($selectedDoc))
{
	$docContainer = HTML::select(".sdkDocViewer .docHolder")->item(0);
	$docViewer = module::loadView($moduleID, "docViewer");
	DOM::append($docContainer, $docViewer);
}

// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['devHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$footerContainer = HTML::select(".sdkDocViewer")->item(0);
$footerMenu = module::loadView($innerModules['devHome'], "footerMenu");
DOM::append($footerContainer, $footerMenu);

// Return output
return $page->getReport();
//#section_end#
?>