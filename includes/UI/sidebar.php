<nav>
  <div class="logo">
    <img src="/assets/img/logo.png" />
  </div>
  <ul>
    <?php
      $brands = $BrandDB->get_brands();
      foreach($brands as $brand) {
        echo '<li data-client="true" data-id="'. $brand['id'] .'" class="brand"><img src="'. $brand['logo'] .'" />'. $brand['name'] .'</li>';
      }
    ?>
    <li data-page="settings" class="settings" data-navigate="settings">
      <i class="fa fa-cogs" aria-hidden="true" ></i>Settings
    </li>
  </ul>
</nav>
