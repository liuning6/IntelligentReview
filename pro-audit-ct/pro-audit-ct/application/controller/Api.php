<?php
namespace app\controller;
use think\Controller;
use think\Db;
class Api extends Controller
{
	
	public function _initialize()
	{
		set_time_limit(0);
		ignore_user_abort(true);    //关掉浏览器，PHP脚本也可以继续执行.
	}
	
	public function Index()
	{
		return 'Welcome to xunzhi api V1.0';
    }
	
	public function OrderReceive()
	{
		$info = file_get_contents('php://input') or die('-1');
		$path = date('Ym/d');
		$dir = APP_PATH . '../source/receive/' . $path;

		if(!is_dir($dir)) @mkdir($dir, 0777, 1);
		$name = md5($info);
		$filepath = $path . '/' . $name . '.json';
		$realpath = $dir . '/' . $name . '.json';
		if(file_put_contents($realpath, $info)){
			if(!$this->jc($info)) return -3;
			db('receives')->insert(['filepath'=>$filepath]);
			return 1;
		}
		return -2;
    }

public function OrderReceive2()
{
    $info = file_get_contents('php://input') or die('-1');
    $path = date('Ym/d');
    $dir = APP_PATH . '../source/receive/' . $path;
    
    if (!is_dir($dir)) {
        $m = @mkdir($dir, 0777, true); 
    }

    $name = md5($info);
    $filepath = $path . '/' . $name . '.json';
    $realpath = $dir . '/' . $name . '.json';

    $r = file_put_contents($realpath, $info);
    echo 'path=' . $realpath . '=='.$r;

    if ($r) {
        $jcResult = $this->jc1($info); // 将结果保存到变量中
    echo 'jc result: ';
	print_r($jcResult);
    echo 'Array size:'.count($jcResult); // 打印数组大小
        if (!$this->jc1($info)) { 
            return -3; 
        }

        // db('receives')->insert(['filepath' => $filepath]);
        return 1;
    }

    return -2;
}

public function OrderReceive3()
	{
		$info = file_get_contents('php://input') or die('-1');
		$path = date('Ym/d');
		$dir = APP_PATH . '../source/receive/' . $path;

		if(!is_dir($dir)) @mkdir($dir, 0777, 1);
		$name = md5($info);
		$filepath = $path . '/' . $name . '.json';
		$realpath = $dir . '/' . $name . '.json';
		if(file_put_contents($realpath, $info)){
			if(!$this->jc1($info)) return -3;
			db('receives')->insert(['filepath'=>$filepath]);
			return 1;
		}
		return -2;
    }
	
	protected function jc($info)
	{
		$info  = json_decode(iconv("GBK", "UTF-8", $info), 1);
		if(!$info) return 0;
		foreach($info as $k){
			if($k['FCUSTOMSERVICEID'] != ''){
				if($k['GDTYPE'] == '水箱清洗') $vs = ['门牌' => 1, '停水通知' => 1, '含氧量' => 1, '喷洒消毒剂' => 1, '浑浊度' => 1, '总氯' => 1, '清洗后水质现场检测情况公示' => 1]; elseif($k['GDTYPE'] == '泵房养护') $vs = ['工作证' => 1, '泵房位置' => 1, '泵房内环境' => 1, '水泵机组全景' => 1, '电控柜外部' => 1, '电控柜内部' => 1, '泵房维保记录' => 1]; else $vs = [];
				foreach($k['Media'] as $v){
					unset($vs[$v['name']]);
				}
				echo 'vs'.$vs;
				if(count($vs)) return 0;
			}
		}
		return 1;
	}

	protected function jc1($info)
	{
		$info = json_decode($info, true); // 直接解码，不进行编码转换
        if (!$info) return 0; // 如果解码失败，返回0
		//return $info;
    foreach ($info as $k) {
        if ($k['FCUSTOMSERVICEID'] != '') {
            if ($k['GDTYPE'] == '水箱清洗') {
                $vs = ['门牌' => 1, '停水通知' => 1, '含氧量' => 1, '喷洒消毒剂' => 1, '浑浊度' => 1, '总氯' => 1, '水箱锁' => 1];
            } elseif ($k['GDTYPE'] == '泵房养护') {
                $vs = ['工作证' => 1, '泵房位置' => 1, '泵房内环境' => 1, '水泵机组全景' => 1, '电控柜外部' => 1, '电控柜内部' => 1, '泵房维保记录' => 1];
            } else {
                $vs = [];
            }
            foreach ($k['Media'] as $v) {
                unset($vs[$v['name']]);
            }
            echo 'vs1: ' . json_encode($vs) . PHP_EOL; // 使用 json_encode 打印 vs 数组
            if (count($vs)) return 0;
        }
    }
    return 1;
	}
	
}
