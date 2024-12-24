<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:57:"/var/www/pro-audit-ct/application/view/manager/users.html";i:1730283939;s:57:"/var/www/pro-audit-ct/application/view/common/header.html";i:1730478573;s:57:"/var/www/pro-audit-ct/application/view/common/footer.html";i:1730283939;}*/ ?>
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
 
<script src="/static/js/plugins/layer/layer.min.js"></script>

<?php $ofs = ['管理部门', '管网大道办', '管网雪野办', '管网高南办', '管网曹路办', '管网浦江办']; ?>


    <div class="contentBox">
        <div class="list-tool">
            <?php if(\think\Session::get('user.type') == 0): ?>
            <button class="search" onclick="tj();">添加账号</button>
            <?php endif; ?>
            <button class="search" onclick="window.location.href='<?php echo U('/manager/users'); ?>';">全部</button>
            <form action="" style="display:inline;">
                <select id="office" name="office"><option value="" style="font-weight:bold;color:#AAA;font-size:120%">部门</option><option value="0">管理员级</option><option value="1">部级</option><option value="2">所级</option><option value="3">站级</option></select>
                <?php if(\think\Session::get('user.type') == 0): ?>
                <select id="place" name="place" style="margin:0 2px;width:140px;display:inline;"><option value="0" style="font-weight:bold;color:#AAA;font-size:120%">管理所</option></select>
                <select id="station" name="station" style="margin:0 2px;width:90px;display:inline;"><option value="0" style="font-weight:bold;color:#AAA;font-size:120%">管理站</option></select>
                <?php endif; ?>
                <select id="status" name="status"><option value="" style="font-weight:bold;color:#AAA;font-size:120%">全部</option><option value="1">正常</option><option value="2">已禁用</option></select>
                <input value="<?php echo I('name'); ?>" type="text" placeholder="账户名" name="name" class="adress"/>
                <button class="search" type="submit">搜索</button>
            </form>
            <button class="search" type="button" onclick="window.location.reload()">刷新</button>
        </div>
        <div class="tab-space">
            <!--<p class="title-page">共<span>41356</span>条记录&nbsp;&nbsp;&nbsp;&nbsp;1655页</p>-->
            <table class="list-tab" id="table">
                <thead>
                    <tr>
                        <td width="15%">用户ID</td>
                        <td width="20%">账户名</td>
                        <td width="25%">部门</td>
                        <td width="15%">申诉权限</td>
                        <td width="15%">最近登录</td>
                        <td width="10%">状态</td>
                    </tr>
                </thead>
                <tbody>
				<?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): if( count($data)==0 ) : echo "" ;else: foreach($data as $key=>$val): ?>
                    <tr>
                        <td><?php echo $val['uid']; ?></td>
                        <td><?php echo $val['username']; ?></td>
                        <td class="uoffice"><?php echo $val['type']; ?> <?php echo $val['office']; ?></td>
                        <td><?php echo !empty($val['audit'])?'有' : '无'; ?></td>
                        <td><?php echo date('Y-m-d H:i:s', $val['logintime']); ?></td>
                        <td class="ustatus"><?php echo $val['status']; ?></td>
                    </tr>
				<?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
			<?php echo $page; ?>
		</div>
        <textarea id="adduserlayer" style="display:none">
                <div class="login-cent" style="margin-top:0;padding-top:10px;padding-bottom:0;height:480px;">
                    <div class="login-row" style="margin-top:0">
                        <p>账户名</p>
                        <input id="username" type="text" placeholder="请输入账户名" value="">
                    </div>
                    <div class="login-row" style="margin-top:20px">
                        <p>部门</p>
                <select id="ofid" onchange="ofid_onchange(this)" style="width:91px;height: 36px;line-height: 36px;border-radius: 4px;border: 1px solid #3C5DE8;padding-left: 10px;background: #F3F3F4;color: #323232;margin-top: 5px;"><option value="0">管理员级</option><option value="1">部级</option><option value="2">所级</option><option value="3">站级</option></select>
                <select id="placeid" onchange="placeid_onchange(this)" style="display:none;width:136px;height: 36px;line-height: 36px;border-radius: 4px;border: 1px solid #3C5DE8;padding-left: 10px;background: #F3F3F4;color: #323232;margin-top: 5px;"></select>
                <select id="stationid" style="display:none;width:98px;height: 36px;line-height: 36px;border-radius: 4px;border: 1px solid #3C5DE8;padding-left: 10px;background: #F3F3F4;color: #323232;margin-top: 5px;"></select>

                    </div>
                    <div class="login-row" style="margin-top:20px">
                        <p>申诉权限</p>
                        <select id="audit" style="width:91px;height: 36px;line-height: 36px;border-radius: 4px;border: 1px solid #3C5DE8;padding-left: 10px;background: #F3F3F4;color: #323232;margin-top: 5px;"><option value="1" selected>有</option><option value="0">无</option></select>
                    </div>
                    <div class="login-row" style="margin-top:20px">
                        <p>密码</p>
                        <input placeholder="请输入密码" id="password" type="password">
                    </div>
                    <div class="login-row" style="margin-top:20px">
                        <p>确认密码</p>
                        <input placeholder="请再次输入密码" id="password1" type="password">
                    </div>
                    <div style="text-align:center;">
                        <button class="login-btn" style="display:inline-block;width:35%" onclick="qr()">确认</button>
                        <button class="login-btn" style="display:inline-block;width:35%;margin-left:20px" onclick="tj2();">取消</button>
                    </div>
                </div>
        </textarea>
	</div>
<script src="/static/js/jquery.min.js"></script>
<script src="/static/js/plugins/layer/layer.min.js"></script>
<script>
var tmp = <?php echo !empty($tmp)?json_encode($tmp, 320) : '[]'; ?>, cs = [], gs = [], ctpl = '';
$(function(){
    $('.h_manager').addClass('active');
    $('.drop-down-content li a').each(function(index,item){if("账户管理"==item.innerHTML){item.className="active"}})
});

function ofid_onchange(dom) {
    $('#placeid').hide();
    $('#stationid').hide().html("");

    if ($("#ofid").val() == '2') {
        $('#placeid').show();
        $('#placeid').val('1');
    } else if ($("#ofid").val() == '3') {
        $('#placeid').show();
        $('#placeid').val('1');
        $("#stationid").show().html(window.optionhtml[$("#placeid").val()]);
        $("#stationid").prop('selectedIndex', 0);
    }
}
function placeid_onchange(dom) {
    if ($("#ofid").val() == '3') {
        $('#stationid').html("");
        $("#stationid").show().html(window.optionhtml[$("#placeid").val()]);
        $("#stationid").prop('selectedIndex', 0);
    }
}


function tj2(){
    layer.close(adduserlayer)
}
var adduserlayer;
function tj(){
    adduserlayer = layer.open({
        type: 1,
        title: "添加账号",
        content: $("#adduserlayer").val(),
        area: ['406'],
        maxmin: false
    });
    $("#placeid").append(window.willappend["place"]);
    $("#stationid").html("")
}

function qr(){
	var username = $("#username").val(), password = $("#password").val(), password1 = $("#password1").val();
	if(!username){layer.tips('请输入账户名', '#username');$('#username').focus();return false;}
	if(!password){layer.tips('请输入密码', '#password');$('#password').focus();return false;}
	if(password.length < 6){layer.tips('密码必须大于等于6位', '#password');$('#password').focus();return false;}
	if(!password1){layer.tips('请再次输入密码', '#password1');$('#password1').focus();return false;}
	if(password != password1){layer.tips('两次输入密码不一致', '#password1');$('#password1').focus();return false;}
    var load = layer.load(1, {shade: [0.5, '#000']});
    var ofid = 0; if ($('#ofid').val()) {ofid = $('#ofid').val();}
    var placeid = 0; if ($('#placeid').val()) {placeid = $('#placeid').val();}
    var stationid = 0; if ($('#stationid').val()) {stationid = $('#stationid').val();}
	var audit = $('#audit').val();
    layer.close(adduserlayer);
	$.post('<?php echo U('/manager/users');; ?>', {type:2, office:ofid, placeid:placeid, stationid:stationid, audit:audit, username:username, password:password}, function(c){
		layer.close(load);
		if(c.code == 1){
			location.reload();
		}else{
			alert(c.msg);
		}
	});
}
var willappend = {"place": [], "station": []};
var optionhtml = {};
$(function(){
	$('.ustatus').each(function(){
		var sts = parseInt($(this).text()), h = '<div class="s1">正常</div>';
		if(sts == 2) h = '<div class="s2">已禁用</div>';
		$(this).html(h);
	});
	$('.uoffice').each(function(){
		var sts = $(this).text().split(' ')
		if (sts[0] == '0') {
            $(this).html("管理员");
        }
        if (sts[0] == '1') {
            $(this).html("部级");
        }
        if (sts[0] == '2') {
            $(this).html(tmp["place"][sts[1]]);
        }
        if (sts[0] == '3') {
            var station_str = "";
            for (var station in tmp["station"]) {
                if (tmp["station"][station][0] == sts[1]) {
                    station_str = tmp["station"][station][3] + " " + tmp["station"][station][1];
                }
            }
            $(this).html(station_str);
        }
	});
    $.each(tmp, function(i, v) {
        $.each(v, function(i2, v2){
            if(i == 'station') {
                if (typeof window.optionhtml[v2[2]] == "undefined") {
                    window.optionhtml[v2[2]] = "";
                }
                window.optionhtml[v2[2]] += '<option value="'+ v2[0] +'">'+ v2[1] +'</option>';
            } else {
                var h = '<option value="'+ i2 +'">'+ v2 +'</option>';
                $('#place').append(h);
                window.willappend["place"] += h;
            }
        });
    });
	$('.ustatus').on('click', 'div', function(){
		var t = $(this), y = '禁用', status = 2, uid = t.parents('tr').find('td').eq(0).text();
		if(t.attr('class') == 's2'){y = '启用';status = 1;}
        //询问框
        layer.confirm('确定要'+ y +'吗', {
            title: "" + y,
            btn: ['确定', '取消'] //按钮
        }, function(){
            layer.close(layer.index);
			var load = layer.load(1, {shade: [0.5, '#000']});
			$.post('<?php echo U('/manager/users');; ?>', {type:1, status:status, uid:uid}, function(c){
				layer.close(load);
				if(c.code == 1){
					t.attr('class', status == 2 ? 's2' : 's1').text(status == 2 ? '已禁用' : '正常');
				}else{
					alert(c.msg);
				}
			});

        }, function(){

        });
	});
	$('.page .current').css({'background': '#3251FF'});
	//$('.page .page-li.page-num.current').css({'padding': '0 10px'});
	$('.page .page-li').css('display', 'inline-block');
	$('.page .page-li input').css({'width':'50px', 'background':'#2B4563', 'border': '1px solid #7591BE', 'border-radius': '6px', 'display': 'inline-block', 'color': 'rgba(255,255,255,0.65)', 'margin-right': '5px'});
	$('.page .page-msg, .page ul').css('display', 'inline-block');
	$('.page .page-msg .rows').css({'font-size': '16px', 'background':'none'});
	$('#status').val('<?php echo I('status'); ?>');

    $('#office').val('<?php echo I('office'); ?>').change(function(){
        if ($(this).val() == '2' || $(this).val() == '3') {
            $('#place option').show();
        } else {
            $('#place option').hide();
            $('#place option:first').show();
        }
        $('#place').val('0');

        $('#station').html('<option value="0" style="font-weight:bold;color:#AAA;font-size:120%">管理站</option>');
        $('#station').val('0');
    });
    if ($('#office').val() == '2' || $('#office').val() == '3') {
        $('#place option').show();
    } else {
        $('#place option').hide();
        $('#place option:first').show();
    }
    $('#place').val('0');
    if ('<?php echo I('place'); ?>' != '') {
        $('#place').val('<?php echo I('place'); ?>')
    }
    $('#place').change(function(){
        $('#station').html('<option value="0" style="font-weight:bold;color:#AAA;font-size:120%">管理站</option>');
        if($('#place').val() != '') $('#station').html( $('#station').html() + window.optionhtml[$('#place').val()] );
        $('#station').val('0');
    });
    $('#station').html( $('#station').html() + window.optionhtml[$('#place').val()] ).show();
    $('#station').val('0');
    if ('<?php echo I('station'); ?>' != '') {
        $('#station').val('<?php echo I('station'); ?>')
    }
});
</script>
<style>
    #table thead td{font-size:14px;font-weight:bold;}
    #table tbody td{font-size:14px;}
    #table tbody td a{color: #C0C9E1;}
</style>

</body>
</html>