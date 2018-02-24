<?php
//#section#[header]
// Module Declaration
$moduleID = 291;

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
importer::import("DEV", "Documentation");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Geoloc\datetimer;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Presentation\notification;
use \DEV\Documentation\classDocumentor;

// Get manual attributes
$objectDomain = $_GET['domain'];
$objectLibrary = $_GET['lib'];
$objectPackage = $_GET['pkg'];
$objectNamespace = trim($_GET['ns']);
$objectNamespace = trim($objectNamespace, "/");
$objectName = $_GET['oname'];
$objectName = (empty($objectName) ? $objectNamespace : $objectName);

// Normalize variables and get object full path
$objectNamespace = str_replace("_", "/", $objectNamespace);
$objectNamespace = str_replace("::", "/", $objectNamespace);
$objectPath = "/".$objectLibrary."/".$objectPackage."/".$objectNamespace;


// Initialize page content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("", "classChangelogContainer", TRUE);


// Initialize class documentor
$classMan = new classDocumentor();
$referenceFilePath = "/System/Resources/Documentation/".$objectDomain."/".$objectPath."/".$objectName.".php.xml";

// Load class documentation
try
{
	$classMan->loadFile($referenceFilePath);
}
catch (Exception $ex)
{
	// Create document not found notification
	$ntf = new notification();
	$depNtf = $ntf->build(notification::ERROR, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Add header
	$header = DOM::create("h2", "Documentation Error.");
	$ntf->append($header);
	
	// Add message
	$message = DOM::create("p", "The class documentation file not found.");
	$ntf->append($message);
	
	// Add notification to description
	$errorNtf = $ntf->get();
	$pageContent->append($errorNtf);
	
	// Exception content
	return $pageContent->getReport("#docViewer");
}

// Get inheritance hierarchy
$classInfo = $classMan->getInfo();


// Class Changelog
$literal = moduleLiteral::get($moduleID, "lbl_version");
$version = DOM::create("b", (empty($classInfo['version']) ? "0.0-0" : $classInfo['version']."-".$classInfo['build']), "", "bxtl");
$box = HTML::select(".classChangelog .version")->item(0);
DOM::append($box, $version);
DOM::append($box, $literal);

$literal = moduleLiteral::get($moduleID, "lbl_createdAt");
$live = DOM::create("b", datetimer::live($classInfo['datecreated']), "", "bxtl");
$box = HTML::select(".classChangelog .created")->item(0);
DOM::append($box, $live);
DOM::append($box, $literal);

$literal = moduleLiteral::get($moduleID, "lbl_updatedAt");
$live = DOM::create("b", datetimer::live($classInfo['daterevised']), "", "bxtl");
$box = HTML::select(".classChangelog .updated")->item(0);
DOM::append($box, $live);
DOM::append($box, $literal);


// Return the report
return $pageContent->getReport("#docViewer");
//#section_end#
?>