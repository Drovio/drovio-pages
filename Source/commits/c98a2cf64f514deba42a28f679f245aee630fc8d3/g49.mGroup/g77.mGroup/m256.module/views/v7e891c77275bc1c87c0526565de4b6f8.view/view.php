<?php
//#section#[header]
// Module Declaration
$moduleID = 256;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\project;

// Create Module Page
$page = new MPage($moduleID);


// Get project id and name
$projectID = $_GET['id'];
$projectName = $_GET['name'];

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];
$projectType = $projectInfo['type'];

// Build module page
$page->build("Market Settings | ".$projectTitle, "projectMarketSettings", TRUE);


//Settings Form
$settingsFormContainer = HTML::select(".settingsContainer.settings .formContainer")->item(0);

//Create Form
$form = new simpleForm();
$settingsForm = $form->build()->engageModule($moduleID, "updateSettings")->get();
DOM::append($settingsFormContainer, $settingsForm);


// Project ID
$input= $form->getInput("hidden", "id", $projectID);
$form->append($input);

// If project is of application type, show choices for appstore and boss market

// Get application center application info
$market = array();
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "appcenter_info");
$attr = array();
$attr['id'] = $projectID;
$result = $dbc->execute($q, $attr);
if ($result)
	$market = $dbc->fetch($result);

$title = moduleLiteral::get($moduleID, "lbl_appCenter");
$label = $form->getLabel($title);
$input= $form->getInput("checkbox", "ac_active", $value = ($market['active'] == "1"), $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($label, $input, $required = FALSE, $notes = "");

// Application Center Tags
$title = moduleLiteral::get($moduleID, "lbl_appCenter_tags");
$label = $form->getLabel($title);
$input = $form->getTextarea("ac_tags", $market['tags'], "",FALSE);
$form->insertRow($label, $input, $required = FALSE, $notes = "");



// Get application center application info
$market = array();
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "bossmarket_info");
$attr = array();
$attr['id'] = $projectID;
$result = $dbc->execute($q, $attr);
if ($result)
	$market = $dbc->fetch($result);
	
$title = moduleLiteral::get($moduleID, "lbl_bssMarket");
$label = $form->getLabel($title);
$input= $form->getInput("checkbox", "bm_active", $value = ($market['active'] == "1"), $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($label, $input, $required = FALSE, $notes = "");
/*
// BOSS Market Price
$title = moduleLiteral::get($moduleID, "lbl_bssMarket_price");
$label = $form->getLabel($title);
$input= $form->getInput("text", "bm_price", $market['price'], "", FALSE, FALSE);
$form->insertRow($label, $input, FALSE);

// BOSS Market Tags
$title = moduleLiteral::get($moduleID, "lbl_bssMarket_tags");
$label = $form->getLabel($title);
$input = $form->getTextarea("bm_tags", $market['tags'], "",FALSE);
$form->insertRow($label, $input, $required = FALSE, $notes = "");
*/

// Password Field
$title = moduleLiteral::get($moduleID, "lbl_authenticate");
$label = $form->getLabel($title);
$input= $form->getInput("password", "pw", "", "", TRUE, TRUE);
$form->insertRow($label, $input, TRUE);

// Privileges Form
$privilegesFormContainer = HTML::select(".settingsContainer.privileges .formContainer")->item(0);

// Create Form
$form = new simpleForm();
$privilegesForm = $form->build()->engageModule($moduleID, "updatePrivileges")->get();
DOM::append($privilegesFormContainer , $privilegesForm);

// Project ID
$input= $form->getInput("hidden", "id", $projectID);
$form->append($input);


// Return output
return $page->getReport();
//#section_end#
?>