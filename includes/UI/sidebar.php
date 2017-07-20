<header>
  <img src="/assets/img/logo.png" class="logo"/>
  <ul class="actions">
    <li data-page="settings">
      <i class="fa fa-cogs" aria-hidden="true" data-navigate="settings" data-navtype="frame"></i>
    </li>
    <li data-action="logout">
      <i class="fa fa-sign-out" aria-hidden="true" data-navigate="logout" data-navtype="action"></i>
    </li>
  </ul>
</header>
<nav>
  <ul class="brands">
    <?php
      $brands = $BrandDB->get_brands();
      foreach($brands as $brand) {
        echo '<li data-client="true" data-id="'. $brand['id'] .'"><img src="'. $brand['logo'] .'" /></li>';
      }
    ?>
  </ul>
</nav>
