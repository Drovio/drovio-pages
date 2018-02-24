<?php
//#section#[header]
// Module Declaration
$moduleID = 390;

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
importer::import("DEV", "WebTemplates");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\WebTemplates\templateThemeJS;

$templateID = engine::getVar("id");
$themeName = engine::getVar("tname");
$jsName = engine::getVar("js_name");
$themeJS = new templateThemeJS($templateID, $themeName, $jsName);
if (engine::isPost())
{
	// Create form error Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Delete theme css
	$status = $themeJS->remove();

	// If there is an error in creating the folder, show it
	if (!$status)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_deleteJs");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", $status));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_deleteJS");
$frame->build($title, "", FALSE)->engageModule($moduleID, "deleteJS");
$form = $frame->getFormFactory();

// Header
$title = moduleLiteral::get($moduleID, "lbl_deleteJs");
$hd = DOM::create("h2", $title);
$frame->append($hd);

$path = "/Themes/".$themeName.".theme/".$jsName.".js";
$p = DOM::create("h4", $path);
$frame->append($p);

// Template id
$input = $form->getInput($type = "hidden", $name = "id", $value = $templateID, $class = "", $autofocus = FALSE);
$form->append($input);
// Template name
$hidden = $form->getInput($type = "hidden", $name = "tname", $value = $themeName);
$form->append($hidden);
// Css name
$hidden = $form->getInput($type = "hidden", $name = "js_name", $value = $jsName);
$form->append($hidden);

// Return the report
return $frame->getFrame();
//#section_end#
?>