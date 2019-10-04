<p><strong>Note:</strong> + is not required in the Operation field</p>
<p>If Activity is left empty, "Description" will be used.  This most likely won't map to any pick list values, but at least prevents empty types</p>
<div id="MapWrap" style="width:100%; display:block;">
    <table class = "SchwabTransactionMappingTable" style="table-layout:fixed; width:100%;">
        <thead>
        <tr>
            <td>Key</td>
            <td>Key Mnemonic Description</td>
            <td>Code Description</td>
            <td>Omniscient Category</td>
            <td>Omniscient Activity</td>
            <td>Operation (+/-)</td>
        </tr>
        </thead>
        <tbody>
        {foreach from=$MAPPING item=v}
            <tr>
                <td>{$v.id}</td>
                <td>{$v.description}</td>
                <td>{$v.code_description}</td>
                <td><input type="text" style="width:100px;" value="{$v.omniscient_category}" name="omniscient_category" data-id="{$v.id}" /></td>
                <td>
                    <select name="omniscient_activity" data-id="{$v.id}">
                        <option value=""></option>
                        {foreach from=$ACTIVITIES item=i}
                            <option value="{$i}" {if $v.omniscient_activity|lower eq $i|lower}selected="selected"{/if}>{$i}</option>
                        {/foreach}
                    </select>
                </td>
                {*<td><input type="text" style="width:100px;" value="{$v.omniscient_activity}" name="omniscient_activity" data-id="{$v.id}" /></td>*}
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