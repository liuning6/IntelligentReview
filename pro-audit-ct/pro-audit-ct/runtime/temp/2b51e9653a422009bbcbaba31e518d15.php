<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:57:"/var/www/pro-audit-ct/application/view/index/welcome.html";i:1734917089;s:57:"/var/www/pro-audit-ct/application/view/common/header.html";i:1732680285;s:57:"/var/www/pro-audit-ct/application/view/common/footer.html";i:1614540793;}*/ ?>
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
#line{height:330px;}
#map{height:680px;min-width: 420px;text-align:center;}
#map img{height:680px;margin:0 auto;}
.data-row i, .data-row span{display:none;}
.map2{width:50%;float:left;}
.data-statistics ul li{width:14.28%}
</style>
    <div class="contentBox" style='height:auto;'>
        

        <div class="mid-picBox" style="margin-top:10px;">
            <div class="halfBox" style="width:30%">
                <h4><span id="h0" data-dt="0" data-o="0">供水分公司</span>分区示意图</h4><h4 id="fh" style="display:none;position: absolute;right:50px;top:0;cursor:pointer;"><<返回</h4>
                <div class="map" id='map'></div>
            </div>
            <div class="halfBox" style="width:70%">
                <h4 style="font-size:20px;"><span id="h1">2020年</span>工单情况统计</h4>
                <select class="top-sel" id="y" style="right:100px;width:88px;"><option value="" disabled>年份</option></select>
                <select class="top-sel" id="m" disabled="disabled"><option value="" disabled>月份</option></select>
				<div class="data-statistics" style="border:none;font-size: 22px;margin: 100px 50px 130px;">
					<ul>
						<li>
							<p class="data-title">总工单数</p>
							<div class="data-row" id="h2">
								<p>-</p>
								<span>-</span>
								<i></i>
							</div>
						</li>
						<li>
							<p class="data-title">合格工单</p>
							<div class="data-row" id="h3">
								<p>-</p>
								<span>-</span>
								<i class="green"></i>
							</div>
						</li>
						<li>
							<p class="data-title">合格率</p>
							<div class="data-row" id="h4">
								<p>-</p>
								<span>-</span>
								<i class="green"></i>
							</div>
						</li>
						<li>
							<p class="data-title">重复工单</p>
							<div class="data-row" id="h5">
								<p>-</p>
								<span>-</span>
								<i class="green"></i>
							</div>
						</li>
						<li>
							<p class="data-title">重复率</p>
							<div class="data-row" id="h6">
								<p>-</p>
								<span>-</span>
								<i class="green"></i>
							</div>
						</li>
						<li>
							<p class="data-title">申诉工单</p>
							<div class="data-row" id="h7">
								<p>-</p>
								<span>-</span>
								<i></i>
							</div>
						</li>
						<li>
							<p class="data-title">人脸合格率</p>
							<div class="data-row" id="h8">
								<p>-</p>
								<span>-</span>
								<i></i>
							</div>
						</li>

					</ul>
				</div>
				
				<div class="map2" style="margin-top:10px;height: 320px;width:33%;">
					<!--<div class="pic-tab"><p class="cur-tab">合格率</p><p>重复率</p></div>-->
					<div class="bar" style="height:100%" id="bar"></div>
				</div>
                <div class="map2" id='line' style="width:33%;"></div>
		<div class="map2" id='facebar' style="height:320px;width:33%;"></div>

				
            </div>
        </div>

        <!--<div class="btmBox">
            <h4>大数据统计</h4>
            <div class="pic-tab"><p class="cur-tab">站点</p><p>汇总</p></div>
            <div class="bar" id="bar"></div>
        </div>-->
    </div>

<script src="/static/js/map.js"></script>
<script src="/static/js/plugins/layer/layer.min.js"></script>
<script src="/static/js/plugins/echarts.min.js"></script>
<script>
var tmp = <?php echo !empty($tmp)?json_encode($tmp, 320) : '[]'; ?>, ut = tmp.ut, uo = tmp.uo, tt = 0, tp = 0, dt = 1, data = [], ds = [], yms = [], bs1 = [], yrs = [], ck = '', bs2 = '', t20 = 0, c10 = 0, bs3 = [], STATIC = '/static', myChart = echarts.init(document.getElementById('line')), barChart = echarts.init(document.getElementById('bar')),faceBar = echarts.init(document.getElementById('facebar')), ohg = {name:'合格率', type: 'line', areaStyle: {color: {type: 'linear', x: 0, y: 0, x2: 0, y2: 1, colorStops: [{offset: 0, color: '#3784FC'}, {offset: 1, color: '#0B1427'}], global: false}}, itemStyle:{color:'#3784FC'}}, ocf = {name:'重复率', type: 'line', areaStyle: {color: {type: 'linear', x: 0, y: 0, x2: 0, y2: 1, colorStops: [{offset: 0, color: '#17E1C0'}, {offset: 1, color: '#0D101B'}], global: false}}, itemStyle:{color:'#17E1C0'}}, option = {tooltip: {trigger: 'axis', formatter: function (params, ticket, callback) {var indexColor, tipString = params[0].axisValue + '<br />';for (var i = 0, length = params.length; i < length; i++) {indexColor = params[i].color;tipString += '<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background:'+ indexColor +'"></span>';tipString += params[i].seriesName + '：' + parseFloat(params[i].value) + '%<br />';}return tipString;}}, legend: {data:['合格率','重复率'],right:'10%', textStyle: {color: '#7887A1'}}, xAxis: {type: 'category', boundaryGap: false, axisLine:{lineStyle:{color:'#7887A1',}}}, yAxis: {min:0, max:100, type: 'value', axisLine:{lineStyle:{color:'#7887A1',}}, splitLine: {lineStyle: {color:'#7887A1'}}}, series: [ohg, ocf]}, option2 = {tooltip: {trigger: 'axis', formatter: function (params) {return '<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background:'+  params[0].color +'"></span>' + params[0].seriesName + '：' + (parseFloat(params[0].value) ? parseFloat(params[0].value) + '%' : 0);}, axisPointer: {type: 'shadow'}}, legend: {data: [], right:'10%', textStyle: {color: '#7887A1'}}, xAxis: {type: 'category', data: [], axisLine:{lineStyle:{color:'#7887A1',}}}, yAxis: {min:0, max:100, type: 'value', axisLine:{lineStyle:{color:'#7887A1'}}, splitLine: {lineStyle: {color:'#7887A1'}}}, series: []}, bs3c = ['#2FE9DB', '#7781EE', '#3784FC', '#4BACFF', '#34C38F', '#F1B44D', '#F46A6A'], load = layer.load(1, {shade: [0.5, '#000']});;

// 定义一个根据 name 设置颜色的映射对象
const nameToColor = {
        "宝山": "#6F85A8","东区": "#8E82A0","中区": "#68948C","北区": "#87958C","南区": "#8F8D8A","西区": "#8F778F","闵行": "#8477A8",
        "淞南站": "#75A8AE","市光站": "#9992B6","曲阳站": "#A48496","密云站": "#7C9CB7","汉阳站": "#83A895","江浦站": "#99A8A2","场中站": "#9684B7",
        "长江站": "#A2A595","沪太站": "#88AA96","普善站": "#779DB6","新闸站": "#75A8A1","半淞园站": "#A484AB","瞿溪站": "#A29395","平塘站": "#7CAAAB",
        "芙蓉江站": "#9AA89B","虹桥站": "#A28DB1","天钥桥站": "#96AA96","上中站": "#7C8EB7","龙华站": "#A288B4","莘庄站": "#A4AA96","吴泾站": "#88AA96",
        "江川站": "#7C9CB7","罗泾站": "#A292AD","罗店站": "#75A891","宝杨站": "#A49D8F","顾太站": "#8B9FB6","华新站": "#A4AA96","华漕站": "#A48496",
        "徐泾站": "#7C9CB7","九亭站": "#86A8A2","泗泾站": "#9684B7","新桥站": "#A28793","银杏站": "#88AA96","大场站": "#88AA96","真北站": "#A285A5","武宁站": "#7D95B6",
		"淞南": "#75A8AE","市光": "#9992B6","曲阳": "#A48496","密云": "#7C9CB7","汉阳": "#83A895","江浦": "#99A8A2",
        "场中": "#9684B7","长江": "#A2A595","沪太": "#88AA96","普善": "#779DB6","新闸": "#75A8A1","半淞园": "#A484AB",
        "瞿溪": "#A29395","平塘": "#7CAAAB","芙蓉江": "#9AA89B","虹桥": "#A28DB1","天钥桥": "#96AA96","上中": "#7C8EB7",
        "龙华": "#A288B4","莘庄": "#A4AA96","吴泾": "#88AA96","江川": "#7C9CB7","罗泾": "#A292AD","罗店": "#75A891",
        "宝杨": "#A49D8F","顾太": "#8B9FB6","华新": "#A4AA96","华漕": "#A48496","徐泾": "#7C9CB7","九亭": "#86A8A2",
        "泗泾": "#9684B7", "新桥": "#A28793","银杏": "#88AA96","大场": "#88AA96","真北": "#A285A5","武宁": "#7D95B6"
    };
window.onload = function() {
	console.log('y is ', $('#y'));
        var y = document.getElementById("y"); // 通过 id 获取元素
	var selectedValue = y.value;
	console.log("当前选中的年份为", selectedValue);
	///
		
		option3 = {
		title: {
        	text: '人脸合格率',
		top: 'bottom',
		left: 'center',
		textStyle: {
            		color: '#ffffff',           // 主标题的颜色
            			fontSize: 12,            // 主标题的字体大小
           			fontWeight: 'bold'       // 主标题的字体粗细
       		 },},
		tooltip: {trigger: 'axis', formatter: function (params){
		return '<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background:'+  params[0].color +'"></span>' + params[0].seriesName + '：' + (parseFloat(params[0].value) ? parseFloat(params[0].value) + '%' : 0);},
	 	axisPointer: {type: 'shadow'}}, 
	 	legend: {data: [], right:'10%', textStyle: {color: '#7887A1'}}, xAxis: {type: 'category', data: [], axisLine:{lineStyle:{color:'#7887A1',}}}, 
	 	yAxis: {min:0, max:100, type: 'value', axisLine:{lineStyle:{color:'#7887A1'}}, 
	 	splitLine: {lineStyle: {color:'#7887A1'}}},
	  	series: []}, 
	 	 load = layer.load(1, {shade: [0.5, '#000']});;

	///
};
function cx(t2,k) {

	

	load = layer.load(1, {shade: [0.5, '#000']})
	option3.xAxis.data = [];
	option3.legend.data = [];
	option3.series = [];
	faceBar.dispose();
	faceBar = echarts.init(document.getElementById('facebar'));
	faceBar.setOption(option3);
    	console.log('cx函数中k is ',k);

	// 获取当前时间
let currentTime = new Date();
let currentYear = currentTime.getFullYear();
let currentMonth = currentTime.getMonth() + 1;  // 月份是从 0 开始的
let currentDate = currentTime.getDate();

console.log(currentYear);  // 当前年份
console.log(currentMonth); // 当前月份
console.log(currentDate);  // 当前日期

let kdateString = k;

// 使用正则表达式提取年份和月份
let kmatches = kdateString.match(/^(\d{4})年(\d{1,2})月$/);

 let startDate='';
let endDate = '';

// 如果匹配成功，构造新的日期格式字符串
if (kmatches) {
    let kyear = kmatches[1];  // 年份
    let kmonth = kmatches[2]; // 月份，注意此时是字符串类型

    // 确保月份为两位数
    kmonth = kmonth.padStart(2, '0'); 

    // 构造标准的日期格式：YYYY-MM-DD，假设日期为该月的第一天
    let formattedDate = `${kyear}-${kmonth}-01`;

    // 创建 Date 对象
    let kdate = new Date(formattedDate);
    console.log('k date is', kdate);  // 输出：2022-08-01T00:00:00.000Z

    // 计算开始日期和结束日期
    startDate = `${kyear}-${kmonth}-01`;

    endDate = "";
    // 比较年月
    if (parseInt(kmonth) === currentMonth && parseInt(kyear) === currentYear) {
        endDate = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${currentDate}`;
    } else {
        let lastDay = new Date(kyear, parseInt(kmonth), 0);  // 获取该月的最后一天
        endDate = `${kyear}-${kmonth}-${lastDay.getDate()}`;
    }

    console.log("Start Date:", startDate); // 输出开始日期
    console.log("End Date:", endDate);     // 输出结束日期
} else {
    console.log("日期格式无效");
}	
	
	///undefined
	t2 = t2 === 0 ? '' : (t2 != null ? t2.toString() : '');
	console.log('t2 is ',t2);
	
	 $.post('<?php echo U("/statistics/get_face"); ?>', {
            type: '',
            place: t2,
            station: '',
            start: startDate,
            end: endDate,
            matching: '',
            clear: '1'
        }, function(d) {
		console.log('d is ',d);
		layer.close(load);
		if ( d['0']['5']['97'] ==0){
			$('#h8').text('无人脸数据').css('font-size', '15px');; 
			return;
		}
	    const faceQualifiedRate = d['0']['3']['97']*100/d['0']['5']['97'];
	    $('#h8').text(faceQualifiedRate.toFixed(2) + '%'); // 假设返回值是百分比
		
	        // 初始化两个空数组
let ratioArray = [];
let firstTwoDigitsArray = [];

// 遍历 d 数组从 0 到 7
for (let i = 1; i < Object.keys(d).length;i++) {
	const key = Object.keys(d)[i]; // 获取字典的键
    	const dic_value = d[key]; // 获取字典对应键的值
    // 计算第一个数组的值，并存入 ratioArray
	console.log('key is ',key);
    let ratio = (d[key]['3']['97'] * 100) / d[key ]['5']['97'];
    ratioArray.push(ratio);

    // 获取 d[i]['0'] 的前两位字符，并存入 firstTwoDigitsArray
    let value = d[key ]['0'].toString().slice(0, 2); // 取前两位字符
    firstTwoDigitsArray.push(value);

    option3.xAxis.data.push(value);
option3.legend.data.push(value);
// 创建 data 数组，按照 i 的值填充空值
    let dataArray = new Array(i-1).fill(null); // 生成 i 个空值
    dataArray.push(parseFloat(ratio.toFixed(2))); // 添加实际的 ratio 值到 data 数组
    console.log("dataArray is ",dataArray);
// 每个系列应该是一个对象，包含 `name` 和 `data` 等字段
option3.series.push({
    name: value, // 系列名称
    type: 'bar', // 图表类型
    data: dataArray ,// 对应 test01 的数据
    stack: '规范率',
    barWidth :30,
itemStyle: {
    color: nameToColor[value] || '#34C38F'  // 如果 name 没有对应颜色，默认颜色为 #34C38F
}
});
console.log("value is ",value);

}
// 使用配置绘制图表
faceBar.setOption(option3);
faceBar.resize();

console.log('Ratio Array:', ratioArray);
console.log('First Two Digits Array:', firstTwoDigitsArray);

	    
        });


}


function pint(n){
	return parseInt(n);
}

function sortNumber(a,b){
    return a - b
}

function bf(a, b, c, d){
	if(b == 0){
		if(!d) return '-';
		return 0;
	}
	return ((a / b) * 100).toFixed(c) + '%';
}

function bs3d(bn, bv, bx){
	vs = [];
	vs[bx] = bv;
	//console.log('data is ',vs);
	return {name: bn, type: 'bar', stack: '规范率', data: vs, color: nameToColor[bn] || '#34C38F', barWidth : 30}
	//return {name: bn, type: 'bar', stack: '规范率', data: vs, color: bs3c[bx], barWidth : 30}
}

function chart(t, t1, t2, t3){
	
	console.log(t, t1, t2, t3);
	if(t3 == 1){
		$('#fh').hide();
	}else if(t3 == 2){
		ck = t2;
		$('#fh').unbind().click(function(){
			--tt;
			--dt;
			$('#h0').text('供水分公司');
			chart(0, tt, tp, dt);
		});
	}else if(t3 == 3){
		$('#fh').unbind().click(function(){
			//--tt;
			--dt;
			console.log(' t3=3的时候，k=',k);
			$('#h0').text(tmp.place[ck]);
			chart(1, 0, ck, dt);
		});
	}
	t = t || 0;
	//console.log('chart:'+t);
	if(t3 < 3)initmap(t2);
	//console.log('data:');
	//console.log(data);
	ds = [];
	if(t == 1){
		if(t1 == 0){
			$.each(tmp.station, function(a1, b1){
				if(t2 == b1[2]) ds.push(a1);
			});
		}else if(t1 == 1){
			$.each(tmp.station, function(a1, b1){
				if(t2 == a1) ds.push(a1);
			});
		}
	}
	//console.log('ds:');
	//console.log(ds);
	$('.top-sel option.c').remove();
	var x = '', z = [], zd = '', yms = [], bs1 = [], bs2 = '', bs3 = [], b0 = 0, ys = [];
	$.each(data, function(st, v){
		if(ds.length == 0 || $.inArray(st, ds) > -1){
			$.each(v, function(o, w){
				//console.log('station:'+st);
				if(t3 == 1) zd = tmp.station[st][2]; else if(t3 == 2) zd = st; else if(t3 == 3) zd = o;
				try{e = bs3[zd].length;}catch(err){bs3[zd] = [];};
				$.each(w, function(y, ms){
					y = parseInt(y);
					if($.inArray(y, ys) < 0){
						ys.push(y);
						yrs[y] = [];
					}
					$.each(ms, function(m, s){
						m = parseInt(m);
						if($.inArray(m, yrs[y]) < 0){
							yrs[y].push(m);
						}
						x = y+'年'+m+'月';
						if($.inArray(x, z) < 0){
							z.push(x);
							yms[x] = [0, 0, 0, 0];
						}
						yms[x][0] += s[0];
						yms[x][1] += s[1];
						yms[x][2] += s[2];
						yms[x][3] += s[3];
						try{e = bs3[zd][x].length;}catch(err){bs3[zd][x] = [0, 0, 0];};
						bs3[zd][x][0] += s[0];
						bs3[zd][x][1] += s[1];
						bs3[zd][x][2] += s[2];
					});
				});
			});
		}
	});
	//console.log('z:');
	//console.log(z);
	console.log('yms is :',yms);
	//console.log(yms);
	//console.log(bs3);
	ys = ys.sort(sortNumber);
	//console.log(ys);
	option.xAxis.data = [];
	option.series[0].data = [];
	option.series[1].data = [];
	/*$.each(z, function(a, b){
		option.series[0].data.push(((yms[b][1] / yms[b][0]) * 100).toFixed(2));
		option.series[1].data.push(((yms[b][2] / yms[b][0]) * 100).toFixed(2));
	});*/
	$('#y, #m').removeAttr('disabled');
	y6 = [];
	$.each(ys, function(y2, y3){
		$('#y').append('<option class="c y'+ y3 +'" value="'+ y3 +'年">'+ y3 +'年</option>');
		$.each(yrs[y3].sort(sortNumber), function(y4, y5){
			if(y3 > 2021 || (y3 > 2020 && y5 > 2)) y6.push(y3+'年'+y5+'月');
		});
	});
	$.each(y6.slice(-6), function(y7, y8){
		option.xAxis.data.push(y8);
		option.series[0].data.push(((yms[y8][1] / yms[y8][0]) * 100).toFixed(2));
		option.series[1].data.push(((yms[y8][2] / yms[y8][0]) * 100).toFixed(2));
	});
	//console.log(option.xAxis.data);
	myChart.setOption(option);
	$('.data-row p').text('-');
	barChart.dispose();
	if(t3 == 1) zz = tmp.place; else if(t3 == 2) zz = tmp.station2; else if(t3 == 3) zz = tmp.office;
	//console.log(zz);
	$('#h1').text($('#h0').text());//console.log(yms);
	if(z.length){
		$('#y').find("option:last").attr("selected", true);
		$('#y').unbind().change(function(){
			k = $('#y').find("option:last").text() + $('#m').find("option:last").text();
			$('#m .c').remove();
			ty = parseInt($(this).val().replace('年', ''));
			$.each(yrs[ty].sort(sortNumber), function(f1, f2){
				$('#m').append('<option class="c m'+ f2 +'" value="'+ f2 +'月">'+ f2 +'月</option>');
			});
			$('#m').val('');
		});
		$.each(yrs[ys.slice(-1)].sort(sortNumber), function(f1, f2){
			$('#m').append('<option class="c m'+ f2 +'" value="'+ f2 +'月">'+ f2 +'月</option>');
			//console.log('f2 is ',f2);
		});
		$('#m').find("option:last").attr("selected", true);
		k = $('#y').find("option:last").text()+$('#m').find("option:last").text();
		$('#h1').text($('#h0').text()+k);//console.log(yms);
		cx(t2,k);
		bs2 = [k];
		option2.xAxis.data = [];
		option2.legend.data = [];
		option2.series = [];
		i1 = 0;
		$.each(zz, function(c1, d1){
			//console.log(c1);
			len = 0;
			try{len = bs3[c1][k].length;}catch(err){};
			if(len){
				zs = parseInt(bs3[c1][k][0]);
				hg = parseInt(bs3[c1][k][1]);
				cf = parseInt(bs3[c1][k][2]);
				if(t3 == 1){
					name = d1.replace('供水管理所', '');
					$('#map area[data-v="'+ t2 +'_'+ c1 +'"]').attr('title', d1 +'\n工单数：'+ zs +'\n合格率：'+ bf(hg, zs, 2) +'\n重复率：'+ bf(cf, zs, 2));
				}else if(t3 == 2){
					name = d1;
					$('#map area[data-v="'+ t2 +'_'+ c1 +'"]').attr('title', d1 +'\n工单数：'+ zs +'\n合格率：'+ bf(hg, zs, 2) +'\n重复率：'+ bf(cf, zs, 2));
					t20 = t2;
				}else if(t3 == 3){
					name = d1;
					//console.log('t20:'+t20+', t2:'+t2);
					$('#map area[data-v="'+ t20 +'_'+ t2 +'"]').attr('title', tmp.station2[t2] +'\n工单数：'+ zs +'\n合格率：'+ bf(hg, zs, 2) +'\n重复率：'+ bf(cf, zs, 2));
				}
				option2.xAxis.data.push(name);
				option2.legend.data.push(name);
				option2.series.push(
					bs3d(name,
					 ((hg / zs) * 50 + (50 - (cf / zs) * 50)).toFixed(2), i1)
					);//hg ? (((hg / zs) * (1 - (cf / zs))) * 100).toFixed(2) : 0
				++i1;
			}
		});
		//console.log(option2);
		barChart = echarts.init(document.getElementById('bar'));
		barChart.setOption(option2);
		
		
		$('#h2 p').text(yms[k][0]);
		$('#h3 p').text(yms[k][1]);
		$('#h4 p').text(((yms[k][1] / yms[k][0]) * 100).toFixed(2) + '%');
		$('#h5 p').text(yms[k][2]);
		$('#h6 p').text(((yms[k][2] / yms[k][0]) * 100).toFixed(2) + '%');
		$('#h7 p').text(yms[k][3]);
		$('#m').unbind().change(function(){
			barChart.dispose();
			k = ($('#y').val()+$(this).val());
			console.log('k is ',k);
			$('#h1').text($('#h0').text()+k);//console.log(yms);
			bs2 = [k];
			cx(t2,k);
		option2.xAxis.data = [];
		option2.legend.data = [];
		option2.series = [];
		i1 = 0;
		$.each(zz, function(c1, d1){
			//console.log(c1);
			len = 0;
			try{len = bs3[c1][k].length;}catch(err){};
			if(len){
				zs = parseInt(bs3[c1][k][0]);
				hg = parseInt(bs3[c1][k][1]);
				cf = parseInt(bs3[c1][k][2]);
				if(t3 == 1){
					name = d1.replace('供水管理所', '');
					$('#map area[data-v="'+ t2 +'_'+ c1 +'"]').attr('title', d1 +'\n工单数：'+ zs +'\n合格率：'+ bf(hg, zs, 2) +'\n重复率：'+ bf(cf, zs, 2));
				}else if(t3 == 2){
					name = d1;
					$('#map area[data-v="'+ t2 +'_'+ c1 +'"]').attr('title', d1 +'\n工单数：'+ zs +'\n合格率：'+ bf(hg, zs, 2) +'\n重复率：'+ bf(cf, zs, 2));
					t20 = t2;
				}else if(t3 == 3){
					name = d1;
					//console.log('t20:'+t20+', t2:'+t2);
					$('#map area[data-v="'+ t20 +'_'+ t2 +'"]').attr('title', tmp.station2[t2] +'\n工单数：'+ zs +'\n合格率：'+ bf(hg, zs, 2) +'\n重复率：'+ bf(cf, zs, 2));
				}
				option2.xAxis.data.push(name);
				option2.legend.data.push(name);
				option2.series.push(bs3d(name, ((hg / zs) * 50 + (50 - (cf / zs) * 50)).toFixed(2), i1));//hg ? (((hg / zs) * (1 - (cf / zs))) * 100).toFixed(2) : 0
				++i1;
			}
		});
		//console.log(option2);
		barChart = echarts.init(document.getElementById('bar'));
		barChart.setOption(option2);
			
			$('#h2 p').text(yms[k][0]);
			$('#h3 p').text(yms[k][1]);
			$('#h4 p').text(((yms[k][1] / yms[k][0]) * 100).toFixed(2) + '%');
			$('#h5 p').text(yms[k][2]);
			$('#h6 p').text(((yms[k][2] / yms[k][0]) * 100).toFixed(2) + '%');
			$('#h7 p').text(yms[k][3]);
		});
	}
}

$(function(){
	$('.h_index').addClass('active');
	if(ut == 2){
		tt = 1;dt = 2;
		$('#h0').text(tmp.place[uo]);
		tp = uo;
	}else if(ut == 3){
		tt = 2;dt = 3;
		tp = tmp.station[uo][2];
		$('#h0').text(tmp.station[uo][1]);
	}
	//initmap(tp);
	/*$('#fh').click(function(){
		location.reload();
	});*/
	$.post('<?php echo U('/index/welcome'); ?>', {a:1}, function(d){
		//layer.close(load);
		data = d;
		chart(0, 0, tp, dt);
	});
});
	
window.onresize = function () {
	myChart.resize();
	barChart.resize();
}
</script>

</body>
</html>