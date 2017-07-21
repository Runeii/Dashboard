<?php include('../app/core.php'); ?>
<article class="settings-form">
  <table id="client_table" cellspacing="0">
      <tr>
          <th class="idholder">ID</th>
          <th>Name</th>
          <th>Logo</th>
          <th>Facebook</th>
          <th>Twitter</th>
          <th>Analytics</th>
          <th>Instagram</th>
      </tr>
      <tr class="hiddenrow">
        <td class="idholder"><input type="text" name="id" disabled /></td>
        <td><input type="text" name="name" /></td>
        <td><input type="text" name="logo" /></td>
        <td><?php echo facebook_options(); ?></td>
        <td><input type="text" name="twitter" /></td>
        <td><?php echo analytics_options(); ?></td>
        <td><input type="text" name="instagram" /></td>
      </tr>
      <?php
        $brands = $BrandDB->get_brands();
        $i = 0;
        foreach($brands as $brand) {
          echo '<tr class="inputrow">
                  <td class="idholder"><input type="text" name="id" value="'. $brand['id'] .'" disabled /></td>
                  <td><input type="text" name="name" value="'. $brand['name'] .'" /></td>
                  <td><input type="text" name="logo" value="'. $brand['logo'] .'" /></td>
                  <td>'. facebook_options($brand) .'</td>
                  <td><input type="text" name="twitter" value="'. $brand['twitter'] .'" /></td>
                  <td>'. analytics_options($brand) .'</td>
                  <td><input type="text" name="instagram" value="'. $brand['instagram'] .'" /></td>
                </tr>';
          $i++;
        }
      ?>
  </table>
  <input type="button" id="add_client_row" value="Add new brand" onclick="insert_row()"/>
  <input type="button" id="save_changes" value="Save Changes" onclick="save_changes()"/>
</article>

<?php
function facebook_options($current = array('facebook'=> '')){
  global $database;
  $facebook = $database->facebook_get_pages();
  $output = '<select name="facebook">';
  $output .= '<option value=""></option>';
  foreach($facebook as $page) {
    $selected = ($current['facebook'] == $page['id']) ? 'selected' : '';
    $output .= '<option value="'. $page['id'] .'" '. $selected .'>'. $page['name'] .'</option>';
  }
  $output .= '</td>';
  return $output;
}
function analytics_options($current = array('analytics'=> '')){
  global $database;
  $analytics = $database->analytics_get_sites();
  $output = '<select name="analytics">';
  $output .= '<option value=""></option>';
  foreach($analytics as $page) {
    $selected = ($current['analytics'] == $page['viewid']) ? 'selected' : '';
    $output .= '<option value="'. $page['viewid'] .'" '. $selected .'>'. $page['name'] .'</option>';
  }
  $output .= '</td>';
  return $output;
}
?>

<script type="text/javascript">
  function insert_row() {
    var x = document.querySelector('#client_table tbody');
    var rows = x.getElementsByTagName('tr').length - 2; //Number of rows, minus header and dummy rows
    var new_row_el = x.rows[1].cloneNode(true);

    // Set classes of tr and then iterate through input elements, updating offset in name
    new_row_el.classList.add('inputrow');
    new_row_el.classList.remove('hiddenrow');

    // Append
    x.appendChild( new_row_el );
  }
  function save_changes(){
    var rows = document.getElementById('client_table').querySelectorAll('.inputrow');
    console.log(rows);
    var data = [];
    for (var i=0, l=rows.length; i<l; i++) {
      data[i] = {};
      // Save input fields
      var inputs = rows[i].getElementsByTagName('input');
      for (var p=0, r=inputs.length; p<r; p++) {
        var name = inputs[p].name;
        data[i][name] = inputs[p].value;
      }
      //Save dropdown fields
      var selects = rows[i].getElementsByTagName('select');
      for (var p=0, r=selects.length; p<r; p++) {
        var name = selects[p].name;
        data[i][name] = selects[p].options[selects[p].selectedIndex].value;
      }
    }
    console.log(data);
    var request = {
      'action' : 'update_clients',
      'load' : data,
    };
    $.post( 'includes/connectors/ajax.php', request, function(response) {
      console.log(response);
    });
  }
</script>
