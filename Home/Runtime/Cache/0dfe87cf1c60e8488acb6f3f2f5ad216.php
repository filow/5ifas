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
    <div class="span4 offset4">
        <form name="myform" action="__APP__/<?php echo (MODULE_NAME); ?>/islogin" method="post">
            <label>用户名
            </label>
            <input type="text" class="span3" autocomplete="off" name="loginname"/>
            <label>密码
            </label>
            <input type="password" class="span3" autocomplete="off" name="password"/>

            <div clas="row">
                <span><img src="__APP__/<?php echo (MODULE_NAME); ?>/code" onclick=this.src="__APP__/<?php echo (MODULE_NAME); ?>/code/"+Math.random() /></span> <span><input class="span2"  name="code" type="text" ></span>

            </div>
            <BR/>
             <input type="submit" value="登陆"class="btn btn-primary"/>
            &nbsp;&nbsp;&nbsp;&nbsp;
             <a class="btn btn-inverse" href="__APP__/Public/forget">找回密码</a>
        </form>
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