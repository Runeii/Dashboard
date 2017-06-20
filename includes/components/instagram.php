<?php 

class Instagram{
  private $token;
  private $brands;
  private $accounts;
  
  function __construct(){
    $this->token = '1332324521.068e5a0.9b6f62bb08774359a5c4bb53a574106a';
    $this->brands = array('HoochLemonBrew', 'crookedbevco');
  }
  function get_accounts(){
    $this->accounts = DB::query("SELECT * FROM sources_instagram_accounts");
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
      d($user);
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
  function update_stats(){
    $this->get_accounts();
    
    foreach($this->accounts as $account) {
      $posts = json_decode(file_get_contents('https://api.instagram.com/v1/users/'. $account['id'] .'/media/recent/?access_token='. $this->token));
      d($account);
      $offset = strtotime("-1 week");
      $week = array(
        'totals' => array(
          'likes' => 0,
          'comments' => 0,
          'engagement' => 0
        )
      );
      foreach($posts->data as $post) {
        if($post->created_time > $offset) {
          $week['totals']['likes'] += $post->likes->count;
          $week['totals']['comments'] += $post->comments->count;
          $engagement = $this->calculate_engagement($post->likes->count, $post->comments->count, $account['current_followers']);
          $week[] = array(
            'id' => $post->id,
            'likes' => $post->likes->count,
            'engagement' => $engagement,
            'comments' => $post->comments->count,
            'link' => $post->link,
            'images' => $post->images
          );
        } else {
          break;
        }
      }
      $week['totals']['engagement'] = $this->calculate_engagement($week['totals']['likes'], $week['totals']['comments'], $account['current_followers']);
      //
      //To do: check quantity and recall from API if could be another set of posts from the week
      //
      $entry = array(
        'account' => $account['username'],
        'date' => date("Y-m-d", strtotime('today')),
        'name' => 'posts',
        'value' => json_encode($week)
      );
      DB::replace('sources_instagram_data', $entry);
    }
    
  }
}

?>
