<?php
namespace app\controller;
use think\Controller;
use think\Db;
class Tools extends Controller
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
		return ok;
        }
         
        public function testdata(){

             $data = ['id'=>9, 'test'=> 123];
             $data1 = ['test'=> 456, ];
             $r = $data1['id'] ? $data1['id'] : $data1['test'];
             echo $r;

	}
	
	public function fetch_pics(){
		$ks = [
			2 => 'anquan', 
			3 => 'fangshui', 
			4 => 'menpaihao', 
			5 => 'qingxi', 
			6 => 'qitijiance', 
			7 => 'shangsuo', 
			8 => 'shigongmingpai', 
			9 => 'shuizhijiance', 
			10 => 'tingshuitongzhi', 
			11 => 'tizi', 
			12 => 'yaoji', 
		];
		$ks = [
				4 => 'menpaihao',
			];
		foreach($ks as $k=>$v){
			//$sql = "select p.id,o.ydate,p.filename,p.extension from photos p, orders o where p.oid=o.id and p.mcid={$k} and p.status=1 and p.matching>95 and p.updatetime > '2022-01-01 00:00:00' and p.updatetime<'2022-07-01 00:00:00' order by p.filename limit 10;";
			$picdir = "/data/webroot/2water.com/source/pics";
			
			// train pics
			$where = "p.mcid={$k} and p.status=1 and p.matching>95 and p.updatetime > '2022-01-01 00:00:00' and p.updatetime<'2022-07-01 00:00:00'";
			$ps = db('photos')->alias('p')->field('p.*, o.ydate, o.office')->join('orders o', 'o.id = p.oid')->where($where)->order('p.filename')->limit('0,2000')->select();
			$root = '/data/webroot/water.com/app/web/static/2water/train';
			
			foreach($ps as $p){
				$pfile = $picdir . '/' .date('Ym', strtotime($p['ydate'])).'/'.$p['oid'].'/'.$p['filename'].'.'.$p['extension'];
				copy($pfile, $root.'/'.$ks[$p['mcid']].'/'.$p['filename'].'.'.$p['extension']);
			}
			// valid pics
			$where = "p.mcid={$k} and p.status=1 and p.matching>95 and p.updatetime > '2022-07-01 00:00:00'";
			$ps = db('photos')->alias('p')->field('p.*, o.ydate, o.office')->join('orders o', 'o.id = p.oid')->where($where)->order('p.filename')->limit('0,300')->select();
			$root = '/data/webroot/water.com/app/web/static/2water/valid';
			foreach($ps as $p){
				$pfile = $picdir . '/' .date('Ym', strtotime($p['ydate'])).'/'.$p['oid'].'/'.$p['filename'].'.'.$p['extension'];
				copy($pfile, $root.'/'.$ks[$p['mcid']].'/'.$p['filename'].'.'.$p['extension']);
			}
		}

		
		echo 'done';
		
	}
	
	public function svnupdate()
	{
		pr(exec('svn up --force --no-auth-cache --username ct --password Water~12123'));
	}
	
	public function img($id){
		$p = db('photos')->alias('p')->field('p.oid, p.filename, p.extension, o.etime')->join('orders o', 'o.id = p.oid')->where("p.id = '{$id}'")->find();
		$pdate = substr(str_replace('-', '', $p['etime']), 0, 6);
		$src = '/source/pics/' . $pdate . '/' . $p['oid'] . '/' . $p['filename'] . '.' . $p['extension'];
		echo "<a href=\"{$src}\" target=\"_blank\"><img style=\"width:80%;\" src=\"{$src}\"></a>";
	}
	
	public function j($oid = '', $rid = '')
	{
		if($rid)pr(json_decode(iconv("GBK", "UTF-8", file_get_contents(APP_PATH . '../source/receive/' . db('receives')->where("id='{$rid}'")->value('filepath'))), 1));
		if($oid)pr(json_decode(iconv("GBK", "UTF-8", file_get_contents(APP_PATH . '../source/receive/' . db('receives')->where("id=(select rid from orders where id = {$oid})")->value('filepath'))), 1));
	}
	
	//生成json文件
	public function sjson($o = '', $p = '')
	{
		$f = date('YmdHis') . substr(microtime(), 2, 6);
		$orders = db('orders')->where($o)->select();
		foreach($orders as $order){
			$photos = db('photos')->where(['oid'=>$order['id']])->where($p)->select();
			//if(!$photos) continue;
			$order['photos'] = $photos;
			$data['orders'][] = $order;
		}
		file_put_contents(APP_PATH . '../source/pics/' . $f . '.json', json_encode($data, 320));
		return $f;
	}
	
	public function test(){
		echo exec('cat /opt/zheng/dpfinder/config/conf.yaml', $a);
		echo '<pre>';
		print_r($a);
	}
	
	public function morder($where = '')
	{
		$orders = db('orders')->where('type=0')->where($where)->select();
		$ts = db('ycodes')->where("otype = 0")->value('GROUP_CONCAT(id)');
		$audit = new \Audit;
		foreach($orders as $order){
			$photos = db()->query('select GROUP_CONCAT(code) codes from(SELECT code FROM `photos` WHERE  ( ycode in('. $ts .') and status = 1 and oid in(select id from orders where fyid="'. $order['yid'] .'") ) GROUP BY `code`) s');
			$ms = explode(',', $photos[0]['codes']);
			$status = $audit->audit(0, $ms) ? 1 : 2;
			db('orders')->where(['id'=>$order['id']])->update(['status'=>$status, 'updatetime'=>$time]);
			lg("-处理母单	{$order['gid']}({$order['id']})：". ($status == 1 ? '通过' : '未通过'));
		}
		return ok;
	}
	
	public function sorder($where = '')
	{
		$zs = explode(',', db('ycodes')->where("otype = 0")->value('GROUP_CONCAT(id)'));
		$orders = db('orders')->field('id, fyid')->where('type=1')->where($where)->select();
		$audit = new \Audit;
		$fos = [];
		foreach($orders as $order){
			$photos = db('photos')->field('ycode, code')->where("oid = {$order['id']} and status = 1")->group('code')->select();
			$fs = [];
			foreach($photos as $photo){
				if(in_array($photo['ycode'], $zs)){
					$fos[$order['fyid']][$photo['code']] = 1;
				}else $fs[] = $photo['code'];
			}
			$as = $audit->audit(1, $fs);
			db('orders')->where(['id' => $order['id']])->setField('status', $as[0] ? 1 : 2);
			foreach($as[1] as $f){
				db()->query("replace into `order_adopt` set `oid` = {$order['id']}, `cate` = {$f}");
			}
		}
		foreach($fos as $yid => $ms){
			$order = db('orders')->where("yid='{$yid}'")->find();
			if(!$order){
				echo $yid . '<br />';
				continue;
			}
			$as = $audit->audit(0, array_keys($ms));
			db('orders')->where(['id' => $order['id']])->setField('status', $as[0] ? 1 : 2);
			foreach($as[1] as $f){
				db()->query("replace into `order_adopt` set `oid` = {$order['id']}, `cate` = {$f}");
			}
		}
		return ok;
	}
	
	public function rs(){
		$redis = $this->redis;
		$rs = $redis->keys('p:list*');
		echo '<pre>';
		foreach($rs as $r){
			$h = $redis->SMEMBERS($r);
			foreach($h as $v){
				$redis->hset('p:res', $v, str_replace('p:list:', '', $r));
			}
		}
		return ok;
	}
	
	//打包工单数据及图片
	function tar($o = '', $p = '')
	{
		lg('开始打包');
		$data = [];
		$files = '';
		$orders = db('orders')->where($o)->select();
		if(!$orders){
			lg('图片打包结束 无内容');
			return ok;
		}
		$tar = date('YmdHis') . substr(microtime(), 2, 6) . '.tar';
		//file_put_contents(APP_PATH . '../../water.com/app/web/ecgs/tar.json', json_encode(['file' => $tar, 'md5' => '', 'size' => 0, 'time' => time(), 'status' => -1]));
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
			//file_put_contents(APP_PATH . '../../water.com/app/web/ecgs/tar.json', json_encode(['file' => $tar, 'md5' => explode(' ', $output[1])[0], 'size' => explode(' ', $output[0])[0], 'time' => $data['time'], 'status' => 0]));
			lg('打包结束：' . $tar);
		}else{
			lg('打包失败');
		}
		@unlink(APP_PATH . '../source/pics/' . $tar . '.list');
		@unlink(APP_PATH . '../source/pics/' . $tar . '.json');
		if(!$exec) return false;
		return "http://180.169.66.162:22234/ecgs/{$tar}";
	}
	
	public function mt($file, $type = 1)
	{
		if($_POST['path']) $file = $_POST['path'];
		if($_FILES["image"]["tmp_name"]){
			$file = '/source/pics/test/' . md5_file($_FILES["image"]["tmp_name"]) . '.jpg';
			move_uploaded_file($_FILES["image"]["tmp_name"], APP_PATH . '..' . $file);
		}
		$url = 'http://192.168.100.215:808';
		$ch = curl_init($url);
		curl_setopt($ch, 47, 1);
		curl_setopt($ch, 19913, 1);
		curl_setopt($ch, 10023, ["Content-type: multipart/form-data"]);
		curl_setopt($ch, 10015, ['type'=>$type, 'path'=> APP_PATH . '..' . $file]);
		$rtn = curl_exec($ch);
		curl_close($ch);
		return $rtn . ($file ? "<br><br><a href='{$file}' target='_blank'><img src='{$file}' width='640' /></a>" : '');
	}
	
	public function mat(){
		$place = \Param::place();
		$station = \Param::station();
		$office = \Param::office();
		$ycodes = \Param::ycodes(0, '');
		$ps = db('photos')->alias('p')->field('p.oid, p.filename, p.extension, p.matching, p.ycode, o.etime, o.place, o.station, o.office, o.gid')->join('orders o', 'o.id = p.oid')->where("o.etime >= 20210501 and o.etime <= 20210510 and p.matching < 80 and p.status = 1")->select();
		echo "工单编号\t所\t站\t维保单位\t分类\t相似度\t照片地址\n";
		foreach($ps as $p){
			$pdate = substr(str_replace('-', '', $p['etime']), 0, 6);
			$src = 'http://192.168.100.215:22235/source/pics/' . $pdate . '/' . $p['oid'] . '/' . $p['filename'] . '.' . $p['extension'];
			echo "{$p['gid']}\t{$place[$p['place']]}\t{$station[$p['station']]}\t{$office[$p['office']]}\t{$ycodes[$p['ycode']]}\t{$p['matching']}\t{$src}\n";
		}
	}
	
	public function tj($m, $t = 0, $c = 80)
	{
		$c = (float)$c;
		echo '<pre>';
		$fs2 = \Param::fs2();//print_r($fs2);
		$fs5 = \Param::fs5();//print_r(array_values($fs5));die;
		foreach($fs5 as $fs){
			foreach($fs as $f){
				$fs0[] = $f;
			}
		}//print_r($fs0);die;
		echo "分类&nbsp;&nbsp;&nbsp;	总数	合格	合格率\n";
		if($t == 0){//按分类
			foreach($fs2 as $f => $fs){
				$z = db('photos')->where("oid in(select id from orders where date_format(etime, '%Y%m') = {$m})")->where(['ycode'=>$fs])->count();$zs += $z;
				$h = db('photos')->where("oid in(select id from orders where date_format(etime, '%Y%m') = {$m})")->where(['ycode'=>$fs, 'matching' => ['gt', $c], 'code'=>['in', $fs5[$f]]])->count();$hs += $h;
				echo "{$fs}". str_repeat('&nbsp;', (15 - strlen($fs)) / 3) ."	{$z}	{$h}	" . ($z ? ceil(($h / $z) * 1000000) / 10000 : 0) . "\n";
			}
		}elseif($t == 1){//按管理所
			$place = \Param::place();//print_r($place);die;
			foreach($place as $p => $ps){
				$z = db('photos')->where("oid in(select id from orders where place={$p} and date_format(etime, '%Y%m') = {$m})")->where(['ycode'=>['in', $fs2]])->count();$zs += $z;
				//echo db()->getLastSql();die;
				$h = db('photos')->where("oid in(select id from orders where place={$p} and date_format(etime, '%Y%m') = {$m})")->where(['ycode'=>['in', $fs2], 'matching' => ['gt', $c], 'code'=>['in', $fs0]])->count();$hs += $h;
				//echo db()->getLastSql();die;
				$ps = str_replace('供水', '', $ps);
				echo "{$ps}". str_repeat('&nbsp;', (15 - strlen($ps)) / 3) ."	{$z}	{$h}	" . ($z ? ceil(($h / $z) * 1000000) / 10000 : 0) . "\n";
			}
		}elseif($t == 2){//按管理站
			$station = \Param::station();//print_r($place);die;
			foreach($station as $s => $ss){
				$z = db('photos')->where("oid in(select id from orders where station={$s} and date_format(etime, '%Y%m') = {$m})")->where(['ycode'=>['in', $fs2]])->count();$zs += $z;
				//echo db()->getLastSql();die;
				$h = db('photos')->where("oid in(select id from orders where station={$s} and date_format(etime, '%Y%m') = {$m})")->where(['ycode'=>['in', $fs2], 'matching' => ['gt', $c], 'code'=>['in', $fs0]])->count();$hs += $h;
				//echo db()->getLastSql();die;
				echo "{$ss}". str_repeat('&nbsp;', (15 - strlen($ss)) / 3) ."	{$z}	{$h}	" . ($z ? ceil(($h / $z) * 1000000) / 10000 : 0) . "\n";
			}
		}elseif($t == 3){//按维保单位
			$office = \Param::office();//print_r($place);die;
			foreach($office as $o => $os){
				$z = db('photos')->where("oid in(select id from orders where office={$o} and date_format(etime, '%Y%m') = {$m})")->where(['ycode'=>['in', $fs2]])->count();$zs += $z;
				//echo db()->getLastSql();die;
				$h = db('photos')->where("oid in(select id from orders where office={$o} and date_format(etime, '%Y%m') = {$m})")->where(['ycode'=>['in', $fs2], 'matching' => ['gt', $c], 'code'=>['in', $fs0]])->count();$hs += $h;
				//echo db()->getLastSql();die;
				echo "{$os}". str_repeat('&nbsp;', (15 - strlen($os)) / 3) ."	{$z}	{$h}	" . ($z ? ceil(($h / $z) * 1000000) / 10000 : 0) . "\n";
			}
		}
		echo "汇总&nbsp;&nbsp;&nbsp;	{$zs}	{$hs}	" . ceil(($hs / $zs) * 1000000) / 10000 . "%\n";
		//echo db()->getLastSql();
	}
	
	public function kk(){
		$rs = db('receives')->field('filepath')->where('id in(select rid from orders where id in(select oid from photos where ycode = 0))')->select();
		$ycodes = \Param::ycodes(1, '');
		foreach($rs as $r){
			$f = $PATH.$r['filepath'];
			$jdata = json_decode(iconv("GBK", "UTF-8", file_get_contents(APP_PATH . '../source/receive/' . $r['filepath'])), 1);
			foreach($jdata as $data){
					foreach($data['Media'] as $pic){
							$p = pathinfo($pic['path']);
							db('photos')->where(['filename' => $p['filename']])->setField('ycode', (int)$ycodes[$pic['name']]);
						}
					
				}
		}
	}

	public function catpics(){
		$rs = db('category')->field('name, mcode')->where('gtype=0 and mcode is not null')->select();
		$urls = [];	
		foreach($rs as $r){
			$cname = $r['name'];
			$mcode = $r['mcode'];
			$ps = db('photos')->alias('p')->field('p.id, p.oid, o.etime, p.filename, p.extension, p.matching')->join('orders o', 'o.id = p.oid')->where("p.code='{$mcode}' and p.status=1 and o.gdtype=0 and o.etime >= '2022-09-01'")->order('p.matching')->limit('0,50')->select();
			//echo "-------------------------<br>";
			foreach($ps as $p){
				//echo $p['id'].'---', $p['matching'].'<br>';
				$urls[] = [$r['name'], 'http://180.169.66.162:22235/source/pics/'.date('Ym',strtotime($p['etime'])).'/'.$p['oid'].'/'.$p['filename'].'.'.$p['extension']];
			}
			
		}

		echo json_encode($urls, true);
	}

	public function checkorders() {
		$t = "江浦站,上海水务建设工程有限公司,1,6,5
汉阳站,浙江舜元建设有限公司,1,3,20
曲阳站/淞南站,上海坤郡建设工程有限公司,1,1|2,4
市光站,上海鑫长泰建设有限公司,1,4,15
密云站,上海惠品实业有限公司,1,5,3

龙华站/上中站,上海宇联给水工程有限公司,3,17|19,9
天钥桥站,上海水务建设工程有限公司,3,18,5
平塘站,上海耐标建设工程发展有限公司,3,16,7
虹桥站,上海城建水务工程有限公司,3,14,2
芙蓉江站,上海宏展自来水设备安装工程有限公司,3,15,8

武宁站/银杏站,上海精隆建筑工程有限公司,7,33|36,18
大场站,上海徐嘉建设工程有限公司,7,34,17
真北站,上海天德建设集团有限公司,7,35,22

新闸站/半淞园站,上海城建水务工程有限公司,2,7|8,2
瞿溪站,上海汛龙消防工程有限公司,2,9,16
长江站,上海鑫景建设工程有限公司,2,10,13
沪太站/普善站,上海水务建设工程有限公司,2,12|13,5
场中站,上海楷通市政配套安装工程有限公司,2,11,14

宝杨站,上海坤郡建设工程有限公司,5,26,4
顾太站,上海徐嘉建设工程有限公司,5,25,17
罗店站/罗泾站,上海城建水务工程有限公司,5,23|24,2

莘庄站/吴泾站,上海鸿基机电工程有限公司,4,20|22,21
江川站,上海市能水电集成有限公司,4,21,11";
		$t = "瞿溪站,上海汛龙消防工程有限公司,2,9,16";
		
		$alloids = [];
		$lines = explode("\n", $t);
		foreach($lines as $line){
			if(trim($line) == ''){
				continue;
			}
			
			$r = explode(",", $line);
			$stationName = $r[0];
			$officeName = $r[1];
			$place = $r[2];
			$station = $r[3];
			$office = $r[4];

			//
			$scond = strpos($station, "|") ? "station in(".str_replace('|',',', $station).")" : "station={$station}";
			//echo $scond. '<br>';continue;
			$os = db('orders')->field("id,pda")->where("cs=0 and type=1 and place={$place} and {$scond} and office={$office} and id in(select distinct oid from photos where realtime is not null)")->order("etime desc")->order("fyid")->limit(0, 50)->select();
			$oids = [];

			$pcount = 0; // 时间相近的数量
			foreach($os as $o){
				$oids[] = $o['id'];
				$pda = $o['pda'];

				$photos = db('photos')->where(['oid'=>$o['id']])->where("ycode=7")->select();
				// echo $o['id'].'>>'.count($photos).'<br>';
				foreach($photos as $p){
					$realtime = $p['realtime'];
        				$start = date('Y-m-d H:i:s', strtotime($realtime . ' - 3 minutes'));
        				$end = date('Y-m-d H:i:s', strtotime($realtime . ' + 3 minutes'));
					$cond = " p.oid<>{$o['id']} and p.status=1 and o.pda='{$pda}' and p.ycode=7 and p.realtime between '{$start}' and '{$end}' ";
					//echo '<br>'. $cond.'<br>';
		     		$pc = db('photos')->alias('p')->field('p.id, p.oid')->join('orders o', 'o.id = p.oid')->where($cond)->count();
					$pcount += $pc;
				}

		     	

				$alloids[] = $o['id'];
			}


			echo $stationName.' '.$officeName. '......'. count($oids). '......'.$pcount;
			echo "<br>";
		}

		echo "<br>";
		echo join(",", $alloids)."<br>";
		echo "total=".count($alloids);

	}
	
}
