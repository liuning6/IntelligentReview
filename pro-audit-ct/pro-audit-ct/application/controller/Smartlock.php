<?php 
namespace app\controller;
use Think\Controller;
class Smartlock extends Common{

	//订单列表
	public function index(){
		echo "<script>console.log('执行了statistics的php脚本里的index方法');</script>";
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

        //$map['station'] = 9;
	
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
        $addr  =  I('addr');
	
	// 关闭现有连接
//db()->disconnect();

// 重新连接
//db()->connect();
	
	// 假设你的模型是 Orders
	$fields11 = db()->query('DESCRIBE orders');
         // 将数组转换为 JSON 格式
	$resultstmt111 = file_put_contents('/var/www/pro-audit-ct/datacount.txt', json_encode($fields11));
	///
	
        if($addr != ''){
            $map[] = ['exp', db()->raw("village_addr like '%{$addr}%' or village like '%{$addr}%' or addr like '%{$addr}%' or gid like '%{$addr}%' or smart_lock_num like '%{$addr}%'")];
        }
        vendor('Page');
	unset($map['station']);
        if($otype){
            //子单
            $resultstmt111 = file_put_contents('/var/www/pro-audit-ct/datacount.txt', "123");
            $count = db('orders')->where($map)->count();// 查询满足要求的总记录数
            $Page  = new \Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
            $data = db('orders')->where($map)->order($fyid ? 'type' : 'etime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        }else{
            //母单
            $subquery = "yid in(select distinct fyid from orders where cs>0)";
            $subquery = "";
            $nonfc=array();
	    //$nonfc['nfc'] = array('exp','not null');    

            $count = db('orders')->where($map)->where($subquery)->count();// 查询满足要求的总记录数
            $Page  = new \Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
//          $data = db('orders')->where($map)->where($subquery)->order($fyid ? 'type' : 'etime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
   //       $data = db('orders')->where($map)->where($subquery)->order('etime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            $data1 = db('orders')->where($map)->where($subquery)->order('etime desc')->select();
            

	

	foreach($data1 as $k=>$v){
                //$data1[$k]['s'] = db('orders')->cache(true, 86400)->field('count(0) `0`,
                //$data1[$k]['s'] = db('orders')->field('count(0) `0`, 
                //count(case when (select count(p.id) from photos p where p.oid=orders.id and tags like \'%<SMARTLOCK>%\')>0 then 1 end) `1`,
                //count(case when nfc<>\'\' then 1 end)  `2`
                //')->where(['fyid'=>$v['yid']])->find();
		$data1[$k]['s'] = db('orders')->field('count(0) `0`, 
                count(case when (select count(p.id) from photos p where p.oid=orders.id and tags like \'%<SMARTLOCK>%\')>0 then 1 end) `1`,
                count(case when nfc<>\'\' then 1 end)  `2`,
		count(case when ((select count(p.id) from photos p where p.oid=orders.id and tags like \'%<SMARTLOCK>%\')>0 or nfc<>\'\') then 1 end) `3`
                ')->where(['fyid'=>$v['yid']])->find();
        //print_r($data1[$k]['s'][1]);
        //echo "，";
	
	        if($data1[$k]['s'][2] == 0) {
                    	       unset($data1[$k]);
		    $count=$count-1;
                }
		$contentToWrite = is_array($data1[$k]['s']) ? json_encode($data1[$k]['s']) : $data1[$k]['s'];
                          // 写入到文件
	            $result = file_put_contents('/home/www/2water.com/count.txt', $contentToWrite);
        	$show  = $Page->show();// 分页显示输出

	    }
            // 写入到文件
            $orders1=$data1;
            $contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
            // 写入到文件
            $result = file_put_contents('/home/www/2water.com/data1.txt', $contentToWrite);
	    $data = array_slice($data1, $Page->firstRow, 25);

	    $Page  = new \Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数

	}

    

	$oids1 = "421248,421247,421246,421245,421244,421243,420682,420681,420680,420679,420678,420677,420676,420633,420632,420631,420630,420627,420626,420625,420425,420424,420423,420422,420421,420304,420303,420302,420301,420300,420299,420298,420297,420296,420295,420294,419634,419633,419632,419631,419630,419629,419628,419621,419620,419619,419618,419617,419616,419615,419614,419613,419612,419611,419610,419609,419608,419607,419606,419605,419135,419134,419133,419132,419131,419130,419129,419128,419127,419123,419122,419121,419120,419119,419118,419117,419116,419115,418380,418379,418378,418377,418376,418375,418374,418373,418372,418367,418366,418365,418364,418363,418362,418361,418360,418359,418358,418357,418356,418355";
//$map['type'] = 1;
//$orders1 = db('orders')->where($map)->select();
//foreach($orders1 as $k=>$v){
		//if($orders1[$k]['id'] < 444186) {
//		if($orders1[$k]['id'] < 531054) {
//                    unset($orders1[$k]);
//                }
//	    }
	//$orders1 = db('orders')->where('id=530915')->select();

        //check_smartlock($orders1);
	// 写入到文件
	$orders2=$map;
	            $contentToWrite = is_array($orders2) ? json_encode($orders2) : $orders2;
                          // 写入到文件
	            $result = file_put_contents('/home/www/2water.com/1.txt', $contentToWrite);
        $show  = $Page->show();// 分页显示输出

		$zs = db('ycodes')->where("otype = 0")->value('GROUP_CONCAT(id)');//print_r($zs);
		if($otype){//子单
			foreach($data as $k=>$v){

                $data[$k]['s'] = db('orders')->cache(true, 86400)->field(' 
                count(case when  (select count(p.id) from photos p where p.oid=orders.id and tags like \'%<SMARTLOCK>%\')>0 then 1 end) `1`,
                count(case when nfc<>\'\' then 1 end)  `2`
                ')->where(['yid'=>$v['yid']])->find();
		//count(case when nfc<>\'\' and  (select count(p.id) from photos p where p.oid=orders.id and tags like \'%<SMARTLOCK>%\')>0 then 1 end) `1`,
                
			}
		}else{//母单
			session('oindex', $_SERVER['REQUEST_URI']);
            foreach($data as $k=>$v){
                //$data[$k]['s'] = db('orders')->cache(true, 86400)->field('count(0) `0`,
                $data[$k]['s'] = db('orders')->field('count(0) `0`, 
		count(case when  (select count(p.id) from photos p where p.oid=orders.id and tags like \'%<SMARTLOCK>%\')>0 then 1 end) `1`,
                count(case when nfc<>\'\' then 1 end)  `2`
                ')->where(['fyid'=>$v['yid']])->find();
		//count(case when nfc<>\'\' and (select count(p.id) from photos p where p.oid=orders.id and tags like \'%<SMARTLOCK>%\')>0 then 1 end) `1`,
		//count(case when nfc<>\'\' and (select count(p.id) from photos p where p.oid=orders.id and tags like \'%<SMARTLOCK>%\')>0 then 1 end) `1`,
        
		if($data[$k]['s'][2] == 0) {
                    unset($data[$k]);
                }
	     //tags前+p.tags   
            }
		}


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

		return $this->fetch();
	}
    
    public function download(){
        $this->opdownload();
    }


    function opdownload(){

        foreach ($data as $k => $v) {
            // 检查 's' 键是否存在，并且 's' 是一个数组，且数组索引 1 存在
            if (isset($v['s'][1])) {
                print_r($v['s'][1]);
                echo "data[$k]['s'][1]: " . $v['s'][1] . "\n";
            } else {
                echo "data[$k]['s'][1] 不存在\n";
            }
        }

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

        //$map['station'] = 9;

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
        $addr  =  I('addr');
        if($addr != ''){
            $map[] = ['exp', db()->raw("village_addr like '%{$addr}%' or village like '%{$addr}%' or addr like '%{$addr}%' or gid like '%{$addr}%'")];
        }
        vendor('Page');

        if($otype){
            //子单
            $data = db('orders')->where($map)->order($fyid ? 'type' : 'etime desc')->select();
        }else{
            //母单
            $subquery = "";
            $data = db('orders')->where($map)->where($subquery)->order('etime desc')->select();
            // 写入到文件
            $orders1=$map;
            $contentToWrite = is_array($orders1) ? json_encode($orders1) : $orders1;
            // 写入到文件
            $result = file_put_contents('/home/www/2water.com/down1.txt', $contentToWrite);
            $data1 = db('orders')->where($map)->where($subquery)->order('etime desc')->select();



            foreach($data1 as $k=>$v){
                //$data1[$k]['s'] = db('orders')->cache(true, 86400)->field('count(0) `0`,
                $data1[$k]['s'] = db('orders')->field('count(0) `0`, 
                count(case when (select count(p.id) from photos p where p.oid=orders.id and tags like \'%<SMARTLOCK>%\')>0 then 1 end) `1`,
                count(case when nfc<>\'\' then 1 end)  `2`
                ')->where(['fyid'=>$v['yid']])->find();
                if($data1[$k]['s'][2] == 0) {
                    unset($data1[$k]);
                }
            }
            $data=$data1;

        }

        $zs = db('ycodes')->where("otype = 0")->value('GROUP_CONCAT(id)');//print_r($zs);
        if($otype){
            foreach($data as $k=>$v){

                $data[$k]['s'] = db('orders')->cache(true, 86400)->field(' 
                count(case when (select count(p.id) from photos p where p.oid=orders.id and tags like \'%<SMARTLOCK>%\')>0 then 1 end) `1`,
                count(case when nfc<>\'\' then 1 end)  `2`
                ')->where(['yid'=>$v['yid']])->find();
            }
        }else{
            session('oindex', $_SERVER['REQUEST_URI']);
            foreach($data as $k=>$v){
                //$data[$k]['s'] = db('orders')->cache(true, 86400)->field('count(0) `0`,
                //$data[$k]['s'] = db('orders')->field('count(0) `0`,
                //count(case when nfc<>\'\' and (select count(p.id) from photos p where p.oid=orders.id and tags like \'%<SMARTLOCK>%\')>0 then 1 end) `1`,
                //count(case when nfc<>\'\' then 1 end)  `2`
                //')->where(['fyid'=>$v['yid']])->find();
                $data[$k]['s'] = db('orders')->field('count(0) `0`, 
                count(case when  (select count(p.id) from photos p where p.oid=orders.id and tags like \'%<SMARTLOCK>%\')>0 then 1 end) `1`,
                        count(case when nfc<>\'\' then 1 end)  `2`
                        ')->where(['fyid'=>$v['yid']])->find();


                //count(case when nfc<>\'\' and (select count(p.id) from photos p where p.oid=orders.id and tags like \'%<SMARTLOCK>%\')>0 then 1 end) `1`,

                if($data[$k]['s'][2] == 0) {
                    unset($data[$k]);
                }
                //tags前+p.tags
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $places = \Param::place();
        
        

        vendor('Classes.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        if($otype){
            $filename = iconv('utf-8', 'gb2312', "智能锁({$fyid}).xls");

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit('A1', '工单编号')
                ->setCellValueExplicit('B1', '日期')
                ->setCellValueExplicit('C1', '地址')
                ->setCellValueExplicit('D1', '管理所')
                ->setCellValueExplicit('E1', '管理站')
                ->setCellValueExplicit('F1', '维保单位')
                ->setCellValueExplicit('G1', '智能锁')
                ->setCellValueExplicit('H1', 'NFC')
                ->setCellValueExplicit('I1', '结果');

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

            $objPHPExcel->getActiveSheet()->setTitle('子单');
            $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray(['font' => ['bold' => true]]);

            $i = 2;

            foreach($data as $d) {
                $objPHPExcel->getActiveSheet()
                    ->setCellValueExplicit('A'.$i, $d['gid'])
                    ->setCellValueExplicit('B'.$i, substr($d['etime'], 0, 10))
                    ->setCellValueExplicit('C'.$i, $d['village'].$d['addr'])
                    ->setCellValueExplicit('D'.$i, $places[$d['place']])
                    ->setCellValueExplicit('E'.$i,  $d['station_name'])
                    ->setCellValueExplicit('F'.$i,  $d['office_name'])
                    ->setCellValueExplicit('G'.$i, $d['s'][1] > 0 ? '有' : '无')
                    ->setCellValueExplicit('H'.$i, $d['s'][2] > 0 ? '有' : '无')
                    ->setCellValueExplicit('I'.$i, $d['s'][1] == $d['s'][2]? '符合' : '不符合');

                $i++;
            }
        }else{

            
            $filename = iconv('utf-8', 'gb2312', "智能锁({$start}至{$end}).xls");

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit('A1', '工单编号')
                ->setCellValueExplicit('B1', '日期')
                ->setCellValueExplicit('C1', '地址')
                ->setCellValueExplicit('D1', '管理所')
                ->setCellValueExplicit('E1', '管理站')
                ->setCellValueExplicit('F1', '维保单位')
                ->setCellValueExplicit('G1', '智能锁数')
                ->setCellValueExplicit('H1', 'NFC登记数')
                ->setCellValueExplicit('I1', '符合');

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

            $objPHPExcel->getActiveSheet()->setTitle('母单');
            $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray(['font' => ['bold' => true]]);

            $i = 2;
//        echo 'count='.count($data);exit;
//        echo json_encode($data, true) ;exit;
            foreach($data as $d) {
                
                $objPHPExcel->getActiveSheet()
                    ->setCellValueExplicit('A'.$i, $d['gid'])
                    ->setCellValueExplicit('B'.$i, substr($d['etime'], 0, 10))
                    ->setCellValueExplicit('C'.$i, $d['village'].$d['village_addr'])
                    ->setCellValueExplicit('D'.$i, $places[$d['place']])
                    ->setCellValueExplicit('E'.$i,  $d['station_name'])
                    ->setCellValueExplicit('F'.$i,  $d['office_name'])
                    ->setCellValueExplicit('G'.$i, $d['s'][1])
                    ->setCellValueExplicit('H'.$i, $d['s'][2])
                    ->setCellValueExplicit('I'.$i, $d['s'][1] == $d['s'][2] ? '符合' : '不符合');

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

        if(!$order['gdtype']){
            $map['oid'] = $id;
            $map['ycode'] = 19;
        }

        // 图片类别ycodes.pcid
        $type  =  I('type');
        if($type != ''){
            $type = (int)$type;
            unset($map['ycode']);
            $map[] = ['exp', db()->raw($type ? "ycode in(select id from ycodes where pcid = {$type})" : "ycode not in(". \Param::ccodes($order['gdtype']) .")")];
        }

        $data = db('photos')->field('photos.*, (select etime from orders where id = photos.oid) etime')->where($map)->order('id desc')->select();
        //echo db()->getLastsql();
        $this->assign('title', '工单详情');
        $this->assign('order', $order);
        $this->assign('data', $data);
        $this->assign('id', $id);
        $this->assign('ycodes', \Param::ycodes(0, ''));
        $this->assign('fs2', \Param::fs2(0, $gtype));
        $this->assign('fs3', \Param::fs3());
        return $this->fetch();
    }

    public function fix(){
        $pid = I('pid');
        $res = db('photos')->where('id='.$pid)->update(['tags'=>'<SMARTLOCK>']);
        return $res;
    }
}
