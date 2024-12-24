<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:56:"/var/www/pro-audit-ct/application/view/labels/index.html";i:1730283939;s:57:"/var/www/pro-audit-ct/application/view/common/header.html";i:1730478573;s:57:"/var/www/pro-audit-ct/application/view/common/footer.html";i:1730283939;}*/ ?>
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
.imgShow thead td{font-weight: bold;font-size: 16px;}
.imgShow tbody td .imgs{text-align: left;margin-left:60px;/*width: 100%;*/height: 350px;overflow-x: hidden;}
.imgs .show-image {height: 345px;}
.show-image {width: 280px;height: 350px;}
.img-text{width:265px;}
.img-text{font-size: 13px;}
.img-text .b {font-weight: bold;font-size: 20px;}
.show-image button{position:relative;left: 210px;top: -350px;width: 60px;background: #0091FF;color: #fff;cursor: pointer;border: 1px solid #246699; border-radius: 2px; height: 36px;line-height: 36px;}
.page{padding-bottom:20px;}


.contentBox {
    height: calc(100vh - 93px);
}
.list-tool{display:none;}
.ctl{height:60px;border-right: 1px solid #17294D;}
.tab-space {padding-bottom:16px;}
.list-tab tbody td{border: 1px solid #17294D;}
#lps, #rps{height: calc(100vh - 191px);}
.ps{overflow-y:scroll;}
#rps img{width: 90px;height: 120px;margin:5px;border-radius: 6px;}
.m{margin-bottom:10px;}
.imgShow thead td, .imgShow tbody td {text-align: left;}
tr,td{vertical-align: top;}
#dq, #submit{width: 70px;height: 28px;background: #3251FF;border-radius: 2px;font-size: 12px;color: #EEF4FF;cursor: pointer;border: 1px solid #434C68;}
#dq{margin: 0 30px 0 -150px;}
#submit{margin-right: 30px;}
::-webkit-scrollbar {
width: 1px;
}
</style>
        <div class="contentBox">
            <div class="list-tool">
				<button class="search" onclick="window.location.href='<?php echo U('/likes'); ?>';">全部</button>
				<form action="<?php echo U('/likes'); ?>" style="display:inline;">
					<!--<select id="gdtype" name="gdtype" style="margin-left:25px;"><option value="">作业类型</option><option value="1">水箱清洗</option><option value="2">水池清洗</option></select>-->
					<select id="type" name="type" style="margin:0 2px;width:80px;"><option value="">类型</option><option value="0">母单</option><option value="1">子单</option></select>
					<select id="place" name="place" class="form-control type" style="margin:0 2px;width:140px;display:inline;"><option value="">管理所</option></select>
					<select id="station" name="station" class="form-control type" style="margin:0 2px;width:90px;display:inline;"><option value="">管理站</option></select>
					<select id="office" name="office" class="form-control type" style="margin:0 2px;width:90px;display:inline;"><option value="">维保单位</option></select>
					<input style="margin:0 2px;" value="<?php echo I('addr'); ?>" class="adress" type="text" placeholder="工单编号 / 地址" name="addr" />

					<input style="margin:0" value="<?php echo I('start'); ?>" class="tiem_put" id="start" name="start" placeholder="开始日期" readonly />
					<span>-</span>
					<input style="margin:0" value="<?php echo I('end'); ?>" class="tiem_put" id="end" name="end" placeholder="结束日期" readonly />
					<select name="type" style="width:90px;margin:0 2px;" id="fs2"><option value="">图片分类</option></select>
					<select id="matching" name="matching" style="width:80px;margin:0 2px;">
						<option value="">相似度</option>
						<!--<option value="80">≥80</option>
						<option value="81">≥81</option>
						<option value="82">≥82</option>
						<option value="83">≥83</option>
						<option value="84">≥84</option>
						<option value="85">≥85</option>
						<option value="86">≥86</option>
						<option value="87">≥87</option>
						<option value="88">≥88</option>
						<option value="89">≥89</option>-->
						<option value="90">≥90</option>
						<option value="91">≥91</option>
						<option value="92">≥92</option>
						<option value="93">≥93</option>
						<option value="94">≥94</option>
						<option value="95">≥95</option>
						<option value="96">≥96</option>
						<option value="97">≥97</option>
						<option value="98">≥98</option>
						<option value="99">≥99</option>
					</select>

					<button style="margin:0 2px" class="search" type="submit">搜索</button>
				</form>
				<button style="margin:0 2px;" class="refresh" onclick="window.location.reload();">刷新</button>
            </div>
            <div class="tab-space">
                <!--<p class="title-page">共<span>41356</span>条记录&nbsp;&nbsp;&nbsp;&nbsp;1655页</p>-->
                <table class="list-tab imgShow">
                    <thead>
                        <tr class="ctl">
                            <td width="50%">
								工单照片
								<select id="ls"><option value="">请选择分类</option></select>
								<input style="margin:0 10px 0 30px;" type="checkbox" id="all" />全选
								
							</td>
                            <td width="50%">
								<button id="dq">丢弃</button>
								<button id="submit">归档 >></button>
								归档照片
								<select id="rs"><option value="">请选择分类</option></select>
							</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="lps"><div class="ps"></div></td>
                            <td id="rps"><div class="ps"></div></td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
<script src="/static/js/plugins/layer/layer.min.js"></script>
<script src="/static/js/plugins/layer/laydate/laydate.js"></script>
<script>
var load = layer.load(1, {shade: [0.5, '#000']}), tmp = <?php echo !empty($tmp)?json_encode($tmp, 320) : '[]'; ?>, st = '';
$(function(){
	setTimeout(function(){layer.close(load);}, 100);
	$('.h_likes').addClass('active');
	$.each(tmp, function(i, v){
		var h = '';
		$.each(v, function(i2, v2){
			if(i == 'ls') h += '<option value="'+ v2.id +'" pcid="'+ v2.pcid +'">'+ v2.name +'</option>'; else h += '<option value="'+ v2.id +'" pid="'+ v2.pid +'" title="'+ v2.name +'">'+ v2.title +'（'+ v2.name +'）</option>';
		});
		$('#'+i).append(h);
	});
	
	function dir(name){
		$('#rps .ps').html('');
		if(name == ''){
			layer.close(load);
			return;
		}
		load = layer.load(1, {shade: [0.5, '#000']});//layer.close(load);return;
		$.post('?', {t:2, dir:name}, function(d){
			$.each(d, function(i, v){
				$('#rps .ps').append('<img src="/source/labels/'+ name + '/' + v +'" />');
			});
			layer.close(load);
		}, 'json');
	}
	
	$('#ls').change(function(){
		$('#lps .ps').html('');
		if($(this).val() == ''){
			layer.close(load);
			return;
		}
		var load = layer.load(1, {shade: [0.5, '#000']}), ycode = $(this).val();
		$.post('?', {t:1, ycode:ycode}, function(d){
			$.each(d, function(i, v){
				$('#lps .ps').append('<div class="show-image m" data-pid="">\
                                    <a target="_blank" href="/source/pics/'+ v.etime.replace('-', '').substr(0,6) +'/'+ v.oid +'/'+ v.filename +'.'+ v.extension +'"><img src="/source/pics/'+ v.etime.replace('-', '').substr(0,6) +'/'+ v.oid +'/'+ v.filename +'.'+ v.extension +'" data-title="'+ v.id +' '+ v.ycode +'" /></a>\
									<div class="img-text">\
										<p>工单编号:'+ v.gid +' &nbsp; ' + v.etime + '</p>\
										<p><input style="margin-right:10px;" type="checkbox" value="'+ v.id +'" />'+ v.addr +'<p>\
									</div>\
                                </div>');
			});
			d = $('#rs option[pid='+ $('#ls option:selected').attr('pcid') +']:first');
			d.attr("selected", true);
			dir(d.attr('title'));
		});
		$('#all').prop('checked', false);
	});
	$('#rs').change(function(){
		load = layer.load(1, {shade: [0.5, '#000']});
		dir($(this).find("option:selected").attr('title') || '');
	});
	$('#all').click(function(){
		if($(this).is(':checked') == true) $('#lps input:checkbox').prop("checked", true); else $('#lps input:checkbox').prop("checked", false);
	});
	
	$('#dq').click(function(){
		if(!$('#lps input:checked').length){layer.msg('请选择照片', {icon: 2, shade: 0.5, time: 1000});return false;}
		if(confirm('确定丢弃选中的照片吗？')){
			layer.msg('正在处理，请稍后。。。', {icon:1, time:6000000});
			layer.load(1, {shade: [0.5, '#000']});
			var ids = [];
			$('#lps input:checked').each(function(i, v){ids.push($(this).val())});
			console.log(ids);
			$.post('?', {t:4, ids:ids, dir:$('#rs').find("option:selected").attr('title')}, function(d){
				layer.closeAll();
				console.log(d);
				if(d == 1){
					layer.msg('处理成功', {icon: 6, shade: 0.5, time: 1000});
					$('#lps input:checked').each(function(i, v){
						m = $(this).parents('.m');
						m.remove();
					});
					$('#all').prop('checked', false);
				}else layer.msg(d, {icon: 2, shade: 0.5, time: 5000});
			}, 'json');
		}
	});
	
	$('#submit').click(function(){
		if(!$('#lps input:checked').length){layer.msg('请选择照片', {icon: 2, shade: 0.5, time: 1000});return false;}
		if(confirm('确定归档选中的照片吗？')){
			layer.msg('正在处理，请稍后。。。', {icon:1, time:6000000});
			layer.load(1, {shade: [0.5, '#000']});
			var ids = [];
			$('#lps input:checked').each(function(i, v){ids.push($(this).val())});
			console.log(ids);
			$.post('?', {t:3, ids:ids, dir:$('#rs').find("option:selected").attr('title'), cid:$('#rs').val()}, function(d){
				layer.closeAll();
				console.log(d);
				if(d == 1){
					layer.msg('处理成功', {icon: 6, shade: 0.5, time: 1000});
					$('#lps input:checked').each(function(i, v){
						m = $(this).parents('.m');
						$('#rps img:first').before('<img src="'+ m.find('img').attr('src') +'" />');
						m.remove();
					});
					$('#all').prop('checked', false);
				}else layer.msg(d, {icon: 2, shade: 0.5, time: 5000});
			}, 'json');
		}
	});
	
	var hh = $('#lps').height();
	$('.ps').css({'height':hh+'px'});
	$(window).resize(function(){hh = $('#lps').height();$('.ps').css({'height':hh+'px'});});;
	
	$('.page .current').css({'background': '#3251FF'});
	//$('.page .page-li.page-num.current').css({'padding': '0 10px'});
	$('.page .page-li').css('display', 'inline-block');
	$('.page .page-li input').css({'width':'50px', 'background':'#2B4563', 'border': '1px solid #7591BE', 'border-radius': '6px', 'display': 'inline-block', 'color': 'rgba(255,255,255,0.65)', 'margin-right': '5px'});
	$('.page .page-msg, .page ul').css('display', 'inline-block');
	$('.page .page-msg .rows').css({'font-size': '16px', 'background':'none'});
	var start={elem:"#start",format:"YYYY-MM-DD",max:'2028-05-01',min:"2020-10-01",istime:false,istoday:true,choose:function(datas){end.min=datas;end.start=datas}};
	var end={elem:"#end",format:"YYYY-MM-DD",max:'2028-05-01',min:"2020-10-01",istime:false,istoday:true,choose:function(datas){start.max=datas}};
	laydate(start);laydate(end);
	$('#status').val('<?php echo I('status'); ?>');
	$('#type').val('<?php echo I('type'); ?>');
	//$('#gdtype').val('<?php echo I('gdtype'); ?>');
	function sst(pid){
		$('#station').append(st);
		$('#station option').each(function(){
			if($(this).val() != '' && $(this).attr('pid') != pid) $(this).remove();
		});
		$('#station').val('');
	}
	$('#place').val('<?php echo I('place'); ?>').change(function(){
		sst($(this).val());
	});
	sst('<?php echo I('place'); ?>');
	$('#station').val('<?php echo I('station'); ?>');
	$('#office').val('<?php echo I('office'); ?>');
	$('#fs2').val('<?php echo I('type'); ?>');
	$('#matching').val('<?php echo I('matching'); ?>');
	/*$('.pc').click(function(){
		$(this).unbind('click');
		$.post('<?php echo U('/likes/pc'); ?>', {lid:$(this).data('lid')}, function(d){
			location.reload();
		});
	});*/
	if('<?php echo \think\Session::get('user.type'); ?>' == 2){
		$('#place').hide();
		sst('<?php echo \think\Session::get('user.office'); ?>');
	}
	if('<?php echo \think\Session::get('user.type'); ?>' == 3){
		$('#place').hide();
		$('#station').hide();
	}
	<?php if(\think\Session::get('user.uid') == 0): ?>$('.show-image').mouseover(function(){
		if(!$(this).find('button').length) $(this).append('<button>排除</button>');
		$(this).find('button').show();
	}).mouseout(function(){
		$(this).find('button').hide();
	}).on("click", "button", function(){
		var p = $(this).parent(), pid = p.data('pid'), type = $(this).parent().hasClass('m') ? 1 : 0;
		$.post('<?php echo U('/likes/pc'); ?>', {pid:pid, type:type}, function(d){
			ptr = p.parents('tr');
			if(type == 1){
				ptr.remove();
				return false;
			}
			imgs = p.parent('.imgs').find('.show-image').length;
			if(imgs == 1){
				ptr.remove();
				return false;
			}
			p.remove();
		});
	});<?php endif; ?>
});
</script>

</body>
</html>