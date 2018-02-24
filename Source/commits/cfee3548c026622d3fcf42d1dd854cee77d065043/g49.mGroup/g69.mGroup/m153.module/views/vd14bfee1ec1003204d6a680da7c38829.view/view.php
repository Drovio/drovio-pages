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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Profile\translator as userTranslator;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\translator;
use \API\Security\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formFactory;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Html\HTMLContent;
use \UI\Presentation\layoutContainer;
use \UI\Presentation\notification;

// Database Connection
$dbc = new interDbConnection();

// Create HTML Content
$content = new HTMLContent();
$actionFactory = $content->getActionFactory();
$reportHolder = "";

$literalScope = $_GET['ltScope'];
$literalName = $_GET['ltName'];

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$literalScope = $_POST['ltScope'];
	$literalName = $_POST['ltName'];
	$reportHolder = "#tr_".$literalScope."_".$literalName;
	$reportHolder = str_replace(".", "\\.", $reportHolder);
	
	// Check Action
	if ($_POST['action'] == "translate")
	{
		$translationValue = $_POST['translationValue'];
		$result = translator::translate($literalScope, $literalName, $translationValue);
	}
	else if ($_POST['action'] == "removeTranslation")
	{
		//Remove translation here
	}
	else if ($_POST['action'] == "lock")
	{
		translator::lock($_POST['translationID']);
		
		$content->build("translationLocked", "literalTranslationsLocked");
		$trContent = moduleLiteral::get($moduleID, "lbl_translationLockSuccess");
		$slContent = DOM::create("p", ".");
		DOM::prepend($slContent, $trContent);
		$content->append($slContent);
		return $content->getReport($reportHolder);
	}
}

// Build the module
$content->build("literalTranslations_".$literalName, "literalTranslations");

// Literal description
$dbq = new dbQuery("2003639869", "resources.literals");
$attr = array();
$attr['name'] = $literalName;
$result = $dbc->execute($dbq, $attr);
$row = $dbc->fetch($result);
$desc = $row['description'];

// Literal Description
$literalDescription = DOM::create("div");
$descTitle = moduleLiteral::get($moduleID, "lbl_literalDescription");
DOM::append($literalDescription, $descTitle);
$descContent = DOM::create("span", " : ".$desc);
DOM::append($literalDescription, $descContent);

$content->append($literalDescription);

$hr = DOM::create("hr");
$content->append($hr);

// Translations
$titleContent = moduleLiteral::get($moduleID, "lbl_translationValues");
$trTitle = DOM::create("h4");
DOM::append($trTitle, $titleContent);
$content->append($trTitle);

// Get account id
$accountID = account::getAccountID();

// Get Translation locale
$translatorProfile = userTranslator::profile();
$translationLocale = $translatorProfile['translation_locale'];

// Get Translations
$dbq = new dbQuery("588698109", "resources.literals.translator");
$attr = array();
$attr['scope'] = $literalScope;
$attr['name'] = $literalName;
$attr['locale'] = $translationLocale;
$attr['translator_id'] = $accountID;
$result = $dbc->execute($dbq, $attr);
$translations = $dbc->toFullArray($result);
$translationsResource = $dbc->toArray($result, "id", "value");
$translationsScore = $dbc->toArray($result, "id", "skor");
$tlor_votes = array();
if (count($translations) == 0)
{
	$noTranslationsSpan = moduleLiteral::get($moduleID, "lbl_noTranslations");
	$content->append($noTranslationsSpan);	
} else { 
	// Get translator votes
	$dbq = new dbQuery("759034943", "resources.literals.translator");
	$attr = array();
	$attr['translator_id'] = $accountID;
	$result = $dbc->execute($dbq, $attr);
	$tlor_votes = $dbc->toArray($result, "translation_id", "vote");
}
$translationsContainer = DOM::create("ol", "", "", "translations");
$content->append($translationsContainer);

foreach ($translations as $translation)
{
	//$value = DOM::create("li", $translation['value']);
	//DOM::append($translationsContainer, $value);
	
	$li = DOM::create("li");
	DOM::append($translationsContainer, $li);
	
	$transRow = DOM::create("div", "", "", "translationRow");
	DOM::append($li, $transRow);
	
	$transCtrl = DOM::create("div");
	layoutContainer::floatPosition($transCtrl, "right");
	DOM::append($transRow, $transCtrl);
	
	$voteMap = array( -1 => " negative", 0 => "", 1 => " positive" );
	$voteType = 0;
	if (!empty($tlor_votes[$translation['id']]))
		$voteType = $tlor_votes[$translation['id']];
	
	
	$ctrlInnerWrapper = DOM::create("span", "", "", "ctrlInnerWrapper".$voteMap[$voteType]);
	DOM::append($transCtrl, $ctrlInnerWrapper);
	// Check If translation is mine
	if ($translation['translator_id'] == $accountID)
	{
		// Add Delete Control
		$ctrlDelete = DOM::create("span", "x");
		DOM::append($ctrlInnerWrapper, $ctrlDelete);
		
		// Confirmation popup content
		$removeTranslation = DOM::create("div", "", "", "rmMyTl");
		// Create form
		$popupForm = new formFactory();
		$popupForm->build($moduleID, "literalTranslations");
		// Create warning
		$notification = new notification();
		$notification->build("warning");
		// Warning text
		$removeTlTitle = moduleLiteral::get($moduleID, "warning.removeTranslation");
		$popupForm->append($removeTlTitle);
		// Hidden Action Type
		$input = $popupForm->getInput($type = "hidden", $name = "action", $value = "removeTranslation", $class = "", $autofocus = FALSE);
		$popupForm->append($input);
		// Add submit button
		$title = moduleLiteral::get($moduleID, "lbl_removeTranslation");
		$popupSubmit = $popupForm->getSubmitButton($title);
		$popupForm->append($popupSubmit);
		
		$notification->append($popupForm->get());
		DOM::append($removeTranslation, $notification->get());
		
		$content->append($removeTranslation);
	}
	else
	{
		// Add Voting Controls
		// Positive
		$ctrlPlus = DOM::create("span", "+");
		DOM::append($ctrlInnerWrapper, $ctrlPlus);
		// Attach module action
		$attr = array();
		$attr['vote'] = "1";
		$attr['translation_id'] = $translation['id'];
		$attr['parent'] = "literalTranslations_".$literalName;
		$actionFactory->setModuleAction($ctrlPlus, $moduleID, "translationVotes", "", $attr);
		// Negative
		$ctrlMinus = DOM::create("span", "-");
		DOM::append($ctrlInnerWrapper, $ctrlMinus);
		// Attach module action
		$attr['vote'] = "-1";
		$attr['translation_id'] = $translation['id'];
		$attr['parent'] = "literalTranslations_".$literalName;
		$actionFactory->setModuleAction($ctrlMinus, $moduleID, "translationVotes", "", $attr);
	}	
	
	
	$transCnt = DOM::create("div");
	DOM::append($transRow, $transCnt);
	
	$cnt = DOM::create("div");
	DOM::append($transCnt, $cnt);
	
	$spanWrapper = DOM::create("div");
	DOM::append($cnt, $spanWrapper);
	
	$span = DOM::create("span", $translation['value']);
	DOM::append($spanWrapper, $span);
	
	/*$vote = DOM::create("span", $translation['skor'], "", "tlscore");
	DOM::append($spanWrapper, $vote);*/
}

// Insert a visual separator
$hr = DOM::create("hr");
$content->append($hr);

// Build the lock translation form
$ltForm = new simpleForm();
$lockTranslationForm = $ltForm->build($moduleID, "literalTranslations")->get();
$content->append($lockTranslationForm);

// Be a translator
//lbl_lockTranslation
$titleContent = moduleLiteral::get($moduleID, "lbl_lockTranslation");
$trTitle = DOM::create("h4");
DOM::append($trTitle, $titleContent);
$ltForm->append($trTitle);

// Hidden Action type
$input = $ltForm->getInput($type = "hidden", $name = "action", $value = "lock", $class = "", $autofocus = FALSE);
$ltForm->append($input);

$input = $ltForm->getInput($type = "hidden", $name = "ltScope", $value = $literalScope, $class = "", $autofocus = FALSE);
$ltForm->append($input);

$input = $ltForm->getInput($type = "hidden", $name = "ltName", $value = $literalName, $class = "", $autofocus = FALSE);
$ltForm->append($input);

// Lock Value
$title = moduleLiteral::get($moduleID, "lbl_lockTranslateValue");
$input = $ltForm->getResourceSelect($name = "translationID", $multiple = FALSE, $class = "", $translationsResource, $selectedValue = NULL);
$inputRow = $ltForm->buildRow($title, $input, $required = FALSE, $notes = "");
$ltForm->append($inputRow);

// Insert a visual separator
$hr = DOM::create("hr");
$content->append($hr);

// Build the translation form
$tForm = new simpleForm();
$translationForm = $tForm->build($moduleID, "literalTranslations")->get();
$content->append($translationForm);

// Be a translator
$titleContent = moduleLiteral::get($moduleID, "lbl_translateThisLiteral");
$trTitle = DOM::create("h4");
DOM::append($trTitle, $titleContent);
$tForm->append($trTitle);

// Hidden Literal Scope
$input = $tForm->getInput($type = "hidden", $name = "ltScope", $value = $literalScope, $class = "", $autofocus = FALSE);
$tForm->append($input);

// Hidden Literal Name
$input = $tForm->getInput($type = "hidden", $name = "ltName", $value = $literalName, $class = "", $autofocus = FALSE);
$tForm->append($input);

// Hidden Action Type
$input = $tForm->getInput($type = "hidden", $name = "action", $value = "translate", $class = "", $autofocus = FALSE);
$tForm->append($input);

// Insert Translation input
$title = moduleLiteral::get($moduleID, "lbl_translation");
$input = $tForm->getTextarea($name = "translationValue", $viewDescription, $class = "");
$tForm->insertRow($title, $input, $required = FALSE, $notes = "");


// Return output
return $content->getReport($reportHolder);
//#section_end#
?>