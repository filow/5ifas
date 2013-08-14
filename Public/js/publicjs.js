$(document).ready(function(){
   host = window.location.host;
   ADMINROOT="http://"+host+"/admin.php/";
    
    $("#return").click(function(){
        window.history.back(-1);
        return false;
    })
    $("#change_shop").change(function(){
        var shop_id=$(this).val();
        var ban_id=$("#change_ban_order").val();
        var href=ADMINROOT+"Sorder/index?shopid="+shop_id+"&ban_id="+ban_id;
        window.location.href=href; 
    })
    $("#change_shop_tuikuan").change(function(){
        var shop_id=$(this).val();
           var ban_id=$("#change_ban_order_tuikuan").val();
        var href=ADMINROOT+"Sorder/seetuikuan?shopid="+shop_id+"&ban_id="+ban_id;
        window.location.href=href; 
    })
       $("#change_ban").change(function(){
        var ban_id=$(this).val();
        var href=ADMINROOT+"Area/index?banid="+ban_id;
        window.location.href=href; 
    })  
     $("#change_ban_add").change(function(){
        var ban_id=$(this).val();
        var href=ADMINROOT+"Area/add?banid="+ban_id;
        window.location.href=href; 
    })  
    $("#shop_ban_add").change(function(){
        var ban_id=$(this).val();
        var href=ADMINROOT+"Shop/add?banid="+ban_id;
        window.location.href=href; 
    }) 
     $("#shop_ban_mod").change(function(){
        var ban_id=$(this).val();
        var shop_id=$("#shopid").attr("value");
        var href=ADMINROOT+"Shop/mod?id="+shop_id+"&banid="+ban_id;
        window.location.href=href; 
    }) 
     $("#shop_ban_index").change(function(){
        var ban_id=$(this).val();
        var href=ADMINROOT+"Shop/index?banid="+ban_id;
        window.location.href=href; 
    }) 
	 $("#shop_area_index").change(function(){
        var ban_id=$("#shop_ban_index").val();
		var area_id=$("#shop_area_index").val();
		
        var href=ADMINROOT+"Shop/index?banid="+ban_id+"&area_id="+area_id;
        window.location.href=href; 
    }) 
     $("#zt_ban_add").change(function(){
        var ban_id=$(this).val();
        var href=ADMINROOT+"Zt/add?banid="+ban_id;
        window.location.href=href; 
    })  
    $("#ware_add").change(function(){
        var shop_id=$(this).val();
        var href=ADMINROOT+"Ware/add?shop_id="+shop_id;
        window.location.href=href; 
    }) 
      $("#change_ban_order").change(function(){
        var ban_id=$(this).val();
        var href=ADMINROOT+"Sorder/index?ban_id="+ban_id;
        window.location.href=href; 
    }) 
     $("#change_ban_order_tuikuan").change(function(){
        var ban_id=$(this).val();
        var href=ADMINROOT+"Sorder/seetuikuan?ban_id="+ban_id;
        window.location.href=href; 
    }) 
})
function check_qq(qq_num){
	url=ADMINROOT+"User/check_qq?qq="+qq_num;

	$.get(url,function(data){
		if(data=="***"){
			$("#qq").css("color","red");
		}else if(data=="###"){
			$("#qq").css("color","white");
		}
	})
	 
}
function check_tele(tele_num){
	url=ADMINROOT+"User/check_tele?tele="+tele_num;
    
	$.get(url,function(data){
		if(data=="***"){
			$("#tele").css("color","red");
		}else if(data=="###"){
			$("#tele").css("color","white");
		}
	})
	 
}