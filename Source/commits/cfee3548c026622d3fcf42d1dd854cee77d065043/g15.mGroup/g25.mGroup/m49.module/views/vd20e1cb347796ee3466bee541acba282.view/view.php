<?php
//#section#[header]
// Module Declaration
$moduleID = 49;

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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("ESS", "Protocol");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Developer\components\sql\dvbQuery;
use \API\Resources\literals\literal;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Navigation\toolbar;
use \UI\Presentation\notification;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Presentation\gridSplitter;
use \ESS\Protocol\server\ModuleProtocol;
use \INU\Developer\redWIDE;
use \INU\Developer\codeEditor;

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
	
	$dbq = new dvbQuery($_POST['domain'], $_POST['qid']);
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

// Get QueryID
$qID = str_replace('.', '_', $_GET['qid']);
$qDomain = $_GET['domain'];

// Create query Form
$queryForm = new simpleForm();
$queryFormElement = $queryForm->build($moduleID, "queryEditor", $controls = FALSE)->get();

// Hidden Values
$input = $queryForm->getInput($type = "hidden", $name = "qid", $qID, $class = "", $autofocus = FALSE);
$queryForm->append($input);

$input = $queryForm->getInput($type = "hidden", $name = "domain", $qDomain, $class = "", $autofocus = FALSE);
$queryForm->append($input);


// Toolbar Control
$tlb = new toolbar();

// Create Global Container
$editorContainer = DOM::create("div", "", "", "queryEditor");
toolbar::setParent($editorContainer, $dock = "L");
$queryForm->append($editorContainer);

// Create Global Container Toolbar
$navToolbar = $tlb->build($dock = "L");
DOM::append($editorContainer, $navToolbar);

// Save Button
$saveQuery = DOM::create("button", "", "", "sideTool save");
$tlb->insertToolbarItem($saveQuery);

// Create Splitter
$splitter = new gridSplitter();
$splitterContainer = $splitter->build($orientation = "vertical", $layout = gridSplitter::SIDE_BOTTOM, $closed = FALSE)->get();
DOM::append($editorContainer, $splitterContainer);


// Create Testing Result viewer
$controlContainer = DOM::create("div", "", "", "testControls");
$splitter->appendToMain($controlContainer);

$testButton = $queryForm->getButton("Test");
$attr = array();
$attr['qid'] = $qID;
$attr['domain'] = $qDomain;
ModuleProtocol::addActionGET($testButton, $moduleID, "testQuery", "", $attr);
DOM::append($controlContainer, $testButton);

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
echo "loading $qID query\n";
$dbq = new dvbQuery($qDomain, $qID);


// Info Page

// Title
$title = moduleLiteral::get($moduleID, "lbl_queryTitle"); 
$input = $queryForm->getInput($type = "text", $name = "title", $dbq->getTitle(), $class = "", $autofocus = TRUE);
$inputRow = $queryForm->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($queryInfoPage, $inputRow);

// Description
$title = moduleLiteral::get($moduleID, "lbl_queryDescription"); 
$input = $queryForm->getTextarea($name = "description", $dbq->getDescription(), $class = "");
$row = $queryForm->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($queryInfoPage, $row);

// Query Access Level
$title = moduleLiteral::get($moduleID, "lbl_queryAccessLevel");
$resource = array();
for ($i=1; $i<6; $i++)
	$resource[$i] = "".$i;
$input = $queryForm->getResourceSelect($name = "accessLevel", $multiple = FALSE, $class = "", $resource, $dbq->getAccessLevel());
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
$gridList->set_headers($headers);

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
	
	$gridList->insert_row($gridRow);
}



// Send redWIDE Tab
$wide = new redWIDE();
$header = $dbq->getTitle()." [".$dbq->getID()."]";
return $wide->getReportContent($qID, $header, $queryFormElement);
//#section_end#
?>