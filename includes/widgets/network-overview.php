<?php
class NetworkOverview extends GBWidget{
  public $dateranges;
  protected $id;
  function __construct(){
    parent::__construct();
    $this->dateranges = array(
      'now' => array($this->daterange, null),
      'quarter' => array(14,null),
      'year' => array(28,7)
    );
    $this->chart_type = 'line';
    $this->outputWidget();
    $this->closeSection();
  }
  //
  //Output HTML
  //
  function outputWidget(){
    echo '<div id="network-overview">';
    foreach($this->networks as $network => $id) {
        $this->id = 'network-overview-'. $network;
        $this->title = $network;
        $this->id = $id;
        $this->icon = 'fa-' . $network;
        echo '<article id="'. $this->title .'">';
        echo $this->buildWidgetHeader();
        $this->widgetBody();
        echo '</article>';
    }
    echo '</div>';
  }
  function buildWidgetHeader($id = false, $icon = false, $title = false){
    $output = '<div class="bar">';
    $output .= '<i class="fa '. $this->icon .'" aria-hidden="true"></i>';
    $output .= '<h5>'. ucfirst($this->title) .'</h5>';
    $output .= '<p>'. $this->id .'</p>';
    $output .= '</div>';
    return $output;
  }
  function widgetBody(){
    $function = 'format_' . $this->title . 'stats';
    $dataset = array();
    foreach($this->dateranges as $range => $dates) {
      $dataset[$range] = $this->{$function}($dates[0], $dates[1]);
    }
    echo '<ul class="body">';
    foreach($dataset['now'] as $name => $stat) {
      echo '<li>';
      echo '<div class="statistic"><h5 data-now="'. $stat .'" ';
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
      echo '>' . number_format($stat) .'</h5>';
      echo '<span class="statcaption">'. ucfirst($name) .'</span></div>';
      echo '</li>';
    }
    echo '</ul>';
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
    unset($dataset['friends']);
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

?>
