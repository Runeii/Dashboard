<?php
class WebsiteReferrals extends GBWidget{
  private $data;
  private $setname;
  private $temporary_database;

  function __construct(){
    parent::__construct();
    $this->format_data();

    $this->title = 'Referrals';
    $this->icon = 'fa-filter';
    $this->id = 'website-referrals';
    $this->chart_type = 'doughnut';
    $this->outputWidget();
  }

  function widgetBody(){
    $this->id = 'website-referrals-overview';
    $this->setname = 'overview';
    echo '<div class="overview">
            <canvas id="'. $this->id .'-chart" width="400" height="400"></canvas>
          </div>';
    $this->build_chart();
    echo '<div class="details">';
      $this->id = 'website-referrals-social';
      $this->setname = 'social';
      echo '<div class="graph"><canvas id="'. $this->id .'-chart" width="400" height="400"></canvas></div>';
      $this->build_chart();

      $this->id = 'website-referrals-historic';
      $this->setname = 'historic';
      $this->chart_type = 'line';
      echo '<div class="graph"><canvas id="'. $this->id .'-chart" width="400" height="400"></canvas></div>';
      $this->build_chart();
    echo '</div>';
  }
  function chart_options($args){
    $args['legend'] = array(
      'position' => 'bottom'
    );
    return $args;
  }
  function build_dataset(){
    $limit = 5;
    switch($this->setname):
      case 'historic':
        $dataset = $this->format_historicstats();
        break;
      case 'social':
        $this->format_data('socialreferrals', 'socialNetwork');
      default:
        $dataset = array(
          'labels' => $this->format_referralstats('labels', $limit),
          'datasets' => array(
            $this->create_source('Google', $this->format_referralstats('data', $limit))
          ),
        );
        $dataset['datasets'][0]['backgroundColor'] = $this->generate_piecolours($dataset['labels']);
        break;
    endswitch;

    return json_encode($dataset);
  }
  function format_referralstats($type = null, $limit = 9999){
    $dataset = array();
    $i = 0;
    foreach($this->data as $source => $data) {
      if($source != '(not set)') {
        if($type === 'labels') {
          $dataset[] = ucfirst($source);
        } else {
          $dataset[] = $data['sessions'];
        }
      }
      $i++;
      if($limit == $i) break;
    }
    return $dataset;
  }
  function format_historicstats(){
    $sourceset = array();
    foreach($this->temporary_database['socialreferrals'] as $day => $data) {
      $offset = array_search($day, $this->dates);
      if($offset !== false) {
        foreach($data as $source) {
          if(!array_key_exists($source->socialNetwork, $sourceset)) {
              $sourceset[$source->socialNetwork] = array(
                "label" => $source->socialNetwork,
                "data" => $this->blank_dataset(),
                "fill" => true,
                "borderColor" => "#000000"
              );
          }
          $sourceset[$source->socialNetwork]['data'][$offset] += $source->sessions;
        }
      }
    }
    unset($sourceset['(not set)']);
    $dataset = array(
      'labels' => $this->dates,
      'datasets' => array_values($sourceset)
    );
    return (object) $dataset;
  }

  function format_data($set = 'referrals', $dimension = 'source'){
    if(!is_array($this->temporary_database)) {
      $data = $this->database->analytics_get_stats($this->brand['analytics'], $this->daterange);
      $temp = $this->data = array();
      foreach($data as $item) {
        $stored = json_decode($item['value']);
        foreach($stored as $name => $dataset) {
          $temp[$name][$item['date']] = $dataset;
        }
      }
      $this->temporary_database = $temp;
    }
    $this->data = array();
    $temp = $this->temporary_database[$set];
    foreach($temp as $date) {
      foreach($date as $source) {
        if(!array_key_exists($source->{$dimension}, $this->data)) {
          $this->data[$source->{$dimension}] = array(
            'views' => 0,
            'sessions' => 0
          );
        }
        $this->data[$source->{$dimension}]['views'] += $source->views;
        $this->data[$source->{$dimension}]['sessions'] += $source->sessions;
      }
    }
  }
}
new WebsiteReferrals();

?>
