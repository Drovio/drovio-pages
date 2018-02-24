<?php
//#section#[header]
// Module Declaration
$moduleID = 115;

// Inner Module Codes
$innerModules = array();
$innerModules['templateObject'] = 117;

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
importer::import("API", "Resources");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\HTMLRibbon;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Presentation\frames\windowFrame;
use \UI\Navigation\sideMenu;


// Create Module Page
$HTMLModulePage = new HTMLModulePage("TwoColumnsLeftSidebarCentered");
$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);

// Build the module
$HTMLModulePage->build($pageTitle);

//____________________ Build Top Navigation ____________________//

// _____ Toolbar Navigation
$instructions = array();
$instructions['position'] = "leftNav";
$instructions['index'] = "0";
$attr = array();
$attr['title'] = "";
$attr['class'] = "add_new";
$nav = array();
$nav['ref'] = "templateManagerNav";
$nav['ribbon'] = "float";
$nav['type'] = "obedient toggle";
$nav['pinnable'] = "0";

// Database Navigation Collection
$navCollection = $HTMLModulePage->getRibbonCollection("templateManagerNav");
$subItem = $HTMLModulePage->addToolbarNavItem("templateManagerNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);


// Create Namespace/Object
$new_objPanel = new ribbonPanel();
//$new_objPanel->insert_group();
//___ Object
$objTitle = moduleLiteral::get($moduleID, "createTemplate");
$new_objItem = $new_objPanel->insertPanelItem("small", $objTitle);
windowFrame::setAction($new_objItem, $innerModules['templateObject'], 'newTemplate');
HTMLRibbon::insertItem($navCollection, $new_objItem );
//____________________ Build Top Navigation ____________________//__________End


// Build Side Navigation Menu 
$sideMenu = new sideMenu();
$sideMenu->build();

// Static Navigation Attributes
$nav_targetcontainer = "pageContent"; 
$nav_targetgroup = "designerOptionsGroup";
$nav_navgroup = "designerOptionsNavGroup";
$nav_display = "none"; 

$menuElement = moduleLiteral::get($moduleID, "hdr_allTemplates");
$ref = 'allTemplates';
$menuItem =  $sideMenu->insertListItem("", $menuElement, "TRUE");
$sideMenu->addNavigation($menuItem, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);

$menuElement = moduleLiteral::get($moduleID, "hdr_myProjectTemplates");
$ref = 'myProject';
$menuItem =  $sideMenu->insertListItem("", $menuElement, "TRUE");
$sideMenu->addNavigation($menuItem, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);

$menuElement = moduleLiteral::get($moduleID, "hdr_myDeployedTemplates");
$ref = 'myTemplates';
$menuItem =  $sideMenu->insertListItem("", $menuElement, "TRUE");
$sideMenu->addNavigation($menuItem, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);


$HTMLModulePage->appendToSection("sidebar", $sideMenu->get());



$attr = array();
// "All Template" Tab Content
$id = "allTemplates";
$attr['viewType'] = 'all';
$viewer = $HTMLModulePage->getModuleContainer($moduleID, "innerListViewer", $attr, TRUE);
$tabContent = DOM::create('div', "", $id);
$prompt = DOM::create('div');
DOM::append($tabContent, $prompt);
$promtText = DOM::create('p');
DOM::append($prompt, $promtText);
DOM::append($promtText, moduleLiteral::get($moduleID, "prmt_allTemplates"));
DOM::append($tabContent, $viewer);
$sideMenu->addNavigationSelector($tabContent, $nav_targetgroup);
$HTMLModulePage->appendToSection("mainContent", $tabContent);

// "My Project" Tab Content
$selected = TRUE;
$id = "myProject";
$attr['viewType'] = 'project';
$viewer = $HTMLModulePage->getModuleContainer($moduleID, "innerListViewer", $attr, TRUE);
$tabContent = DOM::create('div', "", $id);
$prompt = DOM::create('div');
DOM::append($tabContent, $prompt);
$promtText = DOM::create('p');
DOM::append($prompt, $promtText);
DOM::append($promtText, moduleLiteral::get($moduleID, "prmt_myProjectTemplates"));
DOM::append($tabContent, $viewer);
$sideMenu->addNavigationSelector($tabContent, $nav_targetgroup);
$HTMLModulePage->appendToSection("mainContent", $tabContent);

// "My Templates" Tab Content
$id = "myTemplates";
$attr['viewType'] = 'my';
$viewer = $HTMLModulePage->getModuleContainer($moduleID, "innerListViewer", $attr, TRUE);
$tabContent = DOM::create('div', "", $id);
$prompt = DOM::create('div');
DOM::append($tabContent, $prompt);
$promtText = DOM::create('p');
DOM::append($prompt, $promtText);
DOM::append($promtText, moduleLiteral::get($moduleID, "prmt_myDeployedTemplates"));
DOM::append($tabContent, $viewer);
$sideMenu->addNavigationSelector($tabContent, $nav_targetgroup);
$HTMLModulePage->appendToSection("mainContent", $tabContent);

// Return output
return $HTMLModulePage->getReport();
//#section_end#
?>