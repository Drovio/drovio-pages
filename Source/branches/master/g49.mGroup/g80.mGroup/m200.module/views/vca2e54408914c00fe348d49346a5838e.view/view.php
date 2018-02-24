<?php
//#section#[header]
// Module Declaration
$moduleID = 200;

// Inner Module Codes
$innerModules = array();

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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("INU", "Developer");
importer::import("DEV", "Profiler");
//#section_end#
//#section#[code]
use \API\Developer\content\document\parsers\phpParser;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLModulePage;
use \UI\Html\HTMLContent;
use \INU\Developer\codeEditor;
use \DEV\Profiler\console;


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

// Build the module
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "developerConsole", TRUE);

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