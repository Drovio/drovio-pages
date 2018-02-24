<?php
//#section#[header]
// Module Declaration
$moduleID = 160;

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
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \API\Security\account;
use \UI\Html\HTMLContent;
use \UI\Forms\templates\simpleform;

// Initialize
$content = new HTMLContent();
$content->build("myEmailManager", "emailManager");

// Get person's information
$dbc = new interDbConnection();
$q = new dbQuery("1921568048", "profile.person");
$attr = array();
$attr['pid'] = account::getPersonID();
$result = $dbc->execute($q, $attr);
$person = $dbc->fetch($result);

// Header
$headerContent = moduleLiteral::get($moduleID, "lbl_emailManagerHeader");
$header = DOM::create("h4");
DOM::append($header, $headerContent);
$content->append($header);


// Current email
$emailContent = moduleLiteral::get($moduleID, "lbl_emailManager_currentEmail");
$emailHeader = DOM::create("p", " : ");
DOM::prepend($emailHeader, $emailContent);
DOM::append($emailHeader, DOM::create("b", $person['mail']));
$content->append($emailHeader);

// Return output
return $content->getReport($reportHolder);
//#section_end#
?>