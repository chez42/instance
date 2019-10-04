<span style="font-weight: normal !important;">
    <select class="select2 lineItemTax"  name="tks_quotetemp"{if $QUOTER_MODULE && $QUOTER_MODULE->isActive()}
        id="quoter_template"{else}id="tks_quotetemp"{/if} style="width: 150px;" >
        <option selected="true" value="0" >Select An Option</option>
        {foreach item=template from=$TEMPLATES_LIST}
            <OPTION value="{$template.id}">{$template.name}</OPTION>
        {/foreach}
    </select>
</span>
