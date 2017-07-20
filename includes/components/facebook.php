<?php
Class Facebook{
  private $fb;
  private $token;
  private $pages = array();
  public $stats = array();

  function __construct(){
    $this->fb = new \Facebook\Facebook([
      'app_id' => '165243820681529',
      'app_secret' => '46166ff77b7c9ada20f1ff4196b0c5a0',
      'default_graph_version' => 'v2.9',
    ]);
    $this->authenticate();
  }
  function authenticate(){
    //Long lived token
    $this->token = 'EAACWSdM7qTkBAM8TeSRAuPGIZAInL5I1xwQ3Uu8R6Kq5xuvMAXozGM2Ss0dPTj8EvO3hvskIaIhQE8utxtOGJYfyhnqHjuhjmfPio26nJzZBPZArmzOmvS2ZC46pdCCkwa295XaWS1a3MmH5fpNB9HsMKhUMeiIZD';
  }
  function make_call($endpoint, $token = null){
    if($token === null) $token = $this->token;
    try {
      $response = $this->fb->get($endpoint, $token);
    } catch(\Facebook\Exceptions\FacebookResponseException $e) {
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(\Facebook\Exceptions\FacebookSDKException $e) {
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }
    return $response;
  }

  function refresh_data(){
    $this->update_pages();
    $this->update_stats();
  }
  function update_pages($paginating = false, $last = null){
    if($paginating === false) {
      $feed = $this->make_call('/me/accounts')->getGraphEdge();
    } else {
      $feed = $this->make_call($last->getEndpoint())->getGraphEdge();
    }
    $pages = $feed->asArray();
    foreach($pages as $page) {
      $this->pages[] = array(
        'name' => $page['name'],
        'id' => $page['id'],
        'access_token' => $page['access_token']
      );
    }

    if($feed->getPaginationRequest('next') != null) {
      $this->update_pages(true, $feed->getPaginationRequest('next'));
    } else {
      DB::replace('sources_facebook_pages', $this->pages);
    }
  }
  function update_stats(){
    global $database;
    $database->facebook_get_pages();
    $day_metrics = array('page_posts_impressions', 'page_impressions_unique', 'page_fan_removes', 'page_post_engagements');
    $lifetime_metrics = array('page_fans');

    foreach($this->pages as $page) {
      $response = $this->make_call($page['id'] . '/insights?metric=' . json_encode($day_metrics) . '&period=day', $page['access_token']);
      $results = array(
        'id' => $page['id'],
        'date' =>date("Y-m-d", strtotime('today'))
      );

      $data = $response->getGraphEdge()->asArray();
      foreach($data as $metric) {
        $total = 0;
        foreach($metric['values'] as $value) {
          $total += $value['value'];
        }
        $results[$metric['name']] = $total;
      }
      $response = $this->make_call($page['id'] . '/insights?metric=' . json_encode($lifetime_metrics) . '&period=lifetime&since='. strtotime('today') .'&until='. strtotime('today'), $page['access_token']);
      $data = $response->getGraphEdge()->asArray();
      foreach($data as $metric) {
        $results[$metric['name']] = $metric['values'][0]['value'];
      }
      $this->stats[] = array(
        'date' => $results['date'],
        'id' => $results['id'],
        'Page Impressions' => $results['page_posts_impressions'],
        'Post Impressions' => $results['page_impressions_unique'],
        'Unlikes' => $results['page_fan_removes'],
        'Engagements' => $results['page_post_engagements'],
        'Fans' => $results['page_fans']
      );
    }
    DB::replace('sources_facebook_data', $this->stats);
  }
}
?>
