<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
    <head>
        <meta charset="utf-8">
        <title>艾星网络</title>
        <link href="__ROOT__/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="__ROOT__/Public/bootstrap/js/jquery.min.js"></script>
        <script src="__ROOT__/Public/js/publicjs.js"></script>
        <script src="__ROOT__/Public/bootstrap/js/bootstrap.min.js"></script>
        <!--[if IE 6]>    
            <script src="__ROOT__/Public/bootstrap/js/ie6.min.js"></script>
        <![endif]-->
        <!--[if lt IE 9]>
            <script src="__ROOT__/Public/bootstrap/js/html5.js"></script>
        <![endif]-->
        <!--[if IE 6]>    
            <link href="__ROOT__/Public/bootstrap/css/ie6.min.css" rel="stylesheet">
        <![endif]-->
    </head>
    <body>
        <br/><br/>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="/admin.php">艾星网络管理后台</a>
                    <div class="row" style="margin-top:10px;">
                        <span class="pull-right"><a href="#">欢迎您：<?php echo (session('username')); ?></a>&nbsp;&nbsp;<a href="__APP__/Public/logout" onclick="return confirm('您确定要退出吗？')">退出登录</a></span>
                    </div>
                </div>
            </div>
        </div>
        <br/><br/>
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span2">
                    <ul class="nav nav-pills nav-stacked ">
                        <?php if(is_array($nav_list)): $k = 0; $__LIST__ = $nav_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$nav): $mod = ($k % 2 );++$k;?><li class="dropdown" id="menutest">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#menutest<?php echo ($k); ?>">
                                    <?php echo ($nav["action_desc"]); ?>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php if(is_array($nav["children"])): $i = 0; $__LIST__ = $nav["children"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$nav_children): $mod = ($i % 2 );++$i;?><li><a href="__APP__/<?php echo ($nav_children["module_name"]); ?>/<?php echo ($nav_children["action_name"]); ?>"  ><?php echo ($nav_children["action_desc"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </div>
                <div class="span10">
                    <!--Body content-->


<div class="tabbable">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#1" data-toggle="tab">管理员登录</a></li>
        <li><a href="#2" data-toggle="tab">商户登录</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="1">
            <form name="myform" action="__APP__/<?php echo (MODULE_NAME); ?>/adminlogin" method="post">
                <label>用户名
                </label>
                <input type="text" class="span3"  autocomplete="off" name="username"/>
                <label>密码
                </label>
                <input type="password" class="span3"  autocomplete="off"name="password"/>

                <div clas="row">
                    <img src="__APP__/<?php echo (MODULE_NAME); ?>/code" onclick=this.src="__APP__/<?php echo (MODULE_NAME); ?>/code/"+Math.random() /> <span><input class="span2"  name="code" type="text" ></span>

                </div>
                <BR/>
                <button type="submit"   class="btn btn-primary" />确认</button>
            </form>
        </div>
        <div class="tab-pane" id="2">
            <form name="myform" action="__APP__/<?php echo (MODULE_NAME); ?>/shoplogin" method="post">
                <label>用户名
                </label>
                <input type="text" class="span3" autocomplete="off" name="login_name"/>
                <label>密码
                </label>
                <input type="password" class="span3" autocomplete="off" name="password"/>

               <div clas="row">
                    <img src="__APP__/<?php echo (MODULE_NAME); ?>/code" onclick=this.src="__APP__/<?php echo (MODULE_NAME); ?>/code/"+Math.random() /> <span><input class="span2"  name="code" type="text" ></span>

                </div>
                <BR/>
                <button type="submit"   class="btn btn-primary" />确认</button>
            </form>
        </div>
    </div>
</div>
      </div>
            </div>
        </div>
    </body>
</html>