<?php
class NetworkOverview extends GBWidget{
  public $dateranges;

  function __construct(){
    parent::__construct();
    $this->dateranges = array(
      'now' => array($this->daterange, null),
      'quarter' => array(14,null),
      'year' => array(28,7)
    );
    $this->chart_type = 'line';

    $this->outputWidget();
  }
  //
  //Output HTML
  //
  function outputWidget(){
    echo '<div id="network-overview">';
    foreach($this->networks as $network => $id) {
        $this->id = 'network-overview-'. $network;
        $this->title = $network;
        $this->icon = 'fa-' . $network;
        echo $this->buildWidgetHeader();
        $this->widgetBody();
        echo $this->closeWidget();
    }
    echo '</div>';
  }
  function buildWidgetHeader($id = false, $icon = false, $title = false){
    $output = '<article id="'. $this->id .'">';
    $output .= '<div class="bar">';
    $output .= '<h5><i class="fa '. $this->icon .'" aria-hidden="true"></i>'. $this->title .'</h5>';
    $output .= '<h5 class="tab active" data-navigate="setDate" data-navtype="action">now</h5>
                <h5 class="tab" data-navigate="setDate" data-navtype="action">quarter</h5>
                <h5 class="tab" data-navigate="setDate" data-navtype="action">year</h5>';
    $output .= '</div>';
    return $output;
  }
  function widgetBody(){
    $function = 'format_' . $this->title . 'stats';
    $dataset = array();
    foreach($this->dateranges as $range => $dates) {
      $dataset[$range] = $this->{$function}($dates[0], $dates[1]);
    }
    echo '<div class="body">';
    foreach($dataset['now'] as $name => $stat) {
      echo '<div class="statistic"><h4 data-now="'. $stat .'" ';
      if(array_key_exists($name, $dataset['quarter'])) {
        echo 'data-quarter="'. $dataset['quarter'][$name] .'"';
      } else {
        echo 'data-quarter="0"';
      }
      if(array_key_exists($name, $dataset['year'])) {
        echo 'data-year="'. $dataset['year'][$name] .'"';
      } else {
        echo 'data-year="0"';
      }
      echo '>' . number_format($stat) .'</h4>';
      echo '<caption>'. ucfirst($name) .'</caption></div>';
    }
    echo '</div>';
  }
  //
  //Format data
  //
  function format_twitterstats($start, $end = null){
    $twitterstats = $this->database->twitter_get_stats($this->brand['twitter'], $start, $end);
    $dataset = array();
    foreach($twitterstats as $data) {
      foreach($data['totals'] as $metric => $value) {
        if(!array_key_exists($metric, $dataset)) {
          $dataset[$metric] = 0;
        }
        $dataset[$metric] += $value;
      }
    }
    return $dataset;
  }

  function format_facebookstats($start, $end = null){
    $fbstats = $this->database->facebook_get_stats($this->brand['facebook'], $start, $end);
    $dataset = array();
    foreach($fbstats as $day) {
      foreach($day as $metric => $value) {
        if(is_numeric($value)) {
          if(!array_key_exists($metric, $dataset)) {
            $dataset[$metric] = 0;
          }
          $dataset[$metric] += $value;
        }
      }
    }
    unset($dataset['id']);
    return $dataset;
  }
  function format_instagramstats($start, $end = null){
    $instastats = $this->database->instagram_get_stats($this->brand['instagram'], $start, $end);
    $dataset = array();
    foreach($instastats as $day) {
      foreach($day as $metric => $value) {
        if(is_numeric($value)) {
          if(!array_key_exists($metric, $dataset)) {
            $dataset[$metric] = 0;
          }
          $dataset[$metric] += $value;
        }
      }
    }
    unset($dataset['id']);
    return $dataset;
  }

}

new NetworkOverview();
?>
