<?php
//#section#[header]
// Module Declaration
$moduleID = 259;

// Inner Module Codes
$innerModules = array();
$innerModules['teamMembers'] = 260;

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
use \API\Security\accountKey;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;

$page = new MPage($moduleID);

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "myRelationsPage", TRUE);



// Get teams (and companies in a later version)
$teams = team::getAccountTeams();
$keys = accountKey::get();
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
		
		// Create team members module container
		$membersContainer = HTML::select(".currentTeam .members")->item(0);
		$teamMembers = $page->getModuleContainer($innerModules['teamMembers'], "", $attr = array(), $startup = FALSE, $containerID = "teamMembers");
		DOM::append($membersContainer, $teamMembers);
	}
	
	// Add team to list
	$teamDiv = DOM::create("li", "", "", "tmr");
	DOM::append($teamList, $teamDiv);
	
	// Add team name
	$tn = DOM::create("span", $team['name'], "", "tn");
	DOM::append($teamDiv, $tn);
	
	// Add team roles
	$roles = array();
	foreach ($keys as $key)
		if ($key['type_id'] == 1 && $key['context'] == $team['id'])
			$roles[] = $key['groupName'];
	
	
	$roleContext = implode(", ", $roles);
	$gn = DOM::create("span", $roleContext, "", "gn");
	DOM::append($teamDiv, $gn);
}


// Create a new team
$createTeamContainer = HTML::select(".myRelationsPage .createTeam")->item(0);
$form = new simpleForm();
$createTeamForm = $form->build($moduleID, "createTeam", TRUE)->get();
DOM::append($createTeamContainer, $createTeamForm);

$title = moduleLiteral::get($moduleID, "lbl_teamName");
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

return $page->getReport();
//#section_end#
?>