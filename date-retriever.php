<?php

class date_retriever {

    public $wlist;
    public $user;
    public $cache;

    function __construct() {

        global $api_key;
        $this->user = (!empty($_GET['user']) || $_GET['user'] = '') ? $_GET['user'] : 'lshpdttrsblck';
        
        $cachefile = 'date_cache.xml';
        $chart_week_url = 'http://ws.audioscrobbler.com/2.0/?method=user.getweeklychartlist&user=' . $this->user . "&api_key=" . $api_key;
        $chart_weeks = new DOMDocument();
        
        if (file_exists($cachefile) && (time() - 604800 < filemtime($cachefile))) {
            $chart_weeks->load($cachefile);
        }
        
        else {
            $chart_weeks->load($chart_week_url);   
            $fp = fopen($cachefile, 'w');
            fwrite($fp, $chart_weeks->saveXML());
            fclose($fp);
        }
        
        $weeks = $chart_weeks->getElementsByTagName( "chart" );

        foreach( $weeks as $week ) {
          $week_list[] = array(
            'unix-time' => array( 'from' => $week->getAttribute('from'), 'to' => $week->getAttribute('to')),
            'real-time' => date("M j \'y", $week->getAttribute('from'))
            );
        }
        
        $this->wlist = $week_list;
    }
}
?>