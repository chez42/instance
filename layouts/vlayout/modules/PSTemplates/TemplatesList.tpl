<div style="font-weight: normal !important;">
    <select class="chzn-select lineItemTax" {if $QUOTER_MODULE && $QUOTER_MODULE->isActive()}
        id="quoter_template"{else}id="tks_quotetemp"{/if} name="tks_quotetemp" >
        <option selected="true" value="0" >Select An Option</option>
        {foreach item=template from=$TEMPLATES_LIST}
            <OPTION value="{$template.id}">{$template.name}</OPTION>
        {/foreach}
    </select>
</div>
