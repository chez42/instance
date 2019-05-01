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
                          {*  <img src="layouts/vlayout/modules/TimeTracker/images/contact-icon.png" width="14px" style="vertical-align: text-bottom;" />*}
                            <a href="index.php?module=Contacts&view=Detail&record={$contacts['contactid']}">
                                {$contacts['firstname']} {$contacts['lastname']} ({vtranslate('Contacts', $MODULE)})
                            </a>
                        </div>
                    {/foreach}
                {/if}
                {if $RECORD['relatedToOther'] }
                   {* {if $RECORD['relatedToOther']['setype'] == 'HelpDesk'}
                        <img src="layouts/vlayout/modules/TimeTracker/images/ticket-icon.png" width="14px" style="vertical-align: text-bottom;" />
                    {elseif $RECORD['relatedToOther']['setype'] == 'Potentials'}
                        <img src="layouts/vlayout/modules/TimeTracker/images/opportunity-icon.png" width="14px" style="vertical-align: text-bottom;" />
                    {elseif $RECORD['relatedToOther']['setype'] == 'Project'}
                        <img src="layouts/vlayout/modules/TimeTracker/images/project-icon.png" width="14px" style="vertical-align: text-bottom;" />
                    {elseif $RECORD['relatedToOther']['setype'] == 'ProjectTask'}
                        <img src="layouts/vlayout/modules/TimeTracker/images/project-task-icon.png" width="14px" style="vertical-align: text-bottom;" />
                    {elseif $RECORD['relatedToOther']['setype'] == 'Accounts'}
                        <img src="layouts/vlayout/modules/TimeTracker/images/corporate-icon-PNG.png" width="14px" style="vertical-align: text-bottom;" />
                    {/if}*}

                    <a href="index.php?module={$RECORD['relatedToOther']['setype']}&view=Detail&record={$RECORD['relatedToOther']['crmid']}">
                        {$RECORD['relatedToOther']['label']} ({vtranslate({$RECORD['relatedToOther']['setype']}, $MODULE)})
                    </a>
                {/if}
            </td>
            <td class="listViewEntryValue medium" onclick="setCheckBox({$RECORD['crmid']})" style="padding: 0px 5px !important;">
                {$RECORD['subject']}
            </td>
            <td class="listViewEntryValue medium" onclick="setCheckBox({$RECORD['crmid']})" style="padding: 0px 5px !important;">
                {$RECORD['description']}
            </td>
            <td class="listViewEntryValue medium" onclick="setCheckBox({$RECORD['crmid']})" style="padding: 0px 5px !important;">
                {$RECORD['start_date_time']}
            </td>
            <td class="listViewEntryValue medium" onclick="setCheckBox({$RECORD['crmid']})" style="padding: 0px 5px !important;">
                {$RECORD['end_date_time']}
            </td>
            <td class="listViewEntryValue medium" onclick="setCheckBox({$RECORD['crmid']})" style="padding: 0px 5px !important;">
                {if $RECORD['duration_hours']|count_characters==1}0{/if}{$RECORD['duration_hours']}:{if $RECORD['duration_minutes']|count_characters==1}0{/if}{$RECORD['duration_minutes']}:{if $RECORD['duration_seconds']|count_characters==1}0{elseif $RECORD['duration_seconds']|count_characters<1}00{/if}{$RECORD['duration_seconds']}
            </td>
        </tr>
    {/foreach}
    <script>
        function setCheckBox(a){
            if($('input#'+a).prop('checked')){
                $('input#'+a).attr('checked', false);
            }else{
                $('input#'+a).attr('checked', true);
            }
        }
    </script>