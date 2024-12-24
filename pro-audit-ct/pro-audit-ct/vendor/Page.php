<?php

class Page{
    public $firstRow; // 起始行数
    public $listRows; // 列表每页显示行数
    public $parameter; // 分页跳转时要带的参数
    public $totalRows; // 总行数
    public $totalPages; // 分页总页面数
    public $rollPage   = 10;// 分页栏每页显示的页数
    public $lastSuffix = true; // 最后一页是否显示总页数

    private $p       = 'p'; //分页参数名
    private $url     = ''; //当前链接URL
    private $nowPage = 1;

    // 分页显示定制
    private $config  = array(
        'header' => '<div class="page-msg"><span class="rows"> 共 %TOTAL_ROW% 条记录 %TOTAL_PAGE% 页(每页 %LIST_ROW% 条)</span></div>',
        'first'   => '首页',
        'prev'   => '上一页',
        'next'   => '下一页',
        'last'   => '末页',
        'theme'  => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%',
    );

    /**
     * 架构函数
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows, $listRows=20, $parameter = array()) {
        config('VAR_PAGE') && $this->p = config('VAR_PAGE'); //设置分页参数名称
        /* 基础设置 */
        $this->totalRows  = $totalRows; //设置总记录数
        $this->listRows   = $listRows;  //设置每页显示行数
        $this->parameter  = empty($parameter) ? request()->param() : $parameter;
        $this->nowPage    = empty(request()->param()[$this->p]) ? 1 : intval(request()->param()[$this->p]);
        $this->nowPage    = $this->nowPage>0 ? $this->nowPage : 1;
        $this->firstRow   = $this->listRows * ($this->nowPage - 1);
    }

    /**
     * 定制分页链接设置
     * @param string $name  设置名称
     * @param string $value 设置值
     */
    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * 生成链接URL
     * @param  integer $page 页码
     * @return string
     */
    private function url($page){
        return str_replace(urlencode('[PAGE]'), $page, $this->url);
    }

    /**
     * 组装分页链接
     * @return string
     */
    public function show() {
        if(0 == $this->totalRows) return '';

        /* 生成URL */
        $this->parameter[$this->p] = '[PAGE]';
        $this->url = U(request()->controller() . '/'. request()->action(), $this->parameter);
		$parameter = $this->parameter;
		unset($parameter[$this->p]);
		$this->url0 = U(request()->controller() . '/'. request()->action(), $parameter);
        /* 计算分页信息 */
        $this->totalPages = ceil($this->totalRows / $this->listRows); //总页数
        if(!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }

        /* 计算分页零时变量 */
        $now_cool_page      = $this->rollPage/2;
        $now_cool_page_ceil = ceil($now_cool_page);

        //上一页
        $up_row  = $this->nowPage - 1;
        $up_page = $up_row > 0 ? '<a class="prev" href="' . $this->url($up_row) . '"><li class="page-li">' . $this->config['prev'] . '</li></a>' : '<a class="prev" href="javascript:;"><li class="page-li">' . $this->config['prev'] . '</li></a>';

        //下一页
        $down_row  = $this->nowPage + 1;
        $down_page = ($down_row <= $this->totalPages) ? '<a class="next" href="' . $this->url($down_row) . '"><li class="page-li">' . $this->config['next'] . '</li></a>' : '<a class="next" href="javascript:;"><li class="page-li">' . $this->config['next'] . '</li></a>';

        //第一页
        $the_first = '<a class="first" href="' . $this->url(1) . '"><li class="page-li">' . $this->config['first'] . '</li></a>';

        //最后一页
        $the_end = '<li class="page-li" style="padding:0;line-height:2;"><input type="number" value="'. ($this->nowPage == $this->totalPages ? 1 : ($this->nowPage + 1)) .'" style="width:100%;border:0;text-align:right;" id="inputpage" url="'. $this->url0 .'" /></li><a id="topage" class="num" href="javascript:;" onclick="window.location.href=$(\'#inputpage\').attr(\'url\')+\'?'. $this->p .'=\'+$(\'#inputpage\').val()"><li class="page-li page-num">转到</li></a><a class="end" href="' . $this->url($this->totalPages) . '"><li class="page-li">' . $this->config['last'] . '</li></a></ul>';

        //数字连接
        $link_page = "";
        for($i = 1; $i <= $this->rollPage; $i++){
            if(($this->nowPage - $now_cool_page) <= 0 ){
                $page = $i;
            }elseif(($this->nowPage + $now_cool_page - 1) >= $this->totalPages){
                $page = $this->totalPages - $this->rollPage + $i;
            }else{
                $page = $this->nowPage - $now_cool_page_ceil + $i;
            }
            if ($page>0) {
                if($page != $this->nowPage){
                    if($page <= $this->totalPages){
                        $link_page .= '<a class="num" href="' . $this->url($page) . '"><li class="page-li page-num">' . $page . '</li></a>';
                    }else{
                        break;
                    }
                }else{
                    $link_page .= '<span class="current"><li class="page-li page-num current">' . $page . '</li></span>';
                }                
            }

        }

        //替换分页内容
        $page_str = str_replace(
            array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%DOWN_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%TOTAL_ROW%', '%TOTAL_PAGE%', '%LIST_ROW%'),
            array($this->config['header'], $this->nowPage, $up_page, $down_page, $the_first, $link_page, $the_end, $this->totalRows, $this->totalPages, $this->listRows),
            $this->config['theme']);
        return '<div class="page"><ul class="page-ul">'.$page_str.'</div>';
    }
}