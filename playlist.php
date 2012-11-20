<?

include('last-fm.php');

class playlist {
    
    public $track_list;
    public $top_tracks;
    
    function __construct() {
        $last_fm = new last_fm;
        $this->track_list = $last_fm->sev_weeks( $last_fm->start_date );
    }
    
    function top_tracks($tracklist, $playcount_threshold=3) {
    
        $top_tracks = array();
    
        foreach ($tracklist as $track) {
        
            if ($track['playcount'] >= $playcount_threshold)
                $top_tracks[] = $track;
                
        }
            
        $this->top_tracks = $top_tracks;
    }
    
}

?>