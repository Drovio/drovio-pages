<?php
//#section#[header]
// Module Declaration
$moduleID = 241;

// Inner Module Codes
$innerModules = array();
$innerModules['literalEditor'] = 273;

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
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("DEV", "Prototype");
importer::import("DEV", "Core");
importer::import("DEV", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\sql\dbQuery;
use \API\Model\modules\module as APIModule;
use \API\Model\modules\mGroup;
use \API\Literals\moduleLiteral;
use \API\Security\account;
use \API\Resources\DOMParser;
use \UI\Developer\devTabber;
use \UI\Developer\codeEditor;
use \UI\Developer\editors\CSSEditor;
use \UI\Forms\templates\simpleForm;
use \UI\Navigation\sideMenu;
use \UI\Navigation\navigationBar;
use \UI\Navigation\sideBar;
use \UI\Presentation\dataGridList;
use \UI\Presentation\tabControl;
use \UI\Modules\MContent;
use \DEV\Core\sdk\sdkLibrary;
use \DEV\Core\coreProject;
use \DEV\Modules\module;
use \DEV\Modules\modulesProject;
use \DEV\Projects\project;
use \DEV\Prototype\sourceMap;


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
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("mView_".$itemID, "moduleViewEditor");


// Create Global Container
$editorContainer = DOM::create("div", "", "", "mdlEditor");
$pageContent->append($editorContainer);

// Create Global Container Toolbar
$tlb = new sideBar();
$navToolbar = $tlb->build(sideBar::LEFT, $editorContainer)->get();
DOM::append($editorContainer, $navToolbar);

// Literals
$literalEditorTool = DOM::create("div", "", "", "objTool literals");
$tlb->insertToolbarItem($literalEditorTool);
$attr = array();
$attr['mid'] = $viewModuleID;
$actionFactory->setModuleAction($literalEditorTool, $innerModules['literalEditor'], "", "", $attr);

// Delete button
$delTool = DOM::create("div", "", "", "objTool delete");
$tlb->insertToolbarItem($delTool);
$attr = array();
$attr['mid'] = $viewModuleID;
$attr['vid'] = $viewID;
$actionFactory->setModuleAction($delTool, $moduleID, "deleteView", "", $attr);






// Create main tabber
$tabber = new tabControl();
$mainViewTabber = $tabber->build()->get();
DOM::append($editorContainer, $mainViewTabber);


// Create Tabs
//_____ View Source
$tabID = $objID."_source";
$tabHeader = moduleLiteral::get($moduleID, "lbl_sourceCode");
$viewSourceContainer = DOM::create("div", "", "", "viewSource tabPageContent");
$tabber->insertTab($tabID, $tabHeader, $viewSourceContainer, $selected = TRUE);
//_____ View Designer
$tabID = $itemID."_designer";
$tabHeader = moduleLiteral::get($moduleID, "lbl_designer");
$viewDesignerContainer = DOM::create("div", "", "", "viewDesigner tabPageContent");
$tabber->insertTab($tabID, $tabHeader, $viewDesignerContainer, $selected = FALSE);
//_____ View Behavior
$tabID = $objID."_behavior";
$tabHeader = moduleLiteral::get($moduleID, "lbl_jsCode");
$viewJSContainer = DOM::create("div", "", "", "viewJS tabPageContent");
$tabber->insertTab($tabID, $tabHeader, $viewJSContainer, $selected = FALSE);


// Source Code Tab
$form = new simpleForm("", TRUE);
$innerForm = $form->build($moduleID, "updateSource", FALSE)->get();
DOM::append($viewSourceContainer, $innerForm);

// Project ID
$input = $form->getInput("hidden", "id", modulesProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

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
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$navBar->insertToolbarItem($saveTool);

// Settings button
$settingsTool = DOM::create("div", "", "", "objTool settings");
$navBar->insertToolbarItem($settingsTool);


$editor = new codeEditor();
$code = $viewObject->getPHPCode();
$objectEditor = $editor->build($type = "php", $content = $code, $name = "viewSource", $editable = TRUE)->get();
DOM::append($editorOuterContainer, $objectEditor);
$viewInfoContainer = DOM::create("div", "", "", "viewInfo tabPageContent noDisplay");
DOM::append($editorOuterContainer, $viewInfoContainer);



// Designer tab (cssEditor)
$form = new simpleForm("", TRUE);
$innerForm = $form->build($moduleID, "updateView", FALSE)->get();
DOM::append($viewDesignerContainer, $innerForm);

// Project ID
$input = $form->getInput("hidden", "id", modulesProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

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
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$navBar->insertToolbarItem($saveTool);

$editor = new CSSEditor($cssName = "viewCss", $htmlName = "viewHtml");
$viewHtml = $viewObject->getHTML();
$viewCSS = $viewObject->getCSS();
$objectEditor = $editor->build($viewHtml, $viewCSS)->get();
DOM::append($editorOuterContainer, $objectEditor);



// JS Code Tab
$form = new simpleForm("", TRUE);
$innerForm = $form->build($moduleID, "updateJS", FALSE)->get();
DOM::append($viewJSContainer, $innerForm);

// Project ID
$input = $form->getInput("hidden", "id", modulesProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

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
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$navBar->insertToolbarItem($saveTool);

$editor = new codeEditor();
$code = $viewObject->getJSCode();
$objectEditor = $editor->build($type = "js", $content = $code, $name = "viewJS", $editable = TRUE)->get();
DOM::append($editorOuterContainer, $objectEditor);







// View Settings
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
$sidemenu = $nav_menu->build()->get();
DOM::append($headersSideMenu, $sidemenu);

// Static Navigation Attributes
$nav_targetcontainer = "headersViewer_".$itemID;
$nav_targetgroup = "headersGroup";
$nav_navgroup = "headersNavGroup_".$itemID;
$nav_display = "none";


$elementContent = moduleLiteral::get($moduleID, "lbl_moduleInfo");
$menuElement = $nav_menu->insertListItem("", $elementContent, TRUE);
$nav_menu->addNavigation($menuElement, "moduleInfo", $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);

$elementContent = moduleLiteral::get($moduleID, "lbl_dependencies");
$menuElement = $nav_menu->insertListItem("", $elementContent);
$nav_menu->addNavigation($menuElement, "imports", $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);

$elementContent = moduleLiteral::get($moduleID, "lbl_innerModules");
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

// View Info Editor
$attr = array();
$attr['moduleID'] = $viewModuleID;
$attr['viewID'] = $viewID;
$title = moduleLiteral::get($moduleID, "lbl_viewInfo", $attr);
$moduleInfo_header = DOM::create('h2', $title, "", "lhd hd2");
DOM::append($moduleInfo_container, $moduleInfo_header);

// Module Title
$title = moduleLiteral::get($moduleID, "lbl_viewTitle");
$input = $form->getInput($type = "text", "viewName", $viewName, $class = "", $autofocus = TRUE);
$row = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($moduleInfo_container, $row);

// Module Imports
$moduleObjects_container = DOM::create("div", "", "imports", "moduleObjects noDisplay");
$nav_menu->addNavigationSelector($moduleObjects_container, $nav_targetgroup);
DOM::append($headersViewerWrapper, $moduleObjects_container);


$title = moduleLiteral::get($moduleID, "lbl_dependencies");
$hdr = DOM::create('h2', $title, "", "lhd hd2");
DOM::append($moduleObjects_container, $hdr);

$dtGridList = new dataGridList();
$glist = $dtGridList->build("", TRUE)->get();
DOM::append($moduleObjects_container, $glist);

$headers = array();
$headers[] = "Library";
$headers[] = "Package";

$dtGridList->setHeaders($headers);
$dependencies = $viewObject->getDependencies();

// Check if developer is member of the core and get packages from development
$coreProject = new coreProject();
$coreValid = $coreProject->validate();
if ($coreValid)
{
	$sdkLib = new sdkLibrary();
	$libraries = $sdkLib->getList();
}
else
{
	$sourceMap = new sourceMap(systemRoot.systemSDK);
	$libraries = $sourceMap->getLibraryList();
}

// Get All Packages
$packages = array();
foreach ($libraries as $library)
	if ($coreValid)
	{
		$packages[$library] = $sdkLib->getPackageList($library);
		asort($packages[$library]);
	}
	else
	{
		$packages[$library] = $sourceMap->getPackageList($library);
		asort($packages[$library]);
	}
ksort($packages);

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
$title = moduleLiteral::get($moduleID, "lbl_innerModules");
$hdr = DOM::create('h2', $title, "", "lhd hd2");
DOM::append($moduleInnerCodes_container, $hdr);

$dtGridList = new dataGridList();
$glist = $dtGridList->build("", TRUE)->get();
DOM::append($moduleInnerCodes_container, $glist);

$ratios = array();
$ratios[] = 0.25;
$ratios[] = 0.15;
$ratios[] = 0.6;
$dtGridList->setColumnRatios($ratios);

$headers = array();
$headers[] = "Friendly Name";
$headers[] = "ModuleID";
$headers[] = "ModulePath";
$dtGridList->setHeaders($headers);

$inner = $viewObject->getInnerModules();
foreach ($inner as $key => $iCode)
{
	// Grid List Contents
	$gridRow = array();
	$gridRow[] = $key;
	$gridRow[] = $iCode;
	
	// Get module path/trail
	$info = APIModule::info($iCode);
	$modulePath = mGroup::getTrail($info['group_id']);
	$modulePath .= APIModule::getDirectoryName($iCode);
	$gridRow[] = $modulePath;
	
	$dtGridList->insertRow($gridRow, 'inner['.$key.']', FALSE);
}


// Add Inner Codes Header
$title = moduleLiteral::get($moduleID, "lbl_addInnerModules");
$hdr = DOM::create('h2', $title, "", "lhd hd2");
DOM::append($moduleInnerCodes_container, $hdr);

$dtGridList = new dataGridList();
$glist = $dtGridList->build()->get();
DOM::append($moduleInnerCodes_container, $glist);

$headers = array();
$headers[] = "Index";
$headers[] = "Module";

$dtGridList->setHeaders($headers);

$dbc = new dbConnection();
$dbq = new dbQuery("564007386", "security.privileges.developer");
$attr = array();
$attr['aid'] = account::getAccountID();
$modules = $dbc->execute($dbq, $attr);

$modules_resource = array();
$modules_resource[0] = "";
$modules_resource = $modules_resource + $dbc->toArray($modules, "id", "title");

/*
// Name resolving for modules
foreach ($modules_resource as $mID => $title)
{
	if ($mID <= 0)
		continue;
	$info = APIModule::info($mID);
	$modulePath = mGroup::getTrail($info['group_id']);
	$modulePath .= APIModule::getDirectoryName($mID);
	$modules_resource[$mID] = $modulePath;
}
asort($modules_resource);
*/
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
$devTabber = new devTabber();
return $devTabber->getReportContent($itemID, $header, $pageContent->get());
//#section_end#
?>