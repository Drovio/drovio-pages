<?php
//#section#[header]
// Module Declaration
$moduleID = 211;

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
importer::import("API", "Security");
importer::import("DEV", "Projects");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \API\Profile\account;
use \API\Security\accountKey;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "memberListContainer");

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

// Get whether the account is team admin
$projectAdmin = accountKey::validateGroup($groupName = "PROJECT_ADMIN", $context = $projectID, $type = accountKey::PROJECT_KEY_TYPE);

// Get team members
$members = $project->getProjectAccounts();
foreach ($members as $member)
{
	// Build a row with privileges
	$tmember = DOM::create("div", "", "", "tmember");
	$pageContent->append($tmember);
	
	if ($projectAdmin)
	{
		$settingsIco = DOM::create("div", "", "", "stico");
		DOM::append($tmember, $settingsIco);
		
		// Set module action
		$attr = array();
		$attr['id'] = $projectID;
		$attr['aid'] = $member['id'];
		$actionFactory->setModuleAction($settingsIco, $moduleID, "editRoles", "", $attr);
	}
	
	$tico = DOM::create("div", "", "", "pico");
	DOM::append($tmember, $tico);
	
	// Check profile picture
	$accountInfo = account::info($member['id']);
	if (!empty($accountInfo['profile_image_url']))
	{
		$img = DOM::create("img");
		DOM::attr($img, "src", $accountInfo['profile_image_url']);
		DOM::append($tico, $img);
	}	
	
	$tminfo = DOM::create("div", "", "", "mbinfo");
	DOM::append($tmember, $tminfo);
	
	$mName = DOM::create("div", $member['title'], "", "mbname");
	DOM::append($tminfo, $mName);

	// Get account keys/roles
	$keys = accountKey::get($member['id']);
	$roles = array();
	foreach ($keys as $key)
		if ($key['type_id'] == accountKey::PROJECT_KEY_TYPE && $key['context'] == $projectID)
			$roles[] = $key['groupName'];
	
	$roleContext = implode(", ", $roles);
	$mrl = DOM::create("div", $roleContext, "", "mbrole");
	DOM::append($tminfo, $mrl);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>