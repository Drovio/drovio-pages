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

// Get parameters
$websiteID = engine::getVar('id');
$serverID = engine::getVar('sid');

// Initialize server
$wsServer = new wsServer($websiteID, $serverID);
if (engine::isPost())
{
	$has_error = FALSE;
	
	$ntf = new formNotification();
	$errorNtf = new formErrorNotification();
	$errorNtf->build();
	
	// Check whether to delete server
	if (isset($_POST['delete']))
	{
		// Remove server
		$status = $wsServer->remove();
		
		$ntf = new formNotification();
		if ($status)
		{
			// Build notification
			$ntf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
			$header = moduleLiteral::get($moduleID, "ntf_succ_delete");
			$ntf->append($header);
			
			// Delete server from list
			$ntf->addReportAction('website.servers.delete', $serverID);
			
			// Return notification
			return $ntf->getReport(FALSE);
		}
		else
		{
			// Build notification
			$ntf->build($type = formNotification::ERROR, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
			$header = moduleLiteral::get($moduleID, "ntf_err_couldNotDelete");
			$ntf->append($header);
			
			// Return notification
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
	
	// Create server if empty
	if (empty($serverID))
		$status = $wsServer->create($_POST['name'], $_POST['address']);
	
	// Update server information
	$status1 = $wsServer->updateInfo($_POST['description'], $_POST['type'], $_POST['connection'], $_POST['httpdocs']);
	$status2 = $wsServer->updateCredentials($_POST['username'], $_POST['password']);
	$status = $status1 && $status2;
	if (!$status)
	{
		// On create error
		$err_header = moduleLiteral::get($moduleID, "ntf_err_couldNotCreate");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating website server..."));
		return $errFormNtf->getReport(FALSE);
	}
	
	// Success notification
	$ntf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);

	// Notification Message
	$errorMessage = $ntf->getMessage("success", "success.save_success");
	$ntf->append($errorMessage);
	
	if (empty($serverID))
	{
		// Add server to list
		$serverID = $wsServer->getServerID();
		$serverInfo = $wsServer->info();
		$serverInfo['id'] = $serverID;
		$ntf->addReportAction('website.servers.add', $serverInfo);
	}
		
	return $ntf->getReport(FALSE);
}

// Create Module
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("", "serverConfig", TRUE);
$content = HTML::select(".serverConfig .formContainer")->item(0);

$form = new simpleForm();
$serverForm = $form->build("", TRUE)->engageModule($moduleID, "serverConfig")->get();
DOM::append($content, $serverForm);

// Website id
$input = $form->getInput($type = "hidden", $name = "id", $websiteID);
$form->append($input);

// Server id
if (!empty($serverID))
{
	$input = $form->getInput($type = "hidden", $name = "sid", $serverID);
	$form->append($input);
}

// Load server (if not empty)
if (!empty($serverID))
{
	// Get server info
	$serverInfo = $wsServer->info();
	
	// Set server title as header
	$attr = array();
	$attr['sname'] = $serverInfo['name'];
	$title = moduleLiteral::get($moduleID, "hd_editServer", $attr);
	$header = HTML::select(".serverInfo .header")->item(0);
	HTML::innerHTML($header, "");
	HTML::append($header, $title);
}

// quides
$title = moduleLiteral::get($moduleID, "lbl_createGuides");
$stepGuides = DOM::create("p", $title);
$form->append($stepGuides);

// server name
$title = moduleLiteral::get($moduleID, "lbl_srvName");
$input = $form->getInput($type = "text", $name = "name", $value = $serverInfo['name'], $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// server address
$title = moduleLiteral::get($moduleID, "lbl_srvAddress"); 
$input = $form->getInput($type = "text", $name = "address", $serverInfo['address'], $class = "", $autofocus = FALSE, $required = TRUE);
$notes = moduleLiteral::get($moduleID, "notes_srvAddress"); 
$form->insertRow($title, $input, $required = TRUE, $notes);

// Server type / tag
$resource = array();
$resource['dev'] = "developement";
$resource['pr'] = "production";
$selected = empty($serverInfo['type']) ? "dev" : $serverInfo['type'];
$title = moduleLiteral::get($moduleID, "lbl_srvType"); 
$input = $form->getResourceSelect($name = "type", $multiple = FALSE, $class = "", $resource, $selected);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Connection Type
$types = wsServer::getConnectionTypes();
$resource = array();
foreach($types as $type)
	$resource[$type] = $type;
$selected = empty($server['connection']) ? wsServer::CON_TYPE_FTP : $serverInfo['connection'];
$title = moduleLiteral::get($moduleID, "lbl_conType"); 
$input = $form->getResourceSelect($name = "connection", $multiple = FALSE, $class = "", $resource, $selected);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// httpdocs
$title = moduleLiteral::get($moduleID, "lbl_srvHttpdocs"); 
$input = $form->getInput($type = "text", $name = "httpdocs", $serverInfo['httpdocs'], $class = "", $autofocus = FALSE);
$notes = moduleLiteral::get($moduleID, "notes_srvHttpdocs"); 
$form->insertRow($title, $input, $required = FALSE, $notes);

// ftp group
$group = DOM::create('div');
$form->append($group);
// Username
$title = moduleLiteral::get($moduleID, "lbl_username"); 
$input = $form->getInput($type = "text", $name = "username", $serverInfo['username'], $class = "", $autofocus = FALSE, $required = TRUE);
$row = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($group, $row);
		
// Password
$title = moduleLiteral::get($moduleID, "lbl_password"); 
$input = $form->getInput($type = "password", $name = "password", $serverInfo['password'], $class = "", $autofocus = FALSE, $required = TRUE);
$row = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($group, $row);

// Delete switch
$title = moduleLiteral::get($moduleID, "lbl_delServer");
$notes = moduleLiteral::get($moduleID, "lbl_delServer_notes");
$input = $form->getInput($type = "checkbox", "delete", "", $class = "", $autofocus = TRUE, $required = FALSE);
$form->insertRow($title, $input, $required = FALSE, $notes);

// Return output
return $pageContent->getReport();
//#section_end#
?>