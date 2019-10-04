{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Vtiger/views/Index.php *}

{* START YOUR IMPLEMENTATION FROM BELOW. Use {debug} for information *}
{strip}
<div class="VTEEmailMarketing-content-area" style="padding: 15px;">
    <div class="editContainer" style="padding-left: 2%;padding-right: 2%">
        <div class="row">
            {assign var=LABELS value = ["step1" => vtranslate("LBL_CAMPAIGN_DETAILS","VTEEmailMarketing") , "step2" => vtranslate("LBL_MARKETING_LIST","VTEEmailMarketing"), "step3" => vtranslate("LBL_TEMPLATES","VTEEmailMarketing"), "step4" => vtranslate("LBL_REVIEW_AND_SEND","VTEEmailMarketing")]}
            {include file="BreadCrumbs.tpl"|vtemplate_path:$MODULE ACTIVESTEP=1 BREADCRUMB_LABELS=$LABELS MODULE=$QUALIFIED_MODULE}
        </div>