<?php
class DBAccess {

  function database_stat_pull($table, $filter, $value, $start = null, $end = null){
    if($start === null) {
      $stats = DB::query("SELECT * FROM ". $table ." WHERE ". $filter . " = %s", $value);
    } elseif($end == null) {
      $stats = DB::query("SELECT * FROM ". $table ." WHERE ". $filter . " = %s AND date > date_sub(curdate(), interval %i day)", $value, $start);
    } else {
      //Where account is X and date is more recent than Y and date is older than Z (ie, Y > date > Z)
      $stats = DB::query("SELECT * FROM ". $table ." WHERE ". $filter . " = %s AND date < date_sub(curdate(), interval %i day) AND date > date_sub(curdate(), interval %i day)", $value, $start, ($start + $end));
    }
    return $stats;
  }

  //ANALYTICS
  function analytics_get_sites($filter = null, $value = null) {
    if($filter === null) {
      return DB::query("SELECT * FROM sources_googleanalytics_sites");
    } else {
      return DB::query("SELECT * FROM sources_googleanalytics_sites WHERE ". $filter ."=%s", $value);
    }
  }


  function analytics_get_stats($id, $start = null, $end = null){
    $stats = $this->database_stat_pull('sources_googleanalytics_data', 'UAId', $id, $start, $end);
    return $stats;
  }

  ///FACEBOOK
  function facebook_get_pages(){
    return DB::query("SELECT * FROM sources_facebook_pages");
  }
  function facebook_get_stats($id, $start = null, $end = null){
    $stats = $this->database_stat_pull('sources_facebook_data', 'id', $id, $start, $end);
    return $stats;
  }

  ///INSTAGRAM
  function instagram_get_accounts(){
    return DB::query("SELECT * FROM sources_instagram_accounts");
  }
  function instagram_get_stats($account, $start = null, $end = null){
    $stats = $this->database_stat_pull('sources_instagram_data', 'account', $account, $start, $end);
    return $stats;
  }

  ///TWITTER
  function twitter_get_sites(){
    return DB::query("SELECT * FROM sources_twitter_sites");
  }
  function twitter_get_stats($id, $start = null, $end = null){
    $stats = $this->database_stat_pull('sources_twitter_data', 'account', $id, $start, $end);
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
