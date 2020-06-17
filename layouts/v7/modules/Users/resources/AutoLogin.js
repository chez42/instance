jQuery(document).ready(function($) {
    function getSearchParams(k){
        var p={};
        location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
        return k?p[k]:p;
    }

    function getHashValue(key) {
        var matches = location.hash.match(new RegExp(key+'=([^&]*)'));
        return matches ? matches[1] : null;
    }

    var uname = $("#auto_login").data("uname");
    var pword = $("#auto_login").data("pword");
    var hash = window.location.hash;
    if(hash.toString().length > 1)
        pword = pword.concat(hash);

    $(".auto_u").val(uname);
    $(".auto_p").val(pword);

    if(uname.toString().length > 0 && pword.toString().length > 0)
        $(".auto_submit").click();
});