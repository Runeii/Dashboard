<?php
class WebsitePages extends GBWidget{
  private $data;
  function __construct(){
    parent::__construct();
    $this->format_data();
    $this->outputWidget();
  }

  function outputWidget(){
    $this->print_popular();
    $this->print_landingpages();
  }
  function print_popular(){
    echo $this->buildWidgetHeader('website-popular-list', 'Most popular pages');
    echo '<table cellspacing="0" cellpadding="0">'. $this->build_list('pages') .'</table>';
    echo $this->closeWidget();
  }
  function print_landingpages(){
    echo $this->buildWidgetHeader('website-landing-list', 'Most popular landing pages');
    echo '<table cellspacing="0" cellpadding="0">'. $this->build_list('landingpages') .'</table>';
    echo $this->closeWidget();
  }
  function build_list($metric){
    $pages = array();
    foreach($this->data[$metric] as $date) {
      foreach($date as $item) {
        if(!array_key_exists($item->title, $pages)) {
          $pages[$item->title] = 0;
        }
        $pages[$item->title] += $item->views;
      }
    }
    $response = '';
    $i = 0;
    $response .= '<tr><th>#</th><th>Page</th><th>Views</th></tr>';
    foreach($pages as $title => $views) {
      if($title !== '(not set)') {
        $response .= '<tr><td>'. $i .'</td><td><a href="' . $title. '" class="page_title">'. $title .'</a></td><td>'. $views .'</td></tr>';
        $i++;
        if($i >= 10) {
          break;
        }
      }
    }
    return $response;
  }
  function format_data(){
    $data = $this->database->analytics_get_stats($this->brand['analytics'], $this->daterange);
    $this->data = array();
    foreach($data as $day) {
      $data = json_decode($day['value']);
      foreach($data as $metric => $value) {
        $this->data[$metric][$day['date']] = $value;
      }
    }
  }
}
new WebsitePages();

?>
