<?php
//#section#[header]
// Module Declaration
$moduleID = 284;

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
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \UI\Presentation\popups\popup;
use \DEV\Version\tools\commitManager;
use \DEV\Projects\project;

// Get project id
$projectID = $_REQUEST['id'];

$project = new project($projectID);
$projectInfo = $project->info();
$projectTitle = $projectInfo['title'];

// Create Module Page
$id = "pcmm".md5("commitManager_project_".$projectID);
$vcs = new commitManager($id, $projectID);
$vcsControl = $vcs->build($projectTitle." | Commit")->get();


// Build the popup
$vcsPopup = new popup();
$vcsPopup->type($type = "persistent", $toggle = FALSE);
$vcsPopup->background(TRUE);
$vcsPopup->position("user");

return $vcsPopup->build($vcsControl)->getReport();


/*
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Geoloc\datetimer;
use \UI\Modules\MContent;
use \UI\Presentation\frames\dialogFrame;
use \UI\Presentation\dataGridList;
use \DEV\Version\vcs;


// Get website id
$websiteID = $_GET['id'];

// Create Module Page
//$pageContent = new MContent($moduleID);
//$actionFactory = $pageContent->getActionFactory();

// Build the module content
//$pageContent->build("", "wsPublisherDialog");


// Append content
//$frame->append($pageContent->get());

// Build frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "title");
$frame->build($title, "wsPublisherFrame");
$form = $frame->getFormFactory();


// Set website id
$input = $form->getInput($type = "hidden", $name = "id", $value = $websiteID, $class = "", $autofocus = FALSE, $required = FALSE);
$frame->append($input);


// Get working items
$vcs = new vcs($websiteID);
$workingItems = $vcs->getWorkingItems();
$authors = $vcs->getAuthors();


// Force commit items
$forceItems = array();
foreach ($workingItems as $id => $item)
	if ($item['force_commit'])
		$forceItems[$id] = $workingItems[$id];

$ratios = array();
$ratios[] = 0.6;
$ratios[] = 0.2;
$ratios[] = 0.2;

$headers = array();
$headers[] = literal::get("sdk.DEV.Version", "lbl_commitManager_itemPath", array(), FALSE);
$headers[] = literal::get("sdk.DEV.Version", "lbl_commitManager_lastAuthor", array(), FALSE);
$headers[] = literal::get("sdk.DEV.Version", "lbl_commitManager_lastUpdate", array(), FALSE);

if (count($forceItems) > 0)
{
	// Force Commit Item List
	$dataList = new dataGridList();
	$fCommitList = $dataList->build($id = "fCommitItems", $checkable = FALSE)->get();
	$frame->append($fCommitList);
	
	$dataList->setColumnRatios($ratios);
	$dataList->setHeaders($headers);
	
	foreach ($forceItems as $id => $item)
	{
		$rowContents = array();
		$rowContents[] = $item['path'];
		$rowContents[] = $item['last-edit-author'];
		$rowContents[] = datetimer::live($item['last-edit-time'], $format = 'd F, Y \a\t H:i');
		$dataList->insertRow($rowContents);
	}
}

// Commit Item List
$dataList = new dataGridList();
$commitList = $dataList->build($id = "commitItems", $checkable = TRUE)->get();

$dataList->setColumnRatios($ratios);
$dataList->setHeaders($headers);

foreach ($workingItems as $id => $item)
{
	// Skip force commit items
	if (isset($forceItems[$id]))
		continue;
		
	$rowContents = array();
	$rowContents[] = $item['path'];
	$rowContents[] = $item['last-edit-author'];
	$rowContents[] = datetimer::live($item['last-edit-time'], $format = 'd F, Y \a\t H:i');
	$dataList->insertRow($rowContents, $checkName = "citem[".$id."]", $checked = FALSE);
}

$frame->append($commitList);


// Return frame output
return $frame->getFrame();
*/
//#section_end#
?>