<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:55:"/var/www/pro-audit-ct/application/view/theme/index.html";i:1730283939;s:57:"/var/www/pro-audit-ct/application/view/common/header.html";i:1730478573;s:57:"/var/www/pro-audit-ct/application/view/common/footer.html";i:1730283939;}*/ ?>
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <meta name="renderer" content="webkit" />
    <title><?php echo $title; ?> - 二次供水AI智能审核系统</title>
    <!--[if lt IE 9]>
    <script src="/static/js/html5shiv.min.js"></script>
    <script src="/static/js/respond.min.js"></script>
    <![endif]-->
    <link href="/static/css/reset.css" rel="stylesheet">
    <link href="/static/css/style.css" rel="stylesheet">
    <script src="/static/js/jquery.min.js"></script>
    <script src="/static/js/crypto-js.js"></script>
    <!--[if lt IE 9]>
    <script src="/static/js/jquery-1.11.3.min.js"></script>
    <![endif]-->
    <style>
        body{
            background-color: rgba(<?php echo $tc[0]; ?>) !important;/*背景色*/
        }
        .list-tool{
            background-color:rgba(<?php echo $tc[1]; ?>) !important; /*卡片背景色--头部*/
        }

        .contentBox h1 span,
        .list-tool{
            border-bottom-color: rgba(<?php echo $tc[8]; ?>) !important;/*分割线*/
        }

        .list-tool select option:hover,
        .leftBox,
        .rightBox,
        .rLeft-cent,
        .rRight-cent,
        .tab-space,
        .data-statistics,
        .mid-picBox,
        .btmBox,
        .have-bg{
            background-color:rgba(<?php echo $tc[1]; ?>) !important; /*卡片背景色--主体*/
        }

        .list-tool select option:hover,
        .banner-list li a:hover,
        .drop-down:hover,
        .banner-list li a.active,
        .drop-down:hover{
            color:rgba(<?php echo $tc[2]; ?>) !important; /*常规按钮色*/
        }

        .theme button,
        .layui-layer-btn .layui-layer-btn0,
        .login-btn,
        .list-tool button.refresh,
        .list-tool button{
            background-color:rgba(<?php echo $tc[2]; ?>) !important; /*常规按钮色*/
            border: none !important;/*常规按钮色*/
        }
        .page input{
            border: 1px solid rgba(<?php echo $tc[3]; ?>) !important;/*输入框色*/
            background-color: transparent !important;/*输入框背景色*/
            color: rgba(<?php echo $tc[9]; ?>) !important;/*文字色*/
        }
        .list-tool select{
            border: 1px solid rgba(<?php echo $tc[5]; ?>) !important;/*线框按钮色*/
        }

        .list-tab thead td{
            background-color: rgba(<?php echo $tc[4]; ?>) !important;/*列表选择栏色*/
        }

        .list-tool input{
            border: 1px solid rgba(<?php echo $tc[5]; ?>) !important;/*线框按钮色*/
            background-color: transparent !important;
            color: rgba(<?php echo $tc[9]; ?>) !important;/*文字色*/
        }
        #table button,
        .page span.current,
        .page span.current li.current,
        .page a,
        .page span{
            background-color: rgba(<?php echo $tc[6]; ?>) !important;/*次要按钮色*/
            color: rgba(<?php echo $tc[9]; ?>) !important;/*文字色*/
        }

        input:disabled {
            border: 1px solid rgba(<?php echo $tc[7]; ?>) !important;/*禁用按钮色*/
            background-color: rgba(<?php echo $tc[7]; ?>) !important;/*禁用按钮色*/
            color: rgba(<?php echo $tc[9]; ?>) !important;/*文字色*/
        }

        .list-tab thead td,
        .theme button,
        .layui-layer-btn .layui-layer-btn0,
        .login-btn,
        .list-tool button.refresh,
        .list-tool button,
        body,
        #table tbody td a,
        .contentBox h1,
        .contentBox h3,
        .manage-block h4,
        #zj{
            color: rgba(<?php echo $tc[9]; ?>) !important;/*文字色*/
        }
    </style>

<?php if($isDefaultTheme == '1'): ?>
 <style>
     .list-tab thead td,
     .theme button,
     .layui-layer-btn .layui-layer-btn0,
     .login-btn,
     .list-tool button.refresh,
     .list-tool button{
         color: white !important; /*默认主题固定白色*/
     }
 </style>
<?php endif; ?>
</head>
<script>
    $(function () {
        // console.log('init');
        if ('<?php echo \think\Session::get('user.type'); ?>' == 0) {
            $("#link_stat").show();
        }
    })
</script>
<!--
    <?php echo \think\Session::get('user.username'); ?>
    <?php echo \think\Session::get('user.office'); ?>
-->
<body>
    <style>
        .banner-list li {
            font-size: 18px;
            margin-top: 8px;
        }

        .drop-down-content li a {
            font-size: 16px;
        }

        .index3, .set1, .set2, .set3 {
            display: none;
        }
    </style>
    <div class="top-banner" style="height:60px;">
        <div class="logo" style="width:25%;"><a href="<?php echo U('/index');; ?>"><img style="height:41px;" src="/static/images/logo.png"></a></div>
        <div class="banner-list">
            <ul>
                <li class="index<?php echo \think\Session::get('user.type'); ?>"><a class="h_index" href="<?php echo U('/index');; ?>">首页</a></li>
                <li class="drop-down">
                    <a href="<?php echo U('/statistics');; ?>" class="h_statistics">数据统计</a> <i></i>
                    <ul class="drop-down-content">
                        <li><a href="<?php echo U('/statistics');; ?>">合格统计</a></li>
                        <?php if(\think\Session::get('user.gtype') != 4): ?>
                        <li><a href="<?php echo U('/statistics/repeat');; ?>">重复统计</a></li>
                        <?php endif; ?>
                        <li id="link_stat" style="display:none"><a href="<?php echo U('/statistics/opreports');; ?>">图片统计</a></li>
                    </ul>
                </li>
                <li class="drop-down">
                    <a class="h_orders" href="<?php echo U('/orders/index', ['type'=>0]);; ?>">工单</a> <i></i>
                    <ul class="drop-down-content">
                        <li><a href="<?php echo U('/orders/index', ['type'=>0]);; ?>">工单列表</a></li>
                        <?php if(\think\Session::get('user.gtype') != 4): ?><!--                        <li><a href="<?php echo U('/orders/index', ['cs'=>1, 'type'=>1]);; ?>">重复单</a></li>-->
                        <li><a href="<?php echo U('/repeated/index');; ?>">重复单</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo U('/orders/index', ['status'=>2, 'type'=>1]);; ?>">不合格单</a></li>
                        <?php if(\think\Session::get('user.gtype') == 0 ||\think\Session::get('user.gtype') == 2): ?>
                        <li class="set<?php echo \think\Session::get('user.type')==0 || \think\Session::get('user.username') == '中区所瞿溪站'||\think\Session::get('user.username') == '中区所'?0 : 1; ?>"><a href="<?php echo U('/smartlock/index');; ?>">智能锁</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="drop-down">
                    <a class="h_appeal" href="<?php echo U('/appeal');; ?>">申诉</a> <i></i>
                    <ul class="drop-down-content">
                        <li><a href="<?php echo U('/appeal/index', ['appeal'=>1]);; ?>">申诉中</a></li>
                        <li><a href="<?php echo U('/appeal/index', ['appeal'=>2]);; ?>">申诉通过</a></li>
                        <li><a href="<?php echo U('/appeal/index', ['appeal'=>3]);; ?>">申诉未通过</a></li>
                    </ul>
                </li>
                <li class="drop-down">
                    <a class="h_manager" href="<?php echo U('/manager/updatepassword');; ?>">设置</a> <i></i>
                    <ul class="drop-down-content">
                        <!--<li class="set<?php echo \think\Session::get('user.type'); ?>"><a href="<?php echo U('/manager/likessetting');; ?>">查重设置</a></li>-->
                        <li><a href="<?php echo U('/manager/updatepassword');; ?>">密码设置</a></li>
                        <li class="set<?php echo \think\Session::get('user.type'); ?>"><a href="<?php echo U('/manager/users');; ?>">账户管理</a></li>
                        <li class="set<?php echo \think\Session::get('user.type'); ?>"><a href="<?php echo U('/labels');; ?>">照片归档</a></li>
                        <li><a href="<?php echo U('/index/default');; ?>">切换平台</a></li>
                        <li><a href="<?php echo U('/theme/index');; ?>">主题设置</a></li>
                        <li><a href="<?php echo U('/index/logout');; ?>">安全退出</a></li>
                    </ul>
                </li>
            </ul>
            <div class="right-box">
                <!--<div class="head-img"><img src="/static/images/egg.jpeg"></div>-->
                <div class="head-time">
                    <p><?php echo \think\Session::get('user.username'); ?> 欢迎您！</p>                    
                    <!-- <p>当前平台: <?php echo \think\Session::get('user.gtype')?'泵房养护' : '水箱清洗'; ?></p> -->
                    <p>当前平台: 
                        <?php switch(\think\Session::get('user.gtype')): case "0": ?>水箱清洗<?php break; case "2": ?>泵房养护<?php break; case "4": ?>涉电维修<?php break; default: ?>未知平台
                        <?php endswitch; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            var currentUrl = window.location.href;
            $('.banner-list a').each(function () {
                let url = $(this).attr('href');
                if(currentUrl.indexOf(url) >= 0 ) {
                    $(this).addClass('active');
                }
            });
        })
    </script>
 
<style>
    .mid-picBox {
        padding-bottom: 50px;
    }
    .contentBox h1 {
        font-size: 26px;
        margin-bottom: 10px;
    }

    .contentBox h1 span {
        border-bottom-width: 3px;
        border-bottom-style: solid;
    }

    .contentBox h3 {
        font-size: 20px;
        margin-bottom: 10px;
    }

    .contentBox .default {
        margin-bottom: 50px;
    }

    .contentBox ul.item {
        display: flex;
    }

    .contentBox .default li{
        margin-right: 80px;
    }

    .contentBox ul.item li {
        width: 302px;
        height: 264px;
        background-repeat: no-repeat;
        background-size: cover;
        position: relative;
        cursor: pointer;
        box-sizing: border-box;
        border-radius: 5px;
    }

    .contentBox ul.item li img {
        position: relative;
        max-width: 100%;
        z-index: 1;
    }

    .contentBox ul.item li button {
        position: absolute;
        bottom: 15px;
        width: 100px;
        font-size: 18px;
        border-radius: 15px;
        cursor: pointer;
        border: none;
        left: 100px;
        z-index: 10;
    }

    .btns button {
        width: 100px;
        font-size: 18px;
        border-radius: 15px;
        cursor: pointer;
        border: none;
    }

    .diy ul.item{
        display: block;
        float: left;
        margin-right: 30px;
    }

    .diy ul.item li.cur,
    .diy ul.item li:hover {
        border: 2px solid;
        border-image: linear-gradient(to right, red, blue, yellow, purple, green, orange) 1;
        box-shadow: 3px 3px 8px 3px #ccc;
    }
    .diy .color_box{
        display: none;
        float: left;
        border: 2px solid;
        height: 261px;
        border-image: linear-gradient(to right, red, blue, yellow, purple, green, orange) 1;
        padding: 15px;
        box-sizing: border-box;
        text-align: center;
    }

    .diy .color_box ul{
        display: flex;
        flex-wrap: wrap;
        width: 700px;
    }

    .diy .color_box ul li{
        display: flex;
        flex-direction: column;
        margin: 20px;
        justify-content: center;
        align-items: center;
    }
    .diy .color_box ul li p{
        font-size: 16px;
        width: 100%;
        text-align: center;
        float: left;
        margin-bottom: 10px;
    }

    .diy .color_box .color_box_s{
        float: left;
        width: 100px;
        height: 30px;
        border: 1px solid #cccccc;
        box-sizing: border-box;
        cursor: pointer;
        position: relative;
        z-index: 1;
    }
    .diy .color_box .color_box_s span{
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        z-index: 10;
    }
    .diy .color_box .color_box_s span.colorpicker{
        z-index: 20;
    }
    .diy .color_box .color_box_s label{
        position: absolute;
        left: 0;
        right: 0;
        top: 6px;
        bottom: 0;
        z-index: 20;
    }
    .layui-colorpicker{
        opacity: 0 !important;
    }
    .layui-anim-downbit{
        left: auto !important;
        top: auto !important;
        bottom: 150px !important;
        right: 50px !important;
    }
    .layui-layer-content{
        color: black;
    }

    #diyCancel{
        color: black !important;
        background-color: #cccccc !important;
    }


</style>
<div class="contentBox theme" style='height:auto;'>
    <div class="mid-picBox" style="margin-top:10px;">
        <h1><span>主题设置</span></h1>
        <div class="default">
            <h3>经典主题</h3>
            <ul class="item">
                <?php if(is_array($themeDefaultList) || $themeDefaultList instanceof \think\Collection || $themeDefaultList instanceof \think\Paginator): $i = 0; $__LIST__ = $themeDefaultList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <li data-id="<?php echo $vo['id']; ?>" <?php if($vo['id'] == $theme_id): ?>style="border:1px solid rgba(<?php echo $vo['color'][2]; ?>);box-shadow: 3px 3px 8px 3px rgba(<?php echo $vo['color'][2]; ?>);" <?php endif; ?>>
                <img src="/static/images/t<?php echo $key + 1; ?>.png" alt="">
                <button data-id="<?php echo $vo['id']; ?>">
                    <?php echo $vo['name']; ?>
                </button>
                </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
        <div class="diy">
            <h3>自定义主题</h3>
            <ul class="item">
                <li <?php if($isDiyTheme == '1'): ?>class="cur" <?php endif; ?>>
                    <img src="/static/images/t4.png" alt="">
                    <button id="diyBtn">自定义</button>
                </li>
            </ul>
            <div class="color_box">
                <ul>
                    <?php if(is_array($newDiyTheme) || $newDiyTheme instanceof \think\Collection || $newDiyTheme instanceof \think\Paginator): $i = 0; $__LIST__ = $newDiyTheme;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <li>
                        <p><?php echo $vo[1]; ?></p>
                        <div class="color_box_s">
                            <span class="bj" style="background-color:rgba(<?php echo $vo[0]; ?>);"></span>
                            <label><?php echo $vo[2]; ?></label>
                            <span class="colorpicker"
                                  lay-options="{color: 'rgba(<?php echo $vo[0]; ?>)'}"
                                  data-color="rgba(<?php echo $vo[0]; ?>)">
                            </span>
                        </div>
                    </li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
                <div class="btns">
                    <button id="diyOk" type="button">保存</button>
                    <button id="diyCancel" type="button">取消</button>
                </div>
            </div>
        </div>
    </div>
</div>
<link href="/static/layui/css/layui.css" rel="stylesheet">
<script src="/static/layui/layui.js"></script>
<script>
    layui.use(function () {
        var layer = layui.layer;
        var $ = layui.$;
        var colorpicker = layui.colorpicker;

        // 默认主题
        $('.default li').each(function () {
            $($(this)).on("click", function () {
                let id = $(this).data('id');
                // console.log(id);
                if (id == <?php echo $theme_id; ?>) return false;
                layer.confirm('确定更换主题吗?', function (index) {
                    $.post("<?php echo U('/theme/setTheme'); ?>", {id: id}, function (data, status) {
                        layer.msg(data['msg']);
                        if (data['code'] == 1) {
                            window.location.reload();
                        }
                    });
                    layer.close(index);
                });
            });
        });

        // 自定义主题
        colorpicker.render({
            elem: '.colorpicker',
            color: 'rgba(68,66,66,0.5)',
            predefine: true,
            format: 'rgb',
            alpha: true,
            done: function(color){
                $(this.elem).attr('data-color',color);
                $(this.elem).siblings('span.bj').css('background-color', color);
                // 清空或取消选择时也执行 change
                color || this.change(color);
            },
            change: function(color){
                $(this.elem).attr('data-color',color);
                $(this.elem).siblings('span.bj').css('background-color', color);
            },
            cancel: function(color){ // 取消颜色选择的回调 --- 2.8+
                this.change(color);
            }
        });
        $('#diyBtn').on("click", function () {
            $('.diy .color_box').show();
        });
        $('#diyOk').on("click", function () {
            let arr = [];
            $('.diy .color_box ul li span.colorpicker').each(function () {
                let color = $(this).data('color');
                color = color.slice(5);
                color = color.substring(0,color.length - 1);
                arr.push(color);
            });
            //console.log(arr);
            $.post("<?php echo U('/theme/setDiyTheme'); ?>", {color: JSON.stringify(arr)}, function (data, status) {
                if (data['code'] == 1) {
                    layer.msg(data['msg'], {
                        icon: 1,
                        time: 2000 // 设置 2 秒后自动关闭
                    }, function(){
                        window.location.reload();
                    });
                }else{
                    layer.msg(data['msg']);
                }
            });
            layer.close(index);
        });

        $('#diyCancel').on("click", function () {
            $('.diy .color_box').hide();
        });


    });


</script>

</body>
</html>