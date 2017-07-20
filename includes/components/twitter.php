<?php
Class Twitter{
  private $twitter;
  private $settings;
  public $accounts;
  private $datarecord;

  function __construct(){
    $this->setup();
  }
  function setup(){
    $this->keys = array(
        'oauth_access_token' => "114293337-uRG0UjVRcDqPJMnvaYXYyZVbHcpkePGnHLVSXg2N",
        'oauth_access_token_secret' => "1py3P0rlPFm9Irdn2eG6ArumvzWY7n8rGCwHbYMsBeTni",
        'consumer_key' => "22F85O98SQDUJ54FF9rTv8zwU",
        'consumer_secret' => "wPMzAABC6P8D8IYbqFT3B5zDWwgW5j0vuP2zxv3UPYnBQ7vY7C"
    );
    $BrandDB = new Brands();
    $this->settings = array(
      'accounts' => $BrandDB->get_brand_profiles('twitter')
    );
  }
  function make_call($endpoint, $data, $method = 'GET'){
    $twitter = new TwitterAPIExchange($this->keys);
    return $twitter->setGetfield('?' . $data)
        ->buildOauth('https://api.twitter.com/1.1/' . $endpoint, $method)
        ->performRequest();
  }

  function refresh_data(){
    $sites = $this->update_sites($this->settings['accounts']);
    $stats = $this->update_stats();
  }

  function update_sites(){
    $accountdata = json_decode($this->make_call('users/lookup.json',  'screen_name=' . implode(",", $this->settings['accounts'])));
    foreach($accountdata as $account) {
      $entry = array(
        'id' => $account->id_str,
        'account' => $account->screen_name,
        'name' => $account->name,
        'profile_image_url_https' => $account->profile_image_url_https,
        'description' => $account->description,
        'colour' => $account->profile_link_color
      );
      DB::replace('sources_twitter_sites', $entry);
    }
    global $database;
    $this->accounts = $database->twitter_get_sites();

  }
  function update_stats(){
    foreach($this->settings['accounts'] as $screen_name){
      $this->datarecord = array(
        'totals' => array(
          'favs' => 0,
          'retweets' => 0,
          'mentions' => 0,
          'engagement' => 0
        )
      );
      //First get our own posts
      $this->search_tweets($screen_name);
      $this->search_mentions($screen_name);
      $this->calculate_engagement();

      $this->store_data($screen_name);
    }
  }
  function search_tweets($screen_name, $offset = null){
    $i = 1;
    if($offset != null) {
      $timeline = json_decode($this->make_call('statuses/user_timeline.json',  'screen_name=' . $screen_name . '&include_rts=false&exclude_replies=false&count=30&max_id=' . $offset));
    } else {
      $timeline = json_decode($this->make_call('statuses/user_timeline.json',  'screen_name=' . $screen_name . '&include_rts=false&exclude_replies=false&count=30'));
    }
    foreach($timeline as $post) {
      if($i === 1) {
        $this->datarecord['totals']['followers'] = $post->user->followers_count;
        $this->datarecord['totals']['friends'] = $post->user->friends_count;
      }
      $i++;
      if(strtotime($post->created_at) > $offset) {
        $this->datarecord['totals']['favs'] += $post->favorite_count;
        $this->datarecord['totals']['retweets'] += $post->retweet_count;
        $this->datarecord[$post->id_str] = array(
          'id' => $post->id_str,
          'favs' => $post->favorite_count,
          'retweets' => $post->retweet_count,
          'replies' => 0,
          'text' => $post->text,
          'engagement' => 0
        );
      }
      //Simulate pagination
      if(count($timeline) == 30) {
        $this->search_tweets($screen_name, $timeline->statuses[29]->id_str);
      }
    }
  }
  function search_mentions($screen_name, $offset = null){
    if($offset != null) {
      $timeline = json_decode($this->make_call('search/tweets.json',  'q=@' . $screen_name .'&count=100&max_id=' . $offset));
    } else {
      $timeline = json_decode($this->make_call('search/tweets.json',  'q=@' . $screen_name .'&count=100'));
    }
    foreach($timeline->statuses as $status){
      $this->datarecord['totals']['mentions'] += 1;
      if($status->in_reply_to_screen_name === $screen_name) {
        if(array_key_exists($status->in_reply_to_status_id_str, $this->datarecord)) {
          $this->datarecord[$status->in_reply_to_status_id_str]['replies'] += 1;
        }
      }
    }
    //Simulate pagination
    if(count($timeline->statuses) == 100) {
      $this->search_mentions($screen_name, $timeline->statuses[99]->id_str);
    }
  }
  function calculate_engagement(){
    $i = 0;
    foreach($this->datarecord as $post) {
      //Skip "totals" entry
      if($i === 0) {
        $this->datarecord['totals']['engagement'] = (($post['favs'] + $post['retweets'] + $post['mentions']) / $this->datarecord['totals']['followers']) * 100;
      } else {
        $this->datarecord[$post['id']]['engagement'] = (($post['favs'] + $post['retweets'] + $post['replies']) / $this->datarecord['totals']['followers']) * 100;
      }
      $i++;
    }
  }
  function store_data($account){
    //Move totals to root of Array
    $entry = array();
    $entry['account'] = $account;
    $entry['date'] = date("Y-m-d", strtotime('today'));
    $entry['followers'] = $this->datarecord['totals']['followers'];
    $entry['friends'] = $this->datarecord['totals']['friends'];
    $entry['favs'] = $this->datarecord['totals']['favs'];
    $entry['retweets'] = $this->datarecord['totals']['retweets'];
    $entry['mentions'] = $this->datarecord['totals']['mentions'];
    $entry['engagement'] = $this->datarecord['totals']['engagement'];
    unset($this->datarecord['totals']);

    //Encode posts data
    $entry['posts'] = json_encode($this->datarecord);
    //Store it
    DB::replace('sources_twitter_data', $entry);
  }
}
?>
