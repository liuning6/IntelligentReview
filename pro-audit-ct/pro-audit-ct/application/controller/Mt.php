<?php
namespace app\controller;
use think\Controller;
//use think\Db;
class Mt extends Controller
{
	
	public function _initialize()
	{
		set_time_limit(0);
		//ignore_user_abort(true);
	}
	
	public function index()
	{
		$res = 'RES';
		$threshold = '0.8';
		if($_POST){
			$file = $_FILES['image']['tmp_name'];
			$res = $_POST['res'];
			$threshold = (float)$_POST['threshold'];
			$out  = $this->mt($file, $res, $threshold);
			$out .= '<br><br><img src="data:image/jpeg;base64,'. base64_encode(file_get_contents($file)) .'" style="width:640px;" />';
		}
		$this->assign('res', $res);
		$this->assign('threshold', $threshold);
		$this->assign('out', $out);
		return $this->fetch();
    }
	
	public function ecgs()
	{
		if($_POST){
			$file = $_FILES['image']['tmp_name'];
			$out  = $this->mt2($file);
			$out .= '<br><br><img src="data:image/jpeg;base64,'. base64_encode(file_get_contents($file)) .'" style="width:640px;" />';
		}
		$this->assign('out', $out);
		return $this->fetch();
    }
	
	protected function mt($file, $res = 'RES', $threshold = 0.01)
	{
		$r = exec("/usr/bin/python3 /data/wwwroot/2water_pv/python/mt.py {$file} {$res} {$threshold}");
		return str_replace("'", '"', $r);
	}
	
	protected function mt2($file)
	{
		$r = exec("/usr/bin/python3 /data/wwwroot/2water_pv/python/ecgs.py {$file}");
		return str_replace("'", '"', $r);
	}
	
}
