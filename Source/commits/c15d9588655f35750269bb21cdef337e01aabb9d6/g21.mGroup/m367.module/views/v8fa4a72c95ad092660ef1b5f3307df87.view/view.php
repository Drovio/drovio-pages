<?php
//#section#[header]
// Module Declaration
$moduleID = 367;

// Inner Module Codes
$innerModules = array();
$innerModules['accountProfile'] = 39;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Profile\account;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$page->build("", "ProfilePageContainer");

// Get id or name
$profileID = engine::getVar("id");
$profileName = engine::getVar("name");
if (empty($profileID) && empty($profileName))
{
	// Get account info
	$accountInfo = account::info($accountID);
	
	// Redirect to friendly url
	if (!empty($accountInfo['username']))
		$url = url::resolve("www", "/profile/".$accountInfo['username']);
	else
	{
		$params = array();
		$params['id'] = account::getAccountID();
		$url = url::resolve("www", "/profile/index.php", $params);
	}
	return $actionFactory->getReportRedirect($url, "", $formSubmit = TRUE);
}

// Initialize connection and attributes
$dbc = new dbConnection();
$attr = array();
$attr['id'] = $profileID;
$attr['name'] = $profileName;


// Check if it is a person
$q = module::getQuery($moduleID, "search_account");
$result = $dbc->execute($q, $attr);
if ($dbc->get_num_rows($result))
{
	// Get module container
	$mContainer = $page->getModuleContainer($innerModules['accountProfile'], $viewName = "", $attr, $startup = TRUE, $containerID = "accountProfilePageContainer", $loading = TRUE, $preload = TRUE);
	$page->append($mContainer);
	
	return $page->getReport();
}

// Check if it is a company
$q = "";//module::getQuery("search_company");
//$result = $dbc->execute($q, $attr);
if ($dbc->get_num_rows($result))
{
	// Get module container
	$accountInfo = $dbc->fetch($result);
	$mContainer = $page->getModuleContainer($innerModules['companyProfile'], $viewName = "", $attr, $startup = TRUE, $containerID = "companyProfilePageContainer", $loading = TRUE, $preload = TRUE);
	$page->append($mContainer);
	
	return $page->getReport();
}


// Return output
return $page->getReport();
//#section_end#
?>