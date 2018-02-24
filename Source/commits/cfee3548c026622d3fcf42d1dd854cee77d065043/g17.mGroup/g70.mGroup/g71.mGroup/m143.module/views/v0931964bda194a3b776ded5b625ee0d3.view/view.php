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
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\components\ebuilder\ebLibrary;
use \API\Developer\ebuilder\extension;
use \API\Developer\ebuilder\extComponents\extPage;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Navigation\sidebar;
use \UI\Navigation\sideMenu;
use \UI\Navigation\toolbarComponents\toolbarItem;
use \UI\Navigation\toolbarComponents\toolbarSeparator;
use \UI\Presentation\dataGridList;
use \UI\Html\HTMLContent;
use \INU\Developer\codeEditor;
use \INU\Developer\redWIDE;

// Try to Load Extension
$extensionObject = new extension();
$success = $extensionObject->load($_GET['id']);
if(!$success)
{
	//return Notification error. not loaded
	echo "Extension Not Loaded";
}

// Try to Load Object
$pageObject = $extensionObject->getView($_GET['name']);

// Create Module Page
$HTMLContentBuilder = new HTMLContent();
$actionFactory = $HTMLContentBuilder->getActionFactory();
// Create form
$sForm = new simpleForm();
$sForm->build($innerModules['extensionObject'], "saveView", $controls = FALSE);
// Append form to Content
$HTMLContentBuilder->buildElement($sForm->get());

// Build Content
// #Hidden Values
// ## Extension Id
$input = $sForm->getInput("hidden", "id", $_GET['id'], $class = "", $autofocus = FALSE);
$sForm->append($input);
// ## View Name
$input = $sForm->getInput("hidden", "name", $_GET['name'], $class = "", $autofocus = FALSE);
$sForm->append($input);

// #Content Wrapper
$obj_container = DOM::create();
$sForm->append($obj_container);

// ## Code Editor
$code = $pageObject->getSourceCode();
$codeEditor = new codeEditor();
$codeEditor->build("php", $code);
DOM::append($obj_container, $codeEditor->get());

// ##Toolbar
// Toolbar Control 
$tlb = new sidebar();
$tlbItemBuilder = new toolbarItem();
$tlbSeperatorBuilder = new toolbarSeparator();
// Create Source Code Manager Toolbar;
$mgrToolbarElement = $tlb->build($dock = "L", $obj_container)->get();
DOM::append($obj_container, $mgrToolbarElement); 

// ###Code Group Tools
$codeGroup = DOM::create("div", "", "", "toolGroup codeGroup");
DOM::append($mgrToolbarElement, $codeGroup);

// ####Save
$content = DOM::create("button", "", "", "sideTool save");
DOM::attr($content, "type", "submit");
$saveTool = $tlbItemBuilder->build($content)->get();
//PopupProtocol::addAction($saveTool, $innerModules['extensionObject'], "savePage");
DOM::append($codeGroup, $saveTool); 

// ####Commit
$content = DOM::create("div", "", "", "sideTool commit");
$commitTool = $tlbItemBuilder->build($content)->get();
$attr = array();
$attr['id'] = $_GET['id'];
$attr['name'] = $_GET['name'];
$actionFactory->setModuleAction($commitTool, $innerModules['extensionObject'], "commitView", "", $attr);
DOM::append($codeGroup, $commitTool); 

// ###Separator
$tlb->insertSeparator();

// ###Info Group Tools
$infoGroup = DOM::create("div", "", "", "toolGroup infoGroup");
DOM::append($mgrToolbarElement, $infoGroup);

// ####headers
$content = DOM::create("div", "", "", "sideTool headers");
$displayHeaders = $tlbItemBuilder->build($content)->get();
//NavigatorProtocol::staticNav($displayHeaders, 'objHeaderMng', $targetcontainer, '', $navgroup, "toggle");
DOM::append($infoGroup, $displayHeaders);

// #Object Header Options
$objHeaderMng = DOM::create('div','','objHeaderMng','headersContainer');
//DOM::attr($objHeaderMng, 'style',"display: none;");
$sForm->append($objHeaderMng);

// Headers Outer Wrapper (for positioning)
$headersOuterWrapper = DOM::create("div", "", "", "headersOuterWrapper");
DOM::append($objHeaderMng, $headersOuterWrapper);

// #Headers Inner Wrapper
$headersWrapper = DOM::create("div", "", "", "headersInnerWrapper");
DOM::append($headersOuterWrapper, $headersWrapper);

// ##Headers Menu
$headersSideMenu = DOM::create("div", "", "", "headersSideMenu");
DOM::append($headersWrapper, $headersSideMenu);

// ###Build Side Navigation Menu for Headers
$sideMenuBuilder = new sideMenu();
$menu_title = DOM::create('span', 'Menu Title'); //moduleLiteral::get($moduleID, "lbl_menuTitle");
$sideMenuElement = $sideMenuBuilder->build("", $menu_title)->get(); 
DOM::append($headersSideMenu, $sideMenuElement);

// Static Navigation Attributes
$nav_targetcontainer = "headersViewer_".$module_attr['ref'];
$nav_targetgroup = "headersGroup";
$nav_navgroup = "headersNavGroup_".$module_attr['ref'];
$nav_display = "none"; 

$menuElement = DOM::create("span", "View Info"); //moduleLiteral::get($moduleID, "lbl_moduleInfo");
$ref = 'viewInfo';
$sideMenuBuilder->addNavigation($menuElement, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
$sideMenuBuilder->insertListItem("", $menuElement);

$menuElement = DOM::create("span", "View Imports"); //moduleLiteral::get($moduleID, "lbl_moduleInfo");
$ref = 'imports';
$sideMenuBuilder->addNavigation($menuElement, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
$sideMenuBuilder->insertListItem("", $menuElement);

// ##Headers Content
$headersViewer = DOM::create("div", "", $nav_targetcontainer, "headersViewer");
DOM::append($headersWrapper, $headersViewer);

$headersViewerWrapper = DOM::create("div", "", "", "headersViewerWrapper");
DOM::append($headersViewer, $headersViewerWrapper);

// ###Module Info
$moduleInfo_container = DOM::create("div", "", "viewInfo", "viewerPanel");
$sideMenuBuilder->addNavigationSelector($moduleInfo_container, $nav_targetgroup);
DOM::append($headersViewerWrapper, $moduleInfo_container);

//_____ Build Module Info From Database
//$moduleHeader_title = moduleLiteral::get($moduleID, "lbl_moduleInfo");
//$moduleInfo_header = $form_builder->get_header($moduleHeader_title, "2");
//DOM::append($moduleInfo_container, $moduleInfo_header);


// View Name
$title = DOM::create("span", "View Name"); //moduleLiteral::get($moduleID, "lbl_queryTitle"); 
$input = $sForm->getInput($type = "text", $name = "viewName", $_GET['name'], $class = "", $autofocus = TRUE);
$row = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($moduleInfo_container, $row);

// View Description
$title = DOM::create("span", "View Description"); //moduleLiteral::get($moduleID, "lbl_queryDescription"); 
$input = $sForm->getTextarea($name = "viewDescription", $viewDescription, $class = "");
$row = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($moduleInfo_container, $row);

// API Packages Imports
$moduleObjects_container = DOM::create("div", "", "imports", "moduleObjects noDisplay");
DOM::attr($moduleObjects_container, "style", "height:100%");
$sideMenuBuilder->addNavigationSelector($moduleObjects_container, $nav_targetgroup);
DOM::append($headersViewerWrapper, $moduleObjects_container);


$dtGridList = new dataGridList();
$glist = $dtGridList->build("", TRUE)->get();
DOM::append($moduleObjects_container, $glist);

$headers = array();
$headers[] = "Library";
$headers[] = "Package";

$dtGridList->setHeaders($headers);
$imports = $pageObject->getImports();

// Get All Packages
$ebldLib = new ebLibrary();
$packages = $ebldLib->getPackageList("", FALSE);

foreach ($packages as $lib => $pkgs)
{
	foreach ($pkgs as $pkg)
	{
		// Checkbox Value
		$checked = FALSE;
		if (is_array($imports[$lib]) && in_array($pkg, $imports[$lib]))
			$checked = TRUE;
		
		// Grid List Contents
		$gridRow = array();
		$gridRow[] = $lib;
		$gridRow[] = $pkg;
		
		$dtGridList->insertRow($gridRow, "imports[".$lib.','.$pkg.']', $checked);
	}
}


// Prepare report
// Send redWIDE Tab
$obj_id = "View_".$_GET['name'];
$header = "View::".$_GET['name'];
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($obj_id, $header, $HTMLContentBuilder->get());
//#section_end#
?>