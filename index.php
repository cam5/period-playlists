<?php
include ('last-fm-caller.php'); include('date-retriever.php');

//Ask Last.fm for a week's info.
$lfm = new last_fm_call;
$charts = array_reverse($lfm->date_list->wlist);
$tracks = $lfm->sev_weeks($lfm->start_date, 4);

if (isset($_GET['period']) && $_GET['period'] != '')
    $period = $_GET['period'];
else $period = '';

?>
<html>
<head>
    <title>Period Playlists</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="js/jquery.masonry.min.js"></script>
    <link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div id="header">
    <h1><? echo $lfm->user; ?>'s top tracks</h1>
</div>

<div id="period-selector">
    <form action="index.php" method="GET" >
        
        <input name="user" type="text" value="" />
        
        <select name="period">
            <? foreach ($charts as $chart) : ?>
                <option value="<? echo $chart['unix-time']['from']; ?>" <? echo ($period == $chart['unix-time']['from'] ? ' selected ' : ''); ?>><? echo $chart['real-time']; ?></option>
            <? endforeach; ?>
        </select>
        <br />
        <input type="submit">
    </form>
</div>

<div id="song-container">
    <? if ($tracks) : ?>
        <? foreach ($tracks as $track) : ?>
            <div class="song">
                <img src="<? echo $track['image']; ?>" />
                <p class="song-name"><? echo $track['song-name']; ?></p>
                <p class="artist-name"><? echo $track['artist-name']; ?></p>
                <div class="playcount">(<? echo $track['playcount'] ?> plays)</div>
            </div>
        <? endforeach; ?>
    <? endif; ?>
</div>


<script type="text/javascript">
    var $c = $('#song-container');
    $c.imagesLoaded(function(){
      $c.masonry({
        itemSelector : '.song',
        isAnimated: true
      });
    });
    
</script>

</body>

</html>