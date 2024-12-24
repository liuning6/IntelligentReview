<?php
namespace app\controller;
use think\Controller;
use think\Db;
class Waterlogin extends Controller
{
	public function index() 
	{
		if(session('user')){
			$this->redirect(U('/index/default'));
		}else{
			if($user = cookie('user')){
				$cuser = explode("\n!ecgsecgs!\n", $user);
				if($user = Db('users')->where(['username' => $cuser[0], 'status' => 1, 'deleted' => 0])->find()){
					if(md5($user['password']) == $cuser[1]){
						session('user', $user);
						$this->redirect(U('/index/default'));
					}
				}
			}
		}
		return $this->fetch();
    }
	
	public function login()
    {
		if($_POST){
			if($user = Db('users')->where(['username' => I('username'), 'password' => md5(I('password')), 'status' => 1, 'deleted' => 0])->find()){
				session('user', $user);
				cookie('user', $user['username']."\n!ecgsecgs!\n".md5($user['password']), time()+2592000);
				Db('users') -> where(['uid' => $user['uid']]) -> setField('logintime', time());
				return 1;
			}else {
				return 0;
			}
		}
		return $this->fetch();
    }
}
