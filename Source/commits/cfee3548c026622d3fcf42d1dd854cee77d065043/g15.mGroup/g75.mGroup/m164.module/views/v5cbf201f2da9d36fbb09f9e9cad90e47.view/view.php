<?php
//#section#[header]
// Module Declaration
$moduleID = 164;

// Inner Module Codes
$innerModules = array();

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
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Navigation\sideMenu;


$pageTitle = 'Communication';//moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE);
$HTMLModulePage = new HTMLModulePage("TwoColumnsLeftSidebarCentered");
$HTMLModulePage->build($pageTitle);
$actionFactory = $HTMLModulePage->getActionFactory();

// Static Navigation Attributes
$nav_ref = "toolBarNavigationMenu";
$nav_targetcontainer = "toolBarNavigationMenu";
$nav_targetgroup = "toolBarNavigationMenu";
$nav_navgroup = "toolBarNavigationMenu";

$sideMenu = new sideMenu();
$sideMenu->build('navigationSideMenu');
$HTMLModulePage->appendToSection('sidebar', $sideMenu->get());

$menuElement = DOM::create('span', 'eMail');//moduleLiteral::get($moduleID, "lbl_literalManager");
$ref = 'emailSender';
$sideMenu->addNavigation($menuElement, $ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
$item = $sideMenu->insertListItem("", $menuElement);
DOM::appendAttr($item, 'class', 'selected');


$container = DOM::create('div');
$HTMLModulePage->appendToSection('mainContent', $container);

$eMailSenderContainer = $HTMLModulePage->getModuleContainer($moduleID, "emailSender", $attr = array(), $startup = TRUE, 'emailSender');
$sideMenu->addNavigationSelector($eMailSenderContainer, $nav_targetgroup);
DOM::append($container, $eMailSenderContainer);

// Return the report
return $HTMLModulePage->getReport(HTMLModulePage::getPageHolder());
//#section_end#
?>