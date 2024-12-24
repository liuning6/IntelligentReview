<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:57:"/var/www/pro-audit-ct/application/view/orders/detail.html";i:1727252269;s:57:"/var/www/pro-audit-ct/application/view/common/header.html";i:1732680285;s:57:"/var/www/pro-audit-ct/application/view/common/footer.html";i:1614540793;}*/ ?>
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
#addr{color:#fff;font-weight:bold;display:inline-block;font-size:16px;}
.imgs .show-image{margin:10px; width: 320px; height: 420px;} .img-text p{text-align:center;font-size:18px;font-weight:bold;}.img-text{width:320px;padding: 10px 0 7px;}.img-text a{color:#fff;}
.img-text.s1{background-color: rgba(84, 216, 51, 0.8);}
.img-text.s2{background-color: rgba(237, 50, 7, 0.8);}
</style>
        <div class="contentBox">
			<div class="list-tool">
				<button class="search" onclick="location.href='<?php echo \think\Session::get('orderindex'); ?>'" style="width:80px;margin:0 20px;"><<工单列表</button>
				<span id="addr" title="<?php echo $order['addr']; ?>"><?php echo mb_cut($order['addr'], 30); ?> (<?php echo $order['gid']; ?>)</span>
				<button class="search" onclick="window.location.href='<?php echo U('/orders/detail', ['id'=>$id]); ?>';">全部</button>
				<form action="<?php echo U('/orders/detail', ['id'=>$id]); ?>" style="display:inline;">
					<select name="status" style="width:80px;" id="status">
						<option value="">状态</option>
						<option value="0">待审核</option>
						<option value="1">合格</option>
						<option value="2">不合格</option>
					</select>
					<select name="type" style="width:90px;" id="type"><option value="">图片分类</option></select>

					<button class="search" type="submit">搜索</button>
				</form>
				<button class="refresh" onclick="window.location.reload();">刷新</button>
            </div>
			
			
            <div class="tab-space">
                <table class="list-tab imgShow">
                    <tbody>
                        <tr>
                            <div class="imgs" style="margin-bottom:20px;">
							<?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): if( count($data)==0 ) : echo "" ;else: foreach($data as $key=>$val): ?>
							    
								<div class="show-image">
									<a target="_blank" href="/source/pics/<?php echo date('Ym', strtotime($val['etime'])); ?>/<?php echo $val['oid']; ?>/<?php echo $val['filename']; ?>.<?php echo $val['extension']; ?>"><img src="/source/pics/<?php echo date('Ym', strtotime($val['etime'])); ?>/<?php echo $val['oid']; ?>/<?php echo $val['filename']; ?>.<?php echo $val['extension']; ?>" alt="<?php echo !empty($val['status'])?$fs3[$val['code']] : '待审核'; ?>" title="<?php echo $val['id']; ?> <?php echo $val['matching']; ?> <?php echo $val['ycode']; ?> <?php echo $val['code']; ?>" /></a>
									<div class="img-text s<?php echo $val['status']; ?>">
										<?php if($val['ycode'] == 45): if($val['username']): if($val['cause']): ?>
                                                                                        <p style="font-size: 10px;"><?php echo $val['usercode'] . '-' . $val['username'] . '-' . $val['userrole']. '-' . $val['cause'] . ' <a target="_blank" code="'. $val['code'] .'">&nbsp;</a>'; ?></p>
                                                                                        <?php else: ?>
                                                                                        <p style="font-size: 14px;"><?php echo $val['usercode'] . '-' . $val['username'] . '-' . $val['userrole'] . ' <a target="_blank" code="'. $val['code'] .'">&nbsp;</a>'; ?></p>
                                                                                        <?php endif; else: ?>
										    <p style="font-size: 14px;"><?php echo $ycodes[$val['ycode']] . '-' . $val['cause'] . ' <a target="_blank" code="'. $val['code'] .'">&nbsp;</a>'; ?></p>
                                                                                    <?php endif; else: ?>
										<p><?php echo !empty($val['status'])?$ycodes[$val['ycode']] . ' <a target="_blank" code="'. $val['code'] .'">&nbsp;</a>' : '待审核'; ?></p>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; endif; else: echo "" ;endif; ?>
							</div>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
<script>
var fs2 = <?php echo !empty($fs2)?json_encode($fs2, 320) : '[]'; ?>;
var fs3 = <?php echo !empty($fs3)?json_encode($fs3, 320) : '[]'; ?>;
console.log(fs2);
console.log(fs3);
$(function(){
	
	var h = '';
	$.each(fs2, function(i, v){
		h += '<option value="'+ i +'">'+ v +'</option>';
	});
	h += '<option value="99">供水服务代表</option>';
	h += '<option value="98">固定采样箱内部</option>';
	h += '<option value="0">其它</option>';
	$('#type').append(h);
	$('#type').val('<?php echo I('type'); ?>');
	$('#status').val('<?php echo I('status'); ?>');
<?php if(\think\Session::get('user.uid') < 3): ?>	$('a[code]').each(function(){
		code = parseInt($(this).attr('code'));
		$(this).attr('href', '<?php echo U('/tools/mt');; ?>?type='+ ($.inArray(code, [4000, 4101, 4201, 4301]) > -1 ? 2 : 1) +'&file=' + $(this).parents('.show-image').find('img').attr('src'));
	});
<?php endif; ?>
});
</script>

</body>
</html>
