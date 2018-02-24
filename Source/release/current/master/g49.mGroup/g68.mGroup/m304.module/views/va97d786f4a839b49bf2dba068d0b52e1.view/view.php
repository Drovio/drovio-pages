<?php
//#section#[header]
// Module Declaration
$moduleID = 304;

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
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Presentation\dataGridList;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "searchResults");

// Get application id
$applicationID = engine::getVar('id');

// Search accounts
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "search_teams");
$attr = array();
$attr['pid'] = $applicationID;
$attr['q'] = engine::getVar('search_q');
$result = $dbc->execute($q, $attr);
$teams = $dbc->fetch($result, TRUE);

// Build form
$form = new simpleForm();
$addTeamForm = $form->build()->engageModule($moduleID, "addPrivateTeams")->get();
$pageContent->append($addTeamForm);

// Add project id
$input = $form->getInput($type = "hidden", $name = "id", $value = $applicationID, $class = "");
$form->append($input);

// Add grid list
$gridList = new dataGridList();
$accountList = $gridList->build("", TRUE)->get();
$form->append($accountList);

// Set headers
$headers = array();
$headers[] = "Team ID";
$headers[] = "Team Name";
$gridList->setHeaders($headers);

foreach ($teams as $teamData)
{
	$row = array();
	$row[] = $teamData['id'];
	$row[] = $teamData['name'];
	
	$gridList->insertRow($row, "tms[".$teamData['id']."]");
}

// Return output
return $pageContent->getReport(".searchResultsContainer");
//#section_end#
?>