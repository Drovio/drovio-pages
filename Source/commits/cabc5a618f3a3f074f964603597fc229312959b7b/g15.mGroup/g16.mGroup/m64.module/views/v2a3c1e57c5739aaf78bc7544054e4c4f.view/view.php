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
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Interactive");
importer::import("DEV", "Modules");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Interactive\forms\formAutoComplete;
use \DEV\Modules\module;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Get Module Type
	$moduleType = $_POST['moduleType'];
	
	// Check Module Title
	$empty = (is_null($_POST['viewTitle']) || empty($_POST['viewTitle']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "viewTitle");
		$err = $errFormNtf->addErrorHeader("motuleTitle_h", $err_header);
		$errFormNtf->addErrorDescription($err, "motuleTitle_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create new view
	$module = new module($_POST['parent_id']);
	$success = $module->createView($_POST['viewTitle']);
	
	// If there is an error in creating the module group, show it
	if (!$success)
	{
		$err_header = DOM::create("span", "Module View Creation");
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
$frame->build("Create new View", $moduleID, "newModuleView", FALSE);

$sForm = new simpleForm();

// Header
$hdr = moduleLiteral::get($moduleID, "info.newModuleViewPrompt");


// View Title
$title = literal::dictionary("title");
$input = $sForm->getInput($type = "text", $name = "viewTitle", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
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


// Get group modules
$dbq = new dbQuery("666615842", "units.modules");
$attr = array();
$attr['gid'] = array_shift(array_keys($moduleGroups));
$moduleParents = $dbc->execute($dbq, $attr);
$moduleParents_resource = $dbc->toArray($moduleParents, "id", "title");

$title = moduleLiteral::get($moduleID, "lbl_moduleParent");
$moduleParentsSelect = $sForm->getResourceSelect($name = "parent_id", $multiple = FALSE, $class = "", $moduleParents_resource, $selectedValue = NULL);
$parRow = $sForm->buildRow($title, $moduleParentsSelect, $required = TRUE, $notes = "");
$frame->append($parRow);

// autocomplete
$populate = array();
$populate[] = DOM::attr($moduleParentsSelect, "id");
$path = "/ajax/modules/testerGroupModules.php";

formAutoComplete::engage($moduleGroupInput, $path, array(), array(), $populate, "lenient");

// Return the report
return $frame->getFrame();
//#section_end#
?>