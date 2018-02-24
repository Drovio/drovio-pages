<?php
//#section#[header]
// Module Declaration
$moduleID = 327;

// Inner Module Codes
$innerModules = array();
$innerModules['developerHome'] = 100;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("BSS", "WebDocs");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Geoloc\locale;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;
use \UI\Presentation\notification;
use \BSS\WebDocs\wDoc;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the page content
$title = moduleLiteral::get($moduleID, "title");
$page->build($title, "featurePage", TRUE);

// Load document
$wDoc = new wDoc("Developer/Features", "ModelViewController");
$documentContent = $wDoc->load($locale = locale::get(), $public = TRUE, $teamID = 6);

$docViewer = HTML::select(".featureContent .doc_viewer")->item(0);
if (!empty($documentContent))
{
	DOM::innerHTML($docViewer, $documentContent);
}
else
{
	// Notification, document not found
	$ntf = new notification();
	$notification = $ntf->build(notification::ERROR, $header = TRUE, $disposable = FALSE)->get();
	
	$prompt = moduleLiteral::get($moduleID, "hd_docNotFound");
	$hd = DOM::create("h2", $prompt);
	$ntf->append($hd);
	
	$context = moduleLiteral::get($moduleID, "lbl_documentError");
	$context = DOM::create("p", $context);
	$ntf->append($context);
	
	DOM::append($docViewer, $notification);
}


// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['developerHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$featuresPage = HTML::select(".featureContainer")->item(0);
$footerMenu = module::loadView($innerModules['developerHome'], "footerMenu");
DOM::append($featuresPage, $footerMenu);

// Return output
return $page->getReport();
//#section_end#
?>