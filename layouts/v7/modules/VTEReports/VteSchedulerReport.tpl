{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}

<div class="row-fluid">       
    <div class="span12">
        <div class="row-fluid">  
            {include file='modules/VTEReports/VteSchedulerReportEdit.tpl'}
        </div>
    </div>
</div> 
<script>
jQuery( document ).ready(function(){
    setScheduleOptions();
    jQuery('#selectedRecipients').on("change", function (e) {
        var selected_css = e.added.css;
        var last_li = jQuery( "div[id*='selectedRecipients']").children("ul").find("li.select2-search-choice:last");
        last_li.css("background-image","none");
        last_li.addClass(selected_css);
    });
});
</script>