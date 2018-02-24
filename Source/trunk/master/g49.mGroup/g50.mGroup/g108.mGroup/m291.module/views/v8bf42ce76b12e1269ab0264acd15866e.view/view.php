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
importer::import("DEV", "Core");
importer::import("DEV", "Prototype");
importer::import("DEV", "WebEngine");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \UI\Modules\MContent;
use \DEV\Core\sdk\sdkPackage;
use \DEV\WebEngine\sdk\webPackage;
use \DEV\Prototype\sourceMap;

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
$pageContent->build("", "similarObjectsContainer", TRUE);


// Objects from the same package
$sourceMap = NULL;
try
{
	switch ($objectDomain)
	{
		case "SDK":
			$sourceMap = new sdkPackage();
			$similarObjects = $sourceMap->getPackageObjects($objectLibrary, $objectPackage);
			break;
		case "WSDK":
			$sourceMap = new webPackage();
			$similarObjects = $sourceMap->getPackageObjects($objectLibrary, $objectPackage);
			break;
	}
}
catch (Exception $ex)
{
	$sourceMap = new sourceMap($folderPath = systemRoot."/System/Resources/Documentation/".$objectDomain."/");
	$similarObjects = $sourceMap->getObjectList($objectLibrary, $objectPackage);
}

// List similar objects
$sObjectContainer = HTML::select(".similarObjects")->item(0);
foreach ($similarObjects as $sObject)
{
	// Skip current object
	if ($sObject['namespace'] == $objectNamespace && $sObject['name'] == $objectName)
		continue;
	
	// Create object holder
	$objectNs = str_replace("::", "/", $sObject['namespace']);
	$href = url::resolve("developers", "/sdk/".$objectDomain."/".$sObject['library']."/".$sObject['package']."/".(empty($objectNs) ? "" : $objectNs."/").$sObject['name']);
	$sHolder = $pageContent->getWeblink($href, "", "_self", NULL, "", array(), "sHolder");
	DOM::append($sObjectContainer, $sHolder);
	
	// Add action to load manual
	$attr = array();
	$attr['domain'] = $objectDomain;
	$attr['lib'] = $sObject['library'];
	$attr['pkg'] = $sObject['package'];
	$attr['ns'] = $sObject['namespace'];
	$attr['oname'] = $sObject['name'];
	$actionFactory->setModuleAction($sHolder, $moduleID, "manualViewer", ".manualContainer", $attr);
	
	// Ico
	$sIco = DOM::create("div", "", "", "ico");
	DOM::append($sHolder, $sIco);
	
	// Title
	$sTitle = DOM::create("div", $sObject['name'], "", "sTitle");
	DOM::append($sHolder, $sTitle);
	
	// Full namespace
	$objectNs = str_replace("::", "\\", $sObject['namespace']);
	$fullName = $sObject['library']."\\".$sObject['package']."\\".(empty($objectNs) ? "" : $objectNs."\\").$sObject['name'];
	$sFullTitle = DOM::create("div", $fullName, "", "sfTitle");
	DOM::append($sHolder, $sFullTitle);
}

// Return the report
return $pageContent->getReport("#docViewer");
//#section_end#
?>