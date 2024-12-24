<?php
namespace app\controller;
use think\Controller;
use think\Db;
class Autoexec extends Controller
{
	
	public function _initialize()
	{
		set_time_limit(0);
		ignore_user_abort(true);    //关掉浏览器，PHP脚本也可以继续执行.
		$this->serverId = (int)config('serverId');//当前服务器，1：客户服务器；2：巡智服务器
		$rconfig = config('database.redis');	//redis服务器
		$redis = new \Redis();
		$redis->connect($rconfig['host'], $rconfig['port']);
		$redis->select($rconfig['db']);
		$this->redis = $redis;
	}
	
	//公共入口
	public function index()
	{	
		file_put_contents('/var/www/pro-audit-ct/AutoExecLog.txt', "执行autoexec的index");
		echo "<script>console.log('执行autoexec的index');</script>";
		lg('----start');
		
		if($this->serverId == 1)
		{//客户服务器运行
			$indexfile = APP_PATH . '../../water.com/app/web/ecgs/tar.json';
			if(!I('q')){
				if(date('Ymd', filemtime($indexfile)) == date('Ymd')) return file_get_contents($indexfile);
			}
			$data = (int)$this->saveapidata();	//保存从外部接口获取的工单及图片数据
			if($data < 1) return '{"error":"无数据"}';
			if(I('tar')){
				if(!$this->tar('status=0')) return '{"error":"打包出错"}';	//打包工单数据及图片，供巡智服务器获取
			}
			lg('----end');
			return file_get_contents($indexfile);
		}
		elseif($this->serverId == 2)
		{//巡智服务器运行
			// 3~4点运行 http://localhost:81/autoexec
			$data = $this->getapidata2();
			if(!$data) return -3;
			if(!$this->saveapidata2($data)) return -5;
			$result = $this->mts2(['status' => 0], ['status' => 0]);
			if(!$result) return -6;
			if(!$this->submitdata($result)) return -7;
		}
		
		lg('----end');
		return 1;
    }
	
	/*------->客户服务器运行{{{*/	
	
	//客户服务器：保存从外部接口获取的工单及图片数据
	protected function saveapidata()
	{
		if($this->serverId != 1) return 0;
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
						lg('--工单 '. $gid .' 已存在');
						continue;
					}
					
					$gdtype = (int)$gdtypes[$data['GDTYPE']];
					if($gdtype) continue;
					//$etime = $data['CHECKTIME'] ? $data['CHECKTIME'] : $data['SOLVETIME_PLAN'];
                    $etime = $data['SOLVETIME_PLAN']; 
                    if(isset($data['CHECKTIME']) && $data['CHECKTIME'] != ""){
					    $etime = $data['CHECKTIME']; 
                    }

					lg($gid, '--etime-- '. $data['CHECKTIME'], ','. $data['SOLVETIME_PLAN'];

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
					$order['etime']			= $etime; //$data['CHECKTIME'] ? $data['CHECKTIME'] : $data['SOLVETIME_PLAN'];
					$order['village']		= $data['REPORTAREA'];
					$order['village_addr']	= $data['HAPPENADDR'];
					$order['addr']			= $data['SENDSTATION'];
					$order['notes']			= $data['ACCEPTREMARK'];
					$order['rid']			= $jd['id'];
					
					$order['takeover_no']	= $data['TAKEOVERNUMBER'];
					$order['equipment_type']	= $data['EQUIPMENTTYPE'];
					$order['volume']	= $data['VOLUME'] ? $data['VOLUME'] : 0;
					
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
	
	//客户服务器：打包工单数据及图片，供巡智服务器获取
	protected function tar($o = [], $p = [])
	{
		if($this->serverId != 1) return 0;
		lg('开始打包');
		$data = [];
		$files = '';
		$orders = db('orders')->where($o)->select();
		if(!$orders){
			lg('图片打包结束 无内容');
			return ok;
		}
		$tar = date('YmdHis') . substr(microtime(), 2, 6) . '.tar';
		file_put_contents(APP_PATH . '../../water.com/app/web/ecgs/tar.json', json_encode(['file' => $tar, 'md5' => '', 'size' => 0, 'time' => time(), 'status' => -1]));
		foreach($orders as $order){
			$order['photos'] = [];
			$ym = date('Ym', strtotime($order['etime']));
			$photos = db('photos')->where(['oid'=>$order['id']])->where($p)->select();
			#if(!$photos) continue;
			foreach($photos as $photo){
				$pic = $photo['filename'] . '.' . $photo['extension'];
				$files .= $ym . '/' . $order['id'] . '/' . $pic . "\n";
				$order['photos'][] = $photo;
			}
			$data['orders'][] = $order;
		}
		$data['time'] = time();
		file_put_contents(APP_PATH . '../source/pics/' . $tar . '.json', json_encode($data, 320));
		$files .= "{$tar}.json\n";
		
		file_put_contents(APP_PATH . '../source/pics/' . $tar . '.list', $files);
		$tarfile = '../../../water.com/app/web/ecgs/' . $tar;
		$exec = exec("cd source/pics\ntar -T {$tar}.list -cf {$tarfile}\nwc -c {$tarfile}\nmd5sum {$tarfile}", $output, $return_val);
		if($exec){
			file_put_contents(APP_PATH . '../../water.com/app/web/ecgs/tar.json', json_encode(['file' => $tar, 'md5' => explode(' ', $output[1])[0], 'size' => explode(' ', $output[0])[0], 'time' => $data['time'], 'status' => 0]));
			lg('打包结束：' . $tar);
		}else{
			lg('打包失败');
		}
		@unlink(APP_PATH . '../source/pics/' . $tar . '.list');
		#@unlink(APP_PATH . '../source/pics/' . $tar . '.json');
		if(!$exec) return false;
		return ok;
	}
	
	//客户服务器：接收并处理更新识别结果
	public function updatedata()
	{
		lg('接收数据');
		$input = file_get_contents('php://input');
		if(!$input){
			echo '无数据';
			lg('接收数据完成，无数据');
			return false;
		}
		$fs = json_decode(base64_decode($input), 1);
		if($fs['sign'] != md5($fs['data'] . 'SHxunzhie3q87rhfodagfihfawcccw3ghrhjr')){
			echo '数据校验失败';
			lg('接收数据完成，数据校验失败 error');
			return false;
		}
		$data = json_decode($fs['data'], 1);
		$orders = $data[0];
		lg('接收数据成功, 工单：'. count($orders));
		if($orders){
			Db::startTrans();
			try{
				foreach($orders as $od){
					$photos = $od['photos'];
					unset($od['photos']);
					$fs = $od['fs'];
					foreach($fs as $f){
						db()->query("replace into `order_adopt` set `oid` = {$od['id']}, `cate` = {$f}");
					}
					unset($od['fs']);
					db('orders')->where(['id'=>$od['id']])->update($od);
					foreach($photos as $pt){
						$pid = $pt['id'];
						unset($pt['id']);
						db('photos')->where(['id'=>$pid])->update($pt);
					}
				}
				Db::commit();
				lg('数据处理成功, 更新成功');
				echo ok;
				$size = ob_get_length();
				header("Content-Length: $size");  //告诉浏览器数据长度,浏览器接收到此长度数据后就不再接收数据
				header("Connection: Close");      //告诉浏览器关闭当前连接,即为短连接
				ob_flush();
				flush();
				
				return ok;
			} catch (\Exception $e) {
				lg('数据处理失败 error ：'. $e);
				Db::rollback();
			}
		}
		db('op_logs')->insert(['op_code'=>'detect', 'op_count'=>$data[1], 'op_data'=>substr($data[2], 0, 10), 'created_at' => $data[2], 'start_at' => $data[2], 'end_at' => $data[3]]);
		lg('数据处理成功, 无数据');
		return ok;
	}
	
}
