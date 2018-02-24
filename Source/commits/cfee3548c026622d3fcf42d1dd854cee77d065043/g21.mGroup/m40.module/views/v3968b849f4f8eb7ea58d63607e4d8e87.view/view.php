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
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \UI\Html\HTMLContent;


$content = new HTMLContent();
$content->build();

return $content->getReport();






/*
use \API\Geoloc\lang\mlgContent;
use \API\Profile\user;
use \API\Model\protocol\ajax\ascop;
use \API\Model\units\sql\dbQuery;
use \API\Comm\database\connections\interDbConnection;
use \UI\Forms\contentPresenter;

$holder = NULL;

// Get user_id
$profile = user::profile();

// Build Personal Info Content Group
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_personalInfo", FALSE);
$post_action = ascop::get_action($policyCode, "personalInfo");
$personal_info_ctp = new contentPresenter($dom_builder, $title, $post_action, $editable = TRUE);
$full_presenter = $personal_info_ctp->get_full_presenter();

if ($_SERVER['REQUEST_METHOD'] == "GET")
{
	// Create container for the presenter
	$container = DOM::create("div", "", "personal_info");
	DOM::append($container, $full_presenter);
}
else if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	
	$dbc = new interDbConnection();
	
	// Update User Information
	$holder = "#personal_info";
	$container = $full_presenter;

	$dbq = new dbQuery("107598002", "profile.person");	
	$attr = array();
	$attr["uid"] = $profile['id'];
	$attr["firstname"] = $dbc->clear_resource($_POST["firstname"]);
	$attr["lastname"] = $dbc->clear_resource($_POST["lastname"]);
	$attr["fathersname"] = $dbc->clear_resource($_POST["fathersname"]);
	$success = $dbc->execute_query($dbq, $attr, "dta_manager");
	
	if (!$success)
	{
		$error = $reporter->internal_system_error("err.database_connection", TRUE);
		return report::get_content($dom_builder, $error);
	}
}



//_____ Populate Personal Info

$dbc = new interDbConnection();

$dbq = new dbQuery("1921568048", "profile.person");	
$attr = array();
$attr['uid'] = $profile['id'];
$personal_info_raw = $dbc->execute_query($dbq, $attr);
$personal_info = $dbc->fetch($personal_info_raw);

// person's firstname
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_firstName");
$personal_info_ctp->insert_content("input", $title, $name = "firstname", $value = $personal_info['firstname'], $type = "text", $class = "", $required = FALSE, $autofocus = FALSE, $hidden = FALSE);

// person's lastname
$title = mlgContent::get_moduleLiteral($policyCode, "lbl_lastName");
$personal_info_ctp->insert_content("input", $title, $name = "lastname", $value = $personal_info['lastname'], $type = "text", $class = "", $required = FALSE, $autofocus = FALSE, $hidden = FALSE);

report::clear();
report::add_content($container, $holder);
return report::get();
*/
//#section_end#
?>