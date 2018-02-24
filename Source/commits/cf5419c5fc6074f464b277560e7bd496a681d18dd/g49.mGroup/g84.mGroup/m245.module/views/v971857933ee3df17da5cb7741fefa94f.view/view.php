<?php
//#section#[header]
// Module Declaration
$moduleID = 245;

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("INU", "Developer");
importer::import("DEV", "Profiler");
importer::import("DEV", "Tools");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MPage;
use \UI\Modules\MContent;
use \INU\Developer\codeEditor;
use \DEV\Profiler\console;
use \DEV\Tools\parsers\phpParser;

// Run code after safe
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Create page content
	$pageContent = new MContent();
	$pageContent->build();
	
	// Set normal holder
	$holder = ".coreTester .output .content";
	
	$dependencies = $_POST['dep'];
	
	// Get php code safe
	$code = $_POST['devConsoleInput'];
	$output = console::php($code, TRUE, $dependencies);
	
	$output = DOM::create("pre", $output, "", "result");
	$pageContent->append($output);
	
	// Return output
	return $pageContent->getReport($holder);
}


// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module
$page->build("", "coreTester", TRUE);





// Toolbar navigation
$title = moduleLiteral::get($moduleID, "lbl_coreConsole");
$subItem = $page->addToolbarNavItem("loaderNavSub", $title, $class = "", NULL, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0, FALSE);
$actionFactory->setModuleAction($subItem, $moduleID, "", ".prjContent");


$title = moduleLiteral::get($moduleID, "lbl_testingTrunk");
$subItem = $page->addToolbarNavItem("trunkNavSub", $title, $class = "", NULL, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0, FALSE);
$actionFactory->setModuleAction($subItem, $moduleID, "trunkPage", ".prjContent");


// Console container
$consoleContainer = HTML::select(".console")->item(0);

// Build form container for input
$form = new simpleForm();
$formContainer = $form->build($moduleID, "", FALSE)->get();
DOM::append($consoleContainer, $formContainer);

// Console content
$consoleContent = DOM::create("div", "", "", "content");
$form->append($consoleContent);

$content = "// Write your php code here";
$editor = new codeEditor();
$phpEditor = $editor->build($type = "php", $content, $name = "devConsoleInput", $editable = TRUE)->get();
DOM::append($consoleContent, $phpEditor);

// Console controls
$consoleControls = DOM::create("div", "", "", "controls");
$form->append($consoleControls);

$title = moduleLiteral::get($moduleID, "lbl_submitBtn");
$submitButton = $form->getSubmitButton($title, "consoleSubmit");
DOM::append($consoleControls, $submitButton);


// Return output
return $page->getReport();
//#section_end#
?>