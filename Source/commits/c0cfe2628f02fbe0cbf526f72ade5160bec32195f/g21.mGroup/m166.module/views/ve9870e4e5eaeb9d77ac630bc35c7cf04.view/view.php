<?php
//#section#[header]
// Module Declaration
$moduleID = 166;

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
importer::import("API", "Profile");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;

$page = new MPage($moduleID);

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "myRelationsPage", TRUE);



// Get teams (and companies in a later version)
$teams = team::getAccountTeams();
$teamList = HTML::select(".myRelationsPage .team_list")->item(0);
foreach ($teams as $team)
{
	if ($team['id'] == team::getTeamID())
	{
		// Clear current team content
		$noTeam = HTML::select(".currentTeam .noTeam")->item(0);
		HTML::replace($noTeam, NULL);
		
		$teamName = HTML::select(".currentTeam .teamName")->item(0);
		HTML::nodeValue($teamName, $team['name']);
	}
	
	// Add team to list
	$teamDiv = DOM::create("li", $team['name'], "", "tmr");
	DOM::append($teamList, $teamDiv);
}


// Create a new team
$createTeamContainer = HTML::select(".myRelationsPage .createTeam")->item(0);
$form = new simpleForm();
$createTeamForm = $form->build($moduleID, "createTeam", TRUE)->get();
DOM::append($createTeamContainer, $createTeamForm);

return $page->getReport();
//#section_end#
?>