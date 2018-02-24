<?php
//#section#[header]
// Module Declaration
$moduleID = 260;

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
importer::import("API", "Profile");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\team;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Presentation\dataGridList;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "searchResults");

// Get project id
$projectID = engine::getVar('id');

// Search accounts
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "search_accounts");
$attr = array();
$attr['tid'] = team::getTeamID();
$attr['q'] = engine::getVar('search_q');
$result = $dbc->execute($q, $attr);
$accounts = $dbc->fetch($result, TRUE);

// Build form
$form = new simpleForm();
$addMembersForm = $form->build()->engageModule($moduleID, "addMembers")->get();
$pageContent->append($addMembersForm);

// Add project id
$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "");
$form->append($input);

// Add grid list
$gridList = new dataGridList();
$accountList = $gridList->build("", TRUE)->get();
$form->append($accountList);

// Set headers
$headers = array();
$headers[] = "ID";
$headers[] = "Title";
$gridList->setHeaders($headers);

foreach ($accounts as $accountData)
{
	$row = array();
	$row[] = $accountData['id'];
	$row[] = $accountData['title'];
	
	$gridList->insertRow($row, "accs[".$accountData['id']."]");
}

// Get team userGroups
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_team_usergroups");
$attr = array();
$result = $dbc->execute($q);
$teamGroups = $dbc->toArray($result, "id", "name");

$title = moduleLiteral::get($moduleID, "lbl_groupName");
$input = $form->getResourceSelect($name = "role", $multiple = FALSE, $class = "", $teamGroups, $selectedValue = "");
$form->insertRow($title, $input, $required = TRUE);

// Return output
return $pageContent->getReport(".teamMembersList .searchResultsContainer");
//#section_end#
?>