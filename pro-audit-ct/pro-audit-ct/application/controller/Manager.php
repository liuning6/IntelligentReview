<?php 
namespace app\controller;
use think\Db;
class Manager extends Common
{
	//设置
	public function setting() {
		if($_POST) {$this -> error('添加失败');
			$info['username']		= I('username');
			$info['password']		= md5(I('password'));
			$info['type']			= I('type');
			$info['email']			= I('email');
			$info['tel']			= I('tel');
			$info['status']			= 1;
			if(I('repassword') != I('password')) {
				$this -> assign('info',$info);
				$this -> error('两次密码不同');
			}
			$pattern1 = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
            if(!preg_match( $pattern1, $info['email'])){
               $this->error('亲,邮箱格式错误,请输入正确的邮箱格式'); 
            }
			if(strlen(I('password')) < 6){
                $this->error('亲,密码必须大于等于6位！'); 
            }
			$result = Db('user') -> insert($info);
			if($result === false) {
				$this -> error('服务器错误，请稍后重试');
			}else {
				$this -> success('添加成功');
			}
		}
		return $this -> fetch();
	}
	
	//修改密码
	public function updatepassword() {
        $this->assign('title', '密码设置');
		if($_POST) {
			$password = I('password');
			$newpassword = I('newpassword');
			
			if(session("user.password") != md5($password)) $this->error('原密码不正确');
			
			if(strlen($newpassword) < 6){
                $this->error('亲, 密码必须大于等于6位！'); 
            }
			$newpassword = md5($newpassword);
			$result = Db('users') -> where(['uid' => session("user.uid")]) -> setField('password', $newpassword);
			if($result === false) {
				$this->error('服务器错误，请稍后重试');
			}else{
				session('user', null);
				cookie('user', null);
				$this->success('密码修改成功，请重新登录', url('/login'));
			}
		}
		return $this -> fetch();
	}

	//用户管理
	public function users() {
        $this->assign('title', '账户管理');
		if(session("user.type") > 0) die('没有权限');
		if($_POST){
			$type = (int)I('type');
			if($type == 1){
				$status = (int)I('status');
				if(!in_array($status, [1, 2])) $this->error('参数1错误');
				$uid = (int)I('uid');
				if($uid < 2) $this->error('参数2错误');
				db('users')->where(['uid'=>$uid])->update(['status'=>$status]);
			}elseif($type == 2){
                $office = intval(I('office'));
                $placeid = intval(I('placeid'));
                $stationid = intval(I('stationid'));
                $audit = intval(I('audit')) ? 1 : 0;

                if ($office == 0 || $office == 1) {
                    $type = $office;
                    $office = 0;
                } elseif ($office == 2) {
                    $type = 2;
                    $office = $placeid;
                } elseif ($office == 3) {
                    $type = 3;
                    $office = $stationid;
                } else {
                    $this->error('部门选择错误');
                }
				$username = I('username') or $this->error('账户名不能为空');
				$password = I('password') or $this->error('密码不能为空');
				db('users')->where(['username'=>$username, 'deleted'=>0])->find() && $this->error('账户 '. $username .' 已存在');
				db('users')->insert(['username'=>$username, 'password'=>md5($password), 'office'=>$office, 'audit'=>$audit, 'type'=>$type]) or $this->error('系统错误，请稍后再试');
			}
			$this->success('操作成功');
		}
		
		$map['uid'] = ['gt', 1];
		$status  =  I('status');
		if($status != ''){
			$status = max(1, min(2, (int)$status));
			$map['status'] = $status;
		}
		$office  =  I('office');
		if($office != ''){
			$office = intval($office);
			$map['type'] = $office;

            $place  =  I('place');
            if($place != '0'){
                $place = intval($place);
                $map['office'] = $place;
            }
            $station  =  I('station');
            if($station != '0'){
                $station = intval($station);
                $map['office'] = $station;
                $map['type'] = 3;
            }
		}

		$name  =  I('name');
		if($name != ''){
			$name  =  \Security::mysql_escape_string($name);

			$map['username'] = ['like', "%{$name}%"];
		}
		
		$count = db('users')->where($map)->count();// 查询满足要求的总记录数
//		echo db()->getLastsql();
		vendor('Page');
		$Page  = new \Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
        $show  = $Page -> show();// 分页显示输出
		$data = db('users')->where($map)->order('uid')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this -> assign('data', $data);
		$this -> assign('page', $show);// 赋值分页输出

        $tmp['place'] = \Param::place();
        $tmp['station'] = \Param::station(2);

        //$tmp['gdtype'] = db('gdtypes')->field('type id, name')->order('id')->select();
        $this->assign('tmp', $tmp);

		return $this -> fetch();
	}

    //查重设置
    public function likessetting() {
        $this->assign('title', '查重设置');

        if(I("action") == 'update'){
            $data = json_decode(file_get_contents('php://input'), true);

            foreach($data as $d){
                $mcid = $d['mcid'];
                $matching = $d['matching'];
                $deleted = $d['deleted'];
                $cp_days = $d['cp_days'];
                db('likes_setting')->where(['mcid'=>$mcid])->update(['matching'=>$matching, 'cp_days'=>$cp_days, 'deleted'=>$deleted]);
            }

            $this->success('操作成功');
            return;
        }
        $likessetting = db('likes_setting')->select();
        $this->assign('ls', $likessetting);
        return $this -> fetch();
    }

    public function reports() {
	   for($i=0;$i<1000;$i++) {
	       $days = $i+18;
           $date = date("Y-m-d", strtotime("-{$days} days"));
           echo "INSERT INTO `op_reports`(`cdate`, `recd_num`, `detected_num`, `detected_succ_num`, `cp_num`, `cp_pairs_num`, `cp_pairs2_num`, `cp_lk_num`, `created_at`, `updated_at`) VALUES ('{$date}', 1209, 1209, 1209, 1209, 1409181, 203, 2, now(), now());<br>";
       }
    }

}
