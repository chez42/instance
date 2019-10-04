{*<!--
/* ********************************************************************************
* The content of this file is subject to the Profit Calculator ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate('GoogleRadiusMaps', 'GoogleRadiusMaps')}</h3>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="summaryWidgetContainer">
        <div class="row-fluid">
            <span class="span3"><h4>Enable {vtranslate('GoogleRadiusMaps', 'GoogleRadiusMaps')}</h4></span>
            <input type="checkbox" name="enable_module" id="enable_module" value="1" {if $ENABLE eq '1'}checked="" {/if}/>
        </div>
        <div class="row-fluid" style="margin-top: 5px">
            <span class="span3"><h4>{vtranslate('LBL_MAP_CENTER', 'GoogleRadiusMaps')}</h4></span>
            <select class="select2" id="slbMapCenter">
                <option value="Company Address" {if $MAP_CENTER eq 'Company Address'}selected {/if}>Company Address</option>
                <option value="User Address" {if $MAP_CENTER eq 'User Address'}selected {/if}>User Address</option>
                <option value="Current Location" {if $MAP_CENTER eq 'Current Location'}selected {/if}>Current Location</option>
                <option value="Zip Code" {if $MAP_CENTER eq 'Zip Code'}selected {/if}>Zip Code</option>
            </select>
        </div>
        <div class="row-fluid" style="margin-top: 5px">
            <span class="span3"><h4>{vtranslate('LBL_RADIUS', 'GoogleRadiusMaps')}</h4></span>
            <select class="select2" id="slbRadiusUnit">
                <option value="miles" {if $RADIUS_UNIT eq 'miles'}selected {/if}>miles</option>
                <option value="km" {if $RADIUS_UNIT eq 'km'}selected {/if}>km</option>
            </select>
            <input class="" type="number" id="txtRadiusNumber" value="{$RADIUS_NUMBER}"/>
        </div>
        <div class="row-fluid">
            <span class="span3"><h4>{vtranslate('Is default view?', 'GoogleRadiusMaps')}</h4></span>
            <input type="checkbox" name="is_default_view" id="is_default_view" value="1" {if $IS_DEFAULT_VIEW eq '1'}checked="" {/if}/>
        </div>
        <div class="row-fluid" style="margin-top: 5px">
            <span class="span3"><h4>&nbsp;</h4></span>
            <button class="btn btn-success" name="btnGRMSettingSave" id="btnGRMSettingSave" type="button"><strong>{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</strong></button>
        </div>
    </div>
</div>