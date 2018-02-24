<?php
//#section#[header]
// Module Declaration
$moduleID = 64;

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
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Developer\components\units\modules\moduleGroup;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Group Description
	$empty = (is_null($_POST['groupDescription']) || empty($_POST['groupDescription']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_groupDescription");
		$err = $errFormNtf->addErrorHeader("groupDesc_h", $err_header);
		$errFormNtf->addErrorDescription($err, "groupDesc_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Proceed to create module group
	$description = $_POST['groupDescription'];
	$parent_id = $_POST['groupParent'];
	$rootParent = $_POST['rootParent'];
	if (isset($rootParent))
		$parent_id = NULL;
	
	$success = moduleGroup::create($description, $parent_id);
	
	// If there is an error in creating the module group, show it
	if (!$success)
	{
		$err_header = DOM::create("span", "Module Group");
		$err = $errFormNtf->addErrorHeader("moduleGroup_h", $err_header);
		$errFormNtf->addErrorDescription($err, "moduleGroup_desc", DOM::create("span", "Error creating the group..."));
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
$frame->build("Create new Module Group", $moduleID, "newModuleGroup", FALSE);

$sForm = new simpleForm();

// Header
$hdr = moduleLiteral::get($moduleID, "lbl_newModuleGroup");
$frame->append($hdr);

// Module Group Description
$title = literal::dictionary("title");
$input = $sForm->getInput($type = "text", $name = "groupDescription", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Root Group (create under root if checked)
$title = moduleLiteral::get($moduleID, "lbl_rootParent");
$input = $sForm->getInput($type = "checkbox", $name = "rootParent", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($inputRow);

// Get Module Groups
$dbc = new interDbConnection();
$dbq = new dbQuery("785059449", "security.privileges.developer");

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
$moduleGroupInput = $sForm->getResourceSelect($name = "groupParent", $multiple = FALSE, $class = "", $moduleGroups, $selectedValue = NULL);
$libRow = $sForm->buildRow($title, $moduleGroupInput, $required = TRUE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>