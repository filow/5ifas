cookieOptions={
    expiresAt: new Date( 2014, 1, 1 )
}
host = window.location.host;
ROOT="http://"+host+"/index.php/";


$(document).ready(function(){
	url_now=$("#none").attr("rel");
    /*******************************商家选择过滤*************************/
    $(".shop_fileter").click(function(){
        args=getArgs();
        var ban_id=args["banid"];
        var aid=args["aid"];
        var url = url_now+"?banid="+ban_id+"&aid="+aid+"&";
        $(".shop_fileter").each(function(){
            if( $(this).attr("checked")){
                name=$(this).attr("rel");
                url+=name+"=1&";
            }
             
        })
        window.location.href=url; 
    })
    $(".shop_fileter_detial").click(function(){
        id=$(".shop_id").attr("rel")
        var url =url_now+"?id="+id+"&";
        $(".shop_fileter_detial").each(function(){
            if( $(this).attr("checked")){
                name=$(this).attr("rel");
                if(name>0)
                       
                    url+="catid="+name+"&";
            }
             
        })
     
        window.location.href=url; 
    })


       
});

function getArgs() {
    var args = {};
    var query = location.search.substring(1);
    // Get query string
    var pairs = query.split("&");
    // Break at ampersand
    for(var i = 0; i < pairs.length; i++) {
        var pos = pairs[i].indexOf('=');
        // Look for "name=value"
        if (pos == -1) continue;
        // If not found, skip
        var argname = pairs[i].substring(0,pos);// Extract the name
        var value = pairs[i].substring(pos+1);// Extract the value
        value = decodeURIComponent(value);// Decode it, if needed
        args[argname] = value;
    // Store as a property
    }
    return args;// Return the object
}