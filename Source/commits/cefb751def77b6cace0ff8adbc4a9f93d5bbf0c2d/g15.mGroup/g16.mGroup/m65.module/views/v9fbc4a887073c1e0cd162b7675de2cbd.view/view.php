<?php
//#section#[header]
// Module Declaration
$moduleID = 65;

// Inner Module Codes
$innerModules = array();
$innerModules['literalEditor'] = 60;

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
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Security\account;

use \API\Developer\components\sdk\sdkLibrary;
use \API\Developer\components\units\modules\module;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\DOMParser;
use \UI\Forms\templates\simpleForm;
use \UI\Navigation\sideMenu;
use \UI\Navigation\navigationBar;
use \UI\Navigation\toolbarComponents\toolbarItem;
use \UI\Navigation\toolbarComponents\toolbarSeparator;
use \UI\Presentation\dataGridList;
use \UI\Presentation\tabControl;
use \UI\Html\HTMLContent;
use \INU\Developer\redWIDE;
use \INU\Developer\codeEditor;
use \INU\Developer\cssEditor;


// Get module and view ids
$viewModuleID = $_GET['mid'];
$viewID = $_GET['vid'];
$itemID = $viewModuleID."_".$viewID;

// Initialize module
$moduleObject = new module($viewModuleID);
$views = $moduleObject->getViews();
$viewName = $views[$viewID];
$viewObject = $moduleObject->getView("", $viewID);

// Initialize content
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("mView_".$itemID, "moduleViewEditor");


// Create main tabber
$tabber = new tabControl();
$mainViewTabber = $tabber->build()->get();
$pageContent->append($mainViewTabber);


// Create Tabs
//_____ View Designer
$tabID = $itemID."_designer";
$tabHeader = moduleLiteral::get($moduleID, "lbl_designer");
$viewDesignerContainer = DOM::create("div", "", "", "viewDesigner tabPageContent");
$tabber->insertTab($tabID, $tabHeader, $viewDesignerContainer, $selected = TRUE);
//_____ View Source
$tabID = $objID."_source";
$tabHeader = moduleLiteral::get($moduleID, "lbl_sourceCode");
$viewSourceContainer = DOM::create("div", "", "", "viewSource tabPageContent");
$tabber->insertTab($tabID, $tabHeader, $viewSourceContainer, $selected = FALSE);
//_____ View Behavior
$tabID = $objID."_behavior";
$tabHeader = moduleLiteral::get($moduleID, "lbl_jsCode");
$viewJSContainer = DOM::create("div", "", "", "viewJS tabPageContent");
$tabber->insertTab($tabID, $tabHeader, $viewJSContainer, $selected = FALSE);




// Designer tab (cssEditor)
$form = new simpleForm();
$innerForm = $form->build($moduleID, "updateView", FALSE)->get();
DOM::append($viewDesignerContainer, $innerForm);

// Module ID
$input = $form->getInput("hidden", "viewModuleID", $viewModuleID, $class = "", $autofocus = FALSE);
$form->append($input);

// View ID
$input = $form->getInput("hidden", "viewID", $viewID, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Designer Container
$editorOuterContainer = DOM::create();
$form->append($editorOuterContainer);

$navBar = new navigationBar();
$toolsBar = $navBar->build($dock = "T", $editorOuterContainer)->get();
DOM::append($editorOuterContainer, $toolsBar);

// Save button
$saveTool = DOM::create("button", "", "", "sideTool save");
DOM::attr($saveTool, "type", "submit");
$navBar->insertToolbarItem($saveTool);

$editor = new cssEditor($cssName = "viewCss", $htmlName = "viewHtml");
$viewHtml = $viewObject->getHTML();
$viewCSS = $viewObject->getCSS();
$objectEditor = $editor->build($viewHtml, $viewCSS)->get();
DOM::append($editorOuterContainer, $objectEditor);




// Source Code Tab
$innerForm = $form->build($moduleID, "updateSource", FALSE)->get();
DOM::append($viewSourceContainer, $innerForm);

// Module ID
$input = $form->getInput("hidden", "viewModuleID", $viewModuleID, $class = "", $autofocus = FALSE);
$form->append($input);

// View ID
$input = $form->getInput("hidden", "viewID", $viewID, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Designer Container
$editorOuterContainer = DOM::create();
$form->append($editorOuterContainer);

$navBar = new navigationBar();
$toolsBar = $navBar->build($dock = "T", $editorOuterContainer)->get();
DOM::append($editorOuterContainer, $toolsBar);

// Headers button
$headersTool = DOM::create("div", "", "", "sideTool headers");
$navBar->insertToolbarItem($headersTool);

// Tools
$literalEditorTool = DOM::create("div", "", "", "sideTool mlgMan");
$navBar->insertToolbarItem($literalEditorTool);
$attr = array();
$attr['moduleId'] = $viewModuleID;
$actionFactory->setModuleAction($literalEditorTool, $innerModules['literalEditor'], "", "", $attr);

// Save button
$saveTool = DOM::create("button", "", "", "sideTool save");
DOM::attr($saveTool, "type", "submit");
$navBar->insertToolbarItem($saveTool);

$editor = new codeEditor();
$code = $viewObject->getPHPCode();
$objectEditor = $editor->build($type = "php", $content = $code, $name = "viewSource", $editable = TRUE)->get();
DOM::append($editorOuterContainer, $objectEditor);
$viewInfoContainer = DOM::create("div", "", "", "viewInfo tabPageContent noDisplay");
DOM::append($editorOuterContainer, $viewInfoContainer);



// JS Code Tab
$innerForm = $form->build($moduleID, "updateJS", FALSE)->get();
DOM::append($viewJSContainer, $innerForm);

// Module ID
$input = $form->getInput("hidden", "viewModuleID", $viewModuleID, $class = "", $autofocus = FALSE);
$form->append($input);

// View ID
$input = $form->getInput("hidden", "viewID", $viewID, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Designer Container
$editorOuterContainer = DOM::create();
$form->append($editorOuterContainer);

$navBar = new navigationBar();
$toolsBar = $navBar->build($dock = "T", $editorOuterContainer)->get();
DOM::append($editorOuterContainer, $toolsBar);

// Save button
$saveTool = DOM::create("button", "", "", "sideTool save");
DOM::attr($saveTool, "type", "submit");
$navBar->insertToolbarItem($saveTool);

$editor = new codeEditor();
$code = $viewObject->getJSCode();
$objectEditor = $editor->build($type = "js", $content = $code, $name = "viewJS", $editable = TRUE)->get();
DOM::append($editorOuterContainer, $objectEditor);







// Settings Headers
//_____ Headers Area Container
$headersContainer = DOM::create('div','','','headersContainer');
DOM::append($viewInfoContainer, $headersContainer);

// Headers Outer Wrapper (for positioning)
$headersOuterWrapper = DOM::create("div", "", "", "headersOuterWrapper");
DOM::append($headersContainer, $headersOuterWrapper);

// Headers Inner Wrapper
$headersWrapper = DOM::create("div", "", "", "headersInnerWrapper");
DOM::append($headersOuterWrapper, $headersWrapper);

// Headers Menu
$headersSideMenu = DOM::create("div", "", "", "headersSideMenu");
DOM::append($headersWrapper, $headersSideMenu);


//_____ Build Side Navigation Menu for Headers
$nav_menu = new sideMenu();
$menu_title = moduleLiteral::get($moduleID, "lbl_menuTitle");
$nav_menu->build("", $menu_title);
DOM::append($headersSideMenu, $nav_menu->get());

// Static Navigation Attributes
$nav_targetcontainer = "headersViewer_".$itemID;
$nav_targetgroup = "headersGroup";
$nav_navgroup = "headersNavGroup_".$itemID;
$nav_display = "none";


$elementContent = moduleLiteral::get($moduleID, "lbl_moduleInfo");
$menuElement = $nav_menu->insertListItem("", $elementContent, TRUE);
$nav_menu->addNavigation($menuElement, "moduleInfo", $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);

$elementContent = moduleLiteral::get($moduleID, "lbl_packages");
$menuElement = $nav_menu->insertListItem("", $elementContent);
$nav_menu->addNavigation($menuElement, "imports", $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);

$elementContent = moduleLiteral::get($moduleID, "lbl_innerCodes");
$menuElement = $nav_menu->insertListItem("", $elementContent);
$nav_menu->addNavigation($menuElement, "innerCodes", $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);


//_____ Headers Content
$headersViewer = DOM::create("div", "", $nav_targetcontainer, "headersViewer");
DOM::append($headersWrapper, $headersViewer);

$headersViewerWrapper = DOM::create("div", "", "", "headersViewerWrapper");
DOM::append($headersViewer, $headersViewerWrapper);

// Module Info
$moduleInfo_container = DOM::create("div", "", "moduleInfo", "viewerPanel");
$nav_menu->addNavigationSelector($moduleInfo_container, $nav_targetgroup);
DOM::append($headersViewerWrapper, $moduleInfo_container);

//_____ Build Module Info From Database
$moduleHeader_title = moduleLiteral::get($moduleID, "lbl_moduleInfo");
$moduleInfo_header = DOM::create('h2', "", "", "lhd hd2");
DOM::append($moduleInfo_header, $moduleHeader_title);
DOM::append($moduleInfo_container, $moduleInfo_header);

// Module ID [temp?]
$moduleId_title = DOM::create('span', $module_id);
$moduleId_header = DOM::create('h2', "", "", "lhd hd2");
DOM::append($moduleId_header, $moduleId_title);
DOM::append($moduleInfo_container, $moduleId_header);

// Module Title
$title = moduleLiteral::get($moduleID, "lbl_moduleTitle");
$input = $form->getInput($type = "text", "viewName", $viewName, $class = "", $autofocus = TRUE);
$row = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($moduleInfo_container, $row);

// Module Imports
$moduleObjects_container = DOM::create("div", "", "imports", "moduleObjects noDisplay");
$nav_menu->addNavigationSelector($moduleObjects_container, $nav_targetgroup);
DOM::append($headersViewerWrapper, $moduleObjects_container);


$hd = moduleLiteral::get($policyCode, "lbl_packages");
$hdr = DOM::create('h2', "", "", "lhd hd2");
DOM::append($hdr, $hd);
DOM::append($moduleObjects_container, $hdr);

$dtGridList = new dataGridList();
$glist = $dtGridList->build("", TRUE)->get();
DOM::append($moduleObjects_container, $glist);

$headers = array();
$headers[] = "Library";
$headers[] = "Package";

$dtGridList->setHeaders($headers);
$dependencies = $viewObject->getDependencies();

// Get All Packages
$sdkLib = new sdkLibrary();
$packages = $sdkLib->getPackageList("", FALSE);

foreach ($packages as $lib => $pkgs)
	foreach ($pkgs as $pkg)
	{
		$checked = FALSE;
		if (is_array($dependencies[$lib]) && in_array($pkg, $dependencies[$lib]))
			$checked = TRUE;
			
		// Grid List Contents
		$gridRow = array();
		$gridRow[] = $lib;
		$gridRow[] = $pkg;
		
		$dtGridList->insertRow($gridRow, "dependencies[".$lib.','.$pkg.']', $checked);
	}

// Inner Modules
$moduleInnerCodes_container = DOM::create("div", "", "innerCodes", "innerCodes noDisplay");
$nav_menu->addNavigationSelector($moduleInnerCodes_container, $nav_targetgroup);
DOM::append($headersViewerWrapper, $moduleInnerCodes_container);

// Header
$hd = moduleLiteral::get($moduleID, "lbl_innerCodes");
$hdr = DOM::create('h2', "", "", "lhd hd2");
DOM::append($hdr, $hd);
DOM::append($moduleInnerCodes_container, $hdr);

$dtGridList = new dataGridList();
$glist = $dtGridList->build("", TRUE)->get();
DOM::append($moduleInnerCodes_container, $glist);

$headers = array();
$headers[] = "Index";
$headers[] = "Module";
$dtGridList->setHeaders($headers);
$inner = $viewObject->getInnerModules();

foreach ($inner as $key => $iCode)
{
	// Grid List Contents
	$gridRow = array();
	$gridRow[] = $key;
	$gridRow[] = $iCode;
	
	$dtGridList->insertRow($gridRow, 'inner['.$key.']', FALSE);
}


// Add Inner Codes Header
$hd = moduleLiteral::get($moduleID, "lbl_addInnerModules");
$hdr = DOM::create('h2', "", "", "lhd hd2");
DOM::append($hdr, $hd);
DOM::append($moduleInnerCodes_container, $hdr);

$dtGridList = new dataGridList();
$glist = $dtGridList->build()->get();
DOM::append($moduleInnerCodes_container, $glist);

$headers = array();
$headers[] = "Index";
$headers[] = "Module";

$dtGridList->setHeaders($headers);

$dbc = new interDbConnection();
$dbq = new dbQuery("564007386", "security.privileges.developer");		
$attr = array();
$attr['aid'] = account::getAccountID();
$modules = $dbc->execute($dbq, $attr);

$modules_resource = array();
$modules_resource[0] = "";
$modules_resource = $modules_resource + $dbc->toArray($modules, "id", "title");
for ($i = 0; $i < 5; $i++)
{
	$gridRow = array();
	
	// Title
	$input = $form->getInput($type = "text", $name = "inner[".$i."][title]", $value = "", $class = "", $autofocus = FALSE);
	$gridRow[] = $input;
	
	// Module
	$input = $form->getResourceSelect($name = "inner[".$i."][moduleId]", $multiple = FALSE, $class = "", $modules_resource, $selectedValue = 0);
	$gridRow[] = $input;
	
	$dtGridList->insertRow($gridRow);	
}


// Get wide tabber
$header = $viewName;
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($itemID, $header, $pageContent->get());
//#section_end#
?>