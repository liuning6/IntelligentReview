<?php

namespace app\controller;

use think\Controller;
use think\Db;
use think\Request;

class Common extends Controller
{
    public function _initialize()
    {
        if (!session('user')) {
            if ($user = cookie('user')) {
                $cuser = explode("\n!ecgsecgs!\n", $user);
                if ($user = Db('users')->where(['username' => $cuser[0], 'status' => 1, 'deleted' => 0])->find()) {
                    if (md5($user['password']) == $cuser[1]) session('user', $user);
                }
            }
        } else {
            if ($user = Db('users')->where(['username' => session('user.username'), 'password' => session('user.password'), 'status' => 1, 'deleted' => 0])->find()) {
                cookie('user', $user['username'] . "\n!ecgsecgs!\n" . md5($user['password']), time() + 2592000);
            } else session("user", null);
        }

        if (!session('user')) {
            cookie('user', null);
            $this->redirect(U('/login'));
        }

        if (!$user['theme_id']) {
            $user['theme_id'] = db('theme')->where('uid', 0)->value('id');
        }
        $theme = db('theme')->where('id', $user['theme_id'])->find();
        //判断用户是否使用默认主题
        $isDefaultTheme = 1;
        $diyTheme = db('theme')->where(['uid' => $user['uid'], 'id' => $user['theme_id']])->find();
        if ($diyTheme) $isDefaultTheme = 0;
        $this->assign('tc', json_decode($theme['color'], true));
//        halt($isDefaultTheme);
        $this->assign('theme_id', $user['theme_id']);
        $this->assign('isDefaultTheme', $isDefaultTheme);
        $this->user = $user;

    }

}
