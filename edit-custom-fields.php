<?php
/*
Plugin Name: Edit Custom Fields
Plugin URI:
Description: A simple interface to edit or delete Custom Fields.
Version: 0.1.1
Author: Jay Sitter
Author URI: http://www.jaysitter.com/
License: GPL2
*/
 
add_action( 'admin_menu', 'ecf_menu' );
add_action('wp_head','include_ecf');
 
function ecf_menu() {
    add_submenu_page('tools.php','Edit Custom Fields', 'Edit Custom Fields', 'delete_others_posts', 'ecf-options', 'ecf_options');
}
 
 
 
function ecf_options() { // The options page
?>
 
    <div class="wrap">

				<?php global $wpdb; ?>

				<?php // print_r($_POST); ?>

				<?php if (isset($_POST['submit']) || isset($_POST['delete']) || isset($_POST['rename'])) { // If the user has submitted an action to us ?>


					<?php if (isset($_POST['delete'])) { // If the user has confirmed a delete action ?>

						<?php if ($_POST['delete'] == 'confirm') {

							foreach ($_POST as $key => $value) {

								if ($value == 'on') {
									$wpdb->delete( 'wp_postmeta', array('meta_key' => $key) );
								}

							}

							echo '<h2>Success! The custom fields have been deleted.</h2>';

						} else {

							echo '<h2>Something went wrong</h2>';

						} ?>

					<?php } elseif (isset($_POST['rename'])) { ?>

						<?php if ($_POST['rename'] == 'confirm' || $_POST['rename'] == 'undo') {

							if ($_POST['rename'] == 'confirm') echo '<h2>Custom Field renaming complete</h2>';
							else echo '<h2>Renaming has been undone.</h2>';

							$success = FALSE;

							foreach ($_POST as $key => $value) {
								if (!($key == 'rename' || $key == 'submit' || $value == '' || $value == $key)) {
									$existing = $wpdb->get_results( "SELECT * FROM wp_postmeta WHERE meta_key = '".$value."'" );
									if (count($existing) > 0) {
										echo '<p style="color:red">The Custom Field "'.$key.'" could not be renamed to "'.$value.'" because that Custom Field already exists.</p>';
									} else {
										$wpdb->update('wp_postmeta',array('meta_key' => $value),array('meta_key' => $key));
										echo '<p>The Custom Field "'.$key.'" was renamed to "'.$value.'".</p>';
										$success = TRUE;
									}
								}
							}

							if ($success && $_POST['rename'] == 'confirm') { ?>
								<form method="post" action="">

								<input type="hidden" name="rename" value="undo" />

								<?php foreach ($_POST as $key => $value) {
									if (!($key == 'rename' || $key == 'submit' || $value == '')) {
										$existing = $wpdb->get_results( "SELECT * FROM wp_postmeta WHERE meta_key = '".$key."'" );
										if (count($existing) > 0) {
										} else {
											echo '<input type="hidden" name="'.$value.'" value="'.$key.'"/>';
										}
									}
								}

								submit_button('Undo','update');
							}

						} else { ?>

							<h2>Enter new Custom Field names</h2>

							<form method="post" action="">

								<table>

								<?php foreach ($_POST as $key => $value) {

									if ($value == 'on') { ?>
										<tr>
										<td><label><?php echo $key; ?></label></td>
										<td><input name="<?php echo $key; ?>" value="<?php echo $key; ?>"/></td>
										</tr>
									<?php }

								} ?>

								</table>

								<input type="hidden" value="confirm" name="rename" />
								<?php submit_button('Rename','update'); ?>

							</form>

						<?php } ?>

					<?php } else { ?>
						
						<h2>Confirm Custom Field Deleteion</h2>

						<p>The following custom fields will be <em><strong>IRREVOCABLY DELETED:</strong></em></p>

						<ul>

						<?php foreach ($_POST as $key => $value) {

							if ($value == 'on') echo '<li>',$key,'</li>';

						} ?>

						</ul>

						<hr />

						<p>The following corresponding content will <em>also</em> be <em><strong>IRREVOCABLY DELETED:</strong></em></p>

						<form method="post" action="">

						<style>
						<!--
							table.ecf { border-collapse: collapse; }
							table.ecf td { padding: 0.5em; border: 1px solid #000; }
						-->
						</style>
						<table class="ecf">

						<tr>

						<th>Post Title</th><th>Custom Field Value</th>

						</tr>


						<?php foreach ($_POST as $key => $value) {

							if ($value == 'on') {
								echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
								echo '<tr><td colspan="2"><h3>',$key,'</h3></td></tr>';
								$rows = $wpdb->get_results( "SELECT * FROM wp_postmeta WHERE meta_key = '".$key."'" );
								foreach ($rows as $row) {
									echo '<tr>';
										echo '<td><a target="_blank" href="'.get_permalink($row->post_id).'">',get_the_title($row->post_id),'</a></td>';
										echo '<td>',$row->meta_value,'</td>';
									echo '</tr>';
								}
							}


						} ?>

						</table>

								<?php submit_button('Yes, DEFINITELY delete these custom fields','delete'); ?>
								<input type="hidden" value="confirm" name="delete" />
						</form>

					<?php } ?>




				<?php } else { // User hasn't submitted anything yet; show the list of checkboxes ?>
 
	 
					<h2>Edit custom fields</h2>

					<?php


					$myrows = $wpdb->get_results( "SELECT distinct(`meta_key`) FROM wp_postmeta HAVING meta_key NOT LIKE '\_%'" );

					?>

	 
					<div>
					<form method="post" action="">
							<?php

							echo '<ul>';

							foreach ($myrows as $myrow) {
								$cf_instances = $wpdb->get_results( "SELECT * FROM wp_postmeta WHERE meta_key = '".$myrow->meta_key."'" );
								echo '<li>';
								echo '<input type="checkbox" id="'.$myrow->meta_key.'" name="'.$myrow->meta_key.'">';
								echo ' <label for="'.$myrow->meta_key.'">',$myrow->meta_key,' (Used by '.count($cf_instances).' posts)</label></li>';
							}

							echo '</ul>';

							?>
							<?php submit_button('Delete Checked Custom Fields','delete'); ?>
							<?php submit_button('Rename Checked Custom Fields','update','rename'); ?>
					</form>
					</div>

				<?php } ?>
 
    </div>
 
 
 
<?php }
 
 
 
 
?>
