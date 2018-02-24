<?php
//#section#[header]
// Module Declaration
$moduleID = 100;

// Inner Module Codes
$innerModules = array();
$innerModules['sdkManual'] = 291;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("DEV", "Prototype");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Profile\team;
use \API\Model\core\manifests;
use \API\Literals\moduleLiteral;
use \API\Resources\filesystem\directory;
use \UI\Modules\MContent;
use \DEV\Prototype\sourceMap;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get manual attributes
$selectedDomain = engine::getVar("domain");
$selectedLibrary = engine::getVar("lib");
$selectedPackage = engine::getVar("pkg");
$selectedNS = engine::getVar("ns");
$selectedNS = trim($selectedNS, "/ ");
$selectedObjectName = engine::getVar("oname");
$selectedObjectName = (empty($selectedObjectName) ? $selectedNS : $selectedObjectName);

// Build the module content
$pageContent->build("", "sdkMenuContainer", TRUE);
$domainHolder['SDK'] = HTML::select(".platform_sdk")->item(0);
$domainHolder['WSDK'] = HTML::select(".web_sdk")->item(0);

// Set team ids allowed to see the full sdk
$allowedTeams = array(6, 7);

// Get open packages
$openPackages = importer::getOpenPackageList();

// Get protected libraries
$coreManifests = manifests::getManifests();
$mfLibraries = array();
foreach ($coreManifests as $mfID => $mfInfo)
	if (!$mfInfo['info']['private'])
		foreach ($mfInfo['packages'] as $libraryName => $packages)
			foreach ($packages as $packageName)
				$mfLibraries[$libraryName][$packageName] = 1;

// List sdk reference for both domains
$domains = array("SDK");//, "WSDK");
$currentTeamID = team::getTeamID();
foreach ($domains as $domainName)
{
	$sourceMap = new sourceMap(systemRoot."/System/Resources/Documentation/".$domainName."/");
	$libraries = $sourceMap->getLibraryList();
	asort($libraries);
	foreach ($libraries as $library)
	{
		// Filter SDK libraries
		if ($domainName == "SDK" && !in_array($currentTeamID, $allowedTeams) && !isset($openPackages[$library]) && !isset($mfLibraries[$library]))
			continue;

		// Get all packages
		$packages = $sourceMap->getPackageList($library);
		asort($packages);
		foreach ($packages as $packageName)
		{
			
			// Filter packages
			if ($domainName == "SDK" && !in_array($currentTeamID, $allowedTeams) && !in_array($packageName, $openPackages[$library]) && !isset($mfLibraries[$library][$packageName]))
				continue;
			
			// Build package group
			$packageGroup = DOM::create("div", "", "", "menuContainer packageGroup");
			HTML::append($domainHolder[$domainName], $packageGroup);

			// Create menu title item
			$itemIco = DOM::create("span", "", "", "contentIcon pkgIco");
			$menuTitle = DOM::create("div", $itemIco, "", "sd-title sdk-title");
			$itemName = DOM::create("span", $library."/".$packageName);
			DOM::append($menuTitle, $itemName);
			DOM::append($packageGroup, $menuTitle);

			// Create menu
			$menu = DOM::create("ul", "", "", "menu package-menu");
			DOM::append($packageGroup, $menu);

			// List all package objects
			$sdkObjects = $sourceMap->getObjectList($library, $packageName);
			usort($sdkObjects, "sort_sdk_objects");
			foreach ($sdkObjects as $objectInfo)
			{
				// Get object full name
				$objectNS = str_replace("::", "/", $objectInfo['namespace']);
				$objectFullName = trim($objectNS."/".$objectInfo['name'], "/ ");
				
				// Set attributes
				$attr = array();
				$attr['domain'] = $domainName;
				$attr['lib'] = $library;
				$attr['pkg'] = $packageName;
				$attr['ns'] = $objectNS;
				$attr['oname'] = $objectInfo['name'];

				// Create weblink
				$href = url::resolve("developers", "/sdk/".$domainName."/".$library."/".$packageName."/".$objectFullName);
				$weblink = $pageContent->getWeblink($href, $objectFullName, $target = "_self");//, $innerModules['sdkManual'], $viewName = "manualViewer", $attr, $class = "");
				$actionFactory->setModuleAction($weblink, $innerModules['sdkManual'], $viewName = "manualViewer", $holder = ".docContainer", $attr);
				$li = DOM::create("li", $weblink, "", "menu-item");
				DOM::append($menu, $li);
				
				// Set selected
				if ($domainName == $selectedDomain &&
				    $library == $selectedLibrary &&
				    $packageName == $selectedPackage &&
				    $objectNS == $selectedNS &&
				    $objectInfo['name'] == $selectedObjectName)
					HTML::addClass($weblink, "selected");
				
				// Set static nav
				$pageContent->setStaticNav($weblink, $ref = "", $targetcontainer = "", $targetgroup = "", $navgroup = "sdgroup", $display = "none");
			}
		}
	}
}

// Check to preload the manual
if (isset($selectedDomain) && isset($selectedLibrary) && isset($selectedPackage) && isset($selectedNS) && isset($selectedObjectName))
{
	// Load manual
	$docContainer = HTML::select(".docContainer")->item(0);
	$document = $pageContent->loadView($innerModules['sdkManual'], $viewName = "manualViewer");
	DOM::append($docContainer, $document);
	
}

// Return output
return $pageContent->getReport();

function sort_sdk_objects($objectA, $objectB)
{
	// Get full names for each object
	$objectATitle = trim(str_replace("::", "/", $objectA['namespace'])."/".$objectA['name'], "/ ");
	$objectBTitle = trim(str_replace("::", "/", $objectB['namespace'])."/".$objectB['name'], "/ ");
	
	// Check same
	if ($objectATitle == $objectBTitle)
		return 0;
	
	// Compare
	return ($objectATitle < $objectBTitle) ? -1 : 1;
}
//#section_end#
?>