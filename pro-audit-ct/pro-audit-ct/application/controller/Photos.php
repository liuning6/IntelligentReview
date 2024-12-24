<?php 
namespace app\controller;
use Think\Controller;
class Photos extends Common{
	//订单列表
	public function index(){
		$ow = [];
		$fyid  =  (string)I('fyid');
		$fyid && $ow['fyid'] = $fyid;
		$status  =  I('status');
		if($status != ''){
			$status = max(0, min(2, (int)$status));
			$ow['status'] = $status;
		}
		$cs = I('cs');
		if($cs != ''){
			$cs = max(0, $cs);
			if(!$cs) $ow['cs'] = 0; else $ow['cs'] = ['egt', $cs];
		}
		$office  =  I('office');
		if($office != ''){
			$office = max(0, min(19, (int)$office));
			$ow['office'] = $office;
		}
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
		
		$addr  =  I('addr');
		if($addr != ''){
			$ow[] = ['exp', db()->raw("addr like '%{$addr}%' or gid like '%{$addr}%'")];
		}
		
		$start = I('start');
		$end = I('end');
		$start && $ow['etime'] = ['egt', $start];
		$end && $ow['etime'] = ['elt', $end];
		$start && $end && $ow['etime'] = [['egt', $start], ['elt', $end]];
		
		$map = [];
		if($ow) $map[] = ['exp', db()->raw('oid in'. db('orders')->field('id')->where($ow)->buildsql())];
		$status2  =  I('status2');
		if($status2 != ''){
			$status2 = max(0, min(2, (int)$status2));
			$map['status'] = $status2;
		}
		$type  =  I('type');
		if($type != ''){
			$type = (int)$type;
			if($type == 1){
				//$map[] = ['exp', db()->raw('matching <= '. config('matching') .' or code < 2')];
				$map['code'] = 1;
			}else{
				$gs = \Param::fs5();
				//$map['matching'] = ['>', config('matching')];
				$map['code'] = ['in', $gs[$type]];
			}
		}
		$ycode  =  I('ycode');
		if($ycode != ''){
			$map['ycode'] = $ycode;
		}
		$matching = (float)I('matching');
		if($matching) $map['matching'] = ['egt', $matching];
		
		$count = db('photos')->where($map)->count();// 查询满足要求的总记录数
//		echo db()->getLastsql();
		vendor('Page');
		$Page  = new \Page($count,50);// 实例化分页类 传入总记录数和每页显示的记录数
        $show  = $Page->show();// 分页显示输出
		$data = db('photos')->alias('p')->field('p.*, o.etime, o.office, o.station')->join('orders o', 'o.id = p.oid')->where($map)->order('matching desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//print_r($data);
		$tmp['place'] = \Param::place();
		$tmp['station'] = \Param::station(2);
		$tmp['office'] = \Param::office();
		$tmp['fs2'] = \Param::fs2();
		$tmp['ycode'] = \Param::ycodes(0, '');
		$this->assign('tmp', $tmp);
		
		$this->assign('fs3', \Param::fs3());
		$this->assign('title', '照片');
		$this->assign('data', $data);
		$this->assign('page', $show);// 赋值分页输出
		return $this->fetch();
	}
	
}