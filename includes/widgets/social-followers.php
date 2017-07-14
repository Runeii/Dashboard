<?php
class SocialFollowers extends GBWidget{

  function __construct(){
    parent::__construct();
    $this->title = 'Follower growth';
    $this->id = 'social-followers';

    $this->outputWidget();
  }

  //
  //Output HTML
  //
  function widgetBody(){
      echo '
      <canvas id="social-followers-chart" width="400" height="400"></canvas>
      <script type="text/javascript">
            var ctx = document.getElementById("social-followers-chart").getContext("2d");
            var myChart = new Chart(ctx, {
                "type": "line",
                "data": '. $this->build_dataset() .',
                "options": '. $this->chart_options() .'
            });
            '. $this->chartDefaults .'
          </script>';
  }

  //
  //Format data
  //
  function format_twitterstats(){
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

  function format_facebookstats(){
    $fbstats = $this->database->facebook_get_stats($this->brand['facebook'], $this->daterange);
    $dataset = $this->blank_dataset();
    foreach($fbstats as $day) {
      $offset = array_search($day['date'], $this->dates);
      if($offset !== false) {
        if($day['page_fans'] === null) {
          $dataset[$offset] = 0;
        } else {
          $dataset[$offset] = $day['page_fans'];
        }
      }
    }
    return $dataset;
  }
  function format_instagramstats(){
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
