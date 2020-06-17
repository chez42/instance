"use strict";

// Class definition
var KTAppInbox = function() {
    var asideEl = KTUtil.getByID('kt_inbox_aside');
    var listEl = KTUtil.getByClass('kt_inbox_list');
    var viewEl = KTUtil.getByClass('kt_inbox_view');
    var composeEl = KTUtil.getByID('kt_inbox_compose');
    var asideOffcanvas;
    var initEditor = function(editor) {
        // init editor
        var options = {
            modules: {
                toolbar: {}
            },
            placeholder: 'Type message...',
            theme: 'snow'
        };
        var editor = new Quill('#' + editor, options);
    }

    var initForm = function(formEl) {
        var formEl = KTUtil.getByID(formEl);
     // Init autocompletes
        var toEl = KTUtil.find(formEl, '[name=compose_to]');
        var tagifyTo = new Tagify(toEl, {
            delimiters: ", ", // add new tags when a comma or a space character is entered
            maxTags: 10,
            blacklist: ["fuck", "shit", "pussy"],
            keepInvalidTags: true, // do not remove invalid tags (but keep them marked as invalid)
            whitelist: [],
            templates: {
                dropdownItem: function(tagData) {
                    try {
                        var html = '';
                        html += '<div class="tagify__dropdown__item">';
                        html += '   <div class="kt-media-card">';
                        html += '       <span class="kt-media kt-media--' + (tagData.initialsState ? tagData.initialsState : '') + '" style="background-image: url(\''+ (tagData.pic ? tagData.pic : '') + '\')">';
                        html += '           <span>' + (tagData.initials ? tagData.initials : '') + '</span>';
                        html += '       </span>';
                        html += '       <div class="kt-media-card__info">';
                        html += '           <a href="#" class="kt-media-card__title">'+ (tagData.value ? tagData.value : '') + '</a>';
                        html += '           <span class="kt-media-card__desc">' + (tagData.email ? tagData.email : '') + '</span>';
                        html += '       </div>';
                        html += '   </div>';
                        html += '</div>';
                        return html;
                    } catch (err) {}
                }
            },
            transformTag: function(tagData) {
                tagData.class = 'tagify__tag tagify__tag--brand';
            },
            dropdown: {
                classname: "color-blue",
                enabled: 1,
                maxItems: 5
            }
        });
        var ccEl = KTUtil.find(formEl, '[name=compose_cc]');
        var tagifyC = new Tagify(ccEl, {
            delimiters: ", ", // add new tags when a comma or a space character is entered
            maxTags: 10,
            blacklist: ["fuck", "shit", "pussy"],
            keepInvalidTags: true, // do not remove invalid tags (but keep them marked as invalid)
            whitelist: [],
            templates: {
                dropdownItem: function(tagData) {
                    try {
                        var html = '';
                        html += '<div class="tagify__dropdown__item">';
                        html += '   <div class="kt-media-card">';
                        html += '       <span class="kt-media kt-media--' + (tagData.initialsState ? tagData.initialsState : '') + '" style="background-image: url(\''+ (tagData.pic ? tagData.pic : '') + '\')">';
                        html += '           <span>' + (tagData.initials ? tagData.initials : '') + '</span>';
                        html += '       </span>';
                        html += '       <div class="kt-media-card__info">';
                        html += '           <a href="#" class="kt-media-card__title">'+ (tagData.value ? tagData.value : '') + '</a>';
                        html += '           <span class="kt-media-card__desc">' + (tagData.email ? tagData.email : '') + '</span>';
                        html += '       </div>';
                        html += '   </div>';
                        html += '</div>';
                        return html;
                    } catch (err) {}
                }
            },
            transformTag: function(tagData) {
                tagData.class = 'tagify__tag tagify__tag--brand';
            },
            dropdown: {
                classname: "color-blue",
                enabled: 1,
                maxItems: 5
            }
        });
        var bccEl = KTUtil.find(formEl, '[name=compose_bcc]');
        var tagifyBcc = new Tagify(bccEl, {
            delimiters: ", ", // add new tags when a comma or a space character is entered
            maxTags: 10,
            blacklist: ["fuck", "shit", "pussy"],
            keepInvalidTags: true, // do not remove invalid tags (but keep them marked as invalid)
            whitelist: [],
            templates: {
                dropdownItem: function(tagData) {
                    try {
                        var html = '';
                        html += '<div class="tagify__dropdown__item">';
                        html += '   <div class="kt-media-card">';
                        html += '       <span class="kt-media kt-media--' + (tagData.initialsState ? tagData.initialsState : '') + '" style="background-image: url(\''+ (tagData.pic ? tagData.pic : '') + '\')">';
                        html += '           <span>' + (tagData.initials ? tagData.initials : '') + '</span>';
                        html += '       </span>';
                        html += '       <div class="kt-media-card__info">';
                        html += '           <a href="#" class="kt-media-card__title">'+ (tagData.value ? tagData.value : '') + '</a>';
                        html += '           <span class="kt-media-card__desc">' + (tagData.email ? tagData.email : '') + '</span>';
                        html += '       </div>';
                        html += '   </div>';
                        html += '</div>';
                        return html;
                    } catch (err) {}
                }
            },
            transformTag: function(tagData) {
                tagData.class = 'tagify__tag tagify__tag--brand';
            },
            dropdown: {
                classname: "color-blue",
                enabled: 1,
                maxItems: 5
            }
        });
        // CC input display
        $(document).on('click', '.kt-inbox__to .kt-inbox__tool.kt-inbox__tool--cc', function(e) {
            var inputEl = KTUtil.find(formEl, '.kt-inbox__to');
            KTUtil.addClass(inputEl, 'kt-inbox__to--cc');
            $(formEl).find("[name=compose_cc]").focus();
        });
        // CC input hide
        $(document).on('click', '.kt-inbox__to .kt-inbox__field.kt-inbox__field--cc .kt-inbox__icon--delete', function(e) {
            var inputEl = KTUtil.find(formEl, '.kt-inbox__to');
            KTUtil.removeClass(inputEl, 'kt-inbox__to--cc');
        });
        // BCC input display
        $(document).on('click', '.kt-inbox__to .kt-inbox__tool.kt-inbox__tool--bcc', function(e) {
            var inputEl = KTUtil.find(formEl, '.kt-inbox__to');
            KTUtil.addClass(inputEl, 'kt-inbox__to--bcc');
            $(formEl).find("[name=compose_bcc]").focus();
        });
        // BCC input hide
        $(document).on('click', '.kt-inbox__to .kt-inbox__field.kt-inbox__field--bcc .kt-inbox__icon--delete', function(e) {
            var inputEl = KTUtil.find(formEl, '.kt-inbox__to');
            KTUtil.removeClass(inputEl, 'kt-inbox__to--bcc');
        });
    }

    var initAttachments = function(elemId) {
        var id = "#" + elemId;
        var previewNode = $(id + " .dropzone-item");
        previewNode.id = "";
        var previewTemplate = previewNode.parent('.dropzone-items').html();
        previewNode.remove();
        var myDropzone = new Dropzone(id, { // Make the whole body a dropzone
            url: "sendMail.php", // Set the url for your upload script location
            parallelUploads: 20,
            maxFilesize: 1, // Max filesize in MB
            autoProcessQueue: false,
            previewTemplate: previewTemplate,
            uploadMultiple: true,
    		maxFiles: 5,
            previewsContainer: id + " .dropzone-items", // Define the container to display the previews
            clickable: id + "_select", // Define the element that should be used as click trigger to select files.
            sending: function(file, xhr, formData) {
    			var formValues = $('form#dropzone_form').serializeObject();
    			$.each(formValues, function(key, value){
    				formData.append(key,value);
    			});
    			var textMsg = $('form#dropzone_form').find('.ql-editor').html();
    			formData.append('message',textMsg);
    		},
    		/*init: function() {
    			var myDz = this;
    			$('#dropzone_form').on('submit', function (e) {
    				  if (!e.isDefaultPrevented()) {
    					  e.preventDefault();
    					  e.stopPropagation();
    					  myDz.processQueue();
    					  console.log('fdf');
    					 myDz.on("complete", function (file) {
    						 return false;
    						 // window.location = "inbox.php";
    					});
    				 }
    			});
    		}*/
        });
        myDropzone.on("addedfile", function(file) {
            // Hookup the start button
            $(document).find(id + ' .dropzone-item').css('display', '');
        });
        // Update the total progress bar
        myDropzone.on("totaluploadprogress", function(progress) {
            document.querySelector(id + " .progress-bar").style.width = progress + "%";
        });
        myDropzone.on("sending", function(file) {
            // Show the total progress bar when upload starts
            document.querySelector(id + " .progress-bar").style.opacity = "1";
        });
        // Hide the total progress bar when nothing's uploading anymore
        myDropzone.on("complete", function(progress) {
            var thisProgressBar = id + " .dz-complete";
            setTimeout(function() {
                $(thisProgressBar + " .progress-bar, " + thisProgressBar + " .progress").css('opacity', '0');
            }, 300)
        });
        
        $('#dropzone_form').on('submit', function (e) {
        	
            // Make sure that the form isn't actually being sent.
            e.preventDefault();
            e.stopPropagation();
            
			if (myDropzone.getQueuedFiles().length > 0)
			{             
				myDropzone.processQueue(); 
				myDropzone.on("complete", function (file) {
					 return false;
					 // window.location = "inbox.php";
				});
			} else {      
				var formData = new FormData();
				var formValues = $('form#dropzone_form').serializeObject();
    			$.each(formValues, function(key, value){
    				formData.append(key,value);
    			});
    			var textMsg = $('form#dropzone_form').find('.ql-editor').html();
    			formData.append('message',textMsg);
    			
    			$.ajax({						
        			type: "POST",						
        			url: "sendMail.php",
        			processData: false,
                    contentType: false,
        			data: formData,						
        			success: function(data){
        				console.log(data);
        			}					
        		});
			}     

          });
    }

    return {
        // public functions
        init: function() {
            // init
            KTAppInbox.initAside();
            KTAppInbox.initList();
            KTAppInbox.initView();
            //KTAppInbox.initReply();
            KTAppInbox.initCompose();
        },

        initAside: function() {
            // Mobile offcanvas for mobile mode
            asideOffcanvas = new KTOffcanvas(asideEl, {
                overlay: true,
                baseClass: 'kt-inbox__aside',
                closeBy: 'kt_inbox_aside_close',
                toggleBy: 'kt_subheader_mobile_toggle'
            });
            // View list
            KTUtil.on(asideEl, '.kt-nav__item .kt-nav__link[data-action="list"]', 'click', function(e) {
                var type = KTUtil.attr(this, 'data-type');
                var listItemsEl = KTUtil.find(listEl, '.kt-inbox__items');
                var navItemEl = this.closest('.kt-nav__item');
                var navItemActiveEl = KTUtil.find(asideEl, '.kt-nav__item.kt-nav__item--active');
                var type = $('[name="type"]').val();
                var folder = $(this).data('folder');
                var folderId = $(this).data('folderid');
				$('body').waitMe();
				var data = 'folder='+folder+'&page=0&mode=folderMails&type='+type;
				if(folderId)
					var data = 'folder='+folder+'&page=0&mode=folderMails&type='+type+'&folderid='+folderId; 
				$.ajax({						
        			type: "POST",						
        			url: "get_folderMails.php",						
        			data: data,						
        			success: function(data){
        				$(document).find('.detail_div').replaceWith(data);
        				$('body').waitMe('hide');
	                    $(navItemActiveEl).removeClass('kt-nav__item--active');
	                    $(navItemEl).addClass('kt-nav__item--active');
        			}					
        		});
            });
        },

        initList: function() {
            // View message
        	$(document).on('click', '.kt-inbox__item',  function(e) {
                var actionsEl = KTUtil.find(this, '.kt-inbox__actions');
                // skip actions click
                if (e.target === actionsEl || (actionsEl && actionsEl.contains(e.target) === true)) {
                    return ;
                }
                var folder = $(this).closest('.kt-inbox__items').data('folder');
                var msgNo = $(this).data('id');
                var pageNo = $(document).find('.nextpage').data('pageno');
                var type = $('[name="type"]').val();
				$('body').waitMe();
				var data = 'folder='+folder+'&pageno='+pageNo+'&msgNo='+msgNo+'&mode=mailDetail&type='+type;
				$.ajax({						
        			type: "POST",						
        			url: "get_folderMails.php",						
        			data: data,						
        			success: function(data){
        				$(document).find('.detail_div').replaceWith(data);
        				$('body').waitMe('hide');
        				KTAppInbox.initReply();
        			}					
        		});
            });
            // Group selection
            KTUtil.on(listEl, '.kt-inbox__toolbar .kt-inbox__check .kt-checkbox input', 'click', function() {
                var items = KTUtil.findAll(listEl, '.kt-inbox__items .kt-inbox__item');
                console.log('check2');
                for (var i = 0, j = items.length; i < j; i++) {
                    var item = items[i];
                    var checkbox = KTUtil.find(item, '.kt-inbox__actions .kt-checkbox input');
                    checkbox.checked = this.checked;
                    if (this.checked) {
                        KTUtil.addClass(item, 'kt-inbox__item--selected');
                    } else {
                        KTUtil.removeClass(item, 'kt-inbox__item--selected');
                    }
                }
            });
            // Individual selection
            KTUtil.on(listEl, '.kt-inbox__item .kt-checkbox input', 'click', function() {
                var item = this.closest('.kt-inbox__item');
               
                if (item && this.checked) {
                    KTUtil.addClass(item, 'kt-inbox__item--selected');
                } else {
                    KTUtil.removeClass(item, 'kt-inbox__item--selected');
                }
            });
        },

        initView: function() {
            // Back to listing
        	$(document).on('click', '.kt-inbox__toolbar .kt-inbox__icon.kt-inbox__icon--back', function() {
                var folder = $(this).data('folder');
                var page = $(this).data('page');
                var folderId = $(this).data('folderid');
                var type = $('[name="type"]').val();
				$('body').waitMe();
				var data = 'folder='+folder+'&page='+page+'&mode=folderMails&type='+type+'&folderid='+folderId;
				$.ajax({						
        			type: "POST",						
        			url: "get_folderMails.php",						
        			data: data,						
        			success: function(data){
        				$(document).find('.detail_div').replaceWith(data);
        				$('body').waitMe('hide');
         			}					
        		});
            });
        },

        initReply: function() {
            initEditor('kt_inbox_reply_editor');
            initAttachments('kt_inbox_reply_attachments');
            initForm('kt_inbox_reply_form');
            // Show/Hide reply form
            KTUtil.on(viewEl, '.kt-inbox__reply .kt-inbox__actions .btn', 'click', function(e) {
                var reply = this.closest('.kt-inbox__reply');

                if (KTUtil.hasClass(reply, 'kt-inbox__reply--on')) {
                    KTUtil.removeClass(reply, 'kt-inbox__reply--on');
                } else {
                    KTUtil.addClass(reply, 'kt-inbox__reply--on');
                }
            });
            // Show reply form for messages
            KTUtil.on(viewEl, '.kt-inbox__message .kt-inbox__actions .kt-inbox__group .kt-inbox__icon.kt-inbox__icon--reply', 'click', function(e) {
                var reply = KTUtil.find(viewEl, '.kt-inbox__reply');
                KTUtil.addClass(reply, 'kt-inbox__reply--on');
            });
            // Remove reply form
            KTUtil.on(viewEl, '.kt-inbox__reply .kt-inbox__foot .kt-inbox__icon--remove', 'click', function(e) {
                var reply = this.closest('.kt-inbox__reply');
                swal.fire({
                    text: "Are you sure to discard this reply ?",
                    //type: "error",
                    buttonsStyling: false,

                    confirmButtonText: "Discard reply",
                    confirmButtonClass: "btn btn-danger",

                    showCancelButton: true,
                    cancelButtonText: "Cancel",
                    cancelButtonClass: "btn btn-label-brand"
                }).then(function(result) {
                    if (KTUtil.hasClass(reply, 'kt-inbox__reply--on')) {
                        KTUtil.removeClass(reply, 'kt-inbox__reply--on');
                    }
                });
            });
        },

        initCompose: function() {
            initEditor('kt_inbox_compose_editor');
            initAttachments('kt_inbox_compose_attachments');
            initForm('kt_inbox_compose_form');
            // Remove reply form
            KTUtil.on(composeEl, '.kt-inbox__form .kt-inbox__foot .kt-inbox__secondary .kt-inbox__icon.kt-inbox__icon--remove', 'click', function(e) {
                swal.fire({
                    text: "Are you sure to discard this message ?",
                    type: "danger",
                    buttonsStyling: false,
                    confirmButtonText: "Discard draft",
                    confirmButtonClass: "btn btn-danger",
                    showCancelButton: true,
                    cancelButtonText: "Cancel",
                    cancelButtonClass: "btn btn-label-brand"
                }).then(function(result) {
                    $(composeEl).modal('hide');
                });
            });
        }
    };
}();

KTUtil.ready(function() {
    KTAppInbox.init();
});
