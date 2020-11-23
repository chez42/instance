Vtiger.Class("PandaDoc_Js",{
	
	connected: false,
	
	ValidateToken: function(){
		
		var thisInstance = this;
		
		var params = {};
		
		params.module = 'PandaDoc';
		
		params.action = 'ValidateToken';
		
		app.request.post({data:params}).then(
			
			function(err,data){
				
				if(err === null) {
					thisInstance.connected = true;
				} else {
					thisInstance.connected = false;	
				}
				
			}
		);
	
	},
	
	sendPandaDocDocument : function(massurl){

    	var thisInstance = this;
		
    	if(!thisInstance.connected){
			
			app.helper.showConfirmationBox({'message': 'You have to connect your account with OmniCrm, Do you want to proceed?'}).then(
				
				function(data) {
					
					var url = decodeURIComponent(window.location.href.split('index.php', 1) + 'modules/PandaDoc/connect.php');
					var win = window.open(url,'','height=600,width=600,channelmode=1');
					window.RefreshPage = function() {
						thisInstance.ValidateToken();
					}
					
				},
				
				function(error, err) {}
				
			);
		
		} else {
			var data = app.convertUrlToDataParams(massurl);
		
			data['record'] = app.getRecordId();
		
			data['srcmodule'] = app.getModuleName();

			app.helper.showProgress();
		
			app.request.post({'data': data}).then(
				function (err, data) {
					app.helper.hideProgress();
				
					if (data) {
						app.helper.showModal(data,{
							'cb' : function(modalContainer){
								
								var recipients ;
								var token ;
								var meta_data_value;
								var user_id;
								
								var params= {
									'record' : app.getRecordId(),
									'source_module' : app.getModuleName()
								};
								thisInstance.getRecordDetails(params).then(
									
									function(response){
										
										token = response.token;
										
										meta_data_value = response.meta_data_value;
										
										recipients = response.recipients;
										
										user_id = response.user_id;
										
										reference = response.reference;
										
										var editor = new PandaDoc.DocEditor();
										
										editor.show({ 
											
											el: '#pandadoc-sdk',
											
											data: {
												tokens: token,
												recipients : recipients,
												metadata: {
										            'CRM_REFERENCE': meta_data_value,
										            'reference' : reference,
										            'USER_ID': user_id
										        }
											},
											
											events: {
												onDocumentSent: function(){ 
										        	
										        },
												onInit: function(){ },
										        
										        onDocumentCreated: function(){ },
										        
										        
										        
										        onClose: function(){}
											
											}
											
										});
										
									},
									function(error, err){}
								);
							}
						});
					}
				}
			);
		}
	
	},
	
	getRecordDetails : function(params) {
		var aDeferred = jQuery.Deferred();
		var url = "index.php?module=PandaDoc&action=GetData&record="+params['record']+"&source_module="+params['source_module'];
		app.request.get({'url':url}).then(
			function(error, data){
				if(error == null) {
					aDeferred.resolve(data);
				} else {
					//aDeferred.reject(data['message']);
				}
			},
			function(error){
				aDeferred.reject();
			}
			)
		return aDeferred.promise();
	},
	
},{
	
	registerEvents : function(){
		this.Class.ValidateToken();
	},
});

jQuery(document).ready(function(){
	
	obj = new PandaDoc_Js();
	obj.registerEvents();
	
});