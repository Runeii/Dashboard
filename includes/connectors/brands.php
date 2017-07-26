<?php

class Brands {
  function update_brands($data = null) {
    if(is_array($data)) {
      foreach($data as $brand) {
        //If ID isn't set, row doesn't already exist so autoincrement
        if($brand['action'] == 'delete') {
          DB::delete('brands', "id=%s", $brand['id']);
        } else {
          unset($brand['action']);
          if($brand['id'] == '') {
            unset($brand['id']);
          }
          DB::replace('brands', $brand);
        }
      }
    }
    return DB::insertId();
  }
  function get_brands($id = null) {
    if($id === null) {
      return DB::query("SELECT * FROM brands", $id);
    } else {
      return DB::query("SELECT * FROM brands WHERE id=%s", $id);
    }
  }
  function get_brand_profiles($network) {
    $profiles = DB::queryOneColumn($network, "SELECT * FROM brands");
    $response = array();
    foreach($profiles as $profile) {
      if($profile != '') {
        $response[] = $profile;
      }
    }
    return $response;
  }
}

?>
