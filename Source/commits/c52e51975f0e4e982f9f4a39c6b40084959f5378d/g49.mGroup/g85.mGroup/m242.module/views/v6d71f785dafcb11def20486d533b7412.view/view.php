<?php
//#section#[header]
// Module Declaration
$moduleID = 242;

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
importer::import("DEV", "Modules");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Developer\devTabber;
use \UI\Developer\codeEditor;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Navigation\sideBar;
use \UI\Presentation\notification;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Presentation\gridSplitter;
use \DEV\Modules\module;
use \DEV\Modules\modulesProject;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check query title
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("title");
		$err = $errFormNtf->addErrorHeader("qTitle_h", $err_header);
		$errFormNtf->addErrorDescription($err, "qTitle_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
	{
		$notification = $errFormNtf->get();
		return devTabber::getNotificationResult($notification, ($has_error === TRUE));
	}
	
	$module = new module($_POST['mid']);
	$mQuery = $module->getQuery("", $_POST['qid']);
	
	// Get title first
	$title = $mQuery->getTitle();
	
	// Update all information
	$status = $mQuery->update($_POST['title'], $_POST['sqlQuery'], $_POST['description'], $_POST['attributes']);
	
	// If title is different, update query name in module index
	if ($_POST['title'] != $title)
		$module->updateQueryName($_POST['qid'], $_POST['title']);
	
	// Build Notification
	$reportNtf = new notification();
	if ($status === TRUE)
	{
		$reportNtf->build($type = notification::SUCCESS, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("success", "success.save_success");
	}
	else if ($status === FALSE)
	{
		$reportNtf->build($type = notification::ERROR, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("error", "err.save_error");
	}
	else
	{
		$reportNtf->build($type = notification::WARNING, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = DOM::create("span", "There are syntax errors in this document.");
	}
	
	$reportNtf->append($reportMessage);
	$notification = $reportNtf->get();
	
	return devTabber::getNotificationResult($notification, ($status === TRUE));
}

$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get ModuleID and QueryID
$mID = $_GET['mid'];
$qID = str_replace('.', '_', $_GET['qid']);

// Create query Form
$form = new simpleForm("", TRUE);
$queryFormElement = $form->build($moduleID, "moduleQueryEditor", $controls = FALSE)->get();

// Project ID
$input = $form->getInput("hidden", "id", modulesProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

// Module ID
$input = $form->getInput($type = "hidden", $name = "mid", $mID, $class = "", $autofocus = FALSE);
$form->append($input);

// Query ID
$input = $form->getInput($type = "hidden", $name = "qid", $qID, $class = "", $autofocus = FALSE);
$form->append($input);



// Create Global Container
$editorContainer = DOM::create("div", "", "", "queryEditor");
$form->append($editorContainer);

// Create Global Container Toolbar
$tlb = new sideBar();
$navToolbar = $tlb->build(sideBar::LEFT, $editorContainer)->get();
DOM::append($editorContainer, $navToolbar);

// Save Button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertToolbarItem($saveTool);

// Delete query
$deleteTool = DOM::create("span", "", "", "objTool delete");
$tool = $tlb->insertToolbarItem($deleteTool);
$attr = array();
$attr['mid'] = $mID;
$attr['qid'] = $qID;
$actionFactory->setModuleAction($deleteTool, $moduleID, "deleteQuery", "", $attr);

// Test query
$testTool = DOM::create("span", "", "", "objTool test");
$tool = $tlb->insertToolbarItem($testTool);
$attr = array();
$attr['mid'] = $mID;
$attr['qid'] = $qID;
$actionFactory->setModuleAction($testTool, $moduleID, "testQuery", "", $attr);

// Create Splitter
$splitter = new gridSplitter();
$splitterContainer = $splitter->build($orientation = "vertical", $layout = gridSplitter::SIDE_BOTTOM, $closed = FALSE)->get();
DOM::append($editorContainer, $splitterContainer);


$poolClass = "testingPool_".$mID."_".$qID;
$poolClass = str_replace(".", "_", $poolClass);
$testingPool = DOM::create("div", "", "", $poolClass." testingPool");
$splitter->appendToMain($testingPool);

$testingResult = DOM::create("div", "", "", "testResult");
DOM::append($testingPool, $testingResult);


// Create Query Editor Tab Controller
$queryTabber = new tabControl();
$queryTabberControl = $queryTabber->build($id = "q_".$qID)->get();
$splitter->appendToSide($queryTabberControl);

// Insert query Tabs
//_____ Info Tab
$header = literal::get("global::dictionary", "information");
$queryInfoPage = DOM::create("div", "", "", "queryData");
$queryTabber->insertTab("q_".$qID."_info", $header, $queryInfoPage, $selected = FALSE);

//_____ Query Tab
$header = moduleLiteral::get($moduleID, "lbl_querySQL");
$querySQLPage = DOM::create("div", "", "", "queryData");
$queryTabber->insertTab("q_".$qID."_sql", $header, $querySQLPage, $selected = TRUE);

//_____ Attributes Tab
$header = moduleLiteral::get($moduleID, "lbl_queryAttributes");
$queryAttrPage = DOM::create("div", "", "", "queryData");
$queryTabber->insertTab("q_".$qID."_attr", $header, $queryAttrPage, $selected = FALSE);



// Load Query
$module = new module($mID);
$mQ = $module->getQuery("", $qID);


// Info Page

// Title
$title = literal::dictionary("title");
$input = $form->getInput($type = "text", $name = "title", $mQ->getTitle(), $class = "", $autofocus = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($queryInfoPage, $inputRow);

// Description
$title = literal::dictionary("description");
$input = $form->getTextarea($name = "description", $mQ->getDescription(), $class = "");
$row = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($queryInfoPage, $row);

// Query Page
$codeEditor = new codeEditor();
$sqlEditor = $codeEditor->build($type = "sql", $mQ->getPlainQuery(), "sqlQuery")->get();
DOM::append($querySQLPage, $sqlEditor);

// Create attribute list
$gridList = new dataGridList();
$attrGridList = $gridList->build("q_attributes", TRUE)->get();
DOM::append($queryAttrPage, $attrGridList);

// Set headers
$headers = array();
$headers[] = moduleLiteral::get($moduleID, "lbl_attrKey");
$headers[] = moduleLiteral::get($moduleID, "lbl_attrFriendlyName", FALSE);
$gridList->setHeaders($headers);

$attributes = $mQ->getAttributes();
foreach ($attributes as $key => $value)
{
	$gridRow = array();
	
	// Attribute Key
	$attrKeyInput = DOM::create("span", $key);
	$gridRow[] = $attrKeyInput;
	
	// Attribute Friendly Name
	$attrDescInput = $form->getInput($type = "text", $name = "attributes[$key]", $value, $class = "", $autofocus = FALSE);
	$gridRow[] = $attrDescInput;
	
	$gridList->insertRow($gridRow);
}



// Send devTabber Tab
$devTabber = new devTabber();
$header = $mQ->getTitle();
return $devTabber->getReportContent($qID, $header, $queryFormElement);
//#section_end#
?>