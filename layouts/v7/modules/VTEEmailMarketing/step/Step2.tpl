<div class="editViewPageDiv viewContent">
    <div class="editViewContents">
        <div name='editContent'>
            <div class="tab-content">
                <input type="hidden" name="relationIdAccounts" value="{$RELATION_ID['relationIdAccounts']}">
                <input type="hidden" name="relationIdLeads" value="{$RELATION_ID['relationIdLeads']}">
                <input type="hidden" name="relationIdContacts" value="{$RELATION_ID['relationIdContacts']}">
                <input type="hidden" name="module_related" value="">
                <input type="hidden" name="actionPopup" value="">
                <div class="row toolbar">
                    <div class="col-lg-12 marketinglist-toolbar"
                         style="height: 130px; padding-top: 25px; padding-bottom: 20px">
                        <div class="col-lg-1 border">
                            <div class="content-column">
                                <h5> Show Lead Lists
                                    <button type="button" id="show-leads-list" module="Leads"
                                            class="btn btn-sm btn-toggle pull-right btn-on-off-filter"
                                            data-toggle="button" aria-pressed="true" autocomplete="off">
                                        <div class="handle"></div>
                                    </button>
                                </h5>
                                <h5> Show Contacts Lists
                                    <button type="button" id="show-contacts-list" module="Contacts"
                                            class="btn btn-sm btn-toggle pull-right btn-on-off-filter"
                                            data-toggle="button" aria-pressed="true" autocomplete="off">
                                        <div class="handle"></div>
                                    </button>
                                </h5>
                                <h5> Show Organization Lists
                                    <button type="button" id="show-account-list" module="Accounts"
                                            class="btn btn-sm btn-toggle pull-right btn-on-off-filter"
                                            data-toggle="button" aria-pressed="true" autocomplete="off">
                                        <div class="handle"></div>
                                    </button>
                                </h5>
                            </div>
                        </div>
                        <div class="col-lg-2 border">
                            <div class="content-column">
                                <div class="btn-group">
                                    <button data-url="{CustomView_Record_Model::getCreateViewUrl('Leads')}"
                                            class="btn addButton btn-default createFilter"
                                            title="{vtranslate('LBL_CREATE_LIST',$MODULE)}">
                                        <i class="fa fa-plus"></i>&nbsp;{vtranslate('CREATE_LEADS_LIST',$MODULE)}
                                    </button>
                                </div>

                                <div class="btn-group">
                                    <button data-url="{CustomView_Record_Model::getCreateViewUrl('Contacts')}"
                                            class="btn addButton btn-default createFilter"
                                            title="{vtranslate('LBL_CREATE_LIST',$MODULE)}">
                                        <i class="fa fa-plus"></i>&nbsp;{vtranslate('CREATE_CONTACTS_LIST',$MODULE)}
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button data-url="{CustomView_Record_Model::getCreateViewUrl('Accounts')}"
                                            class="btn addButton btn-default createFilter"
                                            title="{vtranslate('LBL_CREATE_LIST',$MODULE)}">
                                        <i class="fa fa-plus"></i>&nbsp;{vtranslate('CREATE_ACCOUNTS_LIST',$MODULE)}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 border">
                            <div class="content-column">
                                <div class="btn-group">
                                    <button type="button" class="btn addButton btn-default selectRelation"
                                            module="VTEEmailMarketing" moduleRel="Leads">
                                        {vtranslate('SELECT_LEADS',$MODULE)}
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn addButton btn-default selectRelation"
                                            module="VTEEmailMarketing" moduleRel="Contacts">
                                        {vtranslate('SELECT_CONTACTS',$MODULE)}
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn addButton btn-default selectRelation"
                                            module="VTEEmailMarketing" moduleRel="Accounts">
                                        {vtranslate('SELECT_ACCOUNTS',$MODULE)}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 border">
                            <h5 class="text-center">Load From Previous Email Marketing</h5>
                            {*Search*}
                            <div class="div-autocomplete">
                                <input name="popupReferenceModule" type="hidden" value="VTEEmailMarketing">
                                <div class="input-group">
                                    <input name="vteemailmarketingid" type="hidden" value="" class="sourceField" data-displayvalue="">
                                    <input class="marginLeftZero autoComplete inputElement ui-autocomplete-input" id="load_other_email_marketing" style="padding-left:10px !important ; font-size:15px">
                                    <span id="deleteValueEmailMarketing" class="input-group-addon relatedPopup cursorPointer hide" title="Delete">
                                        <i class="fa fa-close"></i>
                                    </span>
                                    <span class="input-group-addon relatedPopup cursorPointer" title="Select">
                                        <i class="fa fa-search loadOtherEmailMarketing" module="VTEEmailMarketing"></i>
                                    </span>
                                </div>
                            </div>
                            {*End Search*}
                        </div>
                        <div class="col-lg-5">
                            <h4 class="text-center">Total Records</h4>
                            <h3 class="text-center" id="total-record" style="color: red">{if $RECORD_DATA['total_related']} {$RECORD_DATA['total_related']} {else} 0 {/if}</h3>
                        </div>

                    </div>
                </div>
                <div class="container-fluid" style="padding-top: 30px; padding-bottom: 50px">
                    <table class="table" id="table-list-filter">
                        {foreach item=value key=key from=$FILTER}
                            <tr module="{$value['module']}" class="hide">
                                <td>
                                    {if $value['module'] eq 'Contacts'}
                                        <h5> <i class="vicon-contacts module-icon"></i>{vtranslate($value['module'],$value['module'])}</h5>
                                    {elseif $value['module'] eq 'Leads'}
                                        <h5> <i class="vicon-leads module-icon"></i>{vtranslate($value['module'],$value['module'])}</h5>
                                    {else}
                                        <h5> <i class="vicon-accounts module-icon"></i>{vtranslate($value['module'],$value['module'])}</h5>
                                    {/if}
                                </td>
                                <td>
                                    <h4>{$value['name_filter']}</h4>
                                    <i><span class="marketing-list-user">( {$value['name']} )</span></i>
                                </td>
                                <td>
                                    <h5 class="text-center">{$value['count']}</h5>
                                    <h5 class="text-center">Records</h5>
                                </td>
                                <td>
                                    <button class="btn addButton btn-default btn-success btn-load-filter"
                                            value="{$value['cvid']}" action="0">Load
                                    </button>
                                </td>
                                <td>
                                    <h4 class="text-center filter-loaded-record" ></h4>
                                </td>
                                <td>
                                    <h5 class="show-info-load-0-record"></h5>
                                </td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class=""
     style=" position: fixed; width: 100%; left:0; bottom:0; background: #f0eef0; padding-top: 15px; padding-bottom: 15px; text-align: center;">
    <button style="margin-right:3px;" class="btn btn-success btnNext" name="btnNext" type="button"><strong>Next</strong>
    </button>
    <button style="color:#ff4c42;" class="btn btn-link btnCancel" name="btnCancel" type="button"><strong>Cancel</strong>
    </button>
</div>
</form>