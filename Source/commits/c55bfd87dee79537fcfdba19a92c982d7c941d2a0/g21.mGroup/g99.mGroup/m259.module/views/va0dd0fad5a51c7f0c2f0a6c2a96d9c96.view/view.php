<?php
//#section#[header]
// Module Declaration
$moduleID = 259;

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
importer::import("API", "Connect");
importer::import("API", "Literals");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Connect\invitations;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;

$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "myRelationsPage", TRUE);

// Get selected tab
$selectedSection = engine::getVar("section");
$selectedSection = (empty($selectedSection) ? "teams" : $selectedSection);

// Set static navigation
$items = array();
$items["teams"] = "myTeams";
$items["rel"] = "";
$items["collab"] = "";
$items["invitations"] = "myInvitations";
foreach ($items as $section => $viewName)
{
	// Get navigation item
	$navItem = HTML::select(".myRelations .navItem.".$section)->item(0);
	$page->setStaticNav($navItem, $section, "relationsContainer", "relGroup", "rNavGroup", $display = "none");
	
	// Set selected item
	if ($section == $selectedSection)
		HTML::addClass($navItem, "selected");
	
	// Get target
	$target = HTML::select(".myRelations #".$section)->item(0);
	$page->setNavigationGroup($target, "relGroup");
	
	// Load module
	if (!empty($viewName))
	{
		$mContainer = $page->getModuleContainer($moduleID, $viewName, $attr, $startup = TRUE, $containerID = "", $loading = FALSE, $preload = TRUE);
		DOM::append($target, $mContainer);
	}
}


// Get pending invitations
$invitations = invitations::getAccountInvitations();

// Set invitation title
if (count($invitations) > 0)
{
	$attr = array();
	$attr['count'] = count($invitations);
	$title = moduleLiteral::get($moduleID, "lbl_nav_invitations", $attr);
}
else
	$title = moduleLiteral::get($moduleID, "lbl_nav_invitations_no_count", $attr);
	
$navItem = HTML::select(".myRelations .navItem.invitations")->item(0);
DOM::append($navItem, $title);

	
return $page->getReport();
//#section_end#
?>