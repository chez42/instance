
Vtiger.Class("Settings_GlobalSearch_Edit_Js",{}, {
	
	fieldsContainer : false,
	moduleContainer : false,
	fieldsShowContainer : false,
	
	form : false,
	
	getForm : function(){
		if(this.form == false){
			this.form = jQuery('#GlobalSearchSettings');
		}
		return this.form;
	},
	
	registerSelect2ElementForFields : function() {
		app.changeSelectElementView(this.fieldsContainer, 'select2', {maximumSelectionSize: 6});
		app.changeSelectElementView(this.fieldsShowContainer, 'select2', {maximumSelectionSize: 6});
	},
	
	registerModuleChangeEvent : function() {
		
		var thisInstance = this;
		
		thisInstance.moduleContainer.on('change', function(e){
			
			var progressIndicatorElement = jQuery.progressIndicator({
				'position' : 'html',
				'blockInfo' : {
					'enabled' : true
				}
			});
			var selected_module = thisInstance.moduleContainer.val();
			
			if(selected_module != ''){
				thisInstance.fieldsContainer.attr('data-validation-engine','validate[required]');
				thisInstance.fieldsShowContainer.attr('data-validation-engine','validate[required]');
			} else {
				thisInstance.fieldsContainer.removeAttr('data-validation-engine');
				thisInstance.fieldsShowContainer.removeAttr('data-validation-engine');
			}
			
			var data = 'index.php?module='+app.getModuleName()+'&parent='+app.getParentModuleName()+
			'&action=GetSearchData&selected_module='+selected_module;
			
			app.request.get({url: data}).then(
				function(error,response){
					
					progressIndicatorElement.progressIndicator({
						'mode' : 'hide'
					});
					
					var options = '';
					var showOption = '';
					
					$('input[name="allow_global_search"]').prop('checked',false);
					
					if(response){
						
						var allFields = response.all_fields;	
						var savedData = response.savedData;
						
						options += response.selectedFields;
						showOption += response.showFields;
						
						/*for( var fieldname in allFields ) {
								
							options += '<option value="'+fieldname+'"';
							
							if( typeof savedData.fieldnames != undefined && $.inArray(fieldname, savedData.fieldnames) != '-1' )
								options += ' selected ';
							
							options += '>'+allFields[fieldname]+'</option>';
						}
						
						for( var fieldname in allFields ) {
							
							showOption += '<option value="'+fieldname+'"';
							
							if( typeof savedData.fieldshow != undefined && $.inArray(fieldname, savedData.fieldshow) != '-1' )
								showOption += ' selected ';
							
							showOption += '>'+allFields[fieldname]+'</option>';
						}*/
						
						if( typeof savedData.allow_global_search != undefined && savedData.allow_global_search == '1' )
							$('input[name="allow_global_search"]').prop('checked', true);
								
					}
					
					thisInstance.fieldsContainer.html(options).trigger("change");
					thisInstance.fieldsShowContainer.html(showOption).trigger("change");
					
					
				});
		});
		
		
	},
	
	
	registerFormSave : function(){
		var thisInstance = this;
		$(".saveButton").click(function(e){
			
			var form = thisInstance.getForm();
			var params = {
	            submitHandler : function(form) {
	                  app.helper.showProgress();
	                var form = jQuery(form);
	                thisInstance.saveForm(form);
	            }
			};
			if (form.length) {
	        	form.vtValidate(params);
			 	form.on('submit', function(e){
	            	e.preventDefault();
	            	return false;
	        	});
			}
			
		});
		
	},
	
	saveForm : function(e) {
		var thisInstance = this;
		
		var form = thisInstance.getForm();
		
		var progressIndicatorElement = jQuery.progressIndicator({
				'position' : 'html',
				'blockInfo' : {
					'enabled' : true
				}
		});
		
		var params = form.serializeFormData();
			
				
		params.module = app.getModuleName();
		params.parent = app.getParentModuleName();
		params.action= "Save";
		
		AppConnector.request(params).then(
			function(data){
				
				progressIndicatorElement.progressIndicator({
					'mode' : 'hide'
				});
				
				if(data.success == true){
					params = {
						title: 'Settings Save Successfully'
					};
					Settings_Vtiger_Index_Js.showMessage(params);
					
					
				}else{
					var errorMessage = "Error : "+app.vtranslate(data.error.message);
					params = {
						title: errorMessage,
						type: 'error'
					};
					Settings_Vtiger_Index_Js.showMessage(params);
				}
			},
			function(jqXHR,textStatus, errorThrown){
			}
		);
		
		
	},
	
	registerEvents : function() {
	
		var thisInstance = this;
		var editViewForm = this.getForm();
	
		this.moduleContainer = $("#modulename");
		this.fieldsContainer = $("#fieldnames");
		this.fieldsShowContainer = $("#fieldnames_show");
	
		this.registerModuleChangeEvent();
		this.registerSelect2ElementForFields();
		this.registerFormSave();
	},
	
	init: function () {
		this.addComponents();
	},
	
	addComponents: function () {
		this.addModuleSpecificComponent('Index', 'Vtiger', app.getParentModuleName());
	},
	
});

