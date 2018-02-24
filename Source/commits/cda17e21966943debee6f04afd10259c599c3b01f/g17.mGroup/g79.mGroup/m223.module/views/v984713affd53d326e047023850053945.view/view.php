<?php
//#section#[header]
// Module Declaration
$moduleID = 223;

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("DEV", "Websites");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \DEV\Websites\website;

$websiteID = $_REQUEST['id'];
$website = new website($websiteID);

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$header ='';	
	
	// Check templateName
	$empty = (is_null($_POST['title']) || empty($_POST['title']));
	if($empty)
		$header .= moduleLiteral::get($moduleID, "lbl_websiteTitle");	
	
	// If a required field is empty
	// Return error
	if(!empty($header))
	{
		$formErrorNotification = new formErrorNotification();
		$formErrorNotification->build();
	
		$errorHeader = $formErrorNotification->addErrorHeader('requiredErrorHd', $header);
		$formErrorNotification->addErrorDescription($errorHeader, 'requiredErrorDesc', 'err.required', $extra = "");	
		return $formErrorNotification->getReport();
	}
	
	//$status =  $project->updateInfo($_POST['title'], $_POST['description']);
	
	// If error, show notification
	$formNtf = new formNotification();
	if (!$status)
	{
		//On create error	
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not change info";
					 		
		// Build notification
		$formNtf->build("error");
		
		// Description
		$message= "AN ERROR OCCURED : ".$customErrMsg_desc; 
		$formNtf->appendCustomMessage($message);
				
		return $formNtf->getReport(FALSE);		
	}
	
	// Build notification
	$formNtf->build("success");
	
	// Description
	$message= $formNtf->getMessage( "success", "success.save_success");
	$formNtf->appendCustomMessage($message);
	
	return $formNtf->getReport(FALSE);
}

// Get website information
$websiteInfo = $website->info();

$mcontent = new MContent($moduleID);
$mcontent->build("", "wsMetaInfoEditor", TRUE);

$content = HTML::select(".wsMetaInfo .content")->item(0);

$form = new simpleForm();
$editorForm = $form->build()->engageModule($moduleID, "metaInformation")->get();
DOM::append($content, $editorForm);

// Website id
$input = $form->getInput("hidden", "id", $_GET['id']);
$form->append($input);

// 
$title = moduleLiteral::get($moduleID, "lbl_meta_description");
$input = $form->getTextArea("description", "", "", FALSE);
$form->insertRow($title, $input, TRUE);

//
$title = moduleLiteral::get($moduleID, "lbl_meta_keywords");
$input = $form->getTextArea("keywords", "", "", FALSE);
$form->insertRow($title, $input, TRUE);


// Return output
return $mcontent->getReport();
//#section_end#
?>