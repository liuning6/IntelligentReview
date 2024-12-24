<?php
namespace app\controller;
use think\Controller;
//use think\Cache;

class Index extends Common
{
	public function index() {
		if($this->user['type'] == 3) return $this->redirect(U('/statistics'));
		 return $this->redirect(U('/index/welcome'));
	}
	
	public function default() {
		$this->assign('title', '平台选择');
		return $this->fetch();
	}
	
	public function welcome() {
		$place = \Param::place();
		$station2 = \Param::station(2);
		$office = \Param::office();
		$ut = (int)$this->user['type'];
		$uo = (int)$this->user['office'];
		
		if($_POST){
			$rs = \Statistics::get();
			if($ut == 2){//所级
				foreach($station2 as $s){
					if($s[2] != $uo) unset($rs[$s[0]]);
				}
			}elseif($ut == 3){//站级
				$rs = [$uo => $rs[$uo]];
			}
			return $rs;
		}
		//$update1210 =new Autoexec();
		//$postData = ['start' => '2024-11-01', 'end' => '2024-11-30'];
		//$statistics->index($postData);
		//echo "<script>console.log('statistics');</script>";
	

		$this->assign('title', '首页');
		$tmp['place'] = $place;
		$tmp['station'] = $station2;
		$tmp['station2'] = \Param::station();
		$tmp['office'] = $office;
		$tmp['ut'] = $ut;
		$tmp['uo'] = $uo;
		$this->assign('tmp', $tmp);
		return $this->fetch();
	}
	
	public function gtype() {
		$gtype = min(max(I('gtype/i'), 0), 3);
		db('users')->where('uid='.$this->user['uid'])->setField('gtype', $gtype);
		session('user.gtype', $gtype);
		return 1;
	}
	
	public function logout() {
		session_destroy();
		cookie('user', null);
		$this->redirect(U('/login')); 
	}
	
}
