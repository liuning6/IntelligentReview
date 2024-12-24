<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:61:"/var/www/pro-audit-ct/application/view/statistics/repeat.html";i:1711692671;s:57:"/var/www/pro-audit-ct/application/view/common/header.html";i:1732680285;s:57:"/var/www/pro-audit-ct/application/view/common/footer.html";i:1614540793;}*/ ?>
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
                        <li><a href="<?php echo U('/statistics/repeat');; ?>">重复统计</a></li>
                        <li id="link_stat" style="display:none"><a href="<?php echo U('/statistics/opreports');; ?>">图片统计</a></li>
                    </ul>
                </li>
                <li class="drop-down">
                    <a class="h_orders" href="<?php echo U('/orders/index', ['type'=>0]);; ?>">工单</a> <i></i>
                    <ul class="drop-down-content">
                        <li><a href="<?php echo U('/orders/index', ['type'=>0]);; ?>">工单列表</a></li>
                        <!--                        <li><a href="<?php echo U('/orders/index', ['cs'=>1, 'type'=>1]);; ?>">重复单</a></li>-->
                        <li><a href="<?php echo U('/repeated/index');; ?>">重复单</a></li>
                        <li><a href="<?php echo U('/orders/index', ['status'=>2, 'type'=>1]);; ?>">不合格单</a></li>
    			<?php if(\think\Session::get('user.gtype') == 0 ||\think\Session::get('user.gtype') == 2): ?>
			<!--<p>当前用户的 gtype 值为: <?php echo \think\Session::get('user.gtype'); ?></p>-->
                        <li class="set<?php echo \think\Session::get('user.type')==0 || \think\Session::get('user.type')==2 ||\think\Session::get('user.type')==3?0 : 1; ?>"><a href="<?php echo U('/smartlock/index');; ?>">智能锁</a></li>
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
                    <p>当前平台: <?php echo \think\Session::get('user.gtype')?'泵房养护' : '水箱清洗'; ?></p>
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
	<div class="list-tool gapBox">
		<button class="search" onclick="location.reload();">全部</button>
		<!--<button class="search" onclick="window.location.href='<?php echo U('/statistics/repeat'); ?>'">维保单位</button>-->
		<form action="<?php echo U('/statistics/repeat'); ?>" style="display:inline;">
			<!--<select id="type" name="type" class="form-control type" style="width:140px;display:inline;"><option value="">按管理所/站</option><option value="1">按维保单位</option></select>-->
			<select id="place" name="place" class="form-control type" style="width:140px;display:inline;"><option value="">管理所</option></select>
			<select id="station" name="station" class="form-control type" style="margin:0 2px;width:90px;display:inline;" disabled><option value="">管理站</option></select>
			<input style="margin-left:20px;" value="2020-09-01" class="tiem_put" id="start" name="start" placeholder="开始时间" readonly />
			<span>-</span>
			<input style="margin:0" value="<?php echo date('Y-m-d'); ?>" class="tiem_put" id="end" name="end" placeholder="结束时间" readonly />
			<select id="matching" name="matching" style="width:80px;display:none;"><option value="">相似度</option><option value="90">≥90</option><option value="91">≥91</option><option value="92">≥92</option><option value="93">≥93</option><option value="94">≥94</option><option value="95">≥95</option><option value="96">≥96</option><option value="97">≥97</option><option value="98">≥98</option><option value="99">≥99</option></select>
			<button class="search" type="submit">查询</button>
		</form>
		<button class="search" id="dc">导出</button>
		<button class="refresh" onclick="cx();">刷新</button>
		<!--<button class="export" onclick="window.open('<?php echo U('/orders/out', $_GET); ?>');">导出</button>-->
	</div>
    <div id="tjs"></div>
<script src="/static/js/plugins/layer/layer.min.js"></script>
<script src="/static/js/plugins/layer/laydate/laydate.js"></script>
<script src="/static/js/plugins/echarts.min.js"></script>
<script>

	var placeKeys = <?php echo !empty($placeKeys)?json_encode($placeKeys, 320) : '[]'; ?>;
	var tmp = <?php echo !empty($tmp)?json_encode($tmp, 320) : '[]'; ?>, fs2 = <?php echo !empty($fs2)?json_encode($fs2, 320) : '[]'; ?>, st = '', r0 = '<div class="manage-block ##class##">\
        <h4>##0##</h4>\
        <div class="manage-cent">\
            <div class="leftBox">\
                <div class="rLeft-cent">\
                    <p class="rLeft-title">重复情况</p>\
                    <div class="rLeft-num">\
                        <p><span>##1##</span>工单总量</p>\
                        <p><span>##2##</span>重复工单</p>\
                    </div>\
                    <div class="rLeft-chart pie"></div>\
                </div>\
            </div>\
            <div class="rightBox">\
                <div class="rRight-cent">\
                    <p class="rLeft-title">分类情况</p>\
                    <ul></ul>\
                </div>\
            </div>\
        </div>\
    </div>', r1 = '<li>\
                            <div class="child-order">\
                                <p class="child-title">##0##</p>\
                                <div class="order-show">\
                                    <span class="oNum">##1##</span>\
                                    <div class="wrapper">\
                                        <div class="procent">##2##</div>\
                                        <div class="progress"><span style="width: ##3##;"></span></div>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>';
	function bf(a, b, c, d){
		if(b == 0){
			if(!d) return '-';
			return 0;
		}
		return ((a / b) * 100).toFixed(c) + '%';
	}
	function b0(num){
		if(num > 9) return num;
		return '0'+num;
	}
	function dd(y, m){
		if(m == 1) return [y - 1, 12];
		return [y, m - 1];
	}
	function charts(a, b, c){
		return echarts.init($('.'+ a +' .pie')[0]).setOption({
            title: {text: bf(c, b, 2), left: "center", top: 'center', textStyle: {color:'#7887A1', fontSize: 16, align: "center"}}, 
            series: [{name: '访问来源', type: 'pie', radius: ['80%', '100%'], hoverAnimation: false, label: {normal: {show: false, position: 'center', fontSize: 16, formatter: '{d}%'}}, data: [{value: c, name: '搜索引擎', itemStyle:{color: '#F46A6A'}}, {value: b - c, name: '直接访问', itemStyle:{color: '#656FDA'}}]}]
        });
	}
	
	function ys(){
		$('.procent').each(function(){
			var h = parseFloat($(this).text()), y = '';
			if(h <= 1) y = 'green';
			if(h > 5) y = 'gold';
			if(h > 20) y = 'red';
			$(this).parent().parent().parent().find('p.child-title').addClass(y);
		});
	}
	
	function cx(){
		$('#tjs').html('');
		var load = layer.load(1, {shade: [0.5, '#000']});
		$.post('<?php echo U('/statistics/repeat'); ?>', {type:$('#type').val(), place:$('#place').val(), station:$('#station').val(), start:$('#start').val(), end:$('#end').val(), matching:$('#matching').val()}, function(d){
			layer.close(load);

			$.each(placeKeys, function(pix, i){
				v = d[i];
			// $.each(d, function(i, v){
				var c = 'c' + i, sh = r0.replace('##class##', c).replace('##1##', v[1]).replace('##2##', v[3]).replace(new RegExp('##0##', "gm"), v[0]);//.replace(new RegExp('#####', "gm"), _t.data('id'));
				$('#tjs').append(sh);
				charts(c, v[1], v[3]);
				$.each(fs2, function(i2, v2){
					ve = v[2][i2] || 0;
					var sh2 = r1.replace('##0##', v2).replace('##1##', ve).replace('##2##', bf(ve, v[1], 2)).replace('##3##', bf(ve, v[1], 2, 1));
					$('.'+ c +' ul').append(sh2);
				});
			});
			ys();
		});
	}
	
	$(function(){
		$('.h_statistics').addClass('active');
		var myDate = new Date, year = myDate.getFullYear(), mon = myDate.getMonth() + 1, date = myDate.getDate();
		/*if(date <= 25){
			ym = dd(year, mon);	year = ym[0];	mon = ym[1];
		}*/
		if(date > 25) date = 25;
		ym = dd(year, mon);	year0 = ym[0];	mon0 = ym[1];
		$('#start').val(year0+'-'+b0(mon0)+'-26');
		$('#end').val(year+'-'+b0(mon)+'-'+b0(date));
		var start={elem:"#start",format:"YYYY-MM-DD",max:'<?php echo date('Y-m-d'); ?>',min:"2020-09-01",istime:false,istoday:true,choose:function(datas){end.min=datas;end.start=datas}};
		var end={elem:"#end",format:"YYYY-MM-DD",max:'<?php echo date('Y-m-d'); ?>',min:"2020-09-01",istime:false,istoday:true,choose:function(datas){start.max=datas}};
		laydate(start);laydate(end);
		function sst(pid){
			if(pid == '') $('#station').attr('disabled', 'disabled'); else $('#station').removeAttr('disabled');
			$('#station').append(st);
			$('#station option').each(function(){
				if($(this).val() != '' && $(this).attr('pid') != pid) $(this).remove();
			});
			$('#station').val('');
		}
		$.each(tmp, function(i, v){
			var h = '';
			$.each(v, function(i2, v2){
				if(i == 'station') st += '<option pid="'+ v2[2] +'" value="'+ v2[0] +'">'+ v2[1] +'</option>'; else h += '<option value="'+ i2 +'">'+ v2 +'</option>';
			});
			$('#'+i).append(h);
		});
		if('<?php echo \think\Session::get('user.type'); ?>' == 2){
			$('#place').hide();
			sst('<?php echo \think\Session::get('user.office'); ?>');
		}
		if('<?php echo \think\Session::get('user.type'); ?>' == 3){
			$('#place').hide();
			$('#station').hide();
		}
		
		$('#place').val('<?php echo I('place'); ?>').change(function(){
			sst($(this).val());
		});
		$('#type').val('<?php echo I('type'); ?>').change(function(){
			if($(this).val() == '') $('#place, #station').removeAttr('disabled'); else $('#place, #station').attr('disabled', 'disabled');
		});
		
		$('form').submit(function(){
			cx();
			return false;
		});
		cx();
		$('#dc').click(function(){
			window.open('<?php echo U('/statistics/out'); ?>?dc=1&type='+ $('#type').val() +'&place='+ $('#place').val() +'&station='+ $('#station').val() +'&start='+ $('#start').val() +'&end='+ $('#end').val() +'&matching='+ $('#matching').val());
		});
	});
</script>

</body>
</html>