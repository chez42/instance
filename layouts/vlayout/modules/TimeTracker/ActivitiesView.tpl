<table class="table table-bordered listViewEntriesTable">
    <thead>
    <tr class="listViewHeaders">
        {foreach item=HEADER_FIELD from=$RELATED_HEADERS}
            <th {if $HEADER_FIELD@last} colspan="2" {/if} nowrap>
                {if $HEADER_FIELD->get('column') eq 'time_start'}
                {else}
                    <a href="javascript:void(0);" class="relatedListHeaderValues"  data-fieldname="{$HEADER_FIELD->get('column')}">
                        {vtranslate($HEADER_FIELD->get('label'), 'TimeTracker')}
                    </a>
                {/if}
            </th>
        {/foreach}
    </tr>
    </thead>
    {foreach item=RELATED_RECORD from=$RELATED_RECORDS}
        <tr class="listViewEntries" data-id='{$RELATED_RECORD->getId()}'
                {if $RELATED_MODULE_NAME eq 'Calendar'}
                {assign var=DETAILVIEWPERMITTED value=isPermitted($RELATED_MODULE->get('name'), 'DetailView', $RELATED_RECORD->getId())}
            {if $DETAILVIEWPERMITTED eq 'yes'}
                data-recordUrl='{$RELATED_RECORD->getDetailViewUrl()}'
            {/if}
                {else}
            data-recordUrl='{$RELATED_RECORD->getDetailViewUrl()}'
                {/if}>
            {foreach item=HEADER_FIELD from=$RELATED_HEADERS}
                {assign var=RELATED_HEADERNAME value=$HEADER_FIELD->get('name')}
                <td class="{$WIDTHTYPE}" data-field-type="{$HEADER_FIELD->getFieldDataType()}" nowrap>
                    {if $HEADER_FIELD->isNameField() eq true or $HEADER_FIELD->get('uitype') eq '4'}
                        <a href="{$RELATED_RECORD->getDetailViewUrl()}">{$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}</a>
                    {elseif $RELATED_HEADERNAME eq 'access_count'}
                        {$RELATED_RECORD->getAccessCountValue($PARENT_RECORD->getId())}
                    {elseif $RELATED_HEADERNAME eq 'time_start'}
                    {elseif $RELATED_HEADERNAME eq 'listprice' || $RELATED_HEADERNAME eq 'unit_price'}
                        {CurrencyField::convertToUserFormat($RELATED_RECORD->get($RELATED_HEADERNAME), null, true)}
                        {if $RELATED_HEADERNAME eq 'listprice'}
                            {assign var="LISTPRICE" value=CurrencyField::convertToUserFormat($RELATED_RECORD->get($RELATED_HEADERNAME), null, true)}
                        {/if}
                    {else if $RELATED_HEADERNAME eq 'filename'}
                         {$RELATED_RECORD->get($RELATED_HEADERNAME)}
                         {else}
                            {$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
                    {/if}
                </td>
            {/foreach}
        </tr>
    {/foreach}

</table>