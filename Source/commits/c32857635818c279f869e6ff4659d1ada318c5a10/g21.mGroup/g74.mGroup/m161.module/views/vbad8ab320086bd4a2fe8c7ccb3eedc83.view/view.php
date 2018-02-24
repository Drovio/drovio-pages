<?php
//#section#[header]
// Module Declaration
$moduleID = 161;

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
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Modules\MContent;

// Build the content
$content = new MContent($moduleID);
$content->build("", "myActiveSessions", TRUE);

// Initialize dbConnection
$dbc = new dbConnection();
$reportHolder = "";

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Delete account session
	$reportHolder = "#securityAccountSessions";
	account::deleteActiveSession($_POST['sessionID']);
}

// Get Active Sessions
$q = module::getQuery($moduleID, "get_active_sessions");
$attr = array();
$attr['aid'] = account::getAccountID();
$result = $dbc->execute($q, $attr);
$sessions = $dbc->fetch($result, TRUE);

// Form
$sForm = new simpleForm("accountSessionManagerForm");
$accountForm = $sForm->build($moduleID, "sessionManager", FALSE)->get();
$content->append($accountForm);

// Current Session
//_____header
$hdContent = moduleLiteral::get($moduleID, "lbl_currentSession_description");
$header = DOM::create("p", "", "", "contentHeader");
DOM::append($header, $hdContent);
$sForm->append($header);
$currentSession = DOM::create("div", "", "currentSession");
$sForm->append($currentSession);


if (count($sessions) > 1)
{
	$hdContent = moduleLiteral::get($moduleID, "lbl_activeSession_header");
	$header = DOM::create("p", "", "", "contentHeader");
	DOM::append($header, $hdContent);
	$sForm->append($header);
	
	// Rest Active Sessions
	$accountSessions = DOM::create("div", "", "accountSessions");
	$sessionList = DOM::create("ul", "", "sessionList");
	DOM::append($accountSessions, $sessionList);
	$sForm->append($accountSessions);
}
foreach ($sessions as $session)
{
	if ($session['id'] == account::getSessionID())
		DOM::append($currentSession, getSessionElement($moduleID, $session, $sForm, "div", FALSE, FALSE));
	else
		DOM::append($sessionList, getSessionElement($moduleID, $session, $sForm, "li"));
}

return $content->getReport($reportHolder);


function getSessionElement($moduleID, $session, $sForm, $type = "li", $includeDate = TRUE, $del = TRUE)
{
	// Account Session Container
	$accountSession = DOM::create($type, "", "s".$session['id'], "accountSession");
	
	// End Activity Button
	if ($del)
	{
		$title = moduleLiteral::get($moduleID, "lbl_accountSession_stop");
		$btn_endActivity = $sForm->getLabel($title, $for = "chk_s_".$session['id'], $class = "endActivity");
	}
	
	$checkbox = $sForm->getInput($type = "radio", $name = "sessionID", $value = $session['id'], $class = "hiddenInput", $autofocus = FALSE);
	DOM::attr($checkbox, "id", "chk_s_".$session['id']);
	DOM::append($accountSession, $checkbox);
	DOM::append($accountSession, $btn_endActivity);
	
	if ($includeDate)
	{
		// Row
		$dataRow = DOM::create("div", "", "", "dataRow");
		DOM::append($accountSession, $dataRow);
		// Date
		$title = moduleLiteral::get($moduleID, "lbl_accountSession_date");
		$dataTitle = DOM::create("div", ":", "", "label");
		DOM::prepend($dataTitle, $title);
		DOM::append($dataRow, $dataTitle);
		$lastAccess = "";
			if (!empty($session['lastAccess']))
		$lastAccess = date('d F Y \o\n H:i:s', $session['lastAccess']);
		$sessionDate = DOM::create("div", $lastAccess, "", "content");
		DOM::append($dataRow, $sessionDate);
	}
	
	// Row
	$dataRow = DOM::create("div", "", "", "dataRow");
	DOM::append($accountSession, $dataRow);
	// IP
	$title = moduleLiteral::get($moduleID, "lbl_accountSession_location");
	$dataTitle = DOM::create("div", ":", "", "label");
	DOM::prepend($dataTitle, $title);
	DOM::append($dataRow, $dataTitle);
	$sessionDate = DOM::create("div", $session['ip'], "", "content");
	DOM::append($dataRow, $sessionDate);
	
	// Row
	$dataRow = DOM::create("div", "", "", "dataRow");
	DOM::append($accountSession, $dataRow);
	// Device
	$title = moduleLiteral::get($moduleID, "lbl_accountSession_device");
	$dataTitle = DOM::create("div", ":", "", "label");
	DOM::prepend($dataTitle, $title);
	DOM::append($dataRow, $dataTitle);
	$sessionDate = DOM::create("div", $session['userAgent'], "", "content");
	DOM::append($dataRow, $sessionDate);
	
	return $accountSession;
}
//#section_end#
?>