<div>
    {foreach key=index item=jsModel from=$SCRIPTS}
        <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
    {/foreach}
    {foreach key=index item=cssModel from=$CSS}
        <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
    {/foreach}

    <div class="FixTransaction">
        <p>Creating a new transaction using {$RECORD.security_symbol}.  Entering a date will automatically select the best known price as of that date</p>
        <input type="hidden" id="account_number" value="{$RECORD.account_number}" />
        <input type="hidden" id="security_symbol" value="{$RECORD.security_symbol}" />

        <table class="fixed_transactions_table">
            <tr><td>Date</td>
                <td><input type="text" id="date_select" /></td>
                <td>Quantity</td>
                <td><input class="quantity" type="text" value="{$QUANTITY}" /></td>
            </tr>
            <tr>
                <td>Price</td>
                <td><input type="text" id="price" value="0" /></td>
                <td>Cost Basis</td>
                <td colspan="3"><input type="text" id="cost_basis" readonly value="0" /></td>
            </tr>
            <tr>
                <td colspan="2"><input type="button" value="Save" id="save_transaction" /></td>
                <td colspan="2"><input type="button" value="Cancel" id="cancel_transaction" /></td>
            </tr>
        </table>
    </div>
</div>