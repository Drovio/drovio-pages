<?php
//#section#[header]
// Module Declaration
$moduleID = 94;

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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Prototype");
//#section_end#
//#section#[code]
use \API\Resources\literals\literal;
use \API\Resources\literals\moduleLiteral;
use \API\Profile\user;
use \API\Security\privileges;
use \UI\Html\HTMLModulePage;
use \UI\Presentation\gridView;
use \UI\Presentation\userConnectControls;
use \UI\Presentation\layoutContainer;
use \UI\Navigation\simpleMenu;

use \UI\Html\pageComponents\htmlComponents\weblink; 
use \API\Resources\url;



//May not
//use \ESS\Protocol\client\environment\Url;
use \ESS\Prototype\html\ModuleContainerPrototype;

//$profile = user::profile();
//if (!is_null($profile))
//{
//	//Is user an active ebuilder user
//	$ebuilderUser = privileges::get_userToGroup("DEVELOPER");
//	$ebuilderUser = FALSE;
//	if($ebuilderUser)
//	{
//		//Redirect
//		// If user is already logged in and has an active ebuilder subscription go to userHome		
//		report::clear();		
//		//Module
//		$module = new ModuleContainerPrototype("42");
//		//$module->set_attributes(TRUE, TRUE, FALSE, array());
//		$module->build();
//		$module_element = $module->get();
//		
//		ServerReport::addContent($module_element, "data", "#pageContainer");
//		return ServerReport::get();		
//	}
//}


// Create Module Page
$page = new HTMLModulePage("freeLayout");
// Build the module
$page->build();

//________________________________________
$mainHead = DOM::create("div","","ebuilderPublicHomePromoBanner_mainHead ", "uiMainContent_head");
$page->appendToSection("main", $mainHead);
$mainHeadContent = DOM::create("div","","ebuilderPublicHomePromoBanner_mainHeadContent", "uiMainContent_headContent");
DOM::append($mainHead, $mainHeadContent);
$mainHeadLogo = DOM::create("div","","ebuilderPublicHomePromoBanner_mainHeadLogo","");
DOM::append($mainHeadContent, $mainHeadLogo);
$mainHeadLeft = DOM::create("div","","ebuilderPublicHomePromoBanner_mainHeadLeft","");
DOM::append($mainHeadContent,$mainHeadLeft); 
$mainHeadRight = DOM::create("div","","ebuilderPublicHomePromoBanner_mainHeadRight","");
DOM::append($mainHeadContent, $mainHeadRight);
    
$mainBody = DOM::create("div","", "ebuilderPublicHomePromoBanner_mainBody","uiMainContent_body");
$page->appendToSection("main", $mainBody);
$quoteContent = DOM::create("div","","ebuilderPublicHomePromoBanner_quoteContent","uiMainContent_bodyContent");
DOM::append($mainBody, $quoteContent);
$mainBodyContent = DOM::create("div","","ebuilderPublicHomePromoBanner_mainBodyContent","uiMainContent_bodyContent");
DOM::append($mainBody, $mainBodyContent);


//_______Build Page Head
// Build Head Logo
$headLogo = DOM::create("div", "", "", "headLogo");
DOM::append($mainHeadLogo, $headLogo);
// Logo Image
$logoImg = DOM::create("img");
DOM::attr($logoImg, "src", "/Library/Media/images/logos/medium/RB_eBuilder_logo_medium.svg");
DOM::append($headLogo, $logoImg);

// Build Head Left
$headLeft = DOM::create("div", "", "", "pageHead");
DOM::append($mainHeadLeft, $headLeft );


// Build Head Right
//Build Head Title
$headTitle= DOM::create("div", "", "", "headTitle");
DOM::append($mainHeadRight, $headTitle);

//Title Text
$quotesWrapper = DOM::create("h1", "", "", "ebuilderTitle");
layoutContainer::textAlign($quotesWrapper , "center");
DOM::append($headTitle, $quotesWrapper);

$quote = moduleLiteral::get($moduleID, "lbl_ebuilderPromoMainQuote");
DOM::append($quotesWrapper, $quote);


//Build Head Main Text
$headRight = DOM::create("div", "", "", "headMainText");
DOM::append($mainHeadRight, $headRight );

//Text Whapper
$subquotesWrapper = DOM::create("p", "", "", "headWhite");
DOM::append($headRight , $subquotesWrapper);

$quote = moduleLiteral::get($moduleID, "prmt_promoQuote");
DOM::append($subquotesWrapper, $quote);


//Build Head Subtitle
$headSubtitle= DOM::create("div", "", "", "headSubtitle");
DOM::append($mainHeadRight, $headSubtitle);

$openingDateWrapper = DOM::create("h3", "", "", "oepningDate ebuilderTitle");
DOM::append($headSubtitle, $openingDateWrapper);

$openingDate = literal::get("global.temp", "lbl_openingDate");
DOM::append($openingDateWrapper, $openingDate);


//____Build main Content
//Registration Offer
$registrationOfferWrapper = DOM::create("div", "", "", "offerText");
DOM::append($quoteContent,$registrationOfferWrapper );

$registrationOffer = moduleLiteral::get($moduleID, "prmt_registrationOffer");
DOM::append($registrationOfferWrapper , $registrationOffer );
	
	
	
if(TRUE)
//<-- #Show eBuilder NON substription Owner Conrtent
{
	// User Controls Tile
	$userControlsWrapper = DOM::create("div", "", "", "userControlsWrapper");
	$page->appendToSection("quoteContent",$userControlsWrapper );	
	$userControls = userConnectControls::get();
	DOM::append($userControlsWrapper , $userControls );
}
//--> #Show NON substription Owner Conrtent
else
//<-- #Show substription Owner Conrtent
{	
	// User Controls Tile
	$userControlsWrapper = DOM::create("div", "", "", "userControlsWrapper");
	DOM::append($pageHead, $userControlsWrapper);
	
	$prmt_userPacketsWrapper = DOM::create("h2", "", "", "ebuilderTitle");
	DOM::append($userControlsWrapper, $prmt_userPacketsWrapper);
	
	$prmt_userPackets = moduleLiteral::get($moduleID, "prmt_userPackets");
	DOM::append($prmt_userPacketsWrapper , $prmt_userPackets);
}
//--> #Show substription Owner Conrtent


// Create Grid View
$gridView = new gridView();
$gridViewElement = $gridView->create(3, 1);
DOM::append($mainBodyContent, $gridViewElement);


// Set Grid View Columnts
//<-- #Col 0 - For User
	$forUser = DOM::create('div', '', '', 'gridCellContentWrapper');
	//Create Header
	$forUser_HeaderWrapper = DOM::create('div','', '', "title");
	$forUser_Header =  moduleLiteral::get($moduleID, "features_forUser_hd");
	DOM::append($forUser_HeaderWrapper, $forUser_Header );
	DOM::append($forUser, $forUser_HeaderWrapper);
	//Create Content
	$forUser_ContentWrapper = DOM::create('div');
	layoutContainer::textAlign($forUser_ContentWrapper , "justify");
	layoutContainer::padding($forUser_ContentWrapper , "l", "s");
	layoutContainer::padding($forUser_ContentWrapper , "r", "s");	
	$forUser_Content = moduleLiteral::get($moduleID, "features_forUser_content");
	DOM::append($forUser_ContentWrapper , $forUser_Content );	
	DOM::append($forUser, $forUser_ContentWrapper );
	// More Info Link
	$linkWrapper = DOM::create('div','', '', "linkWrapper");
	$navigationUrl = url::resolve("ebuilder", "/home.php", FALSE);
	$content = moduleLiteral::get($moduleID, "lbl_learnMore");
	$item = weblink::get($navigationUrl, "_target", $content);
	DOM::append($linkWrapper , $item);
	DOM::append($forUser, $linkWrapper);	
	$gridView->set_content($forUser, 0, 0);	
//--> #Col 0 - For User

//<-- #Col 1 - For Company
	$forCompany = DOM::create('div', '', '', 'gridCellContentWrapper');
	//Create Header
	$forCompany_HeaderWrapper = DOM::create('div','', '', "title");
	$forCompany_Header =  moduleLiteral::get($moduleID, "features_forCompany_hd");
	DOM::append($forCompany_HeaderWrapper, $forCompany_Header );
	DOM::append($forCompany, $forCompany_HeaderWrapper);
	//Create Content
	$forCompany_ContentWrapper = DOM::create('div');
	layoutContainer::textAlign($forCompany_ContentWrapper , "justify");
	layoutContainer::padding($forCompany_ContentWrapper , "l", "s");
	layoutContainer::padding($forCompany_ContentWrapper , "r", "s");
	$forCompany_Content = moduleLiteral::get($moduleID, "features_forCompany_content");
	DOM::append($forCompany_ContentWrapper , $forCompany_Content );	
	DOM::append($forCompany, $forCompany_ContentWrapper );
	// More Info Link
	$linkWrapper = DOM::create('div','', '', "linkWrapper");
	$navigationUrl = url::resolve("ebuilder", "/home.php", FALSE);
	$content = moduleLiteral::get($moduleID, "lbl_learnMore");
	$item = weblink::get($navigationUrl, "_target", $content);
	DOM::append($linkWrapper , $item);
	DOM::append($forCompany, $linkWrapper);
	$gridView->set_content($forCompany, 0, 1);
//--> #Col 1 - For Company

//<-- #Col 2 - For Developer
	$forDeveloper = DOM::create('div', '', '', 'gridCellContentWrapper');
	//Create Header
	$forDeveloper_HeaderWrapper = DOM::create('div','', '', "title");
	$forDeveloper_Header =  moduleLiteral::get($moduleID, "features_forDeveloper_hd");
	DOM::append($forDeveloper_HeaderWrapper, $forDeveloper_Header );
	DOM::append($forDeveloper, $forDeveloper_HeaderWrapper);
	//Create Content
	$forDeveloper_ContentWrapper = DOM::create('div');
	layoutContainer::textAlign($forDeveloper_ContentWrapper , "justify");
	layoutContainer::padding($forDeveloper_ContentWrapper , "l", "s");
	layoutContainer::padding($forDeveloper_ContentWrapper , "r", "s");	
	$forDeveloper_Content = moduleLiteral::get($moduleID, "features_forDeveloper_content");
	DOM::append($forDeveloper_ContentWrapper , $forDeveloper_Content );	
	DOM::append($forDeveloper, $forDeveloper_ContentWrapper );
	// More Info Link
	$linkWrapper = DOM::create('div','', '', "linkWrapper");
	$navigationUrl = url::resolve("developer", "/ebuilder/index.php", FALSE);
	$content = moduleLiteral::get($moduleID, "lbl_learnMore");
	$item = weblink::get($navigationUrl, "_target", $content);
	DOM::append($linkWrapper , $item);
	DOM::append($forDeveloper, $linkWrapper);
	$gridView->set_content($forDeveloper, 0, 2);
//--> #Col 2 - For Developer

/*

//Additional features section
$adFeaturesSec = DOM::create("div");
layoutContainer::margin($adFeaturesSec , "t", "l");
layoutContainer::margin($adFeaturesSec , "b", "s");
DOM::append($mainBodyContent, $adFeaturesSec );

//<-- #Features Sec - CMS - LeftBox
	//Create element/feature wrapper
	$featureCMS = DOM::create('div','', '', 'featureWrapper');
	DOM::append($adFeaturesSec, $featureCMS );
	//Create Image Wrapper
	$featureCMS_ImageWrapper = DOM::create('div','', '', 'imageWrapper');
	layoutContainer::floatPosition($featureCMS_ImageWrapper , "left");
	DOM::append($featureCMS , $featureCMS_ImageWrapper );
	$imgCMS = DOM::create("img");
	DOM::attr($imgCMS, "src", "/Library/Media/images/pages/ebuilder/cmsIcon_90x90.png");
	DOM::append($featureCMS_ImageWrapper, $imgCMS );	
	//Create Content Wrapper
	$featureCMS_ContentWrapper = DOM::create('div','', '', 'featureContentWrapper');
	layoutContainer::floatPosition($featureCMS_ContentWrapper , "left");
	DOM::append($featureCMS, $featureCMS_ContentWrapper );	
		//Create Header
		$featureCMS_HeaderWrapper = DOM::create('div','', '', 'title');
		$featureCMS_Header =  moduleLiteral::get($moduleID, "featureCMS_hd");
		DOM::append($featureCMS_HeaderWrapper, $featureCMS_Header );
		DOM::append($featureCMS_ContentWrapper, $featureCMS_HeaderWrapper);
		//Create Text
		$featureCMS_TextWrapper = DOM::create('div','', '', 'content');		
		$featureCMS_Text = moduleLiteral::get($moduleID, "featureCMS_content");
		DOM::append($featureCMS_TextWrapper , $featureCMS_Text );	
		DOM::append($featureCMS_ContentWrapper , $featureCMS_TextWrapper );	
//--> #Features Sec - CMS

//<-- #Features Sec - Statistic - Right
	//Create element/feature wrapper
	$featureStatistic = DOM::create('div','', '', 'featureWrapper');
	DOM::append($adFeaturesSec, $featureStatistic );
	//Create Image Wrapper
	$featureStatistic_ImageWrapper = DOM::create('div','', '', 'imageWrapper');
	layoutContainer::floatPosition($featureStatistic_ImageWrapper , "right");
	DOM::append($featureStatistic , $featureStatistic_ImageWrapper );
	$imgStatistic = DOM::create("img");
	DOM::attr($imgStatistic, "src", "/Library/Media/images/pages/ebuilder/statisticsIcon_90x90.png");
	DOM::append($featureStatistic_ImageWrapper, $imgStatistic );	
	//Create Content Wrapper
	$featureStatistic_ContentWrapper= DOM::create('div','', '', 'featureContentWrapper');
	layoutContainer::floatPosition($featureStatistic_ContentWrapper , "right");
	DOM::append($featureStatistic , $featureStatistic_ContentWrapper );	
		//Create Header
		$featureStatistic_HeaderWrapper = DOM::create('div','', '', 'title');
		layoutContainer::textAlign($featureStatistic_HeaderWrapper , "right");		
		$featureStatistic_Header =  moduleLiteral::get($moduleID, "featureStatistics_hd");
		DOM::append($featureStatistic_HeaderWrapper , $featureStatistic_Header );
		DOM::append($featureStatistic_ContentWrapper, $featureStatistic_HeaderWrapper );
		//Create Text
		$Statistic_TextWrapper = DOM::create('div','', '', 'content');
		$Statistic_Text = moduleLiteral::get($moduleID, "featureStatistics_content");
		DOM::append($Statistic_TextWrapper , $Statistic_Text );	
		DOM::append($featureStatistic_ContentWrapper, $Statistic_TextWrapper );
//--> #Features Sec - Statistic

//<-- #Features Sec - SEO - LeftBox
	//Create element/feature wrapper
	$featureSEO = DOM::create('div','', '', 'featureWrapper');
	DOM::append($adFeaturesSec, $featureSEO );
	//Create Image Wrapper
	$featureSEO_ImageWrapper = DOM::create('div','', '', 'imageWrapper');
	layoutContainer::floatPosition($featureSEO_ImageWrapper , "left");
	DOM::append($featureSEO , $featureSEO_ImageWrapper );
	$imgSEO = DOM::create("img");
	DOM::attr($imgSEO, "src", "/Library/Media/images/pages/ebuilder/seoIcon_90x90.png");
	DOM::append($featureSEO_ImageWrapper, $imgSEO );	
	//Create Content Wrapper
	$featureSEO_ContentWrapper = DOM::create('div','', '', 'featureContentWrapper');
	layoutContainer::floatPosition($featureSEO_ContentWrapper , "left");
	DOM::append($featureSEO, $featureSEO_ContentWrapper );	
		//Create Header
		$featureSEO_HeaderWrapper = DOM::create('div','', '', 'title');
		$featureSEO_Header =  moduleLiteral::get($moduleID, "featureSEO_hd");
		DOM::append($featureSEO_HeaderWrapper, $featureSEO_Header );
		DOM::append($featureSEO_ContentWrapper, $featureSEO_HeaderWrapper);
		//Create Text
		$featureSEO_TextWrapper = DOM::create('div','', '', 'content');
		$featureSEO_Text = moduleLiteral::get($moduleID, "featureSEO_content");
		DOM::append($featureSEO_TextWrapper , $featureSEO_Text );	
		DOM::append($featureSEO_ContentWrapper , $featureSEO_TextWrapper );
//--> #Features Sec - SEO

//<-- #Features Sec - Support - Right
	//Create element/feature wrapper
	$featureSupport = DOM::create('div','', '', 'featureWrapper');
	DOM::append($adFeaturesSec, $featureSupport );
	//Create Image Wrapper
	$featureSupport_ImageWrapper = DOM::create('div','', '', 'imageWrapper');
	layoutContainer::floatPosition($featureSupport_ImageWrapper , "right");
	DOM::append($featureSupport , $featureSupport_ImageWrapper );
	$imgSupport = DOM::create("img");
	DOM::attr($imgSupport, "src", "/Library/Media/images/pages/ebuilder/supportIcon_90x90.png");
	DOM::append($featureSupport_ImageWrapper, $imgSupport );	
	//Create Content Wrapper
	$featureSupport_ContentWrapper= DOM::create('div','', '', 'featureContentWrapper');
	layoutContainer::floatPosition($featureSupport_ContentWrapper , "right");
	DOM::append($featureSupport , $featureSupport_ContentWrapper );	
		//Create Header
		$featureSupport_HeaderWrapper = DOM::create('div','', '', 'title');
		layoutContainer::textAlign($featureSupport_HeaderWrapper , "right");
		$featureSupport_Header =  moduleLiteral::get($moduleID, "featureSupport_hd");
		DOM::append($featureSupport_HeaderWrapper , $featureSupport_Header );
		DOM::append($featureSupport_ContentWrapper, $featureSupport_HeaderWrapper );
		//Create Text
		$Support_TextWrapper = DOM::create('div','', '', 'content');
		$Support_Text = moduleLiteral::get($moduleID, "featureSupport_content");
		DOM::append($Support_TextWrapper , $Support_Text );	
		DOM::append($featureSupport_ContentWrapper, $Support_TextWrapper );	
//--> #Features Sec - Support

//<-- #Features Sec - Maintance - LeftBox
	//Create element/feature wrapper
	$featureMaintance = DOM::create('div','', '', 'featureWrapper');
	DOM::append($adFeaturesSec, $featureMaintance );
	//Create Image Wrapper
	$featureMaintance_ImageWrapper = DOM::create('div','', '', 'imageWrapper');
	layoutContainer::floatPosition($featureMaintance_ImageWrapper , "left");
	DOM::append($featureMaintance , $featureMaintance_ImageWrapper );
	$imgMaintance = DOM::create("img");
	DOM::attr($imgMaintance, "src", "/Library/Media/images/pages/ebuilder/maintanceIcon_90x90.png");
	DOM::append($featureMaintance_ImageWrapper, $imgMaintance );	
	//Create Content Wrapper
	$featureMaintance_ContentWrapper = DOM::create('div','', '', 'featureContentWrapper');
	layoutContainer::floatPosition($featureMaintance_ContentWrapper , "left");
	DOM::append($featureMaintance, $featureMaintance_ContentWrapper );	
		//Create Header
		$featureMaintance_HeaderWrapper = DOM::create('div','', '', 'title');
		$featureMaintance_Header =  moduleLiteral::get($moduleID, "featureMaintance_hd");
		DOM::append($featureMaintance_HeaderWrapper, $featureMaintance_Header );
		DOM::append($featureMaintance_ContentWrapper, $featureMaintance_HeaderWrapper);
		//Create Text
		$featureMaintance_TextWrapper = DOM::create('div','', '', 'content');		
		$featureMaintance_Text = moduleLiteral::get($moduleID, "featureMaintance_content");
		DOM::append($featureMaintance_TextWrapper , $featureMaintance_Text );	
		DOM::append($featureMaintance_ContentWrapper , $featureMaintance_TextWrapper );	
//--> #Features Sec - Maintance
*/

// Return output
return $page->getReport();
//#section_end#
?>