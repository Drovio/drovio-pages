<?php
//#section#[header]
// Module Declaration
$moduleID = 253;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

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
importer::import("API", "Geoloc");
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\server\HTMLServerReport;
use \API\Literals\moduleLiteral;
use \API\Geoloc\locale;
use \API\Resources\forms\inputValidator;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\dataGridList;
use \UI\Presentation\frames\dialogFrame;
use \UI\Modules\MContent;


// Get data
$projectID = $_REQUEST['pid'];
$scope = $_REQUEST['scope'];


$sForm = new simpleForm();
$iwrapper = DOM::create("div", "", "", "literalManagerWrapper");

//__________ SERVER CONTROL __________//
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Module ID
	$module_id = $_POST['moduleId'];
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// If no module specified there's been an error on page... Return Unknown Error
	if (inputValidator::checkNotset($module_id))
	{
		// No module specified.
		// Header
		//$err_header = moduleLiteral::get($moduleID, "lbl_libraryName");
		$err_header = DOM::create("span", "Literal Manager Error");
		$err = $errFormNtf->addErrorHeader("nomo_h", $err_header);
		$err_message = DOM::create("span", "There was an error while trying to save literals. Please reload the Literal Manager and try again.");
		$errFormNtf->addErrorDescription($err, "nomo_desc", $err_message);//$errFormNtf->getErrorMessage("err.required"));
		return $errFormNtf->getReport();
	}
	
	// Initial literal values
	$initLiterals = moduleLiteral::get($module_id, "", FALSE, locale::getDefault());
	
	// Delete literals
	$unset = $_POST['mlgDelete'];
	
	// Create New Literals
	$newLiterals = array();
	foreach ((array)$_POST['mlgCreate'] as $pair)
	{
		if (inputValidator::checkNotset($pair['id']) || inputValidator::checkNotset($pair['value']) /* ||
				inputValidator::checkNotset($pair['description']) */)
			continue;
		
		$newLiterals[$pair['id']] = $pair['value'];
	}
	
	// For each "to delete" literal, unset it from init and new literals
	// The literal is marked for deletion anyways.
	foreach ((array)$unset as $id => $state)
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
			moduleLiteral::add($module_id, $id, $value);
		else 
		{
			if ($initLiterals[$id] != $value /*|| Not same description*/ )
				moduleLiteral::update($module_id, $id, $value);
			unset($initLiterals[$id]);
		}
	}
	
	// For any literal left in initLiterals, 
	// that literal needs to be deleted, along with the "to delete" literals
	$delete = array_merge((array)$unset, (array)$initLiterals);
	foreach ($delete as $id => $mixed)
	{
		moduleLiteral::remove($module_id, $id);
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$succMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($succMessage);
	$successNtfReport = $succFormNtf->get();
}
else
{
	//__________ [Page GET Variables] __________//
	$module_id = $_GET['moduleId'];
	
	//$frameMessage = moduleLiteral::get($moduleID, "info.mlgManPrompt", FALSE);
	$frameMessage = "Module Literal Manager";
	$frame->build($frameMessage, $moduleID, "", FALSE);
	$wrapper = DOM::create("div", "", "ltrlMngrWrapper");
	$frame->append($wrapper);
	DOM::append($wrapper, $iwrapper);
}


$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build();



//create form
$create_module_form = $sForm->build($moduleID, $action = "", $controls = TRUE)->get();

// Header
$hd = moduleLiteral::get($moduleID, "lbl_mlgManInfo");
$hdr = DOM::create("h2");
DOM::append($hdr, $hd);
DOM::append($iwrapper, $hdr);

$input = $sForm->getInput($type = "hidden", $name = "moduleId", $value = $module_id, $class = "", $autofocus = FALSE);
DOM::append($iwrapper, $input);

/*
// Reset literal file
$title = moduleLiteral::get($moduleID, "lbl_reset");
$chkbox = $sForm->getInput($type = "checkbox", $name = "mlgReset", $value = "", $class = "", $autofocus = FALSE);
$resetRow = $sForm->buildRow($title, $chkbox, $required = TRUE, $notes = "");
$frame->append($resetRow);
*/

///_____ Initialize Primary Group
$innerWrap = DOM::create("div", "", "", "mlgManFormContent");
DOM::append($iwrapper, $innerWrap);
$form_group = DOM::create("div", "", "", "mlgManInfoGroup");
DOM::append($innerWrap, $form_group);

// Get module literals
$resources = moduleLiteral::get($module_id, "", FALSE, locale::getDefault());

$lbl = $sForm->getLabel(": ".locale::getDefault());
$title = moduleLiteral::get($moduleID, "lbl_locale");
$localeRow = $sForm->insertRow($title, $lbl, $required = FALSE, $notes = "");

// literal list
$dtGridList = new dataGridList();
$glist = $dtGridList->build("", TRUE)->get();

$headers = array();
//$headers[] = "Delete";
$headers[] = "Name";
$headers[] = "Value";
//$headers[] = "Description";

$dtGridList->setHeaders($headers);

$only_new = TRUE;

foreach ($resources/*['literals']*/ as $id => $content)
{
	$only_new = FALSE;
	$gridRow = array();
		
	// Name
	$input = $sForm->getInput($type = "text", $name = "mlgCreate[".$id."][id]", $value = $id, $class = "", $autofocus = FALSE);
	$gridRow[] = $input;
	
	// Value
	$txtArea = $sForm->getTextarea($name = "mlgCreate[".$id."][value]", $value = "", $class = "");
	DOM::nodeValue($txtArea, $content);
	$gridRow[] = $txtArea;
	
	/*// Description
	$txtArea2 = $sForm->getTextarea($name = "mlgCreate[".$id."][description]", $value = "", $class = "");
	DOM::nodeValue($txtArea2, $description);
	$gridRow[] = $txtArea2;*/
	
	$dtGridList->insertRow($gridRow, "mlgDelete[".$id."]");
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
	$input = $sForm->getInput($type = "text", $name = "mlgCreate[".$i."][id]", $value = "", $class = "", $autofocus = FALSE);
	$gridRow[] = $input;
	
	// Value
	$txtArea = $sForm->getTextarea($name = "mlgCreate[".$i."][value]", $value = "", $class = "");
	$gridRow[] = $txtArea;
	
	/* // Description
	$txtArea2 = $sForm->getTextarea($name = "mlgCreate[".$i."][description]", $value = "", $class = "");
	$gridRow[] = $txtArea2;*/
	
	$dtGridList->insertRow($gridRow);
}

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	HTMLServerReport::clear();
	HTMLServerReport::addContent($iwrapper, "data", "#ltrlMngrWrapper", "replace");
	HTMLServerReport::addContent($successNtfReport, "data", ".formReport", "replace");
	return HTMLServerReport::get();
}


return $frame->append($wrapper)->getFrame();
//#section_end#
?>