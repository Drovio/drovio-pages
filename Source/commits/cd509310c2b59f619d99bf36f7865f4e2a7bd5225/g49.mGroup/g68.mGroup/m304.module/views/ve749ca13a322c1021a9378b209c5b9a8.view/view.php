<?php
//#section#[header]
// Module Declaration
$moduleID = 304;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\person;
use \API\Profile\account;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

if (engine::isPost())
{
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	$has_error = FALSE;
	$projectID = $_POST['id'];
	
	// Authenticate account password
	$username = person::getUsername();
	$status = account::authenticate($username, $_POST['pw']);
	if (!$status)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = "Authentication";
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.authenticate"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update App Center info
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "update_appcenter");
	$attr = array();
	$attr['id'] = $projectID;
	$attr['active'] = ($_POST['ac_active'] == "on" || $_POST['ac_active'] ? 1 : 0);
	$attr['tags'] = $_POST['ac_tags'];
	$result = $dbc->execute($q, $attr);
	
	// Update BOSS Market info
	$q = module::getQuery($moduleID, "update_bossmarket");
	$attr = array();
	$attr['id'] = $projectID;
	$attr['active'] = ($_POST['bm_active'] == "on" || $_POST['bm_active'] ? 1 : 0);
	$attr['price'] = (!empty($_POST['bm_price']) && is_numeric($_POST['bm_price']) ? $_POST['bm_price'] : 0);
	$attr['tags'] = $_POST['bm_tags'];
	$result = $dbc->execute($q, $attr);
	
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}
//#section_end#
?>