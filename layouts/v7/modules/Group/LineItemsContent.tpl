
{assign var="hdnItemId" value="itemid"|cat:$row_no}
{assign var="portfolioid" value="portfolioid"|cat:$row_no}
{assign var="portfolioname" value="portfolioname"|cat:$row_no}
{assign var="billingspecificationid" value="billingspecificationid"|cat:$row_no}
{assign var="billingspecificationname" value="billingspecificationname"|cat:$row_no}
{assign var="active" value="active"|cat:$row_no}

{assign var=displayId value=$data.$hdnItemId} 
   
<td class="" >
	<i class="fa fa-trash deleteRow cursorPointer" title="{vtranslate('LBL_DELETE',$MODULE)}"></i>
	<input type="hidden" class="rowNumber" value="{$row_no}" />
</td>
<td class="schedule">
	<!--<input name="{$portfolioid}" type="text" value="{$data.$portfolioid}" style="display:none;" class="inputElement" id="{$portfolioid}" />-->
	<input name="popupReferenceModule" type="hidden" value="PortfolioInformation" />
	<div class="input-group">
        <input name="{$portfolioid}" type="hidden" value="{$data.$portfolioid}" class="sourceField portfolio"  id="{$portfolioid}" />
        <input id="{$portfolioid}_display" name="{$portfolioid}_display" data-fieldtype="reference" type="text" 
            class="marginLeftZero autoComplete1 inputElement" 
            value="{$data.$portfolioname}" 
            placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}"
            {if $data.$portfolioid neq 0}disabled="disabled"{/if}  
        />
        <a href="#" class="clearReferenceSelection portfolioClearReferenceSelection {if $data.$portfolioid eq 0}hide{/if}"> x </a>
        <span class="input-group-addon relatedPopup cursorPointer" title="{vtranslate('LBL_SELECT', $MODULE)}">
            <i id="{$MODULE}_editView_fieldName_{$portfolioid}_select" class="fa fa-search"></i>
        </span>
    </div>
</td>

<td>
	<!--<input id="{$billingspecificationid}" type="text" style="display:none;" class="inputElement" name="{$billingspecificationid}" value="{$data.$billingspecificationid}" />-->	
	<input name="popupReferenceModule" type="hidden" value="BillingSpecifications" />
	<div class="input-group">
        <input name="{$billingspecificationid}" type="hidden" value="{$data.$billingspecificationid}" class="sourceField"  id="{$billingspecificationid}" />
        <input id="{$billingspecificationid}_display" name="{$billingspecificationid}_display" data-fieldtype="reference" type="text" 
	        class="marginLeftZero autoComplete1 inputElement" 
	        value="{$data.$billingspecificationname}" 
	        placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}"
	        {if $data.$billingspecificationid neq 0}disabled="disabled"{/if}  
        />
        <a href="#" class="clearReferenceSelection {if $data.$billingspecificationid eq 0}hide{/if}"> x </a>
        <span class="input-group-addon relatedPopup cursorPointer" title="{vtranslate('LBL_SELECT', $MODULE)}">
            <i id="{$MODULE}_editView_fieldName_{$billingspecificationid}_select" class="fa fa-search"></i>
        </span>
    </div>
</td>

<td class="">
	<input id="{$active}" type="checkbox" class="inputElement" name="{$active}" {if $data.$active}checked{/if} value="1" />	
</td>

