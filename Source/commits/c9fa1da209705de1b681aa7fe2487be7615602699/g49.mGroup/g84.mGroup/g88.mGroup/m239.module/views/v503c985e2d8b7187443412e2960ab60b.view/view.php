<?php
//#section#[header]
// Module Declaration
$moduleID = 239;

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
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("INU", "Developer");
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Navigation\navigationBar;
use \UI\Presentation\notification;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Presentation\gridSplitter;
use \INU\Developer\redWIDE;
use \INU\Developer\codeEditor;
use \DEV\Core\sql\sqlQuery;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	$empty = is_null($_POST['title']) || empty($_POST['title']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_queryTitle");
		$err = $errFormNtf->addErrorHeader("qTitle_h", $err_header);
		$errFormNtf->addErrorDescription($err, "qTitle_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
	{
		$notification = $errFormNtf->get();
		return redWIDE::getNotificationResult($notification, ($has_error === TRUE));
	}
	
	$dbq = new sqlQuery($_POST['domain'], $_POST['qid']);
	$result = $dbq->update($_POST['title'], $_POST['sqlQuery'], $_POST['description'], $_POST['transaction'], $_POST['accessLevel'], $_POST['attributes']);
	
	
	$succFormNtf = new formNotification();
	if ($result)
	{
		$succFormNtf->build($type = "success", $header = FALSE, $footer = FALSE);
		$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
		$succFormNtf->append($errorMessage);
	}
	else
	{
		$succFormNtf->build($type = "error", $header = TRUE, $footer = FALSE);
		$errorMessage = $succFormNtf->getMessage("error", "err.save_error");
		
	}
	
	$succFormNtf->append($errorMessage);
	$notification = $succFormNtf->get();
	
	return redWIDE::getNotificationResult($notification, ($result === TRUE));
}

$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get QueryID
$qID = str_replace('.', '_', $_GET['qid']);
$qDomain = $_GET['domain'];

// Create query Form
$queryForm = new simpleForm("", TRUE);
$queryFormElement = $queryForm->build($moduleID, "", $controls = FALSE)->get();

// Hidden Values
$input = $queryForm->getInput($type = "hidden", $name = "qid", $qID, $class = "", $autofocus = FALSE);
$queryForm->append($input);

$input = $queryForm->getInput($type = "hidden", $name = "domain", $qDomain, $class = "", $autofocus = FALSE);
$queryForm->append($input);


// Toolbar Control
$tlb = new navigationBar();

// Create Global Container
$editorContainer = DOM::create("div", "", "", "queryEditor");
$queryForm->append($editorContainer);

// Create Global Container Toolbar
$navToolbar = $tlb->build($dock = "L", $editorContainer)->get();
DOM::append($editorContainer, $navToolbar);

// Save Button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertToolbarItem($saveTool);

// Delete query
$deleteTool = DOM::create("span", "", "", "objTool delete");
$tool = $tlb->insertToolbarItem($deleteTool);
$attr = array();
$attr['qid'] = $qID;
$attr['domain'] = $qDomain;
$actionFactory->setModuleAction($deleteTool, $moduleID, "deleteQuery", "", $attr);

// Test query
$testTool = DOM::create("span", "", "", "objTool test");
$tool = $tlb->insertToolbarItem($testTool);
$attr = array();
$attr['qid'] = $qID;
$attr['domain'] = $qDomain;
$actionFactory->setModuleAction($testTool, $moduleID, "testQuery", "", $attr);

// Create Splitter
$splitter = new gridSplitter();
$splitterContainer = $splitter->build($orientation = "vertical", $layout = gridSplitter::SIDE_BOTTOM, $closed = FALSE)->get();
DOM::append($editorContainer, $splitterContainer);


// Create Testing Result viewer
$poolClass = "testingPool_".$qDomain."_".$qID;
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
$header = moduleLiteral::get($moduleID, "lbl_tabInfo");
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
$dbq = new sqlQuery($qDomain, $qID);


// Info Page

$qSeed = str_replace("q_", "", $qID);
// ID
$title = moduleLiteral::get($moduleID, "lbl_queryID");
$label = $queryForm->getLabel($qSeed, $for = "", $class = "");
$inputRow = $queryForm->buildRow($title, $label, $required = FALSE, $notes = "");
DOM::append($queryInfoPage, $inputRow);

// Hash ID
$title = moduleLiteral::get($moduleID, "lbl_queryHashID");
$label = $queryForm->getLabel(sqlQuery::getName($qSeed), $for = "", $class = "");
$inputRow = $queryForm->buildRow($title, $label, $required = FALSE, $notes = "");
DOM::append($queryInfoPage, $inputRow);

// Title
$title = literal::dictionary("title");
$input = $queryForm->getInput($type = "text", $name = "title", $dbq->getTitle(), $class = "", $autofocus = TRUE);
$inputRow = $queryForm->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($queryInfoPage, $inputRow);

// Description
$title = literal::dictionary("description");
$input = $queryForm->getTextarea($name = "description", $dbq->getDescription(), $class = "");
$row = $queryForm->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($queryInfoPage, $row);

// Query Page
$codeEditor = new codeEditor();
$sqlEditor = $codeEditor->build($type = "sql", $dbq->getPlainQuery(), "sqlQuery")->get();
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

$attributes = $dbq->getAttributes();
foreach ($attributes as $key => $value)
{
	$gridRow = array();
	
	// Attribute Key
	$attrKeyInput = DOM::create("span", $key);
	$gridRow[] = $attrKeyInput;
	
	// Attribute Friendly Name
	$attrDescInput = $queryForm->getInput($type = "text", $name = "attributes[$key]", $value, $class = "", $autofocus = FALSE);
	$gridRow[] = $attrDescInput;
	
	$gridList->insertRow($gridRow);
}



// Send redWIDE Tab
$wide = new redWIDE();
$header = $dbq->getTitle()." [".$dbq->getID()."]";
return $wide->getReportContent($qID, $header, $queryFormElement);
//#section_end#
?>