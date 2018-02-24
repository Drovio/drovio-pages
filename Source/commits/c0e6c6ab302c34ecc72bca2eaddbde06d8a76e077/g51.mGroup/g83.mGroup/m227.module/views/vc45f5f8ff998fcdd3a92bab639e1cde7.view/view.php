<?php
//#section#[header]
// Module Declaration
$moduleID = 227;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Model");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("DEV", "BugTracker");
//#section_end#
//#section#[code]
use \API\Model\modules\module;
use \DEV\BugTracker\bugTracker;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Modules\MContent;


// Create Module Page
$HTMLContent = new MContent($moduleID);
$actionFactory = $HTMLContent->getActionFactory();
// Build the module 
$HTMLContent->build("", "issuesView", TRUE);

//
$right = HTML::select('.mContent > .cRight')->item(0); 
$addNew = DOM::create('span', 'ADD NEW');
$actionFactory->setModuleAction($addNew, $moduleID, "newIssue", $holder = "", $attr = array("pid" => $_REQUEST['id']));
DOM::append($right, $addNew);

//
$left = HTML::select('.mContent > .cLeft')->item(0); //DOM::create('div');
// add menu events


$issueList = DOM::create('div');
	$list = module::loadview($moduleID, "issueList");
	DOM::append($issueList, $list);
DOM::append($left, $issueList);









// Return output
return $HTMLContent->getReport();
//#section_end#
?>