<?php
//#section#[header]
// Module Declaration
$moduleID = 76;

// Inner Module Codes
$innerModules = array();

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

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

// Build Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build page content
$pageContent->build("", "help-sidebar-container", TRUE);

// Get if there is a selected doc name
$selectedDocName = engine::getVar("doc_name");

// Initialize menu
$menu = array();

// Set welcome docs
$menu["welcome"] = "Welcome";
$menu["gettinstarted"] = "GetStarted";
$menu['whatsnew'] = "WhatsNew";
$menu['reporting'] = "Reporting";
$menu['branding'] = "Branding";

// Set account docs
$menu['acc_security'] = "accounts/securityAccounts";
$menu['acc_privacy'] = "accounts/Privacy";

// Set enterprise docs
$menu['enterprise_start'] = "Enterprise/GetStarted";

// Set developer docs
$menu['developer_start'] = "Developer/Framework";

// Set policy docs
$menu["policies_terms"] = "Policies/Terms";
$menu["policies_privacy"] = "Policies/Privacy";
$menu["policies_acceptableuse"] = "Policies/AcceptableUse";


// Show all menus
foreach ($menu as $menuClass => $docName)
{
	// Find weblink
	$weblink = HTML::select(".help-sidebar-container .sidebar ul.menu .menu-item.".$menuClass." a")->item(0);
	
	// Set selected
	if ($selectedDocName == $docName)
		HTML::addClass($weblink, "selected");
	
	// Set href
	$href = url::resolve("www", "/help/".$docName);
	HTML::attr($weblink, "href", $href);
	
	// Set static nav
	$pageContent->setStaticNav($weblink, $ref = "", $targetcontainer = "", $targetgroup = "", $navgroup = "hlpgroup", $display = "none");
	
	// Set action
	$attr = array();
	$attr['doc_name'] = $docName;
	$actionFactory->setModuleAction($weblink, $moduleID, "helpDocViewer", ".docContainer", $attr);
}

if (!empty($selectedDocName))
{
	// Load document viewer
	$docContainer = HTML::select(".docContainer")->item(0);
	$document = $pageContent->loadView($moduleID, "helpDocViewer");
	DOM::append($docContainer, $document);
}
return $pageContent->getReport();
//#section_end#
?>