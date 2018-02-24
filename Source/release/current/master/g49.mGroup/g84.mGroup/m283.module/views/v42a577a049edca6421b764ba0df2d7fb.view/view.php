<?php
//#section#[header]
// Module Declaration
$moduleID = 283;

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
importer::import("API", "Resources");
importer::import("DEV", "Core");
importer::import("DEV", "Profiler");
importer::import("DEV", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Resources\filesystem\directory;
use \UI\Modules\MContent;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Forms\interactive\switchButtonForm;
use \UI\Forms\templates\simpleForm;
use \DEV\Profiler\debugger;
use \DEV\Profiler\ui\logViewer;
use \DEV\Core\test\sdkTester;
use \DEV\Core\test\sqlTester;
use \DEV\Core\test\ajaxTester;
use \DEV\Core\test\rsrcTester;
use \DEV\Core\test\jqTester;
use \DEV\Core\sdk\sdkLibrary;
use \DEV\Core\coreProject;
use \DEV\Resources\paths;


// Testing Controller Container
$pageContent = new MContent($moduleID);
$pageContent->build($id = "", $class = "coreConfigurator", TRUE);

$targetContainer = "coreConfigTabs";
$targetGroup = "coreConfigSelector";
$navGroup = "pageNavGroup_core";
$navDisplay = "none";

// Navigation
$navHeader = HTML::select(".navTab.tester")->item(0);
$pageContent->setStaticNav($navHeader, "coreTesterConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$navHeader = HTML::select(".navTab.debugger")->item(0);
$pageContent->setStaticNav($navHeader, "coreDebuggerConfig", $targetContainer, $targetGroup, $navGroup, $navDisplay);

$navHeader = HTML::select(".navTab.profiler")->item(0);
$pageContent->setStaticNav($navHeader, "coreResourceProfiler", $targetContainer, $targetGroup, $navGroup, $navDisplay);


// Group Selectors
$navPage = HTML::select(".page.testerPanel")->item(0);
$pageContent->setNavigationGroup($navPage, $targetGroup);

$navPage = HTML::select(".page.debuggerPanel")->item(0);
$pageContent->setNavigationGroup($navPage, $targetGroup);

$navPage = HTML::select(".page.profilerPanel")->item(0);
$pageContent->setNavigationGroup($navPage, $targetGroup);


// Ajax switch
$switchRow = HTML::select(".switchRow.ajax")->item(0);
$switch = new switchButtonForm("ajxTestSwitch");
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;
$switchObject = $switch->build("", ajaxTester::status())->engageModule($moduleID, "ajaxTesting", $attr)->get();
DOM::append($switchRow, $switchObject);

// Resources switch
$switchRow = HTML::select(".switchRow.rsrc")->item(0);
$switch = new switchButtonForm("rsrcTestSwitch");
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;
$switchObject = $switch->build("", rsrcTester::status())->engageModule($moduleID, "rsrcTesting", $attr)->get();
DOM::append($switchRow, $switchObject);

// SQL switch
$switchRow = HTML::select(".switchRow.sql")->item(0);
$switch = new switchButtonForm("sqlTestSwitch");
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;
$switchObject = $switch->build("", sqlTester::status())->engageModule($moduleID, "sqlTesting", $attr)->get();
DOM::append($switchRow, $switchObject);

// jQuery selector
$switchRow = HTML::select(".switchRow.jquery")->item(0);
$form = new simpleForm();
$jqtForm = $form->build("", FALSE)->engageModule($moduleID, "jqTesting")->get();
DOM::append($switchRow, $jqtForm);

// Project id
$input = $form->getInput($type = "hidden", $name = "id", $value = coreProject::PROJECT_ID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Get all jquery versions
$contents = directory::getContentList(systemRoot.paths::getCdnPath()."/js/jquery/", $includeHidden = FALSE, $includeDotFolders = FALSE, $relativeNames = TRUE);
$jqResource = array();
$jqResource[-1] = "System Default";
foreach ($contents['files'] as $filePath)
	$jqResource[$filePath] = $filePath;
$input = $form->getResourceSelect($name = "jq_file", $multiple = FALSE, $class = "inps_tester", $jqResource, $selectedValue = jqTester::status());
$form->append($input);

// SDK Packages Configurator
$packageConfigurator = HTML::select(".packageConfigurator")->item(0);

// Create form
$form = new simpleForm("packageSelectorForm");
$testerForm = $form->build($moduleID, "sdkTesting")->get();
DOM::append($packageConfigurator, $testerForm);

// Core project id
$input = $form->getInput("hidden", "id", coreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
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
$sdkLib = new sdkLibrary();
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
		$gridList->insertRow($row, "pkg[".$checkName."]", sdkTester::libPackageStatus($libName, $packageName));
	}
	
	
// Logger Switch
$switchRow = HTML::select(".switchRow.logger")->item(0);
$switch = new switchButtonForm("cloggerSwitch");
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;
$loggerSwitch = $switch->build("", logger::status())->engageModule($moduleID, "logger", $attr)->get();
DOM::append($switchRow, $loggerSwitch);

// Logger log
$coreLoggerContainer = HTML::select(".coreLoggerContainer")->item(0);
if (!logger::status())
	HTML::addClass($coreLoggerContainer, "noDisplay");

$logger = new logViewer();
$loggerViewer = $logger->build()->get();
DOM::append($coreLoggerContainer, $loggerViewer);

// Debugger Switch
$switchRow = HTML::select(".switchRow.debugger")->item(0);
$switch = new switchButtonForm("cdebuggerSwitch");
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;
$debuggerSwitch = $switch->build("", debugger::status())->engageModule($moduleID, "debugger", $attr)->get();
DOM::append($switchRow, $debuggerSwitch);
	
	
return $pageContent->getReport();
//#section_end#
?>