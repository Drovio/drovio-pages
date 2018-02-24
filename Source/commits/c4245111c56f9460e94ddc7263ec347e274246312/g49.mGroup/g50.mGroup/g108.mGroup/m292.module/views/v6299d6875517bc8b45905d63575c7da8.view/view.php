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
importer::import("API", "Profile");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("SYS", "Resources");
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
$page->build($title, "SDKHomePage", TRUE);



// Get selected name
$selectedDoc = (empty($_GET['name']) ? "intro" : $_GET['name']);


// Set actions
$actions = array();
$actions["sdk_intro"] = "";
$actions["red_sdk"] = "";
$actions["web_sdk"] = "";

$actions["platform_api"] = "";
$actions["profile_api"] = "";
$actions["comm_api"] = "";
$actions["ui_api"] = "";
$actions["projects_api"] = "";
$actions["geoloc_api"] = "";

$actions["version_api"] = "";
$actions["security_api"] = "";

$actions["literals_api"] = "";
$actions["bugs_api"] = "";
$actions["analytics_api"] = "";

$actions["web_docs"] = "";

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
	$url = url::resolve("developer", "/docs/sdk/doc.php");
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

// Remove advanced items if not in team 1 (Redback Company, Skyworks)
$teamID = team::getTeamID();
if ($teamID != 1)
{
	$advanced = HTML::select(".docViewer .sideMenu li.advanced");
	foreach ($advanced as $advancedItem)
		HTML::replace($advancedItem, NULL);
}


/*
// Load initial document viewer
$docContainer = HTML::select(".docViewer .middleContainer .docHolder")->item(0);
$docViewer = module::loadView($moduleID, "docViewer");
DOM::append($docContainer, $docViewer);
*/
// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['devHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Return output
return $page->getReport();
//#section_end#
?>