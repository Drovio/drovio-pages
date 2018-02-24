<?php
//#section#[header]
// Module Declaration
$moduleID = 223;

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
importer::import("UI", "Modules");
importer::import("DEV", "Websites");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \DEV\Websites\wsSettings;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;

$wsSettings = new wsSettings($_REQUEST['id']); 

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$status = $wsSettings->set('url', $_POST['siteUrl']);
	
	// If error, show notification
	$formNtf = new formNotification();
	if (!$status)
	{
		//On create error	
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not change Website Url";
					 		
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

$mcontent = new MContent($moduleID);
$mcontent->build("","webDomainEditor", TRUE);

$content = HTML::select(".webDomainEditor .content")->item(0);

$form = new simpleForm();
$editorForm = $form->build()->engageModule($moduleID, "domainSettings")->get();
DOM::append($content, $editorForm);

$input = $form->getInput("hidden", "id", $_GET['id'], "", TRUE, TRUE);
$form->append($input);

// siteUrl
$title = moduleLiteral::get($moduleID, "lbl_siteUrl"); 
$input = $form->getInput($type = "text", $name = "siteUrl", $wsSettings->get('url'), $class = "", $autofocus = FALSE);
$notes = moduleLiteral::get($moduleID, "notes_siteUrl"); 
$form->insertRow($title, $input, $required = FALSE, $notes);


// Return output
return $mcontent->getReport();
//#section_end#
?>