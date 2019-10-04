{*<!--
/* ********************************************************************************
* The content of this file is subject to the Profit Calculator ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
<div class="detailViewContainer summaryWidgetContainer">
    <div class="editViewPageDiv">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="contents">
                <div class="form-horizontal">
                    <h4>{vtranslate('GoogleRadiusMaps', 'GoogleRadiusMaps')}</h4>
                </div>
                <hr>
                <br>
                <div class="detailViewInfo">
                    <div class="row form-group">
                        <div class="col-lg-4 control-label fieldLabel">
                            <label>Enable</label>
                        </div>
                        <div class="col-lg-4 input-group">
                            <input type="checkbox" name="enable_module" id="enable_module" value="1" {if $ENABLE eq '1'}checked="" {/if}/>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-lg-4 control-label fieldLabel">
                            <label>{vtranslate('LBL_MAP_CENTER', 'GoogleRadiusMaps')}</label>
                        </div>
                        <div class="col-lg-4 input-group">
                            <select class="select2" id="slbMapCenter">
                                <option value="Company Address" {if $MAP_CENTER eq 'Company Address'}selected {/if}>Company Address</option>
                                <option value="User Address" {if $MAP_CENTER eq 'User Address'}selected {/if}>User Address</option>
                                <option value="Current Location" {if $MAP_CENTER eq 'Current Location'}selected {/if}>Current Location</option>
                                <option value="Zip Code" {if $MAP_CENTER eq 'Zip Code'}selected {/if}>Zip Code</option>
                            </select>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-lg-4 control-label fieldLabel">
                            <label>{vtranslate('LBL_RADIUS', 'GoogleRadiusMaps')}</label>
                        </div>
                        <div class="col-lg-4 input-group">
                            <select class="select2" id="slbRadiusUnit">
                                <option value="miles" {if $RADIUS_UNIT eq 'miles'}selected {/if}>miles</option>
                                <option value="km" {if $RADIUS_UNIT eq 'km'}selected {/if}>km</option>
                            </select>
                            <hr>
                            <div class="input-group inputElement">
                                <input class="inputElement" type="number" id="txtRadiusNumber" value="{$RADIUS_NUMBER}"/>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-lg-4 control-label fieldLabel">
                            <label></label>
                        </div>
                        <div class="col-lg-4 input-group">
                            <button class="btn btn-success" name="btnGRMSettingSave" id="btnGRMSettingSave" type="button"><strong>{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</strong></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>