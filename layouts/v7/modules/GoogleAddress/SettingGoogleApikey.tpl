{*<!--
/* ********************************************************************************
* The content of this file is subject to the Google Address ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
{strip}
    <div id="massEditContainer" class='modal-dialog modal-lg' style="width: 600px;">
        <div class="modal-header">
            <div class="clearfix">
                <div class="pull-right "><button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true" class="fa fa-close"></span></button>
                </div><h4 class="pull-left">Google API-KEY</h4>
            </div>
        </div>
        <form class="form-horizontal" id="editGoogleApiKey">
            <input type="hidden" name="mode" value="saveGoogleApiKey">
            <div name='massEditContent' class="row-fluid">
                <div class="modal-body">
                    <div class="control-group">
                        <label class="muted control-label">
                            <span class="redColor">*</span>&nbsp;Google API-KEY
                        </label>
                        <div class="controls row-fluid">
                            <input name="google-apikey" class="inputElement" type="text" value="{$GOOGLE_APIKEY}" >
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <center>
                    <button class="btn btn-success" type="submit" name="saveButton"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
                    <a href="#" class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                </center>
            </div>
        </form>
    </div>
{/strip}