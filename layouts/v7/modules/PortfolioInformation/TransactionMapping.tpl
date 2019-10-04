<table class="TransactionMappingTable">
    <thead>
    <tr>
        <td>ID</td>
        <td>Transaction Type</td>
        <td>Transaction Activity</td>
        <td>Report As Type</td>
        <td>TD</td>
        <td>Fidelity</td>
        <td>Schwab</td>
        <td>Pershing</td>
        <td>PC</td>
    </tr>
    </thead>
    <tbody>
    {foreach from=$MAPPING item=v}
        <tr>
            <td><input type="text" value="{$v.id}" name="id" data-id="{$v.id}" /></td>
            <td><input type="text" value="{$v.transaction_type}" name="transaction_type" data-id="{$v.id}" /></td>
            <td><input type="text" value="{$v.transaction_activity}" name="transaction_activity" data-id="{$v.id}" /></td>
            <td><input type="text" value="{$v.report_as_type}" name="report_as_type" data-id="{$v.id}" /></td>
            <td><input type="text" value="{$v.td}" name="td" data-id="{$v.id}" /></td>
            <td><input type="text" value="{$v.fidelity}" name="fidelity" data-id="{$v.id}" /></td>
            <td><input type="text" value="{$v.schwab}" name="schwab" data-id="{$v.id}" /></td>
            <td><input type="text" value="{$v.pershing}" name="pershing" data-id="{$v.id}" /></td>
            <td><input type="text" value="{$v.pc}" name="pc" data-id="{$v.id}" /></td>
        </tr>
    {/foreach}
    </tbody>
</table>

<input type="button" class="addrow" value="Add New Mapping" />

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}