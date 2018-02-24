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
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\core\manifests;
use \UI\Modules\MContent;

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

// Get protected libraries
$coreManifests = manifests::getManifests();
$corePermissions = array();
foreach ($coreManifests as $mfID => $mfInfo)
	if (in_array($objectPackage, $mfInfo['packages'][$objectLibrary]))
		$corePermissions[$mfID] = $mfInfo['info'];
				
// Initialize page content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("", "appPermissionsContainer", TRUE);

$pgiList = HTML::select(".appPermissions .list")->item(0);
foreach ($corePermissions as $mfID => $mfInfo)
{
	// Create permission group item
	$pgi = DOM::create("li", "", "", "pgi");
	DOM::append($pgiList, $pgi);
	
	// Manifest Icon
	$icon = DOM::create("div", "", "", "icon");
	DOM::append($pgi, $icon);
	if (isset($mfInfo['icon_url']))
	{
		// Create image
		$img = DOM::create("img");
		DOM::attr($img, "src", $mfInfo['icon_url']);
		DOM::append($icon, $img);
	}
	
	// Manifest info
	$info = DOM::create("div", "", "", "mf_info");
	DOM::append($pgi, $info);
	
	// Manifest title
	$title = DOM::create("div", $mfInfo['title'], "", "title");
	DOM::append($info, $title);
	
	// Manifest description
	$desc = DOM::create("div", $mfInfo['description'], "", "desc");
	DOM::append($info, $desc);
}

// Return the report
return $pageContent->getReport("#docViewer");
//#section_end#
?>