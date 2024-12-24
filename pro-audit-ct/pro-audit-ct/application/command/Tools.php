<?
namespace app\Command;
use think\Db;
use think\console\Command;
use think\console\input;
use think\console\output;

class Tools extends Command
{

	protected function configure()
	{
		$this->setName('Tools')->setDescription('工具 :)')
			->addArgument('param1')->addArgument('param2')	//增加2个参数
			->addArgument('param3')->addArgument('param4');	//增加2个参数
		//php think Tools[ 参数1 参数2 --ignoreTime]
	}

	protected function execute(Input $input, Output $output)
	{
		set_time_limit(0);
		ignore_user_abort(1);
		ini_set('memory_limit', '-1');
		
		$action = $input->getArgument('param1');
		$param2 = $input->getArgument('param2');
		$param3 = $input->getArgument('param3');
		$param4 = $input->getArgument('param4');
		$params = [$param2, $param3, $param4];
		$this->$action($params);
		
		print("ok");
	}
	

	protected function scan_received_files(){

		// 定义要遍历的目录
		$directory = '/data/2water/source/receive';

		// 遍历目录
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));

		foreach ($iterator as $file) {
		    // 检查是否为JSON文件
		    if ($file->isFile() && $file->getExtension() === 'json') {
		    	$receivePos = strpos($file, 'receive/');
				$startPos = $receivePos + 8; // 'receive/' 长度为 8
				$length = 6;

				// 使用 substr 获取子目录
				$yearDirectory = substr($file, $startPos, $length);


		    	if ($yearDirectory != '202408'){
		    		continue;
		    	}

		    	$filepath = substr($file, $startPos);

		    	db('receives')->insert(['filepath'=>$filepath, 'status'=>10]);



		    }
		}


	}






    protected function saveapidata1()
{
    lg('---开始保存接口数据');
    $jds = db('receives')->where('status=10')->group('filepath')->order('id')->select();
    if (!$jds) {
        lg('---保存接口数据完成，无数据');
        return -1;
    }

    $gdtypes = \Param::gdtypes(1);
    $place = \Param::station(2);
    $stations = \Param::station(1);
    $office = \Param::office(1);
    $ycodes = \Param::ycodes(1, '');
    $ps = 0;
    $created_at = $start_at = date('Y-m-d H:i:s');

    foreach ($jds as $jd) {
        $jdata = json_decode(iconv("GBK", "UTF-8", file_get_contents(APP_PATH . '../source/receive/' . $jd['filepath'])), 1);
        if (!$jdata) {
            lg('--保存接口数据出错：' . $jd['filepath']);
            continue;
        }
        Db::startTrans();
        try {
            foreach ($jdata as $data) {
                $yid = $data['ID'];
                $existingOrder = db('orders')->where("yid='{$yid}'")->find();
                $station_id = (int)$stations[$data['STATION']];

                $order = [
                    'yid' => $yid,
                    'fyid' => $data['FCUSTOMSERVICEID'],
                    'gid' => $data['CUSTOMSERVICEID'],
                    'type' => $data['FCUSTOMSERVICEID'] ? 1 : 0,
                    'place' => (int)$place[$station_id][2],
                    'station_name' => $data['STATION'],
                    'station' => $station_id,
                    'office_name' => $data['ACCEPTSTATION'],
                    'office' => (int)$office[$data['ACCEPTSTATION']],
                    'gdtype_name' => $data['GDTYPE'],
                    'gdtype' => (int)$gdtypes[$data['GDTYPE']],
                    'draft_time' => $data['KSTIME'],
                    'etime' => isset($data['CHECKTIME']) && $data['CHECKTIME'] != "" ? $data['CHECKTIME'] : $data['SOLVETIME_PLAN'],
                    'village' => $data['REPORTAREA'],
                    'village_addr' => $data['HAPPENADDR'],
                    'addr' => $data['SENDSTATION'],
                    'notes' => $data['ACCEPTREMARK'],
                    'rid' => $jd['id'],
                    'pass_faces_code' => '',
                    'pass_faces_user_count' => 0,
                    'no_pass_faces_photo_count' => 0,
                    'takeover_no' => $data['TAKEOVERNUMBER'],
                    'equipment_type' => $data['EQUIPMENTTYPE'],
                    'volume' => $data['VOLUME'] ? $data['VOLUME'] : 0,
                    'nfc' => $data['NFC'] ? $data['NFC'] : "",
                    'pda' => $data['PDA'] ? $data['PDA'] : "",
                    'status' => 0
                ];

                if ($existingOrder) {
                    lg('--工单 ' . $yid . ' 已存在，更新数据');
                    db('orders')->where("yid='{$yid}'")->update($order);
                } else {
                    lg('--工单 ' . $yid . ' 不存在，插入新数据');
                    db('orders')->insert($order);
                }

                $oid = $existingOrder ? $existingOrder['id'] : db('orders')->getLastInsID();
                $dir = APP_PATH . '../source/pics/' . date('Ym', strtotime($order['etime'])) . '/' . $oid;
                @mkdir($dir, 0777, true);

                foreach ($data['Media'] as $pic) {
                    $p = pathinfo($pic['path']);
                    $file = $dir . '/' . $p['basename'];

                    // 检查照片是否已存在
                    $existingPhoto = db('photos')->where(['oid' => $oid, 'filename' => $p['filename']])->find();

                    if ($existingPhoto) {
                        // 如果已存在，覆盖更新
                        if (file_put_contents($file, file_get_contents($pic['path']))) {
                            lg('-图片 ' . $p['filename'] . ' 已保存，更新记录');
                            db('photos')->where(['oid' => $oid, 'filename' => $p['filename']])->update([
                                'extension' => $p['extension'],
                                'filesize' => filesize($file),
                                'ycode' => (int)$ycodes[$pic['name']],
                                'realtime' => $pic['time'],
                                'tags' => '',
                                'updatenum' => 0,
                                'status' => 0
                            ]);
                            $ps++;
                        } else {
                            lg('-图片 ' . $p['filename'] . ' 保存失败 error');
                        }
                    } else {
                        // 如果不存在，则插入新记录
                        if (file_put_contents($file, file_get_contents($pic['path']))) {
                            lg('-图片 ' . $p['filename'] . ' 已保存');
                            db('photos')->insert([
                                'oid' => $oid,
                                'filename' => $p['filename'],
                                'extension' => $p['extension'],
                                'filesize' => filesize($file),
                                'ycode' => (int)$ycodes[$pic['name']],
                                'realtime' => $pic['time'],
                                'tags' => '',
                                'updatenum' => 0
                            ]);
                            $ps++;
                        } else {
                            lg('-图片 ' . $p['filename'] . ' 保存失败 error');
                        }
                    }
                }

                // 更新母单接管编号
                if ($order['type'] == 1 && $order['takeover_no'] && $order['takeover_no'] != '') {
                    db()->execute("update orders set takeover_no='{$order['takeover_no']}' where yid = '{$order['fyid']}'; ");
                }
            }
            db('receives')->where(['filepath' => $jd['filepath']])->setField('status', 1);
            Db::commit();
        } catch (\Exception $e) {
            lg('error ：' . $e);
            Db::rollback();
        }
    }

    // 更新母单维保单位
    db()->execute('UPDATE orders AS o1 JOIN (SELECT o.yid, o2.office FROM orders o JOIN orders o2 ON o2.fyid = o.yid WHERE o.office = 0 LIMIT 1) AS subquery ON o1.yid = subquery.yid SET o1.office = subquery.office WHERE o1.office = 0');

    // 记录图片接收数据
    db('op_logs')->insert(['op_code' => 'receive', 'op_count' => $ps, 'op_data' => date('Y-m-d'), 'created_at' => $created_at, 'start_at' => $start_at, 'end_at' => date('Y-m-d H:i:s')]);
    lg('---保存接口数据完成');
    return true;
}





	//保存从外部接口获取的工单及图片数据
	protected function saveapidata()
	{
		lg('---开始保存接口数据');
		//echo 'saveapidata!';
		$jds = db('receives')->where('status=0')->group('filepath')->order('id')->select();
		if(!$jds){
			lg('---保存接口数据完成，无数据');
			return -1;
		}
		
		$gdtypes = \Param::gdtypes(1);
		$place = \Param::station(2);
		$stations = \Param::station(1);
		$office = \Param::office(1);
        // 按分类名　－＞ 分类　返回数组　
		$ycodes = \Param::ycodes(1, '');
		$ps = 0;
		$created_at = $start_at = date('Y-m-d H:i:s');

		foreach($jds as $jd){
			$jdata = json_decode(iconv("GBK", "UTF-8", file_get_contents(APP_PATH . '../source/receive/' . $jd['filepath'])), 1);//print_r($jdata);die;
			if(!$jdata){
				lg('--保存接口数据出错：' . $jd['filepath']);
				continue;
			}
			Db::startTrans();
			try{
				foreach($jdata as $data){
					$yid = $data['ID'];
					if(db('orders')->where("yid='{$yid}'")->find()){
						lg('--工单 '. $yid .' 已存在');
						continue;
					}
					
					$gdtype = (int)$gdtypes[$data['GDTYPE']];
					#if($gdtype) continue;
					if(strtotime($data['SOLVETIME_PLAN']) < 1628438400) continue;

                    $etime = $data['SOLVETIME_PLAN'];
                    if(isset($data['CHECKTIME']) && $data['CHECKTIME'] != ""){
                        $etime = $data['CHECKTIME'];
                    }

					$order = [];
					$station_id = (int)$stations[$data['STATION']];
					$order['yid']			= $yid;
					$order['fyid']			= $data['FCUSTOMSERVICEID'];
					$order['gid']			= $data['CUSTOMSERVICEID'];
					$order['type']			= $data['FCUSTOMSERVICEID'] ? 1 : 0;
					$order['place']			= (int)$place[$station_id][2];
					$order['station_name']	= $data['STATION'];
					$order['station']		= $station_id;
					$order['office_name']	= $data['ACCEPTSTATION'];
					$order['office']		= (int)$office[$data['ACCEPTSTATION']];
					$order['gdtype_name']	= $data['GDTYPE'];
					$order['gdtype']		= $gdtype;
					$order['draft_time']	= $data['KSTIME'];
					$order['etime']			= $etime; //$data['SOLVETIME_PLAN'];
					$order['village']		= $data['REPORTAREA'];
					$order['village_addr']	= $data['HAPPENADDR'];
					$order['addr']			= $data['SENDSTATION'];
					$order['notes']			= $data['ACCEPTREMARK'];
					$order['rid']			= $jd['id'];
					$order['pass_faces_code'] = '';
					$order['pass_faces_user_count'] = 0;
					$order['no_pass_faces_photo_count'] = 0;

					$order['takeover_no']	= $data['TAKEOVERNUMBER'];
					$order['equipment_type']	= $data['EQUIPMENTTYPE'];
					$order['volume']	= $data['VOLUME'] ? $data['VOLUME'] : 0;
                    $order['nfc'] = $data['NFC'] ? $data['NFC'] : "";
                    $order['pda'] = $data['PDA'] ? $data['PDA'] : "";

                    if($order['type'] == 0){
                        $suborder = db('orders')->field('takeover_no')->where("fyid='{$order['yid']}' and takeover_no is not null")->limit(0, 1)->select();
                        if(count($suborder)==1){
                            $order['takeover_no'] = $suborder[0]['takeover_no'];
                        }
                    }

                    db('orders')->insert($order);

                    $oid = db('orders')->getlastInsID();

                    if($oid) lg('--工单 '. $yid .' 已保存'); else{
                        lg('--工单 '. $yid .' 保存失败 error');
                        throw new \Exception("工单保存失败");
                    }

                    $dir = APP_PATH . '../source/pics/' . date('Ym', strtotime($etime)) . '/' . $oid;
                    @mkdir($dir, 0777, 1);

                    foreach($data['Media'] as $pic){
                        $p = pathinfo($pic['path']);

                        $file = $dir . '/' . $p['basename'];
                        /*if(file_exists($file)){
                            lg('-图片 '. $p['filename'] .' 已存在');
                            continue;
                        }*/
                        if(file_put_contents($file, file_get_contents($pic['path']))){
                            lg('-图片 '. $p['filename'] .' 已保存');
                            db('photos')->insert(['oid' => $oid, 'filename' => $p['filename'], 'extension' => $p['extension'],
                                'filesize' => filesize($file), 'ycode' => (int)$ycodes[$pic['name']], 'realtime'=>$pic['time'], 'tags'=>'',
                                'updatenum'=>0
                            ]);
                            $ps++;
                        } else lg('-图片 '. $p['filename'] .' 保存失败 error');
                    }

                    // 更新母单接管编号
                    if($order['type'] == 1 && $order['takeover_no'] && $order['takeover_no'] != ''){
                        db()->execute("update orders set takeover_no='{$order['takeover_no']}' where yid = '{$order['fyid']}'; ");
                    }

				}
				db('receives')->where(['filepath' => $jd['filepath']])->setField('status', 1);
				Db::commit();
			} catch (\Exception $e) {
				echo(print_r($e, true));
				//exit();
				lg('error ：'. $e);
				Db::rollback();
			}
		}

        // 更新母单维保单位
		db()->execute('UPDATE orders AS o1 JOIN (SELECT o.yid, o2.office FROM orders o JOIN orders o2 ON o2.fyid = o.yid WHERE o.office = 0      LIMIT 1) AS subquery ON o1.yid = subquery.yid SET o1.office = subquery.office WHERE o1.office = 0');

        // 记录图片接收数据
		db('op_logs')->insert(['op_code'=>'receive', 'op_count'=>$ps, 'op_data'=>date('Y-m-d'), 'created_at' => $created_at, 'start_at' => $start_at, 'end_at' => date('Y-m-d H:i:s')]);
		lg('---保存接口数据完成');
		return true;
	}
	
	//生成json文件
	protected function sjson($param)
	{
		$f = date('YmdHis') . substr(microtime(), 2, 6);
		$orders = db('orders')->where($param[0])->select();
		foreach($orders as $order){
			$photos = db('photos')->where(['oid'=>$order['id']])->where($param[1])->select();		
			//if(!$photos) continue;
			$order['photos'] = $photos;
			$data['orders'][] = $order;
		}
		file_put_contents(APP_PATH . '../source/pics/' . $f . '.json', json_encode($data, 320));
		return $f;
	}
	
	protected function mts($param)
	{
		$jsonfile = APP_PATH . '../source/pics/' . $param[0] . '.json';
		$created_at = $start_at = date('Y-m-d H:i:s');
		lg('开始识别工单照片');
		$mtpy = config('mtpy');	//识别引擎运行文件，在根目录/python下
		$modeldir = config('modeldir');	//识别引擎模型所在目录，在根目录/python/models下
		$matching = config('matching');	//识别阈值
		$proces = config('proces');	 //运行引擎的进程数
		$PaddleOCR = config('PaddleOCR');	 //PaddleOCR服务路径
		$rconfig = config('database.redis');	//redis服务器
		$rdss = "{$rconfig['host']}#{$rconfig['port']}#{$rconfig['db']}";
		$pyf = config('pyf');	//python绝对路径
        // 审核工单照片
		$mts = exec($pyf . ' ' . APP_PATH . '../python/' . $mtpy . " {$jsonfile} {$modeldir} {$matching} {$proces} {$PaddleOCR} {$rdss} {$pyf}");

		lg($mts);
		lg('工单照片识别结束，开始处理识别结果数据');

		$source = json_decode(file_get_contents($jsonfile), 1);
		$audit = new \Audit;

        // 处理工单识别结果
		$ods = $audit->auditOrders($source['orders']);

		$end_at = date('Y-m-d H:i:s');
		$ods[2] = $created_at;
		$ods[3] = $end_at;
		db('op_logs')->insert(['op_code'=>'detect', 'op_count'=>$ods[1], 'op_data'=>date('Y-m-d'), 'created_at' => $created_at, 'start_at' => $start_at, 'end_at' => $end_at]);
		lg('识别结果数据处理结束');

                print("识别结果数据处理结束");
        //lg("新模型审核照片");
        //print("\n新模型审核照片");
        // 新模型审核照片(第二个参数为ycodes.id 如 浑浊度，消毒剂，消毒剂快检为20， 21， 23 。7喷洒消毒剂 、10总氯)
        $this->audit_photo_new($source['orders'], [7,10,20,21,23]);
        //$this->audit_spec_HunZhuoDu8($source['orders']);

        // jry202405
        // 更新照片审核结果
        foreach($source['orders'] as $order){
            foreach($order['photos'] as $photo){
                db('photos')->where(['oid'=>$order['id']])
                    ->where(['id'=>$photo['id']])
                    ->setInc('updatenum');
            }
            db('orders')->where(['id'=>$order['id']])
                    ->update(['is_pushed' => 0]);
        }

        // 智能锁检测
        $this->check_smart_locks($source['orders']);
        lg("检测智能锁结束");
        print("\n检测智能锁结束");
        // 喷洒消毒剂检测
        // $this->check_xiaoduji($source['orders']);


		return $ods;
	}

    /**
     * 根据条件重新审核工单照片
     * @param $param
     * @return void
     */
	protected function jmts($param)
	{
		$jsonfile = $this->sjson($param);
		$this->mts([$jsonfile]);
		unlink(APP_PATH . '../source/pics/' . $jsonfile . '.json');
	}


    /**
     * 每天自动审核工单照片（定时触发）
     *   1.拉取工单照片数据
     *   2.生成json文件
     *   3.调用识别引擎，处理识别结果数据
     * @param $param
     * @return false|void
     */
	protected function autoexec($param)
	{
		if(!$this->saveapidata()) return false;
		if(!$param[0]) $param[0] = 'status = 0';
		$jsonfile = $this->sjson($param);
		$this->mts([$jsonfile]);
		//unlink(APP_PATH . '../source/pics/' . $jsonfile . '.json');
	}
	
	
	//php ../think Tools audit "id > 0" 50
	protected function audit($param)
	{
		$orders = db('orders')->where($param[0])->select();
		if(!$orders) return [];
		$time = date('Y-m-d H:i:s');
		db('photos')->where('id>0')->update(['status'=>2, 'updatetime'=>$time]);
		db('photos')->where('matching>='.$param[1])->update(['status'=>1, 'updatetime'=>$time]);
		$audit = new \Audit;
		$fs = $audit->fs;
		$forders = [];
		foreach($orders as $order){
			if(!$order['type']){
				if($order['gdtype'] != 0) continue;
				$forders[$order['yid']] = 1;
				continue;
			}
			if($order['gdtype'] == 0) $forders[$order['fyid']] = 1;
			$codes = db('photos')->where(['oid'=>$order['id'], 'status'=>1])->value('GROUP_CONCAT(code)');
			$j = [];
			foreach(explode(',', $codes) as $code){
				if(in_array($code, $audit->s[2])) $j[$code]++;
			}
			$at = $audit->audit(1, $order['gdtype'], array_keys($j));
			$status = $at[0] ? 1 : 2;
			db('orders')->where(['id'=>$order['id']])->update(['status'=>$status, 'updatetime'=>$time]);
			db()->query("delete from `order_adopt` where `oid` = {$order['id']}");
			foreach($at[1] as $f){
				db()->query("insert into `order_adopt` set `oid` = {$order['id']}, `cate` = {$f}");
			}
		}
		
		$ts = db('ycodes')->where("otype = 0")->value('GROUP_CONCAT(id)');
		foreach($forders as $fyid => $v){
			$order = db('orders')->where(['yid'=>$fyid])->find();
			if(!$order) continue;
			$photos = db()->query('select GROUP_CONCAT(code) codes from(SELECT code FROM `photos` WHERE  ( ycode in('. $ts .') and status = 1 and oid in(select id from orders where fyid="'. $order['yid'] .'") ) GROUP BY `code`) s');
			$ms = explode(',', $photos[0]['codes']);
			$at = $audit->audit(0, $order['gdtype'], $ms);
			$status = $at[0] ? 1 : 2;
			db('orders')->where(['id'=>$order['id']])->update(['status'=>$status, 'updatetime'=>$time]);
			db()->query("delete from `order_adopt` where `oid` = {$order['id']}");
			foreach($at[1] as $f){
				db()->query("insert into `order_adopt` set `oid` = {$order['id']}, `cate` = {$f}");
			}
		}
		return ok;
	}

/**
	 * 审核照片（用新模型）
	 */
	protected function audit_photo_new($orders, $ycodes){
        print("\n待审核");
        $oids = [];
        foreach ($orders as $o) {
            // 只处理子单
            if(!$o['type']){
            print("\n只处理子单");
                continue;
            }
            $oids[] = $o['id'];
        }

        lg("[新模型]待审核工单 id: ".join(',', $oids));
        print("\n[新模型]待审核工单 id: ".join(',', $oids));
        if(count($oids) == 0){
            lg("[新模型]待检测工单数为0");
            return;
        }
        $cond = "p.oid in(". join(',', $oids) .")";

		// 所有满足条件的照片（工单， 大类）
        $ps = db('photos')->alias('p')->field('p.id, p.oid, o.etime, p.filename, p.extension, p.matching, p.ycode, p.updatenum')->join('orders o', 'o.id = p.oid')->where("p.ycode in (".join(',', $ycodes).")")->where($cond)->select();
        $plen = count($ps);

        $f = date('YmdHis') . substr(microtime(), 2, 6);
        $data = [];
        $oids = [];
        foreach($ps as $photo){
            $data[] = $photo;
            if(!in_array($photo['oid'], $oids)){
                $oids[] = $photo['oid'];
            }
        }
        if(count($oids) == 0){
            lg("[新模型]待检测照片工单为0");
            return;
        }

        $jsonfile = APP_PATH . '../source/pics/' . $f . '.json';
        file_put_contents($jsonfile, json_encode($data, 320));

        // 根据工单id照片值反向生成redis审核结果(为了兼容auditOrders方法)
        $orders = db('orders')->where("id in(".(join(',', $oids)).")")->select();

        $redis = new \Redis();
        $rconfig = config('database.redis');	//redis服务器
        $redis->connect($rconfig['host'], $rconfig['port']);
        $redis->select($rconfig['db']);

        $data = [];
        foreach($orders as $order){
            $photos = db('photos')->where(['oid'=>$order['id']])->where("matching>0")->select();
            foreach($photos as $p){
                $redis->hset("photos", $p['id'], $p["code"].','.$p['matching'].','.$p['status']);
            }
            $order['photos'] = $photos;
            $data[] = $order;
        }

        // 批量审核照片
        $pyf2 = config('pyf2');	//
        $detectpy = config('detectpy');	// 检测执行脚本

        echo '>>'.$pyf2 . APP_PATH . $detectpy .' '. $jsonfile;
        $detect = exec($pyf2 . APP_PATH . $detectpy .' '. $jsonfile);

        $audit = new \Audit;
        $ods = $audit->auditOrders($data);

        lg("[新模型]检测照片结果，工单数: ".count($orders)." 照片数: {$plen}");
        print("[新模型]检测照片结果，工单数: ".count($orders)." 照片数: {$plen}");

	}



    /**
    // 获取工单浊度仪照片
    // 批量审核照片
    // 更新照片审核结果
    // 更新工单结果
     * **/
    protected function audit_spec_hunzhuodu8($orders){
        $oids = [];
        foreach ($orders as $o) {
            // 只处理子单
            if(!$o['type']){
                continue;
            }
            $oids[] = $o['id'];
        }

        print("待审核浑浊度工单 id: ".join(',', $oids));
        if(count($oids) == 0){
            print("待检浑浊度工单数为0");
            return;
        }
        $cond = "p.oid in(". join(',', $oids) .")";

        $ps = db('photos')->alias('p')->field('p.id, p.oid, o.etime, p.filename, p.extension, p.matching')->join('orders o', 'o.id = p.oid')->where("p.ycode=20")->where($cond)->select();
        $plen = count($ps);

        $f = date('YmdHis') . substr(microtime(), 2, 6);
        $data = [];
        $oids = [];
        foreach($ps as $photo){
            $data[] = $photo;
            if(!in_array($photo['oid'], $oids)){
                $oids[] = $photo['oid'];
            }
        }
        if(count($oids) == 0){
            lg("审核浊度照片工单为0");
            return;
        }
        $jsonfile = APP_PATH . '../source/pics/' . $f . '.json';
        file_put_contents($jsonfile, json_encode($data, 320));

        // 根据工单id照片值反向生成redis审核结果
        $orders = db('orders')->where("id in(".(join(',', $oids)).")")->select();

        $redis = new \Redis();
        $rconfig = config('database.redis');	//redis服务器
        $redis->connect($rconfig['host'], $rconfig['port']);
        $redis->select($rconfig['db']);

        $data = [];
        foreach($orders as $order){
            $photos = db('photos')->where(['oid'=>$order['id']])->where("matching>0")->select();
            foreach($photos as $p){
                //echo "photos". ' '. $p['id']. ' '. $p["code"].','.$p['matching'].',1 \r\n';
                $redis->hset("photos", $p['id'], $p["code"].','.$p['matching'].','.$p['status']);
            }
            $order['photos'] = $photos;
            $data[] = $order;
        }

        // 批量审核照片
        $detect = exec('/home/www/2water.com/detect/venv/bin/python3.8 ' . APP_PATH . '../detect/detect_hunzhuodu.py '. $jsonfile);

        $audit = new \Audit;
        $ods = $audit->auditOrders($data);

        print("审核浊度照片结果，工单数: ".count($orders)." 照片数: {$plen}");

    }

    public function test_smart_locks_orders(){
        $oids = "";

        $orders = db('orders')->where("id in(${oids})")->select();
        $this->check_smart_locks($orders);
    }

    public function test_smart_locks_order($oids){
        // 假设输入的 $oids 是一个用逗号分隔的字符串
        $orders = db('orders')->where("id in(${oids})")->select();
        $this->check_smart_locks($orders);
    }
    
    protected function check_smart_locks($orders){
        $oids = [];
        foreach ($orders as $o) {
            // 只处理子单
            if(!$o['type']){
                continue;
            }

            //if($o['station'] == 9){
                $oids[] = $o['id'];
            //}
        }

        lg("检测智能锁, 工单数：".count($oids)."个");
        print("检测智能锁, 工单数：".count($oids)."个");

        if(count($oids) == 0){
            lg("智能锁工单数为0");
            return;
        }

        // 获取特定条件工单(瞿溪站)水箱锁照片
        $cond = "p.ycode=19 and p.oid in(".join(',', $oids).")";
        lg("cond:". $cond);

        $ps = db('photos')->alias('p')->field('p.id, p.oid, o.etime, p.filename, p.extension')->join('orders o', 'o.id = p.oid')->where($cond)->select();

        $f = date('YmdHis') . substr(microtime(), 2, 6);
        $data = [];
        foreach($ps as $photo){
            $data[] = $photo;
        }

        $jsonfile = APP_PATH . '../source/pics/smartlocks/' . $f . '.json';
        file_put_contents($jsonfile, json_encode($data, 320));
	
        lg("jsonfile:". $jsonfile);

        // 批量检测照片是否有智能锁
	$LL='sudo docker exec -i lavis bash -c "cd /home/LAVIS && python /app/smartlocks/detect_smartlocks.py '."\"/source/pics/smartlocks/".$f . '.json""';
        //$smartLockDetect = exec('docker exec -it lavis bash -c "cd /home/LAVIS && python /app/smartlocks/detect_smartlocks.py '."/source/pics/smartlocks/".$f . '.json"');
        $smartLockDetect=exec($LL . ' 2>&1', $output, $return_var);
// 写入到文件
	$orders1= $output;
	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                          // 写入到文件
	$result = file_put_contents('/home/www/2water.com/smartlockorderoutput.txt', $contentToWrite);

	lg($smartLockDetect);
	print($smartLockDetect);
        $photos = json_decode(file_get_contents($jsonfile), 1);

        foreach($photos as $p){
            if(isset($p['tabs'])){
                lg("smartlocks:". $p['id']. ' '. $p['tabs']);
                db('photos')->where('id='.$p['id'])->update(['tags'=>$p['tabs']]);
            }
        }

    }

    protected function check_xiaoduji($orders){
        // 检测手套， 工作服， 靴子

        // 检测相邻时间
        //
    }

    protected function pda(){
//        $time = "2023-06-21 12:23:29";
//        echo date('Y-m-d H:i:s', strtotime($time. ' - 3 minutes'));
//        echo '--';
//        echo date('Y-m-d H:i:s', strtotime($time. ' + 3 minutes'));

        $data = [];
        // 华漕，华新，新泾
        //$orders = db('orders')->where("id in(423649,423650,423651,423653,423654,423655,423657,423673,423674,423675,423676,423677,423678,423679,421444,421445,421600,421601,421603,423680,423681,421439,421447,421448,421449,421451,421452,423661,423662,423663,423664,423665,423667,423668,423669,423671,423672,422235,422238,422252,422253,422254,422859,422860,422864,422865,422871,422872,422873,422874)")->select();

        // 九亭, 泗泾, 新桥
        // $orders = db('orders')->where("id in(424375,424374,424373,424372,424371,424370,424368,424367,424366,424365,424364,424363,424360,424359,424358,424259,424256,424253,424252,424251,423191,423190,423189,423188,423187,423186,423185,423184,422958,422957,422956,422955,422954,422953,422952,422951,422950,422949,422948,422947,422946,422945,422944,422943,422942,422941,422940,422939,422937,422936)")->select(); 
        

        // （华漕，华新，新泾）(九亭, 泗泾, 新桥)
        // $orders = db('orders')->where("id in(423649,423650,423651,423653,423654,423655,423657,423673,423674,423675,423676,423677,423678,423679,421444,421445,421600,421601,421603,423680,423681,421439,421447,421448,421449,421451,421452,423661,423662,423663,423664,423665,423667,423668,423669,423671,423672,422235,422238,422252,422253,422254,422859,422860,422864,422865,422871,422872,422873,422874,424375,424374,424373,424372,424371,424370,424368,424367,424366,424365,424364,424363,424360,424359,424358,424259,424256,424253,424252,424251,423191,423190,423189,423188,423187,423186,423185,423184,422958,422957,422956,422955,422954,422953,422952,422951,422950,422949,422948,422947,422946,422945,422944,422943,422942,422941,422940,422939,422937,422936)")->select();

        // 西区所除外, 每个合同50张
        $orders = db('orders')->where("id in(432120,432121,432125,432122,432123,432124,432134,432135,432140,432130,432133,432139,432128,432129,432138,432126,432127,432137,432131,432132,432136)")->select();

        // 东区所，每个站50个
        //$orders = db('orders')->where("id in(423683,423684,423328,423327,423332,423331,423329,423330,422893,422894,422892,422891,422889,425963,424311,424310,424308,424302,424303,424304,424305,424301,424238,424237,424233,424235,424236,424234,424229,424230,424231,424227,424226,424208,424211,424209,424210,424220,424212,424213,424214,424219,424218,424217,424216,424215,424206,423425,423423,423419,423418,423417,423396,423617,423616,423614,423609,423610,423611,423325,423319,423320,423321,423313,423312,423311,423308,423314,423309,423310,423304,423303,423302,425922,425920,425921,425924,425925,425926,425923,425911,425910,425913,425912,425909,425914,425906,425905,425752,425755,425756,425754,425757,425993,425994,425598,425546,425548,425547,425237,425239,425240,425238,425241,425224,425223,425222,425208,425207,425198,424950,424949,424948,423636,423635,423634,423633,423632,423631,423629,423627,423628,423630,423626,423625,423624,423623,423622,423621,423619,424654,424655,424656,424658,424657,424661,424659,424660,424641,424632,424631,424630,424642,424629,424628,424643,424635,424633,424634,424636,423375,423374,423373,423372,423371,423370,423369,423368,423367,423366,423365,423364,423353,423352,423351,423350,423349,423348,423347,423346,425767,425766,425764,425763,424295,424296,424290,424294,424293,424292,424291,424285,424286,424287,424288,424289,424284,424283,424282,424281,425101,425107,425096,425098,425099,425100,425103,425104,425102,425105,425106,425097,425095,425094,425086,425085,425087,425088,425089,425090,425984,425982,425983,425985,425986,425987,425860,425859,425858,425855,425856,425857,425791,425789,425790,425787,425788,425786,425785,425784,422561,424005,424004,424003,423839,423840,423841,423842,423843,423844,423845,423837,423836,423761,423759,423757,423755,423738,423747,423744,423746,424503,423825,423816,423814,423813,423815,423809,423810,423808,423807,423806,423805,423811,423812,422854,422855,422856,422857,422853,424174,424173,424154,424153,424152,424149,424150,424151,424147,424148,424162,424159,424160,424161,424158,424157,424156,424155,424138,424137,424184,424188,424187,424186,424185,424183,424182,424181,424180,424179,424178,424177,424204,424189,424190,424191,424203,424202,424201,424200,424316,424315,424314,424317,424111,424027,424032,424033,424034,424035,424036,424037,424031,424030,424029,424028,424026,424025,424024,424023)")->select();

        foreach($orders as $order){
            $photos = db('photos')->where(['oid'=>$order['id']])->where("ycode=6")->select();
            //if(!$photos) continue;
            $order['photos'] = $photos;
            $data[] = $order;
        }

        $cls = "\like\PDATimeDetector";
        $pda = new $cls();
        //echo $pda->name;
        $pda->execute($data);

        $f = date('YmdHis') . substr(microtime(), 2, 6);
        file_put_contents(APP_PATH . '../source/pics/HanYangLiang/' . $f . '.json', json_encode($data, 320));

    }

    protected function pensa(){

    	$ps = db('photos')->alias('p')->field('p.id, p.oid, o.etime, p.filename, p.extension, p.matching')->join('orders o', 'o.id = p.oid')->where("p.ycode=7 and p.updatetime<'2023-06-30'")->order('p.id desc')->limit("0, 300")->select();

    	$fileroot = "/home/www/2water.com/source/pics/";

    	foreach($ps as $p){
    		$oid = $p['oid'];
    		$etime = $p['etime'];
    		$filepath = $fileroot . date('Ym', strtotime($etime)) . '/' . $oid .'/'.$p['filename'].'.'.$p['extension'];
    		//echo $filepath ."\n";
    		$destdir = "/home/www/2water.com/tmp/images/pensa0706/";

    		copy($filepath, $destdir.$p['filename'].'.'.$p['extension']);
    	}
    }

}
