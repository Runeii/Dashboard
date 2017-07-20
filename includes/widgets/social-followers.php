<?php
class SocialFollowers extends GBWidget{

  function __construct(){
    parent::__construct();
    $this->title = 'Follower growth';
    $this->id = 'social-followers';
    $this->chart_type = 'line';

    $this->outputWidget();
  }
  //
  //Format data
  //
  function format_twitterstats($start = null, $end = null){
    $twitterstats = $this->database->twitter_get_stats($this->brand['twitter'], $this->daterange);
    $dataset = $this->blank_dataset();
    foreach($twitterstats as $date => $data) {
      $offset = array_search($date, $this->dates);
      if($offset !== false) {
        $dataset[$offset] = $data['totals']['followers'];
      }
    }
    return $dataset;
  }

  function format_facebookstats($start = null, $end = null){
    $fbstats = $this->database->facebook_get_stats($this->brand['facebook'], $this->daterange);
    $dataset = $this->blank_dataset();
    foreach($fbstats as $day) {
      $offset = array_search($day['date'], $this->dates);
      if($offset !== false) {
        if($day['Fans'] === null) {
          $dataset[$offset] = 0;
        } else {
          $dataset[$offset] = $day['Fans'];
        }
      }
    }
    return $dataset;
  }
  function format_instagramstats($start = null, $end = null){
    $instastats = $this->database->instagram_get_stats($this->brand['instagram'], $this->daterange);
    $dataset = $this->blank_dataset();
    foreach($instastats as $day) {
      $offset = array_search($day['date'], $this->dates);
      if($offset !== false) {
        $dataset[$offset] = $day['fans'];
      }
    }
    return $dataset;
  }

}

new SocialFollowers();
?>
