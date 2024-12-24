<?php 
namespace app\controller;
use Think\Controller;
use think\Cache;

class Facedetect extends Common{
	
	function movePhotosToFaceDetect() {
		// 获取当前时间，确保每天只执行一次
		$currentDate = date('Y-m-d');
		
		// 获取distinct值
		$place = db('orders')->distinct(true)->column('place');
		$gdtype = db('orders')->distinct(true)->column('gdtype');
		$station = db('orders')->distinct(true)->column('station');

		// 构建查询条件
		$map1 = [];
		$map1['status'] = ['gt', 0];

		$currentDateTime = date('Y-m-d H:i:s');

		// 定义插入的数据数组
		$data = [];

		foreach ($place as $place_value) {
			$map1['place'] = $place_value;
			
			foreach ($gdtype as $gdtype_value) {
				$map1['gdtype'] = $gdtype_value;
				
				foreach ($station as $station_value) {
					$map1['station'] = $station_value;
					$map1['etime'] = [['egt', '2024-10-30'], ['elt', '2024-10-31 23:59:59']];
					
					// 获取符合条件的订单ID列表
					$suboid1 = db('orders')->where($map1)->where('type=1')->value('GROUP_CONCAT(id)');
					
					// 如果有符合条件的订单
					if ($suboid1) {
						// 获取符合条件的照片数量
						$count_1 = db()->query("SELECT COUNT(*) as p FROM photos WHERE ycode = 45 AND status = 1 AND oid IN ({$suboid1})")['0']['p'];
						
						// 获取所有照片数量
						$count_all = db()->query("SELECT COUNT(*) as p FROM photos WHERE ycode = 45 AND oid IN ({$suboid1})")['0']['p'];

						// 插入记录
						$data[] = [
							'updatetime' => $currentDateTime,
							'place' => $place_value,
							'etime' => '2024-10-30 23:59:59',
							'gdtype' => $gdtype_value,
							'status' => 1,
							'count' => $count_1,
						];

						$data[] = [
							'updatetime' => $currentDateTime,
							'place' => $place_value,
							'etime' => '2024-10-30 23:59:59',
							'gdtype' => $gdtype_value,
							'status' => null,
							'count' => $count_all,
						];
					} else {
						// 如果没有符合条件的订单，插入 count 为 0 的记录
						$data[] = [
							'updatetime' => $currentDateTime,
							'place' => $place_value,
							'etime' => '2024-10-30 23:59:59',
							'gdtype' => $gdtype_value,
							'status' => 1,
							'count' => 0,
						];

						$data[] = [
							'updatetime' => $currentDateTime,
							'place' => $place_value,
							'etime' => '2024-10-30 23:59:59',
							'gdtype' => $gdtype_value,
							'status' => null,
							'count' => 0,
						];
					}
				}
			}
		}

		// 批量插入
		if ($data) {
			db('face_detect')->insertAll($data);
		}

		$map1['gdtype'] = 0;
		$map1['etime'] = [['egt', '2024-10-30'], ['elt', '2024-11-30'.' 23:59:59']];
		$map1['place'] = 1;

		// echo "<script>console.log('222222222222222222222222222');</script>";
		// 查询符合条件的订单ID列表
		file_put_contents('/var/www/pro-audit-ct/map.txt','map1  is ' . json_encode($map1, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
		
		if ($suboid && $suboid[0]['suboid']) {
			$suboidList = $suboid[0]['suboid'];
	
			// 第一个查询：符合条件的状态为1的照片
			$sql1 = "SELECT * FROM photos WHERE ycode = 45 AND status = 1 AND oid IN ({$suboidList})";
			$photos1 = Db::query($sql1);
	
			if ($photos1) {
				foreach ($photos1 as $photo) {
					// 检查 face_detect 表中是否已有该照片
					$exists = Db::table('face_detect')->where('id', $photo['id'])->count();
					
					if (!$exists) { // 如果记录不存在，则插入
						Db::table('face_detect')->insert($photo);
					}
				}
			}
	
			// 第二个查询：符合条件的所有照片
			$sql2 = "SELECT * FROM photos WHERE ycode = 45 AND oid IN ({$suboidList})";
			$photos2 = Db::query($sql2);
	
			if ($photos2) {
				foreach ($photos2 as $photo) {
					// 检查 face_detect 表中是否已有该照片
					$exists = Db::table('face_detect')->where('id', $photo['id'])->count();
					
					if (!$exists) { // 如果记录不存在，则插入
						Db::table('face_detect')->insert($photo);
					}
				}
			}
	
			// 日志记录
			file_put_contents('/path/to/log/move_photos_log.txt', "Moved photos on {$currentDate}: " . count($photos1) . " (status = 1), " . count($photos2) . " (all photos) \n", FILE_APPEND);
		} else {
			// 如果没有符合条件的订单，写入日志
			file_put_contents('/path/to/log/move_photos_log.txt', "No orders found for {$currentDate} \n", FILE_APPEND);
		}
   

}
