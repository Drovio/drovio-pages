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
importer::import("API", "Resources");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\core\manifests;
use \API\Resources\filesystem\directory;
use \UI\Modules\MPage;
use \UI\Presentation\notification;

// Get manual attributes
$objectDomain = $_GET['domain'];
$objectLibrary = $_GET['lib'];
$objectPackage = $_GET['pkg'];
$objectNamespace = trim($_GET['ns']);
$objectNamespace = trim($objectNamespace, "/");
$objectName = $_GET['oname'];
$objectName = (empty($objectName) ? $objectNamespace : $objectName);

// Normalize
$objectNamespace = str_replace("_", "/", $objectNamespace);
$objectNamespace = str_replace("::", "/", $objectNamespace);
$objectPath = "/".$objectLibrary."/".$objectPackage."/".$objectNamespace;

// Get protected libraries
if ($domain == "SDK")
{
	$coreManifests = manifests::getManifests();
	$corePermissions = array();
	foreach ($coreManifests as $mfID => $mfInfo)
		if (!$mfInfo['info']['private'])
			foreach ($mfInfo['packages'][$objectLibrary] as $packageName)
				if ($packageName == $objectPackage)
					$corePermissions[] = $mfInfo['info'];
}
				
// Initialize page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build page
$container = $page->build($objectName." | Manual", "sdkManualContainer", TRUE);


// Set manual sections
$items = array();
$items['cinfo'] = "classInfo";
$items['cref'] = "classReference";
$items['manual'] = "jsReference_Examples";
$items['model'] = "uiModel";
$items['changelog'] = "changelog";
$items['similar'] = "similarObjects";
$items['permissions'] = "appPermissions";
foreach ($items as $class => $viewName)
{
	// Set refID
	$refID = "ref_".$viewName;
	$targetgroup = "manGroup";
	
	// Set nav item
	$item = HTML::select(".sdkManual .pnavigation .navitem.".$class)->item(0);
	$page->setStaticNav($item, $refID, "bodyDetailsContainer", $targetgroup, "manavGroup", $display = "none");
	
	// Create target group module container
	$bodyDetailsContainer = HTML::select("#bodyDetailsContainer")->item(0);
	$mContainer = $page->getModuleContainer($moduleID, $viewName, $attr = array(), $startup = TRUE, $refID, $loading = FALSE, $preload = TRUE);
	DOM::append($bodyDetailsContainer, $mContainer);
	$page->setNavigationGroup($mContainer, $targetgroup);
}

// Check core permissions and remove item if necessary
if (empty($corePermissions) || $domain == "WSDK")
{
	$item = HTML::select(".docNavigation .navitem.permissions")->item(0);
	HTML::replace($item, NULL);
	
	$group = HTML::select("#appPermissions")->item(0);
	HTML::replace($group, NULL);
}

// Class Name
$classNameWrapper = HTML::select(".className")->item(0);
$span = DOM::create("span", $objectName);
DOM::append($classNameWrapper, $span);


// Return the report
return $page->getReport("#docViewer");
//#section_end#
?>