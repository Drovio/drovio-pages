<?php
//#section#[header]
// Module Declaration
$moduleID = 371;

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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Profile\team;
use \API\Profile\teamSettings;
use \API\Security\accountKey;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get team info
$teamID = team::getTeamID();
$teamInfo = team::info();
$teamName = $teamInfo['name'];

// Initialize team settings
$ts = new teamSettings($teamID);
	
// Get whether the account is team admin
$teamAdmin = accountKey::validateGroup($groupName = "TEAM_ADMIN", $context = $teamID, $type = accountKey::TEAM_KEY_TYPE);

// Build the module content
$pageContent->build("", "teamInfoViewer", TRUE);

// Set team information
$infoValue = HTML::select(".teamInfo .infoItem.name .infoValue")->item(0);
HTML::innerHTML($infoValue, $teamInfo['name']);

$infoValue = HTML::select(".teamInfo .infoItem.description .infoValue")->item(0);
HTML::innerHTML($infoValue, $teamInfo['description']);

$infoValue = HTML::select(".teamInfo .infoItem.uname .infoValue")->item(0);
HTML::innerHTML($infoValue, $teamInfo['uname']);

$infoValue = HTML::select(".teamInfo .infoItem.teamid .infoValue")->item(0);
HTML::innerHTML($infoValue, $teamInfo['id']);

$value = $ts->get("website_url");
$infoValue = HTML::select(".teamInfo .infoItem.website_url .infoValue")->item(0);
$wl = $pageContent->getWebLink($value, $value, "_blank");
HTML::append($infoValue, $wl);

$value = $ts->get("public_profile");
$infoValue = HTML::select(".teamInfo .infoItem.public_profile .infoValue")->item(0);
HTML::innerHTML($infoValue, ($value ? "&#x02713;" : "&#x02717;"));

$editButton = HTML::select(".teamInfo .editor")->item(0);
if ($teamAdmin)
	$actionFactory->setModuleAction($editButton, $moduleID, "editInfo", ".teamInfo .infoEditor");
else
	HTML::replace($editButton, NULL);

// Return output
return $pageContent->getReport();
//#section_end#
?>