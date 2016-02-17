<?php

namespace g\app;

/**
 * @author eoe2005@qq.com
 */
class GPage {
    protected $sum = 0;
    protected $pageSize = 20;
    protected $showPage = 10;
    protected $url = '';
    protected $curPage = 0;
    protected $sumPage = 0;
    public function __construct($sum, $url, $pageSize = 20) {
        if(isset($_GET['__action__'])){
            unset($_GET['__action__']);
        }
        if(isset($_GET['p'])){
            $this->curPage = intval($_GET['p']);
            unset($_GET['p']);
        }else{
            $this->curPage = 0;
        }
        $this->sum = $sum;
        $this->pageSize = $pageSize;
        $this->url = $url;
        $this->sumPage = ceil($this->sum/$this->pageSize);
    }

    /**
     * 获取当前是第几页
     * @return type
     */
    public function getCurPage() {
        return $this->curPage;
    }
    /**
     * 获取查询条件
     * @return type
     */
    public function getStart() {
        return $this->curPage * $this->pageSize;
    }
    protected function getPreText(){
        return sprintf("一共 %s 条记录，当前 %d / %d",$this->sum,$this->curPage + 1,$this->sumPage);
    }
    protected function buildA($page,$name){
        $_GET['p'] = $page;
        return sprintf('<a href="%s?%s">%s</a>',$this->url,  http_build_query($_GET),$name);
    }

    public function fetchData(){
        $ret = '';
        if($this->sumPage > 1){
            $ret .= $this->getPreText();
            if($this->curPage > 0){
                $ret .= $this->buildA(0,'首页');
                $ret .= $this->buildA($this->curPage - 1,'上一页');
            }
            $start = 0;
            $end = $this->sumPage;
            $half = ceil($this->showPage/2);
            if($this->sumPage > $this->showPage){
                if($this->curPage > $half){
                    if($this->curPage + $half > $this->sumPage){
                        $start = $this->sumPage - $this->showPage;
                    }else{
                        $start = $this->curPage - $half;
                        $end = $start + $this->showPage ;
                    }
                }else{
                    $end = $start + $this->showPage;
                }
            }
            for(;$start < $end ;$start++){
                if($start == $this->curPage){
                    $ret .= sprintf('<span>%d</span>',$start+1);
                }else{
                    $ret .= $this->buildA($start,$start+1);
                }
            }
            if($this->curPage+1 < $this->sumPage){
                $ret .= $this->buildA($this->curPage + 1,'下一页');
                $ret .= $this->buildA($this->sumPage - 1,'尾页');
            }
        }
        return $ret;
    }

}
