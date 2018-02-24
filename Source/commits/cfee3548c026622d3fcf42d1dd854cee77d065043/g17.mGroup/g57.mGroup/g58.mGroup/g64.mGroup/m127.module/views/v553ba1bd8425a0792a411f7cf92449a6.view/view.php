<?php
//#section#[header]
// Module Declaration
$moduleID = 127;

// Inner Module Codes
$innerModules = array();

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
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \UI\Html\HTMLContent;
use \INU\Developer\redWIDE;

$templateID = $_GET['id'];

// Create Module Page
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();
$HTMLContent->build();
$ModuleHTMLContent = $HTMLContent->get();
DOM::attr($ModuleHTMLContent, "style", "height: 100%");

// Build layout
$listViewerHolder = DOM::create('div','','','sidebar');
DOM::append($ModuleHTMLContent, $listViewerHolder);

$editorHolder = DOM::create('div','','','rightContent');
DOM::append($ModuleHTMLContent, $editorHolder);

// Fill with content
$WIDE = new redWIDE();
$layout_WIDE = $WIDE->build("psObjectEdiitorWIDE");
DOM::append($editorHolder, $layout_WIDE->get());

// Sidebar LayoutLIst Viewer
$refreshControl = DOM::create('div', '', '', 'refreshControl');	
	$refresh = DOM::create('span', 'Refresh List');
	DOM::append($refreshControl, $refresh);
	$attr = array('templateId' => $templateID, 'holder' => "layoutViewer");
	$actionFactory->setModuleAction($refresh, $moduleID, "listViewer", "", $attr);
DOM::append($listViewerHolder, $refreshControl);

$userPrompt = DOM::create('div', '', '', 'userPrompt');	
	$content = DOM::create('span', 'Select One or More Objects for edit.');
	DOM::append($userPrompt, $content);
DOM::append($listViewerHolder, $userPrompt);

$moduleWhapper = DOM::create("div", "", "layoutViewer", '');
DOM::attr($moduleWhapper, "style", "height: 100%; margin-top: 10px;");
DOM::append($listViewerHolder, $moduleWhapper);

$attr = array('templateId' => $templateID, "editorPool" => "psObjectEdiitorWIDE");
$layoutListViewer  = $HTMLContent->getModuleContainer($moduleID, 'listViewer', $attr, $startup = TRUE);
DOM::append($moduleWhapper, $layoutListViewer);


// Return output
return $HTMLContent->getReport();
//#section_end#
?>