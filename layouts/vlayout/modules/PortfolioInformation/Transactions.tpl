<script src="libraries/jquery/jquery.cookie.js" type="text/javascript"></script>
<table class="table table-bordered listViewEntriesTable" style="border-top: 0px; border-top-right-radius: 0px; border-top-left-radius: 0px;" id="bottomwrapper">
	<thead>
		<tr>
			<td colspan="2">
				<div class="moduleName"><strong>As of: {$DATE}</strong></div>
			</td>
		</tr>
		<tr>
			<td>
				<h3>Portfolio Transactions</h3>
			</td>
			<td class="pull-right">
				
				<div class="pageNumbers alignTop pull-left">
					<span>
						<span class="pageNumbersText" style="padding-right:5px">{$StartRange} {vtranslate('LBL_to', $MODULE)} {$EndRange}</span>
						<span class="icon-refresh pull-right totalNumberOfRecords cursorPointer "></span>
					</span>
				</div>
				
				<div class="btn-group alignTop margin0px pull-left">
					<span class="pull-right">
						<span class="btn-group">
							<button class="btn" id="listViewPreviousPageButton"  disabled type="button"><span class="icon-chevron-left"></span></button>
								<button class="btn dropdown-toggle" type="button" id="listViewPageJump" data-toggle="dropdown" {if $PAGE_COUNT eq 1} disabled {/if}>
									<i class="vtGlyph vticon-pageJump" title="{vtranslate('LBL_LISTVIEW_PAGE_JUMP',$moduleName)}"></i>
								</button>
								<ul class="listViewBasicAction dropdown-menu" id="listViewPageJumpDropDown">
									<li>
										<span class="row-fluid">
											<span class="span3 pushUpandDown2per"><span class="pull-right">{vtranslate('LBL_PAGE',$moduleName)}</span></span>
											<span class="span4">
												<input type="text" id="pageToJump" class="listViewPagingInput" value="{$PAGE_NUMBER}"/>
											</span>
											<span class="span2 textAlignCenter pushUpandDown2per">
												{vtranslate('LBL_OF',$moduleName)}&nbsp;
											</span>
											<span class="span3 pushUpandDown2per" id="totalPageCount">{$NUMPAGES}</span>
										</span>
									</li>
								</ul>
							<button class="btn" id="listViewNextPageButton" {if (!$isNextPageExists) or ($PAGE_COUNT eq 1)} disabled {/if} type="button"><span class="icon-chevron-right"></span></button>
						</span>
					</span>	
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding: 0px;">
				<div id="portfolios">
				    <input type="hidden" name="direction" value="{$DIRECTION}" />
				    <input type="hidden" name="account_number" value="{$ACCOUNT_NUMBER}" />
				   	<table class="table listViewEntriesTable">
						<thead>
			          		<tr class="listViewHeaders">
			            		<th style="width: 8%;" class="lvtCol">
					                <a class="sorter" onclick="return false;">Trade Date</a>
					                <input type="hidden" class="order_by" value="trade_date" />
					                <input type="hidden" class="filter" value="{$FILTER}" />
					                <input type="hidden" class="account_number" value="{$ACCOUNT_NUMBER}" />
			            		</th>
					            <th class="lvtCol">
					                <a class="sorter" onclick="return false;">Activity</a>
					               <input type="hidden" class="order_by" value="activity_name" />
					               <input type="hidden" class="filter" value="{$FILTER}" />
					               <input type="hidden" class="account_number" value="{$ACCOUNT_NUMBER}" />
					            </th>
					            <th class="lvtCol">
					                <a class="sorter" onclick="return false;">Activity Type</a>
					                <input type="hidden" class="order_by" value="report_as_type_name" />
					                <input type="hidden" class="filter" value="{$FILTER}" />
					                <input type="hidden" class="account_number" value="{$ACCOUNT_NUMBER}" />
					            </th>
					            <th class="lvtCol">
					                <a class="sorter" onclick="return false;">Security Symbol</a>
					                <input type="hidden" class="order_by" value="security_symbol" />
					                <input type="hidden" class="filter" value="{$FILTER}" />
					                <input type="hidden" class="account_number" value="{$ACCOUNT_NUMBER}" />
					            </th>
					            <th class="lvtCol">
					                <a class="sorter" onclick="return false;">Description</a>
					               <input type="hidden" class="order_by" value="description" />
					               <input type="hidden" class="filter" value="{$FILTER}" />
					               <input type="hidden" class="account_number" value="{$ACCOUNT_NUMBER}" />
					            </th>
					            <th class="lvtCol">
					                <a class="sorter" onclick="return false;">Security Type</a>
					                <input type="hidden" class="order_by" value="code_description" />
					                <input type="hidden" class="filter" value="{$FILTER}" />
					                <input type="hidden" class="account_number" value="{$ACCOUNT_NUMBER}" />
					            </th>
					            <th class="lvtCol">
					                <a class="sorter" onclick="return false;">Detail</a>
					                <input type="hidden" class="order_by" value="transaction_description" />
					                <input type="hidden" class="filter" value="{$FILTER}" />
					                <input type="hidden" class="account_number" value="{$ACCOUNT_NUMBER}" />
					            </th>
					            <th class="lvtCol">
					                <a class="sorter" onclick="return false;">Quantity</a>
					                <input type="hidden" class="order_by" value="quantity" />
					                <input type="hidden" class="filter" value="{$FILTER}" />
					                <input type="hidden" class="account_number" value="{$ACCOUNT_NUMBER}" />
					            </th>
					            <th class="lvtCol">
					                <a class="sorter" onclick="return false;">Price</a>
					                <input type="hidden" class="order_by" value="current_price" />
					                <input type="hidden" class="filter" value="{$FILTER}" />
					                <input type="hidden" class="account_number" value="{$ACCOUNT_NUMBER}" />
					            </th>
					            <th class="lvtCol">
					                <a class="sorter" onclick="return false;">Amount</a>
					                <input type="hidden" class="order_by" value="net_amount" />
					                <input type="hidden" class="filter" value="{$FILTER}" />
					                <input type="hidden" class="account_number" value="{$ACCOUNT_NUMBER}" />
					            </th>
					            <th class="lvtCol">
					                <a class="sorter" onclick="return false;">Fee</a>
					                <input type="hidden" class="order_by" value="advisor_fee" />
					                <input type="hidden" class="filter" value="{$FILTER}" />
					                <input type="hidden" class="account_number" value="{$ACCOUNT_NUMBER}" />
					            </th>
					            <th class="lvtCol">
					                <a class="sorter" onclick="return false;">Origination</a>
					                <input type="hidden" class="order_by" value="origination" />
					                <input type="hidden" class="filter" value="{$FILTER}" />
					                <input type="hidden" class="account_number" value="{$ACCOUNT_NUMBER}" />
					            </th>
          					</tr>
          				</thead>
      	 				{foreach from=$TRANSACTIONINFO key=k item=v}
			              	<tr>
			               		<td>{$v.trade_date_display}</td>
			                    <td>{$v.activity_name}</td>
			                    <td>{$v.report_as_type_name}</td>
			                    <td>{$v.security_symbol}</td>
			                    <td>{$v.description}</td>
			                    <td>{$v.code_description}</td>
			                    <td>{$v.transaction_description}</td>
			                    <td>{$v.quantity|number_format:2}</td>
			                    <td>${$v.current_price|number_format:2}</td>
			                    <td>${$v.value|number_format:2}</td>
			                    <td>${$v.advisor_fee|number_format:2}</td>
			                    <td>{$v.origination}</td>
			            	</tr>
       					{/foreach}
        			</table>
    			</div>
			</td>
		</tr>
		<!--  <tr>
			<td class="fieldValue" colspan="2" style="text-align: center;background-color: #f5f5f5;">
				<form method="post" action="" name="portfolioform">
				<!--index.php?module={$MODULES}&action={$ACTION}&security_id={$SECURITYID}&m={$smarty.get.m}&order_by={$ORDER}&parenttab=Sales&record={$ACCOUNTNUMBER}&acct_number={$ACCTNUM}&direction={$DIRECTION}&pagenumber=1&searchtype={$SEARCHTYPE}&searchtext={$SEARCHCONTENT}&filter={$FILTER}"
					<a class='pagination' href="#" onclick="return false;">
			            <img src="themes/images/start.gif" border="0" />
			            <input type="hidden" class='nav_arrow' value="first" />
			            <input type="hidden" class="page_number" value="1" />
			            <input type="hidden" class="filter" value="{$FILTER}" />
			            <input type="hidden" class="searchtype" value="{$SEARCHTYPE}" />
			            <input type="hidden" class="searchtext" value="{$SEARCHCONTENT}" />
			            <input type="hidden" class="order_by" value="{$ORDER}" />
			            <input type="hidden" class="numresults" value="{$NUMRESULTS}" />
			        </a>
					<a class='pagination' href="#" onclick="return false;">
			            <img src="themes/images/previous.gif" border="0"/>
			            <input type="hidden" class='nav_arrow' value="back" />
			            <input type="hidden" class="page_number" value="{$PREVPAGE}" />
			            <input type="hidden" class="filter" value="{$FILTER}" />
			            <input type="hidden" class="searchtype" value="{$SEARCHTYPE}" />
			            <input type="hidden" class="searchtext" value="{$SEARCHCONTENT}" />
			            <input type="hidden" class="order_by" value="{$ORDER}" />
			            <input type="hidden" class="numresults" value="{$NUMRESULTS}" />
			        </a>
			        <!--"index.php?module={$MODULES}&action={$ACTION}&security_id={$SECURITYID}&m={$smarty.get.m}&order_by={$ORDER}&parenttab=Sales&record={$ACCOUNTNUMBER}&acct_number={$ACCTNUM}&direction={$DIRECTION}&pagenumber={$PREVPAGE}&searchtype={$SEARCHTYPE}&searchtext={$SEARCHCONTENT}&filter={$FILTER}"
        			<span id="directselect">
			            <select name="pagenumber" id="pagenumber" class="chzn-select">
			                {section name=pageloop start=1 loop=$NUMPAGES+1 step=1}
			                    <option value="{$smarty.section.pageloop.index}" {if $CURRENTPAGE eq $smarty.section.pageloop.index} selected="{$CURRENTPAGE}"{/if}>{$smarty.section.pageloop.index}</option>
			                {/section}
			            </select>
			            <input type="hidden" class="filter" value="{$FILTER}" />
			            <input type="hidden" class="searchtype" value="{$SEARCHTYPE}" />
			            <input type="hidden" class="searchtext" value="{$SEARCHCONTENT}" />
			            <input type="hidden" class="order_by" value="{$ORDER}" />
			            <input type="hidden" class="numresults" value="{$NUMRESULTS}" />
			        </span>
					<a class='pagination' href="#" onclick="return false;">
			            <img src="themes/images/next.gif" border="0"/>
			            <input type="hidden" class='nav_arrow' value="back" />
			            <input type="hidden" class="page_number" value="{$NEXTPAGE}" />            
			            <input type="hidden" class="filter" value="{$FILTER}" />
			            <input type="hidden" class="searchtype" value="{$SEARCHTYPE}" />
			            <input type="hidden" class="searchtext" value="{$SEARCHCONTENT}" />
			            <input type="hidden" class="order_by" value="{$ORDER}" />
			            <input type="hidden" class="numresults" value="{$NUMRESULTS}" />
			        </a>
					<a class='pagination' href="#" onclick="return false;">
			            <img src="themes/images/end.gif" border="0"/>
			            <input type="hidden" class='nav_arrow' value="back" />
			            <input type="hidden" class="page_number" value="{$NUMPAGES}" />
			            <input type="hidden" class="filter" value="{$FILTER}" />
			            <input type="hidden" class="searchtype" value="{$SEARCHTYPE}" />
			            <input type="hidden" class="searchtext" value="{$SEARCHCONTENT}" />
			            <input type="hidden" class="order_by" value="{$ORDER}" />
			            <input type="hidden" class="numresults" value="{$NUMRESULTS}" />
			        </a>
					Results Per Page <input name="numresults" type="text" value="{$NUMRESULTS}" size="5" class="input-append" />
					<span class="showresults">
			            <input id="showresults" type="button" value="Show Results" class="btn"/>
			            <input type="hidden" class="page_number" value="{$NUMPAGES}" />
			            <input type="hidden" class="filter" value="{$FILTER}" />
			            <input type="hidden" class="searchtype" value="{$SEARCHTYPE}" />
			            <input type="hidden" class="searchtext" value="{$SEARCHCONTENT}" />
			            <input type="hidden" class="order_by" value="{$ORDER}" />
			            <input type="hidden" class="numresults" value="{$NUMRESULTS}" />
			        </span>
				</form>
			</td>
		</tr>-->
	</thead>
</table>	
        
{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}
