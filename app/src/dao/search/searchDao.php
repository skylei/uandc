<?php
/*
 * @author:crab
 * @date:
 * sphinx search dao
 */
class searchDao{

    public  $sphinx = '';
    const PAGE_SIZE = 10;
    const MAX_MATCHS = 1000;
    public $init_array = array();

    /**
     * 搜索数据源和索引
     * @var Array
     */
    private static $_search_types = array(
        'article' => array(
            // 索引名
            'name' => 'crab_article',
            // 数据源
            'source' => 'crab_article',
        ),
        'user' => array(
            // 索引名
            'name' => 'crab_user',
            // 数据源
            'source' => 'user',
        ),
        'ask' => array(
            // 索引名
            'name' => 'ask',
            // 数据源
            'source' => 'ask',
        ),
    );

    public  function __construct(){
        \Ouno\Ouno::import('/components/sphinxapi.php');
        $this->sphinx = New \sphinxClient();
        $this->sphinx->SetServer( '127.0.0.1', 9312 );
        $this->sphinx->SetConnectTimeout(5);
        //设置结果以普通数组返回

    }

    public function preQuery(){
        $this->sphinx->SetArrayResult(true);
        //匹配所有查询词(默认模式)
        //$sphinx->SetMatchMode(SPH_MATCH_ANY);
        //清除上一次查询设置到过滤器
        //$this->sphinx->ResetFilters();
        //根据相似度排序
        //$sphinx->SetRankingMode(SPH_RANK_WORDCOUNT);
        //$sphinx->SetSortMode(SPH_SORT_RELEVANCE);
        //$this->sphinx->SetArrayResult(true);
        //$this->sphinx->SetFilterRange('en_length', 10,50);
    }

    public function selectArt($word, $offset = 0, $limit = 10){
        $this->preQuery();
        $word = $this->getUtf8Encode($word);
        echo $word;
        $this->sphinx->SetFieldWeights(array('title'=>'故乡'));
        //$this->sphinx->SetSortMode(SPH_SORT_RELEVANCE, '@weight DESC,@read_num DESC');

        $this->sphinx->setFilter( 'title', $word );
        $this->sphinx->SetLimits( $offset, $limit, self::MAX_MATCHS);
        //模糊匹配
        $this->sphinx->SetMatchMode(SPH_MATCH_ANY);
        $this->sphinx->AddQuery( $word, self::$_search_types['article']['name'] );

        $res = $this->sphinx->RunQueries();


        // $res = $this->sphinx->BuildExcerpts ( $docs, $index, $words, $opts );
        //$res = $this->sphinx->setFilter('content', array($word));

        return $res;
    }

    private function getUtf8Encode($str)
    {

        if (mb_detect_encoding($str, 'UTF-8', true) === false) $str = utf8_encode($str);
        return $str;
    }

    public function selectUser(){


        return true;
    }

}