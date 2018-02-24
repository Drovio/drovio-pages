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
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Modules\MContent;

// Get application id
$applicationID = engine::getVar("id");

if (engine::isPost())
{
	// Update boss market settings
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "update_bossmarket");
	$attr = array();
	$attr['id'] = $applicationID;
	$attr['active'] = ($_POST['bm_active'] == "on" || $_POST['bm_active'] ? 1 : 0);
	$attr['price'] = (!empty($_POST['bm_price']) && is_numeric($_POST['bm_price']) ? $_POST['bm_price'] : 0);
	$attr['tags'] = $_POST['bm_tags'];
	$result = $dbc->execute($q, $attr);
	
	// Get notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "bossSettingsContainer");


//Create Form
$form = new simpleForm();
$settingsForm = $form->build()->engageModule($moduleID, "bossSettings")->get();
$pageContent->append($settingsForm);


// Project ID
$input= $form->getInput($type = "hidden", $name = "id", $value = $applicationID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);


// Get boss market application info
$market = array();
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "bossmarket_info");
$attr = array();
$attr['id'] = $applicationID;
$result = $dbc->execute($q, $attr);
if ($result)
	$market = $dbc->fetch($result);
	
$title = moduleLiteral::get($moduleID, "lbl_bssMarket");
$label = $form->getLabel($title);
$input= $form->getInput("checkbox", "bm_active", $value = ($market['active'] == "1"), $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($label, $input, $required = FALSE, $notes = "");

// Application Center Tags
$title = moduleLiteral::get($moduleID, "lbl_bssMarket_tags");
$notes = moduleLiteral::get($moduleID, "lbl_tags_notes", array(), FALSE);
$label = $form->getLabel($title);
$input = $form->getTextarea("bm_tags", $market['tags'], "",FALSE);
$form->insertRow($label, $input, $required = FALSE, $notes);

// Return output
return $pageContent->getReport();
//#section_end#
?>