<div id="GHReport_wrapper">
    <table id="mom_header" style="height:100px;">
        <tr>
            <td style="width:30%">{if $LOGO neq ''}<img class="pdf_crm_logo" src="{$LOGO}" width="100%"/>{/if}</td>
            <td style="width:50%; text-align:center;"><h1>{$PREPARED_FOR}</h1></td>
            <td style="width:20%; font-size: 18px; font-weight:bold;">
                Prepared By: {$PREPARED_BY}<br />
                {*                {$USER_DATA['first_name']} {$USER_DATA['last_name']}<br />*}
                {*                {if $USER_DATA['title'] neq ''}{$USER_DATA['title']}<br />{/if}
                                {if $USER_DATA['email1'] neq ''}{$USER_DATA['email1']}<br />{/if}
                                {if $USER_DATA['phone_work'] neq ''}{$USER_DATA['phone_work']}{/if}*}
            </td>
        </tr>
    </table>
    {*    <div class="GHReport_section">
            <h2 class="blue_header">PLAN GOALS AND ASSUMPTIONS</h2>
            <p>Report Notes:</p>
            {$PERSONAL_NOTES}
        </div>*}
    <div class="GHReport_section">
        <h2 class="blue_header padded_heading">TRAILING MONTH TO MONTH YEARLY INCOME</h2>
        <table style="width:100%" id="month_over_month">
            <thead>
            <tr>
                <td style="text-align:center;">Year</td>
                <td style="text-align:center;">January</td>
                <td style="text-align:center;">February</td>
                <td style="text-align:center;">March</td>
                <td style="text-align:center;">April</td>
                <td style="text-align:center;">May</td>
                <td style="text-align:center;">June</td>
                <td style="text-align:center;">July</td>
                <td style="text-align:center;">August</td>
                <td style="text-align:center;">September</td>
                <td style="text-align:center;">October</td>
                <td style="text-align:center;">November</td>
                <td style="text-align:center;">December</td>
            </tr>
            </thead>
            <tbody>
            {foreach from=$YEARS key=k item=v}
                <tr>
                    <td style="font-weight:bold; border-top:2px solid black;">DOW</td>
                    {for $x=1 to 12}
                        <td style="text-align:right; border-top:2px solid black;">{$DOW_PRICES.$v.$x.close|number_format:0}</td>
                    {/for}
                </tr>
                <tr>
                    <td style="font-weight:bold">{$v}</td>
                    {for $x=1 to 12}
                        {assign var='SET_TD' value=0}
                        {foreach from=$MOM_TABLE key=mk item=mv}
                            {if $mv.year eq $v AND $mv.month eq $x}
                                {assign var='SET_TD' value=1}
                                <td style="text-align:right;">${$mv.monthovermonth|number_format:0}</td>
                            {/if}
                        {/foreach}
                        {if $SET_TD eq 0}
                            <td style="text-align:center;">-</td>
                        {/if}
                        {assign var='SET_TD' value=0}
                    {/for}
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>