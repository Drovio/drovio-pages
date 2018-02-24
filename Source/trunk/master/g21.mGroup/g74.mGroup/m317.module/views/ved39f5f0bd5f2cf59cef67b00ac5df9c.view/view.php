<?php
//#section#[header]
// Module Declaration
$moduleID = 317;

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
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Security\akeys\apiKey;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the application view content
$pageContent->build("", "myAccountKeys", TRUE);
$keyListContainer = HTML::select(".keyListContainer")->item(0);

// Get all application keys as a team
$teamKeys = apiKey::getAccountKeys($accountID = NULL);
if (!empty($teamKeys))
{
	// Create data grid list
	$gridList = new dataGridList();
	$keysList = $gridList->build($id = "keysList", $checkable = FALSE, $withBorder = TRUE)->get();
	DOM::append($keyListContainer, $keysList);
	
	// Set column ratios
	$ratios = array();
	$ratios['type'] = 0.15;
	$ratios['date'] = 0.15;
	$ratios['team'] = 0.15;
	$ratios['project'] = 0.15;
	$ratios['akey'] = 0.3;
	$ratios['actions'] = 0.1;
	$gridList->setColumnRatios($ratios);
	
	// Set headers
	$headers = array();
	$headers['type'] = "Type";
	$headers['date'] = "Date Created";
	$headers['team'] = "Team";
	$headers['project'] = "Project";
	$headers['akey'] = "API Key";
	$headers['actions'] = "Actions";
	$gridList->setHeaders($headers);
	
	// Show all keys
	foreach ($teamKeys as $keyInfo)
	{
		// Key row
		$row = array();
		$row['type'] = $keyInfo['type_name'];
		$row['date'] = date('M d, Y', $keyInfo['time_created']);
		$row['team'] = $keyInfo['team_id'];
		$row['project'] = $keyInfo['project_id'];
		$row['akey'] = $keyInfo['akey'];
		
		// Create action container
		$actionContainer = DOM::create("div", "", "", "keyActionContainer");
		
		// Edit action (show popup)
		$editKey = DOM::create("div", "", "", "act edit");
		DOM::append($actionContainer, $editKey);
		
		// Set edit action
		$attr = array();
		$attr['app_id'] = $keyInfo['project_id'];
		$attr['akey'] = $keyInfo['akey'];
		$actionFactory->setModuleAction($editKey, $moduleID, "editKeyDialog", "", $attr);
		
		$row['action'] = $actionContainer;
		
		// Insert row
		$gridList->insertRow($row);
	}
}

// Return output
return $pageContent->getReport();
//#section_end#
?>