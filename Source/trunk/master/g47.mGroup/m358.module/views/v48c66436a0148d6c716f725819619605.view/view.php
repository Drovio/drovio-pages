<?php
//#section#[header]
// Module Declaration
$moduleID = 358;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("DEV", "Apps");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Model\apps\application;
use \API\Profile\team;
use \API\Profile\teamSettings;
use \UI\Modules\MContent;
use \DEV\Apps\application as devApp;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get project id and name
$applicationID = engine::getVar('id');
$applicationName = engine::getVar('name');

// Get application info from project
$app = new devApp($applicationID, $applicationName);
$appInfo = $app->info();

// Get project data
$applicationID = $appInfo['id'];
$appInfo = application::getApplicationInfo($applicationID);

// Build the module content
$pageContent->build("", "appDetailsContainer", TRUE);

// Set application information
$infoValue = HTML::select(".appDetails .infoItem.version .infoValue")->item(0);
HTML::innerHTML($infoValue, $appInfo['version']);

$infoValue = HTML::select(".appDetails .infoItem.time_updated .infoValue")->item(0);
HTML::innerHTML($infoValue, date("M d, Y", $appInfo['time_created']));

// Get team info
$ts = new teamSettings($appInfo['team_id']);
$publicPage = $ts->get("public_profile");
$infoValue = HTML::select(".appDetails .infoItem.team .infoValue")->item(0);
if ($publicPage)
{
	$teamInfo = team::info($appInfo['team_id']);
	if (empty($teamInfo['uname']))
	{
		$attr = array();
		$attr['id'] = $appInfo['team_id'];
		$url = url::resolve("www", "/profile/index.php", $attr);
	}
	else
		$url = url::resolve("www", "/profile/".$teamInfo['uname']);
	$wl = $pageContent->getWebLink($url, $appInfo['teamName'], "_blank");
	DOM::append($infoValue, $wl);
}
else
	HTML::innerHTML($infoValue, $appInfo['teamName']);

// Return output
return $pageContent->getReport();
//#section_end#
?>