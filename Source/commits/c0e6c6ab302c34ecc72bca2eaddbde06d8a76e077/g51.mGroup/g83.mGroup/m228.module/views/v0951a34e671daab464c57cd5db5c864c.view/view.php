<?php
//#section#[header]
// Module Declaration
$moduleID = 228;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Content");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Content\diff;	
use \UI\Html\HTMLContent;
use \UI\Forms\templates\simpleForm;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$before = isset($_POST['before']) ? $_POST['before'] : 'Some text before';
	$after = isset($_POST['after']) ? $_POST['after'] : 'This is the text after';
	$mode = (isset($_POST['mode']) ? $_POST['mode'] : diff::MODE_WORD);

	$diff = new diff();
	$diff->init($before, $after);
	
	$generatePatch = TRUE;	
	$difference = $diff->diff($mode, $generatePatch);
	
	//print_r($difference);
	
	if(!is_null($difference)) 
	{
		$after_patch = $diff->patch($difference);
		$html = diffToHtml($before, $after, $difference);
		if(!is_null($html) && !is_null($after_patch))
		{
			$differenceDiv = DOM::create();
			$title = DOM::create('span', 'Difference');
			DOM::append($differenceDiv, $title);
			$content = $html;
			$frameResults = DOM::create('div', $content, '', 'frameResults');
			DOM::append($differenceDiv, $frameResults);
			
			$patchDiv = DOM::create();
			$title = DOM::create('span', 'Patch');
			DOM::append($patchDiv, $title);
			$content = ($after === $after_patch ? 'OK: The patched text matches the text after.' : 'There is a BUG: The patched text (<b>'.HtmlSpecialChars($after_patch).'</b>) does not match the text after (<b>'.HtmlSpecialChars($after).'</b>).');
			$frameResults = DOM::create('div', $content, '', 'frameResults');
			DOM::append($patchDiv, $frameResults);		
			
		}
		else
		{
			$errorDiv = DOM::create();
			$title = DOM::create('span', 'Difference');
			DOM::append($errorDiv, $title);
			$content = HtmlSpecialChars($diff->error);
			$frameResults = DOM::create('div', $content, '', 'frameResults');
			DOM::append($errorDiv, $frameResults);
		
		}
	}
	else
	{
	//error
	}
}


// Create Module Page
$HTMLContent = new HTMLContent();

// Create form
$sForm = new simpleForm();
$sForm->build($moduleID, "", $controls = TRUE);
$HTMLContent->buildElement($sForm->get());

$area = DOM::create();
$title = DOM::create('span', 'before');
DOM::append($area, $title);
$input = $sForm->getTextarea($name = "before", HtmlSpecialChars($before), $class = "", $autofocus = FALSE);
DOM::append($area, $input);
$sForm->append($area);

$area = DOM::create();
$title = DOM::create('span', 'after');
DOM::append($area, $title);
$input = $sForm->getTextarea($name = "after", HtmlSpecialChars($after), $class = "", $autofocus = FALSE);
DOM::append($area, $input);
$sForm->append($area);

$area = DOM::create();
$title = DOM::create('span', 'compare by');
DOM::append($area, $title);
$resource = array();
$resource[diff::MODE_CHAR] = 'Character';
$resource[diff::MODE_WORD] = 'Word';
$resource[diff::MODE_LINE] = 'Line';
$input = $sForm->getResourceSelect($name = "mode", $multiple = FALSE, $class = "", $resource, $selectedValue = $mode);
DOM::append($area, $input);
$sForm->append($area);

if(!is_null($differenceDiv)) 
	$HTMLContent->append($differenceDiv);
if(!is_null($patchDiv))
	$HTMLContent->append($patchDiv);
if(!is_null($errorDiv))
	$HTMLContent->append($errorDiv);	

// Return output
return $HTMLContent->getReport();


/**
 * Using difference array, creates a grafical representation (in html) of the changes interrelating the two versions of the text.
 * 
 * @param	{type}	$before
 * 		{description}
 * 
 * @param	{type}	$after
 * 		{description}
 *
 * @param	array	$difference
 * 		An array of arrays (a list of changes), each one containing information about a change.
 * 
 * @return	DOMElement
 * 		{description}
 */
function diffToHtml($before, $after, $difference)
{
	$html = DOM::create(); 
	$changes = count($difference);
	foreach($difference as $diff)
	{
		switch($diff['change'])
		{
			case '=':
				$content = nl2br(HtmlSpecialChars(substr($before, $diff['position'], $diff['length'])));
				$text = DOM::create('span', $content);
				DOM::append($html, $text);
				break;
			case '-':
				$content = nl2br(HtmlSpecialChars(substr($before, $diff['position'], $diff['length'])));
				$text = DOM::create('del', $content, '', 'deleted');
				DOM::append($html, $text);
				break;
			case '+':
				$content = nl2br(HtmlSpecialChars(substr($after, $diff['position'], $diff['length'])));
				$text = DOM::create('ins', $content, '', 'inserted');
				DOM::append($html, $text);
				break;
			default:
				$error = $diff['change'].' is not an expected difference change type';
				return null;
		} 
	}
	return $html;
}
//#section_end#
?>