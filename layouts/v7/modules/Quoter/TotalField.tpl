{*/* * *******************************************************************************
* The content of this file is subject to the Quoter ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <tr class="totalField">
        <td width="5%" style="vertical-align: middle; text-align: center;">
            <img src="layouts/vlayout/skins/images/drag.png" class="moveIcon" border="0" title="Drag" style="cursor: move;">
            &nbsp;&nbsp;
            <i class="fa fa-trash deleteTotalRow cursorPointer" title="{vtranslate('LBL_DELETE',$MODULE)}"></i>
        </td>
        <td class="fieldValue medium" width="20%" style="vertical-align:middle;">
            <input type="hidden" class="fieldName" value="{$FIELD_NAME}">
            <input type="text" name="totalLabel{$INDEX_TOTAL}" maxlength="40" class="fieldLabel inputElement"  data-rule-required="true" value="{vtranslate($FIELD_VALUE.fieldLabel,'Quoter')}" {if $FIELD_VALUE.isDefault} disabled="disabled"  {/if}>
            <span class="dropdown">
                <a class="dropdown-toggle fieldInfo" data-toggle="dropdown" href="#" title="Show field name"><span class="fa fa-info-circle"></span></a>
                <ul class="dropdown-menu _tooltip">
                    <span style="color: #000">${$FIELD_NAME}$</span>
                </ul>
            </span>
        </td>
        <td class="fieldValue medium">
            <textarea rows="2" class="fieldFormula inputElement textAreaElement">{$FIELD_VALUE.fieldFormula}</textarea>
        </td>
        <td class="fieldValue " width="10%" style="text-align: center;">
            <input type="checkbox" class="fieldType " {if $FIELD_VALUE.fieldType eq 1} checked="" {/if}/>
        </td>
        <td class="fieldValue " width="13%" style="text-align: center;">
            <input type="checkbox" class="isRunningSubTotal " {if $FIELD_VALUE.isRunningSubTotal eq 1} checked="" {/if}/>
        </td>
    </tr>
{/strip}