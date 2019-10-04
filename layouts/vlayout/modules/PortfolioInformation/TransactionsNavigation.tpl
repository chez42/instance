{foreach $ACCOUNTINFO key=k item=v}
    <input type="hidden" class="transactions_navigation_account_numbers" value="{$v}" />
{/foreach}

{foreach key=index item=cssModel from=$STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}

<div id="TransactionsNavigation">
    <p>Filtering</p>
    <div class="transactions_navigation_section">
        <div class='open_closer'>
            <div class='title_bar'>Activity</div>
            <div class='closer_item'>
                <select multiple="multiple" name="transaction_filter_activities_value[]" id="transaction_filter_activities_value">
                    {foreach from=$ACTIVITIES key=k item=v}
                        <option value="{$k}" selected>{$v}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class='open_closer'>
            <div class='title_bar'>Security Symbols</div>
            <div class='closer_item'>
                <select multiple="multiple" name="transaction_filter_sybmols_value[]" id="transaction_filter_symbols_value">
                    {foreach from=$SYMBOLS key=k item=v}
                        <option value="{$k}" selected>{$v}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class='open_closer'>
            <div class='title_bar'>Security Types</div>
            <div class='closer_item'>
                <select multiple="multiple" name="transaction_filter_security_types_value[]" id="transaction_filter_security_types_value">
                    {foreach from=$SECURITY_TYPES key=k item=v}
                        <option value="{$k}" selected>{$v}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class='open_closer'>
            <div class='title_bar'>Dates</div>
            <div class='closer_item'>
               Start Date <input type="text" name="transaction_filter_start_date" id="transaction_filter_start_date" value="{$DATES.inception_date}"/>
               End Date <input type="text" name="transaction_filter_end_date" id="transaction_filter_end_date" value="{$DATES.last_trade_date}" />
            </div>
        </div>
    </div>
    <input type="button" class="filter_transactions" name="filter_transactions" value='Filter Transactions' />
</div>
<div style="clear:both"></div>
<div class="transactions_navigation_content"></div>
