<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class CustomStyler_CustomStyle_View extends Vtiger_IndexAjax_View {
    
	function __construct() {
		
		parent::__construct();
		
		$this->exposeMethod('getCSSForCurrentUser');
		
		$this->exposeMethod('getCSSStyle');
		$this->exposeMethod('getStylePresets');
		
	}
	
	function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}
	
	function getCSSStyle(Vtiger_Request $request){
	    
	    $tp = $request->get("tp");
	    
	    header("Content-type: text/css; charset: UTF-8");
	    
	    $data = base64_decode($tp);
	    
	    $variables = json_decode($data, true);
	    
	    if($variables['theme-name'] != 'Default'){
	        echo $this->getCSS($variables);
	    }
	    
	
	}
	
    function getCSSForCurrentUser(Vtiger_Request $request) {
		
		global $adb, $current_user;
		
        header("Content-type: text/css; charset: UTF-8");
        $variables = array();
		
		$result = $adb->pquery("select * from vtiger_customstyler_current_user_style 
	    INNER JOIN vtiger_customstyler ON vtiger_customstyler.stylerid = vtiger_customstyler_current_user_style.style
		where userid = ?", array($current_user->id));
		
		if($adb->num_rows($result)){
			if($adb->query_result($result, 0, "theme_name") != 'Default'){
				echo $this->getCSS($adb->query_result_rowdata($result));
			}
		}/*else{
		    echo $this->getCSS($variables);
		}*/
	
	}
    
    
    function getCSS($variables){
        
        if(isset($variables['border_radius'])){
            $border_radius = $variables['border_radius'];
        }else if(isset($variables['border-radius'])){
            $border_radius = $variables['border-radius'];
        } else {
            $border_radius = 0; //$variables['border-radius'];
        }
        
        if(isset($variables['menu_font_color'])){
            $menu_color = $variables['menu_font_color'];
        }else if(isset($variables['menu-font-color'])){
            $menu_color = $variables['menu-font-color'];
        } else {
            $menu_color = "#6b6b6b";
        }
        
        if(isset($variables['menu_active_font_color'])){
            $menu_active_color = $variables['menu_active_font_color'];
        }else if(isset($variables['menu-active-font-color'])){
            $menu_active_color = $variables['menu-active-font-color'];
        } else {
            $menu_active_color = "#000000";
        }
        
        if(isset($variables['topbar_color'])){
            $topbar_color = $variables['topbar_color'];
        }else if(isset($variables['topbar-color'])){
            $topbar_color = $variables['topbar-color'];
        }else {
            $topbar_color = "#f5f5f5";
        }
        
        if(isset($variables['topbar_font_color'])){
            $topbar_font_color = $variables['topbar_font_color'];
        }else if(isset($variables['topbar-font-color'])){
            $topbar_font_color = $variables['topbar-font-color'];
        }else {
            $topbar_font_color = "#6b6b6b";
        }
        
        if(isset($variables['topbar_color'])){
            $topbar_color = $variables['topbar_color'];
        } else if(isset($variables['topbar-color'])){
            $topbar_color = $variables['topbar-color'];
        } else {
            $topbar_color = "#f5f5f5";
        }
        
        if(isset($variables['container_color'])){
            $container_color = $variables['container_color'];
        }else if(isset($variables['container-color'])){
            $container_color = $variables['container-color'];
        }else {
            $container_color = "#fafafa";
        }
        
        if(isset($variables['menu_color'])){
            $menuColor = $variables['menu_color'];
        }else if(isset($variables['menu-color'])){
            $menuColor = $variables['menu-color'];
        }else{
            $menuColor = "#ffffff";
        }
        
        if(isset($variables['font_name'])){
            $fontName = $variables['font_name'];
        }else if(isset($variables['font-name'])){
            $fontName = $variables['font-name'];
        }else{
            $fontName = "Open Sans";
        }
        
        if(isset($variables['field_border_color'])){
            $fieldBorder = $variables['field_border_color'];
        }else if(isset($variables['field-border-color'])){
            $fieldBorder = $variables['field-border-color'];
        }else{
            $fieldBorder = "#F3F3F3";
        }
        
        if(isset($variables['field_value_font_color'])){
            $fieldfontcolor = $variables['field_value_font_color'];
        }else if(isset($variables['field-value-font-color'])){
            $fieldfontcolor = $variables['field-value-font-color'];
        }else{
            $fieldfontcolor = "#444444";
        }
        
        if(isset($variables['field_value_color'])){
            $fieldvalcolor = $variables['field_value_color'];
        }else if(isset($variables['field-value-color'])){
            $fieldvalcolor = $variables['field-value-color'];
        }else{
            $fieldvalcolor = "#ffffff";
        }
        
        if(isset($variables['field_labels_font_color'])){
            $fieldlableFontcolor = $variables['field_labels_font_color'];
        }else if(isset($variables['field-labels-font-color'])){
            $fieldlableFontcolor = $variables['field-labels-font-color'];
        }else{
            $fieldlableFontcolor = "#6f6f6f";
        }
        
        if(isset($variables['field_labels_color'])){
            $fieldlablecolor = $variables['field_labels_color'];
        }else if(isset($variables['field-labels-color'])){
            $fieldlablecolor = $variables['field-labels-color'];
        }else{
            $fieldlablecolor = "#fafafa";
        }
        
        if(isset($variables['font_zoom'])){
            $fontSize = 100 + $variables['font_zoom'];
        }else if(isset($variables['font-zoom'])){
            $fontSize = 100 + $variables['font-zoom'];
        }else{
            $fontSize = "100";
        }
        
        if(isset($variables['custom'])){
            $custom = $variables['custom'];
        }else if(isset($variables['custom'])){
            $custom = $variables['custom'];
        }else{
            $custom = "#3f3f3f";
        }
        
        return "@import url('https://fonts.googleapis.com/css?family=".urlencode($fontName)."');
            *:not(i):not(.fa):not(.vicon):not(.teamworkIcon), body, html{
            	font-family: '".$fontName."' !important;
            }
            *{
            	font-size: ".$fontSize."%;	
            }
            .module-action-bar .module-title{
            	font-family: '".$fontName."';
            	font-weight: bold;
            }
            .userName img{
            	border-radius: ".$border_radius."% !important;
            }
             .detailViewContainer .recordImage, .detailViewContainer .recordImage img{
            	border-radius: ".$border_radius."px !important; 
            	border: 1px;
            }
            .app-switcher-container .app-navigator .app-icon{
            	color: ".$topbar_font_color.";
            	opacity: 1;
            }
            .app-fixed-navbar, .app-fixed-navbar .search-link, .app-fixed-navbar  .keyword-input, .module-action-content, .app-switcher-container .app-navigator{
            	background: ".$topbar_color.";
            	color: ".$topbar_font_color.";
            }
            .app-fixed-navbar .dropdown-menu{
            	color: black;
            }
            .keyword-input::placeholder {
              color: ".$topbar_font_color.";
              opacity: 0.6;
            }
            .search-link{
              border-color: ".$topbar_font_color.";
            }
            .global-nav .app-switcher-container .app-navigator:hover{
            	color: ".$topbar_color.";
            	background: ".$topbar_font_color.";
            }
            .global-nav .app-switcher-container .app-navigator:hover .app-icon{
            	color: ".$topbar_color.";
            }  
            #navbar > ul > li:hover > div > a, #navbar > ul > li:hover > div > div > a, #navbar > ul > li > div.open > div > a, #navbar > ul > li > div > a:focus{
            	background: ".$topbar_font_color.";
            	color: ".$topbar_color.";
            }
            #navbar > ul > li > div > a:hover, #navbar > ul > li > div > div > a:hover,  #navbar > ul > li > div.open > div > a, #navbar > ul > li > div > a:focus{
            	color: ".$topbar_color.";
            }
            .settingsPageDiv, .settingsPageDiv .detailViewContainer{
            	background: #fff;
            }
            #detailView .block{
            	background: ".$topbar_color.";
            	color: ".$topbar_font_color.";
            	padding-left: 0px;
            	padding-right: 0px;
            	padding-bottom: 0px;
            }
            #detailView .block .table{
            	margin-bottom: 0px;
            }
            #detailView .block > hr{
            	display: none;
            }
            #detailView .block > div > h4{
            	padding-left: 5px;
            	max-width: 100%;
            	width: 100%;
            	background: transparent;
            	margin-top: 0px;
                margin-bottom: 0px;
                padding-top: 10px;
                padding-bottom: 10px;
                height: auto;
            }
            #detailView .block > div > h4 img{
            	width: 0px;
            	height: 0px;
            	padding: 10px;
            	background-image: url(layouts/v7/skins/images/arrowdown.png);
            	background-repeat: no-repeat;   
            }
            #detailView .block > div > h4 img[data-mode=\"hide\"] {
            	transform: rotate(-90deg);
            }
            #detailView .detailview-table td:not(.fieldLabel):not(.fieldValue){
            	background: white;
            }
            .detailview-table .fieldLabel, #detailView .summaryView .fieldLabel, .editViewContents .fieldLabel{
            	opacity: 1;
            	background: ".$fieldlablecolor.";
            	color: ".$fieldlableFontcolor.";
            	border-bottom: 1px solid ".$fieldBorder.";
            }
            .detailview-table .row:first-child .fieldValue, .detailview-table .row:first-child .fieldLabel {
                border-top: 1px solid ".$fieldBorder.";
            }
            .detailview-table .fieldValue, .detailview-table .fieldLabel, #detailView .summaryView .fieldValue{
            	border-bottom: 1px solid ".$fieldBorder.";
            }
            .fieldLabel label, .fieldLabel .muted{
            	color: #6f6f6f;
            }
            .detailViewContainer .block .fieldValue, .detailViewContainer .block .row, #detailView .summaryView .fieldValue{
            	background: $fieldvalcolor;
            	color: ".$fieldfontcolor.";
            }
            .fieldValue .value a{
            	color: ".$fieldfontcolor.";
            	/*text-decoration: underline !important;*/
            	font-weight: bold;
            }
            .summaryViewEntries .fieldValue .row{
            	margin-left: 0px;
                padding-left: 15px;
                padding-top: 10px;
            }
            .summaryViewEntries .fieldLabel{
                padding-left: 15px;
                padding-top: 10px;
            }
            .detailViewContainer .block .fieldValue, .detailViewContainer .block .row, #detailView .summaryView .fieldValue, .detailViewContainer .block .fieldLabel, .detailViewContainer .block .row, #detailView .summaryView .fieldLabel{
            	border-top: 1px solid ".$fieldBorder.";
            }
            .detailViewContainer .block{
            	border: 0px;
            	box-shadow: inset 0px 0px 0px 0px ".$fieldBorder.";
            }
            .detailViewContainer .block .fieldValue, .detailViewContainer .block .fieldLabel{
            	padding-left: 10px;
            }
            .editViewContents .fieldBlockContainer, .summaryView, .summaryWidgetContainer{
            	border: 1px solid ".$fieldBorder.";
            }
            .related-tabs .nav-tabs{
            	border-bottom: 0px;
            }
            .related-tabs .tab-label {
                line-height: 28px;
            }
            .related-tabs.row, .tab-item.block{
            	background: ".$menuColor." !important;
            	color: ".$menu_color." !important;
            }
			
			#customtabs, .tab-item{
            	background: ".$menuColor." !important;
            	color: ".$menu_color." !important;
            }
			
            #customtabs .nav-tabs>li.active>a{
            	color: ".$menuColor.";
            	background-color: transparent;
            }
            .related-tabs .nav-tabs>li.active, .related-tabs .nav-tabs>li:hover, .related-tabs .nav-tabs>li:hover > a{
            	border-bottom: 0px solid ".$menu_color." !important;
            	background-color: ".$menu_color." !important;
            	color: ".$menuColor." !important;
            }
            .related-tabs .nav-tabs>li, .related-tabs .nav-tabs>li> a{
            	color: ".$menu_color.";
            }
            .related-tabs .nav-tabs>li.active> a{
            	color: ".$menu_active_color.";
            }
            .modules-menu li.active a{
            	color: white;
            }
            .module-action-bar .module-title{
            	color: ".$menu_color.";
            }
            .modules-menu ul li a{
            	color: ".$menu_color.";
            	opacity: 1;
            }
            .modules-menu ul li.active{
            	background: ".$menu_color.";
            }
            .modules-menu ul li.active a{
            	border-left: 3px solid ".$menuColor.";
            	color: ".$menuColor.";
            }
            .app-menu, #modules-menu, .app-menu .app-modules-dropdown{
            	background: ".$menuColor.";
            	color: ".$menu_color.";
            }
            .app-menu .app-modules-dropdown .module-icon{
            	opacity: 1;
            	text-shadow: none;
            }
            .app-item div {
                -webkit-transition: all 0.01s ease-in;
                -moz-transition: all 0.01s ease;
                -o-transition: all 0.01s ease;
                transition: all 0.01s ease-in;
            }
            #app-menu .app-item{
            	transition: 0s;
            	background-color: transparent;
            	opacity: 1;
            	color: ".$menu_color.";
            	text-shadow: none;
            }
            .referencefield-wrapper .createReferenceRecord{
            	margin-top: 0px;
            }
            .module-buttons.btn, .btn-default, .input-group.inputElement .input-group-addon, .input-group-addon, .referencefield-wrapper .createReferenceRecord{
            	background: ".$menuColor.";
            	color: ".$menu_color." !important;
            	text-shadow: none;
            	border: 1px solid ".$menu_color.";
            }
			
            .btn-default i, .input-group.inputElement .input-group-addon i, .input-group-addon i, .referencefield-wrapper .createReferenceRecord i{
            	color: ".$menu_color.";
            }
            .module-buttons.btn:hover, .btn-default:hover{
            	background: ".$menu_color.";
            	color: ".$menuColor." !important;
            }
            .btn-default:hover i{
            	color: ".$menuColor.";
            }
            .app-menu .app-modules-dropdown li a, .app-menu .app-modules-dropdown li a i{
            	color: ".$menu_color.";
            	opacity: 1;
            	text-shadow: none;
            }
            #app-menu .app-item:hover{
            	background-color: ".$menu_color.";
            	opacity: 1;
            	color: ".$menuColor.";
            }
            #app-menu .dropdown.open, #app-menu .dropdown.open .app-menu-items-wrapper{
            	background-color: ".$menu_color.";
            	opacity: 1;
            	color: ".$menuColor.";
            }
            .app-menu .app-modules-dropdown li:hover, .app-menu .app-modules-dropdown li:hover a, .app-menu .app-modules-dropdown li:hover a i{
            	background-color: ".$menu_color.";
            	color: ".$menuColor.";
            }
            .app-menu .app-modules-dropdown li{
            	opacity: 1;
            }
            .app-nav .module-action-bar .module-action-content{
            	background: ".$menuColor.";
            	color: ".$menu_color.";
            }
            .related-tabs ul{
            	padding-left: 0px;
            }
            .related-tabs .nav-tabs>li{
            	margin: 3px !important;
            	min-height: 48px;
            }
            .related-tabs .nav-tabs>li:hover > a, .related-tabs .nav-tabs>li> a:hover, .related-tabs .nav-tabs>li> a:focus{
            	background: ".$menu_color.";
            	color: ".$menuColor.";
            	border-radius: ".$border_radius."px;
            }
            .modal-header, .modal-overlay-footer{
            	background: ".$topbar_color.";
            	color: ".$topbar_font_color.";
            }
            .modal-footer{
            	background: ".$menuColor.";
            	color: ".$menu_color.";
            }
            .modal-overlay-footer .cancelLink, .modal-footer .cancelLink{
            	background: lightgray;
            	color: red;
            	font-size: 13px;
            	border: 1px solid darkgray;
            	padding: 7px 25px;
            	border-radius: ".$border_radius."px;
            	display: inline-block;
            }
            .floatThead-table{
            	border-left: 0px;
            }
            .dashBoardContainer .dashBoardTabContents ul li {
            	border-radius: ".$border_radius."px !important;
            }
            .btn, .fieldValue > input.inputElement, textarea.inputElement, .search-link, .inputElement.select2-container .select2-choice, .listViewPageDiv .inputElement, input.search-list, .module-buttons.btn, .select2-choices, .select2-container-multi .select2-choices, .table-container:not(.relatedContents), .detailview-content, .editViewContents .fieldBlockContainer, th input.inputElement[type='text'], .modal-content, .input-group, .createReferenceRecord, ul.dropdown-menu:not(.app-modules-dropdown), ul.dropdown-menu li a, ul.dropdown-menu:not(.app-modules-dropdown) li, .select2-container .select2-choice, .popover, .nav-tabs .dropdown-menu{
            	border-radius: ".$border_radius."px;
            	border-top-right-radius: ".$border_radius."px;
            	border-top-left-radius: ".$border_radius."px;
            }
            .modal-header, .relatedHeader, .listViewContentHeader, .listview-table, .listview-table thead, #detailView .block > div > h4, .floatThead-floatContainer{
            	border-radius: ".$border_radius."px ".$border_radius."px 0px 0px;
            }
            .modal-footer, .relatedContents{
            	border-radius: 0px 0px ".$border_radius."px ".$border_radius."px;
            }
            .related-tabs.row{
            	border-radius: ".$border_radius."px;
            }
            #detailView .block, .summaryWidgetContainer, .summaryView{
            	border-radius: ".$border_radius."px;
            }
            .related-tabs .nav-tabs>li.active, .related-tabs .nav-tabs>li:hover, .related-tabs .nav-tabs>li:hover > a{
            	border-radius: ".$border_radius."px;
            }
            .modal-overlay-footer{
            	border-left: 0px;
            	z-index: 10;
            }
            #detailView .block tr:last-child{
            	border-radius: 0 0 ".$border_radius."px ".$border_radius."px!important;
            }
            #detailView .block tr:last-child td:last-child {
            	border-radius: 0 0 ".$border_radius."px 0!important;
            }
            .inputElement.currencyField{
            	    border-radius: 0px ".$border_radius."px ".$border_radius."px 0px;
            }
            .referencefield-wrapper .inputElement, 
            .input-group > :first-child{
            	border-radius: ".$border_radius."px 0px 0px ".$border_radius."px !important;
            }
            .input-group :last-child{
            	border-radius: 0px ".$border_radius."px ".$border_radius."px 0px !important;
            }
            .input-save-wrap :first-child{
            	border-radius: 0px !important;
            }
            .inputElement[type='checkbox']{
            	border-radius: 0px !important;
            }
            .lists-menu > li.active{
            	border-radius: ".$border_radius."px;
            	padding-left: 15px;
            	padding-right: 15px;
            }
            .lists-menu > li.active > div{
            	margin-top: 5px;
            }
            .input-group-btn .color-dropdown{
            	border-radius: 0px ".$border_radius."px ".$border_radius."px 0px !important;
            }
            .color-preview{
            	border-radius: 50px !important;
            }
            .resizable-summary-view, .detailview-header-block, .recentActivitiesContainer{
            	border-radius: ".$border_radius."px;
            }
            .detailview-content .details tr .relatedHeader {
                border: 1px solid #F3F3F3;
                border-radius: ".$border_radius."px ".$border_radius."px 0px 0px !important;
            }
            .detailview-content .details tr .relatedContents{
            	border-radius: 0 0 ".$border_radius."px ".$border_radius."px!important;
            }
            .detailview-table tr:last-child .fieldLabel:first-child{
            	border-radius: 0 0 0 ".$border_radius."px!important;
            	border-bottom: 0px !important;
            }
            .detailview-table tr:last-child .fieldValue{
            	border-bottom: 0px !important;
            }
            #detailView .block > div > h5{
            	border-radius: ".$border_radius."px ".$border_radius."px 0px 0px !important;
            }
            #detailView .closedBlock > div > h5{
            	border-radius: ".$border_radius."px !important;
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
            	border-radius: ".$border_radius."px!important;
            }
            /*.fieldLabel, .fieldValue{
            	min-height: 40px;
            }*/
            .commentContainer{
            	padding-top: 15px;
            	padding-bottom: 15px;
            	border-radius: ".$border_radius."px;
            }
            .listViewEntries td img{
            	border-radius: ".$border_radius."px !important; 
            }
            .userName img{
            	border-radius: ".$border_radius."% !important;
            }
             .detailViewContainer .recordImage, .detailViewContainer .recordImage img{
            	border-radius: ".$border_radius."px !important; 
            	border: 1px;
            }
            .commentsRelatedContainer > .commentTitle, .commentsRelatedContainer > .showcomments{
            	border-radius: ".$border_radius."px;
            }
            .noCommentsMsgContainer{
            	margin-bottom: 20px;
            }
            .commentsRelatedContainer{
            	padding-top: 0px;
            }
            .updates_timeline{
            	padding-bottom: 15px;
            }
            .dashboardBanner{
            	display: none;
            }
            .listview-table{
            	background: #fff;	
            }
            .listViewPageDiv{
            	background: ".$container_color.";
            	/*height: calc(100vh - 84px);*/
            }
            .main-container, .detailViewContainer {
            	min-height: calc(100vh - 84px);
            }
            .detailViewContainer{
            	background: ".$container_color.";
            }
            #sidebar-essentials, .listViewPageDiv{
            	/*height: calc(100vh - 84px);*/
            }
            .coloredBorderTop {
                border-top: 1px solid ".$menuColor.";
            }
            #sidebar-essentials, .settingsNav .settingsgroup, .settingsgroup .panel-group .panel, .settingsgroup div.panel-collapse{
            	background: ".$container_color.";
            	color: ".$custom.";
            }
            #sidebar-essentials .lists-menu-container .lists-header, #sidebar-essentials .lists-menu > li > a{
            	color: ".$custom.";
            }
            .lists-menu > li:hover, .lists-menu > li.active{
            	background: ".$custom.";
            	color: ".$container_color." !important;
            	border-radius: ".$border_radius."px;
            }
            .lists-menu > li:hover, .lists-menu > li.active a, .lists-menu > li:hover a{
            	color: ".$container_color." !important;
            }
            .lists-menu > li.active > .pull-right > .js-popover-container > .fa{
            	margin: 0px;
            }
            .extensionContents{
            	height: 100%;
            	background: white;
            }
            .settingsPageDiv .listViewPageDiv{
            	background: white;
            }
            .dashBoardContainer .tabContainer, .listview-actions-container{
            	background: ".$container_color.";
            }
            .dashboardWidget, .settingsPageDiv .listview-actions-container{
            	background: white;
            }
            .detailViewContainer .content-area, .editViewPageDiv .content-area, .editViewPageDiv.content-area, .editViewPageDiv .reports-content-area, .listViewPageDiv{
            	background: ".$container_color.";
            }
            .popover{
            	color: black;
            }
            .module-action-bar{
            	border-top: 1px solid ".$menuColor."; 
            	border-bottom: 1px solid ".$menuColor." !important; 
            }
            .settingsgroup div.settingsgroup-accordion:hover a, .settingsgroup div.settingsgroup-accordion a:link, .settingsgroup div.settingsgroup-accordion a:active, .settingsgroup li:hover a, .settingsgroup ul li a {
                color: ".$custom.";
            }
            .settingsgroup div.settingsgroup-accordion a:active, .settingsgroup li:hover a, .panel-collapse ul li a.settingsgroup-menu-color, .panel-collapse ul li a:hover {
                color: ".$container_color.";
                background: ".$custom.";
            }
            .settingsPageDiv .modal-overlay-footer{
            	border-left: 0px;
            }";
        
    }
    
    
    function getStylePresets(Vtiger_Request $request){
        
        global $adb,$current_user;
        $admin = false;
        if($current_user->is_admin == 'on'){
            $admin = true;
        }
        
        $defaultTheme = $adb->pquery("SELECT * FROM vtiger_customstyler 
        LEFT JOIN vtiger_customstyler_current_user_style ON vtiger_customstyler_current_user_style.style = vtiger_customstyler.stylerid
        WHERE type = 'custom'");
        $data = array();
        if($adb->num_rows($defaultTheme)){
            for($i=0;$i<$adb->num_rows($defaultTheme);$i++){
                if($adb->query_result($defaultTheme, $i, 'userid') == $current_user->id){
                    $currentTheme = $adb->query_result($defaultTheme, $i, 'style');
                }
                $data[$adb->query_result($defaultTheme, $i, 'stylerid')] = array(
                    "theme-name" => $adb->query_result($defaultTheme, $i, 'theme_name'),
                    "font-name" => $adb->query_result($defaultTheme, $i, 'font_name'),
                    "font-zoom" => $adb->query_result($defaultTheme, $i, 'font_zoom'),
                    "topbar-color" => $adb->query_result($defaultTheme, $i, 'topbar_color'),
                    "topbar-font-color" => $adb->query_result($defaultTheme, $i, 'topbar_font_color'),
                    "menu-style" => $adb->query_result($defaultTheme, $i, 'menu_style'),
                    "menu-color" => $adb->query_result($defaultTheme, $i, 'menu_color'),
                    "menu-font-color" => $adb->query_result($defaultTheme, $i, 'menu_font_color'),
                    "menu-active-font-color" => $adb->query_result($defaultTheme, $i, 'menu_active_font_color'),
                    "container-color" => $adb->query_result($defaultTheme, $i, 'container_color'),
                    "border-radius" => $adb->query_result($defaultTheme, $i, 'border_radius'),
                    "isApplied" => $adb->query_result($defaultTheme, $i, 'isapplied'),
                    "owner" => $adb->query_result($defaultTheme, $i, 'owner'),
                    "current_user" => $current_user->id,
                    "isadmin" => $admin,
                    "currentStyle" => $currentTheme
                );
                
            }
        }
        
        echo json_encode($data);
        
    }
    
}