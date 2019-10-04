{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<div class="container-fluid">
    <form action="" method="post" name="macros_settings" id="">
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
            <tr>
                <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
                    <div align=center>
                        <table class="settingsSelUITopLine" align="center" border="0" cellpadding="5" cellspacing="0" width="100%">
                            <tr>
                                <td class="heading2" valign="bottom"> <b>{vtranslate('Macros', $MODULE)}</b></td>
                            </tr>
                            <tr>
                                <td><hr/><br/>The Macros extension has been activated</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </form>
    <div class="clearfix"></div>
    <div>
        <div style="padding: 10px; text-align: justify; font-size: 14px; border: 1px solid #ececec; border-left: 5px solid #2a9bbc; border-radius: 5px; overflow: hidden;">
            <h4 style="color: #2a9bbc; margin: 0px -15px 10px -15px; padding: 0px 15px 8px 15px; border-bottom: 1px solid #ececec;"><i class="fa fa-info-circle"></i>&nbsp;&nbsp;{vtranslate('LBL_INFO_BLOCK', $QUALIFIED_MODULE)}</h4>
            {vtranslate('LBL_INFO_BLOCK_ON_SETTING_PAGE_V6', $QUALIFIED_MODULE)}
        </div>
    </div>
</div>
{literal}
    <script>
        // setTimeout(function(){ window.location.href="index.php?module=Workflows&parent=Settings&view=List" }, 100);
    </script>
{/literal}