<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Keywords" content="艾星网络,艾星.南京,江宁,点餐订座,商家折扣,代理市场,营养早餐,鑫鑫相印" />
<meta name="description" content="艾星网是一家从事网络运营的公司，主要从事学生网络消费服务。艾星网络致力于打造-安全、快捷、省钱、省心-的网络消费服务体系，发展校内校际服务平台，完成-足不出户，轻松购物；让高校服务不足一公里-的组织使命。努力完善服务平台，引领-校园购物新理念-。" />
<meta name="baidu-site-verification" content="ULqVJsBzAANwY2Gu" />
<title>艾星网——安全快捷的校内校际服务平台</title>
<!--CSS定义及ie6支持-->
<link href="__ROOT__/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<script src="__ROOT__/Public/bootstrap/js/jquery.min.js"></script>
<script src="__ROOT__/Public/js/publicjs.js"></script>
<script src="__ROOT__/Public/bootstrap/js/bootstrap.min.js"></script>
<!--[if IE 6]>    
    <script src="__ROOT__/Public/bootstrap/js/ie6.min.js"></script>
    <link rel="stylesheet" type="text/css" href="__ROOT__/Public/css/iecss.css" />
<![endif]-->
<!--[if lt IE 9]>
    <script src="__ROOT__/Public/bootstrap/js/html5.js"></script>
<![endif]-->
<!--[if IE 6]>    
    <link href="__ROOT__/Public/bootstrap/css/ie6.min.css" rel="stylesheet">
<![endif]-->
<link rel="stylesheet" type="text/css" href="__ROOT__/Public/css/style.css" />
<link type="image/x-icon" href="__ROOT__/Public/images/fav.ico" rel="shortcut icon" />
<script  src="__ROOT__/Public/js/jquery.cookies.2.2.0.min.js"> </script>
<script  src="__ROOT__/Public/js/main.js"> </script>
</head>
<body>
<div id="main_container">
  <div class="navbar navbar-inverse" style="position: static;">
      <div class="navbar-inner">
        <div class="container">
          <div class="nav-collapse collapse navbar-inverse-collapse">
            <ul class="nav">
            <?php if(isset($_SESSION['cardn'])): ?><li><a href="__APP__/user/index">用户中心</a></li>
                    <li><a href="__APP__/Public/logout">退出登录</a></li>
                    <?php else: ?><li><a href="__APP__/Public/login">登录</a></li><?php endif; ?>
              <li><a href="__APP__/car/mycar">购物车(<?php echo (($_SESSION['car']['sum'])?($_SESSION['car']['sum']):"0"); ?>)</a></li>
              <li><a>客服电话：189-1448-6244</a></li>
              <li><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=800097413&site=qq&menu=yes">企业QQ：800097413</a></li>
            </ul>
            <ul class="nav pull-right">
            <form class="navbar-search pull-left" action="__APP__/Index/search" method="get">
              <input type="text" class="search-query span3" name="item" placeholder="输入您想搜索的内容,回车搜索">
            </form>
            </ul>
          </div><!-- /.nav-collapse -->
        </div>
      </div><!-- /navbar-inner -->
  </div>
</div>
    <!--顶栏定义结束-->
    <div id="header">
        <div id="logo"> <a href="__APP__"><img src="__ROOT__/Public/images/logo.png" alt="" title="" width="276" height="140" /></a> </div>
        <div class="oferte_content">
            <div class="top_divider"><img src="__ROOT__/Public/images/header_divider.png" alt="" title="" width="1" height="164" /></div>
            <!--分隔线-->
            <div class="oferta">
                <div class="oferta_content"> <!--<img src="__ROOT__/Public/images/thumb_1349661974.jpg" width="100" height="100" alt="" class="oferta_img" />-->
                    <div class="oferta_details">
                        <div class="oferta_title">艾星网新版</div>
                        <div class="oferta_text"><a href="http://1.5ifas.sinaapp.com/">回到旧版</a>
                        </div>
                </div>   
            </div>
            <div class="top_divider"><img src="__ROOT__/Public/images/header_divider.png" alt="" title="" width="1" height="164" /></div>
        </div>
        <!-- 展示区定义结束--> 
        
    </div>
    <!--logo及展示区块定义结束-->
    <div id="main_content">
         <div id="menu_tab">
            <div class="left_menu_corner"></div>
            <ul class="menu">
                <li><a href="__APP__" class="nav1"> 首页 </a></li>
                <li class="divider"></li>
				<?php if(is_array($nav)): $i = 0; $__LIST__ = $nav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$li): $mod = ($i % 2 );++$i;?><li <?php if($li['id']==$_GET['banid']): ?>class="thisli"<?php endif; ?>>
					<a href='<?php if(($li["type"]) == "1"): ?>__APP__/shop/index?banid=<?php echo ($li["id"]); endif; ?>
                            <?php if(($li["type"]) == "2"): ?>__APP__/shop/index1?banid=<?php echo ($li["id"]); endif; if(($li["type"]) == "3"): ?>__APP__/index/ware?banid=<?php echo ($li["id"]); endif; if(($li["type"]) == "4"): ?>__APP__/index/zt?banid=<?php echo ($li["id"]); endif; ?>' class="nav1"><?php echo ($li["ban_name"]); ?></a></li>
					<li class="divider"></li><?php endforeach; endif; else: echo "" ;endif; ?>
                <li><a href="__APP__/index/ly" class="nav1">意见反馈</a></li>
                <li class="divider"></li>
            </ul>
            <div class="right_menu_corner"></div>
        </div>
        <!-- 结束导航条定义 -->

        <div class="alert alert-info fade in" style="margin-top:8px;margin-bottom:0px">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <span class="alert-heading">
                <?php if(isset($_SESSION['cardn'])): ?>欢迎您，<?php echo (session('username')); ?> <a class="btn" href="<?php echo U('user/index');?>">进入用户中心</a>
                <a class="btn btn-primary" href="<?php echo U('user/order');?>">查看订单信息</a>
                <?php else: ?>
                现在登录，查看您的订单信息 <a class="btn btn-primary" href="__APP__/Public/login">登录</a><?php endif; ?>
            </span>
          </div>
        <div class="crumb_navigation"> 您现在所在的位置: <span class="current">首页</span></div>
        <div class="left_content">
			            <div class="title_box"><a href="__APP__/index/newslist" class="broad">网站公告</a></div>
            <ul class="left_menu">
			   <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ls): $mod = ($i % 2 );++$i; if($ls['ishot']==1): ?><li class="odd"><a href="__APP__/index/detial/id/<?php echo ($ls["id"]); ?>"><?php echo ($ls["title"]); ?></a></li>
			   		<?php else: ?>
			   			<li class="even"><a href="__APP__/index/detial/id/<?php echo ($ls["id"]); ?>"><?php echo ($ls["title"]); ?></a></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </ul>
			            <div class="title_box">特别推荐</div>
			<?php if(is_array($ad_data)): $i = 0; $__LIST__ = $ad_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$li): $mod = ($i % 2 );++$i; if(($li["type"]) == "1"): ?><div class="border_box">
				<div class="product_title"><a href="<?php echo ($li["url"]); ?>"><?php echo ($li["shop_name"]); ?></a></div>
				<div class="product_img"><a href="<?php echo ($li["url"]); ?>"><img src="__ROOT__/Public/upload/<?php echo ($li["pic"]); ?>" alt="" title="" /></a></div>
				<div class="prod_price"><span class="price">鑫鑫相印</span></div>
				</div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </div>
        
        <div class="center_content">
            <div class="center_title_bar">推荐商家</div>

			 <?php if(is_array($hot_data)): $i = 0; $__LIST__ = $hot_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$li): $mod = ($i % 2 );++$i;?><div class="prod_box">
					<div class="top_prod_box"></div>
					<div class="center_prod_box">
					   <div class="product_title"><a href="__APP__/Shop/detial1/id/<?php echo ($li["id"]); ?>"><?php echo ($li["shop_name"]); ?></a></div>
					   <div class="product_img">
					       <a href="__APP__/Shop/detial1/id/<?php echo ($li["id"]); ?>">
                                <?php if(isset($$li["can_buy"])): if(!$li.can_buy): ?><div class="discount"><img src="/Public/images/rest.png" /></div><?php endif; endif; ?>
                                <img src="__ROOT__/Public/upload/thumb_<?php echo ($li["logo_href"]); ?>" alt="" />
                            </a>
					   </div>
					</div>
                <div class="bottom_prod_box"></div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>

            <div class="center_title_bar">最新入驻商家</div>

				<?php if(is_array($new_data)): $i = 0; $__LIST__ = $new_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$li): $mod = ($i % 2 );++$i;?><div class="prod_box">
                <div class="top_prod_box"></div>
                <div class="center_prod_box">
                    <div class="product_title"><a href="__APP__/Shop/detial1/id/<?php echo ($li["id"]); ?>"><?php echo ($li["shop_name"]); ?></a></div>
                    <div class="product_img"><a href="__APP__/Shop/detial1/id/<?php echo ($li["id"]); ?>"><img src="__ROOT__/Public/upload/thumb_<?php echo ($li["logo_href"]); ?>" alt="" /></a></div>
                </div>
                <div class="bottom_prod_box"></div>

            </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <!-- 中央区定义结束 -->
        
        <div class="right_content">
			            <div class="shopping_cart">
                <div class="cart_title">购物车</div>
                <div class="cart_details"> 您的购物车中有<?php echo (($_SESSION['car']['sum'])?($_SESSION['car']['sum']):"0"); ?>件商品<br />
                    <span class="border_cart"></span> 总价是：<span class="price">￥<?php echo ((session('car_money'))?(session('car_money')):0); ?></span> </div>
                <div class="cart_icon"><a href="__APP__/car/mycar" title="结账"><img src="__ROOT__/Public/images/shoppingcart.png" alt="" title="" width="48" height="48"/></a></div>
            </div>
			
			            <div class="title_box">最新订单</div>
                <ul class="new_order">
					<?php if(is_array($new_order)): $i = 0; $__LIST__ = $new_order;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ls): $mod = ($i % 2 );++$i;?><li><a><?php echo ($ls["desn"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
        </div>
        
    </div>
    <div class="footer">
        <div class="left_footer"></div>
        <div class="center_footer">Copyright © 2011 - 2012&nbsp;&nbsp;www.5ifas.com.&nbsp;&nbsp;All Rights Reserved<br />
         艾星网络公司版权所有
        </div>
        <div class="right_footer"> <a href="__APP__">首页</a> <a href="__APP__/index/ly">意见反馈</a> <a href="#">回到顶部</a> </div>
    </div>
</div>
</body>
</html>