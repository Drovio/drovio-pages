<?php
//#section#[header]
// Module Declaration
$moduleID = 161;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("DRVC", "Profile");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Geoloc\datetimer;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \API\Profile\accountSession;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Modules\MContent;

// Build the content
$pageContent = new MContent($moduleID);
$pageContent->build("", "myActiveSessions", TRUE);

// Initialize dbConnection
$dbc = new dbConnection();
$reportHolder = "";

if (engine::isPost())
{
	// Delete account session
	$reportHolder = "#sessionManager";
	$sessionID = engine::getVar('sessionID');
	accountSession::remove($sessionID);
}
$sessions = accountSession::getInstance()->getActiveSessions();

// Form
$form = new simpleForm("accountSessionManagerForm");
$sessionForm = $form->build($moduleID, "sessionManager", FALSE)->get();
$pageContent->append($sessionForm);


// Add sessions
foreach ($sessions as $session)
	$form->append(getSessionElement($moduleID, $session, $form));
	
// Set active session first
$activeSession = HTML::select(".accountSession.active")->item(0);
$parent = $activeSession->parentNode;
DOM::prepend($parent, $activeSession);

return $pageContent->getReport($reportHolder);


function getSessionElement($moduleID, $session, $form)
{
	// Account Session Container
	$accountSession = DOM::create("div", "", "s".$session['id'], "accountSession");
	if ($session['id'] == account::getInstance()->getSessionID())
		HTML::addClass($accountSession, "active");
	
	// End Activity Button
	$title = moduleLiteral::get($moduleID, "lbl_accountSession_stop");
	$btn_endActivity = $form->getLabel($title, $for = "chk_s_".$session['id'], $class = "endActivity");
	
	$checkbox = $form->getInput($type = "radio", $name = "sessionID", $value = $session['id'], $class = "hiddenInput", $autofocus = FALSE);
	DOM::attr($checkbox, "id", "chk_s_".$session['id']);
	DOM::append($accountSession, $checkbox);
	DOM::append($accountSession, $btn_endActivity);
	
	
	// Session id
	$title = moduleLiteral::get($moduleID, "lbl_accountSession_id");
	$dataRow = getDataRow($title, $session['id']);
	DOM::append($accountSession, $dataRow);
	
	// Device
	$title = moduleLiteral::get($moduleID, "lbl_accountSession_device");
	$dataRow = getDataRow($title, $session['userAgent']);
	DOM::append($accountSession, $dataRow);
	
	// Location (+ ip address)
	$title = moduleLiteral::get($moduleID, "lbl_accountSession_location");
	$dataRow = getDataRow($title, $session['location']." (".$session['ip'].")");
	DOM::append($accountSession, $dataRow);
	
	// Last access date
	$title = moduleLiteral::get($moduleID, "lbl_accountSession_date");
	$lastAccess = "";
	if (!empty($session['lastAccess']))
		$lastAccess = datetimer::live($session['lastAccess']);
	$dataRow = getDataRow($title, $lastAccess);
	DOM::append($accountSession, $dataRow);
	
	// Session Type
	$title = moduleLiteral::get($moduleID, "lbl_accountSession_stype");
	if ($session['rememberme'])
		$type = moduleLiteral::get($moduleID, "lbl_stype_private");
	else
		$type = moduleLiteral::get($moduleID, "lbl_stype_public");
	$dataRow = getDataRow($title, $type);
	DOM::append($accountSession, $dataRow);
	
	return $accountSession;
}

function getDataRow($title, $content)
{
	// Build row
	$dataRow = DOM::create("div", "", "", "dataRow");
	
	// Row data
	$dataTitle = DOM::create("div", ":", "", "label");
	DOM::prepend($dataTitle, $title);
	DOM::append($dataRow, $dataTitle);
	$sessionDate = DOM::create("div", $content, "", "content");
	DOM::append($dataRow, $sessionDate);
	
	return $dataRow;
}
//#section_end#
?>