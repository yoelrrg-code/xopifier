<?php

/**
 * Plugin Name: Fix Another Update In Progress
 * Plugin URI:  http://wordpress.org/plugins/fix-another-update-in-progress
 * Description: A quick fix to WordPress another update is already in progress
 * Version: 2.0
 * Author: P. Roy
 * Author URI: https://www.proy.info
 * License: GPL v3
 * Text Domain: fix-another-update-in-progress
 **/

/**
 * Fix Another Update In Progress
 * Copyright (C) 2016, P. Roy - contact@proy.info
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

if (!class_exists('FixAnotherUpdate')) {
	class FixAnotherUpdate
	{

		var $nonce = 'fix-another-update-in-progress-options';

		function __construct()
		{
			//Actions
			add_action('admin_menu', array(&$this, 'admin_menu_link'));
		}

		function admin_menu_link()
		{
			add_options_page('Fix another update in progress', 'Fix Another Update In Progress', 'manage_options', basename(__FILE__), array(&$this, 'admin_options_page'));
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'filter_plugin_actions'), 10, 2);
		}

		function filter_plugin_actions($links, $file)
		{
			$settings_link = '<a href="options-general.php?page=' . basename(__FILE__) . '">' . __('Settings', 'fix-another-update-inprogress') . '</a>';
			array_unshift($links, $settings_link); // before other links

			return $links;
		}

		function admin_options_page()
		{

			if (isset($_POST['d2l_fix_another_update_save'])) {
				check_admin_referer($this->nonce);

				if (version_compare(get_bloginfo('version'), '4.5', '>=')) {
					delete_option('core_updater.lock');
					delete_option('auto_updater.lock');
				} else {
					delete_option('core_updater');
				}

				echo '<div class="updated"><p>' . __('Success! You\'ve  successfully fix "another update in progress!"', 'fix-another-update-inprogress') . '</p></div>';
			}

			if (version_compare(get_bloginfo('version'), '4.5', '>=')) {
				$check_coreupdate = get_option('core_updater.lock', null);
				$check_autoupdate = get_option('auto_updater.lock', null);
			} else {
				$check_coreupdate = get_option('core_updater', null);
			}
			?>

			<style>
				.txt-white {
					color: #fff;
				}

				.status-message {
					font-size: 18px;
					line-height: 1.5;
					margin: 0;
					font-weight: 700;
				}

				.wpcam_content_wrapper {
					display: table;
					table-layout: fixed;
					width: 100%;
				}

				#wpcam_content {
					width: 100%;
				}

				#wpcam_sidebar {
					padding-left: 20px;
					padding-right: 20px;
					width: 380px;
				}

				.wpcam_content_cell {
					display: table-cell;
					height: 500px;
					margin: 0;
					padding: 0;
					vertical-align: top;
				}

				.wpcam-sidebar__product {
					background: linear-gradient(to right top, #051937, #003f64, #006770, #008c52, #79a810);
					margin-top: 34px;
					height: 380px;
					padding-bottom: 40px;
					-webkit-box-shadow: 2px 2px 8px 0px rgba(0, 0, 0, 0.75);
					-moz-box-shadow: 2px 2px 8px 0px rgba(0, 0, 0, 0.75);
					box-shadow: 2px 2px 8px 0px rgba(0, 0, 0, 0.75);
				}

				.wpcam-sidebar__product_img {
					color: #fff;
					background: url(<?php echo plugin_dir_url( __FILE__ ).'wp-clean-admin-menu.png';?>) no-repeat;
					background-size: 104%;
					background-position: 0px 160px;
					width: auto;
					height: 100%;
					position: relative;
					overflow: hidden;
					padding: 20px;
				}

				.plugin-buy-button {
					position: absolute;
					bottom: 0;
				}

				.wpcam-button-upsell {
					align-items: center;
					background-color: #fec228;
					border-radius: 4px;
					box-shadow: inset 0 -4px 0 #0003;
					box-sizing: border-box;
					color: #000;
					display: inline-flex;
					filter: drop-shadow(0 2px 4px rgba(0, 0, 0, .2));
					font-family: Arial, sans-serif;
					font-size: 16px;
					justify-content: center;
					line-height: 1.5;
					min-height: 48px;
					padding: 8px 1em;
					text-decoration: none;
				}

				.content_box {
					padding: 20px;
					margin-top: 20px;
					border: 1px solid #c3c4c7;
					box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
					background: #fff;
				}

				@media screen and (max-width: 1024px) {

					table.table-menulist tr:first-child th:last-child {
						width: 100% !important;
					}

					.wpcam_content_cell,
					.wpcam_content_wrapper {
						display: block;
						height: auto;
					}

					#wpcam_sidebar {
						padding-left: 0;
						width: auto;
					}
				}
			</style>



			<div class="wpcam_content_wrapper">
				<div class="wpcam_content_cell" id="wpcam_content">
					<div class='wrap'>
						<h2>Fix Another Update In Progress</h2>
						<span class='description'><?php _e('A quick fix to WordPress another update is already in progress.', 'fix-another-update-in-progress'); ?></span>
						<div class="content_box">
							<form method='post' id='d2l_fix_another_update_inprogress_options'>
								<?php wp_nonce_field($this->nonce); ?>

								<?php
								if ($check_coreupdate != null || $check_autoupdate != null) {
									if ($check_coreupdate != null) {
										echo '<p></p><p class="status-message"> <i style="color: red" class="dashicons dashicons-lock"></i> WordPress Core Update is <span style="color: red">locked</span>.</p>';
									}

									if ($check_autoupdate != null) {
										echo '<p class="status-message"><i style="color: red" class="dashicons dashicons-lock"></i> WordPress Auto Update is <span style="color: red">locked</span>.</p>';
									}

									echo '<h3 style="color:#8c8f94">Lets get rid of the lock status, click the button below and its all done.</h3>';
								} else {
									echo '<p class="status-message" style="color: green; font-size:30px">GREAT! there is no update lock issue.</p>';
									echo '<p class="status-message" style="color: #8c8f94">Lets continue with the <a href="update-core.php">update</a> process...</p>';
								}
								?>
								<?php if ($check_coreupdate != null || $check_autoupdate != null) { ?>
									<p class='submit'><input style="font-size: 18px;" type='submit' value='Fix WordPress update Lock' name='d2l_fix_another_update_save' class='button-primary' /></p>
								<?php } ?>
							</form>
						</div>
					</div>
				</div>
				<div class="wpcam_content_cell" id="wpcam_sidebar">
					<div class="wpcam-sidebar__product">
						<div class="wpcam-sidebar__product_img">
							<h1 class="txt-white">WP Clean Admin Menu</h1>
							<p>Want to control more of your admin menu.</p>
							<p>This plugin helps to simplify WordPress admin-menu by hiding the rarely used menu items.</p>
							<p class="plugin-buy-button">
								<a class="wpcam-button-upsell" target="_blank" href="<?php echo esc_url( get_admin_url() );?>/plugin-install.php?s=clean%2520admin%2520menu&tab=search&type=tag">
									Get WP Clean Admin Menu
								</a>
							</p>
						</div>
					</div>
				</div>
			</div>
	        <?php
		}
	}
}

// instantiate the class
if (class_exists('FixAnotherUpdate')) {
	$d2lfix_update = new FixAnotherUpdate();
}
