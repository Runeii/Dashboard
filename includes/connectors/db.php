<?php
class DBAccess {
  //ANALYTICS
  function analytics_get_sites($filter = null, $value = null) {
    if($filter === null) {
      return DB::query("SELECT * FROM sources_googleanalytics_sites");
    } else {
      return DB::query("SELECT * FROM sources_googleanalytics_sites WHERE ". $filter ."=%s", $value);
    }
  }
  function analytics_get_stats($id, $days = null){
    if($days === null) {
      $stats = DB::query("SELECT * FROM sources_googleanalytics_data WHERE UAId = %s", $id);
    } else {
      $stats = DB::query("SELECT * FROM sources_googleanalytics_data WHERE UAId = %s AND date > date_sub(curdate(), interval %i day)", $id, $days);
    }
    return $stats;
  }

  ///FACEBOOK
  function facebook_get_pages(){
    return DB::query("SELECT * FROM sources_facebook_pages");
  }
  function facebook_get_stats($id, $days = null){
    if($days === null) {
      $stats = DB::query("SELECT * FROM sources_facebook_data WHERE id = %s", $id);
    } else {
      $stats = DB::query("SELECT * FROM sources_facebook_data WHERE id = %s AND date > date_sub(curdate(), interval %i day)", $id, $days);
    }
    return $stats;
  }

  ///INSTAGRAM
  function instagram_get_accounts(){
    return DB::query("SELECT * FROM sources_instagram_accounts");
  }
  function instagram_get_stats($account, $days = null){
    if($days === null) {
      $stats = DB::query("SELECT * FROM sources_instagram_data WHERE account = %s", $account);
    } else {
      $stats = DB::query("SELECT * FROM sources_instagram_data WHERE account = %s AND date > date_sub(curdate(), interval %i day)", $account, $days);
    }
    return $stats;
  }

  ///TWITTER
  function twitter_get_sites(){
    return DB::query("SELECT * FROM sources_twitter_sites");
  }
  function twitter_get_stats($id, $days = null){
    if($days === null) {
      $stats = DB::query("SELECT * FROM sources_twitter_data WHERE account = %s", $id);
    } else {
      $stats = DB::query("SELECT * FROM sources_twitter_data WHERE account = %s AND date > date_sub(curdate(), interval %i day)", $id, $days);
    }

    //Format the response for easier manipulation
    $response = array();
    foreach($stats as $row) {
      $response[$row['date']] = array(
        'totals' => array(
          'favs' => $row['favs'],
          'retweets' => $row['retweets'],
          'mentions' => $row['mentions'],
          'engagement' => $row['engagement'],
          'friends' => $row['friends'],
          'followers' => $row['followers']
        ),
        'posts' => json_decode($row['posts'])
      );
    }
    return $response;
  }
}


?>
