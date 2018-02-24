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
importer::import("DEV", "Websites");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \DEV\Websites\wsServer;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;

use \ESS\Protocol\server\HTMLServerReport;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	if (isset($_POST['delete']))
	{
		$wsServer = new wsServer($_POST['pid']);
		$status = $wsServer->deleteServer($_POST['sid']);
		
		$ntf = new formNotification();
		if($status)
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

	$has_error = FALSE;
	$errorNtf = new formErrorNotification();
	$errorNtf->build();
	
	if (empty($_POST['srvName']))
	{
		$has_error = TRUE;
		$hd = $errorNtf->addErrorHeader('', moduleLiteral::get($moduleID, "lbl_srvName"));		
		$errorNtf->addErrorDescription($hd, '', "err.required");
	}
	
	// If error, show notification
	if ($has_error)
		return $errorNtf->getReport();
	unset($errorNtf);
	
	//No parametres error -> Continue	
	$wsServer = new wsServer($_POST['pid']);
	$status = $wsServer->setServer($_POST['sid'],$_POST['srvName'], $_POST['srvAddress']);
	
	if(!is_bool($status))
	{
		$sid = $status;
		$status = TRUE;
	}
	
	// If error, show notification
	$ntf = new formNotification();
	if ($status)
	{
		$success = $wsServer->setServerExtra($sid, $_POST['description'], $_POST['type'], $_POST['conType']);
		if($success)
		{
			switch ($_POST['conType']) {
				case wsServer::CON_TYPE_FTP:
					$success = $wsServer->setFTPconfig($sid, $_POST['username'], $_POST['password']);
					break;
				default:
					$success = FALSE;
			}
		}
		
		if($success)
		{ 
			// SUCCESS NOTIFICATION
			$ntf->build(formNotification::SUCCESS);
			$ntf->append($ntf->getMessage("success", "success.save_success"));
			
			if(empty($_POST['sid'])) // We are adding a new server
				$ntf->addReportAction('website.servers.add', $_POST['sid'].":".$_POST['srvName']);
				
			return $ntf->getReport(FALSE);
		}
	}
	
	//On create error
	$ntf->build(formNotification::ERROR);
	$ntf->appendCustomMessage(moduleLiteral::get($moduleID, "ntf_err_couldNotCreate"));
		
	return $ntf->getReport(FALSE);
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
$input = $sForm->getInput("hidden", "pid", $_GET['id']);
$sForm->append($input);

// [Hidden] - server id - if exists
$sid = empty($_GET['sid']) ? '' : $_GET['sid'];
$input = $sForm->getInput("hidden", "sid", $sid);
$sForm->append($input);

if(!empty($sid))
{
	$wsServer = new wsServer($_GET['id']);
	$server = $wsServer->getServer($_GET['sid']);
}

// quides
$stepGuides = DOM::create('p');
$text = moduleLiteral::get($moduleID, "lbl_createGuides");
DOM::append($stepGuides, $text);
$sForm->append($stepGuides);

// server name
$title = moduleLiteral::get($moduleID, "lbl_srvName"); 
$input = $sForm->getInput($type = "text", $name = "srvName", $value = $server['name'], $class = "", $autofocus = FALSE);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Server type / tag
$resource = array();
$resource['dev'] = "developement";
$resource['pr'] = "production";
$selected = empty($server['type']) ? "dev" : $server['type'];
$title = moduleLiteral::get($moduleID, "lbl_srvType"); 
$input = $sForm->getResourceSelect($name = "srvType", $multiple = FALSE, $class = "", $resource, $selected);
$sForm->insertRow($title, $input, $required = FALSE, $notes = "");

// server address
$title = moduleLiteral::get($moduleID, "lbl_srvAddress"); 
$input = $sForm->getInput($type = "text", $name = "srvAddress", $server['address'], $class = "", $autofocus = FALSE);
$sForm->insertRow($title, $input, $required = FALSE, $notes = "");

// Connection Type
$types = wsServer::getConnectionTypes();
$resource = array();
foreach($types as $type)
{
	$resource[$type] = $type;
}
$selected = empty($server['conType']) ? wsServer::CON_TYPE_FTP : $server['conType'];
$title = moduleLiteral::get($moduleID, "lbl_conType"); 
$input = $sForm->getResourceSelect($name = "conType", $multiple = FALSE, $class = "", $resource, $selected);
$sForm->insertRow($title, $input, $required = FALSE, $notes = "");

// connection settings
switch ($selected) {
	case wsServer::CON_TYPE_FTP:
		if(!is_null($wsServer))
		{
			$greds = $wsServer->getFTPconfig($_GET['sid']);
			$username = $greds['user'];
			$password = $greds['pass'];
		}
		break;
	default:
		break;
}

// ftp group
$group = DOM::create('div');
$sForm->append($group);
// Username
$title = moduleLiteral::get($moduleID, "lbl_username"); 
$input = $sForm->getInput($type = "text", $name = "username", $username, $class = "", $autofocus = FALSE);
$row = $sForm->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::append($group, $row);
		
// Password
$title = moduleLiteral::get($moduleID, "lbl_password"); 
$input = $sForm->getInput($type = "text", $name = "password", $password, $class = "", $autofocus = FALSE);
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