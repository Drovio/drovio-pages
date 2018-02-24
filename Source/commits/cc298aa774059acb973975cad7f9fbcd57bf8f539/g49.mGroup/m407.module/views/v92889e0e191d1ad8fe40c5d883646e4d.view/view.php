<?php
//#section#[header]
// Module Declaration
$moduleID = 407;

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
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Profile\account;
use \API\Security\akeys\apiKey;
use \UI\Modules\MContent;
use \SYS\Comm\db\dbConnection;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "roadmapListContainer", TRUE);

// Check if is admin enough to edit (add, remove, etc)
$coreAdmin = apiKey::validateGroupName($groupName = "PROJECT_ADMIN", $accountID = account::getInstance()->getAccountID(), $teamID = NULL, $projectID = 1);
$pagesAdmin = apiKey::validateGroupName($groupName = "PROJECT_ADMIN", $accountID = account::getInstance()->getAccountID(), $teamID = NULL, $projectID = 2);
$roadmapAdmin = ($coreAdmin || $pagesAdmin);

// Get all roadmap items
$dbc = new dbConnection();
$q = $pageContent->getQuery("get_all_roadmap");
$result = $dbc->execute($q);
$allRoadmaps = $dbc->fetch($result, TRUE);

$expectedContainer = HTML::select(".rd_list.expected")->item(0);
$deliveredContainer = HTML::select(".rd_list.delivered")->item(0);
foreach ($allRoadmaps as $rdInfo)
{
	// Create roadmap item
	$ritem = DOM::create("div", "", $rdInfo['hashtag'], "ritem");
	
	// Create icon
	$ricon = DOM::create("div", "", "", "ricon");
	DOM::append($ritem, $ricon);
	
	// Set date delivered (or expected)
	$dateFromMysql = (empty($rdInfo['date_delivered']) ? $rdInfo['date_expected'] : $rdInfo['date_delivered']);
	$time = strtotime($dateFromMysql);
	$dateFormated = date("M d, Y", $time);
	$rdate = DOM::create("div", $dateFormated, "", "rdate");
	DOM::append($ritem, $rdate);
	
	// Create title
	$rtitle = DOM::create("div", $rdInfo['title'], "", "rtitle");
	DOM::append($ritem, $rtitle);
	
	if ($roadmapAdmin)
	{
		// Add edit class
		HTML::addClass($ritem, "edit");
		
		// Set action to title
		$attr['rid'] = $rdInfo['id'];
		$actionFactory->setModuleAction($rtitle, $moduleID, "editRoadmap", "", $attr);
	}
	
	// Create description
	$rdesc = DOM::create("div", "", "", "rdesc");
	DOM::innerHTML($rdesc, $rdInfo['description']);
	DOM::append($ritem, $rdesc);
	
	
	
	// Decide where to append it
	if (empty($rdInfo['date_delivered']))
		DOM::append($expectedContainer, $ritem);
	else
		DOM::append($deliveredContainer, $ritem);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>