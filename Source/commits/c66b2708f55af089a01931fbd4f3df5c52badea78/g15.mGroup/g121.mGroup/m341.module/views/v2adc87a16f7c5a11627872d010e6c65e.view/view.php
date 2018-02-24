<?php
//#section#[header]
// Module Declaration
$moduleID = 341;

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
importer::import("SYS", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Resources\pages\page;
use \SYS\Resources\pages\sitemap;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

if (engine::isPost())
{
	// Generate sitemap
	sitemap::generate();
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Build the module content
$pageContent->build("", "sitemapConfig", TRUE);

$formContainer = HTML::select(".sitemap .formContainer")->item(0);
$form = new simpleForm();
$sitemapForm = $form->build("", FALSE)->engageModule($moduleID)->get();
DOM::append($formContainer, $sitemapForm);

// Generate button
$title = moduleLiteral::get($moduleID, "lbl_generate");
$submit = $form->getSubmitButton($title, $id = "", $name = "");
$form->appendControl($submit);

// Return output
return $pageContent->getReport();
//#section_end#
?>