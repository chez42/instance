/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_RelatedList_Js("Contacts_RelatedList_Js",{},{
	
	addRelations : function(idList){
    	
		
        var thisInstance = this;
		var aDeferred = jQuery.Deferred();
		var sourceRecordId = this.parentRecordId;
		var sourceModuleName = this.parentModuleName;
		var relatedModuleName = this.relatedModulename;
        var selectedTabElement = this.getSelectedTabElement();
        if(selectedTabElement.length > 0){
            var relationId = selectedTabElement.data('relationId');
        }

		var params = {};
		params['mode'] = "addRelation";
		params['module'] = sourceModuleName;
		params['action'] = 'RelationAjax';
		params['relationId'] = relationId;
		params['related_module'] = relatedModuleName;
		params['src_record'] = sourceRecordId;
		params['related_record_list'] = JSON.stringify(idList);
		
		if(this.relatedModulename == 'Connection'){
			params['connection_from_pop'] = $(document).find('[name="connection_from_pop"]').val();
			params['connection_to_pop'] = $(document).find('[name="connection_to_pop"]').val();
		}
		
        app.helper.showProgress();
        
		app.request.post({"data":params}).then(
			function(responseData){
                thisInstance.updateRelatedRecordsCount(relationId,idList,true);
                app.helper.hideProgress();
				aDeferred.resolve(responseData);
			},

			function(textStatus, errorThrown){
                app.helper.hideProgress();
				aDeferred.reject(textStatus, errorThrown);
			}
		);
		return aDeferred.promise();
	},
    
	
})