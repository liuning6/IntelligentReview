<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:59:"/var/www/pro-audit-ct/application/view/smartlock/index.html";i:1733723979;s:57:"/var/www/pro-audit-ct/application/view/common/header.html";i:1732680285;s:57:"/var/www/pro-audit-ct/application/view/common/footer.html";i:1614540793;}*/ ?>
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
    #table .img-type p.type-bg{background-color: rgba(51, 190, 139, 0.3);}
    #table .img-type p.type-bb{background-color: rgba(240, 240, 12, 0.5);}
    #table .img-type p.type-bg a{color: #34C38F;}
    #table .img-type p.type-br{background-color: rgba(255, 0, 0, 0.3);}
    #table .img-type p.type-br a{color: #e32929;}
    .page{padding-bottom:20px;}
    #table .zd{display:inline-block;background-color: #3251ff;padding:2px 5px;margin-right:5px;cursor:pointer;}
    .ddd{}
    .contentBox{height:calc(100vh - 93px) !important;}
</style>
<div class="contentBox">
    <div class="list-tool">
        <button id="fh" style="display:none;margin:0 -18px 0 20px;" class="search" onclick="location.href='<?php echo \think\Session::get('oindex'); ?>';"><<返回母单</button>
        <button class="search" onclick="window.location.href='<?php echo U('/smartlock/index', ['type'=>I('type'), 'status'=>I('status'), 'fyid'=>I('fyid')]); ?>';">全部</button>
        <form action="<?php echo U('/smartlock'); ?>" style="display:inline;">
            <input type="hidden" name="fyid" value="<?php echo I('fyid'); ?>">
            <select id="type" name="type" style="margin:0 2px;width:80px;"><option value="" disabled>类型</option><option value="0">母单</option><option value="1">子单</option></select>
            <input style="margin:0 2px;width:160px;" value="<?php echo I('addr'); ?>" class="adress" type="text" placeholder="工单编号/地址/智能锁编号" name="addr" />

            <input style="margin:0;width:100px;" value="<?php echo $start; ?>" class="tiem_put" id="start" name="start" placeholder="开始日期" readonly />
            <span>-</span>
            <input style="margin:0;width:100px;" value="<?php echo $end; ?>" class="tiem_put" id="end" name="end" placeholder="结束日期" readonly />

            <button style="margin:0 2px" class="search" type="submit">搜索</button>
        </form>

        <?php if(I('type') != 1): ?>
        <button style="margin:0 2px" class="" type="button" onclick="download()">下载</button>
        <?php endif; ?>
        <button style="margin:0 2px;" class="refresh" onclick="window.location.reload();">刷新</button>
    </div>
    <div class="tab-space">
        <!--<p class="title-page">共<span>41356</span>条记录&nbsp;&nbsp;&nbsp;&nbsp;1655页</p>-->
        <table class="list-tab" id="table"><?php if($otype): ?>
            <thead>
            <tr>
                <td width="10%">工单编号</td>
                <td width="8%">日期</td>
                <td>地址</td>
                <td width="8%">智能锁编号</td>
                <td width="10%">管理所</td>
                <td width="6%">管理站</td>
                <td width="6%">维保单位</td>
                <td width="10%">智能锁</td>
                <td width="5%">NFC</td>
                <td width="5%">结果</td>
            </tr>
            </thead>
            <tbody><?php $status = ['<div class="tab-state stay">待审核</div>', '<div class="tab-state">合格</div>', '<div class="tab-state pass-no">不合格</div>']; if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): if( count($data)==0 ) : echo "" ;else: foreach($data as $key=>$val): ?>
            <tr>

                <td><a href="<?php echo U('/smartlock/detail', ['id'=>$val['id']]); ?>"><?php echo $val['gid']; ?></a></a></td>
                <td><?php echo substr($val['etime'], 0, 10); ?></td>
                <td class="ddd" title="【<?php echo $val['village']; ?>】<?php echo $val['village_addr']; ?>"><?php echo mb_cut('【'. $val['village'] .'】' . $val['addr'], 50); ?></td>
                <td><?php echo $val['smart_lock_num']; ?></td>
                <td><?php echo $tmp['place'][$val['place']]; ?></td>
                <td><?php echo $tmp['station'][$val['station']][1]; ?></td>
                <td><?php echo $tmp['office'][$val['office']]; ?></td>
                <td><div>
                    <?php if($val['s'][1] == 1): ?>
                    <img src="/static/images/smartlock.png" style="width: 18px">
                    <?php else: ?>
                    无
                    <?php endif; ?>
                </div></td>
                <td><div>
                    <?php if($val['s'][2] == 1): ?>
                    <span style="color: dodgerblue">有</span>
                    <?php else: ?>
                    无
                    <?php endif; ?>
                </div></td>
                <td> <?php echo $val['s'][1]==$val['s'][2]?'<span class="tab-state">符合</span>' : '<span class="tab-state pass-no">不符</span>'; ?></td>
            </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody><?php else: ?>
            <thead>
            <tr>
                <td width="10%">工单编号</td>
                <td width="8%">日期</td>
                <td>地址</td>
		<!--<td width="6%">智能锁编号</td>-->
                <td width="10%">管理所</td>
                <td width="6%">管理站</td>
                <td width="6%">维保单位</td>
                <td width="5%">智能锁数</td>
                <td width="8%">NFC登记数</td>
                <td width="5%">符 合</td>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): if( count($data)==0 ) : echo "" ;else: foreach($data as $key=>$val): ?>
            <tr>
                <td><a href="<?php echo U('/smartlock/index', ['fyid'=>$val['yid']]); ?>"><?php echo $val['gid']; ?></a></a></td>
                <td><?php echo substr($val['etime'], 0, 10); ?></td>
                <td class="ddd" title="【<?php echo $val['village']; ?>】<?php echo $val['village_addr']; ?>"><?php echo mb_cut('【'. $val['village'] .'】' . $val['village_addr'], 50); ?></td>
		<!--<td>123456</td>-->
                <td><?php echo $tmp['place'][$val['place']]; ?></td>
                <td><?php echo $tmp['station'][$val['station']][1]; ?></td>
                <td><?php echo $tmp['office'][$val['office']]; ?></td>
                <td><?php echo $val['s'][1]; ?></td>
                <td><?php echo $val['s'][2]; ?></td>

                <td>
                    <?php if($val['s'][1] == $val['s'][2]): ?>
                    <div class="tab-state">是</div>
                    <?php else: ?>
                    <div class="tab-state pass-no">否</div>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody><?php endif; ?>
        </table>
    </div>
    <?php echo $page; ?>
</div>
<script src="/static/js/plugins/layer/laydate/laydate.js"></script>
<script>
	
	var tmp = <?php echo !empty($tmp)?json_encode($tmp, 320) : '[]'; ?>,val=<?php echo !empty($val)?json_encode($val, 320) : '[]'; ?>;

	window.onload = function() {
		console.log('tmp is ',tmp);
		console.log('val is ',val);
	};



    function download(){
        if('<?php echo I('fyid'); ?>' != ''){
            location.href= '<?php echo U('/smartlock/download', ['type'=>1, 'fyid'=>I('fyid') ]); ?>'
        }else{
            location.href = '<?php echo U('/smartlock/download', ['type'=>0,'start'=>$start, 'end'=>$end, 'place'=>I('place'), 'station'=>I('station'), 'office'=>I('office'), 'addr'=>I('addr'), 'cs'=>I('cs'), 'start'=>I('start'), 'end'=>I('end')   ]); ?>' ;
        }
    }

    var tmp = <?php echo !empty($tmp)?json_encode($tmp, 320) : '[]'; ?>, fs6 = <?php echo !empty($fs6)?json_encode(array_values($fs6), 320) : '[]'; ?>, yps = <?php echo !empty($yps)?json_encode($yps, 320) : '[]'; ?>, param = <?php echo json_encode(input('param.'), 320); ?>, cs = [], ctpl = ['', ''], st = '';
    console.log(fs6);
    $(function(){
        $('.h_orders').addClass('active');
        $.each(tmp, function(i, v){
            var h = '';
            $.each(v, function(i2, v2){
                if(i === 'station') st += '<option pid="'+ v2[2] +'" value="'+ v2[0] +'">'+ v2[1] +'</option>'; else h += '<option value="'+ i2 +'">'+ v2 +'</option>';
            });
            $('#'+i).append(h);
        });
        $.each(fs6, function(t, f){
            ctpl[f.type] += '<p class="type-br"><a href="<?php echo U('/orders/detail'); ?>?type='+ f.id +'&id=#####">'+ f.name +'(<c class="c'+ f.id +'">0</c>)</a></p>';
        });


        // 扩展分类
        var extps = {'39': {title: '供水服务代表', pcid: 99}, '42': {title: '固定采样箱内部', pcid: 98}};
        var extkeys = Object.keys(extps);

        for(var i=0; i < extkeys.length; i++) {
            var ekey = extkeys[i]
            ctpl[1] += '<p><a href="<?php echo U('/orders/detail'); ?>?type='+extps[ekey].pcid+'&id=#####" title="">' + extps[ekey].title + '(<c class="c00'+ ekey +'">0</c>)</a></p>';
        }

        // 增加其他分类
        ctpl[1] += '<p><a href="<?php echo U('/orders/detail'); ?>?type=0&id=#####" title="">其它(<c class="c0">0</c>)</a></p>';

        console.log('extkeys', extkeys);
        $('.img-type').each(function(){
            var _t = $(this), r = '' + _t.data('rs'), k = '' + _t.data('ks'), rs = r.split(','), ks = k.split(','), ps = 0;
            _t.append(ctpl[parseInt(_t.data('type'))].replace(new RegExp('#####', "gm"), _t.data('id')));
            // console.log("img-type", r);

            // 工单扩展分类
            var extvals = {'39': 0, '42': 0};

            if(r !== ''){
                // 分类图片数量
                $.each(rs, function(i1, v1){
                    vs = v1.split(':');
                    cc = parseInt(vs[1]);
                    ps += cc;
                    tps = _t.find('.c'+yps[vs[0]]);
                    if(extkeys.indexOf(vs[0]) > -1){

                        console.log(">> find extkey ", vs[0], "==", cc)
                        extvals[vs[0]] = cc;
                    }

                    // 如果不在分类中则为其它(c0),数量递增1
                    tps = tps.length ? tps : _t.find('.c0');

                    console.log(_t.data('id'), ">>>>  ", cc, tps.text())

                    // 每个小类数量递增
                    // console.log("vs==", vs[0])
                    tps.text(cc + parseInt(tps.text()));
                });
            }
            console.log(_t.data('id'), "==", extvals)
            for(var i=0; i < extkeys.length; i++){
                var ekey = extkeys[i]
                console.log("extp", ekey)

                var tk = _t.find('.c00'+ekey);
                if(extvals[ekey] === 0){
                    tk.parent().parent().hide();
                }else{
                    tk.text(extvals[ekey]).parent().parent().removeClass('type-br').addClass('type-bb');
                }
            }

            if(k !== ''){
                // 如果合格，背景色为绿色
                $.each(ks, function(i1, v2){
                    _t.find('.c'+v2).parent().parent().removeClass('type-br').addClass('type-bg');
                });
            }
            // 显示工单总图片数/重复数
            _t.parent().parent().find('.ps').text(ps);
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
        $('#type').val('<?php echo I('type'); ?>').change(function(){
            if($(this).val() == '1') $('#ztype').removeAttr('disabled'); else $('#ztype').attr('disabled', 'disabled');
        });
        $('#ztype').val('<?php echo I('ztype'); ?>');
        $('#status').val('<?php echo I('status'); ?>');
        //$('#gdtype').val('<?php echo I('gdtype'); ?>');

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
        $('c').each(function(){
            if(parseInt($(this).text()) == 0) $(this).parent().removeAttr('href');
        });
        $('.cf').each(function(){
            if(parseInt($(this).text()) == 0) $(this).removeAttr('href');
        });
        if('<?php echo I('fyid'); ?>' != ''){
            $('#type, #place, #station, #office').remove();
            $('#fh').show();
        }
        if('<?php echo I('type'); ?>' == '1'){
            $('#ztype').removeAttr('disabled');
        }
        if('<?php echo \think\Session::get('user.type'); ?>' == 2){
            $('#place').hide();
            sst('<?php echo \think\Session::get('user.office'); ?>');
        }
        if('<?php echo \think\Session::get('user.type'); ?>' == 3){
            $('#place').hide();
            $('#station').hide();
        }<?php if(!$otype): ?>
        $('#status').remove();
        <?php endif; ?>
        });
</script>

</body>
</html>