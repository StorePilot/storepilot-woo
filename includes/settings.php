<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly
add_action('admin_menu', 'storepilot_menu');

function storepilot_menu() {
  add_submenu_page(
    'woocommerce',
    'StorePilot',
    'StorePilot',
    'manage_options',
    'storepilot',
    'storepilot_controller'
  );
}

function storepilot_controller() {

  $capabilities = json_decode(get_option('storepilot_capabilities'), true);
  if (!$capabilities) {
    $capabilities = [
      'default' => [
        'dashboard' => true,
        'products' => true,
        'customers' => true,
        'orders' => true,
        'pos' => true,
        'settings' => true
      ],
      'machines' => []
    ];
  }
  if (!$capabilities['default']) {
    $capabilities['default'] = [
      'dashboard' => true,
      'products' => true,
      'customers' => true,
      'orders' => true,
      'pos' => true,
      'settings' => true
    ];
  }

  if (isset($_POST['updates'])) {
    $updates = str_replace('\\', '', $_POST['updates']);
    $updates = json_decode($updates, true);
    foreach($updates['machines'] as $key => $val) {
      if ($key === 'Default') {
        $capabilities['default'] = $val['capabilities'];
      } else {
        $capabilities['machines'][$key] = $val;
        if (isset($val['capabilities']['remove']) && $val['capabilities']['remove']) {
          unset($capabilities['machines'][$key]);
        } else if (isset($val['capabilities']['remove'])) {
          unset($capabilities['machines'][$key]['remove']);
        }
      }
    }
    update_option('storepilot_capabilities', json_encode($capabilities));
  }
  $capabilities['machines']['Default'] = [
    'machine' => 'Default',
    'fingerprint' => '',
    'capabilities' => $capabilities['default']
  ];

  ?>

  <div class="wrap">

    <h1>StorePilot</h1>
    <h2>Capabilities management</h2>

    <table class="wp-list-table widefat fixed striped posts" cellspacing="0">
      <thead>
      <tr>
        <th class="manage-column" scope="col">
          Machine
        </th>
        <th class="manage-column" scope="col">
          Fingerprint
        </th>
        <th class="manage-column" scope="col">
          Dashboard
        </th>
        <th class="manage-column" scope="col">
          Products
        </th>
        <th class="manage-column" scope="col">
          Customers
        </th>
        <th class="manage-column" scope="col">
          Orders
        </th>
        <th class="manage-column" scope="col">
          POS
        </th>
        <th class="manage-column" scope="col">
          Settings
        </th>
        <th class="manage-column" scope="col">
          Remove
        </th>
      </tr>
      </thead>

      <tbody>
      <?php
		foreach($capabilities['machines'] as $key => $val) {
			if (!$capabilities['machines'][$key]['capabilities']) {
				$capabilities['machines'][$key]['capabilities'] = $capabilities['default'];
			}
	  ?>
      <?php if ($key === 'Default') { ?>
        <tr>
          <td>
            <span><?php echo $val['machine'] . (isset($val['platform']) ? ' (' . $val['platform'] . ')' : ''); ?></span>
          </td>
          <td>
            <?php if ($key !== 'Default') { ?>
              <input style="max-width: 100%" readonly value="<?php echo $key; ?>"/>
            <?php } ?>
          </td>
          <td>
            <select onchange="update(this, '<?php echo $key; ?>', 'dashboard')">
              <option value="false">Disabled</option>
              <option
                <?php echo $val['capabilities']['dashboard'] && $val['capabilities']['dashboard'] !== 'false' ? 'selected' : ''; ?>
                value="true">Enabled</option>
            </select>
          </td>
          <td>
            <select onchange="update(this, '<?php echo $key; ?>', 'products')">
              <option value="false">Disabled</option>
              <option
                <?php echo $val['capabilities']['products'] && $val['capabilities']['products'] !== 'false' ? 'selected' : ''; ?>
                value="true">Enabled</option>
            </select>
          </td>
          <td>
            <select onchange="update(this, '<?php echo $key; ?>', 'customers')">
              <option value="false">Disabled</option>
              <option
                <?php echo $val['capabilities']['customers'] && $val['capabilities']['customers'] !== 'false' ? 'selected' : ''; ?>
                value="true">Enabled</option>
            </select>
          </td>
          <td>
            <select onchange="update(this, '<?php echo $key; ?>', 'orders')">
              <option value="false">Disabled</option>
              <option
                <?php echo $val['capabilities']['orders'] && $val['capabilities']['orders'] !== 'false' ? 'selected' : ''; ?>
                value="true">Enabled</option>
            </select>
          </td>
          <td>
            <select onchange="update(this, '<?php echo $key; ?>', 'pos')">
              <option value="false">Disabled</option>
              <option
                <?php echo $val['capabilities']['pos'] && $val['capabilities']['pos'] !== 'false' ? 'selected' : ''; ?>
                value="true">Enabled</option>
            </select>
          </td>
          <td>
            <select onchange="update(this, '<?php echo $key; ?>', 'settings')">
              <option value="false">Disabled</option>
              <option
                <?php echo $val['capabilities']['settings'] && $val['capabilities']['settings'] !== 'false' ? 'selected' : ''; ?>
                value="true">Enabled</option>
            </select>
          </td>
          <td>
            <?php if ($key !== 'Default') { ?>
              <select onchange="update(this, '<?php echo $key; ?>', 'remove')">
                <option value="false">Keep</option>
                <option value="true">Remove</option>
              </select>
            <?php } ?>
          </td>
        </tr>
      <?php }} ?>
      <?php foreach($capabilities['machines'] as $key => $val) { ?>
      <?php if ($key !== 'Default') { ?>
        <tr>
          <td>
            <span><?php echo $val['machine'] . (isset($val['platform']) ? ' (' . $val['platform'] . ')' : ''); ?></span>
          </td>
          <td>
            <?php if ($key !== 'Default') { ?>
              <input style="max-width: 100%" readonly value="<?php echo $key; ?>"/>
            <?php } ?>
          </td>
          <td>
            <select onchange="update(this, '<?php echo $key; ?>', 'dashboard')">
              <option value="false">Disabled</option>
              <option
                <?php echo $val['capabilities']['dashboard'] && $val['capabilities']['dashboard'] !== 'false' ? 'selected' : ''; ?>
                value="true">Enabled</option>
            </select>
          </td>
          <td>
            <select onchange="update(this, '<?php echo $key; ?>', 'products')">
              <option value="false">Disabled</option>
              <option
                <?php echo $val['capabilities']['products'] && $val['capabilities']['products'] !== 'false' ? 'selected' : ''; ?>
                value="true">Enabled</option>
            </select>
          </td>
          <td>
            <select onchange="update(this, '<?php echo $key; ?>', 'customers')">
              <option value="false">Disabled</option>
              <option
                <?php echo $val['capabilities']['customers'] && $val['capabilities']['customers'] !== 'false' ? 'selected' : ''; ?>
                value="true">Enabled</option>
            </select>
          </td>
          <td>
            <select onchange="update(this, '<?php echo $key; ?>', 'orders')">
              <option value="false">Disabled</option>
              <option
                <?php echo $val['capabilities']['orders'] && $val['capabilities']['orders'] !== 'false' ? 'selected' : ''; ?>
                value="true">Enabled</option>
            </select>
          </td>
          <td>
            <select onchange="update(this, '<?php echo $key; ?>', 'pos')">
              <option value="false">Disabled</option>
              <option
                <?php echo $val['capabilities']['pos'] && $val['capabilities']['pos'] !== 'false' ? 'selected' : ''; ?>
                value="true">Enabled</option>
            </select>
          </td>
          <td>
            <select onchange="update(this, '<?php echo $key; ?>', 'settings')">
              <option value="false">Disabled</option>
              <option
                <?php echo $val['capabilities']['settings'] && $val['capabilities']['settings'] !== 'false' ? 'selected' : ''; ?>
                value="true">Enabled</option>
            </select>
          </td>
          <td>
            <?php if ($key !== 'Default') { ?>
              <select onchange="update(this, '<?php echo $key; ?>', 'remove')">
                <option value="false">Keep</option>
                <option value="true">Remove</option>
              </select>
            <?php } ?>
          </td>
        </tr>
      <?php }} ?>
      </tbody>
    </table>

    <form action="" method="post">
      <input id="updates" type="hidden" name="updates" value='<?php echo json_encode($capabilities); ?>'>
      <button style="float: right; margin-top: 10px" class="button button-primary" type="submit">
        <?php echo __('Update', 'wordpress') ?>
      </button>
    </form>

  </div>

  <script>
    var hook = document.getElementById('updates')
    var capabilities = JSON.parse(hook.value)
    function update(e, id, key) {
      var v = e.value
      if (v === 'true') v = true
      if (v === 'false') v = false      
      Object.keys(capabilities.machines).forEach(function(k) {
        if (id === k) {
          capabilities.machines[id].capabilities[key] = e.value
        }
      })
      hook.value = JSON.stringify(capabilities)
    }
  </script>

  <?php
}