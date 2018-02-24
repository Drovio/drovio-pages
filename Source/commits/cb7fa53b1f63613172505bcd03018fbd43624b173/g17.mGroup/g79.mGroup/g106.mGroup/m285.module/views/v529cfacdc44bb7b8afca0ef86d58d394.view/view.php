<?php
//#section#[header]
// Module Declaration
$moduleID = 285;

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
use \DEV\Websites\website;
use \DEV\Websites\pages\wsPage;
use \DEV\Websites\pages\wsPageManager;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Navigation\sideMenu;
use \UI\Presentation\frames\windowFrame;

$websiteID = $_GET['id'];
 
// Create Application Content
$mContent = new MContent($moduleID);
$actionFactory = $mContent->getActionFactory();

// Build content
$mContent->build("", "addNewDialog", TRUE);
$sidebar = HTML::select(".addNewDialog .sidebar")->item(0);

// Create a sideMenu
$sMenu = new sideMenu();
$header = moduleLiteral::get($moduleID, "lbl_menuHeader");
$sideMenu = $sMenu->build("", $header)->get();
DOM::append($sidebar, $sideMenu);

$targetcontainer = "mainDialog";
$targetgroup = "menuGroup";
$navgroup = "navGroup";
$display = "none";

$title = moduleLiteral::get($moduleID, "lbl_folders");
$item = $sMenu->insertListItem("wsFolders", $title, TRUE);
$sMenu->addNavigation($item, $ref = "wsPage_folder", $targetcontainer, $targetgroup, $navgroup, $display);

$title = moduleLiteral::get($moduleID, "lbl_pages");
$item = $sMenu->insertListItem("wsPage", $title);
$sMenu->addNavigation($item, $ref = "wsPage_page", $targetcontainer, $targetgroup, $navgroup, $display);

// Set navigator selectors
$ref_element = HTML::select("#wsPage_folder")->item(0);
$sMenu->addNavigationSelector($ref_element, $targetgroup);

$ref_element = HTML::select("#wsPage_page")->item(0);
$sMenu->addNavigationSelector($ref_element, $targetgroup);


// Create form
$folderFormContainer = HTML::select(".dlgContainer.folders .formContainer")->item(0);
$form = new simpleForm();
$folderForm = $form->build()->engageModule($moduleID, "createFolder")->get();
DOM::append($folderFormContainer, $folderForm);

// Folder Parent
$folderResources = array();
$folderResources["-1"] = "/";
$pman = new wsPageManager($websiteID);
$pageFolders = $pman->getFolders("", TRUE);
foreach ($pageFolders as $fl)
	$folderResources[$fl] = $fl;
ksort($folderResources);
 

$input = $form->getInput($type = "hidden", $name = "id", $value = $websiteID, $class = "", $autofocus = TRUE);
$form->append($input);

$title = moduleLiteral::get($moduleID, "lbl_folderParent");
$label = $form->getLabel($title);
$input = $form->getResourceSelect($name = "parent", $multiple = FALSE, $class = "", $folderResources, $selectedValue = "");
$form->insertRow($title, $input, $required = TRUE, $notes = "");

$title = moduleLiteral::get($moduleID, "lbl_folderName");
$label = $form->getLabel($title);
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");


// Create form
$folderFormContainer = HTML::select(".dlgContainer.pages .formContainer")->item(0);
$form = new simpleForm();
$folderForm = $form->build()->engageModule($moduleID, "createPage")->get();
DOM::append($folderFormContainer, $folderForm);

$input = $form->getInput($type = "hidden", $name = "id", $value = $websiteID, $class = "", $autofocus = TRUE);
$form->append($input);

$title = moduleLiteral::get($moduleID, "lbl_folderParent");
$label = $form->getLabel($title);
$input = $form->getResourceSelect($name = "parent", $multiple = FALSE, $class = "", $folderResources, $selectedValue = "");
$form->insertRow($title, $input, $required = TRUE, $notes = "");

$title = moduleLiteral::get($moduleID, "lbl_pageName");
$label = $form->getLabel($title);
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");


// Build window frame
$wFrame = new windowFrame();
$title = moduleLiteral::get($moduleID, "lbl_createNew");
$wFrame->build($title);

$wFrame->append($mContent->get());
return $wFrame->getFrame();
//#section_end#
?>