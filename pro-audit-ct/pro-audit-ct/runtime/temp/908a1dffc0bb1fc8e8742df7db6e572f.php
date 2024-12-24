<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:56:"/var/www/pro-audit-ct/application/view/appeal/index.html";i:1733899423;s:57:"/var/www/pro-audit-ct/application/view/common/header.html";i:1732680285;s:57:"/var/www/pro-audit-ct/application/view/common/footer.html";i:1614540793;}*/ ?>
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
<style>
#table thead td{font-size:14px;font-weight:bold;}
#table tbody td{font-size:14px;}
#table tbody td a{color: #C0C9E1;}
-#table .img-type p a{background-color: rgba(236, 177, 75, 0.2);color: #ECB14B;}
#table .img-type p{margin:3px 5px;}
#table .img-type p.type-bg{background-color: rgba(51, 190, 139, 0.2);}
#table .img-type p.type-bg a{color: #34C38F;}
#table .img-type p.type-br{background-color: rgba(236, 177, 75, 0.2);}
#table .img-type p.type-br a{color: #ECB14B;}
.page{padding-bottom:20px;}
#table .zd{display:inline-block;background-color: #3251ff;padding:2px 5px;margin-right:5px;cursor:pointer;}
#table button {padding:0 10px;height: 28px; background: #3251FF; border-radius: 2px; font-size: 12px; color: #EEF4FF; cursor: pointer; border: 1px solid #434C68;}
button.gray{background: #9ca0b5 !important;cursor:default !important;}
.pass-wait{color:#f5ee07;}
.layui-layer-btn{margin-right:30px;}
.contentBox{height:calc(100vh - 93px) !important;}
.dc{background-color: #3a7671;}
</style>
        <div class="contentBox">
            <div class="list-tool">
					<button class="search" onclick="window.location.href='<?php echo U('/appeal/index', ['appeal'=>'all']); ?>';">全部</button>
					<form action="<?php echo U('/appeal'); ?>" style="display:inline;">
						<select id="appeal" name="appeal" style="margin:0 2px;width:80px;"></select>
						<select id="place" name="place" class="form-control type" style="margin:0 2px;width:140px;display:inline;"><option value="">管理所</option></select>
						<select id="station" name="station" class="form-control type" style="margin:0 2px;width:90px;display:inline;" disabled><option value="">管理站</option></select>
						<select id="office" name="office" class="form-control type" style="margin:0 2px;width:90px;display:inline;"><option value="">维保单位</option></select>
						<select id="cs" name="cs" style="margin:0 2px;width:90px;"><option value="">所有工单</option><option value="1">不合格</option><option value="2">重复单</option></select>
						<select id="appealType" name="appealType" style="margin:0 2px;width:90px;" value="2">
							<option value="1">人脸申诉</option>
							<option value="2">其它申诉</option>
						</select>
						<input style="margin:0 2px;" value="<?php echo I('addr'); ?>" class="adress" type="text" placeholder="工单编号 / 地址" name="addr" />

						<input style="margin:0" value="<?php echo $start; ?>" class="tiem_put" id="start" name="start" placeholder="开始日期" readonly />
						<span>-</span>
						<input style="margin:0" value="<?php echo $end; ?>" class="tiem_put" id="end" name="end" placeholder="结束日期" readonly />

						<button style="margin:0 2px;" class="search" type="submit">搜索</button>
					</form>
					<button style="margin:0 2px;" class="refresh" onclick="window.location.reload();">刷新</button>
                </div>
            <div class="tab-space"><?php $status = ['<div class="tab-state stay">待审核</div>', '<div class="tab-state">通过</div>', '<div class="tab-state pass-no">未通过</div>']; ?>
                <!--<p class="title-page">共<span>41356</span>条记录&nbsp;&nbsp;&nbsp;&nbsp;1655页</p>-->
                <table class="list-tab" id="table">
                    <thead>
                        <tr>
                            <td width="30" class="audit">选择</td>
                            <td width="130">工单编号</td>
                            <td width="100">日期</td>
                            <td width="120">管理所</td>
                            <td width="70">管理站</td>
                            <td width="70">维保单位</td>
                            <td width="230">地址</td>
                            <td width="130">申诉理由</td>
                            <td width="80">AI审核结果</td>
                            <td width="100">
								<?php if(I('appealType') == 1)  echo '人脸';?>申诉状态
							</td>
                            <td width="108" style="text-align:center">操作</td>
                        </tr>
                    </thead>
                    <tbody>
					<?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): if( count($data)==0 ) : echo "" ;else: foreach($data as $key=>$val): ?>
                        <tr class="ds" data='<?php echo json_encode($val, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);?>' data-appeal="<?php echo $data['appeal']; ?>" data-faceappeal="<?php echo $data['faceappeal']; ?>">
                            <td class="audit"><input name="ids[]" type="checkbox" value="<?php echo $val['id']; ?>" disabled data-yid="<?php echo $val['yid']; ?>"/></td>
                            <td><a href="<?php echo U('/orders/index', ['type'=>1, 'addr'=>$val['gid'], 'start'=>substr($val['etime'], 0, 10)]); ?>" target="_blank"><?php echo $val['gid']; ?></a></td>
                            <td><?php echo substr($val['etime'], 0, 10); ?></td>
                            <td><?php echo $tmp['place'][$val['place']]; ?></td>
                            <td><?php echo $tmp['station'][$val['station']][1]; ?></td>
                            <td><?php echo $tmp['office'][$val['office']]; ?></td>
                            <td class="ddd" title="【<?php echo $val['village']; ?>】<?php echo $val['addr']; ?>"><?php echo mb_cut('【'. $val['village'] .'】' . $val['addr'], 50); ?></td>
                            <td class="ddd" title="<?php echo $val['station_msg']; ?>"><?php echo mb_cut($val['station_msg'], 40); ?></td>
                            <td>
								<?php if($val['cs']): ?>
								<div class="tab-state pass-no" style="margin-top:5px;">重复</div>
								<?php else: if(I('appealType') == 1): ?>
										<?php echo $status[$val['facestatus']]; else: ?>
										<?php echo $status[$val['status']]; endif; endif; ?>
							</td>
                            <td>
								<?php echo $appeals[$val[I('appealType')==1?'faceappeal' : 'appeal']]; ?>
							</td>
                            <td style="text-align:center;"><button class="search c1 gray audit"><?php echo $t1; ?></button><button class="c2 search" style="margin-left:10px;">详情</button></td>
                        </tr>
					<?php endforeach; endif; else: echo "" ;endif; ?>
						<tr class="audit">
							<td colspan="2"><button class="search" id="checkall">全选</button><button class="search" style="margin-left:20px;" id="checkall2">全不选</button></td>
							<td>已选择 <b id="x">0</b> 条</td>
							<td colspan="7"><button class="search gray" id="all">批量<?php echo $t1; ?></button></td>
						</tr>
                    </tbody>
                </table>
            </div>
			<?php echo $page; ?>
        </div>
<div id="ss" style="display:none;">
	<div style="margin:30px;">
		<form enctype="multipart/form-data" method="post" action="<?php echo U('/appeal/submit'); ?>">
		<div style="margin:10px 0px;border: 1px solid red;padding:5px;width:500px;max-height:108px;overflow-y:scroll;font-size:13px;" class="y"></div>
		<div style="border: 1px solid #ccc; padding: 10px; background-color: #f9f9f9; margin: 10px 0; border-radius: 4px;">
			<strong>不合格原因：</strong> <span id="appealReason"></span>
		</div>
		<div style="margin-top:10px">
			<p><span style="color:red;">*</span><?php echo $t2; ?>：</p>
			<textarea name="txt" placeholder="请输入<?php echo $t2; ?>，最多200字..." style="border: 1px solid #3C5DE8;width:500px;height:80px;margin-top:5px;border-radius: 4px;padding: 2px 5px;"></textarea>
		</div>
		<div class="login-row" style="margin-top:20px">
			<p>上传凭证：</p>
			<input name="file" placeholder="请上传凭证" type="file" style="margin-top:5px;width:500px;padding:0 5px;" accept=".jpg,.png,.zip,.rar" onchange="ck(this)"><i>上传文件只能是jpg、png、zip、rar格式，50M以内</i>
		</div><div style="display:none" class="ck"></div><input class="s" type="hidden" name="s" value="" />
		<!-- 照片 -->
		<div id="photoListTpl" style="display: flex;flex-wrap: wrap;margin-top: 20px;margin-bottom: 30px;">
		</div>
		<div id="photoListTpl2" style="display: flex;flex-wrap: wrap;margin-top: 20px;margin-bottom: 30px;">
		</div>
		<input name="appealType" id="appealTypeIpt" value="" type="hidden">
		</form>
	</div>
</div>
<script src="/static/js/jquery.form.js"></script>
<script src="/static/js/plugins/layer/laydate/laydate.js"></script>
<script src="/static/js/plugins/layer/layer.min.js"></script>
<script>
var tmp = <?php echo !empty($tmp)?json_encode($tmp, 320) : '[]'; ?>, param = <?php echo json_encode(input('param.'), 320); ?>, st = '', appeal = <?php echo $appeal; ?>, ut = <?php echo $ut; ?>, k = <?php echo $k; ?>;
function ck(e){
	f = $(e).val();
	if(f == '') return;
	f = f.substr(-3).toLowerCase();//console.log(f);
	if($.inArray(f, ['png', 'jpg', 'zip', 'rar']) < 0){layer.msg('上传文件格式错误，请重新选择上传文件');$(e).val('');return false;}
	if(e.files[0].size > 50*1024*1024){layer.msg('上传文件超过50M，请重新选择文件');$(e).val('');return false;}
}
$(function(){
	$('.h_appeal').addClass('active');
	$.each(tmp, function(i, v){
		var h = '';
		$.each(v, function(i2, v2){
			if(i == 'station') st += '<option pid="'+ v2[2] +'" value="'+ v2[0] +'">'+ v2[1] +'</option>'; else h += '<option value="'+ i2 +'">'+ v2 +'</option>';
		});
		$('#'+i).append(h);
	});
	
	$('.page .current').css({'background': '#3251FF'});
	//$('.page .page-li.page-num.current').css({'padding': '0 10px'});
	$('.page .page-li').css('display', 'inline-block');
	$('.page .page-li input').css({'width':'50px', 'background':'#2B4563', 'border': '1px solid #7591BE', 'border-radius': '6px', 'display': 'inline-block', 'color': 'rgba(255,255,255,0.65)', 'margin-right': '5px'});
	$('.page .page-msg, .page ul').css('display', 'inline-block');
	$('.page .page-msg .rows').css({'font-size': '16px', 'background':'none'});
	var start={elem:"#start",format:"YYYY-MM-DD",max:'2028-05-01',min:"2020-10-01",istime:false,istoday:true,choose:function(datas){end.min=datas;end.start=datas}};
	var end={elem:"#end",format:"YYYY-MM-DD",max:'2028-05-01',min:"2020-10-01",istime:false,istoday:true,choose:function(datas){start.max=datas}};
	laydate(start);laydate(end);
	$('#cs').val('<?php echo I('cs'); ?>');
	$('#office').val('<?php echo I('office'); ?>');
	let appealType = '<?php echo I('appealType'); ?>';
	$('#appealType').val(appealType ? appealType : 2);
	function dhk(){
		console.log("申诉弹窗");
		$('#ss .ck, #ss .y').html('');$('#ss .s').val('');
		if(ut < 3){
			$('input[type="checkbox"]:checked').each(function(){
				var d = JSON.parse($(this).parent().parent().attr('data'));
				h = '工单编号：<a href="'+ $(this).parent().parent().find('a').attr('href') +'" target="_blank">' + d.gid + '</a><br />';
				h += '申诉理由：' + d.station_msg + '<br />';
				if(d.station_file != '') h += '上传凭证：<a href="/source/appeal/'+ d.station_file +'" target="_blabk">查看凭证</a><br />';
				h += '申诉时间：'+d.station_time+'<br />';
				h += '申诉人员：'+d.station_uname+'<br />';
				if(d.place_msg){
					h += '管理所审核意见：' + d.place_msg + '<br />';
					if(d.place_file != '') h += '管理所上传凭证：<a href="/source/appeal/'+ d.place_file +'" target="_blabk">查看凭证</a><br />';
					h += '管理所审核时间：'+d.place_time+'<br />';
					h += '管理所审核人员：'+d.place_uname+'<br />';
				}
				h += '<hr>';
				$('#ss .y').append(h);
			});
			$('#ss .y hr:last').remove();
		}
		layer.open({type: 1, title:'<?php echo $t1; ?>', area:['570px', ut == 3 ? '570px' : '508px'], btn: ['提交', '拒绝', '不通过', '取消'], content: $("#ss"), 
			yes:function(index, layero){
				var form = $('#layui-layer' + index + ' form'), txt = $('#layui-layer' + index + ' form textarea');
				if(txt.val() == ''){layer.msg('请输入<?php echo $t2; ?>');txt.focus();return false;}
				$('#layui-layer' + index + ' form .ck').html('').append($('input[type="checkbox"]:checked').clone());
				var load = layer.msg('正在提交，请稍后...', {icon: 16, shade: 0.5, time: false});//layer.load(1, {shade: [0.5, '#000'], content: '正在提交，请稍后...'});
				form.ajaxSubmit({type:'post', success:function(data){
						console.log("data is ",data);
						
						if(data[0] == 1){
							layer.closeAll();
							layer.msg(data[1], {icon: 1, shade: 0.5, time: 3000}, function(){location.reload();});
						}else{
							layer.msg(data[1], {icon: 2, shade: 0.5, time: 3000});
							return false;
						}
					}, error:function(){layer.close(load);}
				});
			}, btn3:function(index, layero){$('#ss .s').val('1');$('.layui-layer-btn0').click();return false;}
		});
		$('.layui-layer-btn1').hide();
		if(ut == 3) $('.layui-layer-btn2').hide();
		$('.layui-layer textarea').focus();
	}
	function call(){
		// 列表勾选一次就会触发一次
		$('#all').unbind().addClass('gray');
		
		// 已选择多少条
		x = $('input[type="checkbox"]:checked').length;
		$('#x').text(x);

		// jry20240525
		// 获取选中工单的照片
		let ids = [];
		let yids = [];
		$('input[type="checkbox"]:checked').each(function() {  
			ids.push($(this).val());  
    		yids.push($(this).data('yid'));  
		});
		console.log('yids', yids);
		$('#appealTypeIpt').val($('#appealType').val());

		// 人脸申诉才获取照片
		if ($('#appealType').val() == 1) {
			$.ajax({
				url: '<?php echo U("appeal/getPhotos"); ?>',
				type: 'POST',
				data: {
					yids: yids,
					ids: ids,
					ut: ut
				},
				headers: {},
				success: res=>{
					let d = JSON.parse(res);
					console.log("d is",d)
					
					console.log(d['data']['0']['list']['0']['cause']);
					// 获取 appealReason 内容
					let cause = d['data']['0']['list']['0']['cause'];
					let outcause='';
					// 判断是否包含特定的字符串
					if (cause.includes('站点不匹配')) {
						outcause="识别人员无对应岗位";
					} 
					else if(cause.includes('No face is found')) {
						outcause="未识别";
					}
					else{
						outcause="";
					}

					$('#appealReason').text(outcause);


					let photoListTpl = ''
					d.data.forEach((ele, idx) => {
						photoListTpl += '<div><h3>' + ele.yid
						if (ut == 3) {
							photoListTpl += '(请在以下选择2项填写人员编号)'
						}
						photoListTpl += '</h3></div>'
						if (ele.list.length == 0) {
							photoListTpl += '<br><h3 style="color: red;text-align: center;">暂无照片</h3>'
						}
						photoListTpl += '<div class="photoListTplGroup" style="width: 100%;display: flex;flex-wrap: wrap;margin-top: 20px;margin-bottom: 30px;">';
						ele.list.forEach((element, index) => {
							let usercode = '';
							let ipt = '';
							if (element.status == 1) {
								usercode = element.usercode ? element.usercode : '';
								ipt = '<input disabled value="' + usercode + '" name="pics[' + element.id + '][usercode]" type="text" placeholder="人员编号" style="width: 78px;border: 1px solid #3C5DE8;border-radius: 4px;padding: 2px 5px;">';
							} else {
								ipt = '<input value="' + usercode + '" name="pics[' + element.id + '][usercode]" type="text" placeholder="人员编号" style="width: 78px;border: 1px solid #3C5DE8;border-radius: 4px;padding: 2px 5px;">';
							}
							let url = '/source/pics/'  + element.data_dir + '/' + element.oid + '/' + element.filename + '.' + element.extension;
							photoListTpl += '<div style="width: 90px;margin-right: 10px">'
								+ '<div style="margin-bottom: 5px;">'
									+ ipt
									+ '<input value="' + element.order_yid + '" name="pics[' + element.id + '][order_yid]" type="hidden">'
									+ '<input value="' + element.id + '" name="pics[' + element.id + '][picid]" type="hidden">'
									+ '<input value="' + url + '" name="pics[' + element.id + '][url]" type="hidden">'
									+ '<input value="' + element.ycode + '" name="pics[' + element.id + '][ycode]" type="hidden">'
									+ '<input value="' + element.oid + '" name="pics[' + element.id + '][oid]" type="hidden">'
								+ '</div>'
								+ '<a style="width: 90px;height: 110px;border: 1px solid #ddd;display: block;" target="_blabk" href="'+ url +'">'
									+ '<img width="90" height="110" src="'+ url + '" alt="">'
								+ '</a>'
							+ '</div>'
						});
						photoListTpl += '</div>'
					});
					$('#photoListTpl').html(photoListTpl)
				},
				error: err=>{
					layer.msg(err, {icon: 7})       //其他错误情况
					reject(err)
				}
			})
		}

		if(x) $('#all').removeClass('gray').click(function(){dhk();});
		$('.dc').removeClass('dc');
		$('input[type="checkbox"]:checked').parents('.ds').addClass('dc');
	}
	$('.ds').each(function(){
		var _t = $(this), d = JSON.parse(_t.attr('data')), c = _t.find('input'), c1 = _t.find('.c1'), c2 = _t.find('.c2');
		if ($('#appealType').val() == 2) {
			dk = parseInt(d.appeal)
		} else if ($('#appealType').val() == 1) {
			dk = parseInt(d.faceappeal)
		}
		if(dk == k){
			c.removeAttr('disabled');
			c1.removeClass('gray');
		}
		//console.log(d);
	});
	
	$('.ds button').click(function(){
		// 点击审核按钮执行
		if($(this).hasClass('gray')) return false;
		$('input[type="checkbox"]:not(:disabled)').prop('checked', false);
		$(this).parent().parent().find('input:not(:disabled)').prop('checked', true);
		call();
		if($(this).hasClass('c1')){
			// 申诉弹窗
			dhk();
		}else{
			var d = JSON.parse($(this).parent().parent().attr('data'));
			h = '<div style="margin:30px;">';
			h += '工单编号：<a href="'+ $(this).parent().parent().find('a').attr('href') +'" target="_blank">' + d.gid + '</a><br />';
			
			h += '申诉理由：' + d.station_msg + '<br />';
			if(d.station_file != '') h += '上传凭证：<a href="/source/appeal/'+ d.station_file +'" target="_blabk">查看凭证</a><br />';
			h += '申诉时间：'+d.station_time+'<br />';
			h += '申诉人员：'+d.station_uname;
			if(d.place_msg){
				h += '<br /><hr /><br />管理所审核意见：' + d.place_msg + '<br />';
				if(d.place_file != '') h += '管理所上传凭证：<a href="/source/appeal/'+ d.place_file +'" target="_blabk">查看凭证</a><br />';
				h += '管理所审核时间：'+d.place_time+'<br />';
				h += '管理所审核人员：'+d.place_uname;
			}
			if(d.branch_msg){
				h += '<br /><hr /><br />终审意见：' + d.branch_msg + '<br />';
				if(d.branch_file != '') h += '终审凭证：<a href="/source/appeal/'+ d.branch_file +'" target="_blabk">查看凭证</a><br />';
				h += '终审时间：'+d.branch_time+'<br />';
				h += '终审人员：'+d.branch_uname;
			}
			h += '</div>';
			layer.open({type: 1, title:'详情', area:['570px', '508px'], btn: ['确定', '关闭'], content: h});
		}
	});
	$('.ds input[type="checkbox"]:not(:disabled)').click(function(){call();});
	$('#checkall').click(function(){
		$('input[type="checkbox"]:not(:disabled)').prop('checked', true);call();
	});
	$('#checkall2').click(function(){
		$('input[type="checkbox"]:not(:disabled)').prop('checked', false);call();
	});
<?php if($ut == 3): ?>
	$('#place, #station, #ss .y').remove();
	if(!appeal) $('#table .c2').remove();
<?php else: ?>
	function sst(pid){
		if(pid == '') $('#station').attr('disabled', 'disabled'); else $('#station').removeAttr('disabled');
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
	<?php if($ut == 2): ?>
	$('#place').hide();
	sst('<?php echo \think\Session::get('user.office'); ?>');
	<?php endif; if(\think\Session::get('user.audit') == 0): ?>
	$('.audit').remove();
	<?php endif; endif; ?>
	$('#appeal').val(appeal);
});
</script>

</body>
</html>