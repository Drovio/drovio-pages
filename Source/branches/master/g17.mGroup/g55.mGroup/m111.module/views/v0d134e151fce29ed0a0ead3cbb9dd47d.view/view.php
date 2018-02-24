<?php
//#section#[header]
// Module Declaration
$moduleID = 111;

// Inner Module Codes
$innerModules = array();
$innerModules['layoutObjectEditor'] = 110;

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
importer::import("API", "Literals");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Layout");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Layout\pageLayout;
use \UI\Modules\MContent;
use \UI\Navigation\treeView;
use \UI\Navigation\navigationBar;
use \UI\Presentation\tabControl;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContainer = $pageContent->build("", "layoutExplorer", TRUE)->get();


// Manager toolbar
$navBar = new navigationBar();
$navigationToolbar = $navBar->build($dock = "T", $pageContainer)->get();
$pageContent->append($navigationToolbar);

// Refresh Tool
$navTool = DOM::create("span", "", "ltRefresh", "ltNavTool refresh");
$navBar->insertToolbarItem($navTool);

// Create TabControl
$layoutTabControl = new tabControl();
$layoutTabWrapper = $layoutTabControl->build($id = "layoutTabber", TRUE)->get();

$pageContent->append($layoutTabWrapper);

// If user in Redback Developer Group
$selected = TRUE;
if (TRUE)
{
	//Tree View
	$navTree = new treeView();
	$navTreeElement = $navTree->build('', '', TRUE)->get();
	
	$sysLayoutManager = new pageLayout("system");
	$sysLayouts = $sysLayoutManager->getAllLayouts();
	foreach($sysLayouts as $layout)
	{
		$item = DOM::create("div");
		$content = DOM::create('span', $layout);
		DOM::append($item, $content);
		$attr = array();
		$attr['category'] = "system";
		$attr['name'] = $layout;
		$actionFactory->setModuleAction($item, $innerModules['layoutObjectEditor'], "", "", $attr);
		$treeItem = $navTree->insertItem('', $item);
	}
	
	// Insert tab
	$header = moduleLiteral::get($moduleID, "hdr_globalLayouts");
	$layoutTabControl->insertTab("global", $header, $navTreeElement, $selected);
	$selected = FALSE;
}

// If user in Web Developer group
if (TRUE)
{
	//Tree View
	$navTree = new treeView();
	$navTreeElement = $navTree->build('', '', TRUE)->get();
	
	$ebldLayoutManager = new pageLayout("ebuilder");
	$ebldLayouts = $ebldLayoutManager->getAllLayouts();
	foreach($ebldLayouts as $layout)
	{
		$item = DOM::create("div");
		$content = DOM::create('span', $layout);
		DOM::append($item, $content);
		$attr = array();
		$attr['category'] = "ebuilder";
		$attr['name'] = $layout;
		$actionFactory->setModuleAction($item, $innerModules['layoutObjectEditor'], "", "", $attr);
		$treeItem = $navTree->insertItem('', $item);
	}
	
	// Insert tab
	$header = moduleLiteral::get($moduleID, "hdr_webLayouts");
	$layoutTabControl->insertTab("web", $header, $navTreeElement, $selected);
}


return $pageContent->getReport();
//#section_end#
?>