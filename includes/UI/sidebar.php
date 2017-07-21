<nav>
  <ul>
    <li class="logo">
      <img src="/assets/img/logo.png" />
    </li>
    <?php
      $brands = $BrandDB->get_brands();
      foreach($brands as $brand) {
        echo '<li data-client="true" data-id="'. $brand['id'] .'" class="brand"><img src="'. $brand['logo'] .'" />'. $brand['name'] .'</li>';
      }
    ?>
    <li data-page="settings" class="settings" data-navigate="settings" data-navtype="frame">
      <i class="fa fa-cogs" aria-hidden="true" ></i>Settings
    </li>
  </ul>
</nav>
