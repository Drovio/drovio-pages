<?php
//#section#[header]
// Module Declaration
$moduleID = 257;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \DEV\Projects\project;

$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$page->build('', 'modulesPrivilegesPage', TRUE);

// Static Navigation Attributes
$nav_ref = "privilegesInfoHolder";
$nav_targetcontainer = "privilegesInfo";
$nav_targetgroup = "privilegesInfo";
$nav_navgroup = "privilegesInfo";

// Project Accounts
$projectID = $_GET['pid'];
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_project_accounts");
$attr = array();
$attr['pid'] = $projectID;
$result = $dbc->execute($q, $attr);
$projectAccounts = $dbc->fetch($result, TRUE);

$membersList = HTML::select('.modulesPrivileges .mlist')->item(0);
foreach ($projectAccounts as $pAccount)
{
	$devItem = DOM::create('li');
	NavigatorProtocol::staticNav($devItem, $nav_ref, $nav_targetcontainer, $nav_targetgroup, $nav_navgroup, $nav_display);
	DOM::append($membersList, $devItem);
	
	// Get account title
	$accountTitle = $pAccount['title'];
	if ($accountTitle == "Personal Account") // Compatibility with old account titles
		$accountTitle = $pAccount['firstname']." ".$pAccount['lastname'];
	
	$devName = DOM::create("span", $accountTitle);
	$itemInner = DOM::create('div', $devName);
	DOM::append($devItem, $itemInner);
	
	$attr = array();
	$attr['aid'] = $pAccount['accountID'];
	$actionFactory->setModuleAction($devItem, $moduleID, "privilegesInfo", "#privilegesInfoHolder", $attr);
}

// Main Content
$mainContent = HTML::select('.modulesPrivileges .privilegesListWrapper > .inner')->item(0);


// Return the report
return $page->getReport();
//#section_end#
?>