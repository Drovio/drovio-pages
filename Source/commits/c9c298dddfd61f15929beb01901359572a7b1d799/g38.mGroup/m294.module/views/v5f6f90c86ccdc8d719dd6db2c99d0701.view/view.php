<?php
//#section#[header]
// Module Declaration
$moduleID = 294;

// Inner Module Codes
$innerModules = array();
$innerModules['frontend'] = 70;

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
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("ESS", "Environment");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \ESS\Environment\url;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \UI\Modules\MPage;

// Build Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "helpDocViewerPage", TRUE);


// Get selected name
$selectedDoc = (empty($_GET['name']) ? "start" : $_GET['name']);


// Set actions
$actions = array();
$actions["start"] = "Public:HelpCenter:GetStarted";
$actions["account"] = "Public:HelpCenter/accounts:securityAccounts";
$actions["privacy"] = "Public:HelpCenter/accounts:Privacy";
$actions["business"] = "Public:HelpCenter/Enterprise:GetStarted";
$actions["dev"] = "Public:HelpCenter/Developer:Framework";
$actions["reporting"] = "Public:HelpCenter:Reporting";
$actions["what_new"] = "Public:HelpCenter:WhatsNew";
$actions["social"] = "Public:HelpCenter:Social";
$actions["international"] = "Public:HelpCenter:International";

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
	$navItem = HTML::select(".helpDocViewer .sideMenu .navItem.".$action)->item(0);
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
	$url = url::resolve("www", "/help/doc.php");
	$params = array();
	$params['name'] = $action;
	$url = url::get($url, $params);
	NavigatorProtocol::web($navWeblink, $url, "_self");
	
	// Set module action
	$attr = array();
	$attr['public'] = $publicDoc;
	$attr['f'] = $docFolder;
	$attr['doc'] = $docName;
	$actionFactory->setModuleAction($navWeblink, $moduleID, "docViewer", ".helpDocViewer .docHolder", $attr);
}

// Load initial document viewer
$docContainer = HTML::select(".helpDocViewer .middleContainer .docHolder")->item(0);
$docViewer = module::loadView($moduleID, "docViewer");
DOM::append($docContainer, $docViewer);


// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['frontend'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$discoverPage = HTML::select(".helpDocViewer")->item(0);
$footerMenu = module::loadView($innerModules['frontend'], "footerMenu");
DOM::append($discoverPage, $footerMenu);

return $page->getReport();
//#section_end#
?>