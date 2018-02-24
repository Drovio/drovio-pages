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

// Get page variables
$websiteID = engine::getVar('id');
$pageFolder = engine::getVar('folder');
$pageName = engine::getVar('name');

$itemID = "p".$websiteID."_".$pageFolder."_".$pageName;
$itemID = str_replace("/", "_", $itemID);

// Initialize object
$pageObject = new wsPage($websiteID, $pageFolder, $pageName);
$pageSettings = $pageObject->getSettings();

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
$pageInfo = HTML::select("#pageInfo")->item(0);
$menu->addNavigationSelector($pageInfo, $targetgroup);

// Page Information
$formContainer = HTML::select("#pageInfo .settings .sContainer")->item(0);

// Page Title
$title = moduleLiteral::get($moduleID, "lbl_pageTitle");
$value = $pageSettings['TITLE'];
$input = $form->getInput($type = "text", "settings[title]", $value, $class = "", $autofocus = TRUE, $required = FALSE);
$row = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($formContainer, $row);


// Meta Information
$formContainer = HTML::select("#pageInfo .meta .sContainer")->item(0);

// Meta Description
$title = moduleLiteral::get($moduleID, "lbl_metaDescription");
$value = $pageSettings['META_DESCRIPTION'];
$input = $form->getTextArea("settings[meta_description]", $value, "", $autofocus = FALSE, $required = TRUE);
$row = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($formContainer, $row);

// Meta Keywords
$title = moduleLiteral::get($moduleID, "lbl_metaKeywords");
$value = $pageSettings['META_KEYWORDS'];
$input = $form->getTextArea("settings[meta_keywords]", $value, "", $autofocus = FALSE, $required = TRUE);
$row = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($formContainer, $row);


// Open graph sitename
$title = moduleLiteral::get($moduleID, "lbl_meta_og_sitename");
$value = $pageSettings['META_OG_SITENAME'];
$input = $form->getInput($type = "text", "settings[meta_og_sitename]", $value, $class = "", $autofocus = TRUE);
$row = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($formContainer, $row);

// Open graph page title
$title = moduleLiteral::get($moduleID, "lbl_meta_og_title");
$value = $pageSettings['META_OG_TITLE'];
$input = $form->getInput($type = "text", "settings[meta_og_title]", $value, $class = "", $autofocus = TRUE);
$row = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($formContainer, $row);

// Open graph type
$title = moduleLiteral::get($moduleID, "lbl_meta_og_type");
$value = $pageSettings['META_OG_TYPE'];
$input = $form->getInput($type = "text", "settings[meta_og_type]", $value, $class = "", $autofocus = TRUE);
$row = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($formContainer, $row);

// Open graph image
$title = moduleLiteral::get($moduleID, "lbl_meta_og_image");
$value = $pageSettings['META_OG_IMAGE'];
$input = $form->getInput($type = "text", "settings[meta_og_image]", $value, $class = "", $autofocus = TRUE);
$row = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($formContainer, $row);

// Page robots
$title = moduleLiteral::get($moduleID, "lbl_pageRobots");
$value = $pageSettings['ROBOTS'];
$input = $form->getInput($type = "text", "settings[robots]", $value, $class = "", $autofocus = TRUE);
$row = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($formContainer, $row);



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