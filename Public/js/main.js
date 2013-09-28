
cookieOptions={
    expiresAt: new Date( 2014, 1, 1 )
}
host = window.location.host;
ROOT="http://"+host+"/index.php/";
 
$(document).ready(function(){
     /*    
	 	editor:Filow Lee
		Data:2012-11-15
	  */
    /*****加载时读入信息***************************/
    /*
  var a_id=$("#select1").attr("aid");
  if(a_id){
      area_select(a_id);
  }
    */
    var ban_id=$("#select1").attr("rel");
    aid=$.cookies.get("area_id"+ban_id);
    // alert(aid);
    if(aid){
        area_select(aid);
    // alert(aid)
    }
	
	if($('.thisli a').text()!=''){
		$('span.current').text($('.thisli a').text());
		document.title=$('.thisli a').text()+'——艾星网络';
	}
	
    /******************商品购买**************************/
	
	/*****商品数量增减******/
	$('.plus').click(function() {
		$num=parseInt($(this).parent().children('input').attr('value'));
		if($num>=0&&$num<=99)
		$num++;
		else
		$num=2;
		$(this).parent().children('input').attr('value',$num);
	});
	
	$('.cut').click(function() {
		$num=parseInt($(this).parent().children('input').attr('value'));
		if($num>1)
		$num--;
		else
		$num=1;
		$(this).parent().children('input').attr('value',$num);
	});
	
     /*****收藏***********************/
    $(".collect").click(function (){
        $id=$(this).attr("rel");
        url=ROOT+"Ajax/collect?productid="+$id;
        $.get(url,function(data){
            if(data=="###"){
                alert("请先登录")
            }else if(data=="***"){
                alert("您已经收藏过了")
            }else if(data=="111"){
                alert("收藏成功");
            }else{
                alert("未知错误");
            }
        })
    })
    $(".collect_shop").click(function (){
        $id=$(this).attr("rel");
        url=ROOT+"Ajax/collect_shop?shop_id="+$id;
        
        $.get(url,function(data){
            if(data=="###"){
                alert("请先登录")
            }else if(data=="***"){
                alert("您已经收藏过了")
            }else if(data=="111"){
                alert("收藏成功");
            }else{
                alert("未知错误");
            }
        })
    })
	//修改于2012年11月15日19:06:56
    $(".prod_buy").click(function(){
        var id=$(this).attr('pid');
        var num=parseInt($(this).parent().children('input').attr('value'));
		
        url=ROOT+"Ajax/checkbuy?id="+id+"&num="+num ;
        $.get(url,function(data){
            if(data=="###"){
                alert("该商品不支持货到付款，请登陆购买");
            }else if(data=="***"){
                alert("该商品不支持发往您所在地");
            }else if(data=="##**"){
                alert("请联系客服购买，不支持在线购买");           
            }else{
                
              var data1=data.split("#");

              $(".ware_sum").html(data1[0]);
             
              alert("加入购物车成功");
            }
        })
    })

    $(".buy_icon").click(function(){
        var id=$(this).attr('pid');


        url=ROOT+"Ajax/checkbuy?id="+id+"&num=1" ;
        //alert(url);
        $.get(url,function(data){
            if(data=="###"){
                alert("该商品不支持货到付款，请登陆购买");
            }else if(data=="***"){
                alert("该商品不支持发往您所在地");
            }else if(data=="##**"){
                alert("请联系客服购买，不支持在线购买");
            }else{
                alert("加入购物车成功");
                $(".ware_sum").html(data);
            }
        })


    })
    //删除商品
    $(".icon-remove").click(function(){
        id=$(this).attr("rel");
        url=ROOT+"Ajax/remove?id="+id ;
        
        $.get(url,function(data){
            if(data=="###")
                location.reload();
        })
    })
    //更新订单
    $(".fresh_num").click(function (){
        
        $(".end_num").each(function(){
            var url = ROOT+"Ajax/end?";
            id=$(this).attr("rel")
            num=$(this).attr("value");
            url+="id="+id+"&num="+num;
            
            $.get(url,function(data){
                 
                   
                })
        })
        $("#form").attr("disabled",false);
        setTimeout("location.reload(); ",1500)
          
    })
    //判断是否送货
    $("#sushel").change(function(){
        $("#form").attr("disabled",false);
        sid=$("#sushel").val();
				
        $('[class^=color]').each(function(){
            id=$(this).attr("class").substr(5);
            url=ROOT+"Ajax/check?id="+id+"&sid="+sid;
             
            $.get(url,function(data){
                var array=data.split("#");
                var data1=array[0];
                var data2=array[1];
                if(data1>0){
                    $(data2).text("目前不能配送到您指定的地点");
                    $(data2).css("color","red");
                    $("#form").attr("disabled",true);
                }else{
                    $(data2).text(" ");
                    $(data2).css("background-color","white");
			    
                } 
            })
             
        })
				
    })
	
		    $(".icon-plus").click(function(){
        id=$(this).attr("rel");
        var num=parseInt($('.num_'+id).attr('value'));
       
         url=ROOT+"Ajax/add?id="+id+"&num=1" ;
        //alert(url);
        $.get(url,function(data){
            var data=data.split("#");
             $('.num_'+id).attr('value',num+1);
              $(".ware_sum").html(data[0]);
              $(".money_sum").html(data[1]);
        })
    })
    $(".icon-minus").click(function(){
        id=$(this).attr("rel");
        var num=parseInt($('.num_'+id).attr('value'));
        if(num<2){
            num=2;

        }
      
           url=ROOT+"Ajax/minus?id="+id+"&num=1" ;
        //alert(url);
        $.get(url,function(data){
            var data=data.split("#");
              $('.num_'+id).attr('value',num-1);
              $(".ware_sum").html(data[0]);
              $(".money_sum").html(data[1]);
        })
    })
	

    /*****分享**********************************/
    url_now=$("#none").attr("rel");
    share_url=$(".share_url").attr("rel");
    $("#bdshare").click(function (){
        url=ROOT+"Ajax/share?share_url="+share_url;
        
        $.get(url,function(data){
         
            })
         
    })
  
  /*  $('#myModal').modal({
        backdrop:true,
        keyboard:true,
        show:true
    });*/
	
	
	    /***********************评分************************/
    $('.star_rating_shop li').each(function(){
      
        $(this).click(function(obj){
            $('.star_rating li').unbind();//移除事件绑定
            var Too = (obj.srcElement.offsetLeft+16);
            setStarPos(Too-80);
            var rate=Too/16;
            var shop_id=$(this).parent().attr("id");
            var url=ROOT+"Ajax/rating_shop?rate="+rate+"&shop_id="+shop_id ;
            $.get(url,function(data){
                if(data =="###"){
                    alert("评分成功");
                }else if(data=="111"){
                    alert("请登录！");
                }else{
                    alert("您已经评价过了！");
                }
            })
             
        })
    })
    $('.star_rating_ware li').each(function(){
      
        $(this).click(function(obj){
            $('.star_rating li').unbind();//移除事件绑定
            var Too = (obj.srcElement.offsetLeft+16);
            setStarPos(Too-80);
            var rate=Too/16;
            var ware_id=$(this).parent().attr("id");
            var url=ROOT+"Ajax/rating_ware?rate="+rate+"&ware_id="+ware_id ;
            // alert(url)
            $.get(url,function(data){
                if(data =="###"){
                    alert("评分成功");
                }else if(data=="111"){
                    alert("请登录！");
                }else{
                    alert("您已经评价过了！");
                }
            })
             
        })
    })
    function setStarPos(id,le){
        $('#'+id).css({
            'backgroundPosition':le+'px 0'
        });
    };
    $('.star_rating_shop').each(function(){
        var rate=$(this).attr("rel")
        var id=$(this).attr("id");
           
        rate=16*(parseInt(rate));
        //alert(rate);
        setStarPos(id,rate-80);
    })
    $('.star_rating_ware').each(function(){
        var rate=$(this).attr("rel")
        var id=$(this).attr("id");
           
        rate=16*(parseInt(rate));
        //alert(rate);
        setStarPos(id,rate-80);
    })
	

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
/*
* 或许下级分类
*/
function area_select(area_id,r){
    var ban_id=$("#select1").attr("rel");
    if(area_id==0)
        exit;
    url=ROOT+"Ajax/select_area?area_id="+area_id;
    
    $.get(url,function(data){
        if(data !="###"){
            $("#select_start").remove();
            $("#area_append").html(data)
        }else{
            
    }
    })
    $.cookies.setOptions(cookieOptions);
    $.cookies.set("area_id"+ban_id,area_id);
    if(r){
        n_url=url_now+"?banid="+ban_id+"&aid="+area_id;
        window.location.href=n_url; 
    }
         
    
}
function clear_ts(){

   $("#ts").css("color","white");

}
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
