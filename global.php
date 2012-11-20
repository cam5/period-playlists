<?
class globals {
    
    public $api_key;
    public $user;
    
    function __construct() {
    
        if ( isset($_GET['user']) && $_GET['user'] != '') $this->user = $_GET['user'];
        else $this->user = 'lshpdttrsblck';
        
        include('api_key_location.php');
    
    }
    
}

$ppl_global = new globals;

?>