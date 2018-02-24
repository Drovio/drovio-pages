<?php
//#section#[header]
// Module Declaration
$moduleID = 175;

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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\appcenter\application;
use \API\Developer\appcenter\appManager;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\geoloc\locale;
use \UI\Html\HTMLContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\windowFrame;
use \UI\Navigation\sideMenu;

$content = new HTMLContent();
$actionFactory = $content->getActionFactory();

// Build the frame
$frame = new windowFrame("appLiteralManager");
$title = moduleLiteral::get($moduleID, "lbl_appLiteralsTitle");
$frame->build($title, "literalManager");

// Validate and Load application data
$appID = $_GET['appID'];
$applicationData = appManager::getApplicationData($appID);
if (is_null($applicationData))
{
	// Error Message
	$errorMessage = DOM::create("h2", "Application request not valid");
	$frame->append($errorMessage);
	return $frame->getFrame();
}

// Create application and get literal manager
$devApp = new application($appID);
$appLiterals = $devApp->getLiterals();
$sideMenu = new sideMenu();

// Build container
$globalContainer = DOM::create("div", "", "", "globalContainer");
$frame->append($globalContainer);

// Side menu
$side = DOM::create("div", "", "", "side");
DOM::append($globalContainer, $side);

// Fill in with locale
$active = locale::active();
$literalsDefaultLocale = $appLiterals->getDefaultLocale();
$friendlyLocale = array();
foreach ($active as $key => $locale)
	$friendlyLocale[$locale['locale']] = $locale['friendlyName'];

$header = DOM::create("span", "Default");
$defaultSideMenu = $sideMenu->build("defaultLocale", $header)->get();
DOM::append($side, $defaultSideMenu);

// Static navigation attributes
$targetcontainer = "";
$targetgroup = "";
$navgroup = "localeNav";
$display = "none";

// Insert default locale item
$itemHeader = DOM::create("span", $friendlyLocale[$literalsDefaultLocale]);
$menuItemContent = DOM::create("a", $itemHeader);
$menuItem = $sideMenu->insertListItem($id = $literalsDefaultLocale, $menuItemContent);
// Static Nav
$sideMenu->addNavigation($menuItem, "", $targetcontainer, $targetgroup, $navgroup, $display);
// Module Action
$attr = array();
$attr['appID'] = $appID;
$attr['locale'] = $literalsDefaultLocale;
$actionFactory->setModuleAction($menuItem, $moduleID, "literalEditor", ".literalEditor", $attr);


$header = DOM::create("span", "Active", "", "sideHeader");
$activeSideMenu = $sideMenu->build("activeLocale", $header)->get();
DOM::append($side, $activeSideMenu);

foreach ($friendlyLocale as $locale => $friendly)
	if ($locale != $literalsDefaultLocale)
	{
		$itemHeader = DOM::create("span", $friendly);
		$menuItemContent = DOM::create("a", $itemHeader);
		$menuItem = $sideMenu->insertListItem($id = $locale, $menuItemContent);
		// Static Nav
		$sideMenu->addNavigation($menuItem, "", $targetcontainer, $targetgroup, $navgroup, $display);
		// Module Action
		$attr = array();
		$attr['appID'] = $appID;
		$attr['locale'] = $locale;
		$actionFactory->setModuleAction($menuItem, $moduleID, "literalEditor", ".literalEditor", $attr);
	}
	
// Main Content
$main = DOM::create("div", "", "", "main");
DOM::append($globalContainer, $main);

// Literal editor
$literalEditor = DOM::create("div", "", "", "literalEditor");
DOM::append($main, $literalEditor);

// Literal EDitor Message
$guide = DOM::create("h3", "Choose the locale on the left and edit your application's literals.");
DOM::append($literalEditor, $guide);


// Literal Creator Content
$literalCreator = DOM::create("div", "Create New Literal", "", "literalCreator");
$frame->append($literalCreator);

$form = new simpleForm();
$createLiteralForm = $form->build($moduleID, "createNewLiteral", FALSE)->get();
DOM::append($literalCreator, $createLiteralForm);

// Application id
$input = $form->getInput($type = "hidden", $name = "appID", $value = $appID, $class = "", $autofocus = FALSE);
$form->append($input);

$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "newLitInput", $autofocus = FALSE, $required = FALSE);
DOM::attr($input, "placeholder", "Literal Name");
$form->append($input);

$input = $form->getInput($type = "text", $name = "value", $value = "", $class = "newLitInput", $autofocus = FALSE, $required = FALSE);
DOM::attr($input, "placeholder", "Literal Value");
$form->append($input);

$title = DOM::create("span", "create");
$submitBtn = $form->getSubmitButton($title, $id = "createLiteral");
$form->append($submitBtn);

// Return the report
return $frame->getFrame();
//#section_end#
?>