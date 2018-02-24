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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("DEV", "Projects");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Profile\account;
use \API\Security\akeys\apiKey;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;
use \DEV\Projects\project;

// Build the content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("", "devApplicationKeys", TRUE);

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];

// Get Account Keys
$projectKeys = apiKey::getProjectAccountKeys($projectID);

// Create key grid list
$keyList = HTML::select(".keylist.projectKeys")->item(0);
$gridList = new dataGridList();
$keyGridList = $gridList->build()->get();
DOM::append($keyList, $keyGridList);

// Set column ratios
$ratios = array();
$ratios['type'] = 0.2;
$ratios['date'] = 0.2;
$ratios['akey'] = 0.5;
$ratios['actions'] = 0.1;
$gridList->setColumnRatios($ratios);

// Set headers
$headers = array();
$headers['type'] = "Type";
$headers['date'] = "Date Created";
$headers['akey'] = "API Key";
$headers['actions'] = "Actions";
$gridList->setHeaders($headers);

// Show all keys
foreach ($projectKeys as $keyInfo)
{
	// Key row
	$row = array();
	$row['type'] = $keyInfo['type_name'];
	$row['date'] = date('M d, Y', $keyInfo['time_created']);
	$row['akey'] = $keyInfo['akey'];

	// Create action container
	$actionContainer = DOM::create("div", "", "", "keyActionContainer");

	// Edit action (show popup)
	$editKey = DOM::create("div", "", "", "act edit");
	DOM::append($actionContainer, $editKey);

	// Set edit action
	$attr = array();
	$attr['id'] = $projectID;
	$attr['akey'] = $keyInfo['akey'];
	$actionFactory->setModuleAction($editKey, $moduleID, "editKeyDialog", "", $attr);

	$row['action'] = $actionContainer;

	// Insert row
	$gridList->insertRow($row);
}

return $pageContent->getReport();
//#section_end#
?>