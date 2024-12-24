<?php

namespace like;

class PDATimeDetector
{
    function __construct(){
        $this->name = "PDATimeDetector";
    }

    function execute($orders){
        foreach ($orders as $o) {
            // 只处理子单
            if(!$o['type']){
                continue;
            }

            $ncs = 0;
            $photos = $o['photos'];
            foreach($photos as $p){
                if($p['ycode'] != 7 || $p['status'] !=1){
                    continue;
                }
                $pda = $o['pda'];
                $check = $this->checkPhoto($pda, $p);
                $ncs += $check;
            }

            // 更新订单（cs+1)
            //db('orders')->where(['id'=>$o['id']])->setInc('cs', $ncs);
        }
    }

    /**
     * @param $p
     * return 重复数
     */
    public function checkPhoto($pda, $p)
    {
        // 查找同一个pda,拍摄时间相近, 且其他工单的照片
        $realtime = $p['realtime'];
        $start = date('Y-m-d H:i:s', strtotime($realtime . ' - 3 minutes'));
        $end = date('Y-m-d H:i:s', strtotime($realtime . ' + 3 minutes'));

        $cond = " p.oid<>{$p['oid']} and p.status=1 and o.pda='{$pda}' and p.ycode=7 and p.realtime between '{$start}' and '{$end}'";
        echo $cond. "\n";
        $closedPhotos = db('photos')->alias('p')->field('p.id, p.oid')->join('orders o', 'o.id = p.oid')->where($cond)->select();

        echo "pid: ".$p['id']."  >> ".count($closedPhotos);

        $cs = 0;
        foreach ($closedPhotos as $cp) {
            $p1 = $p['id'];
            $p2 = $cp['id'];

            // 判断likes表是否已存在
            $like = db('likes')->where(['pid' => $p1, 'pid2' => $p2])->find();
            if ($like) {
//                echo "-- likes exists --\n";
                continue;
            }
            $like = db('likes')->where(['pid2' => $p1, 'pid' => $p2])->find();
            if ($like) {
//                echo "-- likes 2 exists --\n";
                continue;
            }

            exec("cd /home/www/xaiicp/ && ./test compare {$p1} {$p2} 16 false", $output);
            $matching = $output[count($output) - 1];
            $r = explode('=', $matching);
            $matching = floatval($r[1]);

            echo "{$p1} {$p2} >> {$matching} \n";

            $limit = 80;
            if ($matching > $limit) {
                $like = [
                    'pid' => $p1,
                    'pid2' => $p2,
                    'matching' => $matching,
                    'x' => 1,
                    'cause' => '拍摄时间相近',
                ];
                db('likes')->insert($like);
                //db('orders')->where(['id'=>$cp['oid']])->setInc('cs', 1);

                $cs++;
            }
        }

        return $cs;
    }

}