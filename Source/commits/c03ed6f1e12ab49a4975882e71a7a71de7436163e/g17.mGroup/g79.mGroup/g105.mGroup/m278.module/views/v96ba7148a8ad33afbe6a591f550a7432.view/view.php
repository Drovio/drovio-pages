<?php
//#section#[header]
// Module Declaration
$moduleID = 278;

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
use \UI\Modules\MContent;
use \UI\Navigation\sideMenu;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\frames\windowFrame;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \DEV\Websites\source\srcLibrary;

// Create Application Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("", "createNewDialog", TRUE);
$sidebar = HTML::select(".createNewDialog .sidebar")->item(0);

// Create a sideMenu
$sMenu = new sideMenu();
$header = moduleLiteral::get($moduleID, "lbl_menuHeader");
$sideMenu = $sMenu->build("", $header)->get();
DOM::append($sidebar, $sideMenu);

$targetcontainer = "mainDialog";
$targetgroup = "menuGroup";
$navgroup = "navGroup";
$display = "none";

$title = moduleLiteral::get($moduleID, "mi_library");
$item = $sMenu->insertListItem("ws_library", $title, TRUE);
$sMenu->addNavigation($item, $ref = "ws_newLibrary", $targetcontainer, $targetgroup, $navgroup, $display);

$title = moduleLiteral::get($moduleID, "mi_package");
$item = $sMenu->insertListItem("ws_package", $title, FALSE);
$sMenu->addNavigation($item, $ref = "ws_newPackage", $targetcontainer, $targetgroup, $navgroup, $display);

$title = moduleLiteral::get($moduleID, "mi_namespace");
$item = $sMenu->insertListItem("ws_namespace", $title, FALSE);
$sMenu->addNavigation($item, $ref = "ws_newNamespace", $targetcontainer, $targetgroup, $navgroup, $display);

$title = moduleLiteral::get($moduleID, "mi_object");
$item = $sMenu->insertListItem("ws_object", $title, FALSE);
$sMenu->addNavigation($item, $ref = "ws_newObject", $targetcontainer, $targetgroup, $navgroup, $display);


// Set navigator selectors
$ref_element = HTML::select("#ws_newLibrary")->item(0);
$sMenu->addNavigationSelector($ref_element, $targetgroup);

$ref_element = HTML::select("#ws_newPackage")->item(0);
$sMenu->addNavigationSelector($ref_element, $targetgroup);

$ref_element = HTML::select("#ws_newNamespace")->item(0);
$sMenu->addNavigationSelector($ref_element, $targetgroup);

$ref_element = HTML::select("#ws_newObject")->item(0);
$sMenu->addNavigationSelector($ref_element, $targetgroup);



// ----- Create New Library ----- //
// Create form
$formContainer = HTML::select(".dlgContainer.library .formContainer")->item(0);
$form = new simpleForm();
$libForm = $form->build()->engageModule($moduleID, "createLibrary")->get();
DOM::append($formContainer, $libForm);

// Website id
$input = $form->getInput($type = "hidden", $name = "wid", $value = $_GET['id'], $class = "", $autofocus = FALSE);
$form->append($input);

// Library Name
$title = literal::dictionary("name");
$input = $form->getInput($type = "text", $name = "libName", $value = "", $class = "", $autofocus = FALSE);
$libRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$form->append($libRow);




// ----- Create New Package ----- //
// Create form
$formContainer = HTML::select(".dlgContainer.package .formContainer")->item(0);
$form = new simpleForm();
$pkgForm = $form->build()->engageModule($moduleID, "createPackage")->get();
DOM::append($formContainer, $pkgForm);

// Website id
$input = $form->getInput($type = "hidden", $name = "wid", $value = $_GET['id'], $class = "", $autofocus = FALSE);
$form->append($input);

// Library Name
$srcLib = new srcLibrary($_GET['id']);
$libraries = $srcLib->getList();
$title = moduleLiteral::get($moduleID, "lbl_libraryName");
$input = $form->getResourceSelect($name = "library", $multiple = FALSE, $class = "", $libraries, $selectedValue = "");
$libRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$form->append($libRow);

// Package
$title = moduleLiteral::get($moduleID, "lbl_package");
$input = $form->getInput($type = "text", $name = "packageName", $value = "", $class = "", $autofocus = FALSE);
$pkgRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$form->append($pkgRow);




// ----- Create New Namespace ----- //
// Create form
$formContainer = HTML::select(".dlgContainer.namespace .formContainer")->item(0);
$form = new simpleForm();
$nsForm = $form->build()->engageModule($moduleID, "createNamespace")->get();
DOM::append($formContainer, $nsForm);

// Website id
$input = $form->getInput($type = "hidden", $name = "wid", $value = $_GET['id'], $class = "", $autofocus = FALSE);
$form->append($input);

// Library Name
$srcLib = new srcLibrary($_GET['id']);
$libraries = $srcLib->getList();
$packages = array();
foreach ($libraries as $library)
{
	$libPackages = $srcLib->getPackageList($library);
	foreach ($libPackages as $package)
		$packages[$library."::".$package] = $library." > ".$package;
}
$title = moduleLiteral::get($moduleID, "lbl_package");
$input = $form->getResourceSelect($name = "package", $multiple = FALSE, $class = "", $packages, $selectedValue = "");
$libRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$form->append($libRow);

// Parent Namespace
$notes = moduleLiteral::get($moduleID, "lbl_namespaceNotes");
$title = moduleLiteral::get($moduleID, "lbl_parentNamespace");
$input = $form->getInput($type = "text", $name = "parentNs", $value = "", $class = "", $autofocus = FALSE);
$nsRow = $form->buildRow($title, $input, $required = FALSE, $notes);
$form->append($nsRow);

// Namespace
$title = moduleLiteral::get($moduleID, "lbl_namespace");
$input = $form->getInput($type = "text", $name = "nsName", $value = "", $class = "", $autofocus = FALSE);
$objRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$form->append($objRow);



// ----- Create New Object ----- //
// Create form
$formContainer = HTML::select(".dlgContainer.object .formContainer")->item(0);
$form = new simpleForm();
$objForm = $form->build()->engageModule($moduleID, "createObject")->get();
DOM::append($formContainer, $objForm);

// Website id
$input = $form->getInput($type = "hidden", $name = "wid", $value = $_GET['id'], $class = "", $autofocus = FALSE);
$form->append($input);

// Library Name
$title = moduleLiteral::get($moduleID, "lbl_package");
$input = $form->getResourceSelect($name = "package", $multiple = FALSE, $class = "", $packages, $selectedValue = "");
$libRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$form->append($libRow);

// Namespace
$notes = moduleLiteral::get($moduleID, "lbl_namespaceNotes");
$title = moduleLiteral::get($moduleID, "lbl_namespace");
$input = $form->getInput($type = "text", $name = "namespace", $value = "", $class = "", $autofocus = FALSE);
$nsRow = $form->buildRow($title, $input, $required = FALSE, $notes);
$form->append($nsRow);

// Object Name
$title = literal::dictionary("name");
$input = $form->getInput($type = "text", $name = "objectName", $value = "", $class = "", $autofocus = FALSE);
$objRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$form->append($objRow);

// Object Description
$title = literal::dictionary("description");
$input = $form->getTextarea($name = "objectDesc", $value = "", $class = "");
$objDescRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
$form->append($objDescRow);




// Build window frame
$wFrame = new windowFrame();
$title = moduleLiteral::get($moduleID, "lbl_createNewSouceItem");
$wFrame->build($title);

$wFrame->append($pageContent->get());
return $wFrame->getFrame();
//#section_end#
?>