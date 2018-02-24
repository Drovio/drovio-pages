<?php
//#section#[header]
// Module Declaration
$moduleID = 104;

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
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Html\HTMLModulePage;
use \UI\Presentation\dataGridList;

$pageTitle = moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE);

// Create page
$page = new HTMLModulePage("OneColumnFullscreen");
$actionFactory = $page->getActionFactory();
// Build the module
$page->build($pageTitle, "issuesManager");

// _____ Toolbar Navigation
$navCollection = $page->getRibbonCollection("issuesHome");
$subItem = $page->addToolbarNavItem("issuesNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);

// File new issue
$panel = new ribbonPanel();
$addBug = $panel->build("newIssue", TRUE)->get();
$title = moduleLiteral::get($moduleID, "lbl_addIssue");
$addIssue = $panel->insertPanelItem($type = "small", $title, $imgURL = "", $selected = FALSE);
//$actionFactory->setModuleAction($addIssue, $innerModules['addIssue']);
DOM::append($navCollection, $addBug);

$issuesViewer = DOM::create("div", "", "", "issuesViewer");
$page->appendToSection("mainContent", $issuesViewer);

// Search + info bar
$searchContainer = DOM::create("div", "", "", "searchIssues");
DOM::append($issuesViewer, $searchContainer);

$infoBar = DOM::create("div", "", "", "infoBar");
DOM::append($searchContainer, $infoBar);

$searchBar = DOM::create("div", "", "", "searchBar");
DOM::append($searchContainer, $searchBar);


// Bugs viewer
$issuesContainer = DOM::create("div", "", "", "viewIssues");
DOM::append($issuesViewer, $issuesContainer);

$issues = DOM::create("div", "", "", "issuesWrapper");
DOM::append($issuesContainer, $issues);

// Issues grid
$dtGridList = new dataGridList();
$glist = $dtGridList->build("issuesGrid")->get();

$spans = array();
$spans[] = 0.06;
$spans[] = 0.03;
$spans[] = 0.06;
$spans[] = 0.06;
$spans[] = 0.18;
$spans[] = 0.37;
$spans[] = 0.1;
$spans[] = 0.07;
$spans[] = 0.07;
$dtGridList->setColumnRatios($spans);

$headers = array();
$headers[] = "ID";
$headers[] = "Priority";
$headers[] = "Status";
$headers[] = "Domain";
$headers[] = "Location";
$headers[] = "Summary + Tags";
$headers[] = "Owner";
$headers[] = "Reported On";
$headers[] = "Modified On";
$dtGridList->setHeaders($headers);

// Dummy bug
$gridRow = array();
$gridRow[] = "Dum";
$gridRow[] = "1";
$gridRow[] = "Reported";
$gridRow[] = "developer";
$gridRow[] = "modules";
$gridRow[] = "This is a dummy bug. No tags yet.";
$gridRow[] = "limpakos";
$gridRow[] = "13 Oct 2013";
$gridRow[] = "13 Oct 2013";
$dtGridList->insertRow($gridRow);

DOM::append($issues, $glist);

// Return output
return $page->getReport();
//#section_end#
?>