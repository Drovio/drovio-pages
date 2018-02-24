<?php
//#section#[header]
// Module Declaration
$moduleID = 388;

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
use \DEV\WebTemplates\templatePage;

$templateID = engine::getVar('id');
$pageName = engine::getVar('name');
if (engine::isPost())
{
	// Create form error Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Delete template page
	$page = new templatePage($templateID, $pageName);
	$status = $page->remove();

	// If there is an error in creating the folder, show it
	if (!$status)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_deletePage");
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
$title = moduleLiteral::get($moduleID, "hd_deletePage");
$frame->build($title, "", FALSE)->engageModule($moduleID, "deletePage");
$form = $frame->getFormFactory();

// Header
$title = moduleLiteral::get($moduleID, "lbl_deletePage");
$hd = DOM::create("h2", $title);
$frame->append($hd);

$path = "/Pages/".$pageName.".view";
$p = DOM::create("h4", $path);
$frame->append($p);

// Template id
$input = $form->getInput($type = "hidden", $name = "id", $value = $templateID, $class = "", $autofocus = FALSE);
$form->append($input);
// Page name
$input = $form->getInput($type = "hidden", $name = "name", $value = $pageName, $class = "", $autofocus = FALSE);
$form->append($input);


// Return the report
return $frame->getFrame();
//#section_end#
?>