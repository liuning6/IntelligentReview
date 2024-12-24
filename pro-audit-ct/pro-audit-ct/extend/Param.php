<?php

class Param
{
	//业务类型
    public function gdtypes($type = 0)
	{
		$gdtypes = db('gdtypes')->field('type,name')->order('id')->select();
		foreach($gdtypes as $gdtype){
			if(!$type) $gs[$gdtype['type']] = $gdtype['name']; else $gs[$gdtype['name']] = $gdtype['type'];
		}
		return $gs;
	}
	
	//维保单位
    public static function office($type = 0)
	{
		$offices = db('office')->field('id,name')->order('id')->select();
		foreach($offices as $office){
			if(!$type) $gs[$office['id']] = $office['name']; else $gs[$office['name']] = $office['id'];
		}
		return $gs;
	}
	
	//管理所
	public static function place($type = 0)
	{
		$places = db('place')->order('seq')->select();

		foreach($places as $place){
			if(!$type) $gs[$place['id']] = $place['name']; else $gs[$place['name']] = $place['id'];
		}

		return $gs;
	}
	
	//管理站
	public static function station($type = 0)
	{
		$stations = db('station')->order('seq,id')->select();
		if($type == 2) $place = self::place();
		foreach($stations as $station){
			if(!$type){
				$gs[$station['id']] = $station['name'];
			}elseif($type == 1){
				$gs[$station['name']] = $station['id'];
			}else{
				$gs[$station['id']] = [$station['id'], $station['name'], $station['place_id'], $place[$station['place_id']]];
			}
		}
		return $gs;
	}
	
	//管理所、站
	public static function ps()
	{
		$places = self::place();
		foreach($places as $i => $place){
			$ps[$i][0] = $place;
			foreach(db('station')->where(['place_id'=>$i])->order('id')->select() as $station){
				$ps[$i][1][$station['id']] = $station['name'];
			}
		}
		return $ps;
	}
	
	//原始分类
	public function ycodes($type = 0, $where = 'type>0')
	{
		$ycodes = db('ycodes')->where($where)->order('type')->select();
		foreach($ycodes as $ycode){
			if(!$type) $gs[$ycode['id']] = $ycode['name']; else $gs[$ycode['name']] = $ycode['id'];
		}
		return $gs;
	}
	
	//模型分类
	public function fs($type = 0)//用于自动执行脚本，整理模型分类代号
	{
		$fs = db('category')->field('mcode,name')->where('mcode > 0')->order('id')->select();
		foreach($fs as $f){
			if(!$type) $gs[$f['mcode']] = $f['name']; else $gs[$f['name']] = $f['mcode'];
		}
		return $gs;
	}
	
	public function fs2($type = 0, $gtype = 0)//大类
	{
		$fs2 = db('category')->field('id,name')->where('gtype='. (int)$gtype .' and cate=1')->select();
		//$fs2[] = ['id' => 0, 'name' => '其它'];
		foreach($fs2 as $f){
			if(!$type) $gs[$f['id']] = $f['name']; else $gs[$f['name']] = $f['id'];
		}
		return $gs;
	}
	
	public function fs3($type = 0)//detail页面,照片分类名称展示
	{
		$fs3 = db('category')->field('mcode,title')->where('mcode > 0')->order('id')->select();
		$fs3[] = ['mcode' => 1, 'title' => '其它'];
		foreach($fs3 as $f){
			if(!$type) $gs[$f['mcode']] = $f['title']; else $gs[$f['title']] = $f['mcode'];
		}
		return $gs;
	}
	
	public function fs5()//主分类下的子分类
	{
		$fs5 = db('category')->field('pid,scode')->where('scode > 0')->select();
		foreach($fs5 as $f){
			$gs[$f['pid']][] = $f['scode'];
		}
		return $gs;
	}
	
	public function fs6($gtype = 0)//用于前端审核规则
	{
		$fs6 = [];
		$ps = db('category')->field('id,type,name')->where('gtype='. (int)$gtype .' and cate=1')->order('id')->select();
		foreach($ps as $p){
			$fs6[$p['id']] = $p;
			$cs = db('category')->field('name,scode,mode')->where('gtype=' . (int)$gtype. ' and mode and pid='.$p['id'])->order('id')->select();
			foreach($cs as $c){
				$fs6[$p['id']]['fs'][$c['mode']][] = $c['scode'];
			}
		}
		return $fs6;
	}
	
	public function fs7($gtype = 0)//大类带类型
	{
		$fs7 = db('category')->field('id,type,name')->where('gtype='. (int)$gtype .' and cate=1')->select();
		foreach($fs7 as $f){
			$gs[$f['id']] = [$f['type'], $f['name']];
		}
		return $gs;
	}
	public function fs8()//主分类下的子分类;代替Statistics.php  663行中的fs5
        {
        	$fs5 = db('category')->field('id,scode')->where('scode > 0')->select();
        	foreach($fs5 as $f){
                	$gs[$f['id']][] = $f['scode'];//pid为照片类型，scode大概是子分类照片代码
        	}
        	return $gs;
        }
    public function fs9()//主分类下的子分类;代替Statistics.php  663行中的fs5
    {
        $fs5 = db('category')->where("pid in(select id from category where gtype=0 and cate=1)")->select();

        foreach($fs5 as $f){
            $gs[$f['id']][] = $f['scode'];//pid为照片类型，scode大概是子分类照片代码
        }
	// 写入到文件
			$orders1=$gs;
	            	$contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
                        // 写入到文件
	            	$result = file_put_contents('/home/www/2water.com/gs.txt', $contentToWrite);

        return $gs;
    }
	
	public function yps()
	{
		$ycodes = db('ycodes')->field('id,pcid')->select();
		foreach($ycodes as $ycode){
			$gs[$ycode['id']] = $ycode['pcid'];
		}
		return $gs;
	}
	
	public function ccodes($gtype = 0)
	{
		return db('ycodes')->where("pcid in(SELECT id FROM `category` where gtype = {$gtype} and cate = 1)")->value('GROUP_CONCAT(id)');
	}
	
}