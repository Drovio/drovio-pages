<?php
//#section#[header]
// Module Declaration
$moduleID = 188;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Geoloc");
importer::import("DEV", "Projects");
importer::import("DEV", "Version");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Geoloc\datetimer;
use \UI\Modules\MContent;
use \UI\Presentation\dataGridList;
use \DEV\Projects\project;
use \DEV\Version\vcs;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "vcsWorkingItems");

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

// Get vcs object
$vcs = new vcs($projectID);
$allWorkingItems = $vcs->getAllWorkingItems();

$gridList = new dataGridList();
$workingList = $gridList->build($id = "workingItems", $checkable = FALSE)->get();
$pageContent->append($workingList);

$ratios = array();
$ratios[] = 0.4;
$ratios[] = 0.2;
$ratios[] = 0.2;
$ratios[] = 0.2;
$gridList->setColumnRatios($ratios);

$headers = array();
$headers[] = "Item Path";
$headers[] = "Initial Author";
$headers[] = "Last Author";
$headers[] = "Last Update";
$gridList->setHeaders($headers);

foreach ($allWorkingItems as $id => $item)
{
	$rowContents = array();
	$rowContents[] = $item['path'];
	$rowContents[] = $item['author-title'];
	$rowContents[] = $item['last-edit-author-title'];
	$rowContents[] = datetimer::live($item['last-edit-time'], $format = 'd F, Y \a\t H:i');
	$gridList->insertRow($rowContents);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>