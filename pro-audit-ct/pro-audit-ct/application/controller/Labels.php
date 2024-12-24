<?php 
namespace app\controller;
use Think\Controller;
class Labels extends Common{
	//订单列表
	public function index(){
		$ycode = (int)I('ycode');
		$this->user['type'] && die('您没有权限');
		$uid = $this->user['uid'];
		if($ycode){
			#$ycode = db('ycodes')->where('pcid='.$ycode)->value('id');
			$ps = db('photos')->alias('p')->join('orders o', 'o.id=p.oid')->where('p.oid in(select id from orders where appeal=5) and p.status = 2 and p.ycode = '. $ycode .' and p.id not in(select pid from photo_adopt)')->field('p.*, o.gid, left(o.etime, 10) etime, o.office, o.station, o.addr')->order('p.id')->select();
			return $ps;
		}
		$dir = I('dir');
		if($dir){
			$ps = [];//glob(APP_PATH . '../source/labels/'. $dir .'/*.jpg');
			$dir_path = APP_PATH . '../source/labels/'. $dir;
			
			if(I('t') == 4){
				//丢弃
				foreach(I('ids/a') as $pid){
					db()->execute("replace into photo_adopt set uid = {$uid}, pid = {$pid}, type = 0");
				}
				return 1;
			}
			if(I('t') == 3){
				//归档
				$cid = (int)I('cid');
				foreach(I('ids/a') as $pid){
					$p = db('photos')->where("id=".$pid)->find();
					if(!$p)continue;
					$o = substr(str_replace('-', '', db('orders')->where('id='.$p['oid'])->value('etime')), 0, 6);
					$path = '/source/pics/' . $o . '/' . $p['oid'] . '/' . $p['filename'] . '.' . $p['extension'];
					$file = APP_PATH . '..' . $path;
					if(!file_exists($file)){
						$file = 'http://180.169.66.162:22235' . $path;
						lg($file);
					}
					if(!file_put_contents($dir_path . '/' . $p['filename'] . '.' . $p['extension'], file_get_contents($file))) continue;
					db()->execute("replace into photo_adopt set uid = {$uid}, pid = {$pid}, type = 1, cid = {$cid}");
				}
				return 1;
			}
			
			$dir = opendir($dir_path);
			if($dir){
				while(($file = readdir($dir)) !== false) {
					if($file !== '.' && $file !== '..') {
						$ps[] = $file;
					}
				}
				closedir($dir);
			}
			return $ps;
		}
		$this->title = '照片归档';
		$tmp['ls'] = db('ycodes')->field('id,pcid,name')->where('type=2')->order('id')->select();
		$tmp['rs'] = db('category')->field('id,pid,name,title')->where('cate = 2')->order('id')->select();
		$this->assign('tmp', $tmp);
		$this->assign('title', '照片归档');
		return $this -> fetch();
	}
	
	public function pc(){
		$this->user['uid'] == 1 or die('您没有权限');
		$type = (int)I('type');
		$pid = (int)I('pid') or die('缺少参数');
		$ypid = $pid;
		if($type == 1){
			db('likes')->where("pid = {$pid} or pid2 = {$pid}")->setField('x', 0);
			db('orders')->where("id = (select oid from photos where id = {$pid})")->setDec('cs');
		}else{
			db('likes')->where('id', $pid)->setField('x', 0);
			$pid = db('likes')->where('id', $pid)->value('pid');
			if(db('likes')->where("pid = {$pid} and x = 1")->count() <= 0) db('orders')->where("id = (select oid from photos where id = {$pid})")->setDec('cs');
		}
		db('unlikes')->insert(['type' => $type, 'pid' => $ypid, 'uid' => $this->user['uid']]);
		return 1;
	}
	
	
	
}