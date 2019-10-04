{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is: vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
{strip}
    <div class="editContainerSetting" style="padding-left: 2%;padding-right: 2%">
        <h4 class="bold margin-bottom-20">Star Translation</h4>
        <form id="FeedbackSetting">
            <table id="feedback-start-translation" class="margin-bottom-20">
                <tr>
                    <td class="image-star">
                        <img alt="One Star" src="layouts/v7/modules/VTEFeedback/resources/image/onestar.jpg"/>
                    </td>
                    <td class="input-star">
                        <input name="onestar" class="form-control" type="text" value="{$DATA['onestar']}">
                    </td>
                </tr>
                <tr>
                    <td class="image-star">
                        <img alt="One Star" src="layouts/v7/modules/VTEFeedback/resources/image/twostar.jpg"/>
                    </td>
                    <td class="input-star">
                        <input name="twostar" class="form-control" type="text" value="{$DATA['twostar']}">
                    </td>
                </tr>
                <tr>
                    <td class="image-star">
                        <img alt="One Star" src="layouts/v7/modules/VTEFeedback/resources/image/threestar.jpg"/>
                    </td>
                    <td class="input-star">
                        <input name="threestar" class="form-control" type="text" value="{$DATA['threestar']}">
                    </td>
                </tr>
                <tr>
                    <td class="image-star">
                        <img alt="One Star" src="layouts/v7/modules/VTEFeedback/resources/image/fourstar.jpg"/>
                    </td>
                    <td class="input-star">
                        <input name="fourstar" class="form-control" type="text" value="{$DATA['fourstar']}">
                    </td>
                </tr>
                <tr>
                    <td class="image-star">
                        <img alt="One Star" src="layouts/v7/modules/VTEFeedback/resources/image/fivestar.jpg"/>
                    </td>
                    <td class="input-star">
                        <input name="fivestar" class="form-control" type="text" value="{$DATA['fivestar']}">
                    </td>
                </tr>
            </table>

            <h4 class="bold">First Text Block</h4>
            <textarea title="First Text" name="firsttext" class="firsttext form-control margin-bottom-20"  cols rows="3">
                {$DATA['firsttext']}
            </textarea>

            <h4 class="bold">Second Text Block</h4>
            <textarea title="First Text" name="secondtext" class="secondtext form-control margin-bottom-20" cols rows="3" >
                {$DATA['secondtext']}
            </textarea>
            <input type="button" class="save_setting btn btn-success" value="Save"/>
        </form>
    </div>
{/strip}