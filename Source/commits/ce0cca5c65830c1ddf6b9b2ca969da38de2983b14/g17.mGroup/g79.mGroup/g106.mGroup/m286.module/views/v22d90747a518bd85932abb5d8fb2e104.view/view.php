<?php
//#section#[header]
// Module Declaration
$moduleID = 286;

// Inner Module Codes
$innerModules = array();

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
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Prototype");
importer::import("DEV", "Websites");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Navigation\sideMenu;
use \UI\Presentation\dataGridList;
use \UI\Modules\MContent;
use \DEV\Prototype\sourceMap;
use \DEV\Websites\pages\wsPage;
use \DEV\Websites\source\srcLibrary;

// Get variables
$websiteID = $_GET['id'];
$pageFolder = $_GET['folder'];
$pageName = $_GET['name'];

$itemID = "p".$websiteID."_".$pageFolder."_".$pageName;
$itemID = str_replace("/", "_", $itemID);

// Initialize object
$pageObject = new wsPage($websiteID, $pageFolder, $pageName);

// Initialize content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("", "pageHeadersContainer", TRUE);


// Build Side Navigation Menu for Headers
$headersSideMenu = HTML::select(".pageHeadersContainer .headersSideMenu")->item(0);
$menu = new sideMenu();
$sidemenu = $menu->build()->get();
DOM::append($headersSideMenu, $sidemenu);


// Static Navigation Attributes
$targetcontainer = "headersViewerContainer_".$itemID;
$targetgroup = "headersGroup";
$navgroup = "headersNavGroup_".$itemID;
$display = "none";

$elementContent = moduleLiteral::get($moduleID, "lbl_pageInfo");
$menuElement = $menu->insertListItem("", $elementContent, TRUE);
$menu->addNavigation($menuElement, "pageInfo", $targetcontainer, $targetgroup, $navgroup, $display);

$elementContent = moduleLiteral::get($moduleID, "lbl_dependencies");
$menuElement = $menu->insertListItem("", $elementContent);
$menu->addNavigation($menuElement, "dependencies", $targetcontainer, $targetgroup, $navgroup, $display);

// Target container id
$headersViewer = HTML::select(".pageHeaders .headersViewer")->item(0);
HTML::attr($headersViewer, "id", $targetcontainer);


// Form builder
$form = new simpleForm();

// Page Info
$formContainer = HTML::select("#pageInfo")->item(0);
$menu->addNavigationSelector($formContainer, $targetgroup);

/*
// Page Info Editor
$title = moduleLiteral::get($moduleID, "lbl_pageTitle");
$input = $form->getInput($type = "text", "pageName", $pageName, $class = "", $autofocus = TRUE);
$row = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($formContainer, $row);
*/

// Page Dependencies
$formContainer = HTML::select("#dependencies")->item(0);
$menu->addNavigationSelector($formContainer, $targetgroup);

$gridList = new dataGridList();
$glist = $gridList->build("", TRUE)->get();
DOM::append($formContainer, $glist);

$headers = array();
$headers[] = "Library";
$headers[] = "Package";
$headers[] = "Type";
$gridList->setHeaders($headers);

// Get all dependencies
$dependencies = $pageObject->getDependencies();

// Set web core dependencies
$sourceMap = new sourceMap(systemRoot."/System/Library/Web/SDK/");
$libraries = $sourceMap->getLibraryList();

// Get All Packages
$packages = array();
foreach ($libraries as $library)
		$packages[$library] = $sourceMap->getPackageList($library);

foreach ($packages as $lib => $pkgs)
	foreach ($pkgs as $pkg)
	{
		$checked = FALSE;
		if (is_array($dependencies['sdk'][$lib]) && in_array($pkg, $dependencies['sdk'][$lib]))
			$checked = TRUE;
			
		// Grid List Contents
		$gridRow = array();
		$gridRow[] = $lib;
		$gridRow[] = $pkg;
		$gridRow[] = "Web Engine SDK";
		
		$gridList->insertRow($gridRow, "wsdk_dependencies[".$lib.','.$pkg.']', $checked);
	}
	
// Get All Website source libraries and packages
$srcLib = new srcLibrary($websiteID);
$libraries = $srcLib->getList();
$packages = array();
foreach ($libraries as $library)
		$packages[$library] = $srcLib->getPackageList($library);

foreach ($packages as $lib => $pkgs)
	foreach ($pkgs as $pkg)
	{
		$checked = FALSE;
		if (is_array($dependencies['ws'][$lib]) && in_array($pkg, $dependencies['ws'][$lib]))
			$checked = TRUE;
			
		// Grid List Contents
		$gridRow = array();
		$gridRow[] = $lib;
		$gridRow[] = $pkg;
		$gridRow[] = "Website Source";
		
		$gridList->insertRow($gridRow, "ws_dependencies[".$lib.','.$pkg.']', $checked);
	}

// Return output
return $pageContent->getReport();
//#section_end#
?>