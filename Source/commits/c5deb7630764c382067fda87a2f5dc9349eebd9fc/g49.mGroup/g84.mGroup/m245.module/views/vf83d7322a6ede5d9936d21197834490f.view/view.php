<?php
//#section#[header]
// Module Declaration
$moduleID = 245;

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
importer::import("DEV", "Profiler");
importer::import("DEV", "Tools");
importer::import("INU", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \INU\Developer\codeEditor;
use \DEV\Core\sdk\sdkLibrary;
use \DEV\Core\coreProject;
use \DEV\Profiler\console;
use \DEV\Tools\parsers\phpParser;

// Run code after safe
if (engine::isPost())
{
	// Create page content
	$pageContent = new MContent();
	$pageContent->build();
	
	// Set normal holder
	$holder = ".consoleContent .output .content";
	
	$dependencies = $_POST['dep'];
	$c_dep = array();
	foreach ($dependencies as $depKey => $data)
	{
		list($library, $package) = explode(",", $depKey);
		$c_dep[$library][] = $package;
	}
	
	// Get php code safe
	$code = $_POST['devConsoleCode'];
	//$saveHistory = !isset($_POST['hid']);
	$output = console::php($code, TRUE, $c_dep, TRUE);
	
	$output = DOM::create("pre", $output, "", "result");
	$pageContent->append($output);
	
	// Return output
	return $pageContent->getReport($holder);
}


// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the content
$pageContent->build("", "consoleContainer", TRUE);


// Console container
$consoleContainer = HTML::select(".consoleContent .console")->item(0);

// Build form container for input
$form = new simpleForm();
$formContainer = $form->build($moduleID, "console", FALSE)->get();
DOM::append($consoleContainer, $formContainer);

// Project ID
$input = $form->getInput("hidden", "id", coreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

// Console content
$consoleContent = HTML::select(".console .content")->item(0);
$form->append($consoleContent);

// Manager toolbar
$codeMgrToolbar = new navigationBar();
$codeMgrToolbar->build($dock = "T", $consoleContent);
DOM::append($consoleContent, $codeMgrToolbar->get());

// Settings item
$navTool = DOM::create("span", "", "", "consoleTool settings");
$codeMgrToolbar->insertToolbarItem($navTool);

// Run control
$saveTool = DOM::create("button", "", "", "consoleTool run");
DOM::attr($saveTool, "type", "submit");
$codeMgrToolbar->insertToolbarItem($saveTool);
	
	
// Get history id to load content
$history_id = $_GET['hid'];
$dependencies = array();
$content = "// Write your php code here";
if (empty($history_id))
{
	// Get most recent from history log
	$historyLog = console::getHistoryLog();
	$history_id = $historyLog[0]['id'];
}

if (!empty($history_id))
{
	// Load content from tester's trunk history
	$consoleData = console::getFromHistory($history_id);
	$dependencies = $consoleData['dependencies'];
	$content = $consoleData['code'];
}


// Console headers
$consoleHeaders = HTML::select(".console .headers")->item(0);

// Add sdk packages
$dtGridList = new dataGridList();
$glist = $dtGridList->build("cns_headers", TRUE)->get();
DOM::append($consoleHeaders, $glist);

$headers = array();
$headers[] = "Library";
$headers[] = "Package";

$dtGridList->setHeaders($headers);

// Get All Packages
$sdkLib = new sdkLibrary();
$libraries = $sdkLib->getList();
asort($libraries);
$packages = array();
foreach ($libraries as $library)
{
	$packages[$library] = $sdkLib->getPackageList($library);
	asort($packages[$library]);
}

foreach ($packages as $lib => $pkgs)
	foreach ($pkgs as $pkg)
	{
		// Grid List Contents
		$gridRow = array();
		$gridRow[] = $lib;
		$gridRow[] = $pkg;
		
		$dtGridList->insertRow($gridRow, "dep[".$lib.','.$pkg.']', in_array($pkg, $dependencies[$lib]));
	}

// Build the code editor
$editor = new codeEditor();
$phpEditor = $editor->build($type = "php", $content, $name = "devConsoleCode", $editable = TRUE)->get();
DOM::append($consoleContent, $phpEditor);


// Add action to switch to console
$pageContent->addReportAction("switch.nav", "mi_console");


// Return output
return $pageContent->getReport();
//#section_end#
?>