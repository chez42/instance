<style type="text/css">
    *{
        margin: 0; padding: 0;
    }
    #mapContent {
        width: 100%;
    }

    #mapImg {
        max-width: 98%;
        width: 98%;
        margin: 1%;
    }

    .routing-step, .routing-header {
        padding: 1%;
    }
</style>

<div id="mapContent">
    <img id="mapImg" src=""/>
    <div class="routing-step"></div>
</div>

{literal}
<script type="text/javascript">
    var alphabet = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
    (function($){
        $(function(){
            var mapUrl = $.jStorage.get('mapUrl');
            var routingStepData = $.jStorage.get('routingStepData');
            var currRoutingHeader = $.jStorage.get('currRoutingHeader');

            $('#mapImg').attr('src', mapUrl);
            $(currRoutingHeader).insertBefore('.routing-step');

            var routingHtml = '';
            $(routingStepData).each(function(i, leg){
                var marker = 'https://mts.googleapis.com/vt/icon/name=icons/spotlight/spotlight-waypoint-a.png&text='+alphabet[i+1].toUpperCase()+'&psize=16&font=fonts/Roboto-Regular.ttf&color=ff333333&ax=44&ay=48&scale=1';
                
                // 72424 tuannm 12222016 start 
                // InnoTech Extends
                routingHtml += '<h3><img src="'+marker+'" />'+leg.theName+'</h3><hr><h4>'+leg.address+ ' ('+leg.duration.text+') </h4>';
                // 72424 tuannm 12222016 end 
                
                routingHtml += '<ol>';
                $(leg.steps).each(function(j, step){
                    routingHtml += '<li>'+step+'</li>';
                });
                routingHtml += '</ol>';
            });

            $('.routing-step').html(routingHtml);
        });
    })(jQuery);
</script>
{/literal}