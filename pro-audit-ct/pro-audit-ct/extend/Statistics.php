<?php

use think\Cache;

class Statistics
{
	//获取统计
    public static function get()
	{
		$gdtype = (int)session('user.gtype');
		if($rs = Cache::get('Statistics'.$gdtype)){
			return $rs;
		}
		$rs = [];
		$ds = db('orders')->field('station `0`, office `1`, left(etime, 10) `2`, status `3`, cs `4`, appeal `5`')->where("status>0 and gdtype={$gdtype}")->select();
		foreach($ds as $i => $v){
			list($y, $m, $d) = explode('-', $v[2]);
			if($d > 25){
				if($m == 12){
					$m = 1;
					$y++;
				}else{
					$m++;
				}
			}
			$rs[$v[0]][$v[1]][$y][$m][0] ++;
			$hg = $v[3] == 1 ? 1 : 0;
			$cf = $v[4] > 0 ? 1 : 0;
			$ap = $v[5] > 0 ? 1 : 0;
			$rs[$v[0]][$v[1]][$y][$m][1] += $hg;
			$rs[$v[0]][$v[1]][$y][$m][2] += $cf;
			$rs[$v[0]][$v[1]][$y][$m][3] += $ap;
		}
		Cache::set('Statistics'.$gdtype, $rs, 21600);
		return $rs;
	}
	
}