<?php

class Audit
{
    function __construct()
    {
        $m = db('category')->field('id,pid,type,gtype,scode,mode')->where("`pid` in(select id from `category` where `cate` = 1 and `mode` > 0) and `cate` > 1 and `mode` > 0")->select();
		foreach($m as $c){
			$s[$c['type']][$c['gtype']][$c['pid']][$c['mode']][] = $c['scode'];
		}
		$s[2] = explode(',', db('category')->where("type = 1 and cate > 1")->value('GROUP_CONCAT(scode)'));
		$this->s = $s;
		
		$fs = \Param::fs(1);
		$this->fs = array_change_key_case($fs, CASE_LOWER);
		
		$rconfig = config('database.redis');	//redis服务器
		$redis = new \Redis();
		$redis->connect($rconfig['host'], $rconfig['port']); 
		$redis->select($rconfig['db']);
		$this->redis = $redis;
    }
	
	/*
		分类审核
		type：工单类型（0母单，1子单）
		date：审核的图片集
	*/
	function audit($type = 1, $gtype = 0, $data = [])
	{
		if(!$data) return false;

		$s = $this->s[(int)$type][(int)$gtype];
		if($gtype == 0 && $type == 1){
			print_r('---------s-----------');
			print_r($s);
print_r('-----------data---------');
print_r($data);
		}

		
		if(!$s) throw new Exception("param type error!");
		$o = [];
		foreach($s as $fid => $c){
			foreach($c as $t => $k){
				foreach($k as $i => $v){
					if(in_array($v, $data)){
						if($t == 2){
							unset($s[$fid]);
							$o[] = $fid;
							break;
						}
						unset($s[$fid][$t][$i]);
					}
				}
				if($t == 1){
					if(!array_filter($s[$fid])){
						unset($s[$fid]);
						$o[] = $fid;
					}
				}
			}
		}
		//print_r($s);
		if($gtype == 0 && $type == 1){
			print_r('---------s222-----------');
			print_r($s);
		}

		return [$s ? false : true, $o];
	}
	
	function auditOrders($orders)
	{
		if(!$orders) return [];
		$redis = $this->redis;
		$fs = $this->fs;
		$ods = [];
		$time = date('Y-m-d H:i:s');
		$forders = [];
		$ps = 0;

		foreach($orders as $order){
			if(!$order['type']){
				if($order['gdtype'] != 0) continue;
				$forders[$order['yid']] = 1;
				continue;
			}
			if($order['gdtype'] == 0) $forders[$order['fyid']] = 1;
			$photos = $order['photos'];
			//if(!$photos) continue;
			$j = [];
			$yp = db('photos')->field('id,matching,code')->where(['oid'=>$order['id']])->whereNotIn('id', array_column($photos, 'id'))->select();

			if($yp){
				foreach($yp as $p){
					if($p['matching'] > config('matching') && $p['code'] > 1) $j[$p['code']]++;// else $j[1]++;
				}
			}
			$pts = [];
			foreach($photos as $photo){
				$rs = $redis->hget('photos', $photo['id']);//print_r($r);die;

				$rs = explode(',', $rs);
				//print_r($photo);
				$code = (int)$rs[0] or $code = 1;
				$matching = min(round($rs[1], 6), 99.999999);
				$status = (int)$rs[2] or $status = 2;
				if($status == 1){
					if(in_array($code, $this->s[2])) $j[$code]++;
				}
                if($photo['ycode'] == 19){
                    $status=1;
			$code=2601;
                    $matching=0.99;
                }
				db('photos')->where(['id'=>$photo['id']])->update(['status'=>$status, 'matching'=>$matching, 'code'=>$code, 'updatetime'=>$time]);
				$pts[] = ['id'=>$photo['id'], 'matching'=>$matching, 'code'=>$code, 'status'=>$status, 'updatetime'=>$time];
				$redis->hdel('photos', $photo['id']);
				$ps++;
			}
			//lg(" {$order['id']} j: ".print_r($j, true));
			$at = $this->audit(1, $order['gdtype'], array_keys($j));

			//lg(" at： ".print_r($at, true));
			//print_r($order);


			$status = $at[0] ? 1 : 2;
			db('orders')->where(['id'=>$order['id']])->update(['status'=>$status, 'updatetime'=>$time]);
			$ods[] = ['id'=>$order['id'], 'status'=>$status, 'photos' => $pts, 'updatetime'=>$time, 'fs' => $at[1]];
			db()->query("delete from `order_adopt` where `oid` = {$order['id']}");
                        lg("- update order_adopt -".$order['id']);

			foreach($at[1] as $f){
				db()->query("replace into `order_adopt` set `oid` = {$order['id']}, `cate` = {$f}");
			}
			lg("-处理子单	{$order['gid']}({$order['id']})：". ($status == 1 ? '通过' : '未通过'));
		}
		
		$ts = db('ycodes')->where("otype = 0")->value('GROUP_CONCAT(id)');
		foreach($forders as $fyid => $v){
			$order = db('orders')->where(['yid'=>$fyid])->find();
			if(!$order) continue;
			$photos = db()->query('select GROUP_CONCAT(code) codes from(SELECT code FROM `photos` WHERE  ( ycode in('. $ts .') and status = 1 and oid in(select id from orders where fyid="'. $order['yid'] .'") ) GROUP BY `code`) s');
			$ms = explode(',', $photos[0]['codes']);
			$at = $this->audit(0, $order['gdtype'], $ms);
			$status = $at[0] ? 1 : 2;
            //print_r($at);
			db('orders')->where(['id'=>$order['id']])->update(['status'=>$status, 'updatetime'=>$time]);
			$ods[] = ['id'=>$order['id'], 'status'=>$status, 'photos' => [], 'updatetime'=>$time, 'fs' => $at[1]];
			db()->query("delete from `order_adopt` where `oid` = {$order['id']}");
			foreach($at[1] as $f){
				db()->query("replace into `order_adopt` set `oid` = {$order['id']}, `cate` = {$f}");
			}
			lg("-处理母单	{$order['gid']}({$order['id']})：". ($status == 1 ? '通过' : '未通过'));
		}
		return [$ods, $ps];
	}
	
	
	
}
