<?php
//#section#[header]
// Module Declaration
$moduleID = 353;

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
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Profile\team;
use \API\Security\accountKey;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get team info
$teamID = engine::getVar('id');
$teamName = engine::getVar('name');

// Validate team to show info
if (empty($teamID))
{
	$teams = team::getAccountTeams();
	foreach ($teams as $team)
		if ($team['id'] == $teamID || (!empty($team['uname']) && $team['uname'] == $teamName))
		{
			$teamID = $team['id'];
			break;
		}
}

// Get team information
$teamInfo = team::info($teamID);
$teamName = $teamInfo['name'];
	
// Get whether the account is team admin
$teamAdmin = accountKey::validateGroup($groupName = "TEAM_ADMIN", $context = $teamID, $type = accountKey::TEAM_KEY_TYPE);

// Build the module content
$pageContent->build("", "teamInfoViewer", TRUE);

// Set team information
$teamName = HTML::select(".teamInfo .infoItem.name .infoValue")->item(0);
HTML::innerHTML($teamName, $teamInfo['name']);

$teamName = HTML::select(".teamInfo .infoItem.description .infoValue")->item(0);
HTML::innerHTML($teamName, $teamInfo['description']);

$teamName = HTML::select(".teamInfo .infoItem.uname .infoValue")->item(0);
HTML::innerHTML($teamName, $teamInfo['uname']);

$teamName = HTML::select(".teamInfo .infoItem.teamid .infoValue")->item(0);
HTML::innerHTML($teamName, $teamInfo['id']);

// Add info editor if team admin
$infoEditor = HTML::select(".teamInfo .infoViewer .editor")->item(0);
if ($teamAdmin)
{
	$attr = array();
	$attr['tid'] = $teamID;
	$actionFactory->setModuleAction($infoEditor, $moduleID, "editInfo", ".teamInfo .infoEditor", $attr, $loading = TRUE);
}
else
	HTML::replace($infoEditor, NULL);

// Return output
return $pageContent->getReport();
//#section_end#
?>