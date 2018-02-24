<?php
//#section#[header]
// Module Declaration
$moduleID = 275;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Geoloc\datetimer;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\projectLibrary;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "marketReviewManager", TRUE);
$projectList = HTML::select(".marketReview .list")->item(0);

// Get all pending projects
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_projects_releases");
$result = $dbc->execute($q);
$projectsReleases = $dbc->fetch($result, TRUE);
foreach ($projectsReleases as $projectRelease)
{
	$pTile = getProjectTile($moduleID, $actionFactory, $projectRelease);
	DOM::append($projectList, $pTile);
}

// Return output
return $pageContent->getReport();


// Application tile creator
function getProjectTile($moduleID, $actionFactory, $projectRelease)
{
	// Create application tile
	$projectTile = DOM::create("div", "", "", "project_tile");
	
	// Create release details
	$releaseDetails = DOM::create("div", "", "", "release_details");
	DOM::append($projectTile, $releaseDetails);
	
	// Check the status of the release
	$projectID = $projectRelease['project_id'];
	$projectVersion = $projectRelease['version'];
	$projectChangelog = $projectRelease['changelog'];
	
	// Release status
	$rStatus = DOM::create("div", "", "", "releaseStatus");
	DOM::append($releaseDetails, $rStatus);
	HTML::addClass($rStatus, "st".$projectRelease['status_id']);
	
	$statusContainer = DOM::create("div", "", "", "status");
	DOM::append($rStatus, $statusContainer);
	
	$ico = DOM::create("span", "", "", "st_ico");
	DOM::append($statusContainer, $ico);
	
	$attr = array();
	$attr['reviewer'] = $projectRelease['reviewAccountTitle'];
	$title = moduleLiteral::get($moduleID, "lbl_rStatus_".$projectRelease['status_id'], $attr);
	$statusTitle = DOM::create("div", $title, "", "st_title");
	DOM::append($statusContainer, $statusTitle);
	
	// Download project button
	$title = moduleLiteral::get($moduleID, "lbl_downloadRelease");
	$dlButton = DOM::create("div", $title, "", "dl_button");
	DOM::append($rStatus, $dlButton);
	$attr = array();
	$attr['pid'] = $projectID;
	$attr['version'] = $projectVersion;
	$actionFactory->setDownloadAction($dlButton, $moduleID, "downloadProject", $attr);

	// Review detail button
	$title = moduleLiteral::get($moduleID, "lbl_reviewDetails");
	$rvButton = DOM::create("div", $title, "", "rv_button");
	DOM::append($rStatus, $rvButton);
	
	// Add application ico
	$projectIconUrl = projectLibrary::getProjectIconUrl($projectID, $projectVersion);
	if (isset($projectIconUrl ))
	{
		$img = DOM::create("img");
		DOM::attr($img, "src", $projectIconUrl );
	}
	$appIco = DOM::create("div", $img, "", "ico");
	DOM::append($releaseDetails, $appIco);
	
	// Add title and description
	$project_info = DOM::create("div", "", "", "project_info");
	DOM::append($releaseDetails, $project_info);
	// Title
	$releaseTitle = DOM::create("h1", $projectRelease['title'], "", "title");
	DOM::append($project_info, $releaseTitle);
	// Team name
	$pInfo = DOM::create("p", $projectRelease['teamName'], "", "info");
	DOM::append($project_info, $pInfo);
	// Release project type
	$pInfo = DOM::create("p", $projectRelease['type'], "", "info");
	DOM::append($project_info, $pInfo);
	// Release Version
	$attr = array();
	$attr['version'] = $projectVersion;
	$title = moduleLiteral::get($moduleID, "lbl_projectVersion", $attr);
	$pInfo = DOM::create("p", $title, "", "info");
	DOM::append($project_info, $pInfo);
	// Released date
	$attr = array();
	$attr['date'] = date("M d, Y", $projectRelease['time_created']);
	$title = moduleLiteral::get($moduleID, "lbl_dateRelease", $attr);
	$pInfo = DOM::create("p", $title, "", "info");
	DOM::append($project_info, $pInfo);
	
	// Release changelog
	$changelog = DOM::create("div", $projectRelease['changelog'], "", "changelog");
	DOM::append($releaseDetails, $changelog);
	
	
	// Review details
	$reviewDetails = DOM::create("div", "", "", "review_details");
	DOM::append($projectTile, $reviewDetails);
	if ($projectRelease['status_id'] == 1)
	{
		// Project under review
		
		// Build review form
		$form = new simpleForm("rv_".$projectID);
		$reviewForm = $form->build()->engageModule($moduleID, "reviewProject")->get();
		DOM::append($reviewDetails, $reviewForm);
		
		// Project ID
		$input = $form->getInput($type = "hidden", $name = "pid", $value = $projectID, $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// Project version
		$input = $form->getInput($type = "hidden", $name = "version", $value = $projectVersion, $class = "", $autofocus = FALSE, $required = FALSE);
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
	else
	{
		// Add review details
		$attr = array();
		$attr['reviewer'] = $projectRelease['reviewAccountTitle'];
		$attr['date'] = date("M d, Y", $projectRelease['time_updated']);
		$title = moduleLiteral::get($moduleID, "hd_reviewTitle", $attr);
		$hd = DOM::create("h2", $title, "", "review_title");
		DOM::append($reviewDetails, $hd);
		
		$reviewComments = DOM::create("p", $projectRelease['comments'], "", "review_comments");
		DOM::append($reviewDetails, $reviewComments);
	}
	
	
	return $projectTile;
}
//#section_end#
?>