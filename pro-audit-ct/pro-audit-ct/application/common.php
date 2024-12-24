<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
    function check_smartlock($orders){
        $oids = [];

        foreach ($orders as $o) {
            // 只处理子单
            if(!$o['type']){
                continue;
            }
            $oids[] = $o['id'];
        }

        lg("检测智能锁, 工单数：".count($oids)."个");

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

        $jsonfile =  '/home/www/2water.com/source/pics/smartlocks/' . $f . '.json';
	file_put_contents($jsonfile, json_encode($data, 320));
	// 写入到文件
	$orders1= $orders;
	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                          // 写入到文件
	$result = file_put_contents('/home/www/2water.com/source/pics/smartlocks/orders.txt', $contentToWrite);

        

        lg("jsonfile:". $jsonfile);

        // 批量检测照片是否有智能锁
	ini_set('max_execution_time', 1200); // 设置exec时间20分钟
	$smartLockDetect=1;
	$LL='sudo docker exec -i lavis bash -c "cd /home/LAVIS && python /app/smartlocks/detect_smartlocks.py '."\"/source/pics/smartlocks/".$f . '.json""';
	// 写入到文件
	$orders1=$LL;
	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
     	   // 写入到文件
	$result = file_put_contents('/home/www/2water.com/source/pics/smartlocks/LL.txt', $contentToWrite);

        //$smartLockDetect = exec($LL);
	exec($LL . ' 2>&1', $output, $return_var);
	//$smartLockDetect=exec('whoami');
        //lg($smartLockDetect);
	// 写入到文件
	$orders1= $output;
	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
        // 写入到文件
	$result = file_put_contents('/home/www/2water.com/source/pics/smartlocks/output.txt', $contentToWrite);


        $photos = json_decode(file_get_contents($jsonfile), 1);
	//$photos = json_decode(file_get_contents('/home/www/2water.com/source/pics/smartlocks/20240228124823027014.json'), 1);
        foreach($photos as $p){
            if(isset($p['tabs'])){
                lg("smartlocks:". $p['id']. ' '. $p['tabs']);
                db('photos')->where('id='.$p['id'])->update(['tags'=>$p['tabs']]);
            }
        }

    }

	function U($url, $pama = []){
		return url($url, $pama);
	}

	function I($i){
		return input($i);
	}
	
	//记录实时日志
	function lg($msg)
    {
		$file = APP_PATH . '../runtime/log/' . date('Ymd') . '.log';
		$lg = fopen($file, "a+");
		fwrite($lg, seconds() . "\t" . $msg . "\n");
		fclose($lg);
    }
	
	//生成当前系统时间(毫秒)
	function seconds($type = 1){
		list($usec, $sec) = explode(' ', microtime());
		if($type == 1) return date('His') . substr($usec, 1, 4);
		return ($usec + $sec);
	}
	
	function mb_cut($str, $len = 20, $char = 'utf8'){
		if(mb_strlen($str, $char) <= $len) return $str;
		return mb_substr($str, 0, $len, $char) . '...';
	}
	
	function pr($r, $d = 0)
	{
		echo '<pre>';
		print_r($r);
		echo '</pre>';
		if($d) die;
	}