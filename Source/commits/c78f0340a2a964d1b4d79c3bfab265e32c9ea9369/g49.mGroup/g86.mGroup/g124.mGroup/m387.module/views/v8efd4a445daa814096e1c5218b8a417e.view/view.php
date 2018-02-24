<?php
//#section#[header]
// Module Declaration
$moduleID = 387;

// Inner Module Codes
$innerModules = array();
$innerModules['pages'] = 388;
$innerModules['themes'] = 389;
$innerModules['themes_css'] = 391;
$innerModules['themes_js'] = 390;

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
importer::import("DEV", "WebTemplates");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Navigation\toolbarComponents\toolbarMenu;
use \UI\Navigation\treeView;
use \DEV\WebTemplates\templateProject;
use \DEV\WebTemplates\templateTheme;

// Create page content Object
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Initialize Template
$templateID = engine::getVar('id');
$templateName = engine::getVar('name');
$devTemplate = new templateProject($templateID, $templateName);
$templateInfo = $devTemplate->info();
	
// Get project data
$templateID = $templateInfo['id'];

// Build page
$pageContainer = $pageContent->build("", "templateExplorer")->get();


// Manager toolbar
$codeMgrToolbar = new navigationBar();
$codeMgrToolbar->build($dock = "T", $pageContainer);
$pageContent->append($codeMgrToolbar->get());

// Refresh Tool
$navTool = DOM::create("span", "", "TRefresh", "tplNavTool refresh");
$codeMgrToolbar->insertToolbarItem($navTool);

// Create menu
$tMenu = new toolbarMenu();
$menuItem = $tMenu->build("", "", "tplNavTool create_new")->get();
$codeMgrToolbar->insertTool($menuItem);

// Add create menu items
$attr = array();
$attr['id'] = $templateID;

// New Page
$title = moduleLiteral::get($moduleID, "lbl_createPageTitle");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $innerModules['pages'], "createNewPage", "", $attr);

// New Theme
$title = moduleLiteral::get($moduleID, "lbl_createThemeTitle");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $innerModules['themes'], "createNewTheme", "", $attr);

// New Theme css
$title = moduleLiteral::get($moduleID, "lbl_createThemeCssTitle");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $innerModules['themes_css'], "addCSS", "", $attr);

// New Theme js
$title = moduleLiteral::get($moduleID, "lbl_createThemeJsTitle");
$menuItem = $tMenu->insertMenuItem($title, $id = "");
$actionFactory->setModuleAction($menuItem, $innerModules['themes_js'], "addJS", "", $attr);


// Application Views Tree
$navTree = new treeView();
$navTreeElement = $navTree->build("tplEplorerTree")->get();
$pageContent->append($navTreeElement);

// Create pages item
$item = DOM::create("div", "Pages", "", "wstpl_p");
$itemIco = DOM::create("span", "", "", "contentIcon libIco");
DOM::prepend($item, $itemIco);
$treeItem = $navTree->insertExpandableTreeItem($itemID = "_pages", $item);
$navTree->assignSortValue($treeItem, "._pages");

// List all pages
$pages = $devTemplate->getPages();
foreach ($pages as $pageName)
{
	// Create group item container
	$item = DOM::create("div", $pageName.".page", "", "wstpl_pp");
	$itemIco = DOM::create("span", "", "", "contentIcon flIco");
	DOM::prepend($item, $itemIco);
	
	$itemID = substr(hash("md5", $pageName), 0, 10);
	$treeItem = $navTree->insertTreeItem($itemID, $item, $parentID = "_pages");
	$navTree->assignSortValue($treeItem, $pageName);
	
	// Set action item
	$attr = array();
	$attr['id'] = $templateID;
	$attr['name'] = $pageName;
	$actionFactory->setModuleAction($treeItem, $innerModules['pages'], "editPage", "", $attr, TRUE);
}

// Create pages item
$item = DOM::create("div", "Themes", "", "wstpl_th");
$itemIco = DOM::create("span", "", "", "contentIcon libIco");
DOM::prepend($item, $itemIco);
$treeItem = $navTree->insertExpandableTreeItem($itemID = "_themes", $item);

// List all themes
$themes = $devTemplate->getThemes();
foreach ($themes as $themeName)
{
	// Create group item container
	$item = DOM::create("div", $themeName.".theme", "", "wstpl_pthm");
	$itemIco = DOM::create("span", "", "", "contentIcon pkgIco");
	DOM::prepend($item, $itemIco);
	
	$themeItemID = substr(hash("md5", $themeName), 0, 10);
	$treeItem = $navTree->insertExpandableTreeItem($themeItemID, $item, $parentID = "_themes");
	$navTree->assignSortValue($treeItem, $themeName);
	
	// List all theme css and js
	$themeManager = new templateTheme($templateID, $themeName);
	
	// List all javascripts
	$item = DOM::create("div", "Javascripts", "", "wstpl_p_js");
	$itemIco = DOM::create("span", "", "", "contentIcon fldIco");
	DOM::prepend($item, $itemIco);
	
	$treeItem = $navTree->insertExpandableTreeItem($itemID = $themeItemID."_js", $item, $parentID = $themeItemID);
	$navTree->assignSortValue($treeItem, "_js");
	
	// Get all javascripts
	$js_scripts = $themeManager->getAllJS();
	foreach ($js_scripts as $scriptName)
	{
		// Create group item container
		$item = DOM::create("div", $scriptName.".js", "", "wstpl_p_jsf");
		$itemIco = DOM::create("span", "", "", "contentIcon flIco");
		DOM::prepend($item, $itemIco);
		
		$itemID = substr(hash("md5", $scriptName), 0, 10);
		$treeItem = $navTree->insertTreeItem($itemID, $item, $parentID = $themeItemID."_js");
		$navTree->assignSortValue($treeItem, $scriptName);
		
		// Set action item
		$attr = array();
		$attr['id'] = $templateID;
		$attr['tname'] = $themeName;
		$attr['js_name'] = $scriptName;
		$actionFactory->setModuleAction($treeItem, $innerModules['themes_js'], "editJS", "", $attr, TRUE);
	}
	
	
	// List all styles
	$item = DOM::create("div", "Styles", "", "wstpl_p_css");
	$itemIco = DOM::create("span", "", "", "contentIcon fldIco");
	DOM::prepend($item, $itemIco);
	
	$treeItem = $navTree->insertExpandableTreeItem($itemID = $themeItemID."_css", $item, $parentID = $themeItemID);
	$navTree->assignSortValue($treeItem, "_css");
	
	// Get all styles
	$css_styles = $themeManager->getAllCSS();
	foreach ($css_styles as $styleName)
	{
		// Create group item container
		$item = DOM::create("div", $styleName.".scss", "", "wstpl_p_cssf");
		$itemIco = DOM::create("span", "", "", "contentIcon flIco");
		DOM::prepend($item, $itemIco);
		
		$itemID = substr(hash("md5", $styleName), 0, 10);
		$treeItem = $navTree->insertTreeItem($itemID, $item, $parentID = $themeItemID."_css");
		$navTree->assignSortValue($treeItem, $styleName);
		
		// Set action item
		$attr = array();
		$attr['id'] = $templateID;
		$attr['tname'] = $themeName;
		$attr['css_name'] = $styleName;
		$actionFactory->setModuleAction($treeItem, $innerModules['themes_css'], "editCSS", "", $attr, TRUE);
	}
}

return $pageContent->getReport();
//#section_end#
?>