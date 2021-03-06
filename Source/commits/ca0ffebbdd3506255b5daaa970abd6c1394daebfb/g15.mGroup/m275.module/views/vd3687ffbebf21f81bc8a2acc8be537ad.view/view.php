<?php
//#section#[header]
// Module Declaration
$moduleID = 275;

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
importer::import("API", "Geoloc");
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Geoloc\datetimer;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "pendingProjects", TRUE);

// Review list
$rvList = HTML::select(".pendingProjects .rvList")->item(0);

// Get all pending projects
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_pending_projects");
$result = $dbc->execute($q);
$pendingProjects = $dbc->fetch($result, TRUE);
foreach ($pendingProjects as $prj)
{
	// This is a review row with full details
	$rvrow = DOM::create("div", "", "", "rvrow");
	DOM::append($rvList, $rvrow);
	
	// Row header
	$rvhd = DOM::create("div", "", "", "rvhd");
	DOM::append($rvrow, $rvhd);
	
	$ptitle = DOM::create("div", $prj['title'].", version ".$prj['version'], "", "rTitle");
	DOM::append($rvhd, $ptitle);
	
	// Status
	$rStatus = DOM::create("div", "", "", "rStatus");
	DOM::append($rvhd, $rStatus);
	
	$ico = DOM::create("span", "", "", "ico");
	DOM::append($rStatus, $ico);
	
	$title = moduleLiteral::get($moduleID, "lbl_rStatus_".$prj['status_id']);
	$statusTitle = DOM::create("div", $title, "", "status");
	DOM::append($rStatus, $statusTitle);
	
	$live = datetimer::live($prj['time_created']);
	$rvat_live = DOM::create("span", $live, "", "rvat_live");
	DOM::append($rvhd, $rvat_live);
	
	
	// Row body (changelog, download button, comments and review)
	$rvbd = DOM::create("div", "", "", "rvbd");
	DOM::append($rvrow, $rvbd);
	
	// Changelog
	$title = moduleLiteral::get($moduleID, "lbl_prjChangelog");
	$cmthd = DOM::create("h4", $title);
	DOM::append($rvbd, $cmthd);
	
	$comments = DOM::create("pre", $prj['changelog']);
	DOM::append($rvbd, $comments);
	
	
	// Review
	$title = moduleLiteral::get($moduleID, "lbl_prjReview");
	$rv_hd = DOM::create("h4", $title);
	DOM::append($rvbd, $rv_hd);
	
	// Build review form
	$form = new simpleForm("rv_".$prj['project_id']);
	$reviewForm = $form->build()->engageModule($moduleID, "reviewProject")->get();
	DOM::append($rvbd, $reviewForm);
	
	// Project ID
	$input = $form->getInput($type = "hidden", $name = "pid", $value = $prj['project_id'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	// Project version
	$input = $form->getInput($type = "hidden", $name = "version", $value = $prj['version'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	// Approve for publish
	$title = moduleLiteral::get($moduleID, "lbl_approve");
	$input = $form->getInput($type = "radio", $name = "status", $value = "2", $class = "", $autofocus = FALSE, $required = TRUE);
	$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
	$form->append($inputRow);
	
	// Approve for publish
	$title = moduleLiteral::get($moduleID, "lbl_reject");
	$input = $form->getInput($type = "radio", $name = "status", $value = "3", $class = "", $autofocus = FALSE, $required = TRUE);
	$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
	$form->append($inputRow);
	
	// Comments text area
	$title = moduleLiteral::get($moduleID, "lbl_comments");
	$input = $form->getTextarea($name = "comments", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
	$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
	$form->append($inputRow);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>