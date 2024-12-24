<?php 
namespace app\controller;
use Think\Controller;
use think\Cache;

class Statistics extends Common{
	//合格统计
	private $rs;
	public function index(){
		set_time_limit(0);
		$place = \Param::place();
		$station = \Param::station();
		$station2 = \Param::station(2);
		$office = \Param::office();
		$gdtype = (int)$this->user['gtype'];
		$fs7 = \Param::fs7($gdtype); //print_r($fs7);
                if ($gdtype == 0) {
                    $fs7[97][0] = 0;
                    $fs7[97][1] = '人员正脸照片';
                }
		$startTimeTotal = microtime(true);
		if($_POST){
			//var_dump($_POST);
			//echo "<script>console.log('_POST is', " . json_encode($_POST) . ");</script>";

			$pt = \Param::ps();
			$map = [];
			$map['status'] = ['gt', 0];
			$map['gdtype'] = $gdtype;
			
			$gdtype && $map['type'] = 1;
			
			$start = I('start');
			$end = I('end');
			$start && $map['etime'] = ['egt', $start];
			$end && $map['etime'] = ['elt', $end.' 23:59:59'];
			$start && $end && $map['etime'] = [['egt', $start], ['elt', $end.' 23:59:59']];
			
			$ps = [$place, 'place', ''];
			
			$splace = $sstation = false;
			$_place = max(0, min(7, (int)I('place')));
			$_place && $splace = $_place;
			$_station = max(0, min(36, (int)I('station')));
			$_station && $sstation = $_station;
			
			$ut = (int)$this->user['type'];
			$uo = (int)$this->user['office'];
			if($ut == 2){//所级
				$splace = $uo;
			}elseif($ut == 3){//站级
				unset($splace);
				$sstation = $uo;
			}
			
			if($splace){
				$ps = [$pt[$splace][1], 'station', $place[$splace]];
				$map['place'] = $splace;
			}
			if($sstation){
				$ps = [$office, 'office', $station2[$sstation][1]];
				$map['place'] = $station2[$sstation][2];
				$map['station'] = $sstation;
			}
			
			$ck = 'hgcounts_' . md5($gdtype.'~'.$start.'~'.$end.'~'.$splace.'~'.$sstation);
			if(I('clean')) Cache::set($ck, null);
			//$rs = Cache::get($ck);
			if(!$rs){
				$rs = [];
				$rs[0][0] = $ps[2] ?: '合格统计';
				//file_put_contents('/var/www/pro-audit-ct/runtime.txt', "ps[0]--" . json_encode($ps[0], JSON_UNESCAPED_UNICODE));


				foreach($ps[0] as $p => $pl){//管理所
					$n = $ps[1];
					$map[$n] = $p;
					$n = $$n;
					
					$z = db('orders')->where($map)->count();//每个管理所的总数、这里耗时不长
					
					

// 写入到文件			
			        $orders1=$map;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	$result = file_put_contents('/home/www/2water.com/map.txt', $contentToWrite);

					$zm = $gdtype ? 0 : db('orders')->where($map)->where('type=0')->count();//每个管理所的母单总数
					if($sstation && !$z) continue;
					$rs[0][1] += $z;//所有管理所总数
					$rs[0][2] += $zm;//所有管理所母单总数
					$rs[$p][0] = $n[$p];
					$rs[$p][1] = $z;
					$rs[$p][2] = $zm;
					
					$startTime = microtime(true);
					foreach($fs7 as $i => $fs){//大类
						//$newMap['status'] = ['eq', 1];	
						//$newMap['type'] = ($fs[0] <= 0) ? ['eq', 0] : ['egt',0];
						//$newMap = 'status=1 ';
						$newMap = 'status=1 or status=2 ';
						if ($fs[0] <= 0) { 
						    $newMap .= ' and `type` = 0';
						} else {
						    $newMap .= ' and `type`>=0';
						}
						if ($i == 97) {
                                                    $c = 0;
                                                    $suboid = db('orders')->where($map)->where('type=1')->value('GROUP_CONCAT(id)');
													$suboid1 = db('orders')->where($map)->where('type=0')->value('GROUP_CONCAT(id)');
													file_put_contents('/var/www/pro-audit-ct/runtime.txt', "map--" . json_encode($map, JSON_UNESCAPED_UNICODE));
                                                    if ($suboid) {
                                                        $c  = db()->query("SELECT COUNT(*) as p FROM photos WHERE ycode = 45 AND status = 1 AND oid IN ({$suboid})")['0']['p'];
                                                        $c5 = db()->query("SELECT COUNT(*) as p FROM photos WHERE ycode = 45 AND oid IN ({$suboid})")['0']['p'];
														$d  = db()->query("SELECT COUNT(*) as o FROM orders WHERE type = 0 AND facestatus = 1 AND id IN ({$suboid1})")['0']['o'];
                                                        $d5 = db()->query("SELECT COUNT(*) as o FROM orders WHERE type = 0 AND id IN ({$suboid1})")['0']['o'];
                                                        $rs[0][5][$i] += $c5;
                                                        $rs[$p][5][$i] = $c5;
														$rs[0][6][$i] += $d;
                                                        $rs[$p][6][$i] = $d;
														$rs[0][7][$i] += $d5;
                                                        $rs[$p][7][$i] = $d5;
                                                        // $rs[$p][9][$i] = $suboid;
                                                    //} else {
                                                    //    $c = 0;
                                                    } else {
                                                        $rs[0][5][$i] += 0;
                                                        $rs[$p][5][$i] = 0;
                                                    }
                                                } else {
                            			    $c = db('orders')->where($map)->where($newMap)->where("id in(select oid from order_adopt where cate={$i})")->count();
                        			}
						// $c = db('orders')->where($map)->where($newMap)->where("id in(select oid from order_adopt where cate={$i})")->count();
						//$c = db('orders')->where($map)->where("id in(select oid from order_adopt where cate={$i})")->count();
                                                //echo db()->getLastsql();
						$rs[0][3][$i] += $c;
						$rs[$p][3][$i] = $c;
					}
					
					$endTime = microtime(true);
					// 计算运行时间并转换为毫秒
					$executionTimeInMilliseconds = ($endTime - $startTime) * 1000;
					file_put_contents('/var/www/pro-audit-ct/runtime.txt', $pl."--".$executionTimeInMilliseconds . PHP_EOL, FILE_APPEND);

					$cz = db('orders')->where($map)->where("status = 1")->count();

					$rs[0][4] += $cz;
					$rs[$p][4] = $cz;
				}
				if(!$rs[0][1]) $rs = [[$rs[0][0], 0, 0, [], 0, [97=>0]]];
				Cache::set($ck, $rs, 3);
			}
// 写入到文件
			$orders1=$rs;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	$result = file_put_contents('/home/www/2water.com/rs.txt', $contentToWrite);
// 写入到文件
			$orders1=$fs7;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	$result = file_put_contents('/home/www/2water.com/fs7.txt', $contentToWrite);
			

			file_put_contents('/var/www/pro-audit-ct/rs.txt', json_encode($rs, JSON_UNESCAPED_UNICODE));
			
			$endTimeTotal = microtime(true);

			// 计算运行时间并转换为毫秒
			$executionTimeInMillisecondsTotal = ($endTimeTotal - $startTimeTotal) * 1000;
			file_put_contents('/var/www/pro-audit-ct/runtime.txt', "total--".$executionTimeInMillisecondsTotal . PHP_EOL, FILE_APPEND);

			$this->rs=$rs;
			return $rs;
		}

		$tmp['place'] = $place;
		$tmp['station'] = $station2;

		$this->assign('title', '合格统计');
		$this->assign('tmp', $tmp);
		$this->assign('fs7', $fs7);//print_r($fs2);
        $placeKeys = array_merge([0], array_keys($place));
        $this->assign('placeKeys', $placeKeys);//print_r($fs2);
		return $this->fetch();
	}

	public function get_face(){
		set_time_limit(0);
		$place = \Param::place();
		$station = \Param::station();
		$station2 = \Param::station(2);
		$office = \Param::office();
		$gdtype = (int)$this->user['gtype'];
		$fs7 = \Param::fs7($gdtype); //print_r($fs7);
                if ($gdtype == 0) {
                    $fs7[97][0] = 0;
                    $fs7[97][1] = '人员正脸照片';
                }
		$startTimeTotal = microtime(true);
		if($_POST){
			//var_dump($_POST);
			//echo "<script>console.log('_POST is', " . json_encode($_POST) . ");</script>";

			$pt = \Param::ps();
			$map = [];
			$map['status'] = ['gt', 0];
			$map['gdtype'] = $gdtype;
			
			$gdtype && $map['type'] = 1;
			
			$start = I('start');
			$end = I('end');
			$start && $map['etime'] = ['egt', $start];
			$end && $map['etime'] = ['elt', $end.' 23:59:59'];
			$start && $end && $map['etime'] = [['egt', $start], ['elt', $end.' 23:59:59']];
			
			$ps = [$place, 'place', ''];
			
			$splace = $sstation = false;
			$_place = max(0, min(7, (int)I('place')));
			$_place && $splace = $_place;
			$_station = max(0, min(36, (int)I('station')));
			$_station && $sstation = $_station;
			
			$ut = (int)$this->user['type'];
			$uo = (int)$this->user['office'];

			//file_put_contents('/var/www/pro-audit-ct/rs-01.txt', json_encode($start, JSON_UNESCAPED_UNICODE));
			
			if($ut == 2){//所级
				$splace = $uo;
			}elseif($ut == 3){//站级
				unset($splace);
				$sstation = $uo;
			}
			
			if($splace){
				$ps = [$pt[$splace][1], 'station', $place[$splace]];
				$map['place'] = $splace;
			}
			if($sstation){
				$ps = [$office, 'office', $station2[$sstation][1]];
				$map['place'] = $station2[$sstation][2];
				$map['station'] = $sstation;
			}
			
			$ck = 'hgcounts_' . md5($gdtype.'~'.$start.'~'.$end.'~'.$splace.'~'.$sstation);
			if(I('clean')) Cache::set($ck, null);
			//$rs = Cache::get($ck);
			if(!$rs){
				$rs = [];
				$rs[0][0] = $ps[2] ?: '合格统计';
				//file_put_contents('/var/www/pro-audit-ct/runtime.txt', "ps[0]--" . json_encode($ps[0], JSON_UNESCAPED_UNICODE));


				foreach($ps[0] as $p => $pl){//管理所
					$n = $ps[1];
					$map[$n] = $p;
					$n = $$n;
					
					$z = db('orders')->where($map)->count();//每个管理所的总数、这里耗时不长
					//$z = 0;//每个管理所的总数、这里耗时不长
					
					// 写入到文件			
			        $orders1=$map;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	$result = file_put_contents('/home/www/2water.com/map.txt', $contentToWrite);

					//$zm = $gdtype ? 0 : db('orders')->where($map)->where('type=0')->count();//每个管理所的母单总数
					$zm = 0;//每个管理所的母单总数
					if($sstation && !$z) continue;
					$rs[0][1] += $z;//所有管理所总数
					$rs[0][2] += $zm;//所有管理所母单总数
					$rs[$p][0] = $n[$p];
					$rs[$p][1] = $z;
					$rs[$p][2] = $zm;
					
					$startTime = microtime(true);
					foreach($fs7 as $i => $fs){//大类
						//$newMap['status'] = ['eq', 1];	
						//$newMap['type'] = ($fs[0] <= 0) ? ['eq', 0] : ['egt',0];
						//$newMap = 'status=1 ';
						$newMap = 'status=1 or status=2 ';
						if ($fs[0] <= 0) { 
						    $newMap .= ' and `type` = 0';
						} else {
						    $newMap .= ' and `type`>=0';
						}
						if ($i == 97) {
							$c = 0;
							$suboid = db('orders')->where($map)->where('type=1')->value('GROUP_CONCAT(id)');
							//file_put_contents('/var/www/pro-audit-ct/map.txt', $p . json_encode($map, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

							//$suboid1 = db('orders')->where($map)->where('type=0')->value('GROUP_CONCAT(id)');
							file_put_contents('/var/www/pro-audit-ct/runtime.txt', "map--" . json_encode($map, JSON_UNESCAPED_UNICODE));
							if ($suboid) {
								$c  = db()->query("SELECT COUNT(*) as p FROM photos WHERE ycode = 45 AND status = 1 AND oid IN ({$suboid})")['0']['p'];
								$c5 = db()->query("SELECT COUNT(*) as p FROM photos WHERE ycode = 45 AND oid IN ({$suboid})")['0']['p'];
								//$d  = db()->query("SELECT COUNT(*) as o FROM orders WHERE type = 0 AND facestatus = 1 AND id IN ({$suboid1})")['0']['o'];
								//$d5 = db()->query("SELECT COUNT(*) as o FROM orders WHERE type = 0 AND id IN ({$suboid1})")['0']['o'];


								$rs[0][5][$i] += $c5;
								$rs[$p][5][$i] = $c5;
								$rs[0][6][$i] += $d;
								$rs[$p][6][$i] =$d;
								$rs[0][7][$i] += $d5;
								$rs[$p][7][$i] = $d5;
								// $rs[$p][9][$i] = $suboid;
								//} else {
								//    $c = 0;
							} else {
								$rs[0][5][$i] += 0;
								$rs[$p][5][$i] = 0;
							}
						} else {
							//$c = db('orders')->where($map)->where($newMap)->where("id in(select oid from order_adopt where cate={$i})")->count();
							$c = 0;
						}
						// $c = db('orders')->where($map)->where($newMap)->where("id in(select oid from order_adopt where cate={$i})")->count();
						//$c = db('orders')->where($map)->where("id in(select oid from order_adopt where cate={$i})")->count();
                                                //echo db()->getLastsql();
						$rs[0][3][$i] += $c;
						$rs[$p][3][$i] = $c;
					}
					
					$endTime = microtime(true);
					// 计算运行时间并转换为毫秒
					$executionTimeInMilliseconds = ($endTime - $startTime) * 1000;
					file_put_contents('/var/www/pro-audit-ct/runtime.txt', $pl."--".$executionTimeInMilliseconds . PHP_EOL, FILE_APPEND);

					//$cz = db('orders')->where($map)->where("status = 1")->count();
					$cz = 0;

					$rs[0][4] += $cz;
					$rs[$p][4] = $cz;
				}
				if(!$rs[0][1]) $rs = [[$rs[0][0], 0, 0, [], 0, [97=>0]]];
				Cache::set($ck, $rs, 3);
			}
// 写入到文件
			$orders1=$rs;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	$result = file_put_contents('/home/www/2water.com/rs.txt', $contentToWrite);
// 写入到文件
			$orders1=$fs7;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	$result = file_put_contents('/home/www/2water.com/fs7.txt', $contentToWrite);
			

			file_put_contents('/var/www/pro-audit-ct/rs.txt', json_encode($rs, JSON_UNESCAPED_UNICODE));
			
			$endTimeTotal = microtime(true);

			// 计算运行时间并转换为毫秒
			$executionTimeInMillisecondsTotal = ($endTimeTotal - $startTimeTotal) * 1000;
			file_put_contents('/var/www/pro-audit-ct/runtime.txt', "total--".$executionTimeInMillisecondsTotal . PHP_EOL, FILE_APPEND);

			//$this->countPhotosToFaceDetect();

			// $map1 = [];
			// $map1['status'] = ['gt', 0];
			// $map1['gdtype'] = 0;
			// $map1['etime'] = [['egt', '2024-10-30'], ['elt', '2024-11-30'.' 23:59:59']];
			// $map1['place'] = 1;
			// // echo "<script>console.log('222222222222222222222222222');</script>";
			// // 查询符合条件的订单ID列表
			// file_put_contents('/var/www/pro-audit-ct/map.txt','map1  is ' . json_encode($map1, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
			// $suboid1 = db('orders')->where($map1)->where('type=1')->value('GROUP_CONCAT(id)');  // 根据实际需求，补充获取订单ID的逻辑
			// if ($suboid1) {
			// 	//echo "<script>console.log('1111111');</script>";
			// 	$c11  = db()->query("SELECT COUNT(*) as p FROM photos WHERE ycode = 45 AND status = 1 AND oid IN ({$suboid1})")['0']['p'];
			// 	file_put_contents('/var/www/pro-audit-ct/map.txt','map1 de count is ' . json_encode($c11, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
			// }
			
			// // $values_etime = db('orders')->distinct(true)->column('place');
			// // file_put_contents('/var/www/pro-audit-ct/map.txt','values_etime is ' . json_encode($values_etime, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);


			// $result = db('face_detect')->where('id', 0)->update([
			// 	'etime' => '2024-12-31 23:59:59',
			// 	'status' => 2
			// ]);


			$this->rs=$rs;
			return $rs;
		}

		$tmp['place'] = $place;
		$tmp['station'] = $station2;

		$this->assign('title', '合格统计');
		$this->assign('tmp', $tmp);
		$this->assign('fs7', $fs7);//print_r($fs2);
        $placeKeys = array_merge([0], array_keys($place));
        $this->assign('placeKeys', $placeKeys);//print_r($fs2);
		return $this->fetch();
	}

	///非post查询数据
	public function get_face_nonpost($params = []){
		//set_time_limit(0);
		$place = \Param::place();
		$station = \Param::station();
		$station2 = \Param::station(2);
		$office = \Param::office();
		$gdtype = (int)$this->user['gtype'];
		$fs7 = \Param::fs7($gdtype); //print_r($fs7);
                if ($gdtype == 0) {
                    $fs7[97][0] = 0;
                    $fs7[97][1] = '人员正脸照片';
                }

		// file_put_contents('/var/www/pro-audit-ct/map.txt', 'place is ' . json_encode($place, JSON_UNESCAPED_UNICODE).
		// 'station is ' . json_encode($station, JSON_UNESCAPED_UNICODE).'station2 is ' . json_encode($station2, JSON_UNESCAPED_UNICODE). PHP_EOL, FILE_APPEND);
			$pt = \Param::ps();
			$map = [];
			$map['status'] = ['gt', 0];
			$map['gdtype'] = $gdtype;
			
			$gdtype && $map['type'] = 1;
			
			$start = $params['start'];
			$end = $params['end'];
			$start && $map['etime'] = ['egt', $start];
			$end && $map['etime'] = ['elt', $end.' 23:59:59'];
			$start && $end && $map['etime'] = [['egt', $start], ['elt', $end.' 23:59:59']];
			
			$ps = [$place, 'place', ''];
			
			$splace = $sstation = false;
			$_place = max(0, min(7, (int)$params['place']));
			$_place && $splace = $_place;
			$_station = max(0, min(36, (int)$params['station']));
			$_station && $sstation = $_station;
			
			$ut = (int)$this->user['type'];
			$uo = (int)$this->user['office'];

			file_put_contents('/var/www/pro-audit-ct/rs-01.txt', json_encode($start, JSON_UNESCAPED_UNICODE));
			
			if($ut == 2){//所级
				$splace = $uo;
			}elseif($ut == 3){//站级
				unset($splace);
				$sstation = $uo;
			}
			
			if($splace){
				$ps = [$pt[$splace][1], 'station', $place[$splace]];
				$map['place'] = $splace;
			}
			if($sstation){
				$ps = [$office, 'office', $station2[$sstation][1]];
				$map['place'] = $station2[$sstation][2];
				$map['station'] = $sstation;
			}
			
			$ck = 'hgcounts_' . md5($gdtype.'~'.$start.'~'.$end.'~'.$splace.'~'.$sstation);
			if(I('clean')) Cache::set($ck, null);
			//$rs = Cache::get($ck);
			if(!$rs){
				$rs = [];
				$rs[0][0] = $ps[2] ?: '合格统计';
				//file_put_contents('/var/www/pro-audit-ct/runtime.txt', "ps[0]--" . json_encode($ps[0], JSON_UNESCAPED_UNICODE));


				foreach($ps[0] as $p => $pl){//管理所
					$n = $ps[1];
					$map[$n] = $p;
					$n = $$n;
					
					$z = db('orders')->where($map)->count();//每个管理所的总数、这里耗时不长
					//$z = 0;//每个管理所的总数、这里耗时不长
					
					// 写入到文件			
			        $orders1=$map;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	$result = file_put_contents('/home/www/2water.com/map.txt', $contentToWrite);

					//$zm = $gdtype ? 0 : db('orders')->where($map)->where('type=0')->count();//每个管理所的母单总数
					$zm = 0;//每个管理所的母单总数
					if($sstation && !$z) continue;
					$rs[0][1] += $z;//所有管理所总数
					$rs[0][2] += $zm;//所有管理所母单总数
					$rs[$p][0] = $n[$p];
					$rs[$p][1] = $z;
					$rs[$p][2] = $zm;
					
					$startTime = microtime(true);
					foreach($fs7 as $i => $fs){//大类
						//$newMap['status'] = ['eq', 1];	
						//$newMap['type'] = ($fs[0] <= 0) ? ['eq', 0] : ['egt',0];
						//$newMap = 'status=1 ';
						$newMap = 'status=1 or status=2 ';
						if ($fs[0] <= 0) { 
						    $newMap .= ' and `type` = 0';
						} else {
						    $newMap .= ' and `type`>=0';
						}
						if ($i == 97) {
							$c = 0;
							$suboid = db('orders')->where($map)->where('type=1')->value('GROUP_CONCAT(id)');
							//file_put_contents('/var/www/pro-audit-ct/map.txt', $p . json_encode($map, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

							//$suboid1 = db('orders')->where($map)->where('type=0')->value('GROUP_CONCAT(id)');
							file_put_contents('/var/www/pro-audit-ct/runtime.txt', "map--" . json_encode($map, JSON_UNESCAPED_UNICODE));
							if ($suboid) {
								$c  = db()->query("SELECT COUNT(*) as p FROM photos WHERE ycode = 45 AND status = 1 AND oid IN ({$suboid})")['0']['p'];
								$c5 = db()->query("SELECT COUNT(*) as p FROM photos WHERE ycode = 45 AND oid IN ({$suboid})")['0']['p'];
								//$d  = db()->query("SELECT COUNT(*) as o FROM orders WHERE type = 0 AND facestatus = 1 AND id IN ({$suboid1})")['0']['o'];
								//$d5 = db()->query("SELECT COUNT(*) as o FROM orders WHERE type = 0 AND id IN ({$suboid1})")['0']['o'];


								$rs[0][5][$i] += $c5;
								$rs[$p][5][$i] = $c5;
								$rs[0][6][$i] += $d;
								$rs[$p][6][$i] =$d;
								$rs[0][7][$i] += $d5;
								$rs[$p][7][$i] = $d5;
							} else {
								$rs[0][5][$i] += 0;
								$rs[$p][5][$i] = 0;
							}
						} else {
							$c = 0;
						}

						$rs[0][3][$i] += $c;
						$rs[$p][3][$i] = $c;
					}
					
					$cz = 0;

					$rs[0][4] += $cz;
					$rs[$p][4] = $cz;
				}
				if(!$rs[0][1]) $rs = [[$rs[0][0], 0, 0, [], 0, [97=>0]]];
				Cache::set($ck, $rs, 3);
			}
// 写入到文件
			$orders1=$rs;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	//$result = file_put_contents('/home/www/2water.com/rs.txt', $contentToWrite);
// 写入到文件
			$orders1=$fs7;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	//$result = file_put_contents('/home/www/2water.com/fs7.txt', $contentToWrite);
			

			//file_put_contents('/var/www/pro-audit-ct/rs.txt', json_encode($rs, JSON_UNESCAPED_UNICODE));
			
			$endTimeTotal = microtime(true);


			$this->rs=$rs;
			return $rs;
	}

	///创建新的数据库表
	function countPhotosToFaceDetect() {
		// 获取distinct值
		$place = db('orders')->distinct(true)->column('place');
		$gdtype = db('orders')->distinct(true)->column('gdtype');
		$station = db('orders')->distinct(true)->column('station');

		file_put_contents('/var/www/pro-audit-ct/map.txt','place 的值为' . json_encode($place, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
		file_put_contents('/var/www/pro-audit-ct/map.txt','gdtype 的值为' . json_encode($gdtype, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
		file_put_contents('/var/www/pro-audit-ct/map.txt','station 的值为' . json_encode($station, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

		// 构建查询条件
		$map1 = [];
		$map1['status'] = ['gt', 0];
		

		$currentDateTime = date('Y-m-d H:i:s');
		// 定义起始日期和结束日期
		//$startDate = new \DateTime('2020-10-07');
		$startDate = new \DateTime('2024-09-01');
		//$endDate = new \DateTime('2024-12-19');
		$endDate = new \DateTime('2024-09-30');
		// $currentDateTime = date('Y-m-d H:i:s');

		// 定义插入的数据数组
		$data = [];

		 // 循环日期范围
		 while ($startDate <= $endDate) {
            // 格式化为 'Y-m-d' 格式
            $formattedStartDate = $startDate->format('Y-m-d');
            $formattedEndDate = $startDate->format('Y-m-d') . ' 23:59:59';
			file_put_contents('/var/www/pro-audit-ct/map.txt', mb_convert_encoding('formattedStartDate 的值为' . json_encode($formattedStartDate, JSON_UNESCAPED_UNICODE). PHP_EOL, 'UTF-8', 'auto'), FILE_APPEND);
            // 在每次循环中更新 $map1['etime']
            $map1['etime'] = [['egt', $formattedStartDate], ['elt', $formattedEndDate]];
			
			foreach ($place as $place_value) {
				$map1['place'] = $place_value;
				
				foreach ($gdtype as $gdtype_value) {
					if ($gdtype_value ===4){
						continue;
					}
					$map1['gdtype'] = $gdtype_value;
					if ($gdtype_value) {
						$map['type'] = 1;
					} else {
						unset($map['type']);
					}
					
					foreach ($station as $station_value) {
						$map1['station'] = $station_value;
						//$map1['etime'] = [['egt', '2024-11-01'], ['elt', '2024-11-01 23:59:59']];
						
						// 获取符合条件的订单ID列表
						$suboid1 = db('orders')->where($map1)->where('type=1')->value('GROUP_CONCAT(id)');
						//file_put_contents('/var/www/pro-audit-ct/map.txt','map1 的值为' . json_encode($map1, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
						// 如果有符合条件的订单
						if ($suboid1) {
							// 获取符合条件的照片数量
							$count_1 = db()->query("SELECT COUNT(*) as p FROM photos WHERE ycode = 45 AND status = 1 AND oid IN ({$suboid1})")['0']['p'];
							
							// 获取所有照片数量
							$count_all = db()->query("SELECT COUNT(*) as p FROM photos WHERE ycode = 45 AND oid IN ({$suboid1})")['0']['p'];
							//file_put_contents('/var/www/pro-audit-ct/map.txt', 'count_all 的值为: ' . var_export($count_all, true) . PHP_EOL, FILE_APPEND);

							if ($count_all !== 0){
								//file_put_contents('/var/www/pro-audit-ct/map.txt', mb_convert_encoding('count_all 的值为' . json_encode($count_all, JSON_UNESCAPED_UNICODE) . PHP_EOL, 'UTF-8', 'auto'), FILE_APPEND);
								// 插入记录
								$data[] = [
									'updatetime' => $currentDateTime,
									'place' => $place_value,
									'etime' => $formattedStartDate,
									'gdtype' => $gdtype_value,
									'station' => $station_value,
									'status_1' => $count_1,
									'status_all' => $count_all,
								];
							}
							
						} else {
							// 如果没有符合条件的订单，插入 count 为 0 的记录
							// $data[] = [
							// 	'updatetime' => $currentDateTime,
							// 	'place' => $place_value,
							// 	'etime' => $formattedStartDate,
							// 	'gdtype' => $gdtype_value,
							// 	'status' => 1,
							// 	'station' => $station_value,
							// 	'count' => 0,
							// ];
	
							// $data[] = [
							// 	'updatetime' => $currentDateTime,
							// 	'place' => $place_value,
							// 	'etime' => $formattedStartDate,
							// 	'gdtype' => $gdtype_value,
							// 	'status' => null,
							// 	'station' => $station_value,
							// 	'count' => 0,
							// ];
						}
					}
				}
			}

			 // 增加一天，继续下一次循环
			 $startDate->modify('+1 day');
		 }

		

		// 批量插入
		if ($data) {
			//file_put_contents('/var/www/pro-audit-ct/map.txt','data 的值为' . json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
			//db('face_detect_bk')->insertAll($data, false, true);  // 第二个参数是batch size，第三个参数是标识是否使用 insertIgnore

			db('face_detect_bk')->insertAll($data);
		}
	}

	public function get_face_correctity(){
		set_time_limit(0);
		$place = \Param::place();
		$station = \Param::station();
		$station2 = \Param::station(2);
		$office = \Param::office();
		$gdtype = (int)$this->user['gtype'];
		$fs7 = \Param::fs7($gdtype); //print_r($fs7);
                if ($gdtype == 0) {
                    $fs7[97][0] = 0;
                    $fs7[97][1] = '人员正脸照片';
                }
		$startTimeTotal = microtime(true);

		if($_POST){
			$pt = \Param::ps();
			$map = [];
			//$map['status'] = ['gt', 0];
			$map['gdtype'] = $gdtype;
			
			$gdtype && $map['type'] = 1;
			
			$start = I('start');
			$end = I('end');
			$start && $map['etime'] = ['egt', $start];
			$end && $map['etime'] = ['elt', $end.' 23:59:59'];
			$start && $end && $map['etime'] = [['egt', $start], ['elt', $end.' 23:59:59']];
			
			$ps = [$place, 'place', ''];
			
			$splace = $sstation = false;
			$_place = max(0, min(7, (int)I('place')));
			$_place && $splace = $_place;
			$_station = max(0, min(36, (int)I('station')));
			$_station && $sstation = $_station;
			
			$ut = (int)$this->user['type'];
			$uo = (int)$this->user['office'];

			//file_put_contents('/var/www/pro-audit-ct/rs-01.txt', json_encode($start, JSON_UNESCAPED_UNICODE));
			if($ut == 2){//所级
				$splace = $uo;
			}elseif($ut == 3){//站级
				unset($splace);
				$sstation = $uo;
			}
			
			if($splace){
				$ps = [$pt[$splace][1], 'station', $place[$splace]];
				$map['place'] = $splace;
			}
			if($sstation){
				$ps = [$office, 'office', $station2[$sstation][1]];
				$map['place'] = $station2[$sstation][2];
				$map['station'] = $sstation;
			}
			
			$ck = 'hgcounts_' . md5($gdtype.'~'.$start.'~'.$end.'~'.$splace.'~'.$sstation);
			if(I('clean')) Cache::set($ck, null);
			//$rs = Cache::get($ck);
			if(!$rs){
				$rs = [];
				$rs[0][0] = $ps[2] ?: '合格统计';
				foreach($ps[0] as $p => $pl){//管理所
					$n = $ps[1];
					$map[$n] = $p;
					$n = $$n;
					
					$z = db('orders')->where($map)->count();//每个管理所的总数、这里耗时不长
					//$z = 0;//每个管理所的总数、这里耗时不长
					
					// 写入到文件			
			        $orders1=$map;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	$result = file_put_contents('/home/www/2water.com/map.txt', $contentToWrite);

					$zm = 0;//每个管理所的母单总数
					if($sstation && !$z) continue;
					$rs[0][1] += $z;//所有管理所总数
					$rs[0][2] += $zm;//所有管理所母单总数
					$rs[$p][0] = $n[$p];
					$rs[$p][1] = $z;
					$rs[$p][2] = $zm;
					
					$startTime = microtime(true);
					foreach($fs7 as $i => $fs){//大类
						if ($i == 97) {
							file_put_contents('/var/www/pro-audit-ct/map.txt','map为:' . json_encode($map, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
							$count_all = db('face_detect_bk')->where($map)->value('status_all') ?? 0;
							file_put_contents('/var/www/pro-audit-ct/map.txt','count_all:' . json_encode($count_all, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
							$count_1 = db('face_detect_bk')->where($map)->value('status_1') ?? 0;
							file_put_contents('/var/www/pro-audit-ct/map.txt','count_1:' . json_encode($count_1, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

							//$c  = db()->query("SELECT COUNT(*) as p FROM photos WHERE ycode = 45 AND status = 1 AND oid IN ({$suboid})")['0']['p'];
							//$c5 = db()->query("SELECT COUNT(*) as p FROM photos WHERE ycode = 45 AND oid IN ({$suboid})")['0']['p'];

							$rs[0][5][$i] += $count_all;
							$rs[$p][5][$i] = $count_all;
							$rs[0][3][$i] += $count_1;
							$rs[$p][3][$i] = $count_1;
							file_put_contents('/var/www/pro-audit-ct/map.txt','$p:' . json_encode($p, JSON_UNESCAPED_UNICODE) .'$i:' . json_encode($i, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
							// $rs[0][6][$i] += $d;
							// $rs[$p][6][$i] =$d;
							// $rs[0][7][$i] += $d5;
							// $rs[$p][7][$i] = $d5;
						} 

						//$rs[0][3][$i] += $c;
						//$rs[$p][3][$i] = $c;
					}
					//$endTime = microtime(true);	
					// 计算运行时间并转换为毫秒
					//$executionTimeInMilliseconds = ($endTime - $startTime) * 1000;
					//file_put_contents('/var/www/pro-audit-ct/runtime.txt', $pl."--".$executionTimeInMilliseconds . PHP_EOL, FILE_APPEND);

					//$cz = db('orders')->where($map)->where("status = 1")->count();
					//$cz = 0;

				}
				//if(!$rs[0][1]) $rs = [[$rs[0][0], 0, 0, [], 0, [97=>0]]];
				//Cache::set($ck, $rs, 3);
			}
			// 写入到文件
			//$orders1=$rs;
			//$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
				// 写入到文件
			//$result = file_put_contents('/home/www/2water.com/rs.txt', $contentToWrite);
			// 写入到文件
			//$orders1=$fs7;
			//$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
				// 写入到文件
			$result = file_put_contents('/home/www/2water.com/fs7.txt', $contentToWrite);
			

			file_put_contents('/var/www/pro-audit-ct/rs.txt', json_encode($rs, JSON_UNESCAPED_UNICODE));
			file_put_contents('/var/www/pro-audit-ct/map.txt','$rs[0][3][97]:' . json_encode($rs[0][3][97], JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
			//$endTimeTotal = microtime(true);

			// 计算运行时间并转换为毫秒
			//$executionTimeInMillisecondsTotal = ($endTimeTotal - $startTimeTotal) * 1000;
			//file_put_contents('/var/www/pro-audit-ct/runtime.txt', "total--".$executionTimeInMillisecondsTotal . PHP_EOL, FILE_APPEND);

			//$this->rs=$rs;
			return $rs;
		}

		$tmp['place'] = $place;
		$tmp['station'] = $station2;

		$this->assign('title', '合格统计');
		$this->assign('tmp', $tmp);
		$this->assign('fs7', $fs7);//print_r($fs2);
        $placeKeys = array_merge([0], array_keys($place));
        $this->assign('placeKeys', $placeKeys);//print_r($fs2);
		return $this->fetch();
	}

	
	//重复统计
	public function repeat(){
		set_time_limit(0);
		$place = \Param::place();
		$station = \Param::station();
		$station2 = \Param::station(2);
		$office = \Param::office();
		$gdtype = (int)$this->user['gtype'];
		$fs2 = \Param::fs2(0, $gdtype);
		if($_POST){
			$pt = \Param::ps();
			$map = [];
			$map['status'] = ['gt', 0];
			$map['gdtype'] = $gdtype;
			
			$gdtype && $map['type'] = 1;
			
			$start = I('start');
			$end = I('end');
			$start && $map['etime'] = ['egt', $start];
			$end && $map['etime'] = ['elt', $end.' 23:59:59'];
			$start && $end && $map['etime'] = [['egt', $start], ['elt', $end.' 23:59:59']];
			
			$matching  =  (int)I('matching') or $matching = 0;
			$m = " where x = 1 and matching > 0";
			
			$ps = [$place, 'place', ''];
			
			$splace = $sstation = false;
			$stype = I('type');
			if(!$stype){
				$_place = max(0, min(7, (int)I('place')));
				$_place && $splace = $_place;
				$_station = max(0, min(36, (int)I('station')));
				$_station && $sstation = $_station;
			}
			
			$ut = (int)$this->user['type'];
			$uo = (int)$this->user['office'];
			if($ut == 2){//所级
				$splace = $uo;
			}elseif($ut == 3){//站级
				unset($splace);
				$sstation = $uo;
			}
			
			if($splace){
				$ps = [$pt[$splace][1], 'station', $place[$splace]];
				$map['place'] = $splace;
			}
			if($sstation){
				$ps = [$office, 'office', $station2[$sstation][1]];
				$map['place'] = $station2[$sstation][2];
				$map['station'] = $sstation;
			}
			
			$fs5 = \Param::fs5();//print_r($fs5);die;
			
			$ck = 'lkcounts_' . md5($gdtype.'~'.$start.'~'.$end.'~'.$matching.'~'.$splace.'~'.$sstation);
			if(I('clean')) Cache::set($ck, null);
			$rs = Cache::get($ck);
			$fs8 = \Param::fs9();
			$fs5=$fs8;
			//$fs5 = \Param::fs5();
			if(!$rs){
				$rs = [];
				$rs[0][0] = $ps[2] ?: '重复统计';
				foreach($ps[0] as $p => $pl){//管理所
					$n = $ps[1];
					$map[$n] = $p;
					$n = $$n;
					$z = db('orders')->where($map)->count();//每个管理所的总数
					if($sstation && !$z) continue;
					$rs[0][1] += $z;//所有管理所总数
					$rs[$p][0] = $n[$p];
					$rs[$p][1] = $z;
			$orders1=$fs5;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
			$result = file_put_contents('/home/www/2water.com/fs9.txt', $contentToWrite);

					foreach($fs2 as $i => $fs){//大类
						//$c = db('orders')->where($map)->where("id in(select oid from photos where code in(". implode(',', $fs5[$i]) .") and (id in(select pid from likes{$m}) or id in(select pid2 from likes{$m})))")->count();
						$fs5 = db('category')->where("pid ={$i}")->select();

						if ($fs5) {
							$gs = [];
							foreach ($fs5 as $item) { // 遍历 fs5 数组
   								 if (isset($item['scode'])) { // 检测当前字典中是否存在 'scode' 键
        											$gs[] = $item['scode']; // 把 'scode' 值添加到 gs 数组中
   												 }
										}
// 写入到文件
			$orders1=$gs;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	$result = file_put_contents('/home/www/2water.com/gsssss.txt', $contentToWrite);

// 写入到文件

    							// $fi 这个键存在于 $fs8 数组中
							$c = db('orders')->where($map)->where("id in(select oid from photos where code in(". implode(',', $gs) .") and (id in(select pid from likes {$m}) or id in(select pid2 from likes {$m})))")->count();

						} else {
   			 				// $fi 这个键不存在于 $fs8 数组中
							}

						$rs[0][2][$i] += $c;
						$rs[$p][2][$i] = $c;
					}
					$cz = db('orders')->where($map)->where("cs > 0")->count();
					$rs[0][3] += $cz;
					$rs[$p][3] = $cz;
				}
				if(!$rs[0][1]) $rs = [[$rs[0][0], 0, [], 0]];
				Cache::set($ck, $rs, 21600);
			}
// 写入到文件
			$orders1=$fs2;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	$result = file_put_contents('/home/www/2water.com/fs2.txt', $contentToWrite);
	            	


// 写入到文件
			$orders1=$rs;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	$result = file_put_contents('/home/www/2water.com/rs.txt', $contentToWrite);


			return $rs;
		}
		
		$tmp['place'] = $place;
		$tmp['station'] = $station2;
		
		
		$this->assign('title', '重复统计');
		$this->assign('tmp', $tmp);
		$this->assign('fs2', $fs2);//print_r($fs2);

        $placeKeys = array_merge([0], array_keys($place));
        $this->assign('placeKeys', $placeKeys);//print_r($fs2);
		return $this->fetch();
	}
	
	//图片处理统计
    public function opreports() {
        $this->assign('title', '图片统计');
        if($_POST){
            $type = $_POST['type'];
            $sdate = $_POST['sdate'];
            $edate = $_POST['edate'];

            list($sdate, $edate, $data) = $this->fetchReportData($sdate, $edate, $type);

            return ['sdate'=> $sdate, 'edate'=> $edate, 'data'=> $data];
        }
        return $this -> fetch();
    }

    //统计数据下载
    function opdownload(){

        $type = $_GET['type'];
        $sdate = $_GET['sdate'];
        $edate = $_GET['edate'];

        //下载这个表格了，在浏览器输出
        $start = str_replace('-', '', $sdate);
        $end = str_replace('-', '', $edate.' 23:59:59');
        list($sdate, $edate, $data) = $this->fetchReportData($sdate, $edate, $type);

        if ($sdate == '' && $edate == '') {
            $f = '';
        }else{
            $f = "({$sdate}-{$edate})";
        }
        $ext = $type == '0' ? '日' :'月';
        $filename = iconv('utf-8', 'gb2312', "工单统计{$f}{$ext}.xls");

        vendor('Classes.PHPExcel');
        $objPHPExcel = new \PHPExcel();


        //所站维保单位汇总统计
        //第一行数据
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit('A1', '日期')
            ->setCellValueExplicit('B1', '工单照片数量')
            ->setCellValueExplicit('C1', '审核照片数量')
            ->setCellValueExplicit('D1', '查重照片数量');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);

        $objPHPExcel->getActiveSheet()->setTitle('图片统计数据');
        $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray(['font' => ['bold' => true]]);

        $i = 2;
        foreach($data as $d) {
            $label = $d['label'];
            if($type == '2'){
                $label = substr($label, 0, 4) . '年'. substr($label, 4, 6) . '月';
            }
            $objPHPExcel->getActiveSheet()
                ->setCellValueExplicit('A'.$i, $label)
                ->setCellValueExplicit('B'.$i, $d['recd_num'])
                ->setCellValueExplicit('C'.$i, $d['detected_num'])
                ->setCellValueExplicit('D'.$i, $d['cp_num']);

            $i++;
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header("Content-Disposition:attachment;filename=\"{$filename}\"");
        header("Content-Transfer-Encoding:binary");

        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');
    }


    function bf($a, $b, $c = 2, $d = null){
		if($b == 0){
			if(!$d) return '';
			return 0;
		}
		return $a ? number_format(($a / $b) * 100, $c) . '%' : 0;
	}

	//导出excel
	public function out(){
		set_time_limit(0);
		$place = \Param::place();
		$station2 = \Param::station(2);
		$office = \Param::office();
		$gdtype = (int)$this->user['gtype'];
		$fs2 = \Param::fs2(0, $gdtype);//print_r($fs2);//die;
		$fs5 = \Param::fs5();//print_r($fs5);die;
		$fs7 = \Param::fs7($gdtype);//print_r($fs7);die;
		$map = [];
		$map['status'] = ['gt', 0];
		$map['gdtype'] = $gdtype;
		
		$gdtype && $map['type'] = 1;
		
		$start = I('start');
		$end = I('end');
		$start && $map['etime'] = ['egt', $start];
		$end && $map['etime'] = ['elt', $end.' 23:59:59'];
		$start && $end && $map['etime'] = [['egt', $start], ['elt', $end.' 23:59:59']];
		
		$splace = $sstation = false;
		$_place = max(0, min(7, (int)I('place')));
		$_place && $splace = $_place;
		$_station = max(0, min(36, (int)I('station')));
		$_station && $sstation = $_station;
			
		$ut = (int)$this->user['type'];
		$uo = (int)$this->user['office'];
		if($ut == 2){//所级
			$splace = $uo;
		}elseif($ut == 3){//站级
			unset($splace);
			$sstation = $uo;
		}
			
		if($splace){
			$map['place'] = $splace;
			$n = '_'.$place[$splace];
		}
		$map1 = $map;
		if($sstation){
			$map['place'] = $station2[$sstation][2];
			$map1['place'] = $station2[$sstation][2];
			$map1['station'] = $station2[$sstation][0];
			$n = '_'.$station2[$sstation][1];
		}
		$ggtype = 0;
		if (isset($_GET['ggtype'])) {
			$ggtype = $_GET['ggtype'];
		}
		vendor('Classes.PHPExcel');
        $objPHPExcel = new \PHPExcel();		
		//所站维保单位汇总统计
		//第一行数据
		/* $objPHPExcel->setActiveSheetIndex(0)
			->setCellValueExplicit('A1', '管理所')
			->setCellValueExplicit('B1', '管理站')
			->setCellValueExplicit('C1', '维保单位')
			->setCellValueExplicit('D1', '工单总数')
			->setCellValueExplicit('E1', '合格工单数')
			->setCellValueExplicit('F1', '合格率')
			->setCellValueExplicit('G1', '重复工单数')
			->setCellValueExplicit('H1', '重复率')
			->setCellValueExplicit('I1', '申诉工单')
			->setCellValueExplicit('J1', '申诉通过'); */
		if ($ggtype == 2) {
			$objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueExplicit('A1', '管理所')
                        ->setCellValueExplicit('B1', '工单数')
                        ->setCellValueExplicit('C1', '合格率')
                        ->setCellValueExplicit('D1', '管理站')
                        ->setCellValueExplicit('E1', '维保单位')
                        ->setCellValueExplicit('F1', '工单总数')
                        ->setCellValueExplicit('G1', '合格工单数')
                        ->setCellValueExplicit('H1', '合格率')
                        ->setCellValueExplicit('I1', '申诉工单')
                        ->setCellValueExplicit('J1', '申诉通过');
		} else {
		 $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueExplicit('A1', '管理所')
                        ->setCellValueExplicit('B1', '工单数')
                        ->setCellValueExplicit('C1', '合格率')
			->setCellValueExplicit('D1', '重复工单数')
                        ->setCellValueExplicit('E1', '重复率')
                        ->setCellValueExplicit('F1', '管理站')
                        ->setCellValueExplicit('G1', '维保单位')
                        ->setCellValueExplicit('H1', '工单总数')
                        ->setCellValueExplicit('I1', '合格工单数')
                        ->setCellValueExplicit('J1', '合格率')
                        ->setCellValueExplicit('K1', '重复工单数')
                        ->setCellValueExplicit('L1', '重复率')
                        ->setCellValueExplicit('M1', '申诉工单')
                        ->setCellValueExplicit('N1', '申诉通过');
		}
		$objPHPExcel->getActiveSheet()->setTitle('管理所(站)');
		$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray(['font' => ['bold' => true]]);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12);

		$i = 2;
		$zc = $zd = $ze = $zf = $zg = 0;
		$cArr = db('orders')->field('count(*) as num, station, office')->where($map)->group('station,office')->select();
// 写入到文件
			$orders1=$cArr;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	$result = file_put_contents('/home/www/2water.com/map2.txt', $contentToWrite);

//		 var_export($office);echo "\n\n\n</br></br></br>";
//		var_export($station2);echo "\n\n\n</br></br></br>";
		$dArr = db('orders')->field('count(*) as num, station, office')->where($map)->where('status=1')->group('station,office')->select();
		$eArr = db('orders')->field('count(*) as num, station, office')->where($map)->where('cs > 0')->group('station,office')->select();
		$fArr = db('orders')->field('count(*) as num, station, office')->where($map)->where('appeal > 0')->group('station,office')->select();
		$gArr = db('orders')->field('count(*) as num, station, office')->where($map)->where('appeal = 5')->group('station,office')->select();
		$cData = $dData = $eData = $fData = $gData = [];
		foreach($cArr as $ck => $cv) {
		   $cData[$cv['station']][$cv['office']] = $cv['num'];
			//$cData[$cv['office']][$cv['station']] = $cv['num'];
		}
		 foreach($dArr as $ck => $cv) {
                   $dData[$cv['station']][$cv['office']] = $cv['num'];
			//$dData[$cv['office']][$cv['station']] = $cv['num'];
                }
		 foreach($eArr as $ck => $cv) {
                   $eData[$cv['station']][$cv['office']] = $cv['num'];
		//	$eData[$cv['office']][$cv['station']] = $cv['num'];
                }
		 foreach($fArr as $ck => $cv) {
                   $fData[$cv['station']][$cv['office']] = $cv['num'];
		//	$fData[$cv['office']][$cv['station']] = $cv['num'];
                }
		 foreach($gArr as $ck => $cv) {
                   $gData[$cv['station']][$cv['office']] = $cv['num'];
			//$gData[$cv['office']][$cv['station']] = $cv['num'];
                }
//		var_export($map);exit();
//        var_export($station2);echo "\n\n\n</br></br></br>";exit;
// var_export($office);exit();
		foreach($station2 as $v){
			if($splace){
				if($splace != $v[2]) continue;
			}
			if($sstation){
				if($sstation != $v[0]) continue;
			}
			$map['station'] = $v[0];
			$zs = 0;
			$begin_i = $i;
			foreach($office as $k=>$o){
				$map['office'] = $k;
				$c = $d = $e = $f = $g = 0;
				if (isset($cData[$v[0]][$k])) {
				    $c = $cData[$v[0]][$k];
				}
				//$c = db('orders')->where($map)->count();
				$zc += $c;$zs += $c;
				if(!$c)continue;
				if (isset($dData[$v[0]][$k])) {
                                    $d = $dData[$v[0]][$k];
                                }
				//$d = db('orders')->where($map)->where('status = 1')->count();
				$zd += $d;
				if (isset($eData[$v[0]][$k])) {
                                    $e = $eData[$v[0]][$k];
                                }
				//$e = db('orders')->where($map)->where('cs > 0')->count();
				$ze += $e;
				if (isset($fData[$v[0]][$k])) {
                                    $f = $fData[$v[0]][$k];
                                }
				//$f = db('orders')->where($map)->where('appeal > 0')->count();
				$zf += $f;
				if (isset($gData[$v[0]][$k])) {
                                    $g = $gData[$v[0]][$k];
                                }
				//$g = db('orders')->where($map)->where('appeal = 5')->count();
				$zg += $g;
				if ($ggtype == 2) {
					 $objPHPExcel->getActiveSheet()
                                        ->setCellValueExplicit('A'.$i, $v[3])
                                        ->setCellValueExplicit('D'.$i, $v[1])
                                        ->setCellValueExplicit('E'.$i, $o)
                                        ->setCellValueExplicit('F'.$i, $c)
                                        ->setCellValueExplicit('G'.$i, $d)
                                        ->setCellValueExplicit('H'.$i, $this->bf($d, $c))
                                        ->setCellValueExplicit('I'.$i, $f)
                                        ->setCellValueExplicit('J'.$i, $g);
				} else {
					 $objPHPExcel->getActiveSheet()
                                        ->setCellValueExplicit('A'.$i, $v[3])
                                        ->setCellValueExplicit('F'.$i, $v[1])
                                        ->setCellValueExplicit('G'.$i, $o)
                                        ->setCellValueExplicit('H'.$i, $c)
                                        ->setCellValueExplicit('I'.$i, $d)
                                        ->setCellValueExplicit('J'.$i, $this->bf($d, $c))
                                        ->setCellValueExplicit('K'.$i, $e)
                                        ->setCellValueExplicit('L'.$i, $this->bf($e, $c))
                                        ->setCellValueExplicit('M'.$i, $f)
                                        ->setCellValueExplicit('N'.$i, $g);
				}
				$hs[$v[2]][] = $i;
				$hs1[$v[0]][] = $i;
				$i++;//表格是从2开始的
			}
			if(!$zs){
				if($ggtype == 2) {
					 $objPHPExcel->getActiveSheet()
                                        ->setCellValueExplicit('A'.$i, $v[3])
                                        ->setCellValueExplicit('D'.$i, $v[1])
                                        ->setCellValueExplicit('E'.$i, '-')
                                        ->setCellValueExplicit('F'.$i, 0)
                                        ->setCellValueExplicit('G'.$i, 0)
                                        ->setCellValueExplicit('H'.$i, 0)
                                        ->setCellValueExplicit('I'.$i, 0)
                                        ->setCellValueExplicit('J'.$i, 0);
				} else {
					 $objPHPExcel->getActiveSheet()
                                        ->setCellValueExplicit('A'.$i, $v[3])
                                        ->setCellValueExplicit('F'.$i, $v[1])
                                        ->setCellValueExplicit('G'.$i, '-')
                                        ->setCellValueExplicit('H'.$i, 0)
                                        ->setCellValueExplicit('I'.$i, 0)
                                        ->setCellValueExplicit('J'.$i, 0)
                                        ->setCellValueExplicit('K'.$i, 0)
                                        ->setCellValueExplicit('L'.$i, 0)
                                        ->setCellValueExplicit('M'.$i, 0)
                                        ->setCellValueExplicit('N'.$i, 0);
				}
				$hs[$v[2]][] = $i;
				$i++;//表格是从2开始的
			}
		    //for($ti = $begin_i; $ti<$i;$ti++) {
                          //   $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$ti, $zs);
                        //}
		}
		$hs_index = 2;

		foreach($hs as $h){
			$objPHPExcel->getActiveSheet()->mergeCells('A'. $h[0] .':A' . end($h));
			$objPHPExcel->getActiveSheet()->getStyle('A'. $h[0] .':A' . end($h))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('B'. $h[0] .':B' . end($h));
                        $objPHPExcel->getActiveSheet()->getStyle('B'. $h[0] .':B' . end($h))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			if ($ggtype == 0) {
				$objPHPExcel->getActiveSheet()->mergeCells('D'. $h[0] .':D' . end($h));
                        $objPHPExcel->getActiveSheet()->getStyle('D'. $h[0] .':D' . end($h))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			}
			$hs_all = 0;
			$hg_all = 0;
			$cf_all = 0;
			for($hs_tt = $h[0]; $hs_tt <= end($h); $hs_tt++) 
			{
			   if($ggtype==0) {
				$hs_all +=  (int)$objPHPExcel->getActiveSheet()->getCell('H' . $hs_tt)->getValue();
				 $hg_all +=  (int)$objPHPExcel->getActiveSheet()->getCell('I' . $hs_tt)->getValue();
			   } else {
				$hs_all +=  (int)$objPHPExcel->getActiveSheet()->getCell('F' . $hs_tt)->getValue();
		           	$hg_all +=  (int)$objPHPExcel->getActiveSheet()->getCell('G' . $hs_tt)->getValue();
			   }
			   $cf_all +=  (int)$objPHPExcel->getActiveSheet()->getCell('K' . $hs_tt)->getValue();
			}
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' .  $h[0], $hs_all);
			if ($ggtype == 0){
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('D' .  $h[0], $cf_all);
			}
			if (!$hs_all) {
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('C' .  $h[0], '0%');
				 if ($ggtype == 0) $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' .  $h[0], '0%');
			} else {
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('C' .  $h[0], round($hg_all / $hs_all, 4) * 100 . '%');
				if ($ggtype == 0) $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' .  $h[0], round($cf_all / $hs_all, 4) * 100 . '%');
			}
			$objPHPExcel->getActiveSheet()->mergeCells('C'. $h[0] .':C' . end($h));
                        $objPHPExcel->getActiveSheet()->getStyle('C'. $h[0] .':C' . end($h))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
 			if ($ggtype == 0) $objPHPExcel->getActiveSheet()->mergeCells('E'. $h[0] .':E' . end($h));
                        if ($ggtype == 0) $objPHPExcel->getActiveSheet()->getStyle('E'. $h[0] .':E' . end($h))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$hs_index++;
		}
		foreach($hs1 as $h){
			$objPHPExcel->getActiveSheet()->mergeCells('F'. $h[0] .':F' . end($h));
			$objPHPExcel->getActiveSheet()->getStyle('F'. $h[0] .':F' . end($h))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		}

			if ($ggtype == 2) {
				 $objPHPExcel->getActiveSheet()
                                ->setCellValueExplicit('A'.$i, '合计')
                                ->setCellValueExplicit('D'.$i, '')
                                ->setCellValueExplicit('E'.$i, '')
                                ->setCellValueExplicit('F'.$i, $zc)
                                ->setCellValueExplicit('G'.$i, $zd)
                                ->setCellValueExplicit('H'.$i, $this->bf($zd, $zc))
                                ->setCellValueExplicit('I'.$i, $zf)
                                ->setCellValueExplicit('J'.$i, $zg);
			} else {
				 $objPHPExcel->getActiveSheet()
                                ->setCellValueExplicit('A'.$i, '合计')
                                ->setCellValueExplicit('F'.$i, '')
                                ->setCellValueExplicit('G'.$i, '')
                                ->setCellValueExplicit('H'.$i, $zc)
                                ->setCellValueExplicit('I'.$i, $zd)
                                ->setCellValueExplicit('J'.$i, $this->bf($zd, $zc))
                                ->setCellValueExplicit('K'.$i, $ze)
                                ->setCellValueExplicit('L'.$i, $this->bf($ze, $zc))
                                ->setCellValueExplicit('M'.$i, $zf)
                                ->setCellValueExplicit('N'.$i, $zg);
			}
		if ($ggtype == 2){
			$objPHPExcel->getActiveSheet()->mergeCells('A'. $i .':B' . $i);
                $objPHPExcel->getActiveSheet()->getStyle('A1:J' . $i)->applyFromArray(['borders' => ['allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]]]);
                $objPHPExcel->getActiveSheet()->getStyle('A'. $i .':J' . $i)->applyFromArray(['font' => ['bold' => true]]);
		} else {
			$objPHPExcel->getActiveSheet()->mergeCells('A'. $i .':B' . $i);
                $objPHPExcel->getActiveSheet()->getStyle('A1:N' . $i)->applyFromArray(['borders' => ['allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]]]);
                $objPHPExcel->getActiveSheet()->getStyle('A'. $i .':N' . $i)->applyFromArray(['font' => ['bold' => true]]);
		}

		
		
		
		//按分类
		//第一行数据
		$letter = range("D", "Z");$ls = [];
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(1)
			->setCellValueExplicit('A1', '管理所')
			->setCellValueExplicit('B1', '管理站')
			->setCellValueExplicit('C1', '维保单位');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		foreach(array_values($fs2) as $fi => $fs){
			$zm = $letter[$fi];
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($zm.'1', $fs);
			$objPHPExcel->getActiveSheet()->getColumnDimension($zm)->setWidth(15);
			$ls[$fi+1] = $zm;
		}

		$objPHPExcel->getActiveSheet()->setTitle('合格率按分类');
		$objPHPExcel->getActiveSheet()->getStyle('A1:'. $zm .'1')->applyFromArray(['font' => ['bold' => true]]);
		//print_r([$fs2, $ls]);die;
		
		$i = 2;
		$m = '';//" where x = 1 and matching > " . config('matching');
		$cfs = [];

                //$cArr = db('orders')->field('count(*) as num, station, office')->where($map)->group('station')->group('office')->select();
		$fsArr = $fsArr2 = [];
		unset($map['station'], $map['office']);
		$fs8 = \Param::fs8();
		foreach($fs7 as $fi => $fs){
                     $fsArr[$fi] = db('orders')->field('count(*) as num, station, office')->where($map)->where("id in(select oid from order_adopt where cate={$fi})")->group('station,office')->select();
                     // 写入到文件
	$orders1=$fi;
	            $contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                          // 写入到文件
	            $result = file_put_contents('/home/www/2water.com/fi.txt', $contentToWrite);
        
		    if (isset($fs8[$fi])) {
    			// $fi 这个键存在于 $fs8 数组中
			$fsArr2[$fi] = db('orders')->field('count(*) as num, station, office')->where($map)->where("id in(select oid from photos where code in(". implode(',', $fs8[$fi]) .") and (id in(select pid from likes{$m}) or id in(select pid2 from likes{$m})))")->group('station,office')->select();
                
			} else {
   			 // $fi 这个键不存在于 $fs8 数组中
			}
		     }
		$fsData = $fsData2 = $cData = $cmData = [];
		foreach($fsArr as $fkk => $fvv) {
			foreach($fvv as $fvvv) {
			     $fsData[$fkk][$fvvv['station']][$fvvv['office']] = $fvvv['num'];
			}
//		   $fsData[$fkk][$fvv['station']][$fvv['office']] = $fvv['num'];
//		   var_export($fvv);
//		   var_export($fsData);exit;
		}
		foreach($fsArr2 as $fkk => $fvv) {
		 	foreach($fvv as $fvvv) {
                             $fsData2[$fkk][$fvvv['station']][$fvvv['office']] = $fvvv['num'];
                        }
                   //$fsData2[$fkk][$fvv['station']][$fvv['office']] = $fvv['num'];
                }
		$cArr = db('orders')->field('station, count(*) as num, office')->where($map)->where('type=0')->group('station,office')->select();
		foreach($cArr as $ckk => $cvv) {
		   $cData[$cvv['station']][$cvv['office']] = $cvv['num'];
		}
		$cmArr = db('orders')->field('station, count(*) as num, office')->where($map)->group('station,office')->select();
                foreach($cmArr as $ckk => $cvv) {
                   $cmData[$cvv['station']][$cvv['office']] = $cvv['num'];
                }
		//var_export($fsArr);exit;
		foreach($station2 as $v){
			if($splace){
				if($splace != $v[2]) continue;
			}
			if($sstation){
				if($sstation != $v[0]) continue;
			}
			$map['station'] = $v[0];
			$zs = 0;
			foreach($office as $k=>$o){
				$map['office'] = $k;
				$c = $cm = 0;
				if (isset($cmData[$v[0]][$k])) {
				    $c = $cmData[$v[0]][$k];
				}
				//$c = db('orders')->where($map)->count();
				$zs += $c;
				//$cm = $gdtype ? 0 : db('orders')->where($map)->where('type=0')->count();
				if (!$gdtype && isset($cData[$v[0]][$k])) {
				   $cm = $cData[$v[0]][$k];
				}	
				if(!$c)continue;
				$objPHPExcel->getActiveSheet()
					->setCellValueExplicit('A'.$i, $v[3])
					->setCellValueExplicit('B'.$i, $v[1])
					->setCellValueExplicit('C'.$i, $o);
				$fii = 0;
				foreach($fs7 as $fi => $fs){
					$fii ++;
					$z = $fs[0] ? $c - $cm : $cm;
					//$hg = db('orders')->where($map)->where("id in(select oid from order_adopt where cate={$fi})")->count();
					$hg = $cf = 0;
					if (isset($fsData[$fi][$v[0]][$k])) {
					    $hg = $fsData[$fi][$v[0]][$k];
					}
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($ls[$fii].$i, $this->bf($hg, $z));
					//$cf = db('orders')->where($map)->where("id in(select oid from photos where code in(". implode(',', $fs5[$fi]) .") and (id in(select pid from likes{$m}) or id in(select pid2 from likes{$m})))")->count();
					if (isset($fsData2[$fi][$v[0]][$k])) {
                                            $cf = $fsData2[$fi][$v[0]][$k];
                                        }
					$cfs[$ls[$fii].$i] = $this->bf($cf, $z);
				}
				$hs[$v[2]][] = $i;
				$hs1[$v[0]][] = $i;
				$i++;//表格是从2开始的
			}
			if(!$zs){
				$objPHPExcel->getActiveSheet()
					->setCellValueExplicit('A'.$i, $v[3])
					->setCellValueExplicit('B'.$i, $v[1])
					->setCellValueExplicit('C'.$i, '-');
				foreach(array_values($fs2) as $fi => $fs){
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($ls[$fi+1].$i, '-');
				}
				$hs[$v[2]][] = $i;
				$i++;//表格是从2开始的
			}
		}
		foreach($hs as $h){
			$objPHPExcel->getActiveSheet()->mergeCells('A'. $h[0] .':A' . end($h));
			$objPHPExcel->getActiveSheet()->getStyle('A'. $h[0] .':A' . end($h))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		}
		foreach($hs1 as $h){
			$objPHPExcel->getActiveSheet()->mergeCells('B'. $h[0] .':B' . end($h));
			$objPHPExcel->getActiveSheet()->getStyle('B'. $h[0] .':B' . end($h))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		}
		
		
		$objClonedWorksheet = clone $objPHPExcel->getSheet(1);
		$objClonedWorksheet->setTitle('重复率按分类');
		foreach($cfs as $ci => $cv){
			$objClonedWorksheet->setCellValueExplicit($ci, $cv);
		}
		$objPHPExcel->addSheet($objClonedWorksheet);
		$objPHPExcel->setActiveSheetIndex(0);



		
		$totoalresult = [];
		$places = (int)I('place') === 0 ? range(1, 7) : [(int)I('place')];

		file_put_contents('/var/www/pro-audit-ct/map.txt','places 的值为' . json_encode($places, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
		foreach ($places as $place) {
			$myparams = [
				'start' => $start,
				'end' => $end,
				'place' => $place,
				'station' => (int)I('place') === 0 ? null : (int)I('station'),
			];
			// 调用改造后的 get_face_nonpost 方法
			$totoalresult[] = $this->get_face_nonpost($myparams);
		}
		//$getresult=$totoalresult[0]
		file_put_contents('/var/www/pro-audit-ct/map.txt','totoalresult 为' . json_encode($totoalresult, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

		//file_put_contents('/var/www/pro-audit-ct/runtime.txt', $pl."--".$executionTimeInMilliseconds . PHP_EOL, FILE_APPEND);




		$objClonedWorksheet = clone $objPHPExcel->getSheet(1);
		
		$objClonedWorksheet->setTitle('人脸合格率按分类');
		foreach($cfs as $ci => $cv){
			$objClonedWorksheet->setCellValueExplicit($ci, $cv);
		}
		//$rsArray= file_get_contents("/home/www/2water.com/rs.txt");
		//$rsArray = json_decode($rsArray, true);  // 第二个参数 true 会将 JSON 转换为关联数组
		$objClonedWorksheet->removeColumn('C'); 
		$objClonedWorksheet->removeColumn('C'); 
		$objClonedWorksheet->removeColumn('C'); 
		$objClonedWorksheet->removeColumn('C'); 
		$objClonedWorksheet->removeColumn('C'); 
		$objClonedWorksheet->removeColumn('C'); 
		$objClonedWorksheet->removeColumn('C'); 	
		$objClonedWorksheet->removeColumn('C'); 
		$objClonedWorksheet->removeColumn('C'); 
		$objClonedWorksheet->removeColumn('C'); 
		$objClonedWorksheet->removeColumn('C'); 
		$objClonedWorksheet->removeColumn('C'); 
		$objClonedWorksheet->removeColumn('C'); 
		$objClonedWorksheet->removeColumn('C'); 
		$objClonedWorksheet->removeColumn('A'); 
		$objClonedWorksheet->removeColumn('A'); 
		

		





		// 遍历合并单元格的范围（假设 $hs 是一个二维数组，表示合并的行范围）
foreach ($hs as $h) {
    // 确保范围是有效的并且使用 $objClonedWorksheet
    $startRow = $h[0];  // 起始行
    $endRow = end($h);  // 结束行
    
    // 使用 unmergeCells() 方法取消 A 列的合并单元格
    $objClonedWorksheet->unmergeCells('A' . $startRow . ':A' . $endRow);
}		

			//if ( (int)I('place') === 0){
					// 要填充的数据
				$dataToFillA = [
					'管理所',
					];
					
					// 遍历 $totoalresult 数组
					foreach ($totoalresult as $j => $value) {
						// 如果 $value 是一个数组，继续遍历
						foreach ($value as $k => $subValue) {
							// 如果是 $value 数组中的最后一个元素，跳过
							if ($k == 0) {
								continue;
							}
							
							// 如果 $k 为 0，且 $value[0]['0'] 存在
							if (isset($value[0]['0'])) {
								$dataToFillA[] = $value[0]['0'];
								
							} else {
								// 如果不是 0，添加 null
								$dataToFillA[] = null;
							}
						}
					}


					$fillColumn = 'A';
		
		
					$startRow = 1;
		
					foreach ($dataToFillA as $index => $value) {
						$row = $startRow + $index; // 计算当前行号
						$objClonedWorksheet->setCellValue($fillColumn . $row, $value); // 设置单元格内容
					}

					$number = 2;
					// 遍历 $totoalresult 数组
					foreach ($totoalresult as $j => $value) {
						// 获取当前需要填充的值
						$value_merge = $objClonedWorksheet->getCell('A'.$number)->getValue();
						
						// 合并 A列，从 $number 行开始，合并到 $number + count($value) - 1 行
						$objClonedWorksheet->mergeCells('A'.$number.':A'.($number + count($value) - 1));
						
						// 设置合并后的第一个单元格的值
						$objClonedWorksheet->setCellValue('A'.$number, $value_merge);
						
						// 更新 $number 为下一个区域的起始行
						$number += count($value);
					}


					


					$dataToFillB = [
						'管理站'
						];
					// 遍历 $totoalresult 数组
					foreach ($totoalresult as $j => $value) {
						// 如果 $value 是一个数组，继续遍历
						foreach ($value as $k => $subValue) {
							// 如果 $subValue 存在 '0' 键，添加到 $dataToFillB 数组
							if ($k!==0 && isset($subValue['0'])) {
								$dataToFillB[] = $subValue['0'];
							}
						}
					}
						
						$fillColumn = 'B';
			
			
						$startRow = 1;
			
						foreach ($dataToFillB as $index => $value) {
							$row = $startRow + $index; // 计算当前行号
							$objClonedWorksheet->setCellValue($fillColumn . $row, $value); // 设置单元格内容
						}


					
		
						$dataToFillC = [
							'人脸准确率',
						];
						
						// 遍历 $totoalresult 数组
						foreach ($totoalresult as $j => $value) {
							// 如果 $value 是一个数组，继续遍历
							foreach ($value as $k => $subValue) {
								if ($k==0){
									continue;
								}

								// 检查 $subValue['5']['97'] 和 $subValue['3']['97'] 是否存在
								if (isset($subValue['5']['97']) && isset($subValue['3']['97'])) {
									// 计算准确率或设置为 '无数据'
									$dataToFillC[] = $subValue['5']['97'] != 0 ? number_format($subValue['3']['97'] * 100 / $subValue['5']['97'], 2) . '%' : '无数据';
								} else {
									// 如果缺少必要的键，添加 '无数据'
									$dataToFillC[] = '无数据';
								}
							}
						}
				
						$fillColumn = 'C';
				
				
						$startRow = 1;
				
						foreach ($dataToFillC as $index => $value) {
							$row = $startRow + $index; // 计算当前行号
							$objClonedWorksheet->setCellValue($fillColumn . $row, $value); // 设置单元格内容
						}
	
		$objPHPExcel->addSheet($objClonedWorksheet);
		$objPHPExcel->setActiveSheetIndex(0);

		

		//下载这个表格了，在浏览器输出
		$start = str_replace('-', '', $start);
		$end = str_replace('-', '', $end);
		$filename = iconv('utf-8', 'gb2312', "工单统计(". \Param::gdtypes()[$gdtype] ."){$n}({$start}-{$end}).xls");
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");;
		header("Content-Disposition:attachment;filename=\"{$filename}\"");
		header("Content-Transfer-Encoding:binary");
		
		$objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
		$objWriter->save('php://output');
	}

    /**
     * @param $sdate
     * @param $edate
     * @param $type
     * @return array
     */
    private function fetchReportData($sdate, $edate, $type)
    {
        if (!isset($sdate)) {
            $sdate = '';
        }
        if (!isset($edate)) {
            $edate = '';
        }
        if ($sdate == '' && $edate == '') {
            $edate = date('Y-m-d');
            $sdate = date("Y-m-d", strtotime("-3 months"));
        }
        $cond = " ";
        if ($sdate != '') {
            $cond .= " and cdate >= '{$sdate}'";
        }

        if ($edate != '') {
            $cond .= " and cdate <= '{$edate}'";
        }
        if ($type == '0') {
            $data = db("op_reports")->where("1=1" . $cond)
                ->field("cdate as label, detected_num, recd_num, detected_num, cp_num")->order('cdate desc')->select();
            //->field("cdate as label, detected_num, recd_num, detected_num, cp_num")->order('cdate desc')->limit(10)->select();
        } elseif ($type == '1') {
            $data = db("op_reports")->where("1=1" . $cond)
                ->field("DATE_FORMAT(cdate,'%Y%u') label,sum(cp_num) cp_num, sum(recd_num) recd_num, sum(detected_num) detected_num")->group('label')->order("label desc")->select();

        } elseif ($type == '2') {
            $data = db("op_reports")->where("1=1" . $cond)
                ->field("DATE_FORMAT(cdate,'%Y%m') label,sum(cp_num) cp_num, sum(recd_num) recd_num, sum(detected_num) detected_num")->group('label')->order("label desc")->select();

        }
        $data = array_reverse($data);
        return array($sdate, $edate, $data);
    }

}
