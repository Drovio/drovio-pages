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
importer::import("UI", "My");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Model\protocol\ajax\ascop;
use \API\Profile\user;
use \API\Geoloc\lang\mlgContent;
use \API\Geoloc\locale;
use \API\Model\units\sql\dbQuery;
use \API\Comm\database\connections\interDbConnection;
use \UI\Forms\simpleForm;
use \UI\Presentation\heading;
use \UI\My\personAddress;
use \UI\Presentation\layoutContainer;

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
	$container = DOM::create("div", "", "addressHolder");
	DOM::append($container, $inner_container);
}
else if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Set the new conainer and the holder
	$holder = "#addressHolder";
	$container = $inner_container;
	
	$post_act = $_POST['act'];
	// Delete the selected address
	if ($post_act == "del")
	{
		// Delete person address	
		$dbq = new dbQuery("1463255983", "profile.person");
		
		$attr = array();
		$attr['addrid'] = $_POST['address_id'];
		print_r($attr);
		$result = $dbc->execute_query($dbq, $attr, "dta_manager");
	}
	else if ($post_act == "mkdef")
	{
		// Update personal default address	
		$dbq = new dbQuery("2120145441", "profile.person");
		
		$attr = array();
		$attr['uid'] = $profile['id'];
		$attr['addrid'] = $_POST['address_id'];
		
		$result = $dbc->execute_query($dbq, $attr, "dta_manager");
	}
}

// Get person addresses

$dbq = new dbQuery("120297203", "profile.person");

$attr = array();
$attr["uid"] = $profile['id'];
$result = $dbc->execute_query($dbq, $attr);

$addressCount = $dbc->get_num_rows($result);
if ($addressCount == 0)
{
	$absent_address_message = DOM::create("p");
	DOM::append($inner_container, $absent_address_message);
	
	$message = mlgContent::get_moduleLiteral($policyCode, "lbl_absentAddresses");
	DOM::append($absent_address_message, $message);
}
else
{
	// My addresses header
	$hd = mlgContent::get_moduleLiteral($policyCode, "lbl_myAddresses");
	$hd_element = heading::get($hd, 2);
	DOM::append($inner_container, $hd_element);
	
	// My default address header
	$hd = mlgContent::get_moduleLiteral($policyCode, "lbl_defaultAddress");
	$hd_element = heading::get($hd, 3);
	DOM::append($inner_container, $hd_element);
	
	$default_address_id = NULL;
	
	// Get default address
	$dbq = new dbQuery("506231892", "profile.person");
	$attr = array();
	$attr["uid"] = $profile['id'];
	$default_addressRaw = $dbc->execute_query($dbq, $attr);
	
	if ($dbc->get_num_rows($default_addressRaw) > 0)
	{
		$default_address = $dbc->fetch($default_addressRaw);
		
		// Set default address id
		$default_address_id = $default_address['address_id'];
		
		$address_item = new personAddress();
		$address_item->description = $default_address['description'];
		$address_item->address = $default_address['address'];
		$address_item->zipcode = $default_address['zipcode'];
		$address_item->area = $default_address['area'];
		$address_item->town = $default_address['town_description'];
		$address_item->country = $default_address['countryName'];
		
		$address_html = $address_item->get_element();
		DOM::append($inner_container, $address_html);
	}
	
	// Get all person addresses
	$dbq = new dbQuery("120297203", "profile.person");
	
	// My address list header
	$hd = mlgContent::get_moduleLiteral($policyCode, "lbl_addressList");
	$hd_element = heading::get($hd, 3);
	DOM::append($inner_container, $hd_element);
	
	// Address list
	$addressList = DOM::create("div");
	DOM::append($inner_container, $addressList);
	$addressCounter = 0;
	while ($address = $dbc->fetch($result))
	{
		// If address is default (and shown before),
		//hide it and prevent from deleting it
		if ($default_address_id == $address['address_id'])
			continue;
			
		// Outer Container
		$address_container = DOM::create();
		DOM::append($addressList, $address_container);
		
		$address_item = new personAddress();
		$address_item->description = $address['description'];
		$address_item->address = $address['address'];
		$address_item->zipcode = $address['zipcode'];
		$address_item->area = $address['area'];
		$address_item->town = $address['town_description'];
		$address_item->country = $address['countryName'];
		
		$address_html = $address_item->get_element();
		DOM::append($address_container, $address_html);
		
		// Action Container
		$action_container = DOM::create("div", "", "", "actions");
		layoutContainer::add_margin($dom_builder, $action_container, "", "m");
		DOM::append($address_container, $action_container);
		
		//_____ Delete
		$title = mlgContent::get_moduleLiteral($policyCode, "lbl_deleteAddress");
		$action = DOM::create("a");
		DOM::attr($action, "href", "#");
		DOM::append($action, $title);
		DOM::append($action_container, $action);

		//_____ delete action
		ascop::add_actionPOST($action, $policyCode, "addressManager");
		//_____ delete attributes
		$attr = array();
		$attr['act'] = 'del';
		$attr['address_id'] = $address['address_id'];
		ascop::add_asyncATTR($action, $attr);
		
		$bullet = DOM::create("span", " • ");
		DOM::append($action_container, $bullet);
		
		//_____ Make default
		$title = mlgContent::get_moduleLiteral($policyCode, "lbl_makeDefaultAddress");
		$action = DOM::create("a");
		DOM::attr($action, "href", "#");
		DOM::append($action, $title);
		DOM::append($action_container, $action);
		
		//_____ make default action
		ascop::add_actionPOST($action, $policyCode, "addressManager");
		//_____ make default attributes
		$attr = array();
		$attr['act'] = 'mkdef';
		$attr['address_id'] = $address['address_id'];
		ascop::add_asyncATTR($action, $attr);
		
		$addressCounter++;
	}
	if ($addressCounter == 0)
	{
		$absent_address_message = DOM::create("p");
		DOM::append($inner_container, $absent_address_message);
		
		$messageHolder = DOM::create("i");
		$message = mlgContent::get_moduleLiteral($policyCode, "lbl_absentAddresses");
		DOM::append($messageHolder, $message);
		DOM::append($absent_address_message, $messageHolder);
	}
}

// Build Create new Query module component action
$createQueryForm = new simpleForm();

$title = mlgContent::get_moduleLiteral($policyCode, "lbl_newAddress");
$btn_createQuery = $createQueryForm->get_actionButton($title);
ascop::add_actionGET($btn_createQuery, $policyCode, "addNewAddress");
DOM::append($inner_container, $btn_createQuery);

report::clear();
report::add_content($container, $holder);
return report::get();
//#section_end#
?>