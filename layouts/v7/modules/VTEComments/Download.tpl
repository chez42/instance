{*<!--
/* ********************************************************************************
* The content of this file is subject to the Google Drive Integration ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
{strip}
    <div class="contentsDiv marginLeftZero">
        <div class="padding1per">
            <div class="editContainer" style="padding-left: 3%; padding-right: 3%">
                <br>
                <h3>{vtranslate('VTEComments', 'VTEComments')} Down Load</h3>
                <hr>
                <form name="install" id="editLicense" method="POST" action="index.php" class="form-horizontal">
                    <input name="__vtrftk" value="sid:061d9d28a9d8812287ee37dc7f40b5b4f10483cc,1515473647" type="hidden">
                    <input name="module" value="PDFMaker" type="hidden">
                    <input name="view" value="List" type="hidden">
                    <div id="step1" class="padding1per" style="border:1px solid #ccc; padding-left: 10px; padding-bottom: 10px">
                        <input name="installtype" value="download_src" type="hidden">
                        <div class="controls">
                            <div>
                                <strong>In order to use Integration it's necessary to download and install DomPDF script.</strong>
                            </div>
                            <br>
                            <div class="clearfix"></div>
                        </div>
                        <div class="controls">
                            <div>
                                <p>Click on Download button to download and install
                                    <strong>
                                        <a href="http://dompdf.github.com/" target="_blank"> DOMPDF</a>
                                    </strong> script and to progress in installation. Please be patient it may take a while.
                                    <br>
                                    <br>In case that you are unable to automatically download DOMPDF, you can donwload it manually from
                                </p>
                                <input value="https://www.vtexperts.com/files/vtecomment_dompdf.zip" disabled="disabled" style="width: 30%;" type="url"><p style="padding-top: 5px">and unzip it into
                                </p>
                                <input value="modules/VTEComments/" disabled="disabled" style="width: 30%" type="text">
                            </div>
                            <br>
                            <div class="clearfix">
                            </div>
                        </div>
                        <div class="controls">
                            <span style="display: none; color: red;" class="download_api_button_processing">Downloading...</span>
                            <br>
                            <button type="button" id="download_api_button" class="btn btn-success">
                                <strong>Download</strong>
                            </button>&nbsp;&nbsp;
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/strip}