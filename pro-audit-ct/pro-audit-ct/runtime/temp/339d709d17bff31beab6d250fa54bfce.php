<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:55:"/var/www/pro-audit-ct/application/view/login/index.html";i:1730283939;}*/ ?>
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <meta name="renderer" content="webkit" />
    <title>二次供水AI智能审核系统 - 登录</title>
    <!--[if lt IE 9]>
	<script src="/static/js/html5shiv.min.js"></script>
	<script src="/static/js/respond.min.js"></script>
    <![endif]-->
    <link href="/static/css/reset.css" rel="stylesheet">
    <link href="/static/css/style.css" rel="stylesheet">
    <script src="/static/js/jquery.min.js"></script>
    <script src="/static/js/crypto-js.js"></script>
    <script src="/static/js/plugins/layer/layer.min.js"></script>
    <!--[if lt IE 9]>
    <script src="/static/js/jquery-1.11.3.min.js"></script>
    <![endif]-->
    <script>
        if(window.top!==window.self){window.top.location=window.location};
    </script>
</head>
<body>
    <div class="whole-bg">

        <div class="login">
            <h3>二次供水AI智能审核系统</h3>
            <div class="login-cent">
                <h4>登录</h4>
                <div class="login-row">
                    <p>用户名</p>
                    <input id="username" type="text" placeholder="请输入用户名">
                </div>
                <div class="login-row">
                    <p>密码</p>
                    <input id="password" type="password" placeholder="请输入密码">
                </div>
                <!--<div class="login-text">
                    <a>立即注册</a><a>忘记密码</a>
                </div>-->
                <button class="login-btn" id="submit_btn">登录</button>
            </div>
        </div>
        
    </div>
<script>
	function dologin(){
		var username     = $('#username').val();
		var password     = $('#password').val();
		if(!username){
			layer.tips('请输入用户名', '#username');
			$('#username').focus();
			return false;
		}
		if(!password){
			layer.tips('请输入密码', '#password');
			$('#password').focus();
			return false;
		}
        var salt = "2water";
		password = CryptoJS.MD5(password + salt).toString();

		$.post("<?php echo U('/login/login'); ?>",{username:username,password:password},function(data){
			if(data==0){
layer.tips('请输入正确的用户名或密码', '#password');
                                $('#password').focus();
				$('#login_msg').css('display','inline');
			}else{
				window.location.href="?";
			}
		},"json")
	}
	$('#submit_btn').click(function(){dologin();});
	$(function(){
		$('#username, #password').bind('keypress',function(event){
			if(event.keyCode == 13) dologin();
		});
	});
</script>
</body>
</html>