<?php
//#section#[header]
// Module Declaration
$moduleID = 407;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \SYS\Comm\db\dbConnection;

$pageContent = new MContent($moduleID);
$rdItemID = engine::getVar("rid");
if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check title
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
				
		// Header
		$err_header = $errFormNtf->addHeader("Title");
		$errFormNtf->addDescription($err_header, "err.required");
	}
	
	// Check hashtag
	if (empty($_POST['hashtag']))
	{
		$has_error = TRUE;
				
		// Header
		$err_header = $errFormNtf->addHeader("Hashtag");
		$errFormNtf->addDescription($err_header, "err.required");
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	if ($_POST['delete'])
	{
		// Update roadmap item
		$dbc = new dbConnection();
		$q = $pageContent->getQuery("remove_roadmap_item");
		$attr = array();
		$attr['rid'] = $rdItemID;
		$result = $dbc->execute($q, $attr);
	}
	else
	{
		// Update roadmap item
		$dbc = new dbConnection();
		$q = $pageContent->getQuery("update_roadmap_item");
		$attr = array();
		$attr['rid'] = $rdItemID;
		$attr['title'] = $_POST['title'];
		$attr['description'] = $_POST['description'];
		$attr['hashtag'] = $_POST['hashtag'];
		$attr['date_expected'] = $_POST['date_expected'];
		$attr['date_delivered'] = (empty($_POST['date_delivered']) ? "NULL" : $_POST['date_delivered']);
		$result = $dbc->execute($q, $attr);
	}
	
	// If there is an error in updating the item, show it
	if (!$result)
	{
		$err = $errFormNtf->addHeader("Roadmap item error");
		$errFormNtf->addDescription($err, DOM::create("span", "Error updating rodmap..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Refresh the explorer
	$succFormNtf->addReportAction("roadmap.list.reload");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport($reset = FALSE);
}

// Get roadmap info
$dbc = new dbConnection();
$q = $pageContent->getQuery("get_roadmap_info");
$attr = array();
$attr['rid'] = $rdItemID;
$result = $dbc->execute($q, $attr);
$rdInfo = $dbc->fetch($result);

// Build the frame
$frame = new dialogFrame();
$title = $pageContent->getLiteral("hd_editRoadmap");
$frame->build($title, "", FALSE)->engageModule($moduleID, "editRoadmap");
$form = $frame->getFormFactory();

// Project ID
$input = $form->getInput($type = "hidden", $name = "rid", $value = $rdItemID, $class = "", $autofocus = FALSE);
$form->append($input);

$input = $form->getInput($type = "text", $name = "title", $value = $rdInfo['title'], $class = "", $autofocus = TRUE, $required = TRUE);
$form->insertRow("Title", $input, $required = TRUE, $notes = "");

$input = $form->getTextarea($name = "description", $value = $rdInfo['description'], $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow("Description", $input, $required = TRUE, $notes = "");

$input = $form->getInput($type = "text", $name = "hashtag", $value = $rdInfo['hashtag'], $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow("Hashtag", $input, $required = TRUE, $notes = "To refer to this part of the roadmap as #hashtag");

$input = $form->getInput($type = "date", $name = "date_expected", $value = $rdInfo['date_expected'], $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow("Date expected", $input, $required = TRUE, $notes = "");

$input = $form->getInput($type = "date", $name = "date_delivered", $value = $rdInfo['date_delivered'], $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow("Date delivered", $input, $required = TRUE, $notes = "");

$input = $form->getInput($type = "checkbox", $name = "delete", $value = "", $class = "", $autofocus = TRUE, $required = FALSE);
$form->insertRow("Delete?", $input, $required = FALSE, $notes = "");

// Return the report
return $frame->getFrame();
//#section_end#
?>