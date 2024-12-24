<?php 
namespace app\controller;
use PHPExcel_Cell_DataType;
use PHPExcel_Style_NumberFormat;
use Think\Controller;

class Orders extends Common{

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
		$cs = I('cs');
		if($cs != ''){
			$cs = max(0, $cs);
			if(!$cs) $map['cs'] = 0; else $map['cs'] = ['egt', $cs];
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
		
		$addr  =  I('addr');
		if($addr != ''){
			$map[] = ['exp', db()->raw("village_addr like '%{$addr}%' or village like '%{$addr}%' or addr like '%{$addr}%' or gid like '%{$addr}%'")];
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
		
		$count = db('orders')->where($map)->count();// 查询满足要求的总记录数
		#echo db()->getLastsql();
		vendor('Page');
		$Page  = new \Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
        $show  = $Page->show();// 分页显示输出
		$data = db('orders')->where($map)->order($fyid ? 'type' : 'etime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$zs = db('ycodes')->where("otype = 0")->value('GROUP_CONCAT(id)');//print_r($zs);
		if($otype){
			foreach($data as $k=>$v){
				$data[$k]['ks'] = db('order_adopt')->where(['oid'=>$v['id']])->value('group_concat(`cate`)') . ',97';
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
                    // 图片分类
					$m['ks'] = db('order_adopt')->where(['oid'=>$m['id']])->value('group_concat(`cate`)') . ',97';
                    // 子类图片数量
					$m['rs'] = db()->query("select GROUP_CONCAT(r) rs from (SELECT concat(ycode, ':', count(ycode)) r FROM `photos` WHERE `oid` in(select id from orders where fyid = '{$m['yid']}') AND `ycode` IN ({$zs}) GROUP BY `ycode`) s")[0]['rs'];

					$data = array_merge([$m], $data);//print_r($data);
				}
			}
		}else{
			session('oindex', $_SERVER['REQUEST_URI']);
			foreach($data as $k=>$v){
                if($gtype == 0){
                    $data[$k]['s'] = db('orders')->cache(true, 86400)->field('count(0) `0`, count(case when status=1 then 1 end) `1`, count(case when cs>0 then 1 end) `2`, count(case when (select count(p.id) from photos p where p.oid=orders.id and ycode=39)>0 then 1 end) `3`')->where(['fyid'=>$v['yid']])->find();
                }else{
                    $data[$k]['s'] = db('orders')->cache(true, 86400)->field('count(0) `0`, 
                    count(case when status=1 then 1 end) `1`, 
                    count(case when cs>0 then 1 end) `2`, 
                    count(case when (select count(p.id) from photos p where p.oid=orders.id and ycode=39)>0 then 1 end) `3`,
                    count(case when (select count(p.id) from photos p where p.oid=orders.id and ycode=42)>0 then 1 end) `4`
                    ')->where(['fyid'=>$v['yid']])->find();
                }
                $suboid = db('orders')->where(['fyid'=>$v['yid']])->value('group_concat(`id`)');
                //$data[$k]['s']['5'] = db()->query("SELECT COUNT(id) as `5` FROM photos WHERE ycode=45 AND oid IN (".$suboid.") AND status=1;")[0]['5'];
                $data[$k]['s']['5'] = db()->query("SELECT COUNT(*) as `5` FROM ( SELECT DISTINCT usercode FROM photos WHERE oid IN (".$suboid.") AND ycode = 45 AND status=1) as uc;")[0]['5'];
                //$data[$k]['s']['6'] = db()->query("SELECT COUNT(id) as `6` FROM photos WHERE ycode=45 AND oid IN (".$suboid.");")[0]['6'];
                $data[$k]['s']['6'] = db()->query("SELECT COUNT(*) as `6` FROM photos WHERE oid IN (".$suboid.") AND ycode = 45 AND status=2;")[0]['6'];
			}
		}

        // 新显示分类: 服务代表/***

		$tmp['place'] = \Param::place();
		$tmp['station'] = \Param::station(2);
		$tmp['office'] = \Param::office();
		//$tmp['gdtype'] = db('gdtypes')->field('type id, name')->order('id')->select();
		$this->assign('tmp', $tmp);
		$fs6 = \Param::fs6($gtype);
		$fs6[] = [
		    'id' => 97,
                    'type' => 0,
                    'name' => '人员正脸照片',
                    'fs' => [
                        1 => [6011, 6012],
                    ],
                ];
		//dump($data);
        // 小类->大类的映射
        $this->assign('gtype', $gtype);
		$this->assign('yps', \Param::yps());
		// $this->assign('fs6', \Param::fs6($gtype));
		$this->assign('fs6', $fs6);
		$this->assign('title', '工单');
		$this->assign('start', $start);
		$this->assign('end', $end);
		$this->assign('otype', $otype);
		$this->assign('data', $data);
		$this->assign('page', $show);// 赋值分页输出
		session('orderindex', $_SERVER['REQUEST_URI']);
		return $this->fetch();
	}

	
	public function detail()
	{
		$id = (int)I('id') or die('缺少参数');
		$order = db('orders')->find($id) or $this->error('对不起，该信息不存在或已过期。');

		if($this->user['type'] == 2){
			if($order['place'] != $this->user['office']) $this->error('对不起，您无权查看该信息。');
		}
		if($this->user['type'] == 3){
			if($order['station'] != $this->user['office']) $this->error('对不起，您无权查看该信息。');
		}
		
		$gtype = (int)$this->user['gtype'];
		
		$ts = db('ycodes')->where("otype = 0")->value('GROUP_CONCAT(id)');
		$status  =  I('status');
		if($status != ''){
			$status = max(0, min(2, (int)$status));
			$map['status'] = $status;
		}
		
		if(!$order['gdtype']){
			if($order['type']){
				$map['oid'] = $id;
				$map['ycode'] = ['notin', $ts];
			}else{
				$map[] = ['exp', db()->raw('oid in(select id from orders where fyid="'. $order['yid'] .'")')];
				$map['ycode'] = ['in', $ts];
				$order['addr'] = "【{$order['village']}】{$order['village_addr']}";
			}
		}else{
			$map['oid'] = $id;
			$order['addr'] = "【{$order['village']}】{$order['village_addr']}";
		}

        // 图片类别ycodes.pcid
		$type  =  I('type');
		if($type != ''){
			$type = (int)$type;
			unset($map['ycode']);
			$map[] = ['exp', db()->raw($type ? "ycode in(select id from ycodes where pcid = {$type})" : "ycode not in(". \Param::ccodes($order['gdtype']) .",45)")];
		}
                $fs2 = \Param::fs2(0, $gtype);
                $fs2['97'] = '人员正脸照片';
		
		$data = db('photos')->field('photos.*, (select etime from orders where id = photos.oid) etime')->where($map)->order('id desc')->select();
		//echo db()->getLastsql();
                foreach ($data as $key => $val) {
                    if ($val['ycode'] == 45) {
                        if ($val['usercode']) {
                            $userInfo = db('register_user_face')->field('name,role')->where("code = {$val['usercode']}")->select();
                        } else {
                            $userInfo = [];
                        }
                        if ($val['status'] == 1) {

                            $data[$key]['username'] = isset($userInfo['0']['name']) ? $userInfo['0']['name'] : '';
                            $data[$key]['userrole'] = isset($userInfo['0']['role']) ? $this->userRole($userInfo['0']['role']) : '';
                        } else {
                            if (mb_strpos($val['cause'], '岗位') !== false) {
                                $data[$key]['username'] = isset($userInfo['0']['name']) ? $userInfo['0']['name'] : '';
                                $data[$key]['userrole'] = isset($userInfo['0']['role']) ? $this->userRole($userInfo['0']['role']) : '';
                            }                        
                        }
                    } else {
                        $data[$key]['username'] = '';
                    }
                }
		$this->assign('title', '工单详情');
		$this->assign('order', $order);
		$this->assign('data', $data);
		$this->assign('id', $id);
		$this->assign('ycodes', \Param::ycodes(0, ''));
		// $this->assign('fs2', \Param::fs2(0, $gtype));
		$this->assign('fs2', $fs2);
		$this->assign('fs3', \Param::fs3());
		return $this->fetch();
	}

    public function userRole($userRole)
    {
        if ($userRole) {
            $str  = json_decode($userRole, 1);
            $str2 = json_decode($str[0], 1);
            if ($str2) {
                $str2 = $str2;
            } else {
                $str2 = $str;
            }
            $temp = [];
            $stt = '';
            foreach ($str2 as $value) {
                $strpos = mb_strpos($value, '：');
                if ($strpos !== false) {
                    $kk = mb_substr($value, 0, $strpos);
                    $st = $strpos+1;
                    $vv = mb_substr($value, $st);

                    $temp[$kk][] = $vv;
                } else {
                    $stt = $value;
                }
            }

            foreach ($temp as $key => $val) {
                $zhi = implode(',', $val);
                $stt .= ';'.$key.':'.$zhi;
            }
            $str3 = trim($stt, ';');

            // $str3 = implode(';',$str2);
        } else {
            $str3 = '无角色';
        }
        return $str3;
    }

    function download(){
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
        $cs = I('cs');
        if($cs != ''){
            $cs = max(0, $cs);
            if(!$cs) $map['cs'] = 0; else $map['cs'] = ['egt', $cs];
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

        $addr  =  I('addr');
        if($addr != ''){
            $map[] = ['exp', db()->raw("village_addr like '%{$addr}%' or village like '%{$addr}%' or addr like '%{$addr}%' or gid like '%{$addr}%'")];
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

//        $count = db('orders')->where($map)->count();// 查询满足要求的总记录数
        $data = db('orders')->where($map)->order($fyid ? 'type' : 'etime desc')->select();

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
            foreach($data as $k=>$v){
                if($gtype == 0){
                    $data[$k]['s'] = db('orders')->cache(true, 86400)->field('count(0) `0`, count(case when status=1 then 1 end) `1`, count(case when cs>0 then 1 end) `2`, count(case when (select count(p.id) from photos p where p.oid=orders.id and ycode=39)>0 then 1 end) `3`')->where(['fyid'=>$v['yid']])->find();
                }else{
                    $data[$k]['s'] = db('orders')->cache(true, 86400)->field('count(0) `0`, 
                    count(case when status=1 then 1 end) `1`, 
                    count(case when cs>0 then 1 end) `2`, 
                    count(case when (select count(p.id) from photos p where p.oid=orders.id and ycode=39)>0 then 1 end) `3`,
                    count(case when (select count(p.id) from photos p where p.oid=orders.id and ycode=42)>0 then 1 end) `4`
                    ')->where(['fyid'=>$v['yid']])->find();
                }
                $suboid = db('orders')->where(['fyid'=>$v['yid']])->value('group_concat(`id`)');
                $data[$k]['s']['5'] = db()->query("SELECT COUNT(*) as `5` FROM ( SELECT DISTINCT usercode FROM photos WHERE oid IN (".$suboid.") AND ycode = 45 AND status=1) as uc;")[0]['5'];
                $data[$k]['s']['6'] = db()->query("SELECT COUNT(*) as `6` FROM photos WHERE oid IN (".$suboid.") AND ycode = 45 AND status=2;")[0]['6'];
            }
        }

        // 新显示分类: 服务代表/***
        $title = ($place? $place: ''). ($station? $station: ''). ($office? $office: ''). ($addr? $addr: ''). ('-'.$start? $start: ''). ('-'.$end? $end: '');

        $filename = iconv('utf-8', 'gb2312', "工单统计-{$title}.xls");

        vendor('Classes.PHPExcel');
        $objPHPExcel = new \PHPExcel();

        //所站维保单位汇总统计
        //第一行数据
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit('A1', '工单编号')
            ->setCellValueExplicit('B1', '日期')
            ->setCellValueExplicit('C1', '地址')
            ->setCellValueExplicit('D1', '管理所')
            ->setCellValueExplicit('E1', '管理站')
            ->setCellValueExplicit('F1', '维保单位')
            ->setCellValueExplicit('G1', '工单数')
            ->setCellValueExplicit('H1', '合格单')
            ->setCellValueExplicit('I1', '重复单')
            ->setCellValueExplicit('J1', '服务代表')
            ->setCellValueExplicit('K1', '人员正脸照通过人数')
            ->setCellValueExplicit('L1', '人员正脸照不通过照片数');

        if($gtype == 2){
            $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('M1', '固定采样箱');
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(100);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);

        $objPHPExcel->getActiveSheet()->setTitle('工单列表');
        $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray(['font' => ['bold' => true]]);

        $places = \Param::place();
        $stations = \Param::station(2);
        $offices = \Param::office();

        $i = 2;
        foreach($data as $d) {
            $objPHPExcel->getActiveSheet()
                ->setCellValueExplicit('A'.$i, $d['gid'])
                ->setCellValueExplicit('B'.$i, substr($d['etime'], 0, 10))
                ->setCellValueExplicit('C'.$i, mb_cut('【'. $d['village'] .'】' . $d['village_addr'], 50))
                ->setCellValueExplicit('D'.$i, $places[$d['place']])
                ->setCellValueExplicit('E'.$i, $stations[$d['station']][1])
                ->setCellValueExplicit('F'.$i, $offices[$d['office']])
                ->setCellValueExplicit('G'.$i, $d['s'][0], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit('H'.$i, $d['s'][1], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit('I'.$i, $d['s'][2], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit('J'.$i, $d['s'][3], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit('K'.$i, $d['s'][5], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit('L'.$i, $d['s'][6], PHPExcel_Cell_DataType::TYPE_NUMERIC);

            if($gtype == 2){
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('M'.$i, $d['s'][4], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            }
            $i++;
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
