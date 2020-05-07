<div class="PortfolioInformationAssetAllocationReport" style="display:block; width:100%;
                            box-shadow:
                            -6px -6px 8px -4px rgba(224, 224, 235,0.75),
                            6px -6px 8px -4px rgba(224, 224, 235,0.75),
                            6px 6px 8px -4px rgba(255,255,0,0.75),
                            6px 6px 8px -4px rgba(0,0,255,2.75);
                            border-radius:5px;
                            background-color:#f2f2f2;">

    <input type="hidden" value='{$DYNAMIC_PIE}' id="estimate_pie_values" />

    <div class="row-fluid ReportTitle detailViewTitle">
        <div class=" span12 ">
            <div class="row-fluid">
                <div class="span6">
                    <div class="row-fluid">
                        <span class="recordLabel font-x-x-large textOverflowEllipsis span pushDown"><span>Holdings</span>&nbsp;</span>
                    </div>
                </div>
                <div class="span6">
                    <div class="pull-right">
                        <div class="btn-toolbar">
							<span class="btn-group">
								<button class="btn ExportReport"><strong>Generate PDF</strong></button>
							</span>
                        </div>
                        <form method="post" id="export">
                            <input type="hidden" value='{$ACCOUNT_NUMBER}' name="account_number" id="account_number" />
                            <input type="hidden" value="PortfolioInformation" name="module" />
                            <input type="hidden" value="AssetClassReport" name="view" />
                            <input type="hidden" value="" name="pie_image" id="pie_image" />
                            <input type="hidden" value="1" name="pdf" />
                            <input type="hidden" value="{$CALLING_RECORD}" name="calling_record" />
                            <input type="hidden" value="{$END_DATE}" name="report_end_date" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="detailViewInfo row-fluid">
        <div class="contents">
            <div class="row-fluid">
                <div class=" span12 ">
                    <div class=" span2 ">&nbsp;</div>
                    <div class=" span8 ">
                        <div id="dynamic_pie" style="display:block; width:50%; margin-left:auto; margin-right:auto;
                            box-shadow:
                            -6px -6px 8px -4px rgba(224, 224, 235,0.75),
                            6px -6px 8px -4px rgba(224, 224, 235,0.75),
                            6px 6px 8px -4px rgba(255,255,0,0.75),
                            6px 6px 8px -4px rgba(0,0,255,2.75);
                            border-radius:5px;
                            background-color:#f0f0f5;">
                            <div id="dynamic_pie_holder" class="report_top_pie" style="height: 320px; margin-bottom:25px;"></div>
                        </div>
                        {if $MARGIN_BALANCE neq 0}
                            <tr>
                                <td>
                                    <p>Margin Balance: <span style="{if $MARGIN_BALANCE lt 0}color:red;{else}color:green;{/if}">${$MARGIN_BALANCE|number_format:2}</span><span style="font-size:8px;">&nbsp;&nbsp;(represented by $CASH)</span></p>
                                </td>
                            </tr>
                        {/if}
                        {if $NET_CREDIT_DEBIT neq 0}
                            <tr>
                                <td>
                                    <p>Net Credit Debit: <span style="{if $NET_CREDIT_DEBIT lt 0}color:red;{else}color:green;{/if}">${$NET_CREDIT_DEBIT|number_format:2}</span><span style="font-size:8px;">&nbsp;&nbsp;(represented by $CASH)</span></p>
                                </td>
                            </tr>
                        {/if}
                        {if $UNSETTLED_CASH neq 0}
                            <tr>
                                <td>
                                    <p>Unsettled Cash: <span style="{if $UNSETTLED_CASH lt 0}color:red;{else}color:green;{/if}">${$UNSETTLED_CASH|number_format:2}</span><span style="font-size:8px;">&nbsp;&nbsp;(represented by $CASH)</span></p>
                                </td>
                            </tr>
                        {/if}
                    </div>
                    <div class=" span2 ">&nbsp;</div>
                </div>

                {assign var='DYNATABLE' value=$ESTIMATE_TABLE}
                {assign var='DYNAHEADINGS' value=$DYNATABLE['table_headings']}
                {assign var='DYNAROWS' value=$DYNATABLE['table_values']['rows']}
                {assign var='DYNARULES' value=$DYNATABLE['table_values']['rules']}
                {assign var='DYNACATEGORIES' value=$DYNATABLE['table_categories']}
                {assign var='DYNATOTALS' value=$DYNATABLE['TableTotals']}

                <table class="table table-bordered DynaTable table-collapse">
                    <thead>
                    <tr>
                        {foreach from=$DYNAHEADINGS key=k item=heading}
                            <th style="{$heading['heading_td_style']}"><span style="{$heading['heading_span_style']}">{$heading['heading']}</span></th>
                        {/foreach}
                    </tr>
                    </thead>
                    <tbody>
                    {assign var=CatCount value='0'}
                    {foreach from=$DYNACATEGORIES item=CatArray}
                        {counter start=1 print=false assign='count' name='category_count'}
                        {counter start=0 print=false assign='indenting' name='category_indenting'}
                        {foreach from=$CatArray key=k item=cat}
                            {if $k neq 'category_id' AND $k neq 'totals'}
                                {assign var=CatCount value=$CatCount+1}
                                {if $cat != ''}
                                    <tr data-toggle="collapse" id="asset_cat_{$CatCount}" data-target=".asset_cat_{$CatCount}">
                                        <td><i class="fa fa-plus"></i>&nbsp;<strong>{$cat}</strong></td>
                                        {foreach from=$DYNAHEADINGS key=a item=heading}
                                            {if $a neq 'heading'}
                                                {if $DYNARULES[$a]['cat_smarty_modifier'] != ''}
                                                    {capture assign=test}
                                                        <span style="{$DYNARULES[$a]['cat_span_style']}">{$DYNARULES[$a]['cat_prefix']}{ldelim}$CatArray['totals'][$a]|{$DYNARULES[$a]['cat_smarty_modifier']}{rdelim}{$DYNARULES[$a]['cat_suffix']}</span>
                                                    {/capture}
                                                    <td style="{$DYNARULES[$a]['cat_td_style']}">
                                                        {if $CatArray['totals'][$a] neq ''}
                                                            {eval var=$test}
                                                        {/if}
                                                    </td>
                                                {else}
                                                    <td style="{$DYNARULES[$a]['cat_td_style']}">
                                                        {if $CatArray['totals'][$a] neq ''}
                                                            <span style="{$DYNARULES[$a]['cat_span_style']}">{$DYNARULES[$a]['cat_prefix']}{$CatArray['totals'][$a]}{$DYNARULES[$a]['cat_suffix']}</span>
                                                        {/if}
                                                    </td>
                                                {/if}
                                            {/if}
                                        {/foreach}
                                    </tr>
                                {/if}
                                {foreach from=$DYNAROWS item=r}
                                    {foreach from=$r key=index item=row}
                                        {if $index eq 'fields' AND $r['category_id'] eq $CatArray['category_id'] AND $count eq $CatArray|@count-2}
                                            <tr class="holdings collapse asset_cat_{$CatCount}">
                                                {foreach from=$row key=k item=v}
                                                    {if $DYNARULES[$k]['smarty_modifier'] != ''}
                                                        {capture assign=test}
                                                            {$DYNARULES[$k]['prefix']}{ldelim}$v|{$DYNARULES[$k]['smarty_modifier']}{rdelim}{$DYNARULES[$k]['suffix']}
                                                        {/capture}
                                                        <td style="{$DYNARULES[$k]['value_td_style']}">
                                                            {eval var=$test}
                                                        </td>
                                                    {else}
                                                        <td style="{$DYNARULES[$k]['value_td_style']}">
                                                            <span style="{$DYNARULES[$k]['value_span_style']}">{$DYNARULES[$k]['prefix']}{$v}{$DYNARULES[$k]['suffix']}</span>
                                                        </td>
                                                    {/if}
                                                {/foreach}
                                            </tr>
                                        {/if}
                                    {/foreach}
                                {/foreach}
                            {/if}
                            {counter name='category_count'}
                        {/foreach}
                    {/foreach}
                    <tr>
                        {foreach from=$DYNAHEADINGS key=a item=heading}
                            {assign var='VALSET' value=0}{*This is needed so we can appropriately place empty <td>'s if the total heading doesn't match the heading*}
                            {foreach from=$DYNATOTALS key=k item=v}
                                {if $a eq $k}
                                    {if $DYNARULES[$k]['total_smarty_modifier'] != ''}
                                        {capture assign=test}
                                            {$DYNARULES[$k]['total_prefix']}{ldelim}$v|{$DYNARULES[$k]['total_smarty_modifier']}{rdelim}{$DYNARULES[$k]['total_suffix']}
                                        {/capture}
                                        <td style="{$DYNARULES[$k]['total_td_style']};">
                                            {if $DYNARULES[$k]['hide_from_total'] neq 1}
                                                {eval var=$test}
                                            {/if}
                                        </td>
                                    {else}
                                        <td style="{$DYNARULES[$k]['total_td_style']};">
                                            {if $DYNARULES[$k]['hide_from_total'] neq 1}
                                                <span style="{$DYNARULES[$k]['total_span_style']}">{$DYNARULES[$k]['total_prefix']}{$v}{$DYNARULES[$k]['total_suffix']}</span>
                                            {/if}
                                        </td>
                                    {/if}
                                    {assign var='VALSET' value=1}
                                {/if}
                            {/foreach}
                            {if $VALSET eq 0}
                                <td style="border-top:1px dotted black;">&nbsp;</td>
                            {/if}
                        {/foreach}
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>