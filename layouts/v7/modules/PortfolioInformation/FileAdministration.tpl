<table class="FileLocationsTable" style="table-layout:fixed; width:1000px;">
    <thead>
    <tr>
        <td>ID</td>
        <td>Custodian</td>
        <td>Tenant</td>
        <td>File Location</td>
        <td>Rep Code</td>
        <td>Master Rep Code</td>
        <td>Omni Code</td>
        <td>Prefix</td>
        <td>Suffix</td>
    </tr>
    </thead>
    <tbody>
        {foreach from=$LOCATIONS item=v}
            <tr>
                <td><input type="text" style="width:100px;" value="{$v.id}" name="id" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.custodian}" name="custodian" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.tenant}" name="tenant" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.file_location}" name="file_location" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.rep_code}" name="rep_code" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.master_rep_code}" name="master_rep_code" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.omni_code}" name="omni_code" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.prefix}" name="prefix" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.suffix}" name="suffix" data-id="{$v.id}" /></td>
            </tr>
        {/foreach}
    </tbody>
</table>

<input type="button" class="addrow" value="Add New Location" />

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}