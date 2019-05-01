{strip}
<div class="VTEEmailMarketing-content-area" style="padding: 15px;">
    <div class="editContainer" style="padding-left: 2%;padding-right: 2%">
        <div class="row">
            {assign var=LABELS value = ["step1" => vtranslate("LBL_CAMPAIGN_DETAILS","VTEEmailMarketing") , "step2" => vtranslate("LBL_MARKETING_LIST","VTEEmailMarketing"), "step3" => vtranslate("LBL_TEMPLATES","VTEEmailMarketing"), "step4" => vtranslate("LBL_REVIEW_AND_SEND","VTEEmailMarketing")]}
            {include file="BreadCrumbs.tpl"|vtemplate_path:$MODULE ACTIVESTEP=1 BREADCRUMB_LABELS=$LABELS MODULE=$QUALIFIED_MODULE}
        </div>
        <form class="form-horizontal recordEditView" id="EditView" name="edit" method="post" action="index.php" enctype="multipart/form-data">
            <input type="hidden" name="module" value="VTEEmailMarketing">
            <input type="hidden" name="action" value="Save">
            <input type="hidden" name="isCreate" value="1">
            <input type="hidden" name="idEmailMarketing" value="">
            <input type="hidden" name="campagin_name" value="">
            <input type="hidden" name="from_name" value="">
            <input type="hidden" name="from_email" value="">
            <input type="hidden" name="templateEmail" value="">
            <input type="hidden" name="step" value="step1">
            <input type="hidden" name="totalRecord" value="" >
            <input type="hidden" name="status">
            <input type="hidden" name="from_serveremailid">

        </form>
        <div id="EmailMarketingStep1" class="">
            {include file="step/Step1.tpl"|vtemplate_path:$MODULE}
        </div>

        <div id="EmailMarketingStep2" class="hide">
            {include file="step/Step2.tpl"|vtemplate_path:$MODULE}
        </div>

        <div id="EmailMarketingStep3" class="hide">
            {include file="step/Step3.tpl"|vtemplate_path:$MODULE}
        </div>

        <div id="EmailMarketingStep4" class="hide">
            {include file="step/Step4.tpl"|vtemplate_path:$MODULE}
        </div>
    </div><!-- End div editContainer -->
</div>
{/strip}