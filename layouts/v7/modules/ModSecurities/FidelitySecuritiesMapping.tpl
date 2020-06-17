<div id="MapWrap" style="width:100%; display:block;">
    <table class = "FidelitySecuritiesMappingTable" style="table-layout:fixed; width:100%;">
        <thead>
        <tr>
            <td>Type</td>
            <td>Description</td>
            <td>Asset Class Code</td>
            <td>Asset Class Type Code</td>
            <td>Base Asset Class</td>
            <td>Security Type</td>
            <td>Security Type 2</td>
            <td>Domestic/International</td>
            <td>Style</td>
            <td>Size Capitalization</td>
            <td>Multiplier</td>
        </tr>
        </thead>
        <tbody>
        {foreach from=$MAPPING item=v}
            <tr>
                <td>{$v.type}</td>
                <td>{$v.description}</td>
                <td>{$v.asset_class_code}</td>
                <td>{$v.asset_class_type_code}</td>
                <td><input type="text" style="width:100px;" value="{$v.omni_base_asset_class}" name="omni_base_asset_class" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.security_type}" name="security_type" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.security_type2}" name="security_type2" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.domestic_international}" name="domestic_international" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.style}" name="style" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.size_capitalization}" name="size_capitalization" data-id="{$v.id}" /></td>
                <td><input type="text" style="width:100px;" value="{$v.multiplier}" name="multiplier" data-id="{$v.id}" /></td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}