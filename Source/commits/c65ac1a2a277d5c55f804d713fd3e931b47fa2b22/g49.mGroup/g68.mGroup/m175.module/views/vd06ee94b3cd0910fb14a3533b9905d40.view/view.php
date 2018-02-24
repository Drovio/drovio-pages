<?php
//#section#[header]
// Module Declaration
$moduleID = 175;

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
importer::import("API", "Geoloc");
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \API\Geoloc\locale;
use \API\Resources\forms\inputValidator;
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\dataGridList;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Apps\components\appLiteral;


$sForm = new simpleForm();
$frame = new dialogFrame();
$iwrapper = DOM::create("div", "", "", "literalManagerWrapper");


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Module ID
	$appID = $_POST['appID'];
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// If no module specified there's been an error on page... Return Unknown Error
	if (inputValidator::checkNotset($module_id))
	{
		// No module specified.
		// Header
		$err_header = DOM::create("span", "Literal Manager Error");
		$err = $errFormNtf->addErrorHeader("nomo_h", $err_header);
		$err_message = DOM::create("span", "There was an error while trying to save literals. Please reload the Literal Manager and try again.");
		$errFormNtf->addErrorDescription($err, "nomo_desc", $err_message);//$errFormNtf->getErrorMessage("err.required"));
		return $errFormNtf->getReport();
	}
	
	// Initial literal values
	$initLiterals = appLiteral::get($appID, "", FALSE, locale::getDefault());
	
	// Delete literals
	$unset = $_POST['mlgDelete'];
	
	// Create New Literals
	$newLiterals = array();
	foreach ($_POST['mlgCreate'] as $pair)
	{
		if (inputValidator::checkNotset($pair['id']) || inputValidator::checkNotset($pair['value']))
			continue;
		
		$newLiterals[$pair['id']] = $pair['value'];
	}
	
	// For each "to //delete" literal, unset it from init and new literals
	// The literal is marked for deletion anyways.
	foreach ($unset as $id => $state)
	{
		unset($initLiterals[$id]);
		unset($newLiterals[$id]);
	}
	
	// For each new literal, decide if it needs to be added or updated
	foreach ($newLiterals as $id => $value)
	{
		// If literal doesn't exist in initial, then it needs to be added as a new literal.
		// If literal exists in initial and has different value or description, update.
		if (inputValidator::checkNotset($initLiterals[$id]))
			appLiteral::add($appID, $id, $value);
		else 
		{
			// Not same description
			if ($initLiterals[$id] != $value)
				appLiteral::update($appID, $id, $value);
			unset($initLiterals[$id]);
		}
	}
	
	
	// For any literal left in initLiterals, 
	// that literal needs to be deleted, along with the "to ////delete" literals
	$toDelete = array_merge((array)$unset, (array)$initLiterals);
	foreach ($toDelete as $id => $mixed)
		appLiteral::remove($appID, $id);
		
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$succMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($succMessage);
	$successNtfReport = $succFormNtf->get();
}
else
{
	$appID = $_GET['moduleId'];
	
	moduleLiteral::get($moduleID, "lbl_frameTitle");
	$frame->build($frameMessage, $moduleID, "", FALSE);
	$wrapper = DOM::create("div", "", "ltrlMngrWrapper");
	$frame->append($wrapper);
	DOM::append($wrapper, $iwrapper);
}

// Header
$title = moduleLiteral::get($moduleID, "lbl_mlgManInfo");
$hd = DOM::create("h2", $title);
DOM::append($iwrapper, $hd);

$input = $sForm->getInput($type = "hidden", $name = "appID", $value = $appID, $class = "", $autofocus = FALSE);
DOM::append($iwrapper, $input);

///_____ Initialize Primary Group
$innerWrap = DOM::create("div", "", "", "mlgManFormContent");
DOM::append($iwrapper, $innerWrap);
$form_group = DOM::create("div", "", "", "mlgManInfoGroup");
DOM::append($innerWrap, $form_group);

// Get application literals
$resources = appLiteral::get($appID, "", array(), FALSE, locale::getDefault());

// literal list
$dtGridList = new dataGridList();
$glist = $dtGridList->build("", TRUE)->get();

$headers = array();
$headers[] = "Name";
$headers[] = "Value";
$dtGridList->setHeaders($headers);

$only_new = TRUE;
foreach ($resources as $id => $content)
{
	$only_new = FALSE;
	$gridRow = array();
		
	// Name
	$input = $sForm->getInput($type = "text", $name = "litCreate[".$id."][id]", $value = $id, $class = "", $autofocus = FALSE);
	$gridRow[] = $input;
	
	// Value
	$txtArea = $sForm->getTextarea($name = "litCreate[".$id."][value]", $value = "", $class = "");
	DOM::nodeValue($txtArea, $content);
	$gridRow[] = $txtArea;
	
	$dtGridList->insertRow($gridRow, "litDelete[".$id."]");
}

if ($only_new)
{
	$dtGridList = new dataGridList();
	$glist = $dtGridList->build()->get();

	$headers = array();
	$headers[] = "Name";
	$headers[] = "Value";
	//$headers[] = "Description";

	$dtGridList->setHeaders($headers);
}
DOM::append($form_group, $glist);

for ($i = 0; $i < 5; $i++)
{
	$gridRow = array();
	
	// Name
	$input = $sForm->getInput($type = "text", $name = "litCreate[".$i."][id]", $value = "", $class = "", $autofocus = FALSE);
	$gridRow[] = $input;
	
	// Value
	$txtArea = $sForm->getTextarea($name = "litCreate[".$i."][value]", $value = "", $class = "");
	$gridRow[] = $txtArea;
	
	$dtGridList->insertRow($gridRow);
}
/*
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	HTMLServerReport::clear();
	HTMLServerReport::addContent($iwrapper, "data", "#ltrlMngrWrapper", "replace");
	HTMLServerReport::addContent($successNtfReport, "data", ".formReport", "replace");
	return HTMLServerReport::get();
}
*/
return $frame->append($wrapper)->getFrame();
//#section_end#
?>