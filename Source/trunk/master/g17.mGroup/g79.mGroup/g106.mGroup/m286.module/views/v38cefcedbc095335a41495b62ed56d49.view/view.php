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
importer::import("DEV", "Websites");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\notification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Websites\pages\wsPage;

// Get page variables
$websiteID = engine::getVar("id");
$pageFolder = engine::getVar("folder");
$pageName = engine::getVar("name");
if (engine::isPost())
{
	$errFormNtf = new formErrorNotification();
	$errFormNtf->build();
	
	// Remove website page
	$page = new wsPage($websiteID, $pageFolder, $pageName);
	$status = $page->remove();

	// If there is an error in creating the folder, show it
	if ($status !== TRUE)
	{
		$err_header = moduleLiteral::get($moduleID, "hd_deletePage");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", $status));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
	
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

$path = $pageFolder."/".$pageName;
$p = DOM::create("h4", $path);
$frame->append($p);

//_____ Website ID
$input = $form->getInput("hidden", "id", $websiteID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Page folder
$hidden = $form->getInput($type = "hidden", $name = "folder", $value = $pageFolder);
$form->append($hidden);
//_____ Page name
$hidden = $form->getInput($type = "hidden", $name = "name", $value = $pageName);
$form->append($hidden);

// Return the report
return $frame->getFrame();
//#section_end#
?>