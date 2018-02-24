<?php
//#section#[header]
// Module Declaration
$moduleID = 292;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("BSS", "WebDocs");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Geoloc\locale;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Presentation\notification;
use \BSS\WebDocs\wDoc;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "docViewerContainer", TRUE);

// Load document
$public = ($_GET['public'] == 1);
$docFolder = $_GET['f'];
$docName = $_GET['doc'];
$wDoc = new wDoc($docFolder, $docName, $public, $teamID = 6);
$documentContent = $wDoc->load($locale = locale::get());

if (!empty($documentContent))
{
	$docViewer = $pageContent->get();
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
	
	$pageContent->append($notification);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>