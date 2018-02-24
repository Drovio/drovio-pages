<?php
//#section#[header]
// Module Declaration
$moduleID = 153;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\translator;
use \API\Resources\literals\moduleLiteral;
use \API\Security\account;
use \UI\Presentation\popups\popup;
use \UI\Presentation\notification;
use \UI\Html\HTMLContent;

//if ($_SERVER['REQUEST_METHOD'] == "POST")
//{

	// Database Connection
	$dbc = new interDbConnection();
	
	// Build notification
	$newVote = ($_GET['vote'] == "1" ? TRUE : FALSE);
	$translation_id = $_GET['translation_id'];
	$parent = $_GET['parent'];
	$accountID = account::getAccountID();
	
	// Get translator votes. This could return only the votes for $translation_id.
	$dbq = new dbQuery("759034943", "resources.literals.translator");
	$attr = array();
	$attr['translator_id'] = $accountID;
	$result = $dbc->execute($dbq, $attr);
	$tlor_votes = $dbc->toArray($result, "translation_id", "vote");
	
	$revoke = FALSE;
	// If no old translation, just go on to vote the $newVote.
	if (!empty($tlor_votes[$translation_id])) {
		// Get old translation
		$oldVote = ($tlor_votes[$translation_id] == 1 ? TRUE : FALSE);
		// If same with new, revoke, else go on to vote the $newVote.
		$revoke = ($oldVote == $newVote ? TRUE : FALSE);
	}
	
	// Vote or Revoke
	$success = translator::vote($translation_id, ($revoke ? NULL : $newVote));
	
	/*if ($success && $revoke)
	{	
	}*/
	
	$reportNtf = new notification();
	$reportPopup = new popup();
	
	// Check status
	if ($success)
	{
		$reportNtf->build($type = "success");
		$reportMessage = ( $newVote ? moduleLiteral::get($moduleID, "success.positiveVote") : moduleLiteral::get($moduleID, "success.negativeVote"));
		$reportPopup->timeout(TRUE);
	}
	else
	{
		$reportNtf->build($type = "error");
		$reportMessage = moduleLiteral::get($moduleID, "error.voting");
		$reportPopup->timeout(FALSE);
	}
	$reportNtf->append($reportMessage);
	
	$wrapper = DOM::create("div", "", "", "votingResult");
	DOM::append($wrapper, $reportNtf->get());
	
	// Build popup
	$reportPopup->fade(TRUE);
	$reportPopup->distanceOffset(($newVote ? 0 : 19));
	$reportPopup->position("left", "center");
	$reportPopup->parent($parent);
	$reportPopup->build($wrapper);
	
	// Return Report	
	return $reportPopup->getReport();
//}
//#section_end#
?>