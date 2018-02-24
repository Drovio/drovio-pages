<?php
//#section#[header]
// Module Declaration
$moduleID = 143;

// Inner Module Codes
$innerModules = array();
$innerModules['extensionObject'] = 142;

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
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Prototype");
importer::import("INU", "Developer");
importer::import("INU", "Views");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\extension;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\formControls\formItem;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\HTMLRibbon;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Navigation\sideMenu;
use \UI\Presentation\frames\windowFrame;
use \UI\Presentation\gridSplitter;
use \UI\Presentation\tabControl;
use \INU\Developer\redWIDE;
use \INU\Views\fileExplorer; 

// Get Variables
$extensionID = $_GET['id'];
$extensionID = 16;

// Create Module Page
$page = new HTMLModulePage("simpleFullScreen");
$actionFactory = $page->getActionFactory();
$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);

// Build the module
$page->build($pageTitle);

// Try to load Object
$extensionManager = new extension();
$status = $extensionManager->load($extensionID);

if(!$status)
{
	return $page->getReport();
}


// Object Loaded - Continue
$defAttr = array();
$defAttr['id'] = $extensionID;

//____________________ Build Top Navigation ____________________//
$navCollection = $page->getRibbonCollection("extDevNav");
$subItem = $page->addToolbarNavItem("extDevNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

// Add View
$ribbonPanel = new ribbonPanel();
//$new_objPanel->insert_group();
//___ Object 
$title = moduleLiteral::get($moduleID, "lbl_addView");
$ribbonPanel->build();
$panelItem = $ribbonPanel->insertPanelItem("small", $title);
windowFrame::setAction($panelItem, $innerModules['extensionObject'], "createView", $defAttr);
HTMLRibbon::insertItem($navCollection, $ribbonPanel->get());

// Add Theme (same panel as above)
//$new_objPanel->insert_group();
//___ Object 
$title = moduleLiteral::get($moduleID, "lbl_addCssTheme");
$panelItem = $ribbonPanel->insertPanelItem("small", $title);
windowFrame::setAction($panelItem, $innerModules['extensionObject'], "createTheme", $defAttr);

// Add Script (same panel as above)
//___ Object 
$title = moduleLiteral::get($moduleID, "lbl_addJsScript");
$panelItem = $ribbonPanel->insertPanelItem("small", $title);
windowFrame::setAction($panelItem, $innerModules['extensionObject'], "createJsScript", $defAttr);

// Add Php Script
$ribbonPanel = new ribbonPanel();
//$new_objPanel->insert_group();
//___ Object
$title = moduleLiteral::get($moduleID, "lbl_addPhpScript");
$ribbonPanel->build();
$panelItem = $ribbonPanel->insertPanelItem("small", $title);
windowFrame::setAction($panelItem, $innerModules['extensionObject'], "createPhpScript", $defAttr);
HTMLRibbon::insertItem($navCollection, $ribbonPanel->get());

// Add NameSpace (same panel as above)
//___ Object 
$title = moduleLiteral::get($moduleID, "lbl_addNamespace");
$panelItem = $ribbonPanel->insertPanelItem("small", $title);
windowFrame::setAction($panelItem, $innerModules['extensionObject'], "createNamespace", $defAttr);

// Add Package (same panel as above)
//___ Object 
$title = moduleLiteral::get($moduleID, "lbl_addPackage");
$panelItem = $ribbonPanel->insertPanelItem("small", $title);
windowFrame::setAction($panelItem, $innerModules['extensionObject'], "createPackage", $defAttr);

//____________________ Build Top Navigation ____________________//__________End


// Create TabControl
$tabControl = new tabControl();
$extensionDesignerTabber = $tabControl->get_control($id = "tbr_extensionDesignerTabber", FALSE);
//DOM::attr($globalContainer, "style", "height:100%;");
$page->appendToSection("mainContent", $extensionDesignerTabber);


// Configuration Tab
$selected = FALSE;
$id = "config";
$tabContent = DOM::create('div', '', '', '');
	DOM::attr($tabContent, "style", "height:100%");
	// Side Bar Menu
	$sidebar  = DOM::create("div", "", "", "leftSidebar");
	DOM::append($tabContent, $sidebar);
	
	// Static Navigation Attributes
	$nav_targetcontainer = "config";
	$nav_targetgroup = "extConfigMnrgGroup";
	$nav_navgroup = "extConfigMnrgNavGroup";
	$nav_display = "none";
	
	// Build Side Navigation Menu
	$sideMenu_builder = new sideMenu();
	$sideMenu_object = $sideMenu_builder->build('extRrscManagerSideMenu');
	
	// Extension Setting menu item
	$menuElement = moduleLiteral::get($moduleID, "lbl_extSettings");
	$ref = 'extSettings';
	$sideMenu_builder->addNavigation($menuElement, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	$sideMenu_builder->insertListItem("", $menuElement);
		
	// Deploy Manager menu item
	$menuElement = moduleLiteral::get($moduleID, "lbl_deployManager");
	$ref = 'deployManager';
	$sideMenu_builder->addNavigation($menuElement, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	$sideMenu_builder->insertListItem("", $menuElement);
	
	// End User Config menu item
	$menuElement = moduleLiteral::get($moduleID, "lbl_enduserConfig");
	$ref = 'enduserConfigurator';
	$sideMenu_builder->addNavigation($menuElement, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	$sideMenu_builder->insertListItem("", $menuElement);
	
	DOM::append($sidebar, $sideMenu_builder->get());
	
	// Main
	$rightContent = DOM::create("div", "", "", "rightContent");
	DOM::append($tabContent, $rightContent);
	
	// Extension Setting
	$settingsPane = DOM::create("div", "", "extSettings", "");
		// Create form
		$configFormBuilder = new simpleForm();
		$configEditorElement = $configFormBuilder->build($innerModules['extensionObject'], "", TRUE)->get();
		DOM::append($settingsPane, $configEditorElement);
		// Form Header
		$header = DOM::create("h1", "", "", "sectionHeader");
		$headerContent = moduleLiteral::get($moduleID, "lbl_extSettings");
		DOM::append($header, $headerContent);
		$configFormBuilder->append($header);
		// #Hidden Values
		// ## Extension Id
		$input = $configFormBuilder->getInput("hidden", "id", $extensionID, $class = "", $autofocus = FALSE);
		$configFormBuilder->append($input);
		// #Extension Title
		$title = moduleLiteral::get($moduleID, "lbl_extTitle"); 
		$input = $configFormBuilder->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = TRUE);
		$configFormBuilder->insertRow($title, $input, $required = TRUE, $notes = "");
		// #Extension Description
		$title = moduleLiteral::get($moduleID, "lbl_extDescription"); 
		$input = $configFormBuilder->getTextarea($name = "description", $value = "", $class = "");
		$configFormBuilder->insertRow($title, $input, $required = FALSE, $notes = "");
		// #Using jq
		$title = moduleLiteral::get($moduleID, "lbl_jqUsage");
		$input = $configFormBuilder->getInput($type = "checkbox", $name = "jqUsage", $value = "", $class = "", $autofocus = TRUE);
		$configFormBuilder->insertRow($title, $input, $required = FALSE, $notes = "");
		if ($extensionManager->getJqUsage() == "1")
		{
			DOM::attr($input , "checked", "checked");
		}
		#Having Assets
		$title = moduleLiteral::get($moduleID, "lbl_assetsUsage");
		$input = $configFormBuilder->getInput($type = "checkbox", $name = "assetsUsage", $value = "", $class = "", $autofocus = TRUE);
		$configFormBuilder->insertRow($title, $input, $required = FALSE, $notes = "");
		if ($extensionManager->getAssetsExistance() == "1")
		{
			DOM::attr($input , "checked", "checked");
		}
	$sideMenu_builder->addNavigationSelector($settingsPane, $nav_targetgroup);
	DOM::append($rightContent, $settingsPane);
	
	// Deploy Manager
	$pageStructurePane  = DOM::create("div", "", "deployManager", "noDisplay");
		// Main Header
		$header = DOM::create("h1", "", "", "sectionHeader");
		$headerContent = moduleLiteral::get($moduleID, "lbl_deployManager");
		DOM::append($header, $headerContent);
		DOM::append($pageStructurePane, $header);
		// # Release
		$header = DOM::create("h2", "", "", "sectionHeader");
		$headerContent = moduleLiteral::get($moduleID, "lbl_releaseSecHdr");
		DOM::append($header, $headerContent);
		DOM::append($pageStructurePane, $header);		
			// Create Release Button
			$formItem = new formItem();
			$formItem->build("button", $name, $id, "", "uiFormButton".($positive ? " positive" : ""));
			$brn_createGroup = $formItem->get(); 
			$title = DOM::create('span',"Release");//moduleLiteral::get($moduleID, "lbl_createUserGroup");
			DOM::append($brn_createGroup, $title);
			$attr = $defAttr;
			$actionFactory->setModuleAction($brn_createGroup, $innerModules['extensionObject'], "releaseExtension", "", $attr);
			DOM::append($pageStructurePane, $brn_createGroup);
			
		// # Deploy
		$header = DOM::create("h2", "", "", "sectionHeader");
		$headerContent = moduleLiteral::get($moduleID, "lbl_deploySecHdr");
		DOM::append($header, $headerContent);
		DOM::append($pageStructurePane, $header);	
	$sideMenu_builder->addNavigationSelector($pageStructurePane, $nav_targetgroup);
	DOM::append($rightContent, $pageStructurePane);
	
	// End User Config	
	$themesPane = DOM::create("div", "", "enduserConfigurator", "noDisplay");
		// Main Header
		$header = DOM::create("h1", "", "", "sectionHeader");
		$headerContent = moduleLiteral::get($moduleID, "lbl_enduserConfig");
		DOM::append($header, $headerContent);
		DOM::append($themesPane, $header);
	$sideMenu_builder->addNavigationSelector($themesPane, $nav_targetgroup);
	DOM::append($rightContent, $themesPane);
	
$header = moduleLiteral::get($moduleID, "lbl_extProjectConfig");
$tabControl->insert_tab($id, $header, $tabContent, $selected);

// Coding Tab
$selected = TRUE;
$id = "coder";
//$tabContent = DOM::create('div');
	// Create splitter
	$splitter = new gridSplitter();
	$viewer = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_RIGHT, $closed = FALSE)->get();
	$tabContent = $viewer;
	// redWIDE
	$wide = new redWIDE();
	$codeEditor = $wide->build()->get();
	$splitter->appendToMain($codeEditor);
	// Sidebar	
	$attr = $defAttr;
	$viewerContainer = HTMLModulePage::getModuleContainer($moduleID, "codeEditorSidebar", $attr);
	$splitter->appendToSide($viewerContainer);	
$header = moduleLiteral::get($moduleID, "lbl_extProjectCoding");
$tabControl->insert_tab($id, $header, $tabContent, $selected);

// Assets Tab
$selected = FALSE;
$id = "assets";
$tabContent = DOM::create('div', '', '', '');
	DOM::attr($tabContent, "style", "height:100%");
	// Side Bar Menu
	$sidebar  = DOM::create("div", "", "", "leftSidebar");
	DOM::append($tabContent, $sidebar);
	
	// Static Navigation Attributes
	$nav_targetcontainer = "assets";
	$nav_targetgroup = "extRrscManagerGroup";
	$nav_navgroup = "extRrscManagerNavGroup";
	$nav_display = "none";
	
	// Build Side Navigation Menu
	$sideMenu_builder = new sideMenu();
	$sideMenu_object = $sideMenu_builder->build('extRrscManagerSideMenu');
	
	// Literal Manager menu item
	$menuElement = moduleLiteral::get($moduleID, "lbl_literalManager");
	$ref = 'literalManager';
	$sideMenu_builder->addNavigation($menuElement, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	$sideMenu_builder->insertListItem("", $menuElement);
		
	// Extension Resources menu item
	$menuElement = moduleLiteral::get($moduleID, "lbl_extResources");
	$ref = 'extResources';
	$sideMenu_builder->addNavigation($menuElement, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	$sideMenu_builder->insertListItem("", $menuElement);
	
	// Dummy Resources Pane menu item
	$menuElement = moduleLiteral::get($moduleID, "lbl_endUserResources");
	$ref = 'dummyResources';
	$sideMenu_builder->addNavigation($menuElement, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	$sideMenu_builder->insertListItem("", $menuElement);
	
	DOM::append($sidebar, $sideMenu_builder->get());
	
	// Main
	$rightContent = DOM::create("div", "", "", "rightContent");
	DOM::append($tabContent, $rightContent);
	
	// Literal Manager
	//page->getModuleContainer($moduleID, $action = "", $attr = array(), $startup = TRUE, $containerID = "")
	//$settingsPane_builder = new ModuleContainerPrototype($innerModules['settingsManager'] );
	//$settingsPane_object = $settingsPane_builder->build($defAttr, $startup = TRUE, 'settingsPane');
	//$settingsPane = $settingsPane_object->get();
	$settingsPane = DOM::create("div", "", "literalManager", "");
	$sideMenu_builder->addNavigationSelector($settingsPane, $nav_targetgroup);
	DOM::append($rightContent, $settingsPane);
	
	// Extension Resources Structure
	$path = $extensionManager->getMediaFolder();
	$fileExplorer = new fileExplorer($path, 'extResources');
	$pageStructurePane = $fileExplorer->build()->get();
	$sideMenu_builder->addNavigationSelector($pageStructurePane, $nav_targetgroup);
	DOM::append($rightContent, $pageStructurePane);
	
	// Dummy Resources
	//$themesPane_builder = new ModuleContainerPrototype($innerModules['themesManager'] );
	//$themesPane_object = $themesPane_builder->build($defAttr, $startup = TRUE, 'themesPane');
	//$themesPane = $themesPane_object->get();
	$themesPane = DOM::create("div", "", "dummyResources", "");
	$sideMenu_builder->addNavigationSelector($themesPane, $nav_targetgroup);
	DOM::append($rightContent, $themesPane);	
$header = moduleLiteral::get($moduleID, "lbl_extProjectResourceMngr");
$tabControl->insert_tab($id, $header, $tabContent, $selected);

// Return output
return $page->getReport();
//#section_end#
?>