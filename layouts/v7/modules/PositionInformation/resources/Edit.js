/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Edit_Js("PositionInformation_Edit_Js",{},{
	
	//Will have the mapping of address fields based on the modules
	FieldsMapping : {'ModSecurities' :
			{
				'security_symbol' : 'security_symbol',
				'description' : 'security_name',
				'security_type' : 'securitytype',
				'base_asset_class' : 'aclass',
				'last_price' : 'security_price',
			}
	},
	
 
	/**
	 * Function which will copy the address details - without Confirmation
	 */
	copyFieldDetails : function(data, container) {
		var thisInstance = this;
		var sourceModule = data['source_module'];
		
		thisInstance.getRecordDetails(data).then(
			function(response){
				console.log(response)
				thisInstance.mapFieldDetails(thisInstance.FieldsMapping[sourceModule], response['data'], container);
			},
			function(error, err){

			});
	},
	
	/**
	 * Function which will map the address details of the selected record
	 */
	mapFieldDetails : function(addressDetails, result, container) {
		var thisInstance = this;
		for(var key in addressDetails) {
            if(container.find('[name="'+key+'"]').length == 0) {
                var create = container.append("<input type='hidden' name='"+key+"'>");
            }
           
			container.find('[name="'+key+'"]').val(result[addressDetails[key]]);
			container.find('[name="'+key+'"]').trigger('change');
		}
	},
	
	registerEventForSecuritySymbolFiled : function(container){
		var thisInstance = this;
		$(document).ready(function(){
			var symbolVal;
			var params = {
				'module' : app.getModuleName(),
				'action' : 'GetAllSecuritySymbols'
			};
			app.request.post({data: params}).then(function(err, data) {
				if(data) {
					symbolVal = data;
					
					var substringMatcher = function(strs) {
					  return function findMatches(q, cb) {
					    var matches, substringRegex;
					    
					    matches = [];

					    substrRegex = new RegExp(q, 'i');
					    
					    $.each(strs, function(i, str) {
					      if (substrRegex.test(str.value)) {
					        matches.push(strs[i]);
					      }
					    });

					    cb(matches);
					  };
					};

					
					$('[name="security_symbol"]').typeahead({
					  hint: true,
					  highlight: true,
					  //minLength: 1
					},
					{
					  name: 'symbol',
					  display: 'value',
					  source: substringMatcher(symbolVal)
					});
				
					$('[name="security_symbol"]').bind('typeahead:select', function(ev, suggestion) {
						console.log(suggestion.symbol)
						var params = {
							'record' :suggestion.symbol, 
							'source_module': "ModSecurities"
						};
						thisInstance.copyFieldDetails(params, container);
					});
				}
			});
		});
	},
	
	registerBasicEvents: function (container) {
		this._super(container);
		this.registerEventForSecuritySymbolFiled(container);
	},
	
	registerEvents : function(){
		this._super();
	},
	

	
})