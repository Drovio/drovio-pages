<?php
//#section#[header]
// Module Declaration
$moduleID = 34;

// Inner Module Codes
$innerModules = array();
$innerModules['deleteModule'] = 64;
$innerModules['multilingual'] = 60;
$innerModules['saveModule'] = 108;
$innerModules['commitModule'] = 38;

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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Developer\components\sdk\sdkLibrary;
use \API\Developer\components\moduleObject;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\DOMParser;
use \API\Security\account;
use \INU\Developer\redWIDE;
use \INU\Developer\codeEditor;
use \UI\Forms\templates\simpleForm;
use \UI\Navigation\sidebar;
use \UI\Navigation\sideMenu;
use \UI\Navigation\toolbarComponents\toolbarItem;
use \UI\Navigation\toolbarComponents\toolbarSeparator;
use \UI\Presentation\dataGridList;
use \UI\Html\HTMLContent;

//__________ [Page GET Variables] __________//

$module_id = $_GET['id'];
$seed = $_GET['seed'];

if (!isset($module_id) || is_null($module_id))
	return $reporter->content_not_found(TRUE);

//$form_builder = new simpleForm();
$sForm = new simpleForm();
$dbc = new interDbConnection();

$moduleObject = new moduleObject($module_id);

// Get Module | Auxiliary
$module = $moduleObject->getModule("", $seed);
	
// Load Module Code 
// Initialize PHP Editor
$codeEditor = new codeEditor();
$content = $module->getSourceCode();
$editor = $codeEditor->build("php", $content)->get();
// Editor Form 
// Create Module Page
$HTMLContentBuilder = new HTMLContent();
$actionFactory = $HTMLContentBuilder->getActionFactory();
// Initialize Editor Form
$sForm->build($innerModules['saveModule'], "", $controls = FALSE);
// Append form to Content
$submit_editorArea_form = $sForm->get();
$HTMLContentBuilder->buildElement($submit_editorArea_form);


// Get Module Data from Database
$attr = array();
$attr['plc'] = $module_id;

// Module Attributes
$module_attr = array();
$module_attr['id'] = $module_id;
$module_attr['title'] = $module->getTitle();
if (!isset($seed) || trim($seed) == '')
	$module_attr['ref'] = "m_".$module_id;
else
{
	$module_attr['parentTitle'] = $module->getParentTitle();
	$module_attr['ref'] = "aux_".$module_id."_".$seed;
}
DOM::data($submit_editorArea_form, "module", $module_attr);

// Hidden Values
// Module Id
$input = $sForm->getInput("hidden", "moduleId", $module_id, $class = "", $autofocus = FALSE);
$sForm->append($input);

// Auxiliary Seed
$input = $sForm->getInput("hidden", "auxSeed", $seed, $class = "", $autofocus = FALSE);
$sForm->append($input);
// Editor Area 
$form_content_wrapper = DOM::create("div", "", "", "editorAreaFormContent");
$sForm->append($form_content_wrapper);

// Settings Headers
//_____ Headers Area Container
$headersContainer = DOM::create('div','','','headersContainer');
$sForm->append($headersContainer);

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
$nav_targetcontainer = "headersViewer_".$module_attr['ref'];
$nav_targetgroup = "headersGroup";
$nav_navgroup = "headersNavGroup_".$module_attr['ref'];
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
$input = $sForm->getInput($type = "text", "moduleTitle", $module->getTitle(), $class = "", $autofocus = TRUE);
$row = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($moduleInfo_container, $row);

// Module Description
$title = moduleLiteral::get($moduleID, "lbl_moduleDescription"); 
$input = $sForm->getTextarea("moduleDescription", $module->getDescription());
$row = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
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
//$headers[] = "Include";
$headers[] = "Library";
$headers[] = "Package";

$dtGridList->set_headers($headers);
$imports = $module->getImports();

// Get All Packages
$sdkLib = new sdkLibrary();
$packages = $sdkLib->getPackageList("", FALSE);

foreach ($packages as $lib => $pkgs)
{
	foreach ($pkgs as $pkg)
	{
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
//$headers[] = "Delete";
$headers[] = "Index";
$headers[] = "Module";

$dtGridList->set_headers($headers);

foreach ($module->getInnerModules() as $key => $iCode)
{
	// Create Checkbox
	//$fgroup = $sForm->get_form_input("input", $title = "", $name = 'inner['.$key.']', $value = "off", $type = "checkbox", $class = "", $required = FALSE, $autofocus = FALSE);
	
	
	// Grid List Contents
	$gridRow = array();
	//$gridRow[] = $fgroup['element'];
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
	$input = $sForm->getInput($type = "text", $name = "inner[".$i."][title]", $value = "", $class = "", $autofocus = FALSE);
	$gridRow[] = $input;
	
	// Module
	$input = $sForm->getResourceSelect($name = "inner[".$i."][moduleId]", $multiple = FALSE, $class = "", $modules_resource, $selectedValue = 0);
	$gridRow[] = $input;
	
	$dtGridList->insertRow($gridRow);	
}

// Editor Container
//$editorContainer = DOM::create("div", "", "", "editorContainer");
//$editorContainer = DOM::create("div", "", "", "");
//DOM::append($form_content_wrapper, $editorContainer);
//DOM::append($editorContainer, $editor);
DOM::append($form_content_wrapper, $editor);

// ##Toolbar
// Toolbar Control 
$tlb = new sidebar();
$tlbItemBuilder = new toolbarItem();
$tlbSeperatorBuilder = new toolbarSeparator();
// Create Source Code Manager Toolbar;
$sideToolArea = $tlb->build($dock = "L", $form_content_wrapper)->get();
DOM::append($form_content_wrapper, $sideToolArea); 


// Side Controls
//$sideToolArea = DOM::create("div", "", "", "sideToolArea");
//DOM::append($form_content_wrapper, $sideToolArea);

//_____ Code Group Tools
$codeGroup = DOM::create("div", "", "", "toolGroup codeGroup");
DOM::append($sideToolArea, $codeGroup);

// Save
$content = DOM::create("button", "",  "saveModule_".$module_attr['ref'], "sideTool save saveModule");
DOM::attr($content, "type", "submit");
$saveTool = $tlbItemBuilder->build($content)->get();
DOM::append($codeGroup, $saveTool); 

// Commit
$content = DOM::create("div", "", "", "sideTool commit");
$commitTool = $tlbItemBuilder->build($content)->get();
$attr = array();
$attr['moduleId'] = $module_id;
$attr['auxSeed'] = $seed;

$moduleName = "";
if (isset($module_attr['parentTitle']))
	$moduleName = $module_attr['parentTitle']."_";
$moduleName = $moduleName.$module_attr['title'];
$attr['moduleName'] = $moduleName;

$actionFactory->setModuleAction($commitTool, $innerModules['commitModule'], "", "", $attr);
DOM::append($codeGroup, $commitTool); 

// Separator
$tlb->insertSeparator();

// Info Group Tools
$infoGroup = DOM::create("div", "", "", "toolGroup infoGroup");
DOM::append($sideToolArea, $infoGroup);

// Tools
$content = DOM::create("div", "", "", "sideTool headers");
$displayHeaders = $tlbItemBuilder->build($content)->get();
DOM::append($infoGroup, $displayHeaders);

// Tools
$content = DOM::create("div", "", "", "sideTool mlgMan");
$mlg = $tlbItemBuilder->build($content)->get();
DOM::append($infoGroup, $mlg);
$attr = array();
$attr['moduleId'] = $module_id;
$actionFactory->setModuleAction($mlg, $innerModules['multilingual'], "", "", $attr);

$header = $module_attr['title'];
if (isset($module_attr['parentTitle']))
	$header .= "[".$module_attr['parentTitle']."]";
$WIDETab = new redWIDE();
return $WIDETab->getReportContent($module_attr['ref']."_tab", $header, $HTMLContentBuilder->get());
//#section_end#
?>