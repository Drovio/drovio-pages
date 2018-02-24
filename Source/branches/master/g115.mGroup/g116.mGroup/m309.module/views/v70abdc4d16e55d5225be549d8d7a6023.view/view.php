<?php
//#section#[header]
// Module Declaration
$moduleID = 309;

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
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("", "vcsOverviewContainer", TRUE);

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

// Check if project is open
if (!$projectInfo['open'])
{
	// Create error notification
	
	// Return output
	return $pageContent->getReport();
}

// Get vcs information
$vcs = new vcs($projectID);
$headBranch = $vcs->getHeadBranch();
$authors = $vcs->getAuthors();
$value = HTML::select(".stats .sr.authors .value")->item(0);
HTML::innerHTML($value, "".count($authors));

$branches = $vcs->getBranches();
$value = HTML::select(".stats .sr.branches .value")->item(0);
HTML::innerHTML($value, "".count($branches));

$releases = $vcs->getReleases();
$releaseCount = 0;
foreach ($branches as $branchName => $brachInfo)
	$releaseCount += count($releases[$branchName]['packages']);
$value = HTML::select(".stats .sr.releases .value")->item(0);
HTML::innerHTML($value, "".$releaseCount);

$value = HTML::select(".stats .sr.headBranch .value")->item(0);
HTML::innerHTML($value, $headBranch);

$headReleases = $releases[$headBranch];
if (empty($headReleases['packages']))
	$branchVersion = " - ";
else
{
	$currentVersion = $headReleases['current'];
	$headVersion = $headReleases['packages']["v".$currentVersion]['version'];
	$headBuild = $headReleases['packages']["v".$currentVersion]['build'];
	$branchVersion = $headVersion."-".$headBuild;
}
$value = HTML::select(".stats .sr.headVersion .value")->item(0);
HTML::innerHTML($value, $branchVersion);

// Return output
return $pageContent->getReport($holder);
//#section_end#
?>