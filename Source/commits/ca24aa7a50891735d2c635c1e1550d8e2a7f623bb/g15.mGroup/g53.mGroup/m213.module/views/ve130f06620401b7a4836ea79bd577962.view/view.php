<?php
//#section#[header]
// Module Declaration
$moduleID = 213;

// Inner Module Codes
$innerModules = array();
$innerModules['trunkPage'] = 136;

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("INU", "Developer");
importer::import("DEV", "Profiler");
importer::import("DEV", "Tools");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLModulePage;
use \UI\Html\HTMLContent;
use \INU\Developer\codeEditor;
use \DEV\Profiler\console;
use \DEV\Tools\parsers\phpParser;

// Run code after safe
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Create page content
	$pageContent = new HTMLContent();
	$pageContent->build();
	
	// Set normal holder
	$holder = ".output .content";
	
	// Get php code safe
	$code = $_POST['devConsoleInput'];
	$output = console::php($code);
	
	$output = DOM::create("pre", $output, "", "result");
	$pageContent->append($output);
	
	// Return output
	return $pageContent->getReport($holder);
}


// Create Module Page
$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

// Build the module
$page->build("", "coreTester", TRUE);





// Toolbar navigation
// Loader
$title = moduleLiteral::get($moduleID, "lbl_coreConsole");
$subItem = $page->addToolbarNavItem("loaderNavSub", $title, $class = "", NULL, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0, FALSE);
$actionFactory->setModuleAction($subItem, $moduleID);


$title = moduleLiteral::get($moduleID, "lbl_testingTrunk");
$subItem = $page->addToolbarNavItem("trunkNavSub", $title, $class = "", NULL, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0, FALSE);
$actionFactory->setModuleAction($subItem, $innerModules['trunkPage']);



// Set Headers
$consoleHeader = HTML::select("h3.header")->item(0);
$title = moduleLiteral::get($moduleID, "lbl_consoleHeader");
DOM::append($consoleHeader, $title);

$outputHeader = HTML::select("h3.header")->item(1);
$title = moduleLiteral::get($moduleID, "lbl_outputHeader");
DOM::append($outputHeader, $title);

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