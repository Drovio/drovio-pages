<?php
//#section#[header]
// Module Declaration
$moduleID = 348;

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
importer::import("DEV", "Apps");
importer::import("DEV", "Core");
importer::import("DEV", "Projects");
importer::import("UI", "Forms");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\literal;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Interactive\forms\switchButton;
use \UI\Modules\MContent;
use \DEV\Apps\appManifest;
use \DEV\Core\manifests;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

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

// Build the module content
$pageContent->build("", "appSecuritySettingsPage", TRUE);

// Get application manifests
$appMf = new appManifest($projectID);
$mfPermissions = $appMf->getPermissions();

// Get all core manifest packages
$mf = new manifests();
$allManifests = $mf->getAll();

// Create form
$form = new simpleForm();
$form->build()->engageModule($moduleID)->get();

// Show all enabled packages
$pContainer = HTML::select(".appSecuritySettings .permissions")->item(0);
foreach ($allManifests as $mfID => $mfInfo)
{
	// List only enabled manifests
	if (!$mfInfo['info']['enabled'])
		continue;
	
	// Create permission row with a switch button
	$prow = DOM::create("div", "", "", "prow");
	DOM::append($pContainer, $prow);
	
	// Switch button
	$switch = new switchButton();
	
	$active = in_array($mfID, $mfPermissions);
	$attr = array();
	$attr['id'] = $projectID;
	$attr['mf_id'] = $mfID;
	$sb = $switch->build($action = "", $active, $name = "mf_status")->engageModule($moduleID, "addPermissionPackage", $attr)->get();
	HTML::addClass($sb, "mf_switch");
	DOM::append($prow, $sb);
	
	// Permission package title and description
	$lit = literal::get("sdk.manifest", "mf_".$mfInfo['info']['name']."_title");
	$title = DOM::create("h3", $lit, "", "mf_title");
	DOM::append($prow, $title);
	
	$lit = literal::get("sdk.manifest", "mf_".$mfInfo['info']['name']."_desc");
	$desc = DOM::create("p", $lit, "", "mf_desc");
	DOM::append($prow, $desc);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>