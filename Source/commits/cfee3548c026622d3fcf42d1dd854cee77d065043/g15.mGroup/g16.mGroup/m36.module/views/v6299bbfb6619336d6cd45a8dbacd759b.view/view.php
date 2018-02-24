<?php
//#section#[header]
// Module Declaration
$moduleID = 36;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("UI", "Forms");
importer::import("UI", "Notifications");
importer::import("UI", "Presentation");
importer::import("UI", "Interactive");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Developer\components\moduleObject;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Interactive\forms\formAutoComplete;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Get Module Type
	$moduleType = $_POST['moduleType'];
	
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
	
	if ($moduleType == "module")
	{
		$moduleObject = new moduleObject();
		$success = $moduleObject->createModule($_POST['moduleGroup'], $_POST['moduleTitle'], $_POST['moduleDescription']);
	}
	else
	{
		$moduleObject = new moduleObject($_POST['moduleParent']);
		$success = $moduleObject->createAuxiliary($_POST['moduleTitle'], $_POST['moduleDescription']);
	}
	
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

// Get the module's type
$moduleType = $_GET['type'];

// Build the frame
$frame = new dialogFrame();
$frame->build("Create new Module", $moduleID, "createModule", FALSE);

$sForm = new simpleForm();

// Header
$headerKey = ($moduleType == "aux" ? "info.newAuxModulePrompt" : "info.newModulePrompt");
$hdr = moduleLiteral::get($moduleID, $headerKey);
$frame->append($hdr);

// Module Type
$input = $sForm->getInput($type = "hidden", $name = "moduleType", $value = $moduleType, $class = "", $autofocus = FALSE);
$frame->append($input);

// Module Title Description
$title = moduleLiteral::get($moduleID, "lbl_".$moduleType."Title");
$input = $sForm->getInput($type = "text", $name = "moduleTitle", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Module Description
$title = moduleLiteral::get($moduleID, "lbl_".$moduleType."Description");
$input = $sForm->getTextarea($name = "moduleDescription", $value = "", $class = "");
$inputRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($inputRow);



// Get Module Groups
$dbc = new interDbConnection();
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

$title = moduleLiteral::get($moduleID, "lbl_moduleGroup");
$moduleGroupInput = $sForm->getResourceSelect($name = "moduleGroup", $multiple = FALSE, $class = "", $moduleGroups, $selectedValue = NULL);
$libRow = $sForm->buildRow($title, $moduleGroupInput, $required = TRUE, $notes = "");
$frame->append($libRow);


// Add Module selection if auxiliary
if ($moduleType == "aux")
{
	// Get group modules
	$dbq = new dbQuery("666615842", "units.modules");
	$attr = array();
	$attr['gid'] = array_shift(array_keys($moduleGroups));
	$moduleParents = $dbc->execute($dbq, $attr);
	
	$moduleParents_resource = $dbc->toArray($moduleParents, "id", "title");
	
	$title = moduleLiteral::get($moduleID, "lbl_moduleParent");
	$moduleParentsSelect = $sForm->getResourceSelect($name = "moduleParent", $multiple = FALSE, $class = "", $moduleParents_resource, $selectedValue = NULL);
	$parRow = $sForm->buildRow($title, $moduleParentsSelect, $required = TRUE, $notes = "");
	$frame->append($parRow);
	
	// autocomplete
	$populate = array();
	$populate[] = DOM::attr($moduleParentsSelect, "id");
	$path = "/ajax/modules/testerGroupModules.php";
	
	formAutoComplete::engage($moduleGroupInput, $path, array(), array(), $populate, "lenient");
}

// Return the report
return $frame->getFrame();
//#section_end#
?>