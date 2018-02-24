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
$holder = "#email_editor";

// Create container for the presenter
$inner_container = DOM::create("div");

if ($_SERVER["REQUEST_METHOD"] == "GET")
{
	// Create outer container
	$container = DOM::create("div", "", "email_editor");
	DOM::append($container, $inner_container);
}
else if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$holder = "email_editor";
	$container = $inner_container;

	// Change email
	return;
}

$currentEmail_container = DOM::create("p");
DOM::append($inner_container, $currentEmail_container);

$currentEmail = mlgContent::get_moduleLiteral($policyCode, "lbl_currentEmail");
DOM::append($currentEmail_container, $currentEmail);

// Get email from database

$dbc = new interDbConnection();

$dbq = new dbQuery("1921568048", "profile.person");

$attr = array();
$attr["uid"] = $current_user_id;
$result = $dbc->execute_query($dbq, $attr);
$person = $dbc->fetch($result);

$person_email = DOM::create("strong", $person['mail']);
DOM::append($currentEmail_container, $person_email);


$email_msg_content = mlgContent::get_moduleLiteral($policyCode, "msg_email_edit");
DOM::append($inner_container, $email_msg_content);

/*
$username_form = new simpleForm();
$submit_action = ascop::get_action($policyCode, "username_edit");
$username_form_element = $username_form->create_form($id = "", $submit_action, $role = "", $controls = TRUE);
DOM::append($inner_container, $username_form_element);


// Username
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_newUsername");
$fgroup = $username_form->get_form_input("input", $title, $name = "username", $current_username, $type = "text", $class = "", $required = TRUE, $autofocus = FALSE);
$username_form->insert_to_body($fgroup['group']);

// currentPassword
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_currentPassword");
$fgroup = $username_form->get_form_input("input", $title, $name = "currentPassword", $value = "", $type = "password", $class = "", $required = TRUE, $autofocus = FALSE);
$username_form->insert_to_body($fgroup['group']);
*/

report::clear();
report::add_content($container, $holder);
return report::get();
//#section_end#
?>