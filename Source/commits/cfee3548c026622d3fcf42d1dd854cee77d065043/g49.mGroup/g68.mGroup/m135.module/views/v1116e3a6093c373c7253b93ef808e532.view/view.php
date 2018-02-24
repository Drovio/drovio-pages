<?php
//#section#[header]
// Module Declaration
$moduleID = 135;

// Inner Module Codes
$innerModules = array();
$innerModules['appExplorer'] = 172;
$innerModules['literalManager'] = 175;

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Comm\database\connections\interDbConnection;
use \API\Developer\appcenter\application;
use \API\Developer\appcenter\appManager;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\url;
use \API\Security\account;
use \UI\Html\HTMLModulePage;
use \UI\Presentation\gridSplitter;
use \INU\Developer\redWIDE;

// Create Module Page
$page = new HTMLModulePage("OneColumnFullscreen");
$actionFactory = $page->getActionFactory();

// Get Application name
$appID = $_GET['id'];

if (!isset($appID))
{
	// Application id doesn't exist, return to home page
	return $actionFactory->getReportRedirect("/", "apps");
}

// Open Application
appManager::openApplication($appID);

// Validate and get application info
$application = appManager::getApplicationData($appID);
if (is_null($application))
{
	// Close Application
	appManager::closeApplication($appID);
	
	// Return Error Page
	$page->build("Application Author Error", "applicationEditor");
	return $page->getReport();
}

// Build the module
$pageName = (empty($application['name']) ? $application['fullName'] : $application['name']);
$page->build($pageName, "applicationEditor");

// _____ Toolbar Navigation

// Action Attributes
$attr = array();
$attr['appID'] = $appID;

// Info
$subItem = $page->addToolbarNavItem("infoSub", $title = "", $class = "devapp info", NULL);
$actionFactory->setPopupAction($subItem, $moduleID, "appInfo", $attr);

// Settings
$subItem = $page->addToolbarNavItem("settingsSub", $title = "", $class = "devapp settings", NULL);
$actionFactory->setPopupAction($subItem, $moduleID, "appSettings", $attr);

// Version Control
$title = moduleLiteral::get($moduleID, "lbl_navVCS");
$subItem = $page->addToolbarNavItem("vcsSub", $title, $class = "devapp vcs", NULL);
$actionFactory->setPopupAction($subItem, $moduleID, "vcsCommitManager", $attr);

// Literal Manager
$subItem = $page->addToolbarNavItem("literalSub", $title = "", $class = "devapp literals", NULL);
$actionFactory->setPopupAction($subItem, $innerModules['literalManager'], "", $attr);

// Media Manager
$subItem = $page->addToolbarNavItem("mediaSub", $title = "", $class = "devapp media", NULL);
$actionFactory->setModuleAction($subItem, $moduleID, "mediaManager", "", $attr);

// Application Publisher
$subItem = $page->addToolbarNavItem("pubSub", $title = "", $class = "devapp publisher", NULL);
$actionFactory->setPopupAction($subItem, $moduleID, "appPublisher", $attr);

// Run Application (with testing)
$title = moduleLiteral::get($moduleID, "lbl_navRunApp");
$subItem = $page->addToolbarNavItem("playSub", $title, $class = "devapp run", NULL);
$url = url::resolve("apps", "/application.php");
$params = array();
$params['id'] = $appID;
$url = url::get($url, $params);
NavigatorProtocol::web($subItem, $url, "_blank");


// Build Main Content
$splitter = new gridSplitter();
$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_LEFT, $closed = FALSE, "Explorer")->get();
$page->appendToSection("mainContent", $viewer);

// redWIDE
$wide = new redWIDE();
$ajaxWide = $wide->build()->get();
$splitter->appendToMain($ajaxWide);


// Sidebar Application Section Viewer
$appSectionViewer = DOM::create("div", "", "appSectionViewer");
$splitter->appendToSide($appSectionViewer);

$attr = array();
$attr['appID'] = $appID;
$viewerContainer = HTMLModulePage::getModuleContainer($innerModules['appExplorer'], "", $attr);
DOM::append($appSectionViewer, $viewerContainer);


// Return output
return $page->getReport();
//#section_end#
?>