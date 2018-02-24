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
importer::import("DEV", "Projects");
importer::import("DEV", "Version");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the content
$pageContent->build("", "vcsBranchesContainer", TRUE);

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
if (!$projectInfo['public'])
{
	// Create error notification
	
	// Return output
	return $pageContent->getReport();
}

// Get vcs object
$vcs = new vcs($projectID);

// Build branch list
$headBranch = $vcs->getHeadBranch();
$branches = $vcs->getBranches();
$bList = HTML::select(".vcsBranches .bList")->item(0);
foreach ($branches as $branchName => $branchInfo)
{
	$bl = DOM::create("li", "", "", "bli");
	DOM::append($bList, $bl);
	
	// Branch Name
	$bName = DOM::create("div", $branchName, "", "bname");
	DOM::append($bl, $bName);
	if ($branchInfo['head'])
		HTML::addClass($bName, "head");
	
	// Branch/Trunk
	$bExplorer = DOM::create("div", "", "", "bcontrols");
	DOM::append($bl, $bExplorer);
	
	$bTrunk = DOM::create("span", "Trunk", "", "br");
	DOM::append($bExplorer, $bTrunk);
	$attr = array();
	$attr['id'] = $projectID;
	$attr['branch'] = $branchName;
	$attr['type'] = "t";
	$actionFactory->setModuleAction($bTrunk, $moduleID, "repo_branchExplorer", ".vcsBranches .bExplorer", $attr, $loading = TRUE);
	
	$bBranch = DOM::create("span", "Branch", "", "br");
	DOM::append($bExplorer, $bBranch);
	$attr['type'] = "b";
	$actionFactory->setModuleAction($bBranch, $moduleID, "repo_branchExplorer", ".vcsBranches .bExplorer", $attr, $loading = TRUE);
	
	$bRelease = DOM::create("span", "Release", "", "br");
	DOM::append($bExplorer, $bRelease);
	$attr['type'] = "r";
	$actionFactory->setModuleAction($bRelease, $moduleID, "repo_branchExplorer", ".vcsBranches .bExplorer", $attr, $loading = TRUE);
}

return $pageContent->getReport();
//#section_end#
?>