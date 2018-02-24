<?php
//#section#[header]
// Module Declaration
$moduleID = 184;

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

// Use
use \API\Developer\profiler\logger;
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
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Developer\model\units\domain\Upage;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\autoComplete;
use \UI\Interactive\forms\formAutoComplete;
use \UI\Html\HTMLContent;
use \UI\Presentation\notification;
use \UI\Presentation\popups\popup;

$pageID = $_REQUEST['pid'];


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;

	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();

	$static = ($_POST['static'] == "on" ? 1 : 0);
	$sitemap = ($_POST['sitemap'] == "on" ? 1 : 0);
	$success = Upage::update($_POST['pageId'], $_POST['module'], $_POST['title'], $_POST['folder'], $static, $sitemap);
	
	// If there is an error in creating the folder, show it
	if (!$success)
	{
		$err_header = DOM::create("span", "Edit Page");
		$err = $errFormNtf->addErrorHeader("lblEdit_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblEdit_desc", DOM::create("span", "Error updating page..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

$htmlContent = new HTMLContent();
$pageInfoContent = $htmlContent->build("pi_".$pageID, "pageInfo")->get();

$titleContent = moduleLiteral::get($moduleID, "lbl_pageInfo_title");
$title = DOM::create("p", $titleContent);
$htmlContent->append($title);


$dbc = new interDbConnection();

// Get Page Info
$dbq = new dbQuery("739807288", "units.domains.pages");
$attr = array();
$attr['id'] = $pageID;
$result = $dbc->execute($dbq, $attr);
$pageData = $dbc->fetch($result);

// Create form
$sForm = new simpleForm("pageEditor");
$formElement = $sForm->build($moduleID, $action = "pageInfo", $controls = TRUE)->get();
$htmlContent->append($formElement);


// Page ID
$input = $sForm->getInput($type = "hidden", $name = "pageId", $pageID, $class = "", $autofocus = FALSE);
$sForm->append($input);

// Folder id
$input = $sForm->getInput($type = "hidden", $name = "folder", $pageData['folder_id'], $class = "", $autofocus = FALSE);
$sForm->append($input);

// Page title
$fileName = str_replace(".php", "", $pageData['file']);
$input = $sForm->getInput($type = "hidden", $name = "title", $fileName, $class = "", $autofocus = FALSE);
$sForm->append($input);

// Static page
$input = $sForm->getInput($type = "hidden", $name = "folder", $pageData['folder_id'], $class = "", $autofocus = FALSE);
$sForm->append($input);

// Include in sitemap
$input = $sForm->getInput($type = "hidden", $name = "sitemap", $pageData['sitemap'], $class = "", $autofocus = FALSE);
$sForm->append($input);


// Module Group

// Get all groups
$dbc = new interDbConnection();
$dbq = new dbQuery("547558037", "units.groups");
$attr = array();
$moduleGroupsRaw = $dbc->execute_query($dbq);

$moduleGroups = $dbc->to_array($moduleGroupsRaw, "id", "description");
$moduleGroups_depths = $dbc->to_array($moduleGroupsRaw, "id", "depth");
foreach ($moduleGroups_depths as $id => $depth)
{
	$tabs = "";
	if ($depth != 0)
		$tabs = str_repeat("   ", $depth)."- ";
	$moduleGroups[$id] = $tabs.$moduleGroups[$id];
}

$title = moduleLiteral::get($moduleID, "lbl_moduleGroup");
$moduleGroupInput = $sForm->getResourceSelect($name = "moduleGroup", $multiple = FALSE, $class = "", $moduleGroups, $selectedValue = $pageData['moduleGroup_id']);
$libRow = $sForm->buildRow($title, $moduleGroupInput, $required = TRUE, $notes = "");
$sForm->append($libRow);

// Module

// Get group modules
$dbq = new dbQuery("666615842", "units.modules");
$attr = array();
$attr['gid'] = $pageData['moduleGroup_id'];
$modulesRaw = $dbc->execute($dbq, $attr);

$modules = $dbc->toArray($modulesRaw, "id", "title");

$title = moduleLiteral::get($moduleID, "lbl_module");
$moduleInput = $sForm->getResourceSelect($name = "module", $multiple = FALSE, $class = "", $modules, $selectedValue = $pageData['module_id']);
$libRow = $sForm->buildRow($title, $moduleInput, $required = TRUE, $notes = "");
$sForm->append($libRow);

// Auto Complete
$autoComplete = new formAutoComplete();
$populate = array();
$populate[] = $moduleInput->getAttribute("id");
$autoComplete->engage($moduleGroupInput, "/ajax/modules/groupModules.php", $fill = array(), $hide = array(), $populate, $mode = "lenient");















// Build the popup
$popup = new popup();
$popup->position("right|top");
$popup->build($pageInfoContent);

// Return output
return $popup->getReport();
//#section_end#
?>