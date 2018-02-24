<?php
//#section#[header]
// Module Declaration
$moduleID = 362;

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
importer::import("API", "Security");
importer::import("DEV", "Projects");
importer::import("DEV", "WebEngine");
importer::import("DEV", "Websites");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Security\accountKey;
use \UI\Modules\MPage;
use \DEV\Projects\projectLibrary;
use \DEV\WebEngine\webCoreProject;
use \DEV\Websites\website;


// Get token to validate
$token = engine::getVar("token");

// Get website info
$websiteID = engine::getVar("id");
$websiteName = engine::getVar("name");
$website = new website($websiteID, $websiteName);
// Get website id
$websiteID = $website->getID();
define("websiteID", $websiteID);

// Validate token
if (!accountKey::validate($token, $websiteID, $type = accountKey::PROJECT_KEY_TYPE))
{
	// Show error
	echo "Your token is invalid.";die();
}

// Get Web Engine Core production path
$version = projectLibrary::getLastProjectVersion(webCoreProject::PROJECT_ID);
$productionPath = projectLibrary::getPublishedPath(webCoreProject::PROJECT_ID, $version);

// Define website development constants
define("wsystemRoot", systemRoot);
define("wsdkRoot", $productionPath."/SDK/");
define("wsdkRepoRoot", "/.developer/Repository/p6e3b463e3106e4d9d1377738a5a0a180.project/Source/trunk/master/SDK/");
define("innerClassPath", ".object/src/class.php");

// Disable site inner path
define("siteInnerPath", "");

// Define web core lib root
define("wclibRoot", $productionPath);
define("wcrsrcRoot", wclibRoot.projectLibrary::RSRC_FOLDER);
// COMPATIBILITY
define("wrsrcRoot", wclibRoot.projectLibrary::RSRC_FOLDER);

define("_RB_WEBSITE_", 1);
define("_RB_WEBSITE_DEV_", 1);


// Get website and page path to load
$websitePagePath = engine::getVar("page");

// Get project's root folder
$websiteSourceFolder = $website->getRootFolder()."/Source/trunk/master/";
define("websiteRoot", $websiteSourceFolder);

// Define web core lib root
define("wslibRoot", $websiteSourceFolder."/lib/ws/");
define("wsrsrcRoot", $website->getResourcesFolder());

// Require Web Engine Core importer
importer::req(wsdkRoot."/WAPI/Platform/importer.php", $root = TRUE, $once = TRUE);
use \WAPI\Platform\importer as webimporter;

// Import page loader
webimporter::import("WUI", "Core", "WSPage");
use \WUI\Core\WSPage;

// Build HTML Page
$htmlPage = WSPage::getInstance($pageAttributes);
$htmlPage->build($websitePagePath);
$websiteHTML =  $htmlPage->getHTML();

// Replace urls
$fromUrl = url::resolve("www", "/.library/", array(), NULL, TRUE);
$toUrl = url::resolve("lib", "/");
$websiteHTML = str_replace($fromUrl, $toUrl, $websiteHTML);

// Return html
return $websiteHTML;
//#section_end#
?>