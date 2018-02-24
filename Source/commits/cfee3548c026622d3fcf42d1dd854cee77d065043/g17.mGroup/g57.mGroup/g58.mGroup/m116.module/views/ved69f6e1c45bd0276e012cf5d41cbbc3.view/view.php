<?php
//#section#[header]
// Module Declaration
$moduleID = 116;

// Inner Module Codes
$innerModules = array();
$innerModules['settingsManager'] = on;
$innerModules['assetsManager'] = 129;
$innerModules['pageStructureManager'] = 127;
$innerModules['themesManager'] = 128;
$innerModules['componentEditor'] = 126;

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
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\template;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Html\HTMLContent;
use \UI\Navigation\sideMenu;
use \UI\Presentation\notification;
use \UI\Html\pageComponents\toolbarComponents\toolbarItem;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;

$templateID = $_GET['id'];
//$templateID = 7;



// Try to load Object
$templateObject = new template();
$status = $templateObject->load($templateID);

if(!$status)
{
	$reportNtf = new notification();
	
	$reportNtf->build($type = "error", $header = TRUE, $footer = FALSE);
	$reportMessage = DOM::create("span", "Could not find");
	$reportNtf->append($reportMessage);
	
	$HTMLContent = new HTMLContent();
	$HTMLContent->buildElement($reportNtf->get());

	// Return Report	
	return $HTMLContent->getReport();
}

$defAttr = array();
$defAttr['id'] = $templateID;

// Create Module Page
$HTMLModulePage = new HTMLModulePage("OneColumnFullscreen");
$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);

// Build the module
$HTMLModulePage->build($pageTitle);

// Build ToolBar Menu
// Static Navigation Attributes
$nav_targetcontainer = "pageContent";
$nav_targetgroup = "designerOptionsGroup";
$nav_navgroup = "designerOptionsNavGroup";
$nav_display = "none"; 

// Build Side Navigation Menu
$sideMenu_builder = new sideMenu();
//$sideMenu_object = $sideMenu_builder->build('templateDesignerSideMenu');

// settingsPane menu item
$menuElement = moduleLiteral::get($moduleID, "lbl_objectOverview");
$ref = 'settingsPane';
//$menuItem = $sideMenu_builder->insertListItem("", $menuElement);
$menuItem =  $HTMLModulePage->addToolbarNavItem("1", $menuElement, "");
$sideMenu_builder->addNavigation($menuItem, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
DOM::appendAttr($menuItem, "class", "selected");

// Assets Pane menu item
$menuElement = DOM::create("span",  "Assets");
$ref = 'assetsPane';
//$menuItem = $sideMenu_builder->insertListItem("", $menuElement);
$menuItem =  $HTMLModulePage->addToolbarNavItem("2", $menuElement, "");
$sideMenu_builder->addNavigation($menuItem, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);

// Page Structure Pane menu item
$menuElement = DOM::create("span",  "Publish");
$ribbonCollection = $HTMLModulePage->getRibbonCollection('publisherContent', $moduleID, 'publisher');
$HTMLModulePage->addToolbarNavItem('', $menuElement, '', $ribbonCollection, $ribbonType = "inline", $type = "obedient toggle", $pinnable = FALSE, $index = 0);


// Page Structure Pane menu item
$menuElement = DOM::create("span",  "Help");
$navCollection = $HTMLModulePage->getRibbonCollection("helpMenu");
$subItem = $HTMLModulePage->addToolbarNavItem("devNavSub", $menuElement , "", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

// Create new moduleGroup
$panel = new ribbonPanel();
$newGrp = $panel->build("moduleGroup", TRUE)->get();

$title = DOM::create("span", "Support Center");//moduleLiteral::get($moduleID, "lbl_addGroup");
$newGroup = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
//$actionFactory->setModuleAction($newGroup, $innerModules['createGroup']);
DOM::append($navCollection, $newGrp);


$title = DOM::create("span", "Guide");//moduleLiteral::get($moduleID, "lbl_addGroup");
$newGroup = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
//$actionFactory->setModuleAction($newGroup, $innerModules['createGroup']);
DOM::append($navCollection, $newGrp);


// Build Page Content
$globalContainer = DOM::create("div", "", "");
DOM::attr($globalContainer, "style", "height:100%;");
$HTMLModulePage->appendToSection("mainContent", $globalContainer);


// Main

// Settings
$settingsPane = $HTMLModulePage->getModuleContainer($innerModules['componentEditor'], '', $defAttr, $startup = TRUE, 'settingsPane');
$sideMenu_builder->addNavigationSelector($settingsPane, $nav_targetgroup);
DOM::append($globalContainer, $settingsPane);

// Publish
$pageStructurePane = $HTMLModulePage->getModuleContainer($moduleID, 'publisher', $defAttr, $startup = TRUE, 'publishPane');
$sideMenu_builder->addNavigationSelector($pageStructurePane, $nav_targetgroup);
DOM::append($globalContainer, $pageStructurePane);


// Assets
$assetsPane = $HTMLModulePage->getModuleContainer($moduleID, 'assetsEditor', $defAttr, $startup = TRUE, 'assetsPane');
$sideMenu_builder->addNavigationSelector($assetsPane, $nav_targetgroup);
DOM::append($globalContainer, $assetsPane);

// Return output
return $HTMLModulePage->getReport();
//#section_end#
?>