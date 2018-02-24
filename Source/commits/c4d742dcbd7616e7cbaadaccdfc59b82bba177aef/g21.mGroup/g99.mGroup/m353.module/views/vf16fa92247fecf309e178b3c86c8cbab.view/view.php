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
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Profile\team;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get team name
$teamID = engine::getVar('id');
$teamName = engine::getVar('name');

// Validate team
$validTeam = FALSE;
$teams = team::getAccountTeams();
foreach ($teams as $team)
	if ($team['id'] == $teamID || (!empty($team['uname']) && $team['uname'] == $teamName))
	{
		$teamInfo = $team;
		$validTeam = TRUE;
		break;
	}

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

// Return output
return $pageContent->getReport();
//#section_end#
?>