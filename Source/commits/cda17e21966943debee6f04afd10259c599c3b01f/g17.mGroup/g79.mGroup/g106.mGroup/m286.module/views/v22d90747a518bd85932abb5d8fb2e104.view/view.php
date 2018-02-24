<?php
//#section#[header]
// Module Declaration
$moduleID = 286;

// Inner Module Codes
$innerModules = array();

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
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Websites");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \DEV\Websites\pages\wsPage;
use \UI\Forms\templates\simpleForm;
use \UI\Navigation\sideMenu;
use \UI\Presentation\dataGridList;
use \UI\Modules\MContent;

// Get variables
$projectID = (is_null($_GET['pid']) || empty($_GET['pid'])) ? '' : $_GET['pid'];
$name = (is_null($_GET['name']) || empty($_GET['name'])) ? '' : $_GET['name'];

$itemID = $projectID."_".$name;

// Initialize object
$pageObject = new wsPage($projectID, $name);

// Initialize content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("", ""); 



// Page Settings
//_____ Headers Area Container
$headersContainer = DOM::create('div','','','headersContainer');
$pageContent->append($headersContainer);

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


$elementContent = moduleLiteral::get($moduleID, "lbl_pageInfo");
$menuElement = $nav_menu->insertListItem("", $elementContent, TRUE);
$nav_menu->addNavigation($menuElement, "moduleInfo", $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);

$elementContent = moduleLiteral::get($moduleID, "lbl_dependencies");
$menuElement = $nav_menu->insertListItem("", $elementContent);
$nav_menu->addNavigation($menuElement, "imports", $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);


//_____ Headers Content
$headersViewer = DOM::create("div", "", $nav_targetcontainer, "headersViewer");
DOM::append($headersWrapper, $headersViewer);

$headersViewerWrapper = DOM::create("div", "", "", "headersViewerWrapper");
DOM::append($headersViewer, $headersViewerWrapper);

// Module Info
$moduleInfo_container = DOM::create("div", "", "moduleInfo", "viewerPanel");
$nav_menu->addNavigationSelector($moduleInfo_container, $nav_targetgroup);
DOM::append($headersViewerWrapper, $moduleInfo_container);

/*

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
$dependencies = $pageObject->getDependencies();

// Get All Packages
$sdkLib = new sdkLibrary();
$libraries = $sdkLib->getList();
$packages = array();
foreach ($libraries as $library)
	$packages[$library] = $sdkLib->getPackageList($library);

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
*/


// Return output
return $pageContent->getReport();
//#section_end#
?>