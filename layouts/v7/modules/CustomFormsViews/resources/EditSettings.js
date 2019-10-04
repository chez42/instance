/* ********************************************************************************
 * The content of this file is subject to the Custom Forms & Views ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger_Edit_Js("CustomFormsViews_EditSettings_Js",{
    editInstance:false,
    getInstance: function(){
        if(CustomFormsViews_EditSettings_Js.editInstance == false){
            var instance = new CustomFormsViews_EditSettings_Js();
            CustomFormsViews_EditSettings_Js.editInstance = instance;
            return instance;
        }
        return CustomFormsViews_EditSettings_Js.editInstance;
    }
},{
    updatedBlockSequence : {},
    updatedBlocksList : [],
    updatedBlockFieldsList : [],
    registerSelectModulesEvent: function (container) {
        var thisInstance=this;
        container.find('#modulesList').on('change', function(e){
            app.helper.showProgress();
            var selectedVal=jQuery(e.currentTarget).val();
            var url ='index.php?module=CustomFormsViews&view=MassActionAjax&mode=getFieldBlocks';
            var actionParams = {
                "type":"POST",
                "url":url,
                "dataType":"html",
                "data" : {
                    'source_module':selectedVal
                }
            };

            app.request.post(actionParams).then(
                function(err,data){
                    if(err === null) {
                        app.helper.hideProgress();
                        var massEditForm = container.find("#field_blocks");
                        massEditForm.html(data);
                        vtUtils.applyFieldElementsView(massEditForm);
                        vtUtils.applyFieldElementsView(massEditForm.find('select.select2'));
                        thisInstance.makeBlocksListSortable();
                        thisInstance.makeFieldsListSortable();
                        thisInstance.registerSetBlockVisible();
                    }
                }
            );
        });
    },

    registerFormSubmitEvent: function(container) {
        var thisInstance = this;
        container.on('submit', function(e) {
            var source_module=container.find('#modulesList').val();
            var fieldname=container.find('#modulePickList').val();
            var record=container.find('input[name="record"]').val();
            var arrModule=container.find('#arrModule').val();
            // var objModuleFields = jQuery.parseJSON(arrModule);
            var selectedModule=container.find('input[name="selectedModule"]').val();
            thisInstance.updateBlocksListByOrder();
            thisInstance.createUpdatedBlockFieldsList();
            thisInstance.saveCustomViewsForms(container);
            e.preventDefault();
        });
    },
    /**
     * Function to regiser the event to make the blocks sortable
     */
    makeBlocksListSortable : function() {
        var thisInstance = this;
        var contents = jQuery('#field_blocks').find('.contents');
        var table = contents.find('.blockSortable');
        contents.sortable({
            'containment' : contents,
            'items' : table,
            'revert' : true,
            'tolerance':'pointer',
            'cursor' : 'move',
            'update' : function(e, ui) {
                thisInstance.updateBlocksListByOrder();
            }
        });
    },

    /**
     * Function which will arrange the sequence number of blocks
     */
    updateBlocksListByOrder : function() {
        var thisInstance = this;
        var contents = jQuery('#field_blocks').find('.contents');
        contents.find('.editFieldsTable').each(function(index,domElement){
            var blockData={};
            var blockTable = jQuery(domElement);

            var blockId = blockTable.data('blockId');
            var actualBlockSequence = blockTable.data('sequence');
            var visible = blockTable.data('visible');
            var expectedBlockSequence = (index+1);

            if(expectedBlockSequence != actualBlockSequence) {
                blockTable.data('sequence', expectedBlockSequence);
            }
            blockData['sequence']=expectedBlockSequence;
            blockData['visible']=visible;
            if(typeof blockId != 'undefined' && blockId !=0){
                thisInstance.updatedBlockSequence[blockId] = blockData;
            }
            thisInstance.updatedBlocksList.push(blockId);
        });
        return thisInstance.updatedBlockSequence;
    },

    /**
     * Function to regiser the event to make the fields sortable
     */
    makeFieldsListSortable : function() {
        var thisInstance = this;
        var contents = jQuery('#field_blocks').find('.contents');
        var table = contents.find('.editFieldsTable');
        table.find('ul[name=sortable1], ul[name=sortable2]').sortable({
            'containment' : '#moduleBlocks',
            'revert' : true,
            'tolerance':'pointer',
            'cursor' : 'move',
            'connectWith' : '.connectedSortable',
            'update' : function(e, ui) {
                var currentField = ui['item'];
                thisInstance.createUpdatedBlocksList(currentField);
                // rearrange the older block fields
                if(ui.sender) {
                    var olderBlock = ui.sender.closest('.editFieldsTable');
                    thisInstance.reArrangeBlockFields(olderBlock);
                }
            }
        });
    },
    /**
     * Function to create the blocks list which are updated while sorting
     */
    createUpdatedBlocksList : function(currentField) {
        var thisInstance = this;
        var block = currentField.closest('.editFieldsTable');
        var updatedBlockId = block.data('blockId');
        if(jQuery.inArray(updatedBlockId, thisInstance.updatedBlocksList) == -1) {
            thisInstance.updatedBlocksList.push(updatedBlockId);
        }
        thisInstance.reArrangeBlockFields(block);
    },

    /**
     * Function that rearranges fields in the block when the fields are moved
     * @param <jQuery object> block
     */
    reArrangeBlockFields : function(block) {
        // 1.get the containers, 2.compare the length, 3.if uneven then move the last element
        var leftSideContainer = block.find('ul[name=sortable1]');
        var rightSideContainer = block.find('ul[name=sortable2]');
        if(leftSideContainer.children().length < rightSideContainer.children().length) {
            var lastElementInRightContainer = rightSideContainer.children(':last');
            leftSideContainer.append(lastElementInRightContainer);
        } else if(leftSideContainer.children().length > rightSideContainer.children().length+1) {	//greater than 1
            var lastElementInLeftContainer = leftSideContainer.children(':last');
            rightSideContainer.append(lastElementInLeftContainer);
        }
    },
    /**
     * Function to create the list of updated blocks with all the fields and their sequences
     */
    createUpdatedBlockFieldsList : function() {
        var thisInstance = this;
        thisInstance.updatedBlockFieldsList=[];
        var contents = jQuery('#field_blocks').find('.contents');

        for(var index in  thisInstance.updatedBlocksList) {
            var updatedBlockId = thisInstance.updatedBlocksList[index];
            var updatedBlock = contents.find('.block_'+updatedBlockId);
            var firstBlockSortFields = updatedBlock.find('ul[name=sortable1]');
            var editFields = firstBlockSortFields.find('.editFields');


            var expectedFieldSequence = 1;
            editFields.each(function(i,domElement){
                var fieldEle = jQuery(domElement);
                var fieldId = fieldEle.data('fieldId');
                thisInstance.updatedBlockFieldsList.push({'fieldid' : fieldId,'sequence' : expectedFieldSequence, 'block' : updatedBlockId});
                expectedFieldSequence = expectedFieldSequence+2;
            });
            var secondBlockSortFields = updatedBlock.find('ul[name=sortable2]');
            var secondEditFields = secondBlockSortFields.find('.editFields');
            var sequenceValue = 2;
            secondEditFields.each(function(i,domElement){
                var fieldEle = jQuery(domElement);
                var fieldId = fieldEle.data('fieldId');
                thisInstance.updatedBlockFieldsList.push({'fieldid' : fieldId,'sequence' : sequenceValue, 'block' : updatedBlockId});
                sequenceValue = sequenceValue+2;
            });
        }
    },
    /**
     * Function will save the field sequences
     */
    saveCustomViewsForms : function(container) {
        var thisInstance = this;
        app.helper.showProgress();
        var params = {};
        params['module'] = app.getModuleName();
        params['action'] = 'Save';
        params['record'] = container.find('input[name="record"]').val();
        params['source_module'] = container.find('#modulesList').val();
        params['status'] = container.find('#status').val();
        params['custom_name'] = container.find('input[name="custom_name"]').val();
        params['fields'] = thisInstance.updatedBlockFieldsList;
        params['blocks'] = thisInstance.updatedBlockSequence;
        params['profiles'] = container.find('#selected_profiles').val();
        console.log(params);
        app.request.post({'data' : params}).then(
            function(err,data){
                if(err === null) {
                    app.helper.hideProgress();
                    document.location.href='index.php?module=CustomFormsViews&parent=Settings&view=Settings';
                }else{
                    app.helper.hideProgress();
                }
            }
        );
    },
    registerSetBlockVisible:function() {
        var thisInstance = this;
        var blockVisibility = jQuery('#field_blocks').find('.blockVisibility');
        blockVisibility.click(function(e) {
            var blockParent=jQuery(this).closest('div.editFieldsTable');
            var oldDisplayStatus = blockParent.data('visible');
            if(oldDisplayStatus == '0') {
                jQuery(this).find('.glyphicon-ok').removeClass('hide');
                blockParent.data('visible', '1');
            } else {
                jQuery(this).find('.glyphicon-ok').addClass('hide');
                blockParent.data('visible', '0');
            }
        });
    },
    registerEvents : function() {
        this._super();
        var container=jQuery('#customFromView');
        this.registerSelectModulesEvent(container);
        this.registerFormSubmitEvent(container);
        this.registerSetBlockVisible();
        this.makeBlocksListSortable();
        this.makeFieldsListSortable();
    }
});

jQuery(document).ready(function() {
    var instance = new CustomFormsViews_EditSettings_Js();
    instance.registerEvents();
});
