<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2014/10/12
 * Time: 13:36
 */
namespace src\service\search;
class searchService extends \components\BaseService{

    public function getArt($word, $highlight = true){
         \Ouno\Ouno::import('/src/dao/search/searchDao.php');
        $searchDao = new \searchDao();
        //$search = $searchDao->getSphinx();
        $res = $searchDao->selectArt($word);
        var_dump($res);
//        $artDao = InitPHP::getDao('homeArt', 'Home');
//        $res = $this->getResultByIds($artDao, $res, $word, $highlight);

    }


    public function getResultByIds($dao, $matches , $word, $highlight = true){
        if(!empty($matches[0]['matches'])){
            $ids = '';
            foreach($matches[0]['matches'] as $key=>$val){
                $ids .= $val['id'] . ',';
            }
            $ids = rtrim($ids, ',');
            echo $ids;
            $res = $dao->getArtByIds($ids);
           // var_dump($res);
            if($highlight == true){

                foreach($res as $key=>&$val){
                    $bb = $this->buildExcerpt($val['content'], 'home_art', $word);
                    var_dump($bb);
                    if($bb) $val = $bb;
                }
            }

        }
        return !empty($res) ? $res : false;

    }

    public function buildExcerpt($text,$index, $word, $option = null){
        if($option == null){
            $option = array(
                "before_match"		=> "<b>",
                "after_match"		=> "</b>",
                "chunk_separator"	=> " ... ",
                "limit"				=> 60,
                "around"			=> 3,
            );
        }

        $docs = array
        (
            "this is my test text to be highlighted, and for the sake of the testing we need to pump its length somewhat",
            "another test text to be highlighted, below limit",
            "test number three, without phrase match",
            "final test, not only without phrase match, but also above limit and with swapped phrase text test as well",
        );
        $words = "test text";
        $index = "home_art";
        $opts = array
        (
            "before_match"		=> "<b>",
            "after_match"		=> "</b>",
            "chunk_separator"	=> " ... ",
            "limit"				=> 60,
            "around"			=> 3,
        );
        $res = InitPHP::getDao('search', 'Search')->sphinx->buildExcerpts( $text, $index, $word, $option );
        var_dump($res);
        return InitPHP::getDao('search', 'Search')->sphinx->buildExcerpts($text, $index, $word, $option);
    }







}