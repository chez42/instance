$(document).ready(function () {


    function showHideRequestFeedback() {
        var getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        };

        var module =  getUrlParameter("module");
        var parentModule = getUrlParameter("parent");
        var viewModule =  getUrlParameter("view");
        var returnsourceModule = jQuery('#s2id_module_name #select2-chosen-1').text();

        if(returnsourceModule == ''){
            returnsourceModule = jQuery('#module_name').val();
        }

        if(module == "Workflows"
            && parentModule == "Settings"
            && viewModule == "Edit"){
            //if WF create Fill Request Feedback
            var listAction = jQuery('#workflow_action .dropdown-menu li a');

            if(returnsourceModule == "Feedback" || returnsourceModule == "VTEFeedback"){
                //Is Feedback Show
                $.each(listAction,function (key,item) {
                    if(jQuery(item).text() =="Request Feedback"){
                        jQuery(item).closest('li').css('display','block');
                    }
                });
            }else{
                //not Feedback -> Hide
                $.each(listAction,function (key,item) {
                    if(jQuery(item).text() =="Request Feedback"){
                        jQuery(item).closest('li').css('display','none');
                    }
                });
            }
        }
    }

    $( document ).ajaxComplete(function() {
        showHideRequestFeedback();
    });

    // jQuery('#module_name').on('change',function () {
    //     showHideRequestFeedback();
    // });

});