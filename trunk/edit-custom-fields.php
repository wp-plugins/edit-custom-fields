<?php
/*
Plugin Name: Edit Custom Fields
Plugin URI:
Description: A simple interface to edit or delete Custom Fields.
Version: 0.1.4
Author: Jay Sitter
Author URI: http://www.jaysitter.com/
License: GPL2
*/
 
add_action( 'admin_menu', 'ecf_menu' );
 
function ecf_menu() {
	add_submenu_page('tools.php','Edit Custom Fields', 'Edit Custom Fields', 'delete_others_posts', 'ecf-options', 'ecf_options');
}

function get_meta_key_from_id($id) {
	global $wpdb;
	$meta_id_matches = $wpdb->get_results( "SELECT meta_key FROM " . $wpdb->prefix . "postmeta WHERE meta_id = '" . $id . "'" );
	return $meta_id_matches[0]->meta_key;
}
 
function ecf_options() { // The options page
?>
 
    <div class="wrap">

				<?php global $wpdb; ?>

				<?php if (isset($_POST['submit']) || isset($_POST['delete']) || isset($_POST['rename'])) { // If the user has submitted an action to us ?>


					<?php if (isset($_POST['delete'])) { // If the user has confirmed a delete action ?>

						<?php if ($_POST['delete'] == 'confirm') {

							foreach ($_POST['checkbox'] as $key => $value) {

								$wpdb->delete( $wpdb->prefix . 'postmeta', array('meta_key' => $value) );

							}

							echo '<h2>Success! The custom fields have been deleted.</h2>';

						} else {

							echo '<h2>Something went wrong.</h2>';

						} ?>

					<?php } elseif (isset($_POST['rename'])) { ?>

						<?php if ($_POST['rename'] == 'confirm' || $_POST['rename'] == 'undo') {

							if ($_POST['rename'] == 'confirm') echo '<h2>Custom Field renaming complete</h2>';
							else echo '<h2>Renaming has been undone.</h2>';

							$success = FALSE;

							foreach ($_POST['old_values'] as $key => $value) {
								$existing = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '" . $value . "'" );
								if (count($existing) > 0) {
									echo '<p style="color:red">The Custom Field "' . $key . '" could not be renamed to "' . $value . '" because a Custom Field with that key already exists.</p>';
								} else {
									$wpdb->update($wpdb->prefix . 'postmeta',array('meta_key' => $value),array('meta_key' => $key));
									echo '<p>The Custom Field "' . $key . '" was renamed to "' . $value . '" . </p>';
									$success = TRUE;
								}
							}

							if ($success && $_POST['rename'] == 'confirm') { ?>
								<form method="post" action="">

								<input type="hidden" name="rename" value="undo" />

								<?php foreach ($_POST['old_values'] as $key => $value) {
									$existing = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_id = '" . $value . "'" );
									if (count($existing) > 0) {
									} else {
										// echo '<input type="hidden" name="' . $key . '" value="' . $previous_meta_key[$key] . '">';
										echo '<input type="hidden" name="old_values[', $value, ']" value="' . $key . '">';
										// echo '<input type="hidden" name="checkbox[]" value="' . $value . '"/>';
									}
								}

								submit_button('Undo','update');
							}

						} else { ?>

							<h2>Enter new Custom Field names</h2>

							<form method="post" action="">

								<table>

								<?php foreach ($_POST['checkbox'] as $key => $value) { ?>

										<tr>
										<td><label><?php echo $value; ?></label></td>
										<td><input name="old_values[<?php echo $value; ?>]" value="<?php echo $value; ?>"/></td>
										</tr>

								<?php } ?>

								</table>

								<input type="hidden" value="confirm" name="rename" />
								<?php submit_button('Rename','update'); ?>

							</form>

						<?php } ?>

					<?php } else { ?>

						<h2>Confirm Custom Field Deletion</h2>

						<p>The following custom fields will be <em><strong>IRREVOCABLY DELETED:</strong></em></p>

						<ul>

						<?php foreach ($_POST['checkbox'] as $key => $value) {

								echo '<li>', $value, '</li>';

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


						<?php foreach ($_POST['checkbox'] as $key => $value) {

							echo '<input type="hidden" name="checkbox[]" value="' . $value . '" />';
							echo '<tr><td colspan="2"><h3>',$value,'</h3></td></tr>';
							$rows = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '" . $value . "'" );
							foreach ($rows as $row) {
								echo '<tr>';
									echo '<td><a target="_blank" href="' . get_permalink($row->post_id) . '">',get_the_title($row->post_id),'</a></td>';
									echo '<td>',$row->meta_value,'</td>';
								echo '</tr>';
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

						$custom_fields = $wpdb->get_results( "SELECT distinct(`meta_key`) FROM " . $wpdb->prefix . "postmeta HAVING meta_key NOT LIKE '\_%'" );

					?>
	 
					<div>
					<form method="post" action="">
							<?php

							echo '<ul>';

							foreach ($custom_fields as $custom_field) {
								$cf_instances = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '" . $custom_field->meta_key . "'" );
								echo '<li>';
								echo '<input type="checkbox" id="cf_' . $custom_field->meta_key . '" name="checkbox[]" value="' . $custom_field->meta_key . '">';
								echo ' <label for="cf_' . $custom_field->meta_key . '">', $custom_field->meta_key,' (Used by ' . count($cf_instances) . ' posts)</label></li>';
							}

							echo '</ul>';

							?>
							<?php submit_button('Delete Checked Custom Fields', 'delete'); ?>
							<?php submit_button('Rename Checked Custom Fields', 'update', 'rename'); ?>
					</form>
					</div>

				<?php } ?>
 
    </div>
 
 
 
<?php }
 
 
 
 
?>
