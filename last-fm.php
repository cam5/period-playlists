<?php

include('date-retriever.php');

class last_fm {

    public $user;       
    public $date_list;  //Will pull in a date_retriever object
    public $start_date; //Standard start date.
    
    /*
     *  __construct
     *
     *  Upon instantiation, the
     *  @date_list (date_retriever obj.) (->wlist is the array of weeks)
     *  @user and 
     *  @tofrom become available.
     *
     */
    function __construct() {
        $this->date_list = new date_retriever;

        if ( isset($_GET['user']) && $_GET['user'] != '') $this->user = $_GET['user'];
        else $this->user = 'lshpdttrsblck';
        
        if ( isset($_GET['period']) && $_GET['period'] != '') $this->start_date = $_GET['period'];
        else $this->start_date = 1349611200;
    }
    
    /*
     *  get_week
     *
     *  $start_date = the unix timestamp appearing somewhere in date_list->wlist
     *
     */
    function get_week($index) {
        return $this->build_array($this->cache_or_not($index));
    }
    
    /*
     *  sev_weeks
     *
     *  takes a $start_week unix timestamp,
     *  and a number of weeks to run for
     *
     *  returns an assoc. array of tracks
     *
     */
     
     function sev_weeks($start_week, $weeks_to_ask_for=16) {
        
        $index = $this->get_date_index( $start_week );
        $big_array = array();
        
        for ($i=0; $weeks_to_ask_for > $i; $i++) {
            $current_week = $this->get_week($index + $i);
            
            foreach ($current_week as $song)
                $big_array[] = $song;
                
        }
        
        return $big_array;
        
     }
    

    /*
     *  getToFrom
     *
     *  $offset the number of weeks to jump ahead by
     *  uses the date_list property for references
     *
     */
    function getToFrom($offset, $query = false) {
        $array_tofrom = $this->date_list->wlist[$offset]['unix-time'];
        
        if ($query)
            return "&from=".$array_tofrom['from']."&to=".$array_tofrom['to'];
            
        else
            return $array_tofrom;
    }
    
    
    /*
     *  cache_or_not
     *  
     *  $index = the index for the date_list->wlist property
     *  that corresponds to the week we're pulling data for
     *
     */
    function cache_or_not($index) {
    
        $this_date = $this->get_date_index($index);
    
        $cache_location = "cache/" . $this->user . "-" . $this->date_list->wlist[$index]['unix-time']['from'] . ".xml";
        
        if (file_exists($cache_location)) {
            $dom_doc = new DOMDocument;
            $dom_doc->load($cache_location);
            
            return $dom_doc;
        }
        
        else {
            $dom_doc = new DOMDocument;
            $dom_doc->load($this->get_url($index));
            
            //Save a cached copy
            $fp = fopen($cache_location, 'w');
            fwrite($fp, $dom_doc->saveXML());
            fclose($fp);
            
            return $dom_doc;
        }
        
    }
    
    
    
    /*
     *  get_url
     *
     *  $index = the index for the date_list array 
     *  which corrospondes to the week at which to pull data
     *  returns a URL to an XML file to load into a DOMDocument
     *
     */
    function get_url($index) {
        
        global $api_key;    //Necessary to access Last.fm's datastores.

        $tofrom = $this->getToFrom($index, TRUE); //Ask for querystring
        
        return 'http://ws.audioscrobbler.com/2.0/?method=user.getweeklytrackchart&user=' . $this->user . $tofrom . "&api_key=" . $api_key;
    }
    
    /*
     *  get_date_index
     *
     *  $start_date = a unix timestamp appearing in this object's
     *  date_list property
     *
     */
    
    function get_date_index($start_date) {
        
        $index = '';
        
        //Assign $size in the first expression to save memory!
        for ($i=0, $size=count($this->date_list->wlist); $i < $size; $i++) {
            
            if ($this->date_list->wlist[$i]['unix-time']['from'] == $start_date)
                $index = $i;

        }
        return $index;
    }
    
    
    /*
     *  build_array
     *
     *  $dom_doc is a DOMDocument created with either a URL or a cached local file
     *  returns an assoc. array of tracks with name, artist, playcount, and image 
     *  for each song.
     *
     */
    function build_array($dom_doc) {
        
        $track_els = $dom_doc->getElementsByTagName( "track" );
        $tracks = array();
        
        $i=0;
        foreach( $track_els as $track ) {
          $track_name = $track->getElementsByTagName( "name" );
          $tracks[$i]['song-name'] = $track_name->item(0)->nodeValue;
          
          $track_artist = $track->getElementsByTagName( "artist" );
          $tracks[$i]['artist-name'] = $track_artist->item(0)->nodeValue;
          
          $track_playcount = $track->getElementsByTagName( "playcount" );
          $tracks[$i]['playcount'] = $track_playcount->item(0)->nodeValue;
          
          $track_image = $track->getElementsByTagName( "image" );
          $tracks[$i]['image'] = $track_image->item(2)->nodeValue;
          
          $i++;
        }
        return $tracks;
    }
    
}

?>