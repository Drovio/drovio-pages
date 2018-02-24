<?php
//#section#[header]
// Module Declaration
$moduleID = 244;

// Inner Module Codes
$innerModules = array();
$innerModules['publisher'] = 261;

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
importer::import("DEV", "Projects");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Geoloc\datetimer;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\project;
use \DEV\Projects\projectLibrary;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Build module page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title." | ".$projectTitle, "releaseLogPage", TRUE);


// Set publisher action
$publisher = HTML::select(".releaseLog .hd .publisher")->item(0);

// Set publish action
$attr = array();
$attr['pid'] = $projectID;
$attr['id'] = $projectID;
$actionFactory->setModuleAction($publisher, $moduleID, "releaseProject", "", $attr, $loading = TRUE);

// Get releases
$releases = $project->getReleases();


// Show release log
$listContainer = HTML::select(".releaseLog .list")->item(0);
foreach ($releases as $projectRelease)
{
	// Check the status of the release
	$projectID = $projectRelease['project_id'];
	$projectVersion = $projectRelease['version'];
	$projectChangelog = $projectRelease['changelog'];
	
	// Create application tile
	$rowID = str_replace(".", "_", "rr_".$projectID."_".$projectVersion);
	$projectTile = DOM::create("div", "", $rowID, "releaseRow");
	DOM::append($listContainer, $projectTile);
	
	// Create release details
	$releaseDetails = DOM::create("div", "", "", "release_details");
	DOM::append($projectTile, $releaseDetails);
	
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

	// Review detail button
	$title = moduleLiteral::get($moduleID, "lbl_reviewDetails");
	$rvButton = DOM::create("div", $title, "", "rv_button");
	DOM::append($rStatus, $rvButton);
	
	// Add application ico
	$projectIconUrl = projectLibrary::getProjectIconUrl($projectID, $projectVersion);
	$img = NULL;
	if (isset($projectIconUrl))
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
	// Release Version
	$attr = array();
	$attr['version'] = $projectVersion;
	$title = moduleLiteral::get($moduleID, "lbl_relVersion", $attr);
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
		// Add revoke action from the team
		$form = new simpleForm("rv_".$projectID."_".$projectVersion);
		$reviewForm = $form->build("", FALSE)->engageModule($moduleID, "revokeRelease")->get();
		DOM::append($reviewDetails, $reviewForm);
		
		// Project ID
		$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// Project version
		$input = $form->getInput($type = "hidden", $name = "version", $value = $projectVersion, $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// Create a row container
		$formRow = DOM::create("div", "", "", "frow");
		$form->append($formRow);
		
		// Get submit button to reject
		$title = moduleLiteral::get($moduleID, "lbl_rejectRelease");
		$submitButton = $form->getSubmitButton($title, $id = "", $name = "");
		HTML::addClass($submitButton, "fbutton");
		DOM::append($formRow, $submitButton);
		
		// Create label to explain
		$title = moduleLiteral::get($moduleID, "lbl_rejectRelease_notes");
		$label = $form->getLabel($title, $for = "", $class = "flabel");
		DOM::append($formRow, $label);
	}
	else if (!empty($projectRelease['comments']))
	{
		// Add review comments
		$reviewComments = DOM::create("p", $projectRelease['comments'], "", "review_comments");
		DOM::append($reviewDetails, $reviewComments);
	}
	
	// Add remove release action
	$form = new simpleForm("rr_".$projectID."_".$projectVersion);
	$reviewForm = $form->build("", FALSE)->engageModule($moduleID, "removeRelease")->get();
	DOM::append($reviewDetails, $reviewForm);
	HTML::addClass($reviewForm, "rrForm");
	
	// Project ID
	$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	// Project version
	$input = $form->getInput($type = "hidden", $name = "version", $value = $projectVersion, $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	// Create a row container
	$formRow = DOM::create("div", "", "", "frow");
	$form->append($formRow);
	
	// Get submit button to reject
	$title = moduleLiteral::get($moduleID, "lbl_removeRelease");
	$submitButton = $form->getSubmitButton($title, $id = "", $name = "");
	HTML::addClass($submitButton, "fbutton");
	DOM::append($formRow, $submitButton);
	
	// Create label to explain
	$title = moduleLiteral::get($moduleID, "lbl_removeRelease_notes");
	$label = $form->getLabel($title, $for = "", $class = "flabel");
	DOM::append($formRow, $label);
}



// Return output
$holder = engine::getVar('holder');
return $page->getReport($holder);
//#section_end#
?>