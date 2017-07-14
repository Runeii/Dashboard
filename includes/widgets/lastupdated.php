<?php
class LastUpdated extends GBWidget{

  function __construct(){
    parent::__construct();
    $this->outputWidget();
  }

  function outputWidget(){
    echo '<article id="last-updated">
      <h4>
        <i class="fa fa-clock-o" aria-hidden="true"></i>
        At a glance: '. $this->brand['name'] .'
      </h4>
    </article>';
  }

}
new LastUpdated();

?>
