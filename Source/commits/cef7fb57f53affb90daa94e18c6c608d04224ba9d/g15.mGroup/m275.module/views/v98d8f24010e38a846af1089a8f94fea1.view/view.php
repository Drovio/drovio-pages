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
$pageContent->build("", "reviewedProjects", TRUE);

// Review list
$rvList = HTML::select(".reviewedProjects .crvList")->item(0);

// Get all pending projects
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_reviewed_projects");
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
	
	$ico = DOM::create("span", "", "", "ptico");
	DOM::append($rvhd, $ico);
	
	$projectType = DOM::create("span", $prj['type'], "", "ptype");
	DOM::append($rvhd, $projectType);
	
	$ptitle = DOM::create("div", $prj['title'], "", "rTitle");
	DOM::append($rvhd, $ptitle);
	
	// Status
	$rStatus = DOM::create("div", "", "", "rStatus st".$prj['status_id']);
	DOM::append($rvhd, $rStatus);
	
	$ico = DOM::create("span", "", "", "ico");
	DOM::append($rStatus, $ico);
	
	$title = moduleLiteral::get($moduleID, "lbl_rStatus_".$prj['status_id']);
	$statusTitle = DOM::create("div", $title, "", "status");
	DOM::append($rStatus, $statusTitle);
	
	$live = datetimer::live($prj['time_updated']);
	$rvat_live = DOM::create("span", $live, "", "rvat_live");
	DOM::append($rvhd, $rvat_live);
	
	
	// Row body (Review comments)
	$rvbd = DOM::create("div", "", "", "rvbd");
	DOM::append($rvrow, $rvbd);
	
	// Comments
	$title = moduleLiteral::get($moduleID, "lbl_reviewComments");
	$cmthd = DOM::create("h4", $title);
	DOM::append($rvbd, $cmthd);
	
	$commentsContext = (empty($prj['comments']) ? "No Comments" : $prj['comments']);
	$comments = DOM::create("pre", $commentsContext);
	DOM::append($rvbd, $comments);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>