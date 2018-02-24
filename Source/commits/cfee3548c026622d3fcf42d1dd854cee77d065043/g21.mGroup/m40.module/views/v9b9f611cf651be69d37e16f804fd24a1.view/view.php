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
$container = DOM::create("div", "", "addressHolder");
	
$inner_container = DOM::create();
DOM::append($container, $inner_container);

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	$error_bucket = new error_bucket();
	$errBucketList = $error_bucket->get_error_bucket();

	// Set the new conainer and the holder
	$holder = simpleForm::get_reportHolder($_POST['formID']);
	$container = $inner_container;
	//$clearedPOST = array_map("trim", $_POST);
	
	// Add the new address
	//_____ Perform all available checks
	
	// Check if description abide by the restrictions
	//__ Not Empty
	$empty = is_null($_POST['description']) || empty($_POST['description']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = mlgContent::get_moduleLiteral($policyCode, "lbl_addressDescription");
		$errSubList = $error_bucket->get_error_bucket_item($errBucketList, $err_header, $open = FALSE);
		
		// Descriptions
		$error_bucket->insert_error_desc_message($errSubList, $message_id = "err.required");
	}
	
	// Check if address abide by the restrictions
	//__ Not Empty
	$empty = is_null($_POST['address']) || empty($_POST['address']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = mlgContent::get_moduleLiteral($policyCode, "lbl_address");
		$errSubList = $error_bucket->get_error_bucket_item($errBucketList, $err_header, $open = FALSE);
		
		// Descriptions
		$error_bucket->insert_error_desc_message($errSubList, $message_id = "err.required");
	}
	
	// Check if zipcode abide by the restrictions
	//__ Not Empty
	$empty = is_null($_POST['zipcode']) || empty($_POST['zipcode']);
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = mlgContent::get_moduleLiteral($policyCode, "lbl_zipcode");
		$errSubList = $error_bucket->get_error_bucket_item($errBucketList, $err_header, $open = FALSE);
		
		// Descriptions
		$error_bucket->insert_error_desc_message($errSubList, $message_id = "err.required");
	}
	
	if ($has_error)
	{
		report::clear();
		$notification = reporter::get("error", "error", "err.invalid_data", $errBucketList);
		report::add_content($notification, $holder);
		return report::get();
	}
	else
	{
		// Create person address	
		$dbq = new dbQuery("1916130585", "profile.person");
		
		$attr = array();
		$attr['pid'] = $profile['personID'];
		$attr['description'] = $_POST['description'];
		$attr['address'] = $_POST['address'];
		$attr['zipcode'] = $_POST['zipcode'];
		$attr['area'] = $_POST['townArea'];
		$attr['cid'] = $_POST['country'];
		$attr['tid'] = $_POST['town'];
		
		//$result = $dbc->execute_query($dbq, $attr, "dta_manager");
		
		// Clear report
		report::clear();
		
		// In case of wrong input data, show database error
		if (!$result)
		{
			$error = reporter::error("err.database_connection");
			report::add_content($error, $holder);
			
		}
		
		return report::get();
	}
}

// Add new address Form
$create_addressForm = new simpleForm();
$submit_action = ascop::get_action($policyCode, "addNewAddress");
$create_addressFormElement = $create_addressForm->create_form($id = "newAddress", $submit_action, $role = "", $controls = TRUE);
DOM::append($inner_container, $create_addressFormElement);

// Header
$hd = mlgContent::get_moduleLiteral($policyCode, "hdr_addNewAddress");
$hdr = $create_addressForm->get_header($hd, "2");
$create_addressForm->insert_to_body($hdr);

// description
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_addressDescription");
$fgroup = $create_addressForm->get_form_input("input", $title, $name = "description", $value = "", $type = "text", $class = "", $required = TRUE, $autofocus = TRUE);
$create_addressForm->insert_to_body($fgroup['group']);

// address
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_address");
$fgroup = $create_addressForm->get_form_input("input", $title, $name = "address", $value = "", $type = "text", $class = "", $required = TRUE, $autofocus = FALSE);
$create_addressForm->insert_to_body($fgroup['group']);

// zipcode
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_zipcode");
$fgroup = $create_addressForm->get_form_input("input", $title, $name = "zipcode", $value = "", $type = "text", $class = "", $required = TRUE, $autofocus = FALSE);
$create_addressForm->insert_to_body($fgroup['group']);

// area
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_townArea");
$fgroup = $create_addressForm->get_form_input("input", $title, $name = "townArea", $value = "", $type = "text", $class = "", $required = FALSE, $autofocus = FALSE);
$create_addressForm->insert_to_body($fgroup['group']);

// country
//_____ get current country
$current_country_id = geoloc::get_countryId();
//_____ get all available countries
$dbq = new dbQuery("11977879", "geo");
$result = $dbc->execute_query($dbq, $attr);
$country_resource = $dbc->to_array($result, "id", "countryName");
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_country");
$fgroup = $create_addressForm->get_rsrc_select($title, $name = "country", $country_resource, $value = $current_country_id, $multi = FALSE, $required = TRUE, $autofocus = FALSE);
$create_addressForm->insert_to_body($fgroup['group']);

// town
//_____ get all available towns
$dbq = new dbQuery("1400229893", "geo");
$result = $dbc->execute_query($dbq, $attr);
$town_resource = $dbc->to_array($result, "id", "description");
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_town");
$fgroup = $create_addressForm->get_rsrc_select($title, $name = "town", $town_resource, $value = "", $multi = FALSE, $required = TRUE, $autofocus = FALSE);
$create_addressForm->insert_to_body($fgroup['group']);

report::clear();
report::add_content($container, $holder);
return report::get();
//#section_end#
?>