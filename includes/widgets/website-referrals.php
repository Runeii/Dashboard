<?php
class WebsiteReferrals extends GBWidget{
  private $data;
  function __construct(){
    parent::__construct();
    $this->format_data();

    $this->title = 'Referrals';
    $this->id = 'website-referrals';
    $this->icon = 'fa-filter';

    $this->outputWidget();
  }

  function widgetBody(){
    echo '<div class="overview"><canvas id="'. $this->id .'-overview-chart" width="400" height="400"></canvas></div>
        <script type="text/javascript">
          var ctx = document.getElementById("'. $this->id .'-overview-chart").getContext("2d");
          var myChart = new Chart(ctx, {
              "type": "doughnut",
              "data": '. $this->build_dataset() .',
              "options": '. $this->chart_options_pie() .'
          });
          '. $this->chartDefaults .'
        </script>';
  }
  function build_dataset(){
    $limit = 5;
    $dataset = array(
      'labels' => $this->format_referralstats('labels', $limit),
      'datasets' => array(
        $this->create_source('Google', $this->format_referralstats('data', $limit))
      ),
    );
    $dataset['datasets'][0]['backgroundColor'] = $this->generate_piecolours($dataset['labels']);
    return json_encode($dataset);
  }
  function format_referralstats($type = null, $limit = 9999){
    $dataset = array();
    $i = 0;
    foreach($this->data as $source => $data) {
      if($type === 'labels') {
        $dataset[] = ucfirst($source);
      } else {
        $dataset[] = $data['sessions'];
      }
      $i++;
      if($limit == $i) break;
    }
    return $dataset;
  }

  function format_data(){
    $data = $this->database->analytics_get_stats($this->brand['analytics'], $this->daterange);
    $temp = $this->data = array();
    foreach($data as $item) {
      $temp[$item['key']][$item['date']] = json_decode($item['value']);
    }
    $temp = $temp['referrals'];
    foreach($temp as $date) {
      foreach($date as $source) {
        if(!array_key_exists($source->source, $this->data)) {
          $this->data[$source->source] = array(
            'views' => 0,
            'sessions' => 0
          );
        }
        $this->data[$source->source]['views'] += $source->views;
        $this->data[$source->source]['sessions'] += $source->sessions;
      }
    }
  }
}
new WebsiteReferrals();

?>
