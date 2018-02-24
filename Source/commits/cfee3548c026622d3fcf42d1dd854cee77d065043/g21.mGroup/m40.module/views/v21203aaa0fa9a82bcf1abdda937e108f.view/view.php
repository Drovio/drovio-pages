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
//#section_end#
//#section#[code]
use \API\Profile\user;
use \API\Model\protocol\ajax\ascop;
use \API\Geoloc\lang\mlgContent;
use \API\Model\units\sql\dbQuery;
use \API\Comm\database\connections\interDbConnection;
use \UI\Forms\simpleForm;

$profile = user::profile();
$current_user_id = $profile['id'];
$current_username = $profile['username'];
$holder = "#username_info";

// Create container for the presenter
$inner_container = DOM::create("div");

if ($_SERVER["REQUEST_METHOD"] == "GET")
{
	// Create outer container
	$container = DOM::create("div", "", "username_info");
	DOM::append($container, $inner_container);
}
else if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	//$holder = "username_info";
	$container = $inner_container;

	// Change username and replace holder
	$clearedPOST = $_POST;//$dbql->trim_resource($_POST);
	
	if (empty($clearedPOST["username"]) || empty($clearedPOST["currentPassword"]))
	{
		$invalid_data = $reporter->invalid_data();
		return report::get_content($dom_builder, $invalid_data);
	}
	
	$currentPassword = $clearedPOST["currentPassword"];
	$result = $currentUser->authenticate($current_username, $currentPassword);
	if (!is_null($result))
	{
		
		$dbc = new interDbConnection();
		
		$dbq = new dbQuery("update_username", "profile.user");
		
		$attr = array();
		$attr["uid"] = $current_user_id;
		$attr["username"] = $clearedPOST["username"];
		$success = $dbc->execute_query($dbq, $attr, "dta_manager");
		
		if (!$success)
		{
			$system_error = $reporter->internal_system_error("err.database_connection", TRUE);
			return report::get_content($dom_builder, $system_error);
		}
			
		$current_username = $clearedPOST["username"];
	}
	else
	{
		$invalid_data = $reporter->invalid_data();
		return report::get_content($dom_builder, $invalid_data);
	}
	
	return;
}

$username_edit_message_content = mlgContent::get_moduleLiteral($policyCode, "msg_username_edit");
DOM::append($inner_container, $username_edit_message_content);

$username_form = new simpleForm();
$submit_action = ascop::get_action($policyCode, "username_edit");
$username_form_element = $username_form->create_form($id = "editUsername", $submit_action, $role = "", $controls = TRUE);
DOM::append($inner_container, $username_form_element);


// Username
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_newUsername");
$fgroup = $username_form->get_form_input("input", $title, $name = "username", $current_username, $type = "text", $class = "", $required = TRUE, $autofocus = FALSE);
$username_form->insert_to_body($fgroup['group']);

// currentPassword
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_currentPassword");
$fgroup = $username_form->get_form_input("input", $title, $name = "currentPassword", $value = "", $type = "password", $class = "", $required = TRUE, $autofocus = FALSE);
$username_form->insert_to_body($fgroup['group']);

report::clear();
report::add_content($container, $holder);
return report::get();
//#section_end#
?>