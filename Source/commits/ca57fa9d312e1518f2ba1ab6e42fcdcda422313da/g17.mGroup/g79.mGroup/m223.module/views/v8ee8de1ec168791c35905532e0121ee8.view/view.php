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
importer::import("DEV", "Websites");
importer::import("ESS", "Protocol");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \DEV\Websites\wsServer;

if (engine::isPost())
{
	$has_error = FALSE;
	
	$ntf = new formNotification();
	$errorNtf = new formErrorNotification();
	$errorNtf->build();
	
	// Check whether to delete server
	if (isset($_POST['delete']))
	{
		$wsServer = new wsServer($_POST['pid'], $_POST['sid']);
		$status = $wsServer->remove();
		
		$ntf = new formNotification();
		if ($status)
		{
			$ntf->build(formNotification::SUCCESS);
			$ntf->appendCustomMessage(moduleLiteral::get($moduleID, "ntf_succ_delete"));
			
			$ntf->addReportAction('website.servers.delete', $_POST['sid']);
				
			return $ntf->getReport(FALSE);
		}
		else
		{ 
			$ntf->build(formNotification::ERROR);
			$ntf->appendCustomMessage(moduleLiteral::get($moduleID, "ntf_err_couldNotDelete"));
				
			return $ntf->getReport(FALSE);
		}
	}
	
	// Check server name
	if (empty($_POST['name']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_srvName");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check server address
	if (empty($_POST['address']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_srvAddress");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errorNtf->getReport();
		
	
	// Set server
	$wsServer = new wsServer($_POST['pid'], $_POST['sid']);
	
	// Create server if empty
	if (empty($_POST['sid']))
		$status = $wsServer->create($_POST['name'], $_POST['address']);
	else
	{
		// TODO
		// Maybe ->load() to see if server exists, in order to
		// provide better error detection
		$status = TRUE;
	}
	
	
	// If error, show notification
	if ($status)
	{
		$success = $wsServer->updateInfo($_POST['description'], $_POST['type'], $_POST['connection'], $_POST['httpdocs']);
		if ($success)
			$success = $wsServer->updateCredentials($_POST['username'], $_POST['password']);
		
		if ($success)
		{ 
			// SUCCESS NOTIFICATION
			$ntf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
			// Notification Message
			$errorMessage = $ntf->getMessage("success", "success.save_success");
			$ntf->append($errorMessage);
			
			if (empty($_POST['sid']))
			{
				$serverID = $wsServer->getServerID();
				$ntf->addReportAction('website.servers.add', $serverID.":".$_POST['name']);
			}
				
			return $ntf->getReport(FALSE);
		}
	}
	
	// On create error
	$err_header = moduleLiteral::get($moduleID, "ntf_err_couldNotCreate");
	$err = $errFormNtf->addHeader($err_header);
	$errFormNtf->addDescription($err, DOM::create("span", "Error creating website server..."));
	return $errFormNtf->getReport(FALSE);
}

// Create Module
$HTMLContent = new MContent($moduleID);
$actionFactory = $HTMLContent->getActionFactory();

$HTMLContent->build("","serverConfig", TRUE);
$content = HTML::select(".serverConfig .formContainer")->item(0);

$sForm = new simpleForm();
$sForm->build("", TRUE)->engageModule($moduleID, "serverConfig");
DOM::append($content, $sForm->get());

// [Hidden] - website id
$input = $sForm->getInput("hidden", "pid", $_GET['id']);
$sForm->append($input);

// [Hidden] - server id - if exists
$sid = empty($_GET['sid']) ? '' : $_GET['sid'];
if (!empty($sid))
{
	$input = $sForm->getInput("hidden", "sid", $sid);
	$sForm->append($input);
}

// Load server (if not empty)
if (!empty($sid))
{
	$wsServer = new wsServer($_GET['id'], $_GET['sid']);
	$serverInfo = $wsServer->info();
	
	// Set server title as header
	$title = moduleLiteral::get($moduleID, "hd_editServer", $serverInfo);
	$header = HTML::select(".serverInfo .header .title")->item(0);
	HTML::innerHTML($header, "");
	HTML::append($header, $title);
}

// quides
$stepGuides = DOM::create('p');
$text = moduleLiteral::get($moduleID, "lbl_createGuides");
DOM::append($stepGuides, $text);
$sForm->append($stepGuides);

// server name
$title = moduleLiteral::get($moduleID, "lbl_srvName");
$input = $sForm->getInput($type = "text", $name = "name", $value = $serverInfo['name'], $class = "", $autofocus = FALSE);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// server address
$title = moduleLiteral::get($moduleID, "lbl_srvAddress"); 
$input = $sForm->getInput($type = "text", $name = "address", $serverInfo['address'], $class = "", $autofocus = FALSE);
$notes = moduleLiteral::get($moduleID, "notes_srvAddress"); 
$sForm->insertRow($title, $input, $required = FALSE, $notes);

// Server type / tag
$resource = array();
$resource['dev'] = "developement";
$resource['pr'] = "production";
$selected = empty($serverInfo['type']) ? "dev" : $serverInfo['type'];
$title = moduleLiteral::get($moduleID, "lbl_srvType"); 
$input = $sForm->getResourceSelect($name = "type", $multiple = FALSE, $class = "", $resource, $selected);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Connection Type
$types = wsServer::getConnectionTypes();
$resource = array();
foreach($types as $type)
	$resource[$type] = $type;
$selected = empty($server['connection']) ? wsServer::CON_TYPE_FTP : $serverInfo['connection'];
$title = moduleLiteral::get($moduleID, "lbl_conType"); 
$input = $sForm->getResourceSelect($name = "connection", $multiple = FALSE, $class = "", $resource, $selected);
$sForm->insertRow($title, $input, $required = FALSE, $notes = "");

// httpdocs
$title = moduleLiteral::get($moduleID, "lbl_srvHttpdocs"); 
$input = $sForm->getInput($type = "text", $name = "httpdocs", $serverInfo['httpdocs'], $class = "", $autofocus = FALSE);
$notes = moduleLiteral::get($moduleID, "notes_srvHttpdocs"); 
$sForm->insertRow($title, $input, $required = FALSE, $notes);

// ftp group
$group = DOM::create('div');
$sForm->append($group);
// Username
$title = moduleLiteral::get($moduleID, "lbl_username"); 
$input = $sForm->getInput($type = "text", $name = "username", $serverInfo['username'], $class = "", $autofocus = FALSE);
$row = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($group, $row);
		
// Password
$title = moduleLiteral::get($moduleID, "lbl_password"); 
$input = $sForm->getInput($type = "password", $name = "password", $serverInfo['password'], $class = "", $autofocus = FALSE);
$row = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($group, $row);

// Delete switch
$title = moduleLiteral::get($moduleID, "lbl_delServer");
$notes = moduleLiteral::get($moduleID, "lbl_delServer_notes");
$input = $sForm->getInput($type = "checkbox", "delete", "", $class = "", $autofocus = TRUE, $required = FALSE);
$sForm->insertRow($title, $input, $required = FALSE, $notes);

// Return output
return $HTMLContent->getReport();
//#section_end#
?>