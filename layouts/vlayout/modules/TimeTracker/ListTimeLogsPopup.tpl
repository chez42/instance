<style>
    .blockUI.blockMsg.blockPage{
        position: absolute !important;

        left: inherit !important;
        width: 90% !important;
        margin-left: 5% !important;

    }
</style>
<div class="modelContainer">
    <div class="modal-header contentsBackground">
        <button class="close" aria-hidden="true" data-dismiss="modal" type="button" title="{vtranslate('LBL_CLOSE')}">x</button>
        <h3>{vtranslate('LBL_EVENTS_LIST', $MODULE)}</h3>
    </div>
    <form class="form-horizontal recordEditView" id="frmEventsList" method="post" action="index.php">
        <input type="hidden"  name="service_name" value="{$SERVICE_RECORD['servicename']}"/>
        <input type="hidden"  name="service_price" value="{$SERVICE_RECORD['unit_price']}"/>
        <input type="hidden"  name="service_type" value="{$SERVICE_RECORD['setype']}"/>
        <input type="hidden"  name="service_id" value="{$SERVICE_RECORD['serviceid']}"/>
        <div class="quickCreateContent">
            <div class="modal-body">
                <table class="table table-bordered listViewEntriesTable">
                    <thead>
                    <tr class="listViewHeaders">
                        <th width="5%" class="medium">
                            <input type="checkbox" id="masCheckBox" value="" />
                        </th>
                        <th class="medium">{vtranslate('LBL_TYPE_EVENT', $MODULE)}</th>
                        <th class="medium">{vtranslate('LBL_ASSIGNED_TO_EVENT', $MODULE)}</th>
                        <th class="medium">{vtranslate('LBL_RELATED_TO_EVENT', $MODULE)}</th>
                        <th class="medium">{vtranslate('LBL_SUBJECT_EVENT', $MODULE)}</th>
                        <th class="medium" width="15%">{vtranslate('LBL_DESCRIPTION_EVENT', $MODULE)}</th>
                        <th class="medium">{vtranslate('LBL_START_DATE_TIME_EVENT', $MODULE)}</th>
                        <th class="medium">{vtranslate('LBL_DUE_DATE_TIME_EVENT', $MODULE)}</th>
                        <th class="medium">{vtranslate('LBL_DURATION_EVENT', $MODULE)}</th>
                    </tr>
                    </thead>
                    {foreach item=RECORD from=$RECORD_MODEL}
                        <tr class="listViewEntries" data-info ='{ZEND_JSON::encode($RECORD)}' >

                            <td width="5%" class="medium" style="padding: 0px 5px !important;">
                                <input class="listViewEntriesCheckBox" type="checkbox" value="{$RECORD['crmid']}" id="{$RECORD['crmid']}" />
                            </td>

                            <td class="listViewEntryValue medium" onclick="setCheckBox({$RECORD['crmid']})" style="padding: 0px 5px !important;">
                                {$RECORD['activitytype']}
                            </td>
                            <td class="listViewEntryValue medium" onclick="setCheckBox({$RECORD['crmid']})" style="padding: 0px 5px !important;">
                                {$RECORD['assignedTo']}
                            </td>
                            <td class="listViewEntryValue medium" onclick="setCheckBox({$RECORD['crmid']})" style="padding: 0px 5px !important;">
                                {if $RECORD['relatedToContact'] }
                                    {foreach $RECORD['relatedToContact']  as $contacts  }
                                        <div class="box-related" style="margin-bottom: 10px;">
                                            <img src="layouts/vlayout/modules/TimeTracker/images/contact-icon.png" width="14px" style="vertical-align: text-bottom;" />
                                            <a href="index.php?module=Contacts&view=Detail&record={$contacts['contactid']}">
                                                {$contacts['firstname']} {$contacts['lastname']}
                                            </a>
                                        </div>
                                    {/foreach}
                                {/if}
                                {if $RECORD['relatedToOther'] }
                                    {if $RECORD['relatedToOther']['setype'] == 'HelpDesk'}
                                        <img src="layouts/vlayout/modules/TimeTracker/images/ticket-icon.png" width="14px" style="vertical-align: text-bottom;" />
                                    {elseif $RECORD['relatedToOther']['setype'] == 'Potentials'}
                                        <img src="layouts/vlayout/modules/TimeTracker/images/opportunity-icon.png" width="14px" style="vertical-align: text-bottom;" />
                                    {elseif $RECORD['relatedToOther']['setype'] == 'Project'}
                                        <img src="layouts/vlayout/modules/TimeTracker/images/project-icon.png" width="14px" style="vertical-align: text-bottom;" />
                                    {elseif $RECORD['relatedToOther']['setype'] == 'ProjectTask'}
                                        <img src="layouts/vlayout/modules/TimeTracker/images/project-task-icon.png" width="14px" style="vertical-align: text-bottom;" />
                                    {elseif $RECORD['relatedToOther']['setype'] == 'Accounts'}
                                        <img src="layouts/vlayout/modules/TimeTracker/images/corporate-icon-PNG.png" width="14px" style="vertical-align: text-bottom;" />
                                    {/if}

                                    <a href="index.php?module={$RECORD['relatedToOther']['setype']}&view=Detail&record={$RECORD['relatedToOther']['crmid']}">
                                        {$RECORD['relatedToOther']['label']}
                                    </a>
                                {/if}
                            </td>
                            <td class="listViewEntryValue medium" onclick="setCheckBox({$RECORD['crmid']})" style="padding: 0px 5px !important;">
                                {$RECORD['subject']}
                            </td>
                            <td class="listViewEntryValue medium" onclick="setCheckBox({$RECORD['crmid']})" style="padding: 0px 5px !important;">
                                <pre style="margin: 0; padding: 0; border: none; background: none">
                                {$RECORD['description']}
                                </pre>
                            </td>
                            <td class="listViewEntryValue medium" onclick="setCheckBox({$RECORD['crmid']})" style="padding: 0px 5px !important;">
                                {$RECORD['start_date_time']}
                            </td>
                            <td class="listViewEntryValue medium" onclick="setCheckBox({$RECORD['crmid']})" style="padding: 0px 5px !important;">
                                {$RECORD['end_date_time']}
                            </td>
                            <td class="listViewEntryValue medium" onclick="setCheckBox({$RECORD['crmid']})" style="padding: 0px 5px !important;">
                                {if $RECORD['duration_hours'] > 9}
                                    {$RECORD['duration_hours']} :
                                {else}
                                    0{$RECORD['duration_hours']} :
                                {/if}
                                {if $RECORD['duration_minutes'] > 9}
                                    {$RECORD['duration_minutes']} :
                                {else}
                                    0{$RECORD['duration_minutes']} :
                                {/if}
                                {$RECORD['duration_seconds'] = ceil($RECORD['duration_seconds'])}
                                {if $RECORD['duration_seconds'] > 9}
                                    {$RECORD['duration_seconds']}
                                {elseif $RECORD['duration_seconds'] == ''}
                                    00
                                {else}
                                    0{$RECORD['duration_seconds']}
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </div>
        </div>
        <div class="modal-footer quickCreateActions">
            <a class="cancelLink cancelLinkContainer pull-right" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
            <button class="btn btn-success" id="btnSaveEvent" type="button"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
        </div>
    </form>
</div>
<script>
    function setCheckBox(a){
        if($('input#'+a).prop('checked')){
            $('input#'+a).attr('checked', false);
        }else{
            $('input#'+a).attr('checked', true);
        }
    }
</script>