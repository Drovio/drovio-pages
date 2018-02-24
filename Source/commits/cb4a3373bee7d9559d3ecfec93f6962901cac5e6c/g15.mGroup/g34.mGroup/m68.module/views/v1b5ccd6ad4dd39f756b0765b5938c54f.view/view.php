<?php
//#section#[header]
// Module Declaration
$moduleID = 68;

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
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Developer\model\units\domain\Upage;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\literal;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\autoComplete;
use \UI\Interactive\forms\formAutoComplete;
use \UI\html\HTMLContent;

$htmlContent = new HTMLContent();
$htmlContent->build("pageEditorContent");

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;

	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Delete page if checked
	if (isset($_POST['delete']))
	{
		$success = Upage::delete($_POST['pageId']);
		
		// If there is an error in creating the folder, show it
		if (!$success)
		{
			$err_header = DOM::create("span", "Delete Page Page");
			$err = $errFormNtf->addErrorHeader("lblDelete_h", $err_header);
			$errFormNtf->addErrorDescription($err, "lblDelete_desc", DOM::create("span", "Error deleting page..."));
			return $errFormNtf->getReport();
		}
		
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
		
		// Notification Message
		$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
		$succFormNtf->append($errorMessage);
		return $succFormNtf->getReport(FALSE);
	}
	
	// Check Title
	$empty = is_null($_POST['folder']) || empty($_POST['folder']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_folder");
		$err = $errFormNtf->addErrorHeader("lblFolder_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblFolder_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Title
	$empty = is_null($_POST['title']) || empty($_POST['title']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::get("global::dictionary", "name");
		$err = $errFormNtf->addErrorHeader("lblName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblName_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
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

$dbc = new interDbConnection();
$pageID = $_GET['pageID'];

// Get Page Info
$dbq = new dbQuery("739807288", "units.domains.pages");
$attr = array();
$attr['id'] = $pageID;
$result = $dbc->execute($dbq, $attr);
$pageData = $dbc->fetch($result);

// Create form
$sForm = new simpleForm("pageEditor");
$formElement = $sForm->build($moduleID, $action = "editPage", $controls = TRUE)->get();
$htmlContent->append($formElement);

// Header
$hd = moduleLiteral::get($moduleID, "lbl_editPage");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
$sForm->append($hdr);


// Page ID
$input = $sForm->getInput($type = "hidden", $name = "pageId", $pageID, $class = "", $autofocus = FALSE);
$sForm->append($input);

// Get all folders
$dbc = new interDbConnection();
$dbq = new dbQuery("737200095", "units.domains.folders");
$folders = $dbc->execute($dbq);
	
// Create domain tree on the sidebar
$folderResource = array();
while ($folder = $dbc->fetch($folders))
{
	$folderTitle = ($folder['name'] == "" ? $folder['domain'] : $folder['name']);
	$parentTitle = $folderResource[$folder['parent_id']];
	$folderID = $folder['id'];
	$folderResource[$folderID] = ($parentTitle == "" ? "" : $parentTitle." > ").$folderTitle;
}

// Parent Folder
$title = moduleLiteral::get($moduleID, "lbl_folder");
$input = $sForm->getResourceSelect($name = "folder", $multiple = FALSE, $class = "", $folderResource, $selectedValue = $pageData['folder_id']);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$sForm->append($libRow);

// Page Title
$titleContent = literal::get("global::dictionary", "name");
$titleNote = DOM::create("span", " (.php)");
$title = DOM::create("span");
DOM::append($title, $titleContent);
DOM::append($title, $titleNote);
$fileName = str_replace(".php", "", $pageData['file']);
$input = $sForm->getInput($type = "text", $name = "title", $value = $fileName, $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = TRUE, $notes = "");
$sForm->append($libRow);

// Static Page
$title = moduleLiteral::get($moduleID, "lbl_static");
$input = $sForm->getInput($type = "checkbox", $name = "static", $value = "", $class = "", $autofocus = FALSE);
if ($pageData['static'] == 1)
	DOM::attr($input , "checked", "checked");
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$sForm->append($libRow);

// Include in sitemap
$title = moduleLiteral::get($moduleID, "lbl_sitemap");
$input = $sForm->getInput($type = "checkbox", $name = "sitemap", $value = "", $class = "", $autofocus = FALSE);
if ($pageData['sitemap'] == 1)
	DOM::attr($input , "checked", "checked");
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$sForm->append($libRow);


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


// Delete Page
$title = moduleLiteral::get($moduleID, "lbl_deletePage");
$input = $sForm->getInput($type = "checkbox", $name = "delete", $value = "", $class = "", $autofocus = FALSE);
$libRow = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
$sForm->append($libRow);

return $htmlContent->getReport();
//#section_end#
?>