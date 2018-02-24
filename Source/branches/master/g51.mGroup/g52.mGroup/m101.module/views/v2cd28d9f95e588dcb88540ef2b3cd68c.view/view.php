<?php
//#section#[header]
// Module Declaration
$moduleID = 101;

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
importer::import("AEL", "Mail");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("ESS", "Environment");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Developer\codeMirror;
use \API\Model\modules\mMail;

if (engine::isPost())
{
	print_r($_POST);
	return;
}

$pageContent = new MContent($moduleID);
$pageContent->build("", "testingPage");

$form = new simpleForm();
$testForm = $form->build()->engageModule($moduleID)->get();
$pageContent->append($testForm);

// Build a code mirror object
$cm = new codeMirror();
$codeEditor = $cm->build($type = "fgdf", $code = "This is the default code", $name = "wideContent", $editable = TRUE)->get();
$form->append($codeEditor);

return $pageContent->getReport();

/*
// Send email
$attr = array();
$attr['member_title'] = "Account Title";//$accountInfo['title'];
$attr['email_address'] = "mymail@example.com";//$_POST['mail'];
$attr['reset_url'] = url::resolve("www", "/profile/reset_pw.php?rs=123213123");//.$passwordResetHash);
$attr['ip_address'] = "1.1.1.1";//$_SERVER['REMOTE_ADDR'];
$subject = "Drovio password changed";
return mMail::send("/mail/password_reset.html", $subject, "papikas.ioan@gmail.com", $attr);


/*
use \AEL\Mail\appMailer;

$apm = new appMailer(appMailer::TEAM_MODE);
$apm->addRecipient("papikas.ioan@gmail.com");
$apm->addAttachment("/retail/invoices/exports/inv-cmp6-id6_1_5.pdf", FALSE);
$apm->addAttachment("/retail/invoices/exports/inv-cmp6-id6_1_5_a.pdf", FALSE);
$status = $apm->send("Test html message", "This is the text body", "<h1>This is a header 1.</h1>");
return $status;

/*
$url = "https://api.mailgun.net/v3/sandbox9c158280525b49db916017c0a442ae5f.mailgun.org/messages";
$parameters = array();
$parameters['from'] = "Drovio Sandbox <postmaster@sandbox.drov.io>";
$parameters['to'] = "papikas.ioan@gmail.com";
$parameters['subject'] = "Hello from mailgun";
$parameters['text'] = "Congratulations Ioannis Papikas, you just sent an email with Mailgun!  You are truly awesome!!!";

// Initialize cURL
$curl = curl_init();

// Set options
$options = array();
$options[CURLOPT_RETURNTRANSFER] = 1;
$options[CURLOPT_URL] = $url;
$options[CURLOPT_USERPWD] = "api:key-b596b7499f9c28896ee15586ab0dbc65";

//$method = "POST";
if (TRUE)//$method == "POST")
{
	$options[CURLOPT_POST] = 1;
	$options[CURLOPT_POSTFIELDS] = $parameters;
}

//$options['CURLOPT_RETURNTRANSFER'] = 1;

// Set options array
curl_setopt_array($curl, $options);

// Execute and close url
$response = curl_exec($curl);
curl_close($curl);
*/
// Return response
return $response;

/*
curl -s --user 'api:key-3ax6xnjp29jd6fds4gc373sgvjxteol0' \
    https://api.mailgun.net/v3/samples.mailgun.org/messages \
    -F from='Excited User <excited@samples.mailgun.org>' \
    -F to='devs@mailgun.net' \
    -F subject='Hello' \
    -F text='Testing some Mailgun awesomeness!'


/*
$p = new MContent($moduleID);
$p->build("", "testingPage");


// Initialize basic options

// Create options array
$options = array();

// Load Common Domain / Server Configuration
//$options['Hostname'] = 'www.drov.io';

// Load Credentials
$options['SMTPUsername'] = "papikas.ioan@gmail.com";
$options['SMTPPassword'] = "my30031988gm@il";

// Load options in send mode

// SMTP configuration
// Basic properties
$options['SMTPHost'] = 'smtp.gmail.com';
$options['SMTPPort'] = 587;
$options['SMTPAuth'] = true;
$options['SMTPAuthType'] = 'LOGIN';

// Optional properties
$optionSetArray['SMTPSecure'] = 'ssl';
//$commonOptioset['SMTPRealm'] = '';
//$commonOptioset['SMTPWorkstation'] = '';
//$commonOptioset['SMTPTimeout'] = 10;


// Load Additional Mail Configuration Options
$options['ContentType'] = 'text/plain';
$options['Encoding']= '8bit';
$options['WordWrap'] = 0;

// Optional configuration
//$optionSetArray['ReturnPath'] = '';
//$optionSetArray['ConfirmReadingTo'] = '';


// Create a mailer instance
$mailer = new mailer();

// Initialize options (set in the previous example)
$mailer->options($options);
		
		
		
// SEND EMAIL

// Set mail subject
$mailer->subject($subject = "Mail Subject");

// Set HTML message
$mailer->MsgHTML($messageHTML = "HTML Message for mail");

// Add recipients
$mailer->AddAddress($mail = "papikas.ioan@gmail.com", $name = "Ioannis Papikas");


// Send mail
$sender = array();
$sender["no-reply@drov.io"] = 'Drovio NoReply';
$status = $mailer->send($subject, $sender);
var_dump($status);
*/
//#section_end#
?>