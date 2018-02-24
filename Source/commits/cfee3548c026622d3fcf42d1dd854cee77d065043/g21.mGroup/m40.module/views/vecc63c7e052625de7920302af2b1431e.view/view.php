<?php
//#section#[header]
// Module Declaration
$moduleID = 40;

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
importer::import("API", "Geoloc");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("UI", "Forms");
importer::import("UI", "Notifications");
//#section_end#
//#section#[code]
use \API\Model\protocol\ajax\ascop;
use \API\Profile\user;
use \API\Geoloc\lang\mlgContent;
use \API\Geoloc\locale;
use \API\Model\units\sql\dbQuery;
use \API\Comm\database\connections\interDbConnection;
use \UI\Forms\simpleForm;
use \UI\Notifications\notification;
use \UI\Notifications\error_bucket;

// Initialize database elements
$dbc = new interDbConnection();

// Initialize current user
$profile = user::profile();

// Initialize gui elements
$holder = NULL;
$inner_container = DOM::create();

if ($_SERVER['REQUEST_METHOD'] == "GET")
{
	// Create container for the content
	$container = DOM::create("div", "", "phoneHolder");
	DOM::append($container, $inner_container);
}
else if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	$error_bucket = new error_bucket();
	$errBucketList = $error_bucket->get_error_bucket();

	
}

// Create Notification
$notification = new notification();

// Build Notification Container
$notification->build_notification("warning", $header = TRUE);

// Retrieve Message
$message = $notification->get_message("warning", "wrn.function_uc");
$notification->append_content($message);

$request = $notification->get_notification();

report::clear();
report::add_content($request, $holder);
return report::get();
//#section_end#
?>