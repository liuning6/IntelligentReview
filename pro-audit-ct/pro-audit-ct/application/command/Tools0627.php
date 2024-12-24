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
		
		$output->writeln('ok');
	}
	
	//保存从外部接口获取的工单及图片数据
	protected function saveapidata()
	{
		lg('---开始保存接口数据');
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

                                        lg($gid, '--etime-- '. $data['CHECKTIME'], ','. $data['SOLVETIME_PLAN']);
					
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

					$order['takeover_no']	= $data['TAKEOVERNUMBER'];
					$order['equipment_type']	= $data['EQUIPMENTTYPE'];
					$order['volume']	= $data['VOLUME'] ? $data['VOLUME'] : 0;

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
							db('photos')->insert(['oid' => $oid, 'filename' => $p['filename'], 'extension' => $p['extension'], 'filesize' => filesize($file), 'ycode' => (int)$ycodes[$pic['name']]]);
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
				lg('error ：'. $e);
				Db::rollback();
			}
		}
		db()->execute('update `orders` set orders.office=(select office from orders o where o.fyid = orders.yid limit 1) where orders.office = 0');
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

        lg("审核浊度照片");
        $oids = [];
        foreach ($source['orders'] as $o) {
            // 只处理子单
            if(!$o['type']){
                continue;
            }
            $oids[] = $o['id'];
        }

        lg("(o.id in(". join(',', $oids) ."))");
        $this->audit_spec_HunZhuoDu8("o.id in(". join(',', $oids) .")");

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

    protected function audit_spec_hunzhuodu8($param){
        // 获取工单浊度仪照片
        // 批量审核照片
        // 更新照片审核结果
        // 更新工单结果
        $ps = db('photos')->alias('p')->field('p.id, p.oid, o.etime, p.filename, p.extension, p.matching')->join('orders o', 'o.id = p.oid')->where("p.ycode=20")->where($param)->select();

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
        $detect = exec('/root/local/python38/bin/python3.8 ' . APP_PATH . '../detect/detect_hunzhuodu.py '. $jsonfile);

        $audit = new \Audit;
        $ods = $audit->auditOrders($data);

        lg("审核浊度照片结果，工单数: ".count($orders)." 照片数: {$plen}");

    }

}
