<!DOCTYPE html>
<html lang="zh_CN">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="yuqi.zhou@DeNA">
        <title>微信管理</title>
        <link rel="stylesheet" href="<?php echo base_url('static/css/bootstrap.min.css');?>">
        <link rel="stylesheet" href="<?php echo base_url('static/css/bootstrap-datetimepicker.min.css');?>">
        <script src="<?php echo base_url('static/js/jquery.min.js');?>"></script>
        <script src="<?php echo base_url('static/js/bootstrap.min.js');?>"></script>
        <script src="<?php echo base_url('static/js/bootstrap-datetimepicker.min.js');?>"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style type="text/css">
        .news{border:1px dashed #CCC;}
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="navbar">
                <div class="navbar-inner">
                    <div class="container">
                      <a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                      </a>
                      <a class="brand" href="#">微信管理</a>
                      <div class="nav-collapse collapse navbar-responsive-collapse">
                        <ul class="nav">
                          <!-- <li class="<?=($active == "game")?"active":""?>"><a href="<?=base_url("/manage/index")?>">游戏选择</a></li> -->
                          <li class="dropdown <?=($active == "game")?"active":""?>">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">游戏信息 <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                              <li class="nav-header">查看所有游戏</li>
                              <li><a href="<?=base_url("/manage/index")?>">游戏选择</a></li>
                              <li class="divider"></li>
                              <li><a href="<?=base_url("/manage/wechat/gmedit/$game_id/")?>">添加/编辑游戏条目</a></li>
                            </ul>
                          </li>
                          <li class="dropdown <?=($active == "wechat")?"active":""?>">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">微信账号 <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                              <li class="nav-header">查看所有微信</li>
                              <li><a href="<?=($game_id == null)?"#":base_url("/manage/wechat/view/$game_id")?>">微信列表</a></li>
                              <li class="divider"></li>
                              <li><a href="<?=($game_id == null)?"#":base_url("/manage/wechat/wxedit/$game_id/")?>">添加新微信</a></li>
                            </ul>
                          </li>
                          <li class="<?=($active == "menu")?"active":""?>"><a href="<?=($game_id == null)?"#":base_url("/manage/wechat/menu/$game_id/$wxid")?>">自定义菜单</a></li>
                          <li class="<?=($active == "template")?"active":""?>"><a href="<?=($game_id == null)?"#":base_url("/manage/wechat/template/$game_id/$wxid")?>">模板消息</a></li>
                          <li class="<?=($active == "reply")?"active":""?>"><a href="<?=($game_id == null)?"#":base_url("/manage/wechat/reply/$game_id/$wxid")?>">自定义回复</a></li>
                          <li class="dropdown <?=($active == "event")?"active":""?>">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">礼包活动 <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                              <li class="nav-header">查看所有礼包活动</li>
                              <li><a href="<?=($game_id == null || $wxid == null)?"#":base_url("/manage/wechat/event/$game_id/$wxid/")?>">活动列表</a></li>
                              <li><a href="<?=($game_id == null || $wxid == null)?"#":base_url("/manage/wechat/event/$game_id/$wxid/add")?>">添加新活动</a></li>
                              <li class="divider"></li>
                              <li class="nav-header">查看活动所属礼包码</li>
                              <li><a href="<?=($game_id == null || $wxid == null || $eventid == null || $codeo == null)?"#":base_url("/manage/wechat/event/$game_id/$wxid/$eventid/code")?>">查看当前活动下的礼包码</a></li>
                              <li><a href="<?=($game_id == null || $wxid == null || $eventid == null || $codeo == null)?"#":base_url("/manage/wechat/event/$game_id/$wxid/$eventid/addcode")?>">导入礼包码</a></li>
                            </ul>
                          </li>
                        </ul>

                        <ul class="nav pull-right">
                          <li><a href="#" title="<?=($game_name != null)?$game_name:"未选择游戏"?>"><?=($game_name != null)?"游戏: ".mb_substr($game_name, 0, 5, 'utf-8')."...":"未选择游戏"?></a></li>
                          <li class="divider-vertical"></li>
                          <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <?= ($current_user==false)?"未登陆":$current_user; ?><b class="caret"></b></a>
                            <ul class="dropdown-menu">
                              <li><a href="<?= base_url("/admin/account/logout"); ?>">退出当前账号</a></li>
                            </ul>
                          </li>
                        </ul>
                      </div><!-- /.nav-collapse -->
                    </div>
                  </div><!-- /navbar-inner -->
                </div>
            </div>