<?php
//#section#[header]
// Module Declaration
$moduleID = 337;

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
importer::import("SYS", "Resources");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\pages\pageFolder;
use \SYS\Resources\pages\page;
use \API\Model\sql\dbQuery;
use \API\Model\modules\mGroup;
use \API\Model\modules\module;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Developer\codeEditor;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\autoComplete;
use \UI\Interactive\forms\formAutoComplete;
use \UI\Modules\MContent;

$htmlContent = new MContent($moduleID);
$htmlContent->build("", "pageEditor");

// Get Page Info
$pageID = engine::getVar('pid');
$pageData = page::info($pageID);

if (engine::isPost())
{
	$has_error = FALSE;

	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Delete page if checked
	if (isset($_POST['delete']))
	{
		$success = page::remove($pageID);
		
		// If there is an error in deleting the folder, show it
		if (!$success)
		{
			$err_header = DOM::create("span", "Delete Page");
			$err = $errFormNtf->addHeader($err_header);
			$errFormNtf->addDescription($err, DOM::create("span", "Error deleting page..."));
			return $errFormNtf->getReport();
		}
		
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
		
		// Notification Message
		$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
		$succFormNtf->append($errorMessage);
		return $succFormNtf->getReport(FALSE);
	}
	
	// Check filename
	if (empty($_POST['filename']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_pageName");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Title
	if (empty($_POST['folder']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_folder");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Module
	if (empty($_POST['module']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_module");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();

	$sitemap = ($_POST['sitemap'] || $_POST['sitemap'] == "on" ? 1 : 0);
	$success = page::update($pageID, $_POST['filename'], $_POST['folder'], $sitemap, $_POST['pageContent']);
	
	// Set page content
	switch ($_POST['ptype'])
	{
		case "plain":
			break;
		case "module":
			page::engageModule($pageID, $_POST['module_id'], $static = 1, $_POST['module']['attr']);
			break;
	}
	
	// If there is an error in creating the folder, show it
	if (!$success)
	{
		$err_header = DOM::create("span", "Edit Page");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error updating page..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Create form
$form = new simpleForm("pageEditorForm");
$formElement = $form->build("", FALSE)->engageModule($moduleID, $view = "pageEditor")->get();
$htmlContent->append($formElement);

// Add submit button
$title = moduleLiteral::get($moduleID, "lbl_savePage");
$saveButton = $form->getSubmitButton($title, "btn_savePage");
$form->append($saveButton);

// Header
$title = moduleLiteral::get($moduleID, "hd_genericInfo");
$hd = DOM::create("h2", $title, "", "hd");
$form->append($hd);


// Page ID
$input = $form->getInput($type = "hidden", $name = "pid", $pageID, $class = "", $autofocus = FALSE);
$form->append($input);

// Page Filename
$title = moduleLiteral::get($moduleID, "lbl_pageName");
$input = $form->getInput($type = "text", $name = "filename", $value = $pageData['file'], $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Get all folders
$folders = pageFolder::getAllFolders();
foreach ($folders as $folder)
{
	// Normalize folder title
	$folderTitle = ($folder['name'] == "" ? $folder['domain'] : $folder['name']);
	$folderTitle = ($folder['is_root'] ? $folder['domain'] : $folderTitle);
	
	// Get parent title (if any)
	$parentTitle = $folderResource[$folder['parent_id']];
	
	// Add resource
	$folderResource[$folder['id']] = ($parentTitle == "" ? "" : $parentTitle." > ").$folderTitle;
}
asort($folderResource);

// Parent Folder
$title = moduleLiteral::get($moduleID, "lbl_folder");
$input = $form->getResourceSelect($name = "folder", $multiple = FALSE, $class = "", $folderResource, $selectedValue = $pageData['folder_id']);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Include in sitemap
$title = moduleLiteral::get($moduleID, "lbl_sitemap");
$input = $form->getInput($type = "checkbox", $name = "sitemap", $value = "", $class = "", $autofocus = FALSE);
if ($pageData['sitemap'] == 1)
	DOM::attr($input , "checked", "checked");
$form->insertRow($title, $input, $required = FALSE, $notes = "");

// Delete Page
$title = moduleLiteral::get($moduleID, "lbl_deletePage");
$input = $form->getInput($type = "checkbox", $name = "delete", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = FALSE, $notes = "");



// Page Content
$title = moduleLiteral::get($moduleID, "lbl_pageContent");
$hd = DOM::create("h2", $title, "", "hd");
$form->append($hd);

// Content Type
$typeResource = array();
$typeResource['plain'] = "Plain Content";
$typeResource['module'] = "Connect to Module";

$title = moduleLiteral::get($moduleID, "lbl_pageContent_type");
$input = $form->getResourceSelect($name = "ptype", $multiple = FALSE, $class = "", $typeResource, $selectedValue = "plain");
$form->insertRow($title, $input, $required = TRUE, $notes = "");


// Page content containers
$plainWrapper = DOM::create("div", "", "", "pageContentWrapper plain selected");
$form->append($plainWrapper);

$moduleWrapper = DOM::create("div", "", "", "pageContentWrapper module");
$form->append($moduleWrapper);


// -------- Plain Page Content -------- //

// Plain Content
$title = moduleLiteral::get($moduleID, "hd_plainContent");
$hd = DOM::create("h3", $title, "", "hd");
DOM::append($plainWrapper, $hd);

// Get page content
$pageContent = page::getContent($pageID);

// Create code editor for editing
$ce = new codeEditor();
$pageCoder = $ce->build($type = codeEditor::PHP, $pageContent, $name = "pageContent", $editable = TRUE)->get();
DOM::append($plainWrapper, $pageCoder);
HTML::addClass($pageCoder, "pageCode");

// -------- Module Page Content -------- //

// Module Content
$title = moduleLiteral::get($moduleID, "hd_moduleContent");
$hd = DOM::create("h3", $title, "", "hd");
DOM::append($moduleWrapper, $hd);

// Get all module groups
$mGroups = mGroup::getAllGroups();
$mGroupResource = array();
foreach ($mGroups as $mGroup)
{
	$mGroupResource[$mGroup['id']] = $mGroup['description'];
	$moduleGroups_depths[$mGroup['id']] = $mGroup['depth'];
}
foreach ($moduleGroups_depths as $id => $depth)
{
	$tabs = str_repeat(" - ", $depth);
	$mGroupResource[$id] = $tabs.$mGroupResource[$id];
}

$title = moduleLiteral::get($moduleID, "lbl_moduleGroup");
$moduleGroupInput = $form->getResourceSelect($name = "module[group]", $multiple = FALSE, $class = "", $mGroupResource, $selectedValue = $pageData['moduleGroup_id']);
$fRow = $form->buildRow($title, $moduleGroupInput, $required = TRUE, $notes = "");
DOM::append($moduleWrapper, $fRow);

// Module

// Get modules
$modules = module::getAllModules($pageData['moduleGroup_id']);
$modulesResource = array();
foreach ($modules as $mdl)
	$modulesResource[$mdl['id']] = $mdl['title'];

$title = moduleLiteral::get($moduleID, "lbl_module");
$moduleInput = $form->getResourceSelect($name = "module_id", $multiple = FALSE, $class = "", $modulesResource, $selectedValue = $pageData['module_id']);
$fRow = $form->buildRow($title, $moduleInput, $required = TRUE, $notes = "");
DOM::append($moduleWrapper, $fRow);

// Auto Complete
$autoComplete = new formAutoComplete();
$populate = array();
$populate[] = $moduleInput->getAttribute("id");
$autoComplete->engage($moduleGroupInput, "/ajax/modules/groupModules.php", $fill = array(), $hide = array(), $populate, $mode = "lenient");


// Module Page Attributes
$title = moduleLiteral::get($moduleID, "lbl_pageAttributes");
$hd = DOM::create("h3", $title, "", "hd");
DOM::append($moduleWrapper, $hd);


// Dynamic attribute
$title = moduleLiteral::get($moduleID, "lbl_pageAttr_dynamic");
$input = $form->getInput($type = "checkbox", $name = "module[attr][dynamic]", $value = "1", $class = "", $autofocus = FALSE);
if ($pageData['attributes']['dynamic'])
	DOM::attr($input, "checked", TRUE);
$fRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($moduleWrapper, $fRow);

// Meta attribute
$title = moduleLiteral::get($moduleID, "lbl_pageAttr_meta");
$input = $form->getInput($type = "checkbox", $name = "module[attr][meta]", $value = "1", $class = "", $autofocus = FALSE);
if ($pageData['attributes']['meta'])
	DOM::attr($input, "checked", TRUE);
$fRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($moduleWrapper, $fRow);

// Open Graph attribute
$title = moduleLiteral::get($moduleID, "lbl_pageAttr_og");
$input = $form->getInput($type = "checkbox", $name = "module[attr][og]", $value = "1", $class = "", $autofocus = FALSE);
if ($pageData['attributes']['og'])
	DOM::attr($input, "checked", TRUE);
$fRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($moduleWrapper, $fRow);

// Data Only attribute
$title = moduleLiteral::get($moduleID, "lbl_pageAttr_dataOnly");
$notes = moduleLiteral::get($moduleID, "lbl_pageAttr_dataOnly_notes", array(), FALSE);
$input = $form->getInput($type = "checkbox", $name = "module[attr][data]", $value = "1", $class = "", $autofocus = FALSE);
if ($pageData['attributes']['data'])
	DOM::attr($input, "checked", TRUE);
$fRow = $form->buildRow($title, $input, $required = FALSE, $notes);
DOM::append($moduleWrapper, $fRow);

// Connection Toolbar attribute (for guests)
$title = moduleLiteral::get($moduleID, "lbl_pageAttr_no_toolbar");
$input = $form->getInput($type = "checkbox", $name = "module[attr][no_toolbar_guest]", $value = "1", $class = "", $autofocus = FALSE);
if ($pageData['attributes']['no_toolbar_guest'])
	DOM::attr($input, "checked", TRUE);
$fRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($moduleWrapper, $fRow);

// Connection Toolbar attribute (for embedded content)
$title = moduleLiteral::get($moduleID, "lbl_pageAttr_embedded_content");
$notes = moduleLiteral::get($moduleID, "lbl_pageAttr_embedded_content_notes", array(), FALSE);
$input = $form->getInput($type = "checkbox", $name = "module[attr][embedded]", $value = "1", $class = "", $autofocus = FALSE);
if ($pageData['attributes']['embedded'])
	DOM::attr($input, "checked", TRUE);
$fRow = $form->buildRow($title, $input, $required = FALSE, $notes);
DOM::append($moduleWrapper, $fRow);



return $htmlContent->getReport();
//#section_end#
?>