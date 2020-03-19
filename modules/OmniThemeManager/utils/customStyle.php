<?php
require_once(__DIR__."/csscolor.php");

header("Content-type: text/css; charset: UTF-8");

function getContrastColor($hexColor,$mode="hex") {
		$hexColor = str_replace('#', '', $hexColor);
		$color = Csscolor::make($hexColor);
		$ctColor = $color->fg['+2'];
		if($mode=="hex") return '#'.$ctColor;
		else return implode(",", hex2rgb($ctColor)); 
		
        //////////// hexColor RGB
        $R1 = hexdec(substr($hexColor, 1, 2));
        $G1 = hexdec(substr($hexColor, 3, 2));
        $B1 = hexdec(substr($hexColor, 5, 2));

        //////////// Black RGB
        $blackColor = "#000000";
        $R2BlackColor = hexdec(substr($blackColor, 1, 2));
        $G2BlackColor = hexdec(substr($blackColor, 3, 2));
        $B2BlackColor = hexdec(substr($blackColor, 5, 2));

         //////////// Calc contrast ratio
         $L1 = 0.2126 * pow($R1 / 255, 2.2) +
               0.7152 * pow($G1 / 255, 2.2) +
               0.0722 * pow($B1 / 255, 2.2);

        $L2 = 0.2126 * pow($R2BlackColor / 255, 2.2) +
              0.7152 * pow($G2BlackColor / 255, 2.2) +
              0.0722 * pow($B2BlackColor / 255, 2.2);

        $contrastRatio = 0;
        if ($L1 > $L2) {
            $contrastRatio = (int)(($L1 + 0.05) / ($L2 + 0.05));
        } else {
            $contrastRatio = (int)(($L2 + 0.05) / ($L1 + 0.05));
        }

        //////////// If contrast is more than 5, return black color
        if ($contrastRatio > 5) {
	        if($mode=="hex") return '#000000';
			else return "0,0,0";
        } else { //////////// if not, return white color.
            if($mode=="hex") return "#FFFFFF";
			else return "255,255,255";
        }
        
        
        
}

function hex2rgb( $colour ) {
        if ( $colour[0] == '#' ) {
                $colour = substr( $colour, 1 );
        }
        if ( strlen( $colour ) == 6 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
        } elseif ( strlen( $colour ) == 3 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
        } else {
                return false;
        }
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );
        return array( $r, $g, $b );
}

function adjustBrightness($hex, $steps=0) {
	
	// Steps should be between -255 and 255. Negative = darker, positive = lighter	
	if($hex=="#FFFFFF" && $steps==0) $steps = max(-255, min(255, -100));
	elseif($hex=="#000000" && $steps==0) $steps = max(-255, min(255, 100));
	else $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0,min(255,$color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}


$default_params = array(
	"font-name" => "Roboto Condensed",
	"font-zoom" => 0,
	
	"colored-topbar" => true,
	"topbar-color" => "#f5f5f5",
	"topbar-font-color" => "#6b6b6b",
	
	"colored-menu" => true,
	
	"menu-style" => "top-menu-dropdown",
	"menu-color" => "#ffffff",
	"menu-font-color" => "#6b6b6b",
	
	"colored-container" => true,
	"container-color" => "#fafafa",
	
	"field-border-color" => "#dddddd",
	"field-labels-color" => "#fafafa",
	"field-labels-font-color" => "#6f6f6f",
	
	"field-value-color" => "#ffffff",
	"field-value-font-color" => "#444444",
	
	
	"border-radius" => 0,
);

$styleid="";
if(isset($_REQUEST["cs"])) $styleid = urldecode($_REQUEST["cs"]);
    
        
$avStyles = json_decode(file_get_contents(__DIR__."/stylePresets.json"),true);
$avDefaultStyles = json_decode(file_get_contents(__DIR__."/stylePresetsMYC.json"),true);

$params = array();
foreach($avDefaultStyles as $stylePresetKey => $presets)
	if($styleid==$stylePresetKey)  $params = $presets;
	//elseif($presets["isApplied"]) $params = $presets;
foreach($avStyles as $stylePresetKey => $presets)
	if($styleid==$stylePresetKey)  $params = $presets;
	
if(isset($_REQUEST["tp"])){
	$params = json_decode(base64_decode(urldecode($_REQUEST["tp"])),true);
}
$params = array_merge($default_params,$params);

$google_font = $params["font-name"];
$font_zoom = 100+$params["font-zoom"];

$main_bg_color = $params["topbar-color"];
$text_contrast = getContrastColor($main_bg_color);
$text_contrast_rgb = getContrastColor($main_bg_color,"rgb");

$text_contrast = $params["topbar-font-color"];
$text_contrast_rgb = implode(",", hex2rgb($text_contrast));


$menu_color = $params["menu-color"];
$text_contrast_menu = getContrastColor($menu_color);
$text_contrast_menu_rgb = getContrastColor($menu_color,"rgb");
$text_contrast_menu_hover = adjustBrightness($text_contrast_menu);

$text_contrast_menu = $params["menu-font-color"];
$text_contrast_menu_rgb = implode(",", hex2rgb($text_contrast_menu));
$text_contrast_menu_hover = adjustBrightness($text_contrast_menu);

$container_color = $params["container-color"];
$text_contrast_container = getContrastColor($container_color);
$text_contrast_container_rgb = getContrastColor($container_color,"rgb");
$text_contrast_container_hover = adjustBrightness($text_contrast_container);

$border_radius = intval($params["border-radius"]);

$flat_theme = false;
if(isset($params["menu-style"])) $menu_type = $params["menu-style"];
else $menu_type = "top-menu-dropdown";

$field_label_color = $params["field-labels-color"];
$field_label_font_color = $params["field-labels-font-color"];

?>


@import url('https://fonts.googleapis.com/css?family=<?= urlencode($google_font) ?>');

*:not(i):not(.fa):not(.vicon):not([class^='ti-']):not([class*='ti-']):not([class*='vicon-']), body, html{
	font-family: '<?= $google_font ?>' !important;
}

body, html{
	font-family: '<?= $google_font ?>' !important;
	font-size: <?= $font_zoom ?>%;
}

.module-action-bar .module-title{
	font-family: '<?= $google_font ?>';
	font-weight: bold;
}
<?php if(true): ?>

.ajaxEdited, 
.editElement textarea{
	min-width: 100%;
}
.search-links-container.hidden-sm .keyword-input{
	min-width: 25vw;
}

tbody .ps__rail-y{
	background-color: rgba(<?= $text_contrast_menu_rgb ?>,.9) !important;
	width: 12px;
}
tbody .ps__thumb-y{
	background-color: <?= $menu_color ?> !important;
}


.ps__rail-x{
	background-color: rgba(<?= $text_contrast_menu_rgb ?>,.9) !important;
	height: 12px;
}
.ps__thumb-x{
	background-color: <?= $menu_color ?> !important;
	bottom: 0px;
}
.table-container .ps__rail-x:nth-of-type(1){
	bottom: auto;
}
.table-container .ps__rail-x:nth-of-type(1) .ps__thumb-x{
	top: 0px;
	bottom: auto;
}

.app-fixed-navbar, .search-link .keyword-input{
	background-color: <?= $main_bg_color ?> !important;
}

.app-navigator-container .btn-fask, .search-link .keyword-input, .searchWorkflows, .search-link, #navbar .dropdown-toggle a i[class^="material"], .keyword-input::placeholder, #navbar > ul > li > div > a > i{
	color: <?= $text_contrast ?> !important;
}

.modal-header{
	background-color: <?= $main_bg_color ?> !important;
	color: <?= $text_contrast ?> !important;
}
.modal-content{
	color: <?= $text_contrast ?> !important;
}
.modal-body{
	background-color: white;
	color: black;
}

.modal-content #EditView{
	padding: 0px;
}

#navbar li a{
	color: black;
}

#navbar li a:hover, #navbar li a:hover i, #navbar li a:hover span, #navbar li a:hover, #navbar li .col-lg-4:hover i, #navbar li .col-lg-4:hover .quick-create-module, #navbar li a:hover, #navbar li .col-lg-4:hover i, #navbar li .col-lg-4:hover .quick-create-module{
	color: <?= $text_contrast ?> !important;
}

#navbar li .col-lg-4:hover{
	background-color: <?= $main_bg_color ?>;
	border-radius: 15px;
	color: <?= $text_contrast ?> !important;
}

.search-link{
	border: 1px solid <?= $text_contrast ?> !important;
}

.btn-info{
	background-color: <?= $main_bg_color ?>;
	border-color: rgba(<?= $text_contrast_rgb ?>,.5);
	color: <?= $text_contrast ?>;
}
.btn-info:hover, .btn-info:focus{
	background-color: <?= $text_contrast ?> !important;
	border-color: <?= $main_bg_color ?> !important;
	color: <?= $main_bg_color ?> !important;
	background: <?= $text_contrast ?> !important;
	background-image: none;
}

.table-hover>tbody>tr:not(.emptyRecordsDiv):hover, .table-hover>tbody>tr:not(.emptyRecordsDiv):hover * {
    background-color: <?= $main_bg_color ?> !important;
    color: <?= $text_contrast ?> !important;
}

.table-hover>tbody>tr.emptyRecordsDiv:hover, .table-hover>tbody>tr.emptyRecordsDiv:hover * {
    background-color: #f9f9f9 !important;
    color: black;
}

#searchmobile{
	background-color: <?= $main_bg_color ?> !important;
	border-color: <?= $text_contrast ?>;
	color: <?= $text_contrast ?>;
}

.select2-results .select2-highlighted {
    color: <?= $text_contrast_menu ?>;
    background-color: <?= $menu_color ?>;
}

.input-group-addon, .referencefield-wrapper .createReferenceRecord  {
	color: <?= $text_contrast ?>;
    background-color: <?= $main_bg_color ?>;
}

.quickTopButtons button{
	color: <?= $text_contrast ?> !important;
	border-color: <?= $text_contrast ?> !important;
}
.app-switcher-container .app-navigator .app-icon{
	color: <?= $text_contrast ?> !important;
	opacity: 1;
}
<?php endif; ?>


<?php if(true): ?>

.sidebar-nav ul li a:hover, .sidebar-nav ul li a:hover i, .sidebar-nav ul li a.active {
    color: <?= $menu_color ?>;
    background: <?= $text_contrast_menu ?>;
}

.sidebar-nav{
	background: <?= $menu_color ?>;
	color: <?= $text_contrast_menu ?>;
}

.sidebar-nav ul li a, .sidebar-nav > ul > li > a i, .sidebar-nav > ul > li.active > a i{
	color: <?= $text_contrast_menu ?>;
}
.sidebar-nav .has-arrow::after{
	color: <?= $text_contrast_menu ?>;
	border-color: <?= $text_contrast_menu ?>;
}

.sidebar-nav > ul > li.active > a, .sidebar-nav ul li a.active {
    color: <?= $text_contrast_menu ?>;
    font-weight: bold;
    border-left: 4px solid lightgray;
    background: <?= $menu_color ?>;
}

#sidebarnav li.active > a, #sidebarnav li.active > a i, #sidebarnav li > ul a.active i, #sidebarnav li.active > a.has-arrow::after, .sidebar-nav ul li a.active{
	color: <?= $menu_color ?> !important;
    background: <?= $text_contrast_menu ?> !important;
    font-weight: bold !important;
}

#sidebarnav li.active > a.has-arrow::after{
	border-color: <?= $menu_color ?> !important;
}

.btn-primary{
	background-color: <?= $menu_color ?>;
	border-color: rgba(<?= $text_contrast_menu_rgb ?>,.2);
	color: <?= $text_contrast_menu ?>;
}
.btn-primary:hover, .btn-primary:focus{
	background-color: <?= $text_contrast_menu ?> !important;
	border-color: <?= $menu_color ?> !important;
	color: <?= $menu_color ?> !important;
	background: <?= $text_contrast_menu ?> !important;
	background-image: none;
}

.app-navigator-container .fask{
	background-color: <?= $menu_color ?> !important;
	color: <?= $text_contrast_menu ?> !important;
}

.fasksecond>li.with-childs {
	border-left: 1px solid <?= $text_contrast_menu ?> !important;
}
.app-navigator-container hr{
	border-top: 1px solid <?= $text_contrast_menu ?> !important;
}	


.fasksecond li.with-childs ul a:hover, .faskfirst li a:hover {
    color: <?= $menu_color ?> !important;
}

.fasksecond li.with-childs > a:hover{
    color: <?= $text_contrast_menu ?> !important;
    cursor: default;
}

.dropdown-menu.fask > ul >li > ul > li:hover a, .faskfirst li:hover a {
    padding-left: 10px;
}
.dropdown-menu.fask > ul >li > ul > li:hover , .faskfirst li:hover{
	background-color: <?= $text_contrast_menu ?> !important;
	color: <?= $menu_color ?> !important;
}


.module-action-bar{
	background-color: <?= $menu_color ?> !important;
	color: <?= $text_contrast_menu ?> !important;
}

.module-action-bar .module-title{
	color: <?= $text_contrast_menu ?> !important;
}

.module-action-bar .btn{
	background-color: <?= $menu_color ?>;
	color: <?= $text_contrast_menu ?>;
	border-color: rgba(<?= $text_contrast_menu_rgb ?>,.2);
}

.module-action-bar .btn:hover{
	background-color: <?= $text_contrast_menu ?> !important;
	color: <?= $menu_color ?>;
	border-color: rgba(<?= $text_contrast_menu_rgb ?>,.2);
}


<?php endif; ?>

<?php if(true): ?>

.main-container, .content-area, #page{
	background-color: <?= $container_color ?> !important;
	color: <?= $text_contrast_container ?>;
	
}
.settingsPageDiv{
	color: black;
}
.settingsPageDiv .table, .settingsPageDiv{
	background-color: white !important;
}

.dashBoardContainer .tabContainer, .tabContainer > .nav{
	background-color: <?= $container_color ?> !important;
}
.tabContainer .nav-tabs>li>a{
	color: <?= $text_contrast_container ?> !important;
}

.tabContainer .nav-tabs>li>a:hover{
	color: <?= $text_contrast_container_hover ?> !important;
}
<?php endif; ?>

.blockData .detailview-table{
	margin-bottom: 0px;
}

.detailview-table .fieldValue, .detailview-table .fieldLabel {
    padding-top: 5px;
    padding-bottom: 0px;
    padding-left: 10px;
}

.block{
	padding: 0px !important;
}

#appnav{
	margin-right: 0px;
}
.module-action-bar{
	border-top: 0px solid rgba(<?= $text_contrast_rgb ?>,.5) !important;
	border-bottom: 2px solid rgba(<?= $text_contrast_container_rgb ?>,.5) !important;
	box-shadow: none !important;
}

.dashBoardContainer .dashBoardTabContents ul li {
	border-radius: <?= $border_radius ?>px !important;
}
.btn, .fieldValue > input.inputElement, textarea.inputElement, .search-link, .inputElement.select2-container .select2-choice, .listViewPageDiv .inputElement, input.search-list, .module-buttons.btn, .select2-choices, .select2-container-multi .select2-choices, .table-container, .detailview-content, .editViewContents .fieldBlockContainer, th input.inputElement[type='text'], .modal-content, .input-group, .createReferenceRecord, ul.dropdown-menu, ul.dropdown-menu li a, ul.dropdown-menu li, .select2-container .select2-choice, .popover, .nav-tabs .dropdown-menu{
	border-radius: <?= $border_radius ?>px;
	border-top-right-radius: <?= $border_radius ?>px;
	border-top-left-radius: <?= $border_radius ?>px;
}
.editViewContents .fieldBlockContainer{
	margin-bottom: 15px;
}

.modal-header{
	border-radius: <?= $border_radius ?>px <?= $border_radius ?>px 0px 0px;
}

.modal-footer{
	border-radius: 0px 0px <?= $border_radius ?>px <?= $border_radius ?>px;
}

.dashboardWidgetHeader{
	background: transparent;
}
.related-tabs.row{
	padding-left: 15px;
    padding-right: 15px;
	border-radius: <?= $border_radius ?>px;
	background: <?= $menu_color ?>;
	color: <?= $text_contrast_menu ?>;
}
.related-tabs.row a, .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus{
	color: <?= $text_contrast_menu ?>;
}

#layoutEditorContainer .nav-tabs>li.active{
	background: <?= $menu_color ?>;
}


.related-tabs .nav-tabs>li.active, .related-tabs .nav-tabs>li.active:focus, .dashBoardContainer .nav-tabs>li.active, .dashBoardContainer .nav-tabs>li.active:focus, .contents.tabbable .nav-tabs>li.active, .contents.tabbable .nav-tabs>li.active:focus {
    border: none;
    border-bottom: 2px solid <?= $text_contrast ?>;
}

.row .nav>li>a:hover {
    color: <?= $text_contrast_menu ?>;
    font-weight: bold;
}
.related-tabs .nav>li:hover, .related-tabs .nav-tabs>li.active {
    border-bottom: 2px solid <?= $text_contrast_menu ?>;
}

.related-tabs .nav-tabs>li.active .numberCircle {
    background-color: <?= $text_contrast_menu ?>;
    color: <?= $menu_color ?> !important;
}

.detailViewContainer .block h5 {
    color: <?= $text_contrast ?>;
    background-color: <?= $main_bg_color ?>;
    font-weight: bold;
    margin: 0px;
    padding: 10px;
}

.detailViewContainer .block{
	background: transparent;
}
.detailViewContainer .block .fieldValue, .detailViewContainer .block .row{
	background: white;
}

.detailViewContainer .block .row:last-child{
	border-radius: 0 0 <?= $border_radius ?>px <?= $border_radius ?>px!important;
}

.detailViewContainer .block .row:last-child .fieldValue:last-child {
	border-radius: 0 0 <?= $border_radius ?>px 0!important;
}

.numberCircle, .numberCircle.disabled {
    background: <?= $text_contrast_menu ?>;
    color: <?= $menu_color ?> !important;
}

.inputElement.currencyField{
	    border-radius: 0px <?= $border_radius ?>px <?= $border_radius ?>px 0px;
}
.referencefield-wrapper .inputElement, 
.input-group > :first-child{
	border-radius: <?= $border_radius ?>px 0px 0px <?= $border_radius ?>px !important;
}
.input-group :last-child{
	border-radius: 0px <?= $border_radius ?>px <?= $border_radius ?>px 0px !important;
}

.input-save-wrap :first-child{
	border-radius: 0px !important;
}

.inputElement[type='checkbox']{
	border-radius: 0px !important;
}
.lists-menu > li.active{
	border-radius: <?= $border_radius ?>px;
	padding-left: 15px;
	padding-right: 15px;
}

.lists-menu > li.active > div{
	margin-top: 5px;
}

.input-group-btn .color-dropdown{
	border-radius: 0px <?= $border_radius ?>px <?= $border_radius ?>px 0px !important;
}
.color-preview{
	border-radius: 50px !important;
}

.resizable-summary-view, .detailview-header-block, .recentActivitiesContainer{
	border-radius: <?= $border_radius ?>px;
}

.detailview-content .details.row .relatedHeader {
    border: 1px solid #F3F3F3;
    border-radius: <?= $border_radius ?>px <?= $border_radius ?>px 0px 0px !important;
}
.detailview-content .details.row .relatedContents{
	border-radius: 0 0 <?= $border_radius ?>px <?= $border_radius ?>px!important;
}

.detailview-table .row:last-child .fieldLabel:first-child{
	border-radius: 0 0 0 <?= $border_radius ?>px!important;
	border-bottom: 0px !important;
}

.detailview-table .row:last-child .fieldValue{
	border-bottom: 0px !important;
}
.detailview-table .row:first-child .fieldValue, .detailview-table .row:first-child .fieldLabel{
    border-top: 1px solid #ddd;
}

#detailView .block > div > h5{
	border-radius: <?= $border_radius ?>px <?= $border_radius ?>px 0px 0px !important;
}

#detailView .closedBlock > div > h5{
	border-radius: <?= $border_radius ?>px !important;
}

.relatedContents .bottomscroll-div{
	margin-bottom: 15px;
}

.fasksecond ul{
	padding-right: 5px;
}

.detailview-content {
    background: transparent;
    box-shadow: none;
}
.detailViewContainer .block {
	margin: 0px;
	margin-bottom: 10px;
	border-radius: <?= $border_radius ?>px!important;
}
.fieldLabel, .fieldValue{
	min-height: 40px;
}

.commentContainer{
	padding-top: 15px;
	padding-bottom: 15px;
	border-radius: <?= $border_radius ?>px;
}

.listViewEntries td img{
	border-radius: <?= $border_radius ?>px !important; 
}

.userName img{
	border-radius: <?php if($border_radius>=25) echo "50"; else echo $border_radius; ?>% !important;
}


 .detailViewContainer .recordImage, .detailViewContainer .recordImage img{
	border-radius: <?php if($border_radius>=25) echo "50"; else echo $border_radius; ?>px !important; 
	border: 1px;
}

.detailViewContainer .commentContainer .commentTitle.row, .detailViewContainer .commentContainer .showcomments {
    background: transparent;
}

.recentActivitiesContainer{
	padding-bottom: 60px;
	background: white;
}

.resizable-summary-view, .commentsRelatedContainer{
	background: white;
}
.block > div > h5{
	width: 100% !important;
}

.editViewContents .fieldValue{
	margin-bottom: 15px !important;
}

#appnav  button{
	margin-top: 0px;
	height: 41px;
	border-top: 0px;
	border-bottom: 0px;
	border-radius: 0px;
	font-weight: bold;
}

.module-action-bar .module-breadcrumb .module-title {
    max-width: 20vw;
}
.commentActionsContainer, .commentTime, .creatorName{
	font-size: 75%;
}

.select2-container-active .select2-choice, .select2-container-multi.select2-container-active .select2-choices {
    border-color: black;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(0, 0, 0, 0.6);
}
.select2-container-active .select2-choice, .select2-container-active .select2-choices {
    border: 1px solid black;
}    

.select2-drop-active {
    border-color: black;
}
.select2-dropdown-open.select2-drop-above .select2-choice, .select2-dropdown-open.select2-drop-above .select2-choices {
    border: 1px solid black;
}

.select2-drop-auto-width, .select2-drop.select2-drop-above.select2-drop-active {
    border-top-color: black;
}
    
@media (max-width: 992px){

#appnav {
    margin-right: -16px;
}

}

.editViewBody .fieldBlockContainer{
	padding: 0px;
    margin: 0px;
}

.editViewBody .fieldBlockHeader{
	width: 100% !important;
	color: <?= $text_contrast ?>;
    background-color: <?= $main_bg_color ?>;
    border-bottom: 2px solid rgba(<?= $text_contrast_rgb ?>,.5) !important;
}

.editViewBody .fieldBlockContainer .table{
	padding: 15px;
    margin-bottom: 0px;
}

.modal-overlay-footer, .modal-footer{
	background: <?= $menu_color ?>;
}

.summaryWidgetContainer, .summaryView{
	background: transparent;
}

.detailview-header-block{
	background: white;
	margin-top: 15px;
	margin-bottom: 15px;
}

.editViewContents .fieldBlockContainer{
	border-bottom: 0px !important;
	margin-bottom: 15px;
}

.editViewContents  .fieldBlockHeader{
	padding-left: 15px;
	font-weight: bold;
}

.recordBasicInfo{
	color: black !important;
}

<?php if(!$flat_theme): ?>
#detailView .block{
	border: 0px;
}
.module-action-bar{
	box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24) !important;
	border-bottom: 0px !important;
}
.sh-effect1, .resizable-summary-view, #detailView .block, .recentActivitiesContainer, .relatedContents, .commentsRelatedContainer, .fieldBlockContainer, .dashBoardTabContents ul li{
    -webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24)!important;
       -moz-box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24)!important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24)!important;
}
<?php endif; ?>

.filterConditionsDiv, .reports-content-area{
	color: black;
}

<?php if($menu_type == "sidebar-menu"): ?>
.app-navigator-container > div > .dropdown{
	display: none !important;
}
#appnavigator{
	display: inline-block !important;
}
.left-sidebar{
	width: 0px;
	display: block !important;
}
<?php endif; ?>


.detailview-table .fieldLabel, #detailView .summaryView .fieldLabel{
	opacity: 1;
	background: <?= $field_label_color ?>;
	color: <?= $field_label_font_color ?>;
	border-bottom: 1px solid <?= $params["field-border-color"] ?>;
}
.detailview-table .row:first-child .fieldValue, .detailview-table .row:first-child .fieldLabel {
    border-top: 1px solid <?= $params["field-border-color"] ?>;
}
.detailview-table .fieldValue, .detailview-table .fieldLabel, #detailView .summaryView .fieldValue{
	border-bottom: 1px solid <?= $params["field-border-color"] ?>;
}

.fieldLabel label, .fieldLabel .muted{
	color: <?= $field_label_font_color ?>;
}

.massEditTable .fieldLabel label, .massEditTable .fieldLabel .muted{
	color: #2c3b49;
}

.detailViewContainer .block .fieldValue, .detailViewContainer .block .row, #detailView .summaryView .fieldValue{
	background: <?= $params["field-value-color"] ?>;
	color: <?= $params["field-value-font-color"] ?>;
}
.fieldValue .value a{
	color: <?= $params["field-value-font-color"] ?>;
	text-decoration: underline !important;
	font-weight: bold;
}

.listViewEntryValue .fieldValue .value a{
	color: black;
	text-decoration: none !important;
}

#messageBar > div{
	top: 40% !important;
	left: auto !important;
	width: 100%;
}

#messageBar > divimg{
	margin: auto;
}

.lineItemTable{
	background-color: white;
	color: black;
}
.fieldBlockHeader{
	margin-top: 0px!important;
}
.editViewBody .fieldBlockContainer > hr{
	display: none
}
.editViewBody .fieldBlockContainer{
	color: black;
}
.notification_bell:before, .notification_bell:after{
	color: <?= $text_contrast ?> !important;
}