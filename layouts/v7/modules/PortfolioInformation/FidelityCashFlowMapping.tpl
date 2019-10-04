<div id="MapWrap" style="width:800px; display:block;">
    <table class = "FidelityCashFlowMapping" style="table-layout:fixed; width:800px;">
        <thead>
        <tr>
            <td>ID</td>
            <td>Key</td>
            <td>Description</td>
            <td>Category</td>
        </tr>
        </thead>
        <tbody>
        {foreach from=$MAPPING item=v}
            <tr>
                <td><input type="text" style="width:100px;" value="{$v.id}" name="id" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.transaction_key}" name="transaction_key" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.description}" name="description" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.category}" name="category" data-id="{$v.id}" /></td>
            </tr>
        {/foreach}
        </tbody>
    </table>

    {*    <input type="button" class="addrow" value="Add New Mapping" /> *}
</div>

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}