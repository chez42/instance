<p> - Schwab Category is simply a guideline for quick reference as they map to what Schwab categorizes existing transactions.
    If it is empty, it simply means we do not have a transaction of that type.</p>
<p><strong>Note:</strong> + is not required in the Operation field</p>
<div id="MapWrap" style="width:100%; display:block;">
    <table class = "SchwabTransactionMappingTable" style="table-layout:fixed; width:1000px; overflow:auto; height:500px; display:block; border:1px solid black;">
        <thead>
        <tr>
            <td>ID</td>
            <td>Source Code</td>
            <td>Type Code</td>
            <td>Subtype Code</td>
            <td>Direction</td>
            <td>Schwab Category</td>
            <td>Transaction Activity</td>
            <td>Omniscient Category</td>
            <td>Omniscient Activity</td>
            <td>Operation (+/-)</td>
        </tr>
        </thead>
        <tbody>
        {foreach from=$MAPPING item=v}
            <tr>
                <td>{$v.id}</td>
                <td>{$v.source_code}</td>
                <td>{$v.type_code}</td>
                <td>{$v.subtype_code}</td>
                <td>{$v.direction}</td>
                <td>{$v.schwab_category}</td>
                <td>{$v.transaction_activity}</td>
                <td><input type="text" style="width:100px;" value="{$v.omniscient_category}" name="omniscient_category" data-id="{$v.id}" /></td>
                <td>
                    <select name="omniscient_activity" data-id="{$v.id}">
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
{*
{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}*}