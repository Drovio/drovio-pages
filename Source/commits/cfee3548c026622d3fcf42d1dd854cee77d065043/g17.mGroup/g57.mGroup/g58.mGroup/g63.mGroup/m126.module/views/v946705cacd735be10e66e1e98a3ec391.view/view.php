<?php
//#section#[header]
// Module Declaration
$moduleID = 126;

// Inner Module Codes
$innerModules = array();
$innerModules['pageStructureObject'] = 130;
$innerModules['templateObject'] = 117;
$innerModules['templateViewer'] = 131;

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\template;
use \UI\Presentation\tabControl;
use \UI\Presentation\layoutContainer;
use \UI\Html\HTMLContent;
use \UI\Presentation\frames\windowFrame;
use \UI\Forms\formControls\formItem;
use \INU\Developer\redWIDE;
use \UI\Presentation\gridSplitter;


$templateID = $_GET['id'];

// Load Template
$template = new template();
$template->load($templateID);

// Array of common module load variable
$derAttr = array();
$derAttr['templateId'] = $templateID;

$splitter = new gridSplitter();
$splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_LEFT, $closed = FALSE, "SideBar");


// Create Module Page
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();
$HTMLContent->buildElement($splitter->get());
//$globalContainer = $HTMLContent->get();
//DOM::appendAttr($globalContainer, "style", "height:100%;");

// Side Bar Menu
$sidebar  = DOM::create("div", "", "", "sidebar");
	$attr['tplID'] = $templateID;
	$pageStructureItem = $HTMLContent->getModuleContainer($moduleID, "sideBar", $attr, TRUE);
	DOM::append($sidebar, $pageStructureItem);
$splitter->appendToSide($sidebar);

// Main Content
$rightContent = DOM::create("div", "", "", "rightContent");
$splitter->appendToMain($rightContent);

$themeStructureSelector = DOM::create("div", "", "themeStructureSelector", "themeStructureSelector noDisplay");
DOM::append($rightContent, $themeStructureSelector);

$editorContainer = DOM::create("div", "", "editorContainer", "editorContainer full");
	// Fill with content
	$WIDE = new redWIDE();
	$layout_WIDE = $WIDE->build();
	DOM::append($editorContainer, $layout_WIDE->get());
DOM::append($rightContent, $editorContainer);



	 




// Return output
return $HTMLContent->getReport();
//#section_end#
?>