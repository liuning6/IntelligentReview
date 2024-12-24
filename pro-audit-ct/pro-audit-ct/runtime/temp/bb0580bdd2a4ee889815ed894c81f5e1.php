<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:64:"/var/www/pro-audit-ct/application/view/statistics/opreports.html";i:1658400295;s:57:"/var/www/pro-audit-ct/application/view/common/header.html";i:1732680285;s:57:"/var/www/pro-audit-ct/application/view/common/footer.html";i:1614540793;}*/ ?>
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

    #qtj {width:250px;margin:20px auto;}
    #qtj li{float:left;list-style:none;width:60px;height:30px;color:#fff;text-align:center;margin:0 10px;line-height:30px;cursor:pointer;}
    #statistics {width: 80%;height:360px;padding:0;margin:0 auto;}
    .c0{background:#FF9D6F;}
    .c1{background:#FF9D6F;}
    .c2{background:#FF9D6F;}
    .tjcur{background:#79FF79 !important;}
    #zj {width:60%;color:#fff;line-height:30px;font-size:14px;margin:0 auto;}
    .button {
        width: 70px;
        height: 28px;
        background: #3251FF;
        border-radius: 2px;
        margin-left: 20px;
        font-size: 12px;
        color: #EEF4FF;
        cursor: pointer;
        border: 1px solid #434C68;
    }
</style>

    <div class="main_cont">
        <div class="list-tool gapBox">
            <form action="" style="display:inline;" onsubmit="search();return false">
                <input value="<?php echo $start; ?>" class="tiem-put" id="start" name="start" placeholder="开始日期" readonly />
                <span>-</span>
                <input value="<?php echo $end; ?>" class="tiem-put" id="end" name="end" placeholder="结束日期" readonly />
                <button class="search" type="submit">搜索</button>
            </form>
        </div>
        <div id="qtj"><li class="c0" data-val="0">日</li><li class="c2" data-val="2">月</li></div>
        <div style="clear:both;height:30px;"></div>
        <div id="statistics"></div>
        <div style="clear:both;height:30px;"></div>
        <div id="zj">
            <div>时间段：（<span id="d1"></span> 至 <span id="d2"></span>）
                <button class="button" id="export" onclick="exportReport()">导 出</button>
            </div>
            <!--            <div>接收图片：<span id="total_recd"></span></div>-->
<!--            <div>检测图片：<span id="total_detected"></span></div>-->
<!--            <div>查重图片：<span id="total_cp"></span></div>-->
        </div>
        <div> &nbsp; </div>
        <div>

            <table class="list-tab" id="table" style="width: 60%;padding:0;margin:0 auto;">
                <thead>
                <tr>
                    <td width="25%"><div id="stat-unit">月份</div></td>
                    <td width="25%">工单照片数量</td>
                    <td width="25%">审核照片数量</td>
                    <td width="25%">查重照片数量</td>
                </tr>
                </thead>
                <tbody id="tb-data">
                <tr>
                    <td width="25%">1月</td>
                    <td width="25%">1290</td>
                    <td width="25%">392</td>
                    <td width="25%">29</td>
                </tr>
                </tbody>
            </table>


        </div>
    </div>

<script src="/static/js/plugins/echarts.min.js"></script>
<script src="/static/js/plugins/echarts-liquidfill.min.js"></script>
<script src="/static/js/plugins/layer/laydate/laydate.js"></script>

<script>
    $(function(){
        $('.h_statistics').addClass('active');
    $('.drop-down-content li a').each(function(index,item){if("图片统计"==item.innerHTML){item.className="active"}})
});

Date.prototype.format = function() {
    var mm = this.getMonth() + 1; // getMonth() is zero-based
    var dd = this.getDate();

    return [this.getFullYear(),
        (mm>9 ? '' : '0') + mm,
        (dd>9 ? '' : '0') + dd
    ].join('-');
};

function tranNumber(num, point) {
    point = point ? point : 2;
    var numStr = num.toString()
    // 十万以内直接返回
    if (numStr.length < 6) {
        return numStr;
    }
    //大于8位数是亿
    else if (numStr.length > 8) {
        var decimal = numStr.substring(numStr.length - 8, numStr.length - 8 + point);
        return parseFloat(parseInt(num / 100000000) + '.' + decimal) + '亿';
    }
    //大于6位数是十万 (以10W分割 10W以下全部显示)
    else if (numStr.length > 5) {
        var decimal = numStr.substring(numStr.length - 4, numStr.length - 4 + point)
        return parseFloat(parseInt(num / 10000) + '.' + decimal) + '万';
    }
}

var stype = 0;

var labelOption = {
    show: 0,
    position: 'insideBottom',
    distance: 5,
    align: 'left',
    verticalAlign: 'middle',
    formatter: '{name|{a}}  {c}%',
    // rotate: 90,
    fontSize: 18,
    rich: {
        name: {
            textBorderColor: '#fff'
        }
    },
    color:'#fff'
};

var sdate = '';
var edate = '';

function loadData(type){
        $("#statistics").css({
            //left: (window.innerWidth - 1280 )/2
        });
        // var load = layer.load(1, {shade: [0.5, '#000']});
        $.post("<?php echo U('/statistics/opreports'); ?>",{type: type, sdate: sdate, edate: edate}, function(data){
            var labels = [];
            var recd = [];
            var detected = [];
            var cp = [];
            var total_recd = 0;
            var total_detected = 0;
            var total_cp = 0;

            var statUnit = type === 0 ? "日期" : "月份";
            $("#stat-unit").html(statUnit);

            // 表格渲染
            var html = "";
            $.each(data.data, function(k, v){
                labels.push(v.label);
                recd.push(v.recd_num);
                total_recd += parseInt(v.recd_num);
                detected.push(v.detected_num);
                total_detected += parseInt(v.detected_num);
                cp.push(v.cp_num);
                total_cp += parseInt(v.cp_num);
                var label = type === 0 ? v.label : v.label.substr(0, 4) + "年" + v.label.substr(4, 6) + "月";

                var _html = '<tr>'
                    +'<td width="25%">'+ label +'</td>'
                    +'<td width="25%">'+ v.recd_num +'</td>'
                    +'<td width="25%">'+ v.recd_num +'</td>'
                    +'<td width="25%">'+ v.cp_num +'</td>'
                    +'</tr>';
                html += _html;
            });
            $("#tb-data").html(html);

            $('#d1').html(data.sdate);
            $('#d2').html(data.edate);
            var option = {
                textStyle: {color: '#fff'},
                title: {
                    text: '图片统计',textStyle: {fontSize: 30, fontFamily: 'Microsoft Yahei', fontWeight: 'normal', color: '#bcb8fb', rich: {a: {fontSize: 28}}}}
                ,
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        label: {
                            backgroundColor: '#6a7985'
                        }
                    }
                },
                legend: {
                    itemWidth: 30,
                    data:[
                        {name: '接收图片数量'},
                        {name: '检测图片数量'},
                        {name: '查重图片数量'},
                    ],
                    textStyle: {
                        color: '#fff',
                        fontSize: 16,
                    }
                },
                xAxis: {
                    type: 'category',
                    data: labels,
                    axisLine: {
                        lineStyle: {
                            fontSize:18,
                            type: 'solid',
                            color: '#fff',//左边线的颜色
                            width: 2//坐标线的宽度
                        }
                    },

                    //data: labels
                },
                yAxis: {
                    type: 'value',
                    splitLine:{show: 1},//去除网格线
                    axisLabel: {
                        textStyle: {
                            fontSize:16
                        },
                        formatter: function(value, index){
                            return value;
                        }

                    },
                },
                series: [{
                    symbolSize: 8,
                    itemStyle: {
                    },
                    lineStyle: {
                        width: 2
                    },
                    label: labelOption,
                    name: '接收图片数量',
                    type: 'line',
                    data: recd,
                    areaStyle: {}
                },
                    {
                        symbolSize: 10,
                        itemStyle: {
                        },
                        lineStyle: {
                            width: 2
                        },
                        label: labelOption,
                        name: '检测图片数量',
                        type: 'line',
                        data:  detected,
                    }
                    ,
                    {
                        symbolSize: 10,
                        itemStyle: {
                        },
                        lineStyle: {
                            width: 2
                        },
                        label: labelOption,
                        name: '查重图片数量',
                        type: 'line',
                        data:  cp,
                    }
                ]
            };

            echarts.init(document.getElementById("statistics")).setOption(
                option
            );
	        console.log('total recd',total_recd);
            console.log(tranNumber(total_recd));
            $('#total_recd').html(total_recd);
            //$('#total_recd').html(tranNumber(total_recd));
            $('#total_detected').html(total_detected);
            //$('#total_detected').html(tranNumber(total_detected));
            $('#total_cp').html(total_cp);
            //$('#total_cp').html(tranNumber(total_cp));

            // layer.close(load);

        });
}

    function initdate() {
    var start={elem:"#start",format:"YYYY-MM-DD",max:'2028-05-01',min:"2017-05-01",istime:false,istoday:true,choose:function(datas){end.min=datas;end.start=datas}};
    var end={elem:"#end",format:"YYYY-MM-DD",max:'2028-05-01',min:"2017-05-01",istime:false,istoday:true,choose:function(datas){start.max=datas}};
    laydate(start);laydate(end);
}

function search(){
    sdate = $("#start").val();
    edate = $("#end").val();
    $('#qtj li:first').click();
}

function exportReport(){
    sdate = $("#start").val();
    edate = $("#end").val();
    location.href = "/statistics/opdownload.html?sdate=" + sdate + "&edate=" + edate + "&type=" + stype;
}
$(function(){
    initdate();
    $('#qtj li').click(function(){
        var _t = $(this);
        stype = parseInt(_t.data('val'));
        loadData(stype);
        $('.tjcur').removeClass('tjcur');
        _t.addClass('tjcur');
    });
    $('#qtj li:first').click();

    // loadData();
});

</script>

</body>
</html>
