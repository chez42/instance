<div class="row-fluid">
    <div class="span6">
        <div class="pull-right">
            <div class="btn-toolbar margin_right" style="display:block;">
                    <span class="btn-group">
                        <button class="btn ExportReport" style="display:block;"><strong>Generate PDF</strong></button>
                    </span>
            </div>
            <form method="post" id="export">
                        <input type="hidden" value='{$ACCOUNT_NUMBER}' name="account_number" id="account_number" />
                        <input type="hidden" value="PortfolioInformation" name="module" />
                        <input type="hidden" value="" name="pie_image" id="pie_image" />
                        <input type="hidden" value="" name="sector_pie_image" id="sector_pie_image" />
                        <input type="hidden" value="MonthOverMonth" name="view" />
                        <input type="hidden" value="{$ORIENTATION}" name="orientation" />
                        <input type="hidden" value="1" name="pdf" />
                        <input type="hidden" value="{$CALLING_RECORD}" name="calling_record" />
                        <input type="hidden" value="{$START_DATE}" name="report_start_date" />
                        <input type="hidden" value="{$END_DATE}" name="report_end_date" />
            </form>
        </div>
    </div>
</div>

<div class="row-fluid" style="display:block; clear:both;">
    <div id="month_over_month_wrapper" class="margin_right">
    {*    <div class="GHReport_section">
            <h2 class="blue_header">MONTH OVER MONTH NOTES</h2>
            <p>Report Notes:</p>
            <textarea rows="5" name="personal_notes" form="export" style="font-size:14pt;"></textarea>
        </div>*}
        <div class="GHReport_section">
            <h2 class="blue_header padded_heading">TRAILING MONTH TO MONTH YEARLY INCOME</h2>
            <table style="width:100%" border="1" id="month_over_month">
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
                                {if $DOW_PRICES.$v.$x.close eq 0}
                                    <td style="text-align:center; border-top:2px solid black;">-</td>
                                {else}
                                    <td style="text-align:right; border-top:2px solid black;">{$DOW_PRICES.$v.$x.close|number_format:0}</td>
                                {/if}
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
</div>