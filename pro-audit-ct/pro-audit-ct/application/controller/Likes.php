<?php 
namespace app\controller;
use Think\Controller;
class Likes extends Common{
	//订单列表
	public function index(){
		$where = [];
		$map['x'] = 1;
		
		$matching  =  (float)I('matching') or $matching = 90;
		$map['matching'] = ['egt', $matching];
		
		$addr  =  I('addr');
		if($addr != ''){
			$ow[] = ['exp', db()->raw("addr like '%{$addr}%' or gid like '%{$addr}%'")];
			if(!(float)I('matching')) unset($map['matching']);
		}
		
		$where[] = ['exp', db()->raw('id in'. db('likes')->field('pid')->where($map)->buildsql() . ' or id in' . db('likes')->field('pid2')->where($map)->buildsql())];
		
		//$ow['cs'] = ['gt', 0];
		$place  =  I('place');
		if($place != ''){
			$place = max(1, min(7, (int)$place));
			$ow['place'] = $place;
		}
		$station  =  I('station');
		if($station != ''){
			$station = max(1, min(36, (int)$station));
			$ow['station'] = $station;
		}
		
		if($this->user['type'] == 2){
			$ow['place'] = $this->user['office'];
		}
		if($this->user['type'] == 3){
			$ow['station'] = $this->user['office'];
		}
		
		$office  =  I('office');
		if($office != ''){
			$office = max(1, min(19, (int)$office));
			$ow['office'] = $office;
		}
		
		/*$gdtype  =  I('gdtype');
		if($gdtype != ''){
			$gdtype = max(0, min(2, (int)$gdtype));
			$ow['gdtype'] = $gdtype;
		}*/
		
		$start = I('start');
		$end = I('end');
		$start && $ow['etime'] = ['egt', $start];
		$end && $ow['etime'] = ['elt', $end];
		$start && $end && $ow['etime'] = [['egt', $start], ['elt', $end]];
		
		if($ow){
			$where[] = ['exp', db()->raw('oid in'. db('orders')->field('id')->where($ow)->buildsql())];
		}
		
		
		$type  =  I('type');
		if($type != ''){
			$type = max(1, min(11, (int)$type));
			$gs = \Param::fs5();
			//$where['matching'] = ['>', config('matching')];
			$where['code'] = ['in', $gs[$type]];
		}
		
		
		
		$count = db('photos')->where($where)->count();// 查询满足要求的总记录数
		vendor('Page');
		$c  =  (int)I('c') or $c = 10;
		$Page  = new \Page($count, $c);// 实例化分页类 传入总记录数和每页显示的记录数
        $show  = $Page -> show();// 分页显示输出
		$data = db('photos')->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//echo db()->getLastsql().'<br>';
		//echo '<pre>';print_r($data[0]);die;
		foreach($data as $k=>$v){
		    $o = db('orders')->find($v['oid']);
			$data[$k]['gid'] = $o['gid'];
			$data[$k]['etime'] = $o['etime'];
			$data[$k]['addr'] = $o['addr'];
			$data[$k]['office'] = $o['office'];
			$data[$k]['station'] = $o['station'];
			
			$pids = [];
			$pid1 = db('likes')->field("id, pid2 ppid, matching")->where($map)->where("pid={$v['id']}")->select();
			$pid2 = db('likes')->field("id, pid ppid, matching")->where($map)->where("pid2={$v['id']}")->select();
			foreach(array_merge((array)$pid1, (array)$pid2) as $p){
				$ms[$v['id']][$p['ppid']] = $p;
				$pids[] = $p['ppid'];
			}
			//$data[$k]['pids'] = $pids;
			$data[$k]['ps'] = db('photos')->alias('p')
            ->join('orders o', 'o.id=p.oid')
            ->where(['p.id' => ['in', $pids]])
            ->field('p.*, o.gid, o.etime, o.office, o.station, o.addr')->order('p.id')->select();
        }
        //echo '<pre>';print_r($data);die;
		
		$tmp['place'] = \Param::place();
		$tmp['station'] = \Param::station(2);
		$tmp['office'] = \Param::office();
		$tmp['fs2'] = \Param::fs2();
		$tmp['fs3'] = \Param::fs3();
		
		//$tmp['gdtype'] = db('gdtypes')->field('type id, name')->order('id')->select();
		$this->assign('title', '重复图片');
		$this->assign('tmp', $tmp);
        $this->assign('time', I('time'));

		$this -> assign('data', $data);
		$this -> assign('ms', $ms);
		$this -> assign('page', $show);// 赋值分页输出
		session('lkindex', $_SERVER['REQUEST_URI']);
		return $this -> fetch();
	}
	
	public function pc(){
		$this->user['uid'] == 1 or die('您没有权限');
		$type = (int)I('type');
		$pid = (int)I('pid') or die('缺少参数');
		$ypid = $pid;
		if($type == 1){
			db('likes')->where("pid = {$pid} or pid2 = {$pid}")->setField('x', 0);
			db('orders')->where("id = (select oid from photos where id = {$pid})")->setDec('cs');
		}else{
			db('likes')->where('id', $pid)->setField('x', 0);
			$pid = db('likes')->where('id', $pid)->value('pid');
			if(db('likes')->where("pid = {$pid} and x = 1")->count() <= 0) db('orders')->where("id = (select oid from photos where id = {$pid})")->setDec('cs');
		}
		db('unlikes')->insert(['type' => $type, 'pid' => $ypid, 'uid' => $this->user['uid']]);
		return 1;
	}
	
	
	
}