<?php
//#section#[header]
// Module Declaration
$moduleID = 144;

// Inner Module Codes
$innerModules = array();
$innerModules['localeModule'] = 146;
$innerModules['countryModule'] = 145;
$innerModules['currencyModule'] = 147;
$innerModules['regionModule'] = 148;
$innerModules['languageModule'] = 150;
$innerModules['timezoneModule'] = 151;
$innerModules['townsModule'] = 152;

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
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \UI\Navigation\sideMenu;
use \UI\Presentation\dataGridList;
use \UI\Html\HTMLModulePage;


// Create Module Page
$page = new HTMLModulePage("TwoColumnsLeftSidebarCentered");
$actionFactory = $page->getActionFactory();

// Build the module
$page->build("Geolocation Manager", "geoloc");


// Build the side menu
$geoMenu = new sideMenu();
$geoMenuElement = $geoMenu->build($id = "", $header = "GEOLOCATION")->get();
$page->appendToSection("sidebar", $geoMenuElement);

// List items
$content = DOM::create("span", "Regions");
$listItem = $geoMenu->insertListItem($id = "regionItem", $content);
$geoMenu->addNavigation($listItem, $ref = "", $targetcontainer = "", $targetgroup = "", $navgroup = "geoNavGroup", $display = "none");
$actionFactory->setModuleAction($listItem, $innerModules['regionModule'], $action = "", $holder = "#geolocEditor", $attr = array());

$content = DOM::create("span", "Countries");
$listItem = $geoMenu->insertListItem($id = "countryItem", $content);
$geoMenu->addNavigation($listItem, $ref = "", $targetcontainer = "", $targetgroup = "", $navgroup = "geoNavGroup", $display = "none");
$actionFactory->setModuleAction($listItem, $innerModules['countryModule'], $action = "", $holder = "#geolocEditor", $attr = array());

$content = DOM::create("span", "Towns");
$listItem = $geoMenu->insertListItem($id = "townsItem", $content);
$geoMenu->addNavigation($listItem, $ref = "", $targetcontainer = "", $targetgroup = "", $navgroup = "geoNavGroup", $display = "none");
$actionFactory->setModuleAction($listItem, $innerModules['townsModule'], $action = "", $holder = "#geolocEditor", $attr = array());

$content = DOM::create("span", "Timezones");
$listItem = $geoMenu->insertListItem($id = "timezoneItem", $content);
$geoMenu->addNavigation($listItem, $ref = "", $targetcontainer = "", $targetgroup = "", $navgroup = "geoNavGroup", $display = "none");
$actionFactory->setModuleAction($listItem, $innerModules['timezoneModule'], $action = "", $holder = "#geolocEditor", $attr = array());

$content = DOM::create("span", "Currency");
$listItem = $geoMenu->insertListItem($id = "currencyItem", $content);
$geoMenu->addNavigation($listItem, $ref = "", $targetcontainer = "", $targetgroup = "", $navgroup = "geoNavGroup", $display = "none");
$actionFactory->setModuleAction($listItem, $innerModules['currencyModule'], $action = "", $holder = "#geolocEditor", $attr = array());

$content = DOM::create("span", "Languages");
$listItem = $geoMenu->insertListItem($id = "languageItem", $content);
$geoMenu->addNavigation($listItem, $ref = "", $targetcontainer = "", $targetgroup = "", $navgroup = "geoNavGroup", $display = "none");
$actionFactory->setModuleAction($listItem, $innerModules['languageModule'], $action = "", $holder = "#geolocEditor", $attr = array());

$content = DOM::create("span", "Locale");
$listItem = $geoMenu->insertListItem($id = "localeItem", $content);
$geoMenu->addNavigation($listItem, $ref = "", $targetcontainer = "", $targetgroup = "", $navgroup = "geoNavGroup", $display = "none");
$actionFactory->setModuleAction($listItem, $innerModules['localeModule'], $action = "", $holder = "#geolocEditor", $attr = array());




// Grid List Holder
$geolocEditorHolder = DOM::create("div", "", "geolocEditor");
$page->appendToSection("mainContent", $geolocEditorHolder);


// Return output
return $page->getReport();
//#section_end#
?>