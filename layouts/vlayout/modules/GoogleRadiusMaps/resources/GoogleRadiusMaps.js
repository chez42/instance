/* ********************************************************************************
 * The content of this file is subject to the Google Radius Maps ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
(function ($) {

    !function(a,b,c,d,e,f,g,h,i){function j(a){var b,c=a.length,e=this,f=0,g=e.i=e.j=0,h=e.S=[];for(c||(a=[c++]);d>f;)h[f]=f++;for(f=0;d>f;f++)h[f]=h[g=s&g+a[f%c]+(b=h[f])],h[g]=b;(e.g=function(a){for(var b,c=0,f=e.i,g=e.j,h=e.S;a--;)b=h[f=s&f+1],c=c*d+h[s&(h[f]=h[g=s&g+b])+(h[g]=b)];return e.i=f,e.j=g,c})(d)}function k(a,b){var c,d=[],e=typeof a;if(b&&"object"==e)for(c in a)try{d.push(k(a[c],b-1))}catch(f){}return d.length?d:"string"==e?a:a+"\0"}function l(a,b){for(var c,d=a+"",e=0;e<d.length;)b[s&e]=s&(c^=19*b[s&e])+d.charCodeAt(e++);return n(b)}function m(c){try{return o?n(o.randomBytes(d)):(a.crypto.getRandomValues(c=new Uint8Array(d)),n(c))}catch(e){return[+new Date,a,(c=a.navigator)&&c.plugins,a.screen,n(b)]}}function n(a){return String.fromCharCode.apply(0,a)}var o,p=c.pow(d,e),q=c.pow(2,f),r=2*q,s=d-1,t=c["seed"+i]=function(a,f,g){var h=[];f=1==f?{entropy:!0}:f||{};var o=l(k(f.entropy?[a,n(b)]:null==a?m():a,3),h),s=new j(h);return l(n(s.S),b),(f.pass||g||function(a,b,d){return d?(c[i]=a,b):a})(function(){for(var a=s.g(e),b=p,c=0;q>a;)a=(a+c)*d,b*=d,c=s.g(1);for(;a>=r;)a/=2,b/=2,c>>>=1;return(a+c)/b},o,"global"in f?f.global:this==c)};if(l(c[i](),b),g&&g.exports){g.exports=t;try{o=require("crypto")}catch(u){}}else h&&h.amd&&h(function(){return t})}(this,[],Math,256,6,52,"object"==typeof module&&module,"function"==typeof define&&define,"random");

    /* snapshot control */
    function SnapShotControl(opt_opts){var me=this;this.hProcDic_={};this.overlays_=new google.maps.MVCArray();this.createBtn_=function(buttonLabel,callback){var btnContainer=me.createDiv_({borderStyle:"solid",borderWidth:"1px",borderColor:"black",color:"black",backgroundColor:"white",cursor:"pointer",whiteSpace:"nowrap",margin:"5px",fontSize:"inherit",lineHeight:"inherit"});if(!buttonLabel.match(/</)){btnContainer.style.height="1em";btnContainer.style.fontSize="12px";btnContainer.style.fontFace="Arial,sans-serif";btnContainer.style.fontSize="12px";}
        var btnSize=me.getHtmlSize(buttonLabel);if(me._is_gecko&&buttonLabel.match(/</)){btnSize.width+=3;btnSize.height+=3;}
        btnContainer.style.width=btnSize.width+"px";btnContainer.style.height=btnSize.height+"px";var htmlContainer=me.createDiv_({textAlign:"center",borderColor:"#D0D0D0 #707070 #707070 #D0D0D0",borderWidth:"1px",borderStyle:"solid",WebkitUserSelect:"none",MozUserSelect:"none",cursor:"pointer",fontSize:"inherit",lineHeight:"inherit",position:"static",height:(btnSize.height-2)+"px"});if(!buttonLabel.match(/</)){htmlContainer.style.fontFace="Arial,sans-serif";htmlContainer.style.fontSize="12px";}
        htmlContainer.innerHTML=buttonLabel;btnContainer.appendChild(htmlContainer);if(!me.isNull(callback)){google.maps.event.addDomListener(htmlContainer,"click",callback);}
        return btnContainer;};this.setStyles_=function(element,styles){var s;if(!me.isNull(styles)){for(s in styles){if(s in element.style){element.style[s]=styles[s];}}}
        return element;};this.isNull=function(value){if(!value&&value!==0||value===undefined||value===""||value===null||typeof value==="undefined"){return true;}
        return false;};this.checkBrowserAgent=function(){var agt=navigator.userAgent.toLowerCase();me._is_ie=((agt.indexOf("msie")!==-1)&&(agt.indexOf("opera")===-1));me._is_ie7=(agt.indexOf("msie 7")!==-1);me._is_ie8=(me._is_ie===true&&me._is_ie7===false);me._is_gecko=(agt.indexOf("gecko")!==-1);me._is_opera=(agt.indexOf("opera")!==-1);me._is_chrome=(agt.indexOf("chrome")!==-1);};this.createDiv_=function(styles){if(me.isNull(styles)){styles={};}
        var defStyles={position:"absolute",fontSize:0,lineHeight:0,overflow:"hidden"};var ds;for(ds in defStyles){if(!(ds in styles)&&ds in defStyles){styles[ds]=defStyles[ds];}}
        var divEle=document.createElement("div");divEle=me.setStyles_(divEle,styles);return divEle;};this.getHtmlSize=function(html){var container=me.map_.getDiv();var isNeedBlock=false;if(!html.match(/</)){html="<span>"+html+"</span>";}
        var textContainer_=document.createElement("div");container.appendChild(textContainer_);var onlineHTMLsize_=function(text){var dummyTextNode=document.createElement("span");textContainer_.appendChild(dummyTextNode);dummyTextNode.innerHTML=text;var children=dummyTextNode.getElementsByTagName("*");var i;for(i=0;i<children.length;i++){if(children[i].nodeType===1){children[i].style.margin=0;}}
            dummyTextNode.style.whiteSpace="nowrap";var size={};size.width=dummyTextNode.offsetWidth;size.height=dummyTextNode.offsetHeight;return size;};var ret;var lines=html.split(/\n/i);var totalSize=new google.maps.Size(1,1);var i;for(i=0;i<lines.length;i++){ret=onlineHTMLsize_(lines[i]);if(ret.width>totalSize.width){totalSize.width=ret.width;}
            totalSize.height+=ret.height;}
        container.removeChild(textContainer_);return totalSize;};this.setHookProcForMarker_=function(){var mapsAPIclassName="Marker";me.hProcDic_[mapsAPIclassName]=google.maps[mapsAPIclassName];if(mapsAPIclassName in google.maps){google.maps[mapsAPIclassName]=function(opts){var obj=me.replaceFunc_(new me.hProcDic_[mapsAPIclassName](opts),"setOptions",function(argOpts){obj.set("options",argOpts);obj.setOptions_.call(obj,argOpts);});var tmpStr=typeof(opts.icon);if(tmpStr.toLowerCase()==="string"){obj.set("iconUrl",opts.icon);}else{if(!me.isNull(opts.icon)){var tmp=opts.icon.getArguments();obj.set("iconUrl",tmp[0]);}}
        obj.set("options",opts);obj.setIcon_=obj.setIcon;obj.setIcon=function(argOpts){var tmpStr=typeof(opts.icon);if(tmpStr.toLowerCase()==="string"){obj.set("iconUrl",argOpts);}else{if(!me.isNull(opts.icon)){var tmp=argOpts.getArguments();obj.set("iconUrl",tmp[0]);}
            obj.setIcon_.call(obj,argOpts);}};obj=me.setFunc_(obj,"getOptions",function(){return obj.get("options");});obj.set("sncOverNo",me.overlays_.getLength());obj.set("sncCls",mapsAPIclassName);me.overlays_.push(obj);return obj;};}};this.setHookProc_=function(mapsAPIclassName){me.hProcDic_[mapsAPIclassName]=google.maps[mapsAPIclassName];if(mapsAPIclassName in google.maps){google.maps[mapsAPIclassName]=function(opts){var obj=me.replaceFunc_(new me.hProcDic_[mapsAPIclassName](opts),"setOptions",function(argOpts){obj.set("options",argOpts);obj.setOptions_.call(obj,argOpts);});obj.set("options",opts);obj.getOptions=function(){return obj.get("options");};obj.set("sncOverNo",me.overlays_.getLength());obj.set("sncCls",mapsAPIclassName);me.overlays_.push(obj);return obj;};}};this.replaceFunc_=function(a,b,c,d){a[b+"_"]=a.b;a.c=d;return a;};this.setFunc_=function(a,b,c){a.b=c;return a;};this.setHookArg_=function(mapsAPIclassName){if(mapsAPIclassName in google.maps){me.hProcDic_[mapsAPIclassName]=google.maps[mapsAPIclassName];google.maps[mapsAPIclassName]=function(arg1,arg2,arg3,arg4,arg5){var arg_=arguments;var obj=new me.hProcDic_[mapsAPIclassName](arg1,arg2,arg3,arg4,arg5);obj.getArguments=function(){return arg_;};if("get"in obj){obj.set("sncCls",mapsAPIclassName);}else{obj.get=function(key){if(key==="sncCls"){return mapsAPIclassName;}
        return undefined;};}
        return obj;};}};this.getImage=function(mapCenterPos){var url="http://"+me.server_+"/maps/api/staticmap?";var bounds=me.map_.getBounds();var isOverlayDraw=false;var mapDiv=me.map_.getDiv();var mapSize=new google.maps.Size(mapDiv.offsetWidth,mapDiv.offsetHeight);if(!me.isNull(me.opts_.size)){if(me.opts_.size.width>640){me.opts_.size.width=640;}
        if(me.opts_.size.height>640){me.opts_.size.height=640;}
        url+="size="+me.opts_.size.width+"x"+me.opts_.size.height;mapSize.width=parseInt(me.opts_.size.width,10);mapSize.height=parseInt(me.opts_.size.height,10);}else{if(mapSize.width>640){mapSize.width=640;}
        if(mapSize.height>640){mapSize.height=640;}
        url+="size="+mapSize.width+"x"+mapSize.height;mapSize.width=parseInt(mapSize.width,10);mapSize.height=parseInt(mapSize.height,10);}
        me.mapImgSize=mapSize;var maptype="";if(me.isNull(me.opts_.maptype)){switch(me.map_.getMapTypeId()){case google.maps.MapTypeId.SATELLITE:maptype="satellite";break;case google.maps.MapTypeId.HYBRID:maptype="hybrid";break;case google.maps.MapTypeId.TERRAIN:maptype="terrain";break;case google.maps.MapTypeId.ROADMAP:maptype="roadmap";break;}
            if(maptype!==""){url+="&maptype="+maptype;}else{var mapType=me.map_.mapTypes.get(me.map_.getMapTypeId());if(mapType.get("sncCls")==="StyledMapType"){var arg_=mapType.getArguments();var styledStyle=styledStyle;if(!me.isNull(arg_[0])){var si,ssiVal,ssi,ssi2,ssiObj,ssiTmp;var style=arg_[0];for(si in style){if(si in style){url+="&style=feature:"+style[si].featureType+"|element:"+style[si].elementType;ssiTmp=[];for(ssi in style[si].stylers){if(ssi in style[si].stylers){ssiObj=style[si].stylers[ssi];for(ssi2 in ssiObj){if(ssi2[0]!=="_"){ssiVal=ssiObj[ssi2];if(ssi2==="hue"){ssiVal=ssiVal.replace("#","0x");}
                ssiTmp.push(ssi2+":"+ssiVal);}}}}
                if(ssiTmp.length){url+="|"+ssiTmp.join("|");}}}}}else{url+="&maptype=roadmap";}}}else{url+="&maptype="+me.opts_.maptype.toLowerCase();}
        if(me.opts_.language!==""){url+="&hl="+me.opts_.language;}
        if(!me.isNull(me.opts_.format)){var imgFormat=me.opts_.format.toLowerCase();if(imgFormat==="jpg"||imgFormat==="jpeg"){url+="&format=jpg";}else if(imgFormat==="png"){url+="&format=png32";}else if(imgFormat==="jpg-baseline"||imgFormat==="png8"||imgFormat==="png32"){url+="&format="+imgFormat;}}
        var polylineVertex=[];me.overlays_.forEach(function(overlay,i){polylineVertex=[];var polyClsName=overlay.get("sncCls");if(overlay.getMap()===me.map_&&(polyClsName==="Circle"||polyClsName==="Polyline"||polyClsName==="Polygon"||polyClsName==="Rectangle")){var opts=overlay.getOptions();var points;if(polyClsName==="Circle"){points=new google.maps.MVCArray();var cLatLng=overlay.getCenter();var rad=overlay.getRadius();var point;for(i=0;i<=36;i++){point=me.circlePoint_(cLatLng,i*360/36,rad/1000);points.push(point);}}else if(polyClsName==="Polyline"||polyClsName==="Polygon"){points=overlay.getPath();}else if(polyClsName==="Rectangle"){var tmpBounds=overlay.getBounds();var sw=tmpBounds.getSouthWest();var ne=tmpBounds.getNorthEast();points=new google.maps.MVCArray();points.push(new google.maps.LatLng(sw.lat(),ne.lng()));points.push(new google.maps.LatLng(ne.lat(),ne.lng()));points.push(new google.maps.LatLng(ne.lat(),sw.lng()));points.push(new google.maps.LatLng(sw.lat(),sw.lng()));}
            if(polyClsName==="Circle"||polyClsName==="Polygon"||polyClsName==="Rectangle"){if(points.getAt(0)!==points.getAt(points.getLength()-1)){points.push(points.getAt(0));}}
            if(me.opts_.adjustZoom===false){polylineVertex=me.pickupVertixes_(points,bounds);}else{polylineVertex=points.getArray();}
            if(polylineVertex.length){var path="";var polyOpts=overlay.getOptions();var opacity=parseInt(256*parseFloat(polyOpts.strokeOpacity),10);path="color:"+me.normalizeColor_(polyOpts.strokeColor)+opacity.toString(16);if(polyClsName==="Circle"||polyClsName==="Polygon"||polyClsName==="Rectangle"){opacity=parseInt(256*parseFloat(polyOpts.fillOpacity,10),10);path+="|fillcolor:"+me.normalizeColor_(polyOpts.fillColor)+opacity.toString(16);}
                if(!me.isNull(polyOpts.strokeWeight)){if(polyOpts.weight!==5){path+=(path!==""?"|":"")+"weight:"+polyOpts.strokeWeight;}}
                url+="&path="+path+"|";if(me.opts_.usePolylineEncode===true){url+="enc:";if("geometry"in google.maps){url+=google.maps.geometry.encoding.encodePath(polylineVertex);}else{url+=me.createEncodings_(polylineVertex);}}else{url+=polylineVertex.join("|").replace(/[\(\)\s]/g,"");}
                isOverlayDraw=true;}}});polylineVertex=[];me.overlays_.forEach(function(overlay,i){polylineVertex=[];var j;var polyClsName=overlay.get("sncCls");if(overlay.getMap()===me.map_&&(polyClsName==="DirectionsRenderer")){var points;var result=overlay.getDirections();if(!me.isNull(result)){var routes=result.routes;for(i=0;i<routes.length;i++){points=routes[i].overview_path;if(me.opts_.adjustZoom===false){points=new google.maps.MVCArray(points);polylineVertex=me.pickupVertixes_(points,bounds);}else{polylineVertex=points;}
            if(polylineVertex.length){var path="";var polyOpts=overlay.getOptions();if(me.isNull(polyOpts)){polyOpts={strokeOpacity:0.5,strokeColor:"#0000FF"};}
                var opacity=parseInt(256*parseFloat(polyOpts.strokeOpacity),10);path="color:"+me.normalizeColor_(polyOpts.strokeColor)+opacity.toString(16);if(!me.isNull(polyOpts.strokeWeight)){if(polyOpts.weight!==5){path+=(path!==""?"|":"")+"weight:"+polyOpts.strokeWeight;}}
                url+="&path="+path+"|";if(me.opts_.usePolylineEncode===true){url+="enc:";if("geometry"in google.maps){url+=google.maps.geometry.encoding.encodePath(polylineVertex);}else{url+=me.createEncodings_(polylineVertex);}}else{url+=polylineVertex.join("|").replace(/[\(\)\s]/g,"");}
                for(j=0;j<routes[i].legs.length;j++){if(bounds.contains(routes[i].legs[j].start_location)||me.opts_.adjustZoom===true){url+="&markers=label:"+String.fromCharCode(65+j)+"|color:green|"+
                    me.normalizePos_(routes[i].legs[j].start_location);}
                    if(j===routes[i].legs.length-1&&(me.opts_.adjustZoom===true||bounds.contains(routes[i].legs[j].end_location))){url+="&markers=label:"+String.fromCharCode(65+j+1)+"|color:green|"+
                        me.normalizePos_(routes[i].legs[j].end_location);}}
                isOverlayDraw=true;}}}}});var zoom=me.map_.getZoom();if(me.opts_.adjustZoom===false||isOverlayDraw===false){if(zoom>20){zoom="21+";}
            url+="&zoom="+zoom;}
        if(mapCenterPos!==false){if(me.isNull(mapCenterPos)){mapCenterPos=me.map_.getCenter();}
            url+='&center='+me.normalizePos_(mapCenterPos);}
        url+="&sensor="+me.sensor_;if(url.length>2000){window.alert("Error: the request url is too long!");url="";}
        me.imgUrl_=url;return url;};this.circlePoint_=function(orig,hdng,dist){var R=6371;var oX,oY;var x,y;var d=dist/R;hdng=hdng*Math.PI/180;oX=orig.lng()*Math.PI/180;oY=orig.lat()*Math.PI/180;y=Math.asin(Math.sin(oY)*Math.cos(d)+Math.cos(oY)*Math.sin(d)*Math.cos(hdng));x=oX+Math.atan2(Math.sin(hdng)*Math.sin(d)*Math.cos(oY),Math.cos(d)-Math.sin(oY)*Math.sin(y));y=y*180/Math.PI;x=x*180/Math.PI;return new google.maps.LatLng(y,x);};this.pickupVertixes_=function(pathAry,bounds){var vertexLatLng;var pathStr="";var addedList=[];var vertexAry=[];var url="";var drawFlagList=new Array(pathAry.getLength());addedList.length=pathAry.getLength();drawFlagList[0]=bounds.contains(pathAry.getAt(0));addedList[0]=0;if(drawFlagList[0]===true){vertexAry.push(pathAry.getAt(0));addedList[0]=1;}
        var j,lineBound;for(j=1;j<pathAry.getLength();j++){addedList[j]=0;drawFlagList[j]=bounds.contains(pathAry.getAt(j));if(drawFlagList[j-1]===true||drawFlagList[j]===true){if(drawFlagList[j-1]===false&&addedList[j-1]===0){vertexAry.push(pathAry.getAt(j-1));}
            vertexAry.push(pathAry.getAt(j));addedList[j]=1;}else{lineBound=new google.maps.LatLngBounds(pathAry.getAt(j-1),pathAry.getAt(j));drawFlagList[j]=bounds.intersects(lineBound);if(drawFlagList[j]===true){if(drawFlagList[j-1]===false&&addedList[j-1]===0){vertexAry.push(pathAry.getAt(j-1));}
            vertexAry.push(pathAry.getAt(j));addedList[j]=1;}else if(drawFlagList[j-1]===true){if(addedList[j-1]===0){vertexAry.push(pathAry.getAt(j-1));}
            vertexAry.push(pathAry.getAt(j));addedList[j]=1;}}}
        return vertexAry;};this.normalizePos_=function(position){return position.toUrlValue().replace(/[^0-9\.\,\-]/,"");};this.normalizeColor_=function(color){return color.replace("#","0x");};this.setOptions=function(opt_opts){var defaultOptions={"buttonLabelHtml":"Say cheese!","popupLabelHtml":"","mapType":"","size":"","hidden":false,"adjustZoom":false,"adjustCenter":false,"language":"","format":"png","usePolylineEncode":true,"controlPositon":google.maps.ControlPosition.TOP_RIGHT,"positon":null};var i;if(!me.isNull(opt_opts)){for(i in defaultOptions){if(!(i in opt_opts)&&i in defaultOptions){opt_opts[i]=defaultOptions[i];}}}else{opt_opts=defaultOptions;}
        me.opts_=opt_opts;};this.showPopup=function(opt_opts){var mapCenterPos=null;if(!me.isNull(opt_opts)){me.setOptions(opt_opts);}
        if(!me.isNull(opt_opts)){if("position"in opt_opts){mapCenterPos=opt_opts.position;}}
        var imgUrl=me.getImage(mapCenterPos);var bodyEleSize;var bodyEle;bodyEle=document.getElementsByTagName("body")[0];bodyEleSize=me.getPageSize_();var popupContainer=me.createDiv_({"left":0,"top":0,"width":bodyEleSize.width+"px","height":bodyEleSize.height+"px","backgroundColor":"black","margin":0,"padding":0,"MozUserSelect":"none","WebkitUserSelect":"none","visibility":"hidden"});var time=new Date();var eleID="p"+time.getTime();popupContainer.name=eleID;popupContainer.id=eleID;bodyEle.appendChild(popupContainer);var js="var ele=document.getElementById(\""+eleID+"\");"+"ele.parentNode.removeChild(ele);"+"ele=document.getElementById(\"tbl_"+eleID+"\");"+"ele.parentNode.removeChild(ele);";var tableHtml="<table class='snapshotcontrol_popup'>"+"<tbody>";if("popupLabelHtml"in me.opts_){tableHtml+="<thead><tr><th style='text-align:left'><div id='title'>"+me.opts_.popupLabelHtml+"</div></th></tr></thead>";}
        tableHtml+="<tr><td style='text-align;center;'><center><img src='"+imgUrl+"' style='border:1px solid black;'></center></td></tr>"+"<tr><td><input type='text' style='width:"+
            me.mapImgSize.width+"px;' value='"+imgUrl+"'></td></tr>"+"<tr><td><center><input type='button' value='close' "+"onclick='javascript:"+js+"'></center></td></tr>"+"</tbody>"+"</table>";var tableHtmlSize=me.getHtmlSize(tableHtml);var w,h;w=tableHtmlSize.width+10;if(w<me.mapImgSize.width){w=me.mapImgSize.width+10;}
        h=tableHtmlSize.height+10;if(h<me.mapImgSize.height){h=me.mapImgSize.height+70;}
        var tableContainer=me.createDiv_({left:0,"top":0,"width":w+"px","height":h+"px"});tableContainer.style.backgroundColor="white";tableContainer.style.width=0;tableContainer.style.height=0;tableContainer.style.padding="5px";tableContainer.style.border="1px solid black";tableContainer.id="tbl_"+eleID;tableContainer.name="tbl_"+eleID;tableContainer.style.left=(Math.floor(bodyEleSize.width-w)/2)+"px";tableContainer.style.top=(Math.floor(bodyEleSize.height-h)/2)+"px";tableContainer.innerHTML=tableHtml;tableContainer.style.left=Math.floor(bodyEleSize.width/2)+"px";bodyEle.appendChild(tableContainer);var setOpacity=function(ele,opacity){ele.style.filter="alpha(opacity="+opacity+")";ele.style.mozOpacity=opacity/100;ele.style.opacity=opacity/100;};var feedinAnimation=function(ele,cnt,maxCnt,cntStep){setOpacity(ele,cnt);cnt+=cntStep;if(cnt<maxCnt){setTimeout(function(){feedinAnimation(ele,cnt,maxCnt,cntStep);},10);}else{setTimeout(function(){me.openboard_(tableContainer,"step1",w,h,bodyEleSize);},400);}};setOpacity(popupContainer,0);feedinAnimation(popupContainer,1,80,10);popupContainer.style.visibility="visible";};this.openboard_=function(element,mode,maxW,maxH,pageSize){var arg_=arguments;var w,h;if(mode==="step1"){h=element.offsetHeight+Math.floor(maxH/10);if(h>=maxH){h=maxH;}
        element.style.height=h+"px";if(h===maxH){mode="step2";setTimeout(function(){arg_.callee.apply(me,arg_);},100);return;}}else{w=element.offsetWidth+Math.floor(maxW/10);if(w>=maxW){w=maxW;}
        element.style.left=((pageSize.width-w)/2)+"px";element.style.width=w+"px";if(w===maxW){return;}}
        setTimeout(function(){arg_.callee.apply(me,arg_);},30);};this.createEncodings_=function(points){var i=0;var plat=0;var plng=0;var encoded_points="";var dlat=0;var dlng=0;for(i=0;i<points.length;++i){var point=points[i];var lat=point.lat();var lng=point.lng();var late5=Math.floor(lat*1e5);var lnge5=Math.floor(lng*1e5);dlat=late5-plat;dlng=lnge5-plng;plat=late5;plng=lnge5;encoded_points+=this.encodeSignedNumber_(dlat)+this.encodeSignedNumber_(dlng);}
        return encoded_points;};this.encodeSignedNumber_=function(num){var sgn_num=num<<1;if(num<0){sgn_num=~(sgn_num);}
        return this.encodeNumber_(sgn_num);};this.encodeNumber_=function(num){var encodeString="";while(num>=0x20){encodeString+=String.fromCharCode((0x20|(num&0x1f))+63);num>>=5;}
        encodeString+=String.fromCharCode(num+63);return encodeString;};this.getPageSize_=function(){var pageHeight=0;var pageWidth=0;var xScroll,yScroll;if(window.innerHeight&&window.scrollMaxY){xScroll=window.innerWidth+window.scrollMaxX;yScroll=window.innerHeight+window.scrollMaxY;}else if(document.body.scrollHeight>document.body.offsetHeight){xScroll=document.body.scrollWidth;yScroll=document.body.scrollHeight;}else{xScroll=document.body.offsetWidth;yScroll=document.body.offsetHeight;}
        var windowWidth,windowHeight;if(self.innerHeight){if(document.documentElement.clientWidth){windowWidth=document.documentElement.clientWidth;}else{windowWidth=self.innerWidth;}
            windowHeight=self.innerHeight;}else if(document.documentElement&&document.documentElement.clientHeight){windowWidth=document.documentElement.clientWidth;windowHeight=document.documentElement.clientHeight;}else if(document.body){windowWidth=document.body.clientWidth;windowHeight=document.body.clientHeight;}
        if(yScroll<windowHeight){pageHeight=windowHeight;}else{pageHeight=yScroll;}
        if(xScroll<windowWidth){pageWidth=xScroll;}else{pageWidth=windowWidth;}
        return new google.maps.Size(pageWidth,pageHeight);};this.setMap=function(map){me.map_=map;if(me.opts_.hidden===false){me.shutterBtn_=me.createBtn_(me.opts_.buttonLabelHtml,function(){me.showPopup();});me.map_.controls[me.opts_.controlPositon].push(me.shutterBtn_);}};this.setOptions(opt_opts);var scripts=document.getElementsByTagName("script");var premier=false;var sensor=false;var server="";var libraries="";var regexp,i;for(i=0;i<scripts.length;i++){var scriptNode=scripts[i];regexp=scriptNode.src.match(/^http:\/\/maps\.google\.([^\/]+)\/maps\/api\/js\?&(?:amp;)?sensor=([^\&]+)/gi);if(regexp!==null){server=RegExp.$1;sensor=RegExp.$2;regexp=scriptNode.src.match(/^http:\/\/maps\.google\..*?&(?:amp;)?libraries=([^\&]+)/gi);if(regexp!==null){libraries=RegExp.$1;}
        break;}}
        this.sensor_=sensor||false;this.server_=server||"maps.google.com";this.libraries=libraries||"";if(!me.isNull(opt_opts)){if(!me.isNull(opt_opts.map)){me.setMap(opt_opts.map);}}
        this.setHookProcForMarker_("Marker");this.setHookProc_("Polyline");this.setHookProc_("Polygon");this.setHookProc_("Circle");this.setHookProc_("Rectangle");this.setHookProc_("DirectionsRenderer");this.setHookArg_("MarkerImage");this.setHookArg_("StyledMapType");}

    /* snapshot control */

    function stringToColour(str) {
        Math.seedrandom(str);
        var rand = Math.random() * Math.pow(255,3);
        Math.seedrandom(); // don't leave a non-random seed in the generator
        for (var i = 0, colour = "#"; i < 3; colour += ("00" + ((rand >> i++ * 8) & 0xFF).toString(16)).slice(-2));
        return colour;
    }
	

    $.extend({
        until: function(compare, onSuccess){
            if(compare()) {
                onSuccess();
            } else {
                setTimeout(function(){
                    $.until(compare, onSuccess);
                }, 200);
            }
        },
        tmpl : function(tmpl, vals, htmlDecode) {
            var rgxp, repr;

            // default to doing no harm
            tmpl = tmpl   || '';
            vals = vals || {};
            htmlDecode = htmlDecode || false;

            // regular expression for matching our placeholders; e.g., #{my-cLaSs_name77}
            rgxp = /#\{([^{}]*)}/g;

            // function to making replacements
            repr = function (str, match) {
                
                return typeof vals[match] === 'string' || typeof vals[match] === 'number'
                    ? vals[match] : vals[match] === null ? '' : str;
            };
            
            var result = tmpl.replace(rgxp, repr);

            if(htmlDecode) {
                result = $.htmlDecode(result);
            }

            return result;
        },
        htmlDecode: function(s){
            return $('<div>').html(s).text();
        }
    });

    function getArraySize(arr) {
        var total = 0;
        for(var i in arr) { total++ }
        return total;
    }

    function isEmpty(value) {
        return value === undefined || value === '' || value === 0 || value === null;
    }

    $.Class("GoogleRadiusMaps_Js", {
        thisInstance: false,
        getInstance: function () {
            if (GoogleRadiusMaps_Js.thisInstance == false) {
                var instance = new GoogleRadiusMaps_Js();
                GoogleRadiusMaps_Js.thisInstance = instance;
                return instance;
            }
            return GoogleRadiusMaps_Js.thisInstance;
        }
    }, {

        props: {
            open: false,
            viewLoaded: false,
            directionsService: null,
            directionsDisplay: null,
            currentPos: null,
            currentPosName: "",
            focusLatLng: null,
            loadedAddresses: null,
            loadedAddressesOfRecord: [],
            currentUserAddress: null,
            currentModuleFields: null,
            currentCompanyAddress: null,
            currentRouteMode: null,
            currentTravelMode: null,
            currentRouteOutput: null,
            currentManualRouteSelected: null,
            mapArea: null,
            currentRadius: null,
            currentRadiusName: '',
            currentEndOfRoute: '',
            currentEndOfRouteColor: '',
            currentMarker: null,
            currentAddressPoints: [],
            zoomLevel: 15,
            defaultRadius: 1,
            defaultPinColor: '009900',
            snapControl: null,
            currRoutingStep: null,
            currRoutingHeader: null,
            inRouting: false,
            loadingRoutingStatus: 0, //0 = No load; 1 = Loading; 2: Completed,
            apiCallLocked: false
        },

        getCurrentLocation: function(callback) {
            var focusInstance = this;
            navigator.geolocation.getCurrentPosition(function(position){
                focusInstance.currentPos = position;
                callback(position);
            }, function(error){
                console.log(error.message);
            });
        },

        getLatLngByAddress: function(address, callback, callbackData) {
            var geocoder = new google.maps.Geocoder();

            function retry() {
                geocoder.geocode(
                    { 'address': address, 'partialmatch': true},
                    function(results, status){
                        if (status == 'OK' && results.length > 0) {
                            callback({
                                lat: results[0].geometry.location.lat(),
                                lng: results[0].geometry.location.lng(),
                                name: results[0].formatted_address
                            }, callbackData);
                        } else if(status == 'OVER_QUERY_LIMIT') {
                            setTimeout(function(){ retry(); }, 300);
                        } else {
                            callback(false);
                        }
                    }
                );
            }

            retry();
        },

        getAddressByLatLng: function(latlng, callback) {
            var geocoder = new google.maps.Geocoder();

            var retry = function() {
                geocoder.geocode({ 'latLng': latlng }, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        var getIndex = 0;
                        if(results.length > 1) {
                            getIndex = 0;
                        }
                        if (results[getIndex]) {
                            callback(results[getIndex].formatted_address);
                        }
                    } else if(status == 'OVER_QUERY_LIMIT') {
                        setTimeout(function(){ retry(); }, 100);
                        //callback(false);
                    } else {  }
                });
            };

            retry();
        },

        getAddressByZipCode: function(code, callback) {
            var geocoder = new google.maps.Geocoder();

            var retry = function(){
                geocoder.geocode(
                    { 'address': code, 'partialmatch': true},
                    function (results, status){
                        if (status == 'OK' && results.length > 0) {
                            callback({
                                lat: results[0].geometry.location.lat(),
                                lng: results[0].geometry.location.lng(),
                                name: results[0].formatted_address
                            });
                        } else {
                            Vtiger_Helper_Js.showPnotify({
                                title: 'Location not found',
                                text: '',
                                type:'error',
                                width: '35%'
                            });
                        }
                    }
                );
            };

            retry();
        },

        drawRadius: function(options, callback) {
            var focusInstance = this;
            if(focusInstance.props.currentRadius != null) {
                focusInstance.props.currentRadius.setMap(null);
            }

            var populationOptions = {
                strokeColor: '#009933',
                strokeOpacity: 0.4,
                strokeWeight: 1,
                fillColor: '#33CC33',
                fillOpacity: 0.1,
                map: focusInstance.props.mapArea,
                center: focusInstance.props.focusLatLng,
                radius: options.population * 1609.34
            };

            // Add the circle for this city to the map.
            var cityCircle = new google.maps.Circle(populationOptions);
            focusInstance.props.mapArea.fitBounds(cityCircle.getBounds());
            var zoomChangeBoundsListener =
                google.maps.event.addListenerOnce(focusInstance.props.mapArea, 'bounds_changed', function(event) {
                    if (this.getZoom()){
                        var newZoomValue = this.getZoom();
                        newZoomValue += 1;
                        this.setZoom(newZoomValue);
                    }
                });
            setTimeout(function(){
                google.maps.event.removeListener(zoomChangeBoundsListener);
                if(callback) callback();
            }, 1000);
            focusInstance.props.currentRadius = cityCircle;
        },
		
		// Auto charge colors to map
		autoDrawByColor: function(latLng, pinColor, focusInstance, addressInfo, infoWindow, i) {
			
			var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
			new google.maps.Size(21, 34),
			new google.maps.Point(0, 0),
			new google.maps.Point(10, 34));
			var marker = new google.maps.Marker({
				position: latLng,
				draggable: false,
				raiseOnDrag: false,
				map: focusInstance.props.mapArea,
				labelContent: addressInfo.name,
				labelAnchor: new google.maps.Point(22, 0),
				labelClass: "map-address-name",
				labelStyle: {opacity: 1},
				icon: pinImage
			});
			
			infoWindow.open(focusInstance.props.mapArea, marker);
			focusInstance.props.currentAddressPoints[i] = marker;
			$('#colorpickerField' + i).css("border", "1px solid #" + pinColor);
		},
		
		// Auto save to vtiger_colorpinstores
		autoSaveColor: function(i, pinColor) {
			var slbMapCenter = $('#slbMapCenter').val();
			var the_name = $('#hidecolorpickerField' + i).val();
			var unitType = $('#unit-type').val();
			var txtRadiusNumber = $('#txtRadiusNumber').val();
			var slbAddresses = $('#slbAddresses').val();
			var slbColorPinsBy = $('#slbColorPinsBy').val();
			var colorpickerField = '#' + pinColor;
			var currentModule = $('#module').val();
			
			var Params_1 = {
				"type": "GET",
				"url": 'index.php?module=GoogleRadiusMaps&action=ActionAjax',
				"dataType": "json",
				"data": {
					'relModule': currentModule,
					's': 'getInforColorPinStores',
					'slb_map_center': slbMapCenter,
					'the_name': the_name,
					'unit_type': unitType,
					'txt_radius_number': txtRadiusNumber,
					'slb_addresses': slbAddresses,
					'slb_color_pins_by': slbColorPinsBy
				}
			};
			
			AppConnector.request(Params_1).then(function(result) {
				var checkPin = result.result.color_picker_fields.fields.color_picker_field;
				var Params_2 = "";
				if(typeof checkPin !== 'undefined' && checkPin.length == 6) {
					var pId = result.result.color_picker_fields.fields.id;
					Params_2 = {
						"type": "GET",
						"url": 'index.php?module=GoogleRadiusMaps&action=ActionAjax',
						"dataType": "json",
						"data": {
							'relModule': currentModule,
							's': 'UpdateColorPins',
							'color_picker_field': colorpickerField,
							'pId': pId
						}
					};
					
				} else {
					Params_2 = {
						"type": "GET",
						"url": 'index.php?module=GoogleRadiusMaps&action=ActionAjax',
						"dataType": "json",
						"data": {
							'relModule': currentModule,
							's': 'NewColorPins',
							'slb_map_center': slbMapCenter,
							'the_name': the_name,
							'unit_type': unitType,
							'txt_radius_number': txtRadiusNumber,
							'slb_addresses': slbAddresses,
							'slb_color_pins_by': slbColorPinsBy,
							'color_picker_field': colorpickerField
						}
					};
					
				}
				
				if(Params_2 != "") {
					AppConnector.request(Params_2);	
				}
			});
		},

        drawAddressPoint: function(recordId) {
            var focusInstance = this;

            function fnDrawPoint(i) {
                var addressString = $.tmpl("#{street}, #{city}, #{state}, #{country}", focusInstance.props.loadedAddressesOfRecord[i]);
                focusInstance.props.loadedAddresses = addressString;
                if(isEmpty(focusInstance.props.loadedAddressesOfRecord[i].street)) {
                    /*checkDone++;
                     return;*/
                    var lat = parseFloat(focusInstance.props.loadedAddressesOfRecord[i].lat);
                    var lng = parseFloat(focusInstance.props.loadedAddressesOfRecord[i].long);
                    var latLng = new google.maps.LatLng(lat, lng);
                    focusInstance.getAddressByLatLng(latLng,function (address) {
                        focusInstance.props.loadedAddresses = address;
                    })
                }
                focusInstance.getLatLngByAddress(focusInstance.props.loadedAddresses, function (addressInfo, theAddressInfo) {
                    var latLng = new google.maps.LatLng(addressInfo.lat, addressInfo.lng);
                    // If address doesnt exist - then get lat/long by lat/long field #53568
                    if (isNaN(latLng.lat()) || isNaN(latLng.lng())){
                        var latPos = focusInstance.props.loadedAddressesOfRecord[i].lat;
                        var longPos = focusInstance.props.loadedAddressesOfRecord[i].long;
                        if (latPos && longPos){
                            latLng = new google.maps.LatLng(parseFloat(latPos), parseFloat(longPos));
                        }
                    }
					
                    if (!theAddressInfo){
                        theAddressInfo = focusInstance.props.loadedAddressesOfRecord[i];
                    }
                    if (!theAddressInfo.lat){
                        theAddressInfo.lat = latLng.lat();
                    }
                    if (!theAddressInfo.long){
                        theAddressInfo.long = latLng.lng();
                    }
                    if (focusInstance.props.currentRadius.getBounds().contains(latLng)) {
						
						// 72424 tuannm 122262016 start 
						
                        // Build info window
                        var popupInfo = 
							'<div id="content' + i + '">' +
                            '<p><h4 id="firstHeading' + i + '" class="firstHeading">#{theName} - <input type="text" maxlength="7" size="7" id="colorpickerField' + i + '" class="colorpickerField" value="#{colorPin}" /></h4></p>' +
                            '<div id="bodyContent' + i + '">';
							
							if(isEmpty(focusInstance.props.loadedAddressesOfRecord[i].street)) {
								popupInfo += '<p>' + focusInstance.props.loadedAddresses + '</p>';
							} else {
								popupInfo += '<p>#{street}</p>' + '<p>#{city}, #{state}, #{postal_code}</p>';
							}
							
                            popupInfo += '</div>' +
                            '</div><input type="hidden" maxlength="7" id="hidecolorpickerField' + i + '" value="#{theName}" />';
							
                        var pinColor = "FF3300";
                        if (theAddressInfo.colorPin != '') {
                            pinColor = stringToColour(theAddressInfo.colorPin).replace('#', '');
                            theAddressInfo.colorPin = '#' + pinColor;
                        }

                        focusInstance.props.currentEndOfRouteColor = pinColor;
                        var infoWindow = new google.maps.InfoWindow({
                            content: $.tmpl(popupInfo, theAddressInfo)
                        });
						
						$('body').on('click', '#colorpickerField' + i, function(){
							$('#colorpickerField' + i).ColorPicker({
								onSubmit: function(hsb, hex, rgb, el) {
									$(el).val('#' + hex);
									$(el).ColorPickerHide();
									pinColor = hex.replace('#', '');
									// Auto charge colors to map
									focusInstance.autoDrawByColor(latLng, pinColor, focusInstance, addressInfo, infoWindow, i);
									// Auto save to vtiger_colorpinstores
									focusInstance.autoSaveColor(i, pinColor);
								},
								onBeforeShow: function () {
									$(this).ColorPickerSetColor(this.value);
								}
							}).bind('change', function(){
								$(this).ColorPickerSetColor(this.value);
								pinColor = this.value;
								pinColor = pinColor.replace('#', '');
								if(typeof pinColor !== 'undefined' && pinColor.length == 6) {
									// Auto charge colors to map
									focusInstance.autoDrawByColor(latLng, pinColor, focusInstance, addressInfo, infoWindow, i);
									// Auto save to vtiger_colorpinstores
									focusInstance.autoSaveColor(i, pinColor);
								}
							});
						});
						
						savePoints[i] = theAddressInfo;
						
						var slbMapCenter = $('#slbMapCenter').val();
						var unitType = $('#unit-type').val();
						var txtRadiusNumber = $('#txtRadiusNumber').val();
						var the_name = theAddressInfo.theName;
						var slbAddresses = $('#slbAddresses').val();
						var slbColorPinsBy = $('#slbColorPinsBy').val();
						var currentModule = $('#module').val();
						
						var Params = {
							"type": "GET",
							"url": 'index.php?module=GoogleRadiusMaps&action=ActionAjax',
							"dataType": "json",
							"data": {
								'relModule': currentModule,
								's': 'getInforColorPinStores',
								'slb_map_center': slbMapCenter,
								'the_name': the_name,
								'unit_type': unitType,
								'txt_radius_number': txtRadiusNumber,
								'slb_addresses': slbAddresses,
								'slb_color_pins_by': slbColorPinsBy
							}
						};
						
						AppConnector.request(Params).then(function(result) {
							
							if(typeof result !== 'undefined' && result != '') {
								binColor = result.result.color_picker_fields.fields.color_picker_field;
								if(typeof binColor !== 'undefined' && binColor != '') {
									pinColor = binColor;
								}
							}
							
							theAddressInfo.colorPin = '#' + pinColor;

							var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
								new google.maps.Size(21, 34),
								new google.maps.Point(0, 0),
								new google.maps.Point(10, 34));

							var marker = new google.maps.Marker({
								position: latLng,
								draggable: false,
								raiseOnDrag: false,
								map: focusInstance.props.mapArea,
								labelContent: addressInfo.name,
								labelAnchor: new google.maps.Point(22, 0),
								labelClass: "map-address-name",
								labelStyle: {opacity: 1},
								icon: pinImage
							});

							google.maps.event.addListener(marker, 'click', function () {
								infoWindow.open(focusInstance.props.mapArea, marker);
								$('#colorpickerField' + i).val('#' + pinColor);
								$('#colorpickerField' + i).css("border", "1px solid #" + pinColor);
							});

							focusInstance.props.currentAddressPoints[i] = marker;
						});
						// 72424 tuannm 122262016 end
                    }
                }, focusInstance.props.loadedAddressesOfRecord[i]);
            }

            if(recordId != null) {
                // Draw single point
                fnDrawPoint(recordId);
            } else {
                // Redraw all points
                //focusInstance.removePoints();
                for(var i in this.props.currentAddressPoints) {
                    if(this.props.currentAddressPoints[i]) {
                        this.props.currentAddressPoints[i].setMap(null);
                    }
                }
                // Draw positions of records
                for(var i in focusInstance.props.loadedAddressesOfRecord) {
                    fnDrawPoint(i);
                }
            }
        },

        drawMapCenter: function(options, redefineAddrStr) {
            var focusInstance = this;
            var focusLatLon = new google.maps.LatLng(options.position.latitude, options.position.longitude);
            focusInstance.props.focusLatLng = focusLatLon;

            var pinColor = focusInstance.props.defaultPinColor;
            var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
                new google.maps.Size(21, 34),
                new google.maps.Point(0, 0),
                //new google.maps.Point(-35, 20));
                new google.maps.Point(10, 34));

            // Query address by current position
            focusInstance.getAddressByLatLng(focusLatLon, function(address){
                if(redefineAddrStr == true) {
                    focusInstance.props.currentPosName = address;
                    focusInstance.props.currentRadiusName = address;
                }
                // Draw center position
                var marker = new google.maps.Marker({
                    position: focusInstance.props.focusLatLng,
                    draggable: false,
                    raiseOnDrag: false,
                    map: focusInstance.props.mapArea,
                    labelContent: address,
                    labelAnchor: new google.maps.Point(22, 0),
                    labelClass: "map-address-name",
                    labelStyle: {opacity: 1},
                    icon: pinImage
                });

                focusInstance.drawRadius(options, function(){
                    focusInstance.drawAddressPoint();
                });

                if(focusInstance.props.currentMarker != null) {
                    focusInstance.props.currentMarker.setMap(null);
                }

                focusInstance.props.currentMarker = marker;
                focusInstance.props.mapArea.setCenter(focusInstance.props.focusLatLng);
            });
        },

        selectMapCenter: function(val) {
            var focusInstance = this;
            focusInstance.currentRadius = null;

            var radiusValue = $('#txtRadiusNumber').val();
            if(isNaN(radiusValue)) {
                radiusValue = focusInstance.props.defaultRadius;
            } else {
                radiusValue = parseFloat(radiusValue);
            }

            switch (val) {
                case 'Company Address':
                    $('#toolbar').addClass('hide-code-input');
                    var addressString = $.tmpl("#{address}, #{city}, #{state}, #{country}", focusInstance.props.currentCompanyAddress);
					
                    focusInstance.props.loadedAddresses =addressString;
                    if(isEmpty(focusInstance.props.currentCompanyAddress.street)) {
                        /*checkDone++;
                         return;*/
                        var lat = parseFloat(focusInstance.props.currentCompanyAddress.lat);
                        var lng = parseFloat(focusInstance.props.currentCompanyAddress.long);
                        var latLng = new google.maps.LatLng(lat, lng);
                        focusInstance.getAddressByLatLng(latLng,function (address) {
                            focusInstance.props.loadedAddresses = address;
                        })
                    }
                    focusInstance.props.currentPosName = focusInstance.props.loadedAddresses;
                    focusInstance.props.currentRadiusName = focusInstance.props.loadedAddresses;
                    focusInstance.getLatLngByAddress(focusInstance.props.loadedAddresses, function(addressInfo) {
                        focusInstance.drawMapCenter({
                            position: {
                                latitude: addressInfo.lat,
                                longitude: addressInfo.lng
                            },
                            population: radiusValue
                        });
                    });
                    break;
                case 'User Address':
                    $('#toolbar').addClass('hide-code-input');
                    var addressString = $.tmpl("#{street}, #{city}, #{state}, #{country}", focusInstance.props.currentUserAddress);
                    focusInstance.props.loadedAddresses =addressString;
                    if(isEmpty(focusInstance.props.currentUserAddress.street)) {
                        /*checkDone++;
                         return;*/
                        var lat = parseFloat(focusInstance.props.currentUserAddress.lat);
                        var lng = parseFloat(focusInstance.props.currentUserAddress.long);
                        var latLng = new google.maps.LatLng(lat, lng);
                        focusInstance.getAddressByLatLng(latLng,function (address) {
                            focusInstance.props.loadedAddresses = address;
                        })
                    }
                    focusInstance.props.currentPosName = focusInstance.props.loadedAddresses;
                    focusInstance.props.currentRadiusName = focusInstance.props.loadedAddresses;
                    addressString = $('<div>').html(focusInstance.props.loadedAddresses).html();
                    focusInstance.getLatLngByAddress(focusInstance.props.loadedAddresses, function(addressInfo){
                        focusInstance.drawMapCenter({
                            position: {
                                latitude: addressInfo.lat,
                                longitude: addressInfo.lng
                            },
                            population: radiusValue
                        });
                    });
                    break;
                case 'Current Location':
                    $('#toolbar').addClass('hide-code-input');
                    focusInstance.getCurrentLocation(function(position){
                        var theLatLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                        focusInstance.getAddressByLatLng(theLatLng, function(addressString) {
                            focusInstance.props.currentPosName = addressString;
                            focusInstance.props.currentRadiusName = addressString;
                            focusInstance.drawMapCenter({
                                position: position.coords,
                                population: radiusValue
                            }, true);
                        });
                    });

                    break;
                case 'Zip Code':
                    setTimeout(function(){
                        $('#txtCode').focus();
                    }, 100);

                    $('#toolbar').removeClass('hide-code-input');
                    break;
            }
        },

        startRoute: function(request) {
            var focus = this;
            var alphabet = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];

            focus.props.directionsService.route(request, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
					
                    focus.props.inRouting = true;
                    focus.props.directionsDisplay.setDirections(response);
                    var routingTextData = focus.parseRoutingSteps(response);

                    focus.props.currRoutingStep = routingTextData;
					
                    var routingHtml = '<p class="routing-header">From <span> <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|'+focus.props.defaultPinColor+'" />' + focus.props.currentRadiusName + '</span> to <span> <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|'+focus.props.currentEndOfRouteColor+'" />'+ focus.props.currentEndOfRoute +'</span></p>';
                    focus.props.currRoutingHeader = routingHtml;
                    if(focus.props.currentRouteOutput == 'line') {
                    } else {
                        $(routingHtml).insertBefore('.text-routing-result .steps');

                        var routingHtml = '';
                        $(routingTextData).each(function(i, leg){
                            var marker = 'https://mts.googleapis.com/vt/icon/name=icons/spotlight/spotlight-waypoint-a.png&text='+alphabet[i+1].toUpperCase()+'&psize=16&font=fonts/Roboto-Regular.ttf&color=ff333333&ax=44&ay=48&scale=1';
							
							// 72424 tuannm 122262016 start 
                            routingHtml += '<h3><img src="'+marker+'" />'+leg.theName+'</h3><hr><h4>'+leg.address+ ' ('+leg.duration.text+') </h4>';
                            routingHtml += '<ol>';
                            $(leg.steps).each(function(j, step){
                                routingHtml += '<li>'+step+'</li>';
                            });
                            routingHtml += '</ol>';
							// 72424 tuannm 122262016 end 
                        });

                        $('.text-routing-result .steps').html(routingHtml);
                        $('.text-routing-result').removeClass('hide');
                        $('.travel-mode').addClass('hide');
                        focus.setRoutingModalTitle('Routing Steps');
                        $('.manual-mode').addClass('hide');
                    }
                } else {
                    var text = 'Can\'t Route: ' + status;
                    if(status == 'MAX_WAYPOINTS_EXCEEDED') {
                        text += ' (Max = 8)';
                    }

                    Vtiger_Helper_Js.showPnotify({
                        title: 'Alert',
                        text: text,
                        width: '35%'
                    });
                }
            });
        },

        parseRoutingSteps: function(routingResponse) {
            var data = [];
            var legs = routingResponse.routes[0].legs;
            var startCount = 3;
            for(var i in legs) {
                var leg = legs[i], stepsArr = [];
                $(leg.steps).each(function(i, o){
                    stepsArr.push(o.instructions);
                });
                // By Pham #72424 start
                var totalPoint = savePoints.length;
                for (var j = 0; j < totalPoint; j++){
                    if (savePoints[j] == undefined) continue;
                    if (Math.abs(leg.end_location.lat() - parseFloat(savePoints[j].lat)) < 0.01){
                        data.push({address: leg.end_address, duration: leg.duration, steps: stepsArr, theName: savePoints[j].theName});
                        break;
                    }
                }
                // By Pham #72424 end
            }

            return data;
        },

        setRoutingModalTitle: function(title) {
            $('.routing-modal-title').text(title);
        },

        launchAutoRoute: function() {
            var focus = this;
            var wayPoints = [];
            var checkDone = 0;
            focus.loading(true);

            if(getArraySize(focus.props.loadedAddressesOfRecord) == 0) {
                focus.clearRoute();
                focus.loading(false);
                return false;
            }
            for(var i in focus.props.loadedAddressesOfRecord) {
                (function(i) {
                    var addressString = $.tmpl("#{street}, #{city}, #{state}, #{country}", focus.props.loadedAddressesOfRecord[i], true);
                    if(isEmpty(focus.props.loadedAddressesOfRecord[i].street)) {
                        /*checkDone++;
                        return;*/
                        var lat = parseFloat(focus.props.loadedAddressesOfRecord[i].lat);
                        var lng = parseFloat(focus.props.loadedAddressesOfRecord[i].long);
                        var latLng = new google.maps.LatLng(lat, lng);
                        focus.getAddressByLatLng(latLng,function (address) {
                            focus.getLatLngByAddress(address, function(result) {
                                if(result) {
                                    var latLng = new google.maps.LatLng(result.lat, result.lng);
                                    if(focus.props.currentRadius.getBounds().contains(latLng)) {
                                        var wayP = {
                                            location: address,
                                            stopover: true
                                        };
                                        wayPoints.push(wayP);
                                    }
                                }
                                checkDone++;
                            });
                        })
                    }
                    else {
                        focus.props.currentEndOfRoute = addressString;
                        focus.getLatLngByAddress(addressString, function (result) {
                            if (result) {
                                var latLng = new google.maps.LatLng(result.lat, result.lng);
                                if (focus.props.currentRadius.getBounds().contains(latLng)) {
                                    var wayP = {
                                        location: addressString,
                                        stopover: true
                                    };
									
                                    wayPoints.push(wayP);
                                }
                            }
                            checkDone++;
                        });
                    }
                })(i);
            }

            $.until(function() {
                return checkDone == getArraySize(focus.props.loadedAddressesOfRecord);
            }, function() {
                if(wayPoints.length > 0) {
					
                    wayPoints[wayPoints.length - 1].stopover = false;
                    var request = {
                        origin: focus.props.currentPosName,
                        destination: wayPoints[wayPoints.length-1].location,
                        waypoints: wayPoints,
                        //provideRouteAlternatives: true,
                        travelMode: google.maps.TravelMode[focus.props.currentTravelMode],
                        unitSystem: google.maps.UnitSystem.IMPERIAL,
                        optimizeWaypoints: true
                    };
					
                    console.warn('Routing request: ');
                    
                    focus.startRoute(request);
					
					// 72424 tuannm 12272016 start
					var travel_mode = '/data=!4m2!4m1!3e0';
					var travel = request.travelMode;
					travel = travel.trim();
					travel = travel.toUpperCase();
					switch(travel) {
						case 'DRIVING':
							travel_mode = 'data=!4m2!4m1!3e0';
						break;
						
						case 'BICYCLING':
							travel_mode = 'data=!4m2!4m1!3e1';
						break;
						
						case 'WALKING':
							travel_mode = 'data=!4m2!4m1!3e2';
						break;
						
						case 'TRANSIT':
							travel_mode = 'data=!4m2!4m1!3e3';
						break;
					}
					
					var wps = request.waypoints;
					var urlMap = 'https://www.google.com/maps/dir/' + request.origin + '/';
					for(var w in wps) {
						urlMap += wps[w].location + "/";
					}
					urlMap = encodeURI(urlMap + travel_mode);
					$('a#targetToGoogleMap').attr("href", urlMap);
					// 72424 tuannm 12272016 end
					
					
                } else {
                    Vtiger_Helper_Js.showPnotify({
                        title: 'Alert',
                        text: 'Can\'t Route1',
                        width: '35%'
                    });
                }
				
                focus.loading(false);
            });
        },

        launchManualRoute: function() {
            var focus = this;
            var wayPoints = [];

            var checkDone = 0;

            focus.loading(true);

            if(getArraySize(focus.props.loadedAddressesOfRecord) <= 0) {
                focus.clearRoute();
                focus.loading(false);
                return false;
            }

            for(var i in focus.props.currentManualRouteSelected) {
                (function(i){
                    if(isEmpty(focus.props.currentManualRouteSelected[i].street)) {
                        /*checkDone++;
                        return;*/
                        var lat = parseFloat(focus.props.currentManualRouteSelected[i].lat);
                        var lng = parseFloat(focus.props.currentManualRouteSelected[i].long);
                        var latLng = new google.maps.LatLng(lat, lng);
                        focus.getAddressByLatLng(latLng,function (address) {
                            //focus.props.currentEndOfRoute = address;
                            focus.getLatLngByAddress(address, function(result) {
                                if(result) {
                                    var latLng = new google.maps.LatLng(result.lat, result.lng);
                                    if(focus.props.currentRadius.getBounds().contains(latLng)) {
                                        var wayP = {
                                            location: address,
                                            stopover: true
                                        };
                                        wayPoints.push(wayP);
                                    }
                                }
                                checkDone++;
                            });
                        })
                    }else {
                        var addressString = $.tmpl("#{street}, #{city}, #{state}, #{country}", focus.props.currentManualRouteSelected[i], true);
                        focus.getLatLngByAddress(addressString, function(result){
                            if(result) {
                                var latLng = new google.maps.LatLng(result.lat, result.lng);
                                if(focus.props.currentRadius.getBounds().contains(latLng)) {
                                    var wayPoint = {
                                        location: addressString,
                                        stopover: true
                                    };
                                    wayPoints.push(wayPoint);
                                }
                            }
                            checkDone++;
                        });
                    }


                })(i);
            }

            $.until(function(){
                return focus.props.currentManualRouteSelected && checkDone == getArraySize(focus.props.currentManualRouteSelected);
            }, function(){
                if(wayPoints.length > 0) {
                    wayPoints[wayPoints.length - 1].stopover = false;
                    var request = {
                        origin: focus.props.currentRadius.center,
                        destination: wayPoints[wayPoints.length -1].location,
                        waypoints: wayPoints,
                        provideRouteAlternatives: true,
                        travelMode: google.maps.TravelMode[focus.props.currentTravelMode],
                        unitSystem: google.maps.UnitSystem.IMPERIAL,
                        optimizeWaypoints: true
                    };
					
                    focus.startRoute(request);
                } else {
                    Vtiger_Helper_Js.showPnotify({
                        title: 'Alert',
                        text: 'Can\' Route',
                        width: '35%'
                    });
                }
                focus.loading(false);
            });
        },

        refreshRoute: function() {
            if(this.props.currentRouteMode == 'AUTO') {
                this.launchAutoRoute();
            } else {
                this.launchManualRoute();
            }
        },

        clearRoute: function() {
            this.props.directionsDisplay.setMap(null);
            this.props.directionsDisplay = new google.maps.DirectionsRenderer();
            this.props.directionsDisplay.setMap(this.props.mapArea);
        },

        removePoints: function(recordId){
            var thisInstance = this;
            if(recordId) {
                // Remove one
                if(this.props.currentAddressPoints[recordId]) {
                    this.props.currentAddressPoints[recordId].setMap(null);
                    delete this.props.loadedAddressesOfRecord[recordId];
                }
            } else {
                // Remove all
                for(var i in this.props.currentAddressPoints) {
                    if(this.props.currentAddressPoints[i]) {
                        this.props.currentAddressPoints[i].setMap(null);
                    }
                }
                this.props.loadedAddressesOfRecord = [];
            }

            if(thisInstance.inRouting)
                thisInstance.refreshRoute();
        },

        loading: function(turn) {
            if(turn) { $('.loading-layer').fadeIn(); }
            else { $('.loading-layer').fadeOut() }
        },

        registerEvents: function() {
            var thisInstance = this;
            // Check then enable google map button
            this.checkEnable(function(result){

                var isEnabled = result.result.enabled;
                if(!isEnabled) return;

                $('.listViewActionsDiv .btn-toolbar')
                    .first()
                    .append('<div class="btn-group"><a href="" id="btnShowMap" title="Google Radius Maps">' +
                    '<img src="layouts/vlayout/modules/GoogleRadiusMaps/resources/images/google_maps.png" /></a></div>');
					
                $('#btnShowMap').click(function(e){
                    e.preventDefault();
                    if(thisInstance.props.open) {
                        thisInstance.hideMapWindow();
                    } else {
                        thisInstance.showMapWindow();
                    }
                });

                thisInstance.props.currentUserAddress = result.result.current_user_address;
                thisInstance.props.currentModuleFields = result.result.module_fields;
                thisInstance.props.currentCompanyAddress = result.result.company_address;

                // Add custom style
                $('head').append('<link href="layouts/vlayout/modules/GoogleRadiusMaps/resources/css/main.css" rel="stylesheet" />');
                $('head').append('<link href="layouts/vlayout/modules/GoogleRadiusMaps/resources/css/colorpicker.css" rel="stylesheet" />');
                $('head').append('<link href="layouts/vlayout/modules/GoogleRadiusMaps/resources/css/layout.css" rel="stylesheet" />');
				
				$('head').append('<script type="text/javascript" ' +
                        'src="layouts/vlayout/modules/GoogleRadiusMaps/resources/js/colorpicker.js"></script>');
						
				$('head').append('<script type="text/javascript" ' +
					'src="layouts/vlayout/modules/GoogleRadiusMaps/resources/js/eye.js"></script>');
					
				$('head').append('<script type="text/javascript" ' +
					'src="layouts/vlayout/modules/GoogleRadiusMaps/resources/js/utils.js"></script>');
					
				$('head').append('<script type="text/javascript" ' +
					'src="layouts/vlayout/modules/GoogleRadiusMaps/resources/js/layout.js?ver=1.0.2"></script>');

                window.initializeMap = function() {
                    $('head').append('<script type="text/javascript" ' +
                        'src="layouts/vlayout/modules/GoogleRadiusMaps/resources/js/markerwithlabel.js"></script>');

                    $.until(function(){
                        return typeof MarkerWithLabel != 'undefined';
                    }, function(){
                        $('#btnShowMap').removeClass('inactive');
                    });
                };

                if(typeof google != 'undefined' && typeof google.maps != 'undefined') {
                    window.initializeMap();
                } else {
                    $('head').append('<script type="text/javascript" ' +
                        'src="//maps.googleapis.com/maps/api/js?v=3&key=AIzaSyAnU5seE2NDKpWmi6N8S7D4QbpubOuPHQw&signed_in=true&callback=window.initializeMap"></script>');
                }

                // Popup window handle
                $(document).on('change', '#slbMapCenter', function(){
                    thisInstance.selectMapCenter($(this).val());
					// Reload address point
					thisInstance.drawAddressPoint();
                });

				// 72424 tuannm 12152016 start
                $(document).on('change', '#txtRadiusNumber', function(){
                    var radiusValue = $(this).val();
                    if(isNaN(radiusValue)) {
                        radiusValue = thisInstance.props.defaultRadius;
                    } else {
                        radiusValue = parseFloat(radiusValue);
                    }

                    thisInstance.drawRadius({population: radiusValue}, function(){
                        // Reload address point
                        thisInstance.drawAddressPoint();
                    });
                });
				
                
				$(document).on('change', '#unit-type', function(){
					
					var radiusValue = $('#txtRadiusNumber').val();
                    if(isNaN(radiusValue)) {
                        radiusValue = thisInstance.props.defaultRadius;
                    } else {
                        radiusValue = parseFloat(radiusValue);
                    }
					
                    var mile = '0.621371';
                    mile = mile*radiusValue;
                    var unitType = $('#unit-type').val();
                    if(unitType == 'km') {
                        radiusValue = Math.ceil(mile * 100) / 100;
                    }
                    
                    thisInstance.drawRadius({population: radiusValue}, function(){
                        // Reload address point
                        thisInstance.drawAddressPoint();
                    });
				});
				// 72424 tuannm 12152016 end 

                $(document).on('change', '#slbAddresses, #slbAddressesEvents', function(){
                    var recordId = [];

                    $('.listViewEntriesCheckBox:checked').each(function(i, o){
                        recordId.push($(o).val());
                    });

                    thisInstance.getAddressesOfRecord(recordId, function(result){
                        if(result.success) {
                            $.each(result.result.addresses, function(i, o){
                                thisInstance.props.loadedAddressesOfRecord[o.theId] = o;
                            });
                            thisInstance.drawAddressPoint();
                        }
                    });
                });

                $(document).on('change', '#slbColorPinsBy', function(){
                    var recordId = [];

                    $('.listViewEntriesCheckBox:checked').each(function(i, o){
                        recordId.push($(o).val());
                    });

                    thisInstance.getAddressesOfRecord(recordId, function(result) {
                        if(result.success) {
                            $.each(result.result.addresses, function(i, o) {
                                thisInstance.props.loadedAddressesOfRecord[o.theId] = o;
                            });
                            thisInstance.drawAddressPoint();
                        }
                    });
                });

				// 72424 tuannm 12272016 start 
                $('body').on('change', '.listViewEntriesCheckBox', function() {
                    if(thisInstance.props.mapArea == null) {
                        return;
                    }
                    var recordId = $(this).val();
                    if($(this).prop('checked')) {
                        thisInstance.getAddressesOfRecord(recordId, function(result) {
                            if (result.success) {
                                var street = result.result.addresses[0].street;
								if(typeof street === 'undefined' || isEmpty(street)) {
									var lat = parseFloat(result.result.addresses[0].lat);
									var lng = parseFloat(result.result.addresses[0].long);
									var latLng = new google.maps.LatLng(lat, lng);
									thisInstance.getAddressByLatLng(latLng, function(address) {
										thisInstance.getLatLngByAddress(address, function(result) {
											if(result) {
												var latLng = new google.maps.LatLng(result.lat, result.lng);
												if(thisInstance.props.currentRadius.getBounds().contains(latLng)) {
													var href = $('a#targetToGoogleMap').attr("href");
													if(href.search(thisInstance.props.currentPosName) == -1) {
														href = href.replace('https://www.google.com/maps/dir/', 'https://www.google.com/maps/dir/' + thisInstance.props.currentPosName + '/');
													}
													
													if(href.search('data') != -1) {
														href = href.replace('data', address + '/data');
													} else {
														href = href + address + '/';
													}
													$('a#targetToGoogleMap').attr("href", href);
												}
											}
										});
									});
									
								} else {
									street = street.split(' ');
									var st = [];
									
									for(var t in street) {
										st[t] = street[t].trim(); 
									}
									
									street = st.join('+');
									var city = result.result.addresses[0].city;
									var state = result.result.addresses[0].state;
									var postal_code = result.result.addresses[0].postal_code;
									var address = street + ',+' + city + ',+' + state + '+' + postal_code; 
									
									var href = $('a#targetToGoogleMap').attr("href");
									if(href.search(thisInstance.props.currentPosName) == -1) {
										href = href.replace('https://www.google.com/maps/dir/', 'https://www.google.com/maps/dir/' + thisInstance.props.currentPosName + '/');
									}
									
									if(href.search('data') != -1) {
										href = href.replace('data', address + '/data');
									} else {
										href = href + address + '/';
									}
									$('a#targetToGoogleMap').attr("href", href);
								}
								
                                thisInstance.props.loadedAddressesOfRecord[recordId] = result.result.addresses[0];
                                /* if(!isEmpty(thisInstance.props.loadedAddressesOfRecord[recordId].street)) { */
                                    thisInstance.drawAddressPoint(recordId);
                                    if(thisInstance.props.inRouting) {
                                        thisInstance.refreshRoute();
                                    }
                                /* } */
                            }
                        });
                    } else {
                        thisInstance.removePoints(recordId);
						thisInstance.getAddressesOfRecord(recordId, function(result) {
                            if(result.success) {
								
								// thisInstance.props.currentPosName, // origin
								var street = result.result.addresses[0].street;
								if(typeof street === 'undefined' || isEmpty(street)) {
									var lat = parseFloat(result.result.addresses[0].lat);
									var lng = parseFloat(result.result.addresses[0].long);
									var latLng = new google.maps.LatLng(lat, lng);
									
									thisInstance.getAddressByLatLng(latLng, function(address) {
										thisInstance.getLatLngByAddress(address, function(result) {
											if(result) {
												var latLng = new google.maps.LatLng(result.lat, result.lng);
												if(thisInstance.props.currentRadius.getBounds().contains(latLng)) {
													var href = $('a#targetToGoogleMap').attr("href");
													href = href.replace(address + '/','');
													$('a#targetToGoogleMap').attr("href", href);
												}
											}
										});
									});
									
								} else {
									street = street.split(' ');
									var st = [];
									for(var t in street) {
										st[t] = street[t].trim(); 
									}
									street = st.join('+');
									var city = result.result.addresses[0].city;
									var state = result.result.addresses[0].state;
									var postal_code = result.result.addresses[0].postal_code;
									var address = street + ',+' + city + ',+' + state + '+' + postal_code; 

									var href = $('a#targetToGoogleMap').attr("href");
									href = href.replace(address + '/', '');
									$('a#targetToGoogleMap').attr("href", href);
								}
                            }
                        });
                    }
                });
				// 72424 tuannm 12272016 end

                $('body').on('click', '#listViewEntriesMainCheckBox', function(){
                    if(thisInstance.props.mapArea == null) {
                        return;
                    }

                    if($(this).prop('checked')) {
                        setTimeout(function(){
                            //var recordIds = [];
                            $('.listViewEntriesCheckBox:checked').each(function(i, o){
                                $(o).trigger('change');
                            });
                        }, 500);
                    } else {
                        thisInstance.removePoints();
                    }
                });
				
                $('body').on('click', '#deSelectAllMsg', function(){
                    if(thisInstance.props.mapArea == null) {
                        return;
                    }

                    if($(this).prop('checked')) {
                        setTimeout(function(){
                            //var recordIds = [];
                            $('.listViewEntriesCheckBox:checked').each(function(i, o){
                                $(o).trigger('change');
                            });
                        }, 500);
                    } else {
                        thisInstance.removePoints();
                    }
                });
				
                $('body').on('click', '#selectAllMsg', function(){
                    if(thisInstance.props.mapArea != null) {
                        return;
                    }

                    if($(this).prop('checked')) {
                        setTimeout(function(){
                            //var recordIds = [];
                            $('.listViewEntriesCheckBox:checked').each(function(i, o){
                                $(o).trigger('change');
                            });
                        }, 500);
                    } else {
                        thisInstance.removePoints();
                    }
                });
				
                $(document).on('change', '#txtCode', function() {
                    var radiusValue = $('#txtRadiusNumber').val();
                    if(isNaN(radiusValue)) {
                        radiusValue = focusInstance.props.defaultRadius;
                    } else {
                        radiusValue = parseFloat(radiusValue);
                    }

                    thisInstance.getAddressByZipCode($(this).val(), function(addressInfo){
                        thisInstance.props.currentPosName = addressInfo.name;
                        thisInstance.props.currentRadiusName = addressInfo.name;
                        thisInstance.drawMapCenter({
                            position: {
                                latitude: addressInfo.lat,
                                longitude: addressInfo.lng
                            },
                            population: radiusValue
                        });
                    });
                });

                $(document).on('click', '.btnRoute', function(e) {

                    e.preventDefault();

                    var type = $(this).data('type');
                    thisInstance.props.currentRouteOutput = type;

                    if(type == 'print') {
                        function printStaticMap() {
                            var prop = { height: 600, width: 800};
                            var windowProp = {height: $(window).height(), width: $(window).width()};
                            var options = {
                                usePolylineEncode: true,
                                adjustCenter: true,
                                //adjustZoom: true,
                                controlPositon: 3,
                                format: "png",
                                hidden: false,
                                language: "",
                                mapType: "",
                                positon: null,
                                size: new google.maps.Size(prop.width, prop.height)
                            };
							
                            thisInstance.props.snapControl.setOptions(options);
                            var url = thisInstance.props.snapControl.getImage(null).trim();

                            $.jStorage.set('mapUrl', url);
                            $.jStorage.set('routingStepData', thisInstance.props.currRoutingStep);
                            $.jStorage.set('currRoutingHeader', thisInstance.props.currRoutingHeader);

                            var popupUrl = 'index.php?module=GoogleRadiusMaps&src_module=GoogleRadiusMaps&view=PopupPrint';
                            var printWindow = window.open(popupUrl, 'Routing Print','height='+prop.height+',width='+prop.width+',scrollbars=yes');
                            printWindow.moveTo((windowProp.width / 2) - (prop.width/2), (windowProp.height / 2) - (prop.height/2));
                        }

                        if(!thisInstance.props.inRouting) {
                            thisInstance.props.currentRouteMode = 'AUTO';
                            thisInstance.props.currentRouteOutput = 'line';
                            thisInstance.props.currentTravelMode = 'DRIVING';
                            thisInstance.launchAutoRoute();

                            $.until(function(){
                                return thisInstance.props.inRouting;
                            }, function(){
                                printStaticMap();
                            })
                        } else {
                            printStaticMap();
                        }

                    } else {
                        function showRoutingModal(element) {
                            app.showModalWindow(element, function() {
                                //Prepare records list
                                var recordsHtml = "";
                                for(var i in thisInstance.props.loadedAddressesOfRecord) {
                                    recordsHtml += $.tmpl('<li rid="#{theId}">#{theName}</li>', thisInstance.props.loadedAddressesOfRecord[i]);
                                }

                                $('.manual-mode .list-records').html(recordsHtml);
                                $( ".list-records, .selected-records" ).sortable({
                                    connectWith: ".connectedSortable"
                                }).disableSelection();
                            }, {'width':'600px'})
                        }

                        if(typeof window.routingModalHtml == 'undefined') {
                            $.get('layouts/vlayout/modules/GoogleRadiusMaps/resources/views/routing-modal.html', function(html){
                                //^Load language
                                html = $.tmpl(html, {
                                    LBL_AUTO_OR_MANUAL_ROUTE: app.vtranslate('LBL_AUTO_OR_MANUAL_ROUTE'),
                                    LBL_AUTO_ROUTE: app.vtranslate('LBL_AUTO_ROUTE'),
                                    LBL_MANUAL_ROUTE: app.vtranslate('LBL_MANUAL_ROUTE'),
                                    LBL_DRIVING: app.vtranslate('LBL_DRIVING'),
                                    LBL_BICYCLING: app.vtranslate('LBL_BICYCLING'),
                                    LBL_TRANSIT: app.vtranslate('LBL_TRANSIT'),
                                    LBL_WALKING: app.vtranslate('LBL_WALKING'),
                                    LBL_PRINT_ROUTING: app.vtranslate('LBL_PRINT_ROUTING'),
                                    LBL_CANCEL: app.vtranslate('LBL_CANCEL')
                                });
                                //$Load language

                                var newElement = $(html);
                                showRoutingModal(newElement);
                                window.routingModalHtml = html;
                            });
                        } else {
                            var newElement = $(window.routingModalHtml);
                            showRoutingModal(newElement);
                        }
                    }


                });

                $(document).on('click', '#btnRouteAuto', function() {
                    $('.travel-mode').removeClass('hide');
                    $(this).parent().children('button').hide();
                    thisInstance.props.currentRouteMode = 'AUTO';
                    thisInstance.setRoutingModalTitle('Select travel mode');
                });

                $(document).on('click', '#btnRouteManual', function(){
                    $('.travel-mode').removeClass('hide');
                    $(this).parent().children('button').hide();
                    thisInstance.props.currentRouteMode = 'MANUAL';
                    thisInstance.setRoutingModalTitle('Select travel mode');
                });

                $(document).on('click', 'input[name="travel-mode"]', function() {
                    var sltMode = $(this).val();
                    thisInstance.props.currentTravelMode = sltMode;
                    if(thisInstance.props.currentRouteMode == 'AUTO') {
                        if(thisInstance.props.currentRouteOutput == 'line') {
                            app.hideModalWindow(function(){
                                thisInstance.launchAutoRoute();
                            });
                        } else {
                            thisInstance.launchAutoRoute();
                        }
                    } else {
                        $('.travel-mode').addClass('hide');
                        $('.manual-mode').removeClass('hide');
                        thisInstance.setRoutingModalTitle('Select records');
                    }
                });

                $(document).on('click', '#btnPrintRoute', function(){
                    if($('.selected-records li').length == 0) {
                        Vtiger_Helper_Js.showPnotify({
                            title: 'Alert',
                            text: 'Select at least 1 record',
                            width: '35%'
                        });
                    } else {
                        var selectedRecords = [];
                        $('.selected-records li').each(function(i, o){
                            var rid = $(this).attr('rid');
                            for(var i in thisInstance.props.loadedAddressesOfRecord) {
                                var item = thisInstance.props.loadedAddressesOfRecord[i];
                                if(item.theId == rid) {
                                    selectedRecords.push(item);
                                }
                            }
                        });

                        thisInstance.props.currentManualRouteSelected = selectedRecords;

                        if(thisInstance.props.currentRouteOutput == 'line') {
                            app.hideModalWindow(function(){
                                thisInstance.launchManualRoute();
                            });
                        } else {
                            thisInstance.launchManualRoute();
                        }
                    }
                });

                window.adjustLayout = function() {
                    var list = $('.listViewPageDiv');
                    var map = $('#wdMapWindow');
                    var rightPanel = $('#rightPanel');
                    var toolbar1 = $('.listViewActionsDiv .btn-toolbar').get(0);
                    var toolbar2 = $('.listViewActionsDiv .btn-toolbar').get(1);
                    var toolbar3 = $('.listViewActionsDiv .btn-toolbar').get(2);

                    var halfOfWith = (rightPanel.width() / 2) - 60;

                    if(map.hasClass('on')) {
                        list.width(halfOfWith);
                        map.width(halfOfWith);
                        $(toolbar1).removeClass('span4').addClass('span12');
                        $(toolbar2).removeClass('span4').addClass('span6').css('margin-left', 0);
                        $(toolbar3).removeClass('span4').addClass('span5').css('float', 'right');
                    } else {
                        list.removeAttr('style');
                        $(toolbar1).removeClass('span12').addClass('span4');
                        $(toolbar2).removeClass('span6').addClass('span4').removeAttr('style');
                        $(toolbar3).removeClass('span5').addClass('span4').removeAttr('style');
                    }
                };

                $(window).resize(window.adjustLayout);
            });

            this.getAddresses(function(result){
                if(result.success) {
                    thisInstance.props.loadedAddresses = result.result.addresses;
                }
            });
        },

        showMapWindow: function() {
            var focusInstance = this;

            var loadMap = function() {
                // Create the map.
                var mapOptions = {
                    zoom: 4,
                    center: new google.maps.LatLng(37.09024, -95.712891),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                var map = new google.maps.Map(document.getElementById('map-content'), mapOptions);
                focusInstance.props.mapArea = map;

                focusInstance.props.snapControl = new SnapShotControl({
                    hidden: true
                });

                focusInstance.props.snapControl.setMap(focusInstance.props.mapArea);

                focusInstance.props.directionsDisplay = new google.maps.DirectionsRenderer();
                focusInstance.props.directionsService = new google.maps.DirectionsService();
                focusInstance.props.directionsDisplay.setMap(map);

                focusInstance.getAddressesOfRecord(null, function(result){
                    if(result.success) {
                        $.each(result.result.addresses, function(i, o){
                            focusInstance.props.loadedAddressesOfRecord[o.theId] = o;
                        });
                    }

                    $.until(function(){return focusInstance.props.currentRadius != null}, function(){
                        focusInstance.drawAddressPoint();
                    });
                });
            };

            if(!this.props.viewLoaded) {
                // Load view
                var url = "index.php?relModule=Accounts&s=loadHtml&module=GoogleRadiusMaps&action=ActionAjax";
                $.get(url, function(data) {
                    html = data.result.html;
                    //^Load language
                    html = $.tmpl(html, {
                        LBL_MAP_CENTER: app.vtranslate('LBL_MAP_CENTER'),
                        LBL_COMPANY_ADDRESS: app.vtranslate('LBL_COMPANY_ADDRESS'),
                        LBL_USER_ADDRESS: app.vtranslate('LBL_USER_ADDRESS'),
                        LBL_CURRENT_LOCATION: app.vtranslate('LBL_CURRENT_LOCATION'),
                        LBL_ZIP_CODE: app.vtranslate('LBL_ZIP_CODE'),
                        LBL_RADIUS: app.vtranslate('LBL_RADIUS'),
                        LBL_ADDRESS: app.vtranslate('LBL_ADDRESS'),
                        LBL_COLOR_PIN_BY: app.vtranslate('LBL_COLOR_PIN_BY'),
                        LBL_ROUTE: app.vtranslate('LBL_ROUTE'),
                        LBL_LINE: app.vtranslate('LBL_LINE'),
                        LBL_TEXT: app.vtranslate('LBL_TEXT'),
                        LBL_PRINT: app.vtranslate('LBL_PRINT'),
                        LBL_STATUS_LOADING: app.vtranslate('LBL_STATUS_LOADING')
                    });
                    //$Load language
                    $('#rightPanel').append(html);
                    var addressesOptions = "";
                    var addressesOptionsEvents = "";
                    if(focusInstance.props.loadedAddresses.Calendar != undefined) {
                        for(var i in focusInstance.props.loadedAddresses.Calendar) {
                            addressesOptions += $.tmpl('<option value="#{id}">#{address_name}</option>',
                                focusInstance.props.loadedAddresses.Calendar[i]);
                        }

                        $('#slbAddresses').append(addressesOptions).addClass('small');

                        $('<select class="select2 small" id="slbAddressesEvents"></select>').insertAfter('#slbAddresses');

                        for(var i in focusInstance.props.loadedAddresses.Events) {
                            addressesOptionsEvents += $.tmpl('<option value="#{id}">#{address_name}</option>',
                                focusInstance.props.loadedAddresses.Events[i]);
                        }
                        $('#slbAddressesEvents').append(addressesOptionsEvents);
                    } else {
                        for(var i in focusInstance.props.loadedAddresses) {
                            addressesOptions += $.tmpl('<option value="#{id}">#{address_name}</option>',
                                focusInstance.props.loadedAddresses[i]);
                        }
                        $('#slbAddresses').append(addressesOptions);
                    }

                    var colorPinsByOptions = "";
                    for(var i in focusInstance.props.currentModuleFields) {
                        colorPinsByOptions += $.tmpl('<option value="#{name}">#{label}</option>',
                            focusInstance.props.currentModuleFields[i]);
                    }

                    $('#slbColorPinsBy').append(colorPinsByOptions);

                    app.showSelect2ElementView($('#wdMapWindow').find('select.select2'));
                    $('#wdMapWindow').addClass('on');

                    $.until(function(){
                        return focusInstance.props.mapArea;
                    }, function(){
                        $('.listViewEntriesCheckBox').trigger('change');
                    });

                    window.adjustLayout();
                    loadMap();
                });
                this.props.viewLoaded = true;
                $.until(function(){
                    return focusInstance.props.mapArea != null && google.maps != undefined && google.maps.Geocoder != undefined;
                }, function(){
                    // $('#slbMapCenter').select2('val', 'User Address');
                    $('#slbMapCenter').select2('val', 'Company Address');
                    $('#slbMapCenter').trigger('change');
                });
            } else {
                $('#wdMapWindow').show().addClass('on');
                window.adjustLayout();
            }
            this.props.open = true;
        },

        hideMapWindow: function() {
            this.props.open = false;
            $('#wdMapWindow').hide();
            $('#wdMapWindow').removeClass('on');
            window.adjustLayout();
        },

        getAddressesOfRecord: function(recordId, callback) {
            var focus = this, selectedRecords = [];

            if($.isArray(recordId)) {
                selectedRecords = recordId;
            } else if(recordId) {
                selectedRecords = [recordId];
            }

            if(selectedRecords.length == 0) {
                callback({success: false});
                return;
            }

            // Get current selected address
            var selectedAddress = $('#slbAddresses').val();
            var currentModule = $('#module').val();
            var currentColorPin = $('#slbColorPinsBy').val();

            //Get current address info
            var actionParams = {
                "type": "GET",
                "url": 'index.php?module=GoogleRadiusMaps&action=ActionAjax',
                "dataType": "json",
                "data": {
                    'relModule': currentModule,
                    's': 'getCurrentAddressInfo',
                    'addressId': selectedAddress,
                    'records': selectedRecords,
                    'colorPin': currentColorPin
                }
            };

            if(currentModule == 'Events' || currentModule == 'Calendar') {
                actionParams['data']['addressId'] = {
                   Calendar: $('#slbAddresses').val(), Events: $('#slbAddressesEvents').val()
                };
            }

            AppConnector.request(actionParams)
                .then(function (result) {
                    callback(result);
                }
            );
        },

        getAddresses: function(callback) {
            var currentModule = $('#module').val();

            if (!currentModule) {
                return false;
            }

            // Get config fields
            var actionParams = {
                "type": "GET",
                "url": 'index.php?module=GoogleRadiusMaps&action=ActionAjax',
                "dataType": "json",
                "data": {
                    'relModule': currentModule,
                    's': 'getAddresses'
                }
            };

            AppConnector.request(actionParams)
                .then(function (result) {
                    callback(result);
                }
            );
        },

        checkEnable: function (callback) {
            var currentModule = $('#module').val();

            if (!currentModule) {
                return false;
            }

            // Get config fields
            var actionParams = {
                "type": "GET",
                "url": 'index.php?module=GoogleRadiusMaps&action=ActionAjax',
                "dataType": "json",
                "data": {
                    'relModule': currentModule,
                    's': 'loadProps'
                }
            };

            AppConnector.request(actionParams)
                .then(function (result) {
                    callback(result);
                }
            );
        }
    });

    $(function () {
        var currViewMode = $('#view').val();
        if(currViewMode != 'List') return;
        var googlemap = GoogleRadiusMaps_Js.getInstance();
        googlemap.registerEvents();
    });
})(jQuery);


// By Pham #72424 start
var savePoints = [];
// By Pham #72424 end

function test(){
    var url = "index.php?relModule=Accounts&s=loadHtml&module=GoogleRadiusMaps&action=ActionAjax";
    $.get( url, function( data ) {
    });
}