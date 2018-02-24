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
importer::import("API", "Geoloc");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("UI", "Forms");
importer::import("UI", "Notifications");
//#section_end#
//#section#[code]
use \API\Model\protocol\ajax\ascop;
use \API\Geoloc\lang\mlgContent;
use \API\Profile\user;
use \UI\Forms\simpleForm;
use \UI\Notifications\error_bucket;

// Initialize gui elements
$holder = NULL;
$container = DOM::create("div", "", "changePassword");

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$holder = "#changePassword";
	
	// Clear report
	report::clear();
	
	// Check if current password is correct
	$profile = user::profile();
	$username = $profile['username'];
	$password = $_POST['currentPwd'];
	
	$has_error = FALSE;
	$error_bucket = new error_bucket();
	$errBucketList = $error_bucket->get_error_bucket();
	
	if (is_null(user::authenticate($username, $password)))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = mlgContent::get_moduleLiteral($policyCode, "lbl_currentPassword");
		$errSubList = $error_bucket->get_error_bucket_item($errBucketList, $err_header, $open = FALSE);
		
		// Descriptions
		$error_bucket->insert_error_desc_message($errSubList, $message_id = "err.invalid");
	}
	
	// Check password rules
	$match = preg_match("/^.*(?=.*\d)((?=.*[a-z])|(?=.*[A-Z]))((?=.*\W)|(?=.*\_)).*$/", $_POST['newPwd']);
	if (!$match)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = mlgContent::get_moduleLiteral($policyCode, "lbl_newPassword");
		$errSubList = $error_bucket->get_error_bucket_item($errBucketList, $err_header, $open = FALSE);
		
		// Descriptions
		$error_bucket->insert_error_desc_message($errSubList, $message_id = "err.PASSWORD_REGEXP");
	}
	
	// Check if verification is valid
	if (empty($_POST['newPwd']) || $_POST['newPwd'] != $_POST['verifyPwd'])
	{
		$has_error = TRUE;
		
		// Header
		$err_header = mlgContent::get_literal("global::dictionary", "verifification");
		$errSubList = $error_bucket->get_error_bucket_item($errBucketList, $err_header, $open = FALSE);
		
		// Descriptions
		$error_bucket->insert_error_desc_message($errSubList, $message_id = "err.validate");
	}
	
	if ($has_error)
	{
		report::clear();
		$notification = reporter::get("error", "error", "err.invalid_data", $errBucketList);
		report::add_content($notification, simpleForm::get_reportHolder($_POST['formID']));
		return report::get();
	}
	return report::get();
	// Change Password
	
	print_r($_POST);
}

// Create form
$passwordChanger = new simpleForm();
$submit_action = ascop::get_action($policyCode, "changePassword");
$passwordForm = $passwordChanger->create_form($id = "passwordChanger", $submit_action, $role = "", $controls = TRUE);
DOM::append($container, $passwordForm);

// Current Password
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_currentPassword");
$fgroup = $passwordChanger->get_form_input("input", $title, $name = "currentPwd", $value = "", $type = "password", $class = "", $required = TRUE, $autofocus = TRUE);
$passwordChanger->insert_to_body($fgroup['group']);

// New Password
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_newPassword");
$fgroup = $passwordChanger->get_form_input("input", $title, $name = "newPwd", $value = "", $type = "password", $class = "", $required = TRUE, $autofocus = FALSE);
$passwordChanger->insert_to_body($fgroup['group']);


// Verify New Password
$title = mlgContent::get_literal("global::dictionary", "verifification");
$fgroup = $passwordChanger->get_form_input("input", $title, $name = "verifyPwd", $value = "", $type = "password", $class = "", $required = TRUE, $autofocus = FALSE);
$passwordChanger->insert_to_body($fgroup['group']);

//---------- AUTO-GENERATED CODE ----------//
// Clear report stack
report::clear();



//_____ Place Your Code Here
// Returns the development page notification
//$default = reporter::get("success", "info", "info.page_default");



// Add content
report::add_content($container, $holder);
//report::add_content($default, $data_holder = NULL, $method = "replace", $prompt = FALSE);

// Return the report
return report::get();
//#section_end#
?>