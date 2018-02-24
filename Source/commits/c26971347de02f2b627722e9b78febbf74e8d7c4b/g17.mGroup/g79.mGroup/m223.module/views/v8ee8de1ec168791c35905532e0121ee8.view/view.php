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
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;

use \ESS\Protocol\server\HTMLServerReport;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	// Check templateName
	$empty = (is_null($_POST['srvName']) || empty($_POST['srvName']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_srvName");
		$headerId = 'wsTitle'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'wsTitle'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
	
	// If error, show notification
	if ($has_error)
	{	
		return $formErrorNotification->getReport();
	}
	
	//No parametres error -> Continue
	$srvName = $_POST['srvName'];
	$address = $_POST['srvAddress'];
	$website = new website($_POST['wsId']);
	
	$wsConfig = $website->getWebsiteConfiguration();
	if($_POST['mode'] == 'edit')
	{
		$success = $wsConfig->updateServer($_POST['currName'], $srvName, $address);
	}
	else
	{
		$success = $wsConfig->setServer($srvName, $address);
	}
	
	
	
	// If error, show notification
	if (!$success )
	{
		//On create error	
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not create website";
					 		
		// ERROR NOTIFICATION
		$errorNotification = new formNotification();
		$errorNotification->build("error");
		
		// Description
		$message= "AN ERROR OCCURED : ".$customErrMsg_desc; 
		$errorNotification->appendCustomMessage($message);
				
		return $errorNotification->getReport(FALSE);		
	}
	
	if($_POST['mode'] == 'edit')
	{
		// Return new object
		// SUCCESS NOTIFICATION
		$successNotification = new formNotification();
		$successNotification->build("success");
		
		// Description
		$message= $successNotification->getMessage( "success", "success.save_success");
		$successNotification->appendCustomMessage($message);
		
		return $successNotification->getReport(FALSE); 
	}
	else
	{
		// Return Edit success
		$HTMLContent = new HTMLContent();
		$actionFactory = $HTMLContent->getActionFactory();
		$HTMLContent->build();
		$HTMLContent->addReportAction('website.wizard.addSrv', $srvName);
		
		/* Dublicate part with websiteWizard -> serverConfiguration. Consider changing it */
		$server = DOM::create("div", $srvName, "", "");
		$actionFactory->setModuleAction($server, $moduleID, "serverConfig", $holder = ".mainContent .srvEditorHolder", array('id' => $wsId, 'srvName' => $srvName));
		$HTMLContent->append($server);
		
		
		return $HTMLContent->getReport('.sidebar > .middle > .serverList', HTMLServerReport::APPEND_METHOD);
		
	}
	
	
}

// Create Module
$HTMLContent = new MContent($moduleID);
$actionFactory = $HTMLContent->getActionFactory();

$HTMLContent->build("","serverConfig", TRUE);
$content = HTML::select(".serverConfig .content")->item(0);

$sForm = new simpleForm();
$sForm->build($moduleID, "serverConfig", TRUE);
DOM::append($content, $sForm->get());

// [Hidden] - website id
$input = $sForm->getInput($type = "hidden", $name = "wsId", $value = $_GET['id']);
$sForm->append($input);

// [Hidden] - Mode
$mode = 'edit';
if(is_null($_GET['srvName']) || empty($_GET['srvName']))
{
	$mode = 'new';
}
else
{	
	$website = new website($_GET['id']);
	$wsConfig = $website->getWebsiteConfiguration();
	$data = $wsConfig->getServer($srvName);
	$address = $data['address'];
	
	// [Hidden] - server name
	$input = $sForm->getInput($type = "hidden", $name = "wsId", $value = $_GET['srvName']);
	$sForm->append($input);
}
$input = $sForm->getInput($type = "hidden", $name = "mode", $mode);
$sForm->append($input);


// quides
$stepGuides = DOM::create('p');
$text = moduleLiteral::get($moduleID, "lbl_createGuides");
DOM::append($stepGuides, $text);
$sForm->append($stepGuides);

// server name
$title = moduleLiteral::get($moduleID, "lbl_srvName"); 
$input = $sForm->getInput($type = "text", $name = "srvName", $value = $_GET['srvName'], $class = "", $autofocus = FALSE);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// server address
$title = moduleLiteral::get($moduleID, "lbl_srvAddress"); 
$input = $sForm->getInput($type = "text", $name = "srvAddress", $value = $address, $class = "", $autofocus = FALSE);
$sForm->insertRow($title, $input, $required = FALSE, $notes = "");






$HTMLContent->addReportAction('website.wizard.editSrvEnabled', '');
// Return output
return $HTMLContent->getReport();
//#section_end#
?>