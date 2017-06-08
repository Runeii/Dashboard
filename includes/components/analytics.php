<?php
Class Google_Data{
    public $data;
    private $analytics;
    private $credentials = __DIR__ . '/ga-credentials.json';

    function __construct($service = null){
      //Get data from DB if not service (ie, not updating)
      if($service === null) {
        $this->data = $this->get_GA_data();
      } else {
        $this->analytics = $this->initializeAnalytics();
        $this->reporting = $this->initializeAnalyticsReporting();
        $this->refresh_GA_data();
      }
    }
    //Retrieving from DB
    function get_GA_data(){

    }
    //Updating
    function refresh_GA_data(){
      $sites = $this->update_sites();

    }
    function update_sites(){
      $analytics = $this->analytics;
      $properties = $analytics->management_webproperties->listManagementWebproperties('~all')->getItems();
      $response = [];
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
      return $response;
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
