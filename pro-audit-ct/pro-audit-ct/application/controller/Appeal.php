<?php 
namespace app\controller;
use Think\Controller;
use think\Cache;
class Appeal extends Common{
	//订单列表
	public function index(){
		$ut = $this->user['type'];
		$uo = $this->user['office'];
		$station2 = \Param::station(2);
		$map = [];
		$gdtype = (int)session('user.gtype');
		$map['orders.gdtype'] = $gdtype;
		$pcount = 25;
		$join = 1;
		
		// jry20240527
		$appeal_field = '';
		if (I('appealType') == 1) {
			$appeal_field = 'faceappeal';
		} else {
			$appeal_field = 'appeal';
		}
		
		$appeal  =  I('appeal');
		$place  =  I('place');
		if($place != ''){
			$place = max(1, min(7, (int)$place));
			$map['orders.place'] = $place;
		}
		$station  =  I('station');
		if($station != ''){
			$station = max(1, min(36, (int)$station));
			$map['orders.station'] = $station;
		}
		$office  =  I('office');
		if($office != ''){
			$office = max(0, min(19, (int)$office));
			$map['orders.office'] = $office;
		}
		
		if($appeal != '2'){
			$cs = I('cs');
			if($cs == ''){
				$map[] = ['exp', db()->raw("orders.status = 2 or orders.cs > 0 or orders." . $appeal_field . " = 5")];
			}elseif($cs == 1){
				$map['orders.status'] = 2;
			}elseif($cs == 2){
				$map['orders.cs'] = ['gt', 0];
			}
		}
		
		$addr  =  I('addr');
		if($addr != ''){
			$map[] = ['exp', db()->raw("orders.addr like '%{$addr}%' or orders.gid like '%{$addr}%'")];
		}
		
		
		$start = I('start');
		$end = I('end');
		if(!$start && !$end){
			$start = date('Y-m', strtotime('last day of -1 months')) . '-26';
			$day = date('d');
			if($day > 25) $day = 25;
			$end = date('Y-m') . '-' . $day;
		}
		$start && $map['orders.etime'] = ['egt', $start];
		$end && $map['orders.etime'] = ['elt', $end];
		$start && $end && $map['orders.etime'] = [['egt', $start], ['elt', $end]];
		
		vendor('Page');
		if($ut == 0){//管理员
			$appeal_ = ['全部', '<div class="tab-state pass-wait">待审核</div>', '<div class="tab-state">通过</div>', '<div class="tab-state pass-no">未通过</div>'];
			$appeals = ['全部', '<div class="tab-state pass-wait">待初审</div>', '<div class="tab-state pass-no">初审未通过</div>', '<div class="tab-state pass-wait">待终审</div>', '<div class="tab-state pass-no">终审未通过</div>', '<div class="tab-state">终审通过</div>'];
			$k = 3;
			if($appeal == '1') $map['orders.' . $appeal_field] = 3; elseif($appeal == '2') $map['orders.' . $appeal_field] = 5; elseif($appeal == '3') $map['orders.' . $appeal_field] = ['in', [2, 4]];
		}elseif($ut == 1){//部级
			$appeal_ = ['全部', '<div class="tab-state pass-wait">待审核</div>', '<div class="tab-state">通过</div>', '<div class="tab-state pass-no">未通过</div>'];
			$appeals = ['', '', '', '<div class="tab-state pass-wait">待审核</div>', '<div class="tab-state pass-no">未通过</div>', '<div class="tab-state">通过</div>'];
			$k = 3;
			$map['orders.' . $appeal_field] = 3;
			if($appeal == '0' or $appeal == 'all') $map['orders.' . $appeal_field] = ['in', [3, 4, 5]]; elseif($appeal == '2') $map['orders.' . $appeal_field] = 5; elseif($appeal == '3') $map['orders.' . $appeal_field] = 4;
		}elseif($ut == 2){//所级
			$appeal_ = ['全部', '<div class="tab-state pass-wait">待审核</div>', '<div class="tab-state">通过</div>', '<div class="tab-state pass-no">未通过</div>'];
			$appeals = ['', '<div class="tab-state pass-wait">待审核</div>', '<div class="tab-state pass-no">未通过</div>', '<div class="tab-state pass-wait">待终审</div>', '<div class="tab-state pass-no">终审未通过</div>', '<div class="tab-state">终审通过</div>'];
			$k = 1;
			$map['orders.place'] = $uo;
			$map['orders.' . $appeal_field] = 1;
			if($appeal == '0' or $appeal == 'all') unset($map['orders.' . $appeal_field]); elseif($appeal == '') $appeal = 1; elseif($appeal == '2') $map['orders.' . $appeal_field] = ['in', [3, 5]]; elseif($appeal == '3') $map['orders.' . $appeal_field] = ['in', [2, 4]];
		}elseif($ut == 3){//站级
			$appeal_ = ['可申诉', '<div class="tab-state pass-wait">待审核</div>', '<div class="tab-state">通过</div>', '<div class="tab-state pass-no">未通过</div>'];
			$appeals = ['未申诉', '<div class="tab-state pass-wait">待审核</div>', '<div class="tab-state pass-no">未通过</div>', '<div class="tab-state pass-wait">待审核</div>', '<div class="tab-state pass-no">未通过</div>', '<div class="tab-state">通过</div>'];
			$k = 0;
			//$map['orders.place'] = $station2[$uo][2];
			$map['orders.station'] = $uo;
			if(!$appeal or $appeal == 'all'){
				$map['orders.' . $appeal_field] = 0;
				$join = 0;
			}else{
				if($appeal == '1') $map['orders.' . $appeal_field] = ['in', [1, 3]]; elseif($appeal == '2') $map['orders.' . $appeal_field] = 5; elseif($appeal == '3') $map['orders.' . $appeal_field] = ['in', [2, 4]];
			}
		}
		
		if($join){
			// jry20240527 根据申诉类型获取待申诉列表
			$map['appeal.appealType'] = I('appealType') ? I('appealType') : 2;

			// 查询列表
			$count = db('appeal')->field('appeal.*, orders.*, (select username from users where uid = appeal.station_uid) station_uname, (select username from users where uid = appeal.place_uid) place_uname, (select username from users where uid = appeal.branch_uid) branch_uname')->join('orders', 'appeal.oid = orders.id')->where($map)->count();
			$Page  = new \Page($count, $pcount);
			$show  = $Page->show();
			$data = db('appeal')->field('appeal.*, orders.*, (select username from users where uid = appeal.station_uid) station_uname, (select username from users where uid = appeal.place_uid) place_uname, (select username from users where uid = appeal.branch_uid) branch_uname')->join('orders', 'appeal.oid = orders.id')->where($map)->order('appeal.aid desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		}else{
			// jry20240527 根据申诉类型获取待申诉列表
			switch (I('appealType')) {
				case 1:  // 人脸申诉
					$map['orders.facestatus'] = 2;
					$map['orders.type'] = 0; // 人脸只申诉母单
					break;
				case 2:   // 其它申诉
				default:
					$map['orders.status'] = 2;
					break;
			}
			$count = db('orders')->where($map)->count();// 查询满足要求的总记录数
			$Page  = new \Page($count, $pcount);// 实例化分页类 传入总记录数和每页显示的记录数
			$show  = $Page->show();// 分页显示输出
			$data = db('orders')->where($map)->order('etime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		}
		
		//echo db()->getLastSql();
		
		$tmp['place'] = \Param::place();
		$tmp['station'] = $station2;
		$tmp['office'] = \Param::office();
		$tmp['appeal'] = $appeal_;
		//$tmp['gdtype'] = db('gdtypes')->field('type id, name')->order('id')->select();
		$this->assign('tmp', $tmp);
		
		$this->assign('title', '申诉');
		$this->assign('start', $start);
		$this->assign('end', $end);
		$this->assign('ut', $ut);
		$this->assign('uo', $uo);
		$this->assign('appeals', $appeals);
		$this->assign('appeal', (int)$appeal);
		$this->assign('k', $k);
		$this->assign('t1', $ut == 3 ? '申诉' : '审核');
		$this->assign('t2', $ut == 3 ? '申诉理由' : '审核意见');
		$this->assign('data', $data);
		file_put_contents('/var/www/pro-audit-ct/datainfo.txt', json_encode($data));
		$this->assign('page', $show);// 赋值分页输出
		session('orderindex', $_SERVER['REQUEST_URI']);
		return $this->fetch();
	}

	public function getPhotos(){
		$list = [];
		switch ($_POST['ut']) {
			case 0:
			case 1:
			case 5:
				// 就是先查母单下有哪些子单
				foreach ($_POST['ids'] as $key => $id) {
					$map = [];
					$map['oid'] = ['=', $id];
					$appeal_info = db('appeal')->where($map)->find();
					if ($appeal_info['picsJson']) {
						$appeal_info['picsJson'] = json_decode($appeal_info['picsJson'], true);
						if ($appeal_info['picsJson'] && count($appeal_info['picsJson']) > 0) {
							$ids = [];
							$usercodes = [];
							foreach ($appeal_info['picsJson'] as $key => $value) {
								$ids[] = "" . $value['picid'] . "";
								$usercodes[$value['picid']] = $value['usercode'];
							}
							// 然后根据子单的oid去photos查照片吗
							$map = [];
							$map['p.id'] = ['in', $ids];
							$map['p.ycode'] = '45'; // 人员
							$tmp = db('photos')
								->alias('p')
								->where($map)
								->select();
							foreach ($tmp as $key => &$value) {
								$value['status'] = 1;
								$value['usercode'] = $usercodes[$value['id']];
								$value['order_yid'] = $appeal_info['picsJson'][0]['order_yid'];
								$order = db('orders')->where(['id' => $value['oid']])->find();
								$value['data_dir'] = date('Ym', strtotime($order['etime']));
							}
							$list[] = [
								'yid' => $appeal_info['picsJson'][0]['order_yid'],
								'list' => $tmp
							];
						}
					}
				}
				break;
			case 3:
				// 就是先查母单下有哪些子单
				foreach ($_POST['yids'] as $key => $yid) {
					$map = [];
					$map['type'] = 1;
					$map['fyid'] = ['=', $yid];
					$orders = db('orders')->where($map)->select();
					$ids = [];
					$etimes = [];
					foreach ($orders as $key => $value) {
						$ids[] = "" . $value['id'] . "";
						$etimes[$value['id']] = $value['etime'];
					}
					// 然后根据子单的oid去photos查照片吗
					$map = [];
					$map['p.oid'] = ['in', $ids];
					$map['p.ycode'] = '45'; // 人员
					$tmp = db('photos')
						->alias('p')
						->where($map)
						->select();
					foreach ($tmp as $key => &$value) {
						$value['order_yid'] = $yid;
						$value['data_dir'] = date('Ym', strtotime($etimes[$value['oid']]));
					}
					$list[] = [
						'yid' => $yid,
						'list' => $tmp
					];
				}
				break;
			default:
				# code...
				break;
		}

		echo json_encode([
			'code' => 200,
			'ut' => $_POST['ut'],
			'data' => $list
		]);
	}
	
	public function submit(){
		if(!$this->user['audit']) return ['msg'=>'您没有权限啊', 'code'=>0];
		$ids = array_unique(array_filter(I('ids/a')));
		$count = count($ids);
		$s = (int)I('s');
		if(!$count) return ['msg'=>'未选择工单', 'code'=>0];
		$txt = I('txt');
		if(!$txt) return ['msg'=>'未输入内容', 'code'=>0];
		if(mb_strlen($txt, 'utf8') > 200) return ['msg'=>'内容字数超限', 'code'=>0];
		$sfile = '';

		// jry20240527检查usercode
		if (isset($_POST['appealType']) && $_POST['appealType'] == 1) {
			$pics = $_POST['pics'];
			// dump($pics);
			// $pic_group_by_oid = [];
			// Group photos by usercode instead of order ID to support multiple photos per person
			$pic_group_by_usercode = [];
			$pic_group_by_foid = [];
			foreach ($pics as $key => $value) {
				if ($value['ycode'] !== 45 && $value['usercode']) {
					$count = db('register_user_face')->where(['code' => $value['usercode']])->count();
					if ($count == 0) {
						return [0, $value['usercode'] . '人员编号不存在'];
					}
					$value['order_foid'] = db('orders')->where(['yid' => $value['order_yid']])->value('id');
					
					// Group by usercode for validation
					$pic_group_by_usercode[$value['usercode']][] = $value;
					// Maintain order-based structure for compatibility
					$pic_group_by_foid[$value['order_foid']][] = $value;
				}
			}
			if (count($pic_group_by_usercode) == 0) {
				return [0, '未填写人员编号'];
			}
			// Validate that each photo has a valid usercode, but allow multiple photos per person
			foreach ($pic_group_by_foid as $order_id => $photos) {
				if (count($photos) == 0) {
					return [0, '工单必须至少包含一张照片'];
				}
			}
		}

		// 文件上传
		if($file = $_FILES['file']){
			$ext = substr($file['name'], -3);
			if(!in_array(strtolower($ext), ['png', 'jpg', 'zip', 'rar'])) return [0, '上传的文件格式错误'];
			if($file['size'] > 50*1024*1024) return [0, '上传文件超过50M，请重新选择文件'];
			$date = date('Ymd');
			$dir = APP_PATH . '../source/appeal/' . $date;
			$nfile = md5($file['name'].$file['size']) . '.' . $ext;
			@mkdir($dir, 0777, 1);
			if(move_uploaded_file($file['tmp_name'], $dir . '/' . $nfile)) $sfile = $date . '/' . $nfile; else return [0, '文件上传失败，错误未知']; 
		}
		$ut = $this->user['type'];
		$uo = $this->user['office'];
		$uid = $this->user['uid'];
		$error = false;
		// jry20240527
		$appeal_field = '';
		if (isset($_POST['appealType']) && $_POST['appealType'] == 1) {
			$appeal_field = 'faceappeal';
		} else {
			$appeal_field = 'appeal';
		}
		if($ut == 1 or $ut == 0){//部级及管理员
			db('orders')->where(['id' => ['in', $ids], $appeal_field => 3])->count() == $count or $error = '您选择的部分工单状态异常，请刷新后重新提交';
			$filed = 'branch_';
			$appeal = 5;
		}elseif($ut == 2){//所级
			db('orders')->where(['id' => ['in', $ids], 'place' => $uo, $appeal_field => 1])->count() == $count or $error = '您选择的部分工单状态异常，请刷新后重新提交';
			$filed = 'place_';
			$appeal = 3;
		}elseif($ut == 3){//站级
			db('orders')->where(['id' => ['in', $ids], 'station' => $uo, $appeal_field => 0])->count() == $count or $error = '您选择的部分工单状态异常，请刷新后重新提交';
			$filed = 'station_';
			$appeal = 1;
		}
		
		if($error) return [0, $error];
		
		db()->startTrans();
		try{
			// $ut使用用户类型0/1部级及管理员2所级3站级
			$appeal = $s ? ($ut == 2 ? 2 : 4) : $appeal;
			if($ut == 3){
				// jry20240527人脸申诉类型需要保存人脸编号信息在picJson中
				if (isset($_POST['appealType']) && $_POST['appealType'] == 1) {
					foreach($ids as $id){
						$ds[] = [
							'appealType' => 1, // 人脸申诉
							'picsJson' => json_encode($pic_group_by_foid[$id]),
							'oid'=>$id,
							$filed.'uid'=>$uid,
							$filed.'msg'=>$txt,
							$filed.'file'=>$sfile,
							$filed.'time'=>db()->raw('now()')
						];
					}
				} else {
					foreach($ids as $id){
						$ds[] = ['oid'=>$id, $filed.'uid'=>$uid, $filed.'msg'=>$txt, $filed.'file'=>$sfile, $filed.'time'=>db()->raw('now()')];
					}
				}
				db('appeal')->insertAll($ds);
			}else{
				db('appeal')->where(['oid'=>['in', $ids]])->update([$filed.'uid'=>$uid, $filed.'msg'=>$txt, $filed.'file'=>$sfile, $filed.'time'=>db()->raw('now()')]);
			}
			// jry20240527
			if (isset($_POST['appealType']) && $_POST['appealType'] == 1) {
				db('orders')->where(['id'=>['in', $ids]])->setField('faceappeal', $appeal);
				// todo?需要处理子单状态
				if($appeal == 5){
					db('orders')->where(['id'=>['in', $ids]])->update(['facestatus' => 1]);
				}
				// 终审通过需要将picsJson中的usercode存储进photos表对应纪录
				// 支持同一个人的多张照片共享相同的usercode
				$alist = db('appeal')->where(['oid'=>['in', $ids]])->select();
				foreach($alist as $k1 => $appeal_info) {
					if ($appeal_info['picsJson']) {
						$appeal_info['picsJson'] = json_decode($appeal_info['picsJson'], true);
						if ($appeal_info['picsJson'] && count($appeal_info['picsJson']) > 0) {
							// Group photos by usercode for batch update
							$updates_by_usercode = [];
							foreach ($appeal_info['picsJson'] as $key1 => $value1) {
								$updates_by_usercode[$value1['usercode']][] = $value1['picid'];
							}
							// Update all photos for each usercode at once
							foreach ($updates_by_usercode as $usercode => $photo_ids) {
								db('photos')->where('id', 'in', $photo_ids)
									->update(['status' => 1, 'usercode' => $usercode]);
							}
						}
					}
				}
			} else {
				db('orders')->where(['id'=>['in', $ids]])->setField('appeal', $appeal);
				if($appeal == 5){
					db('orders')->where(['id'=>['in', $ids]])->update(['status' => 1, 'cs' => 0]);
				}
			}
			db()->commit();
			Cache::clear(); 
		} catch (\Exception $e) {
			db()->rollback();//print_r($e);
			return [0, $e];
		}
		return [1, '提交成功'];
	}
	
}
