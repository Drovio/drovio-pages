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
importer::import("DEV", "Documentation");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\core\manifests;
use \API\Resources\filesystem\directory;
use \UI\Modules\MPage;
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

// Normalize
$objectNamespace = str_replace("_", "/", $objectNamespace);
$objectNamespace = str_replace("::", "/", $objectNamespace);
$objectPath = "/".$objectLibrary."/".$objectPackage."/".$objectNamespace;

// Get protected libraries
$coreManifests = manifests::getManifests();
$corePermissions = array();
foreach ($coreManifests as $mfID => $mfInfo)
	if (!$mfInfo['info']['private'])
		foreach ($mfInfo['packages'][$objectLibrary] as $packageName)
			if ($packageName == $objectPackage)
				$corePermissions[] = $mfInfo['info'];
				
// Initialize page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build page
$container = $page->build($objectName." | Manual", "sdkManualContainer", TRUE);


// Set manual sections
$items = array();
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
	$item = HTML::select(".docNavigation .navitem.".$class)->item(0);
	$page->setStaticNav($item, $refID, "bodyDetailsContainer", $targetgroup, "manavGroup", $display = "none");
	
	// Create target group module container
	$bodyDetailsContainer = HTML::select("#bodyDetailsContainer")->item(0);
	$mContainer = $page->getModuleContainer($moduleID, $viewName, $attr = array(), $startup = TRUE, $refID, $loading = FALSE, $preload = TRUE);
	DOM::append($bodyDetailsContainer, $mContainer);
	$page->setNavigationGroup($mContainer, $targetgroup);
}

// Check core permissions and remove item if necessary
if (empty($corePermissions))
{
	$item = HTML::select(".docNavigation .navitem.permissions")->item(0);
	HTML::replace($item, NULL);
	
	$group = HTML::select("#appPermissions")->item(0);
	HTML::replace($group, NULL);
}
				

// Initialize class documentor
$classMan = new classDocumentor();
$docRoot = "/System/Resources/Documentation/";
$referenceFilePath = $docRoot.$objectDomain."/".$objectPath."/".$objectName.".php.xml";

// Load manual
$error_man = FALSE;
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
	$page->append($errorNtf);
	
	// Exception content
	return $page->getReport("#docViewer");
}

// Get inheritance hierarchy
$classInfo = $classMan->getInfo();
$extends = $classInfo['extends'];
$inheritance = array();
$p = directory::normalize($objectPath."/".$objectName);
$inheritance[] = str_replace("/", "\\", $p);
while (!empty($extends))
{
	// Add parent into inheritance hierarchy
	$inheritance[] = directory::normalize($extends);
	
	// Load parent's documentation
	$objectPath = str_replace("\\", "/", $extends);
	$referenceFilePath = $docRoot.$objectDomain."/".$objectPath.".php.xml";
	$parentClassMan = new classDocumentor();
	
	try
	{
		$parentClassMan->loadFile($referenceFilePath);
		$parentClassInfo = $parentClassMan->getInfo();
		$extends = $parentClassInfo['extends'];
	}
	catch (Exception $ex)
	{
		$extends = NULL;
	}
}



// Get Class Info
$classInfo = $classMan->getInfo();

// Class Name
$classNameWrapper = HTML::select(".className")->item(0);
$span = DOM::create("span", $objectName);
DOM::append($classNameWrapper, $span);

$classTitleWrapper = HTML::select(".classTitle")->item(0);
$span = DOM::create("span", $classInfo['title']);
DOM::append($classTitleWrapper, $span);

$classDescWrapper = HTML::select(".classDescription")->item(0);
$span = DOM::create("span", $classInfo['description']);
DOM::append($classDescWrapper, $span);
DOM::append($paragraph, $classDesc);

// Class inheritance (if any)
$wrapper = HTML::select(".inheritance")->item(0);
$i = 0;
$rinheritance = array_reverse($inheritance);
foreach ($rinheritance as $object)
{
	$title = DOM::create("span");
	DOM::innerHtml($title, str_repeat("  ", $i)."&rarr; ".$object."\n");
	DOM::append($wrapper, $title);
	$i++;
}

// Class Implements
if (!empty($classInfo['implements'])) 
{ 
	$wrapper = HTML::select(".classImplements")->item(0);
	$title = DOM::create("span", "implements: ".$classInfo['implements']);
	DOM::append($wrapper, $title);
}

if (!empty($classInfo['deprecated']))
{
	// Create deprecated notification
	$ntf = new notification();
	$depNtf = $ntf->build(notification::WARNING, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Add header
	$title = moduleLiteral::get($moduleID, "lbl_deprecatedClass");
	$header = DOM::create("h2", $title);
	$ntf->append($header);
	
	// Add message
	$message = DOM::create("p", $classInfo['deprecated']);
	$ntf->append($message);
	
	// Add notification before navigation
	$container = HTML::select(".section.classDesc")->item(0);
	DOM::append($container, $ntf->get());
}


// Return the report
return $page->getReport("#docViewer");
//#section_end#
?>