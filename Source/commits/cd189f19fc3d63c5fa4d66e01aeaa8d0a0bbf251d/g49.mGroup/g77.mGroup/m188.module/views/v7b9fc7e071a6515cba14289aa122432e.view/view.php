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
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("DEV", "Version");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Resources\filesystem\directory;
use \API\Literals\moduleLiteral;
use \API\Geoloc\datetimer;
use \UI\Modules\MContent;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the content
$pageContent->build("", "vcsReleasesContainer", TRUE);

// Get project id and name
$projectID = $_REQUEST['id'];
$projectName = $_REQUEST['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

$releasePackagesContainer = HTML::select(".vcsReleases .releasePackagesContainer .list")->item(0);

// Get releases
$vcs = new vcs($projectID);
$branchName = $_GET['bn'];
$releases = $vcs->getReleases();
$packages = $releases[$branchName]['packages'];
foreach ($packages as $packageID => $packageData)
{
	$packageContainer = DOM::create("div", "", "package_".$packageID, "branchPackage");
	DOM::append($releasePackagesContainer, $packageContainer);
	
	// Get releases
	$packageReleases = $packageData['releases'];
	
	// Package Row
	$packageRow = DOM::create("div", "", "package_".$branchName."_".$packageID, "packageRow");
	DOM::append($packageContainer, $packageRow);
	
	$attr = array();
	$attr['id'] = $projectID;
	$attr['branch'] = $branchName;
	$attr['pid'] = $packageID;
	$actionFactory->setModuleAction($packageRow, $moduleID, "releaseBuilds", ".vcsReleases .releaseBuildsContainer", $attr, $loading = TRUE);
	
	$latestRelease = reset($packageReleases);
	
	// Set release date
	$date = datetimer::live($latestRelease['time']);
	$releaseDate = DOM::create("div", $date, "", "releaseDate");
	DOM::append($packageRow, $releaseDate);
	
	// Set release info
	$releaseInfo = DOM::create("div", "", "", "releaseInfo");
	DOM::append($packageRow, $releaseInfo);
	$releaseTitle = DOM::create("h4", $latestRelease['title'], "", "releaseTitle");
	DOM::append($releaseInfo, $releaseTitle);
	$version = DOM::create("div", $latestRelease['version'].".".$latestRelease['build'], "", "releaseVersion");
	DOM::append($releaseInfo, $version);
}

// Release button
$releaseBtn = HTML::select(".releaseBtn")->item(0);
$attr = array();
$attr['pid'] = $projectID;
$actionFactory->setModuleAction($releaseBtn, $moduleID, "releaseProject", "", $attr, $loading = TRUE);

// No releases notification
if (count($releases) == 0)
{
	$noReleases = DOM::create("p", "There are no releases in this repository yet.");
	DOM::append($relPackageContainer, $noReleases);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>