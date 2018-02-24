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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Geoloc\datetimer;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Interactive\forms\switchButton;
use \UI\Modules\MContent;
use \UI\Presentation\dataGridList;

// Get application id
$applicationID = engine::getVar("id");

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "audienceSettingsContainer", TRUE);



// Get application center application info
$market = array();
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "appcenter_info");
$attr = array();
$attr['id'] = $applicationID;
$result = $dbc->execute($q, $attr);
if ($result)
	$market = $dbc->fetch($result);

$attr = array();
$attr['id'] = $applicationID;
$attr['type'] = "apc";
$switch = new switchButton();
$sb = $switch->build($action = "", $active = ($market['active'] == "1"), $name = "active")->engageModule($moduleID, "setAudience", $attr)->get();

$switchContainer = HTML::select(".audienceSettings .srow.apc .switchContainer")->item(0);
DOM::append($switchContainer, $sb);


// Get boss market application info
$market = array();
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "bossmarket_info");
$attr = array();
$attr['id'] = $applicationID;
$result = $dbc->execute($q, $attr);
if ($result)
	$market = $dbc->fetch($result);

$attr = array();
$attr['id'] = $applicationID;
$attr['type'] = "enp";
$switch = new switchButton();
$sb = $switch->build($action = "", $active = ($market['active'] == "1"), $name = "active")->engageModule($moduleID, "setAudience", $attr)->get();

$switchContainer = HTML::select(".audienceSettings .srow.enp .switchContainer")->item(0);
DOM::append($switchContainer, $sb);




// Add private team list
$q = module::getQuery($moduleID, "get_private_teams");
$attr = array();
$attr['id'] = $applicationID;
$result = $dbc->execute($q, $attr);
$privates = $dbc->fetch($result, TRUE);
$teamList = HTML::select(".privateSettings .teamList")->item(0);
if (count($privates) > 0)
{
	// Create grid list
	$gridList = new dataGridList();
	$privateList = $gridList->build()->get();
	HTML::append($teamList, $privateList);
	
	// Set headers
	$headers = array();
	$headers[] = "Team ID";
	$headers[] = "Team Name";
	$headers[] = "Date Created";
	$headers[] = "Team Version";
	$headers[] = "Remove";
	$gridList->setHeaders($headers);
	
	// Set records
	foreach ($privates as $privateTeamData)
	{
		$row = array();
		$row[] = $privateTeamData['team_id'];
		$row[] = $privateTeamData['team_name'];
		$row[] = datetimer::live($privateTeamData['time_created']);
		$row[] = $privateTeamData['version'];
		
		// Remove form
		$form = new simpleForm();
		$removeForm = $form->build("", FALSE)->engageModule($moduleID, "removePrivateTeam")->get();
		
		// Add hidden values
		$input = $form->getInput($type = "hidden", $name = "id", $value = $applicationID, $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "tid", $value = $privateTeamData['team_id'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$btn = $form->getSubmitButton($title = "Remove", $id = "");
		$form->append($btn);
		
		$row[] = $removeForm;
		$gridList->insertRow($row);
	}
	
}

// Search team form
$formContainer = HTML::select(".privateSettings .searchContainer .searchForm")->item(0);
$form = new simpleForm("searchForm");
$searchForm = $form->build($moduleID, "searchTeams", FALSE)->get();
DOM::append($formContainer, $searchForm);

// Add project id
$input = $form->getInput($type = "hidden", $name = "id", $value = $applicationID, $class = "");
$form->append($input);

$ph = moduleLiteral::get($moduleID, "lbl_teamSearch_ph", array(), FALSE);
$input = $form->getInput($type = "search", $name = "search_q", $value = "", $class = "tminp");
DOM::attr($input, "placeholder", $ph);
$form->append($input);

$title = moduleLiteral::get($moduleID, "lbl_search");
$button = $form->getSubmitButton($title, $id = "btn_search");
$form->append($button);



// Return output
return $pageContent->getReport();
//#section_end#
?>