$(document).ready(function () {
    var container = jQuery('.wrap-star');
    container.on('click','.onestar',function () {
        container.find('.fa-star').css('color','#333333');
        container.find('.onestar').css('color','#ffc107');
        container.find('.image-submited').removeClass('hide');
        container.find('[name="star"]').attr('value',1);
    });
    container.on('click','.twostar',function () {
        container.find('.fa-star').css('color','#333333');
        container.find('.onestar').css('color','#ffc107');
        container.find('.twostar').css('color','#ffc107');
        container.find('.image-submited').removeClass('hide');
        container.find('[name="star"]').attr('value',2);
    });
    container.on('click','.threestar',function () {
        container.find('.fa-star').css('color','#333333');
        container.find('.onestar').css('color','#ffc107');
        container.find('.twostar').css('color','#ffc107');
        container.find('.threestar').css('color','#ffc107');
        container.find('.image-submited').removeClass('hide');
        container.find('[name="star"]').attr('value',3);
    });
    container.on('click','.fourstar',function () {
        container.find('.fa-star').css('color','#333333');
        container.find('.onestar').css('color','#ffc107');
        container.find('.twostar').css('color','#ffc107');
        container.find('.threestar').css('color','#ffc107');
        container.find('.fourstar').css('color','#ffc107');
        container.find('.image-submited').removeClass('hide');
        container.find('[name="star"]').attr('value',4);
    });
    container.on('click','.fivestar',function () {
        container.find('.fa-star').css('color','#333333');
        container.find('.onestar').css('color','#ffc107');
        container.find('.twostar').css('color','#ffc107');
        container.find('.threestar').css('color','#ffc107');
        container.find('.fourstar').css('color','#ffc107');
        container.find('.fivestar').css('color','#ffc107');
        container.find('.image-submited').removeClass('hide');
        container.find('[name="star"]').attr('value',5);
    });

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

    $.ajax({
        method: "POST",
        url: "../../../modules/VTEFeedback/customer/save.php",
        data: {
            star: getUrlParameter('star'),
            recordid: getUrlParameter('recordid'),
            key: getUrlParameter('key')
        }
    }).done(function( data ) {
        data = jQuery.parseJSON(data);
        if(data.message != "Success"){
            alert("Feedback No update, Please don't edit any from url");
        }else{
            if(data.star==1){
                jQuery('.onestar').trigger('click');
            }else if(data.star==2){
                jQuery('.twostar').trigger('click');
            }else if(data.star == 3){
                jQuery('.threestar').trigger('click');
            }else if(data.star ==4){
                jQuery('.fourstar').trigger('click');
            }else if(data.star == 5){
                jQuery('.fivestar').trigger('click');
            }
        }
    });

});
