<?php
namespace app\Command;
use think\Db;
use think\console\Command;
use think\console\input;
use think\console\output;

class Receive extends Command
{
	
	protected function configure()
	{
		$this->setName('Receive')->setDescription('执行更新数据任务 :)')
			->addArgument('param1')->addArgument('param2')	//增加2个参数
			->addOption('ReParam', null, 4);				//选项：重新识别的条件
		//php think AutoExec[ 参数1 参数2 --ignoreTime]
		//php think AutoExec --ReParam=status,eq,2;updatetime,gt,20200912
	}
	
	
	protected function execute(Input $input, Output $output)
	{
		set_time_limit(0);
		ignore_user_abort(1);
		ini_set('memory_limit', '-1');

		lg('---开始更新接口数据');
		$jds = db('receives')->where('status=2')->group('filepath')->order('id')->select();
		if(!$jds){
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
		foreach($jds as $jd){
			$jdata = json_decode(iconv("GBK", "UTF-8", file_get_contents(APP_PATH . '../source/receive/' . $jd['filepath'])), 1);//print_r($jdata);die;
			lg('-- jdata --'.$jdata);

			if(!$jdata){
				lg('--更新接口数据出错：' . $jd['filepath']);
				continue;
			}
			
			Db::startTrans();
			try{
				foreach($jdata as $data){
					$yid = $data['ID'];
					$order = db('orders')->where("yid='{$yid}'")->find();
					lg('-- order --'.$order);

					if(!$order){
						lg('--工单 yid:'. $yid .' 不存在');
						continue;
					}
					$oid = $order['id'];
					lg('-- oid --'.$oid);
				
					//$etime = $data['CHECKTIME'] ? $data['CHECKTIME'] : $data['SOLVETIME_PLAN'];
                    $etime = $data['SOLVETIME_PLAN']; 
                    if(isset($data['CHECKTIME']) && $data['CHECKTIME'] != ""){
						$etime = $data['CHECKTIME']; 
                    }

					// $order = [];
					// $station_id = (int)$stations[$data['STATION']];
					// $order['yid']			= $yid;
					// $order['fyid']			= $data['FCUSTOMSERVICEID'];
					// $order['gid']			= $data['CUSTOMSERVICEID'];
					// $order['type']			= $data['FCUSTOMSERVICEID'] ? 1 : 0;
					// $order['place']			= (int)$place[$station_id][2];
					// $order['station_name']	= $data['STATION'];
					// $order['station']		= $station_id;
					// $order['office_name']	= $data['ACCEPTSTATION'];
					// $order['office']		= (int)$office[$data['ACCEPTSTATION']];
					// $order['gdtype_name']	= $data['GDTYPE'];
					// $order['gdtype']		= $gdtype;
					// $order['draft_time']	= $data['KSTIME'];
					// $order['etime']			= $etime; //$data['CHECKTIME'] ? $data['CHECKTIME'] : $data['SOLVETIME_PLAN'];
					// $order['village']		= $data['REPORTAREA'];
					// $order['village_addr']	= $data['HAPPENADDR'];
					// $order['addr']			= $data['SENDSTATION'];
					// $order['notes']			= $data['ACCEPTREMARK'];
					// $order['rid']			= $jd['id'];
					
					
						//db('orders')->insert($order);
						// $oid = db('orders')->getlastInsID();
						// if($oid) lg('--工单 '. $yid .' 已保存'); else{
						// 	lg('--工单 '. $yid .' 保存失败 error');
						// 	throw new \Exception("工单保存失败");
						// }
						
						$dir = APP_PATH . '../source/pics/' . date('Ym', strtotime($etime)) . '/' . $oid;
						@mkdir($dir, 0777, 1);
						lg('-- Media count --'.count($data['Media']));

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
					
				}
				//db('receives')->where(['filepath' => $jd['filepath']])->setField('status', 1);
				Db::commit();
			} catch (\Exception $e) {
				lg('error ：'. $e);
				Db::rollback();
			}
		}
		//db()->execute('update `orders` set orders.office=(select office from orders o where o.fyid = orders.yid limit 1) where orders.office = 0');
		//db('op_logs')->insert(['op_code'=>'receive', 'op_count'=>$ps, 'op_data'=>date('Y-m-d'), 'created_at' => $created_at, 'start_at' => $start_at, 'end_at' => date('Y-m-d H:i:s')]);
		lg('---更新接口数据完成');
		return true;
	}
	
	
}
