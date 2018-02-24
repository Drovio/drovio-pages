<?php
//#section#[header]
// Module Declaration
$moduleID = 250;

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
importer::import("API", "Resources");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Resources\filesystem\fileManager;
use \API\Resources\filesystem\directory;
use \API\Resources\DOMParser;
use \UI\Modules\MContent;
use \DEV\Prototype\sourceMap;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "uiPreview");



// Load model map file
$modelFolder = systemRoot."/System/Resources/Model/SDK/";
$srcMap = new sourceMap($modelFolder);


// Get all objects and add model
$fullObjects = array();
$libraries = $srcMap->getLibraryList();
foreach ($libraries as $library)
{
	$packages = $srcMap->getPackageList($library);
	foreach ($packages as $package)
	{
		$objects = $srcMap->getObjectList($library, $package);
		foreach ($objects as $object)
			$fullObjects[] = $library."/".$package."/".str_replace("::", "/", $object['namespace'])."/".$object['name'];
	}
}

// Get all objects
asort($fullObjects);
foreach ($fullObjects as $object)
{
	// Get model first and if empty, continue
	$parser = new DOMParser();
	$modelXML = fileManager::get($modelFolder."/".$object.".xml");
	$parser->loadContent($modelXML, "xml");
	$modelNode = $parser->evaluate("//model")->item(0);
	$modelHtml = $parser->innerHTML($modelNode);
	if (empty($modelHtml))
		continue;
	
	// Create container
	$objContainer = DOM::create("div", "", "", "objectContainer");
	$pageContent->append($objContainer);
	
	$title = DOM::create("h4", directory::normalize($object), "", "title");
	DOM::append($objContainer, $title);
	
	$objModel = DOM::create("div", "", "", "objModel");
	DOM::innerHTML($objModel, $modelHtml);
	DOM::append($objContainer, $objModel);
}


// Return output
return $pageContent->getReport();
//#section_end#
?>