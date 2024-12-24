<?php

namespace app\controller;

use think\Controller;

//use think\Cache;

class Theme extends Common
{
    public function index()
    {
        $isDiyTheme = 0;
        $user = $this->user;
        //获取默认主题色
        $themeDefaultList = db('theme')->where('uid', 0)->select();
        foreach ($themeDefaultList as &$the) {
            $the['color'] = json_decode($the['color'], true);
        }
        $this->assign('themeDefaultList', $themeDefaultList);
        $this->assign('title', '主题设置');
        //用户自定义主题
        $diyTheme = db('theme')->where(['uid' => $user['uid']])->value('color');
//        halt($diyTheme);
        if ($diyTheme) {
            $isDiyTheme = 1;
            $diyTheme = json_decode($diyTheme, true);
        } else {
            $diyTheme = ["8,13,24,1", "13,20,38,1", "22,93,255,1", "49,68,97,1", "27,41,74,1", "69,76,102,1", "27,41,74,1", "201,205,212,1", "27,41,74,1", "255,255,255,1"];
        }
        $newDiyTheme = [];
        foreach ($diyTheme as $k => $vo) {
            if ($k == 0) $newDiyTheme[] = [$vo, '背景色', ''];
            if ($k == 1) $newDiyTheme[] = [$vo, '卡片背景色', ''];
            if ($k == 2) $newDiyTheme[] = [$vo, '主题按钮色', '全部'];
            if ($k == 3) $newDiyTheme[] = [$vo, '输入框色', '1'];
            if ($k == 4) $newDiyTheme[] = [$vo, '列表选择栏色', ''];
            if ($k == 5) $newDiyTheme[] = [$vo, '线框按钮色', '审核'];
            if ($k == 6) $newDiyTheme[] = [$vo, '次要按钮色', '下一页'];
            if ($k == 7) $newDiyTheme[] = [$vo, '禁用按钮色', '审核'];
            if ($k == 8) $newDiyTheme[] = [$vo, '分割线色', ''];
            if ($k == 9) $newDiyTheme[] = [$vo, '文字颜色', ''];
        }
//        halt($newDiyTheme);
        $this->assign('newDiyTheme', $newDiyTheme);
        $this->assign('isDiyTheme', $isDiyTheme);
        return $this->fetch();
    }

    public function setTheme()
    {
        $id = I('id');
        $user = $this->user;
        if ($user['theme_id'] == $id) return ['code' => 1, 'msg' => '主题设置成功'];
        $theme = db('theme')->find($id);
        if (!$theme) return ['code' => 0, 'msg' => '主题不存在'];
        db('users')->where('uid', $user['uid'])->update(['theme_id' => $id]);
        return ['code' => 1, 'msg' => '主题设置成功'];
    }

    public function setDiyTheme()
    {
        $color = I('color');
        $user = $this->user;
        // 之前是否设置过主题
        $diyTheme = db('theme')->where(['uid' => $user['uid']])->find();
        if ($diyTheme) {
            db('theme')->where(['id' => $diyTheme['id']])->update(['color' => $color]);
            db('users')->where('uid', $user['uid'])->update(['theme_id' => $diyTheme['id']]);
        } else {
            $theme_id = db('theme')->insertGetId([
                'name' => '自定义',
                'uid' => $user['uid'],
                'image' => '',
                'color' => $color,
                'createtime' => time()
            ]);
            db('users')->where('uid', $user['uid'])->update(['theme_id' => $theme_id]);
        }
        return ['code' => 1, 'msg' => '自定义主题设置成功'];
    }

}
