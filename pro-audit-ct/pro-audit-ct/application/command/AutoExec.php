<?
namespace app\Command;
use think\Db;
use think\console\Command;
use think\console\input;
use think\console\output;
use think\console\input\Argument;
use think\console\input\Option;

class AutoExec extends Command
{

	protected function configure()
	{
		$this->setName('AutoExec')->setDescription('自动执行任务 :)')
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
		$this->serverId = (int)config('serverId');
		
		if($this->serverId != 2) die('-1');
		lg('----start');
		$r = $input->getOption('ReParam');
		if(!$r){
			$created_at = $start_at = date('Y-m-d H:i:s');
			$data = $this->getapidata2();
			if(!$data) die('-3');
			$jsonfile = $this->saveapidata2($data);
			if(!$jsonfile) die('-5');
			db('op_logs')->insert(['op_code'=>'receive', 'op_count'=>$jsonfile[1], 'op_data'=>date('Y-m-d'), 'created_at' => $created_at, 'start_at' => $start_at, 'end_at' => date('Y-m-d H:i:s')]);
			$result = $this->mts($jsonfile[0]);
			if(!$result) die('-6');
			if(!$this->submitdata($result)) die('-7');
			#$this->mts(APP_PATH . '../source/pics/20210106113615350770.tar.json');
		}else{
			$w = explode(';', $r);//print_r($w);
			$where = [];
			foreach($w as $p){
				if($p){
					$p = explode(',', $p);
					$where[$p[0]] = [$p[1], $p[2]];
				}
			}//print_r($where);
			$jsonfile = APP_PATH . '../runtime/log/' . date('Ymd') . seconds() . '.re.json';
			$orders = [];
			$pts = db('photos')->field('id,oid,filename,extension')->where('matching <= '. config('matching') .' or code < 2')->where($where)->select();
			foreach($pts as $p){
				$oid = $p['oid'];unset($p['oid']);
				if(!isset($orders[$oid])) $orders[$oid] = db('orders')->field('id,ydate,office')->find($oid);
				$orders[$oid]['photos'][] = $p;
			}
			if(!$orders) die('-5');
			file_put_contents($jsonfile, json_encode(['orders'=>array_values($orders), 'time'=>time()]));
			$result = $this->mts($jsonfile);
			if(!$result) die('-6');
			$result['re'] = 1;
			if(!$this->submitdata($result)) die('-7');
		}
		
		
		lg('----end');
		$output->writeln(1);
	}
	
	/*------->巡智服务器运行{{{*/
	//巡智服务器：从内网接口获取打包好的工单及图片数据
	protected function getapidata2()
	{
		if($this->serverId != 2) die('-2');
		lg("---获取打包数据");
		$source = file_get_contents("http://180.169.66.162:22234/ecgs/autoexec?tar=1", 0, stream_context_create(['http'=>['method'=>'POST', 'timeout'=>7200]]));	//1小时超时
		lg("---{$source}");
		if(!$source){
			lg('---获取打包数据失败 error0');
			return false;
		}
		$data = json_decode($source, 1);
		if($data['error']){
			lg('---获取打包数据失败 '.$data['error']);
			return false;
		}
		$tarfile = $data['file'];
		if(!$tarfile){
			lg('---获取打包数据失败 error1');
			return false;
		}
		lg("---获取打包数据索引完成");
		$status = (int)$data['status'];
		if($status == -1){
			lg('---打包数据正在处理 wait');
			return false;
		}
		if($status == -2){
			lg('---打包数据已过期 error');
			return false;
		}
		$bfile = json_decode(file_get_contents(APP_PATH . '../source/' . 'tar.json'), 1);
		if($bfile['time'] >= (int)$data['time']){
			lg('---打包数据已过期，已被处理');
			return false;
		}
		if($bfile['file'] == $tarfile){
			lg('---打包数据已过期，文件已被处理');
			return false;
		}
		lg('---数据包正常，文件名：'. $tarfile .'开始保存...');
		$yfile = fopen("http://180.169.66.162:22234/ecgs/" . $tarfile, 'r');
		//$yfile = fopen("http://localhost/" . $tarfile, 'r');
		$btarfile = APP_PATH . '../source/pics/' . $tarfile;
		$rb = fopen($btarfile, 'w');
		while (!feof($yfile)) {
			$fr = fread($yfile, 8192);
			fwrite($rb, $fr);
		}
		fclose($yfile);
		fclose($rb);
		lg('---数据包已保存，开始校验文件大小');
		if(filesize($btarfile) != (int)$data['size']){
			lg('---数据包错误 error');
			return false;
		}
		lg('---文件大小正确:'. $data['size'] .'bytes，校验通过');
		/*lg('---文件大小正确:'. $data['size'] .'，开始校验文件md5...');
		if(md5_file($btarfile) != $data['md5']){
			lg('---数据包校验失败 error');
			return false;
		}
		lg('---数据包md5正确, md5：' . $data['md5'] . '，校验通过。');*/
		return $data;
	}
	
	//巡智服务器：保存从内网接口获取打包好的工单及图片数据
	protected function saveapidata2($data)
	{
		if($this->serverId != 2) die('-2');
		if(!$data) return false;
		lg('---开始保存数据包数据');
		$tarfile = $data['file'];
		$APP_PATH = str_replace('\\', '/', APP_PATH);
		//exec("cd source/pics\ntar -xf {$tarfile}", $output, $return_val);
		//$phar = new \PharData(APP_PATH . '../source/pics/' . $tarfile);
		//$phar->extractTo(APP_PATH . '../source/pics', null, true);//解压后的路径 数组或者字符串指定解压解压的文件，null为全部解压  是否覆盖
		exec("python {$APP_PATH}../python/untar.py {$APP_PATH}../source/pics/{$tarfile} {$APP_PATH}../source/pics", $output, $return_val);
		$jsonfile = $APP_PATH . '../source/pics/' . $tarfile . '.json';
		if(!file_exists($jsonfile)){
			lg('--数据包解压失败 error');
			return false;
		}
		lg('--数据包解压成功');
		$source = json_decode(file_get_contents($jsonfile), 1);
		$order_count = $photo_count = 0;
		foreach($source['orders'] as $order){
			$photos = $order['photos'];
			unset($order['photos']);
			$yorder = db('orders')->where(['id' => $order['id']])->find();
			if($yorder){
				db('orders')->where(['id' => $order['id']])->update($order);
			}else{
				db('orders')->insert($order);
			}
			foreach($photos as $photo){
				$yphoto = db('photos')->where(['id' => $photo['id']])->find();
				if($yphoto){
					db('photos')->where(['id' => $photo['id']])->update($photo);
				}else{
					db('photos')->insert($photo);
				}
				$photo_count ++;
			}
			$order_count ++;
		}
		$data['status'] = 1;
		file_put_contents($APP_PATH . '../source/' . 'tar.json', json_encode($data));
		//@unlink(APP_PATH . '../source/pics/' . $tarfile);
		lg('---保存数据包数据完成 工单：'. $order_count .', 照片：' . $photo_count);
		//@unlink($jsonfile);
		return [$jsonfile, $photo_count];
	}
	
	//巡智服务器：识别工单图片，提交进行识别
	protected function mts($jsonfile)
	{
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
		$mts = exec($pyf . ' ' . APP_PATH . '../python/' . $mtpy . " {$jsonfile} {$modeldir} {$matching} {$proces} {$PaddleOCR} {$rdss} {$pyf}");
		lg($mts);
		lg('工单照片识别结束，开始处理识别结果数据');
		$source = json_decode(file_get_contents($jsonfile), 1);
		$audit = new \Audit;
		$ods = $audit->auditOrders($source['orders']);
		$end_at = date('Y-m-d H:i:s');
		$ods[2] = $created_at;
		$ods[3] = $end_at;
		db('op_logs')->insert(['op_code'=>'receive', 'op_count'=>$ods[1], 'op_data'=>date('Y-m-d'), 'created_at' => $created_at, 'start_at' => $start_at, 'end_at' => $end_at]);
		lg('识别结果数据处理结束');
		return $ods;
	}
	
	//巡智服务器：发送识别结果给内网服务器
	protected function submitdata($data)
	{
		lg('发送数据');
		if(!$data){
			lg('发送数据完成，无内容');
			return false;
		}
		$dk = json_encode($data);
		$sign = md5($dk . 'SHxunzhie3q87rhfodagfihfawcccw3ghrhjr');
		$fs = base64_encode(json_encode(['data' => $dk, 'sign' => $sign]));
		
		$ch = curl_init('http://180.169.66.162:22234/ecgs/updatedata');
		curl_setopt($ch, 47, 1);
		curl_setopt($ch, 19913, 1);
		curl_setopt($ch, 10015, $fs);
		curl_setopt($ch, 10023, ["Content-type: application/octet-stream"]);
		$rtn = curl_exec($ch);
		curl_close($ch);
		if($rtn == 'ok'){
			lg('发送数据成功，客户服务器处理成功');
			return ok;
		}
		lg('发送数据成功，客户服务器处理失败，返回'.$rtn);
		return false;
	}
	/*<-------巡智服务器运行}}}*/

}