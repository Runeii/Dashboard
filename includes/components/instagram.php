<?php

class Instagram{
  private $token;
  private $brands;
  private $accounts;

  function __construct(){
    $this->token = '1332324521.068e5a0.9b6f62bb08774359a5c4bb53a574106a';
    $BrandDB = new Brands();
    $this->brands = $BrandDB->get_brand_profiles('instagram');
  }

  function calculate_engagement($likes, $comments, $followers){
    return (($likes + $comments) / $followers);
  }


  function refresh_data(){
    $sites = $this->update_accounts();
    $stats = $this->update_stats();
  }
  function update_accounts(){
    foreach($this->brands as $brand) {
      $user = json_decode(file_get_contents('https://www.instagram.com/'. $brand .'/?__a=1'))->user;
      if(is_object($user)) {
        $entry = array(
          'id' => $user->id,
          'username' => $user->username,
          'full_name' => $user->full_name,
          'profile_picture' => $user->profile_pic_url_hd,
          'bio' => $user->biography,
          'current_following' => $user->follows->count,
          'current_followers' => $user->followed_by->count
        );
        DB::replace('sources_instagram_accounts', $entry);
      }
    }
  }
  function update_stats(){
    global $database;
    $this->accounts = $database->instagram_get_accounts();
    foreach($this->accounts as $account) {
      $posts = json_decode(file_get_contents('https://www.instagram.com/'. $account['username'] .'/?__a=1'))->user->media->nodes;
      $offset = strtotime("yesterday");
      $day = array(
        'totals' => array(
          'likes' => 0,
          'comments' => 0,
          'engagement' => 0,
        )
      );
      foreach($posts as $post) {
        if($post->date > $offset) {
          $day['totals']['likes'] += $post->likes->count;
          $day['totals']['comments'] += $post->comments->count;
          $engagement = $this->calculate_engagement($post->likes->count, $post->comments->count, $account['current_followers']);
          $day[] = array(
            'id' => $post->id,
            'likes' => $post->likes->count,
            'engagement' => $engagement,
            'comments' => $post->comments->count,
            'link' => $post->code,
            'image' => $post->display_src
          );
        } else {
          break;
        }
      }
      $day['totals']['engagement'] = $this->calculate_engagement($day['totals']['likes'], $day['totals']['comments'], $account['current_followers']);
      //
      //To do: check quantity and recall from API if could be another set of posts from the week
      //
      $entry = array(
        'account' => $account['username'],
        'date' => date("Y-m-d", strtotime('today')),
        'likes' => $day['totals']['likes'],
        'comments' => $day['totals']['comments'],
        'engagement' => $day['totals']['engagement'],
        'fans' => $account['current_following']
      );
      unset($day['totals']);
      $entry['posts'] = json_encode($day);
      DB::replace('sources_instagram_data', $entry);
    }

  }
}

?>
