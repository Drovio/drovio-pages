<?php
//#section#[header]
// Module Declaration
$moduleID = 364;

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
importer::import("DEV", "WebEngine");
importer::import("UI", "Forms");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \UI\Interactive\forms\switchButton;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Forms\templates\simpleForm;
use \DEV\WebEngine\test\wsdkTester;
use \DEV\WebEngine\sdk\webLibrary;
use \DEV\WebEngine\webCoreProject;


// Testing Controller Container
$pageContent = new MContent($moduleID);
$pageContent->build($id = "", $class = "webCoreConfigurator", TRUE);

$targetContainer = "wcoreConfigTabs";
$targetGroup = "wcoreConfigSelector";
$navGroup = "pageNavGroup_wcore";
$navDisplay = "none";

// Navigation
$navHeader = HTML::select(".navTab.tester")->item(0);
$pageContent->setStaticNav($navHeader, "wcoreTesterConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$navHeader = HTML::select(".navTab.profiler")->item(0);
$pageContent->setStaticNav($navHeader, "wcoreResourceProfiler", $targetContainer, $targetGroup, $navGroup, $navDisplay);


// Group Selectors
$navPage = HTML::select(".page.testerPanel")->item(0);
$pageContent->setNavigationGroup($navPage, $targetGroup);

$navPage = HTML::select(".page.profilerPanel")->item(0);
$pageContent->setNavigationGroup($navPage, $targetGroup);



// SDK Packages Configurator
$packageConfigurator = HTML::select(".packageConfigurator")->item(0);

// Create form
$form = new simpleForm("packageSelectorForm");
$testerForm = $form->build($moduleID, "wsdkTesting")->get();
DOM::append($packageConfigurator, $testerForm);

// Core project id
$input = $form->getInput("hidden", "id", webCoreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

// Package Selector container
$packageList = DOM::create("div", "", "", "packageList");
$form->append($packageList);

$gridList = new dataGridList();
$packageGrid = $gridList->build($id = "packageGrid", $checkable = TRUE)->get();
$headers = array();
$headers[] = "Library";
$headers[] = "Package";
$gridList->setHeaders($headers);
DOM::append($packageList, $packageGrid);

// Get All Packages
$sdkLib = new webLibrary();
$libraries = $sdkLib->getList();
$packageList = array();
foreach ($libraries as $library)
{
	$packageList[$library] = $sdkLib->getPackageList($library);
	asort($packageList[$library]);
}
ksort($packageList);

foreach ($packageList as $libName => $packages)
	foreach ($packages as $packageName)
	{
		$row = array();
		$row[] = $libName;
		$row[] = $packageName;
		$checkName = $libName."_".$packageName;
		$gridList->insertRow($row, "pkg[".$checkName."]", wsdkTester::libPackageStatus($libName, $packageName));
	}
	
	
return $pageContent->getReport();
//#section_end#
?>