<?php
//#section#[header]
// Module Declaration
$moduleID = 164;

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
importer::import("API", "Comm");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Forms");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Comm\mail\rbMailer;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Html\HTMLContent;
use \INU\Forms\HTMLEditor;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Clear report
	report::clear();
	
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	// Check Recipient Name
	$empty = (is_null($_POST['recipient']) || empty($_POST['recipient']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = DOM::create("span", "To"); //moduleLiteral::get($moduleID, "lbl_templateName");
		$headerId = 'recipient'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'recipient'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
	
	// Check Subject
	$empty = (is_null($_POST['subject']) || empty($_POST['subject']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = DOM::create("span", "Subject"); //moduleLiteral::get($moduleID, "lbl_templateName");
		$headerId = 'subject'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'subject'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
	
	// Get Content Type
	$htmlContent = $_POST['htmlContent'];
	if($htmlContent)
		$msgBody = $_POST['htmlBody'];
	else
		$msgBody = $_POST['textBody'];
	
	// Initialize Mail
	$mailer = new rbMailer("support", "send");
	
	//$mailer->setSubject($_POST['subject']);
	//$mailer->AddAddress($_POST['recipient']);
	//$mailer->IsHTML();
	$mailer->MsgHTML($msgBody);
	//$mailer->setAltBody();
	
	$sender = array();
	$sender[0] = 'no-reply@redback.gr';
	$sender[1] = 'Redback No-Reply';
	// Send Mail	
	$mailer->send($_POST['subject'], $sender, $_POST['recipient']);
	
	
	// If error, show notification
	if (!$success )
	{
		//On create error	
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not send mail";
					 		
		// ERROR NOTIFICATION
		$errorNotification = new formNotification();
		$errorNotification->build("error");
		
		// Description
		$message= "AN ERROR OCCURED : ".$customErrMsg_desc; 
		$errorNotification->appendCustomMessage($message);
				
		return $errorNotification->getReport(FALSE);		
	}
	
	// SUCCESS NOTIFICATION
	$successNotification = new formNotification();
	$successNotification->build("success");
	
	// Description
	$message= $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport(FALSE);
	
}

// Create Module Page
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();
// Create form
$sForm = new simpleForm();
$sForm->build($moduleID, "emailSender", $controls = FALSE);
// Append form to Content
$HTMLContent->buildElement($sForm->get());

// Recipient Name
$title = DOM::create("span", "To"); //moduleLiteral::get($moduleID, "lbl_queryTitle"); 
$input = $sForm->getInput($type = "text", $name = "recipient", '', $class = "", $autofocus = TRUE);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Subject
$title = DOM::create("span", "Subject"); //moduleLiteral::get($moduleID, "lbl_queryTitle"); 
$input = $sForm->getInput($type = "text", $name = "subject", '', $class = "", $autofocus = TRUE);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

$msgBodyWrapper = DOM::create('div', '', '', 'msgBodyWrapper');
$sForm->append($msgBodyWrapper);

// Contnt Type Swith 
$controlBar = DOM::create('div', '', '', 'contentTypeSwitchWrapper');
DOM::append($msgBodyWrapper, $controlBar);

$contentTypeSwitch = DOM::create('div', '', '', 'contentTypeSwitch');
DOM::append($controlBar, $contentTypeSwitch);

$input = $sForm->getInput("hidden", "htmlContent", 0, $class = "", $autofocus = FALSE);
DOM::append($controlBar, $input);

// Edit control
$control = DOM::create('div', '', '', 'control');
DOM::append($contentTypeSwitch, $control);
$controlContent = DOM::create('span', 'Plain Text');
DOM::append($control, $controlContent);
DOM::appendAttr($control, 'class', 'selected');

// Delete control
$control = DOM::create('div', '', '', 'control');
DOM::append($contentTypeSwitch, $control);
$controlContent = DOM::create('span', 'Html');
DOM::append($control, $controlContent);

$msgBody = DOM::create('div', '', '', 'msgBody');
DOM::append($msgBodyWrapper, $msgBody);

// Plain Text Body
$textBody = DOM::create('div', '', '', 'textBody');
DOM::append($msgBody, $textBody);

$title = DOM::create("span", "Content"); //moduleLiteral::get($moduleID, "lbl_queryTitle"); 
$input = $sForm->getTextarea($name = "textBody", '', $class = "");
DOM::append($textBody, $input);

// Html Body
$htmlBody = DOM::create('div', '', '', 'htmlBody');
DOM::append($msgBody, $htmlBody);
DOM::appendAttr($htmlBody, 'class', 'noDisplay');

$hrmlEditor = new HTMLEditor();
$content = '<p></p>';
$hrmlEditor->build($content, "htmlBody",  HTMLEditor::HTML_EDITOR);
DOM::append($htmlBody, $hrmlEditor->get());


// Form Buttons
$title = DOM::create("span", "Send");
$submit = $sForm->getSubmitButton($title, $id = "");
$sForm->append($submit);

$title = DOM::create("span", "Clear");
$reset = $sForm->getResetButton($title, $id = "");
$sForm->append($reset);


return $HTMLContent->getReport();
//#section_end#
?>