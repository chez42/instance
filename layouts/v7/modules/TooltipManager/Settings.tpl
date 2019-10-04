{literal}
    <style>
        #field_list thead tr td {font-weight:bold;}
        .image {
            width: 25px;
            height: 25px;
            overflow: hidden;
            cursor: pointer;
            color: #fff;
        }
        .image img {
            visibility: hidden;
        }
    </style>
{/literal}

<script src="libraries/jquery/ckeditor/ckeditor.js"></script>
{*<script src="layouts/v7/modules/TooltipManager/resources/migrate_jquery.js"></script>*}

<div style="padding:20px;">
    <div class="widget_header row-fluid" style="padding-bottom: 20px;">
        <div class="span12">
            <h3>
                {$MODULE_LBL}
            </h3>
        </div>
    </div>

    <form method="post" action="">

        <div style="clear:both;"></div>
        <table style="width:100%" cellpadding="10" id="field_list">
            <tr>
                <td style="padding: 10px" >&nbsp;</td>
                <td valign="top" style="padding: 10px">
                    <select class="chzn-select select2" name="" id="selected_module" onchange="selectModule(this)" style="width: 200px;">
                        <option value="">--Select Module--</option>
                        {foreach item=MODULE from=$MODULE_LIST}
                            <option value="{$MODULE.tabid}" {if $SELECTED_MODULE eq $MODULE.tabid}selected{/if}>

                                {$MODULE.tablabel|getTranslatedString:$MODULE["name"]}

                            </option>
                        {/foreach}
                    </select>
                </td>
                <td valign="top" colspan="2" style="padding: 10px">
                    <p style="text-align:justify">
                        Tooltip Manager will allow you to create custom field tooltips and display them as a popup or as a regular tooltip. </br></br>
                        1. Select the Module </br>
                        2. Select the Field to add the Tooltip to.</br>
                        3. Select the Tooltip type(Tooltip - "regular" Tooltip. Popup - a Tooltip that is displayed as a Popup)</br>
                        4. Click on the Icon to add a new Icon or select from existing one.</br>
                        5. Define the Tooltip content, Preview and Save.</br>
                    </p>
                </td>
            </tr>
            {if $SELECTED_MODULE}
                <tr>

                    <td style="text-align: left;padding: 10px"><strong>Icon</strong></td>

                    <td style="text-align: left;padding: 10px"><strong>Field Name</strong></td>


                    <td style="padding: 10px"><strong>Description</strong></td>

                    <td style="padding: 10px">&nbsp;</td>

                </tr>
                <tr>
                    <td align="center" style="width: 25px;padding: 10px" valign="top">
                        <div id="image_{$SELECTED_FIELD.fieldid}" class="image" onclick="openKCFinderImageType(this,{$SELECTED_FIELD.fieldid})" style="float:left;">
                            {if $SELECTED_FIELD.icon eq ''}
                                <img class="img" src="layouts/v7/modules/TooltipManager/resources/info_icon.png" style="visibility: visible;max-width: 95%;">
                            {else}
                                <img class="img" src="{$SELECTED_FIELD.icon}" style="visibility: visible;max-width: 95%;">
                            {/if}
                        </div>

                        <input type="hidden" style="display: block; margin-left:0px;" value="{$SELECTED_FIELD.icon}" id="field_icon_{$SELECTED_FIELD.fieldid}" name="field_icon_{$SELECTED_FIELD.fieldid}"/>

                    </td>
                    <td valign="top" style="padding: 10px">
                        <select id="" name="field_list_" class="chzn-select select2" onchange="changeField(this);" style="width: 200px;">
                            <option value="">--Select field--</option>
                            {foreach from=$FIELD_LIST item=FIELD}
                                <option value="{$FIELD.fieldid}" {if $FIELD.fieldid eq $SELECTED_FIELD.fieldid}selected{/if} style="{if $FIELD.helpinfo neq ''}font-weight:bold{/if}">
                                    {vtranslate($FIELD.fieldlabel, $SELECTED_MODULE_NAME)}
                                </option>
                            {/foreach}
                        </select>
                        <br/><br/>
                        <select class="chzn-select select2" name="preview_type_{$SELECTED_FIELD.fieldid}" style="width: 150px">
                            <option value="2">Tooltip</option>
                            <option value="1"{if $SELECTED_FIELD.preview_type} selected{/if}>Popup</option>
                        </select>
                    </td>
                    <td style="padding: 10px">
                        <textarea name="field_helpinfo_{$SELECTED_FIELD.fieldid}" id="field_helpinfo_{$SELECTED_FIELD.fieldid}" class="input-xxlarge" style="width:100%;">{$SELECTED_FIELD.helpinfo}</textarea>
                        <script>CKEDITOR.replace("field_helpinfo_{$SELECTED_FIELD.fieldid}", {ldelim}toolbar: "Basic"/*, toolbarStartupExpanded: false*/, height: "150px"{rdelim});</script>
                    </td>
                    <td style="width: 75px;padding: 10px" valign="top">
                        {if $SELECTED_FIELD.fieldid}
                            <button type="button" class="btn addButton" style="width:75px" onclick="tmPreview({$SELECTED_FIELD.fieldid})">{"LBL_PREVIEW"|getTranslatedString:"TooltipManager"}</button>
                            <div style="margin:1px;padding:1px"></div>
                            <button type="button" class="btn btn-success" style="width:75px" onclick="tmSave({$SELECTED_FIELD.fieldid})">{$APP_STRINGS["LBL_SAVE"]}</button>
                            <div style="margin:1px;padding:1px"></div>
                            <button type="button" class="btn btn-warning" style="width:75px" onclick="tmDelete({$SELECTED_FIELD.fieldid})">Delete</button>
                        {/if}
                    </td>
                </tr>
            {/if}
        </table>
    </form>
</div>

{literal}
    <script type="text/javascript">
        function openKCFinderImageType(div,fieldid) {
            window.KCFinder = {
                callBack: function(url) {
                    window.KCFinder = null;
                    div.innerHTML = '<div style="margin:5px">Loading...</div>';
                    var img = new Image();
                    img.src = url;
                    var field_icon_box = document.getElementById('field_icon_'+fieldid);
                    field_icon_box.value = url;
                    img.onload = function() {
                        div.innerHTML = '<img id="img_'+fieldid+'" class="img" src="' + url + '" />';
                        var img = document.getElementById('img_'+fieldid);
                        var o_w = img.offsetWidth;
                        var o_h = img.offsetHeight;
                        var f_w = div.offsetWidth;
                        var f_h = div.offsetHeight;
                        if ((o_w > f_w) || (o_h > f_h)) {
                            if ((f_w / f_h) > (o_w / o_h))
                                f_w = parseInt((o_w * f_h) / o_h);
                            else if ((f_w / f_h) < (o_w / o_h))
                                f_h = parseInt((o_h * f_w) / o_w);
                            img.style.width = f_w + "px";
                            img.style.height = f_h + "px";
                        } else {
                            f_w = o_w;
                            f_h = o_h;
                        }
                        //                    img.style.marginLeft = parseInt((div.offsetWidth - f_w) / 2) + 'px';
                        //                    img.style.marginTop = parseInt((div.offsetHeight - f_h) / 2) + 'px';
                        img.style.visibility = "visible";
                    }
                }
            };
            window.open('modules/TooltipManager/kcfinder/browse.php?type=images&dir=images/public',
                    'kcfinder_image', 'status=0, toolbar=0, location=0, menubar=0, ' +
                    'directories=0, resizable=1, scrollbars=0, width=800, height=600'
            );
        }
    </script>
{/literal}

{literal}
<script type="text/javascript">

    function selectModule(obj) {
        var selected_module = obj.value;
        // haph86@gmail.com - #17635 - 10192015
        app.helper.showProgress();
        window.location.href="index.php?module=TooltipManager&parent=Settings&view=Settings&selected_module="+selected_module;
    }

    function changeField(obj) {
        var selected_field = obj.value;
        var selected_module = jQuery('#selected_module :selected').val();
        // haph86@gmail.com - #17635 - 10192015
        app.helper.showProgress();
        window.location.href="index.php?module=TooltipManager&parent=Settings&view=Settings&selected_module="+selected_module+"&selected_field="+selected_field;
    }

    function tmPreview(fId){
        var previewType= jQuery("select[name=preview_type_"+ fId+ "]").val();
        var fieldHelpinfo= jQuery("textarea[name=field_helpinfo_"+ fId+ "]").val();

        var value = CKEDITOR.instances['field_helpinfo_'+fId].getData();
        fieldHelpinfo = value;

        if(previewType== 1){
            // haph86@gmail.com - #17635 - 10142015
//                app.showModalWindow(fieldHelpinfo,{'width':'800px','max-height':'500px',overflow:'auto',padding:'10px'})

            app.helper.showModal('<div class="modal-dialog modal-lg" style="width: 600px;"><div class="modal-content"><div class="modal-body"><div>'+ fieldHelpinfo +'</div></div></div></div>');
        }
        else{
            if(!fieldHelpinfo){
                return;
            }
            jQuery("#image_"+ fId).qtip({
                content: fieldHelpinfo,
                hide: false,
                show: {
                    event: "click",
                    ready: true
                    //, solo: true
                },
                hide: "unfocus",
                //copy
                style: {
                    width:"auto",
                    background: '#E5F6FE',
                    border: {
                        width: 2,
                        radius: 1,
                        color: '#dddddd'
                    },
                    tip: 'topLeft'
                } // haph86@gmail.com - #17635
                //paste
            });
        }
    }
    function tmSave(fId){
        for(var instance in CKEDITOR.instances){
            CKEDITOR.instances[instance].updateElement();
        }
        app.helper.showProgress();

        if(CKEDITOR.instances.field_helpinfo_{/literal}{$SELECTED_FIELD.fieldid}{literal}.document.getBody().getChild(0).getText().length <= 0) {
            jQuery('#field_helpinfo_'+fId).val('').text('');
        }


        // haph86@gmail.com - #5455 - 01202014
        jQuery.post(
                "index.php?module=TooltipManager&parent=Settings&view=Settings&selected_module={/literal}{$SELECTED_MODULE}{literal}&save_form=1",
                jQuery("form").serialize(),
                function(data){
                    app.helper.hideProgress();
                }
        );
    }
    //#609566
    function tmDelete(fId){
        var message = 'Do you want delete tooltip?';
        app.helper.showConfirmationBox({
            message: message
        }).then(function () {
            app.helper.showProgress();
            jQuery.post(
                    "index.php?module=TooltipManager&parent=Settings&view=Settings&selected_module={/literal}{$SELECTED_MODULE}{literal}&delete=1",
                    jQuery("form").serialize(),
                    function(data){
                        app.helper.hideProgress();
                        window.location.reload();
                    }
            );
        });
    }
    //#609566 end

</script>
{/literal}
{* haph86@gmail.com - #17635 - 10072015 *}
{if !$SELECTED_FIELD.fieldid}
{literal}
    <script>
        CKEDITOR.on( 'instanceReady', function( ev ) {
            ev.editor.setReadOnly( true );
        });
    </script>
{/literal}
{/if}