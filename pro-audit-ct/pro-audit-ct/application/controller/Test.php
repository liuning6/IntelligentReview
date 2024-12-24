<?php
namespace app\controller;
use think\Controller;
class Test extends Controller
{
	public function index()
	{
		return ok1;
    }
	
	public function t1(){
		$stations = \Param::station(2);
		print_r($stations);
	}
        public function photos(){
            $ps = db('photos')->alias('p')->field('p.id, p.oid, o.etime, p.filename, p.extension, p.matching')->join('orders o', 'o.id = p.oid')->where("p.status=2 and p.updatetime > '2022-10-10'")->select();
            $picdir = "/home/www/2water.com/source/pics";
            
	    foreach($ps as $p){
               $filename = $picdir .'/'.date('Ym',strtotime($p['etime'])).'/'.$p['oid'].'/'.$p['filename'].'.'.$p['extension'];
               if(!file_exists($filename)){
                   echo $p['oid'].',<br>';     
               }
            }

        }
	
	public function audit($id)
	{
		$audit = new \Audit;
		
		$rs = db('photos')->field('matching,code')->where(['oid'=>$id])->select();//print_r($rs);
		foreach($rs as $p){
			if($p['matching'] > config('matching') && $p['code'] > 1) $photos[] = $p['code']; else $photos[] = 1;
		}
		
		echo '<pre>';
		print_r($audit->audit(1, []));
		//print_r($audit->audit(1, []));
		
		
		//print_r($photos);
		$s = $audit->audit(1, $photos);
		//print_r($s);
		
	}
	
	public function p()
	{
		$data = '[
  {
    "ID": "17577a6f-82f4-4db6-ba1f-3053c7e4b395",
    "FCUSTOMSERVICEID": "fe6835db-44f9-48d1-9302-b397b12cb9c2",
    "CUSTOMSERVICEID": "2110151905003912",
    "GDTYPE": "泵房养护",
    "ACCEPTSTATION": "徐嘉",
    "STATION": "大场站",
    "REPORTAREA": "平利路21弄；41弄，平利路87弄，灵石路1082弄，志丹路501弄",
    "HAPPENADDR": "平利路21弄1-14、17-22、24-27号；41弄1-2、4-21号，平利路87弄2-17号，灵石路1082弄1-33号，志丹路501弄1-14、17-23、25-30、33-47号",
    "SENDSTATION": "平利路87弄1#",
    "KSTIME": "2021-10-14 21:53:47",
    "SOLVETIME_PLAN": "2021/10/17 0:00:00",
    "ACCEPTREMARK": "",
    "Media": [
      {
        "name": "泵房位置",
        "path": "http://10.9.6.30:8001/egFileRoot/2021/10/20/6d10da0135064a53899fe4b77803030e/6d10da0135064a53899fe4b77803030e.jpg"
      },
      {
        "name": "泵房用电",
        "path": "http://10.9.6.30:8001/egFileRoot/2021/10/20/c9a11512f2f441de9cff62e5aed128f0/c9a11512f2f441de9cff62e5aed128f0.jpg"
      },
      {
        "name": "水泵机组全景",
        "path": "http://10.9.6.30:8001/egFileRoot/2021/10/20/56737031dd334bb499fc1b92235b96fd/56737031dd334bb499fc1b92235b96fd.jpg"
      },
      {
        "name": "工作证",
        "path": "http://10.9.6.30:8001/egFileRoot/2021/10/20/c777a93e09474bd89e10a1e6a9367210/c777a93e09474bd89e10a1e6a9367210.jpg"
      },
      {
        "name": "泵房内环境",
        "path": "http://10.9.6.30:8001/egFileRoot/2021/10/20/45030972be8c466a9f73a7c1a28cbbd8/45030972be8c466a9f73a7c1a28cbbd8.jpg"
      },
      {
        "name": "电控柜外部",
        "path": "http://10.9.6.30:8001/egFileRoot/2021/10/20/cc0a557d9909457cb40436ac1218fa4a/cc0a557d9909457cb40436ac1218fa4a.jpg"
      },
      {
        "name": "电控柜内部",
        "path": "http://10.9.6.30:8001/egFileRoot/2021/10/20/0ea5b7bd47824ecbbe5396822188483e/0ea5b7bd47824ecbbe5396822188483e.jpg"
      },
      {
        "name": "泵房维保记录",
        "path": "http://10.9.6.30:8001/egFileRoot/2021/10/20/b00a7d7a261f463e8097a092176c1f92/b00a7d7a261f463e8097a092176c1f92.jpg"
      }
    ],
    "SFYH": "能",
    "GZZ": "2021-10-20 14:30:47",
    "SFGZ": "已改造",
    "BFWZ": "2021-10-20 14:32:13",
    "SCWB": "正常",
    "BFNHJ": "2021-10-20 14:32:23",
    "BFHJ": "正常",
    "SBWB": "正常",
    "BFYD": "2021-10-20 14:32:34",
    "SBJZQJ": "2021-10-20 14:32:39",
    "SBSFZC": "正常",
    "KZGWB": "2021-10-20 14:32:51",
    "KZGNB": "2021-10-20 14:32:59",
    "DKGSFZC": "正常",
    "FSSB": "正常",
    "BFXJJL": "2021-10-20 14:34:22",
    "QT": "",
    "BZ": ""
  }
]';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://180.169.66.162:22235/api/OrderReceive');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($ch, CURLOPT_HTTPHEADER,['Content-Type: application/json','Content-Length:' . strlen($data)]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
	
}
