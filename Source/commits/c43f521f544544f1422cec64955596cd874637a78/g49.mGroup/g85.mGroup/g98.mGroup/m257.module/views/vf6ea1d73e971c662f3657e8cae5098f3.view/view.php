<?php
//#section#[header]
// Module Declaration
$moduleID = 257;

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
importer::import("API", "Literals");
importer::import("API", "Profile");
importer::import("DEV", "Modules");
importer::import("DEV", "Projects");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \UI\Modules\MPage;
use \DEV\Projects\project;
use \DEV\Modules\modulesProject;

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
$projectAccounts = $project->getProjectAccounts();

$page->build('', 'modulesPrivilegesPage', TRUE);

$membersList = HTML::select('.modulesPrivileges .memberList')->item(0);
foreach ($projectAccounts as $accountInfo)
{
	// Get account id
	$accountID = $accountInfo['id'];
	
	// Create account item
	$devItem = DOM::create('li');
	$page->SetStaticNav($devItem, "", "", "", "mpNavGroup", "none");
	DOM::append($membersList, $devItem);
	
	// Add ico (and image if any)
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($devItem, $ico);
	$accountInfo = account::info($accountID);
	if (!empty($accountInfo['profile_image_url']))
	{
		$img = DOM::create("img");
		DOM::attr($img, "src", $accountInfo['profile_image_url']);
		DOM::append($ico, $img);
	}
	
	// Account title
	$title = DOM::create("div", $accountInfo['accountTitle'], "", "title");
	DOM::append($devItem, $title);
	
	// Set action for loading privileges
	$attr = array();
	$attr['id'] = modulesProject::PROJECT_ID;
	$attr['aid'] = $accountID;
	$actionFactory->setModuleAction($devItem, $moduleID, "privilegesInfo", ".privilegesInfoHolder", $attr);
}

// Return the report
return $page->getReport();
//#section_end#
?>