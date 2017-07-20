<?php
Class Google_Data{
  public $data;
  public $sites;
  private $analytics;
  private $reporting;
  private $credentials = __DIR__ . '/ga-credentials.json';

  function __construct(){
    $this->analytics = $this->initializeAnalytics();
    $this->reporting = $this->initializeAnalyticsReporting();
    $this->errorTiming = 0;
  }

  //Updating
  function refresh_data(){
    $this->update_sites();
    $this->update_all_stats();
  }

  //Sites
  function update_sites(){
    $analytics = $this->analytics;
    $properties = $analytics->management_webproperties->listManagementWebproperties('~all')->getItems();
    $response = [];
    DB::delete('sources_googleanalytics_sites', 'id', '*');
    foreach($properties as $property) {
      $profile = $analytics->management_profiles->listManagementProfiles($property->accountId, $property->id)->getItems();
      if(is_object($profile[0])) {
        $entry = array(
          'UAId' => $property->id,
          'accountId' => $property->accountId,
          'viewid' => $profile[0]->id,
          'internalWebPropertyId' => $property->internalWebPropertyId,
          'name' => $property->name,
          'profileCount' => $property->profileCount,
          'selfLink' => $property->selfLink,
          'updated' => $property->updated,
          'websiteUrl' => $property->websiteUrl
        );
        DB::replace('sources_googleanalytics_sites', $entry);
        $response[] = $entry;
      }
    }
    $this->sites = $response;
  }

  //Stats
  function update_all_stats(){
    foreach($this->sites as $site) {
      $this->update_stats($site['viewid']);
    }
  }

  function update_stats($id){
    $this->current_view_id = $id;

    $this->get_averagetime();
    $this->get_toppages();
    $this->get_toplandingpages();
    $this->get_referrals();
    $this->get_socialreferrals();

    $this->commit_data();
  }

  function get_averagetime() {
    $args = array('metrics' => 'ga:avgSessionDuration');
    $dataset = $this->get_dataset($args);
    //From the first (only) set, return rows, get first row, get metrics from that row, get that values from that row, the first (only) value
    $reply = $dataset[0]->getData()->getRows();
    if(sizeof($reply) > 0) {
      $reply = $reply[0]->getMetrics()[0]->getValues()[0];
    } else {
      $reply = 0;
    }

    $this->store_data('averagetime', $reply);
    return $reply;
  }
  function get_toppages() {
    $args = array(
      'metrics' => 'ga:pageviews',
      'dimensions' => 'ga:pageTitle',
      'sort' => array('ga:pageviews', 'DESCENDING')
    );
    $dataset = $this->get_dataset($args);

    $reply = array();

    $rows = $dataset[0]->getData()->getRows();
    foreach($rows as $row) {
      $reply[] = array(
        'title' => $row->getDimensions()[0],
        'views' => $row->getMetrics()[0]->getValues()[0]
      );
    }
    $this->store_data('pages', $reply);
    return $reply;
  }
  function get_toplandingpages() {
    $args = array(
      'metrics' => 'ga:pageviews',
      'dimensions' => 'ga:pageTitle',
      'sort' => array('ga:pageviews', 'DESCENDING')
    );
    $dataset = $this->get_dataset($args);

    $reply = array();

    $rows = $dataset[0]->getData()->getRows();
    foreach($rows as $row) {
      $reply[] = array(
        'title' => $row->getDimensions()[0],
        'views' => $row->getMetrics()[0]->getValues()[0]
      );
    }
    $this->store_data('landingpages', $reply);
    return $reply;
  }
  function get_referrals() {
    $args = array(
      'metrics' => array('ga:sessions', 'ga:pageViews'),
      'dimensions' => 'ga:source',
      'sort' => array('ga:sessions', 'DESCENDING')
    );
    $dataset = $this->get_dataset($args);

    $reply = array();

    $rows = $dataset[0]->getData()->getRows();
    foreach($rows as $row) {
      $reply[] = array(
        'source' => $row->getDimensions()[0],
        'sessions' => $row->getMetrics()[0]->getValues()[0],
        'views' => $row->getMetrics()[0]->getValues()[1]
      );
    }
    $this->store_data('referrals', $reply);
    return $reply;
  }
  function get_socialreferrals() {
    $args = array(
      'metrics' => array('ga:sessions', 'ga:pageViews'),
      'dimensions' => 'ga:socialNetwork',
      'sort' => array('ga:sessions', 'DESCENDING')
    );
    $dataset = $this->get_dataset($args);

    $reply = array();

    $rows = $dataset[0]->getData()->getRows();
    foreach($rows as $row) {
      $reply[] = array(
        'socialNetwork' => $row->getDimensions()[0],
        'sessions' => $row->getMetrics()[0]->getValues()[0],
        'views' => $row->getMetrics()[0]->getValues()[1]
      );
    }
    $this->store_data('socialreferrals', $reply);
    return $reply;
  }
  function store_data($key, $data){
    $this->data[$key] = $data;
  }
  function commit_data(){
    $entry = array(
      'id' => 0, //autoincrements
      'UAId' => $this->current_view_id,
      'value' => json_encode($this->data),
      'date' => date("Y-m-d", strtotime('today'))
    );
    DB::replace('sources_googleanalytics_data', $entry);
    $new_row = DB::insertId();
  }

  function get_dataset($args = array()){
    $VIEW_ID = $this->current_view_id;
    $dateRange = new Google_Service_AnalyticsReporting_DateRange();
    $dateRange->setStartDate("yesterday");
    $dateRange->setEndDate("yesterday");

      if(array_key_exists('metrics', $args)) {
        $metrics = $args['metrics'];
        if(!is_array($metrics)) {
          $metrics = array($metrics);
        }
      } else {
        $metrics = array();
      }

      if(array_key_exists('dimensions', $args)) {
        $dimensions = $args['dimensions'];
        if(!is_array($dimensions)) {
          $dimensions = array($dimensions);
        }
      } else {
        $dimensions = array();
      }

      //Set metrics
      $m = array();
      foreach($metrics as $metric) {
        $$metric = new Google_Service_AnalyticsReporting_Metric();
        $$metric->setExpression($metric);
        $m[] = $$metric;
      }

      //Set dimensions
      $d = array();
      foreach($dimensions as $dimension) {
        $$dimension = new Google_Service_AnalyticsReporting_Dimension();
        $$dimension->setName($dimension);
        $d[] = $$dimension;
      }

      //Set sort
      if(array_key_exists('sort', $args))  {
        $ordering = new Google_Service_AnalyticsReporting_OrderBy();
        $ordering->setFieldName($args['sort'][0]);
        $ordering->setOrderType("VALUE");
        $ordering->setSortOrder($args['sort'][1]);
      }

    $request = new Google_Service_AnalyticsReporting_ReportRequest();
    $request->setViewId($VIEW_ID);
    $request->setDateRanges($dateRange);
    $request->setMetrics($m);
    $request->setDimensions($d);
    if(isset($ordering)) $request->setOrderBys($ordering);

    $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
    $body->setReportRequests( array( $request) );
    //Update catch errors, deploy exponential backoff if errors
    try {
      $response = $this->reporting->reports->batchGet( $body );
      return $response;
    } catch (Google_Service_Exception $e) {
      if($e->getCode() == 429) {
        customlog('Hit Google API limit, code ' . $e->getCode() . ': ' . $e->getMessage(), 'notice');
        $this->errorTiming += 2;
        customlog('Will wait ' . $this->errorTiming . ' seconds before proceeding.', 'notice');
        sleep($this->errorTiming);
        return $this->get_dataset($args);
      } else {
        customlog('Fatal Google API error, code ' . $e->getCode() . ': ' . $e->getMessage());
        die();
      }
    }
  }

  function initializeAnalytics() {
    global $KEY_FILE_LOCATION;
    $client = new Google_Client();
    $client->setApplicationName("Global Brands Comms Dashboard");
    $client->setAuthConfig($this->credentials);
    $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
    $analytics = new Google_Service_Analytics($client);

    return $analytics;
  }

  function initializeAnalyticsReporting(){
    global $KEY_FILE_LOCATION;
    $client = new Google_Client();
    $client->setApplicationName("Global Brands Comms Dashboard");
    $client->setAuthConfig($this->credentials);
    $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
    $reporting = new Google_Service_AnalyticsReporting($client);
    return $reporting;
  }
}

?>
