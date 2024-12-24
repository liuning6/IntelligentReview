<?php 
namespace app\controller;
use Think\Controller;
class Repeated extends Common{

    public function index2(){

        $map = [];
        $type  =  I('type');
        $fyid  =  (string)I('fyid');
        $gtype = (int)$this->user['gtype'];
        $map['gdtype'] = $gtype;
        if(!$type && !$fyid){
            $otype = 0;
            $map['type'] = 0;
        }else{
            $otype = 1;
            if($gtype){
                $map['type'] = 1;
            }else{
                $ztype  =  I('ztype');
                if($ztype != ''){
                    $ztype = max(0, min(1, (int)$ztype));
                    $map['type'] = $ztype;
                }
            }
            if($fyid) $map['fyid'] = $fyid;
        }
        $status  =  I('status');
        if($status != ''){
            $status = max(0, min(2, (int)$status));
            $map['status'] = $status;
        }
//		$cs = I('cs');
//		if($cs != ''){
//			$cs = max(0, $cs);
//			if(!$cs) $map['cs'] = 0; else $map['cs'] = ['egt', $cs];
//		}
//        $map['cs'] = ['egt', 1];
        if($otype){
            $map['cs'] = ['egt', 1];

        }

        $gdtype  =  I('gdtype');
        if($gdtype != ''){
            $gdtype = max(0, min(2, (int)$gdtype));
            $map['gdtype'] = $gdtype;
        }
        $office  =  I('office');
        if($office != ''){
            $office = max(0, min(22, (int)$office));
            $map['office'] = $office;
        }
        $place  =  I('place');
        if($place != ''){
            $place = max(1, min(7, (int)$place));
            $map['place'] = $place;
        }
        $station  =  I('station');
        if($station != ''){
            $station = max(1, min(36, (int)$station));
            $map['station'] = $station;
        }

        if($this->user['type'] == 2){
            $map['place'] = $this->user['office'];
        }
        if($this->user['type'] == 3){
            $map['station'] = $this->user['office'];
        }
//
//		$addr  =  I('addr');
//		if($addr != ''){
//			$map[] = ['exp', db()->raw("village_addr like '%{$addr}%' or village like '%{$addr}%' or addr like '%{$addr}%' or gid like '%{$addr}%'")];
//		}

        $start = I('start');
        $end = I('end');
        if(!$start && !$end && !$fyid){
            $start = date('Y-m', strtotime('last day of -1 months')) . '-26';
            $day = date('d');
            if($day > 25) $day = 25;
            $end = date('Y-m') . '-' . $day;
        }
        $start && $map['etime'] = ['egt', $start];
        $end && $map['etime'] = ['elt', $end];
        $start && $end && $map['etime'] = [['egt', $start], ['elt', $end. ' 23:59:59']];

        vendor('Page');

        if($otype){
            //子单
            $count = db('orders')->where($map)->count();// 查询满足要求的总记录数
            $Page  = new \Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
            $data = db('orders')->where($map)->order($fyid ? 'type' : 'etime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        }else{
            //母单
            $subquery = "yid in(select distinct fyid from orders where cs>0)";
            $count = db('orders')->where($map)->where($subquery)->count();// 查询满足要求的总记录数
            $Page  = new \Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
//            $data = db('orders')->where($map)->where($subquery)->order($fyid ? 'type' : 'etime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            $data = db('view_orders')->where($map)->where($subquery)->order('place_v')->limit($Page->firstRow.','.$Page->listRows)->select();
        }

        var_dump($map);exit();

        $show  = $Page->show();// 分页显示输出

        $zs = db('ycodes')->where("otype = 0")->value('GROUP_CONCAT(id)');//print_r($zs);
        if($otype){
            foreach($data as $k=>$v){
                $data[$k]['ks'] = db('order_adopt')->where(['oid'=>$v['id']])->value('group_concat(`cate`)');
                if(!$v['type']){
                    $data[$k]['addr'] = "【{$v['village']}】{$v['village_addr']}";
                    $data[$k]['rs'] = db()->query("select GROUP_CONCAT(r) rs from (SELECT concat(ycode, ':', count(ycode)) r FROM `photos` WHERE `oid` in(select id from orders where fyid = '{$v['yid']}') AND `ycode` IN ({$zs}) GROUP BY `ycode`) s")[0]['rs'];
                }else{
                    $data[$k]['rs'] = db()->query("select GROUP_CONCAT(r) rs from (SELECT concat(ycode, ':', count(ycode)) r FROM `photos` WHERE `oid` = {$v['id']}". ($v['gdtype'] ? '' : " AND `ycode` NOT IN ({$zs})") ." GROUP BY `ycode`) s")[0]['rs'];
                }
            }
            if($fyid){
                $m = db('orders')->where(['yid'=>$fyid])->find();
                if($m['gdtype'] == 0){
                    $m['addr'] = "【{$m['village']}】{$m['village_addr']}";
                    $m['ks'] = db('order_adopt')->where(['oid'=>$m['id']])->value('group_concat(`cate`)');
                    $m['rs'] = db()->query("select GROUP_CONCAT(r) rs from (SELECT concat(ycode, ':', count(ycode)) r FROM `photos` WHERE `oid` in(select id from orders where fyid = '{$m['yid']}') AND `ycode` IN ({$zs}) GROUP BY `ycode`) s")[0]['rs'];
                    $data = array_merge([$m], $data);//print_r($data);
                }
            }
        }else{
            session('oindex', $_SERVER['REQUEST_URI']);
            foreach($data as $k=>$v){
                //水箱容积
//                var_dump($data);
                $sx = db()->query("select count(id) as ct, sum(volume) as volume from orders where fyid='{$data[$k]['yid']}' and equipment_type='水箱' and cs>0")[0];
                $data[$k]['tank_volume'] = $sx['volume'];
                $data[$k]['tank_count'] = $sx['ct'];
                $sx = db()->query("select count(id) as ct, sum(volume) as volume from orders where fyid='{$data[$k]['yid']}' and equipment_type='水池' and cs>0")[0];
                $data[$k]['pool_volume'] = $sx['volume'];
                $data[$k]['pool_count'] = $sx['ct'];
                //水箱数量
                //水池容积
                //水池数量
//				$data[$k]['s'] = db('orders')->cache(true, 86400)->field('count(0) `0`, count(case when status=1 then 1 end) `1`, count(case when cs>0 then 1 end) `2`')->where(['fyid'=>$v['yid']])->find();
            }
        }
        //echo '<pre>';print_r($data);die;

        $tmp['place'] = \Param::place();
        $tmp['station'] = \Param::station(2);
        $tmp['office'] = \Param::office();
        //$tmp['gdtype'] = db('gdtypes')->field('type id, name')->order('id')->select();
        $this->assign('tmp', $tmp);

        $this->assign('yps', \Param::yps());
        $this->assign('fs6', \Param::fs6($gtype));
        $this->assign('title', '工单');
        $this->assign('start', $start);
        $this->assign('end', $end);
        $this->assign('otype', $otype);
        $this->assign('data', $data);
        $this->assign('page', $show);// 赋值分页输出

        echo 'size='.count($data);
    }

	//订单列表
	public function index(){
		$map = [];
		$type  =  I('type');
		$fyid  =  (string)I('fyid');
		$gtype = (int)$this->user['gtype'];
		$map['gdtype'] = $gtype;
		if(!$type && !$fyid){
			$otype = 0;
			$map['type'] = 0;
		}else{
			$otype = 1;
			if($gtype){
				$map['type'] = 1;
			}else{
				$ztype  =  I('ztype');
				if($ztype != ''){
					$ztype = max(0, min(1, (int)$ztype));
					$map['type'] = $ztype;
				}
			}
			if($fyid) $map['fyid'] = $fyid;
		}
		$status  =  I('status');
		if($status != ''){
			$status = max(0, min(2, (int)$status));
			$map['status'] = $status;
		}
//		$cs = I('cs');
//		if($cs != ''){
//			$cs = max(0, $cs);
//			if(!$cs) $map['cs'] = 0; else $map['cs'] = ['egt', $cs];
//		}
//        $map['cs'] = ['egt', 1];
        if($otype){
            $map['cs'] = ['egt', 1];

        }

		$gdtype  =  I('gdtype');
		if($gdtype != ''){
			$gdtype = max(0, min(2, (int)$gdtype));
			$map['gdtype'] = $gdtype;
		}
		$office  =  I('office');
		if($office != ''){
			$office = max(0, min(22, (int)$office));
			$map['office'] = $office;
		}
		$place  =  I('place');
		if($place != ''){
			$place = max(1, min(7, (int)$place));
			$map['place'] = $place;
		}
		$station  =  I('station');
		if($station != ''){
			$station = max(1, min(36, (int)$station));
			$map['station'] = $station;
		}
		
		if($this->user['type'] == 2){
			$map['place'] = $this->user['office'];
		}
		if($this->user['type'] == 3){
			$map['station'] = $this->user['office'];
		}
//
//		$addr  =  I('addr');
//		if($addr != ''){
//			$map[] = ['exp', db()->raw("village_addr like '%{$addr}%' or village like '%{$addr}%' or addr like '%{$addr}%' or gid like '%{$addr}%'")];
//		}
		
		$start = I('start');
		$end = I('end');
		if(!$start && !$end && !$fyid){
			$start = date('Y-m', strtotime('last day of -1 months')) . '-26';
			$day = date('d');
			if($day > 25) $day = 25;
			$end = date('Y-m') . '-' . $day;
		}
		$start && $map['etime'] = ['egt', $start];
		$end && $map['etime'] = ['elt', $end];
		$start && $end && $map['etime'] = [['egt', $start], ['elt', $end. ' 23:59:59']];

        vendor('Page');

        if($otype){
            //子单
            $count = db('orders')->where($map)->count();// 查询满足要求的总记录数
            $Page  = new \Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
            $data = db('orders')->where($map)->order($fyid ? 'type' : 'etime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        }else{
            //母单
            $subquery = "yid in(select distinct fyid from orders where cs>0)";
            $count = db('orders')->where($map)->where($subquery)->count();// 查询满足要求的总记录数
            $Page  = new \Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
//            $data = db('orders')->where($map)->where($subquery)->order($fyid ? 'type' : 'etime desc')->limit($Page->firstRow.','.$Page->listRows)->select();

            //var_dump($map);exit();
            $data = db('view_orders')->where($map)->where($subquery)->order('place_v')->limit($Page->firstRow.','.$Page->listRows)->select();
        }

        $show  = $Page->show();// 分页显示输出

		$zs = db('ycodes')->where("otype = 0")->value('GROUP_CONCAT(id)');//print_r($zs);
		if($otype){
			foreach($data as $k=>$v){
				$data[$k]['ks'] = db('order_adopt')->where(['oid'=>$v['id']])->value('group_concat(`cate`)');
				if(!$v['type']){
					$data[$k]['addr'] = "【{$v['village']}】{$v['village_addr']}";
					$data[$k]['rs'] = db()->query("select GROUP_CONCAT(r) rs from (SELECT concat(ycode, ':', count(ycode)) r FROM `photos` WHERE `oid` in(select id from orders where fyid = '{$v['yid']}') AND `ycode` IN ({$zs}) GROUP BY `ycode`) s")[0]['rs'];
				}else{
					$data[$k]['rs'] = db()->query("select GROUP_CONCAT(r) rs from (SELECT concat(ycode, ':', count(ycode)) r FROM `photos` WHERE `oid` = {$v['id']}". ($v['gdtype'] ? '' : " AND `ycode` NOT IN ({$zs})") ." GROUP BY `ycode`) s")[0]['rs'];
				}
			}
			if($fyid){
				$m = db('orders')->where(['yid'=>$fyid])->find();
				if($m['gdtype'] == 0){
					$m['addr'] = "【{$m['village']}】{$m['village_addr']}";
					$m['ks'] = db('order_adopt')->where(['oid'=>$m['id']])->value('group_concat(`cate`)');
					$m['rs'] = db()->query("select GROUP_CONCAT(r) rs from (SELECT concat(ycode, ':', count(ycode)) r FROM `photos` WHERE `oid` in(select id from orders where fyid = '{$m['yid']}') AND `ycode` IN ({$zs}) GROUP BY `ycode`) s")[0]['rs'];
					$data = array_merge([$m], $data);//print_r($data);
				}
			}
		}else{
			session('oindex', $_SERVER['REQUEST_URI']);
			foreach($data as $k=>$v){
                //水箱容积
//                var_dump($data);
                $sx = db()->query("select count(id) as ct, sum(volume) as volume from orders where fyid='{$data[$k]['yid']}' and equipment_type='水箱' and cs>0")[0];
                $data[$k]['tank_volume'] = $sx['volume'];
                $data[$k]['tank_count'] = $sx['ct'];
                $sx = db()->query("select count(id) as ct, sum(volume) as volume from orders where fyid='{$data[$k]['yid']}' and equipment_type='水池' and cs>0")[0];
                $data[$k]['pool_volume'] = $sx['volume'];
                $data[$k]['pool_count'] = $sx['ct'];
                //水箱数量
                //水池容积
                //水池数量
//				$data[$k]['s'] = db('orders')->cache(true, 86400)->field('count(0) `0`, count(case when status=1 then 1 end) `1`, count(case when cs>0 then 1 end) `2`')->where(['fyid'=>$v['yid']])->find();
			}
		}
        //echo '<pre>';print_r($data);die;
		
		$tmp['place'] = \Param::place();
		$tmp['station'] = \Param::station(2);
		$tmp['office'] = \Param::office();
		//$tmp['gdtype'] = db('gdtypes')->field('type id, name')->order('id')->select();
		$this->assign('tmp', $tmp);
		
		$this->assign('yps', \Param::yps());
		$this->assign('fs6', \Param::fs6($gtype));
		$this->assign('title', '工单');
		$this->assign('start', $start);
		$this->assign('end', $end);
		$this->assign('otype', $otype);
		$this->assign('data', $data);
		$this->assign('page', $show);// 赋值分页输出
		session('orderindex', $_SERVER['REQUEST_URI']);

		return $this->fetch();
	}

    public function download(){
        $this->opdownload();
    }


    function opdownload(){
        $map = [];
        $type  =  I('type');
        $fyid  =  (string)I('fyid');
        $gtype = (int)$this->user['gtype'];
        $map['gdtype'] = $gtype;
        if(!$type && !$fyid){
            $otype = 0;
            $map['type'] = 0;
        }else{
            $otype = 1;
            if($gtype){
                $map['type'] = 1;
            }else{
                $ztype  =  I('ztype');
                if($ztype != ''){
                    $ztype = max(0, min(1, (int)$ztype));
                    $map['type'] = $ztype;
                }
            }
            if($fyid) $map['fyid'] = $fyid;
        }
        $status  =  I('status');
        if($status != ''){
            $status = max(0, min(2, (int)$status));
            $map['status'] = $status;
        }
        if($otype){
            $map['cs'] = ['egt', 1];
        }

        $gdtype  =  I('gdtype');
        if($gdtype != ''){
            $gdtype = max(0, min(2, (int)$gdtype));
            $map['gdtype'] = $gdtype;
        }
        $office  =  I('office');
        if($office != ''){
            $office = max(0, min(22, (int)$office));
            $map['office'] = $office;
        }
        $place  =  I('place');
        if($place != ''){
            $place = max(1, min(7, (int)$place));
            $map['place'] = $place;
        }
        $station  =  I('station');
        if($station != ''){
            $station = max(1, min(36, (int)$station));
            $map['station'] = $station;
        }

        if($this->user['type'] == 2){
            $map['place'] = $this->user['office'];
        }
        if($this->user['type'] == 3){
            $map['station'] = $this->user['office'];
        }

        $start = I('start');
        $end = I('end');
        if(!$start && !$end && !$fyid){
            $start = date('Y-m', strtotime('last day of -1 months')) . '-26';
            $day = date('d');
            if($day > 25) $day = 25;
            $end = date('Y-m') . '-' . $day;
        }
        $start && $map['etime'] = ['egt', $start];
        $end && $map['etime'] = ['elt', $end];
        $start && $end && $map['etime'] = [['egt', $start], ['elt', $end. ' 23:59:59']];

        if($otype){
            //子单
            $data = db('orders')->where($map)->order($fyid ? 'type' : 'etime desc')->select();
//            $count = db('orders')->where($map)->count();// 查询满足要求的总记录数
        }else{
            //母单
            $subquery = "yid in(select distinct fyid from orders where cs>0)";
            $data = db('view_orders')->where($map)->where($subquery)->order('place_v')->select();
//            $count = db('orders')->where($map)->where($subquery)->count();// 查询满足要求的总记录数
        }

        $zs = db('ycodes')->where("otype = 0")->value('GROUP_CONCAT(id)');

        $places = \Param::place();
        $stations = \Param::station(2);
        $offices = \Param::office();

        if($otype){
            foreach($data as $k=>$v){
                $data[$k]['ks'] = db('order_adopt')->where(['oid'=>$v['id']])->value('group_concat(`cate`)');
                if(!$v['type']){
                    $data[$k]['addr'] = "【{$v['village']}】{$v['village_addr']}";
                    $data[$k]['rs'] = db()->query("select GROUP_CONCAT(r) rs from (SELECT concat(ycode, ':', count(ycode)) r FROM `photos` WHERE `oid` in(select id from orders where fyid = '{$v['yid']}') AND `ycode` IN ({$zs}) GROUP BY `ycode`) s")[0]['rs'];
                }else{
                    $data[$k]['rs'] = db()->query("select GROUP_CONCAT(r) rs from (SELECT concat(ycode, ':', count(ycode)) r FROM `photos` WHERE `oid` = {$v['id']}". ($v['gdtype'] ? '' : " AND `ycode` NOT IN ({$zs})") ." GROUP BY `ycode`) s")[0]['rs'];
                }
            }
            if($fyid){
                $m = db('orders')->where(['yid'=>$fyid])->find();
                if($m['gdtype'] == 0){
                    $m['addr'] = "【{$m['village']}】{$m['village_addr']}";
                    $m['ks'] = db('order_adopt')->where(['oid'=>$m['id']])->value('group_concat(`cate`)');
                    $m['rs'] = db()->query("select GROUP_CONCAT(r) rs from (SELECT concat(ycode, ':', count(ycode)) r FROM `photos` WHERE `oid` in(select id from orders where fyid = '{$m['yid']}') AND `ycode` IN ({$zs}) GROUP BY `ycode`) s")[0]['rs'];
                    $data = array_merge([$m], $data);//print_r($data);
                }
            }
        }else{
            foreach($data as $k=>$v){
                //水箱容积
                //水箱数量
                //水池容积
                //水池数量
                $sx = db()->query("select count(id) as ct, sum(volume) as volume from orders where fyid='{$data[$k]['yid']}' and equipment_type='水箱' and cs>0")[0];
                $data[$k]['tank_volume'] = $sx['volume'];
                $data[$k]['tank_count'] = $sx['ct'];
                $sx = db()->query("select count(id) as ct, sum(volume) as volume from orders where fyid='{$data[$k]['yid']}' and equipment_type='水池' and cs>0")[0];
                $data[$k]['pool_volume'] = $sx['volume'];
                $data[$k]['pool_count'] = $sx['ct'];

            }
        }


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        vendor('Classes.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        if($otype){
            $filename = iconv('utf-8', 'gb2312', "重复工单({$fyid}).xls");

            //所站维保单位汇总统计
            //第一行数据
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit('A1', '工单号')
                ->setCellValueExplicit('B1', '接管编号')
                ->setCellValueExplicit('C1', '小区')
                ->setCellValueExplicit('D1', '地址')
                ->setCellValueExplicit('E1', '管理所')
                ->setCellValueExplicit('F1', '管理站')
                ->setCellValueExplicit('G1', '维保单位')
                ->setCellValueExplicit('H1', '设备类型')
                ->setCellValueExplicit('I1', '容积(m³)');

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18);

            $objPHPExcel->getActiveSheet()->setTitle('子单');
            $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray(['font' => ['bold' => true]]);

            $i = 2;
//        echo 'count='.count($data);exit;
//        echo json_encode($data, true) ;exit;
            foreach($data as $d) {
                $objPHPExcel->getActiveSheet()
                    ->setCellValueExplicit('A'.$i, $d['gid'])
                    ->setCellValueExplicit('B'.$i, $d['takeover_no'])
                    ->setCellValueExplicit('C'.$i, $d['village'])
                    ->setCellValueExplicit('D'.$i, $d['addr'])
                    ->setCellValueExplicit('E'.$i, $places[$d['place']])
                    ->setCellValueExplicit('F'.$i,  $d['station_name'])
                    ->setCellValueExplicit('G'.$i,  $d['office_name'])
                    ->setCellValueExplicit('H'.$i, $d['equipment_type'])
                    ->setCellValueExplicit('I'.$i, $d['volume'] == "" ? '0.00' : $d['volume']);

                $i++;
            }
        }else{

            $filename = iconv('utf-8', 'gb2312', "重复工单({$start}至{$end}).xls");

            //所站维保单位汇总统计
            //第一行数据
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit('A1', '接管编号')
                ->setCellValueExplicit('B1', '小区')
                ->setCellValueExplicit('C1', '地址')
                ->setCellValueExplicit('D1', '管理所')
                ->setCellValueExplicit('E1', '管理站')
                ->setCellValueExplicit('F1', '维保单位')
                ->setCellValueExplicit('G1', '水箱个数')
                ->setCellValueExplicit('H1', '水箱容积')
                ->setCellValueExplicit('I1', '水池个数')
                ->setCellValueExplicit('J1', '水池容积');

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(18);

            $objPHPExcel->getActiveSheet()->setTitle('母单');
            $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray(['font' => ['bold' => true]]);

            $i = 2;
//        echo 'count='.count($data);exit;
//        echo json_encode($data, true) ;exit;
            foreach($data as $d) {
                $objPHPExcel->getActiveSheet()
                    ->setCellValueExplicit('A'.$i, $d['takeover_no'])
                    ->setCellValueExplicit('B'.$i, $d['village'])
                    ->setCellValueExplicit('C'.$i, $d['village_addr'])
                    ->setCellValueExplicit('D'.$i, $places[$d['place']])
                    ->setCellValueExplicit('E'.$i,  $d['station_name'])
                    ->setCellValueExplicit('F'.$i,  $d['office_name'])
                    ->setCellValueExplicit('G'.$i, $d['tank_count'])
                    ->setCellValueExplicit('H'.$i, $d['tank_volume'] == "" ? '0.00' : $d['tank_volume'])
                    ->setCellValueExplicit('I'.$i, $d['pool_count'])
                    ->setCellValueExplicit('J'.$i, $d['pool_volume'] == "" ? '0.00' : $d['pool_volume']);

                $i++;
            }
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header("Content-Disposition:attachment;filename=\"{$filename}\"");
        header("Content-Transfer-Encoding:binary");

        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');
    }

}
