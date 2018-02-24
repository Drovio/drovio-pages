<?php
//#section#[header]
// Module Declaration
$moduleID = 240;

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
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("DEV", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Modules\module;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Module Title
	$empty = (is_null($_POST['moduleTitle']) || empty($_POST['moduleTitle']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_".$moduleType."Title");
		$err = $errFormNtf->addErrorHeader("motuleTitle_h", $err_header);
		$errFormNtf->addErrorDescription($err, "motuleTitle_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	$module = new module();
	$success = $module->create($_POST['moduleTitle'], $_POST['moduleGroup'], $_POST['moduleDescription']);
	
	// If there is an error in creating the module group, show it
	if (!$success)
	{
		$err_header = DOM::create("span", "Module Creation");
		$err = $errFormNtf->addErrorHeader("module_h", $err_header);
		$errFormNtf->addErrorDescription($err, "module_desc", DOM::create("span", "Error creating the module..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_newModule");
$frame->build($title, $moduleID, "newModule", FALSE);
$sForm = new simpleForm();

// Module Type
$input = $sForm->getInput($type = "hidden", $name = "moduleType", $value = $moduleType, $class = "", $autofocus = FALSE);
$frame->append($input);

// Module Title Description
$title = literal::dictionary("title");
$input = $sForm->getInput($type = "text", $name = "moduleTitle", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Module Description
$title = literal::dictionary("description");
$input = $sForm->getTextarea($name = "moduleDescription", $value = "", $class = "");
$inputRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($inputRow);



// Get Module Groups
$dbc = new dbConnection();
$dbq = new dbQuery("677677266", "security.privileges.developer");

$attr = array();
$attr['aid'] = account::getAccountID();
$moduleGroupsRaw = $dbc->execute($dbq, $attr);

$moduleGroups = $dbc->toArray($moduleGroupsRaw, "id", "description");
$moduleGroups_depths = $dbc->toArray($moduleGroupsRaw, "id", "depth");
foreach ($moduleGroups_depths as $id => $depth)
{
	$tabs = "";
	if ($depth != 0)
		$tabs = str_repeat("   ", $depth)."- ";
	$moduleGroups[$id] = $tabs.$moduleGroups[$id];
}

$title = moduleLiteral::get($moduleID, "lbl_groupParent");
$moduleGroupInput = $sForm->getResourceSelect($name = "moduleGroup", $multiple = FALSE, $class = "", $moduleGroups, $selectedValue = NULL);
$libRow = $sForm->buildRow($title, $moduleGroupInput, $required = TRUE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>