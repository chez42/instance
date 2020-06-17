<p>Pershing feeds us Trade transactions with a Buy/Sell indicator and "code" is empty.  You will not see any Buy/Sell trades in this list because of this</p>
<p><strong>Note:</strong> + is not required in the Operation field</p>
<div id="MapWrap" style="width:800px; display:block;">
    <table class = "PershingTransactionMappingTable" style="table-layout:fixed; width:800px;">
        <thead>
        <tr>
            <td>Code</td>
            <td>Description</td>
            <td>Omniscient Category</td>
            <td>Omniscient Activity</td>
            <td>Operation (+/-)</td>
        </tr>
        </thead>
        <tbody>
        {foreach from=$MAPPING item=v}
            <tr>
                <td>{$v.source_code}</td>
                <td>{$v.description}</td>
                <td><input type="text" style="width:100px;" value="{$v.omniscient_category}" name="omniscient_category" data-id="{$v.source_code}" /></td>
                <td>
                    <select name="omniscient_activity" data-id="{$v.source_code}">
                        <option value=""></option>
                        {foreach from=$ACTIVITIES item=i}
                            <option value="{$i}" {if $v.omniscient_activity|lower eq $i|lower}selected="selected"{/if}>{$i}</option>
                        {/foreach}
                    </select>
                </td>
                {*<td><input type="text" style="width:100px;" value="{$v.omniscient_activity}" name="omniscient_activity" data-id="{$v.source_code}" /></td>*}
                <td><input type="text" style="width:100px;" value="{$v.operation}" name="operation" data-id="{$v.id}" /></td>
            </tr>
        {/foreach}
        </tbody>
    </table>

    {*    <input type="button" class="addrow" value="Add New Mapping" /> *}
</div>

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}