<?php
/**
 * Media from FTP
 * 
 * @package    Media from FTP
 * @subpackage MediafromFTPAdmin Main & Management screen
/*  Copyright (c) 2013- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class MediaFromFtpAdmin {

	/* ==================================================
	 * Add a "Settings" link to the plugins page
	 * @since	1.0
	 */
	function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty($this_plugin) ) {
			$this_plugin = MEDIAFROMFTP_PLUGIN_BASE_FILE;
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="'.admin_url('admin.php?page=mediafromftp').'">Media from FTP</a>';
			$links[] = '<a href="'.admin_url('admin.php?page=mediafromftp-settings').'">'.__( 'Settings').'</a>';
			$links[] = '<a href="'.admin_url('admin.php?page=mediafromftp-search-register').'">'.__('Search & Register', 'media-from-ftp').'</a>';
			$links[] = '<a href="'.admin_url('admin.php?page=mediafromftp-import').'">'.__('Import').'</a>';
			$links[] = '<a href="'.admin_url('admin.php?page=mediafromftp-log').'">'.__('Log', 'media-from-ftp').'</a>';
		}
			return $links;
	}

	/* ==================================================
	 * Settings page
	 * @since	1.0
	 */
	function add_pages() {
		add_menu_page(
				'Media from FTP',
				'Media from FTP',
				'upload_files',
				'mediafromftp',
				array($this, 'manage_page'),
				'dashicons-upload'
		);
		add_submenu_page(
				'mediafromftp',
				__('Settings'),
				__('Settings'),
				'upload_files',
				'mediafromftp-settings',
				array($this, 'settings_page')
		);
		add_submenu_page(
				'mediafromftp',
				__('Search & Register', 'media-from-ftp'),
				__('Search & Register', 'media-from-ftp'),
				'upload_files',
				'mediafromftp-search-register',
				array($this, 'search_register_page')
		);
		add_submenu_page(
				'mediafromftp',
				__('Log', 'media-from-ftp'),
				__('Log', 'media-from-ftp'),
				'upload_files',
				'mediafromftp-log',
				array($this, 'log_page')
		);
		add_submenu_page(
				'mediafromftp',
				__('Import'),
				__('Import'),
				'upload_files',
				'mediafromftp-import',
				array($this, 'medialibrary_import_page')
		);
	}

	/* ==================================================
	 * Add Css and Script
	 * @since	2.23
	 */
	function load_custom_wp_admin_style() {
		if ($this->is_my_plugin_screen()) {
			wp_enqueue_style( 'jquery-datetimepicker', MEDIAFROMFTP_PLUGIN_URL.'/css/jquery.datetimepicker.css' );
			wp_enqueue_style( 'jquery-responsiveTabs', MEDIAFROMFTP_PLUGIN_URL.'/css/responsive-tabs.css' );
			wp_enqueue_style( 'jquery-responsiveTabs-style', MEDIAFROMFTP_PLUGIN_URL.'/css/style.css' );
			wp_enqueue_style( 'stacktable', MEDIAFROMFTP_PLUGIN_URL.'/css/stacktable.css' );
			wp_enqueue_style( 'mediafromftp',  MEDIAFROMFTP_PLUGIN_URL.'/css/mediafromftp.css' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-datetimepicker', MEDIAFROMFTP_PLUGIN_URL.'/js/jquery.datetimepicker.js', null, '2.3.4' );
			wp_enqueue_script( 'jquery-responsiveTabs', MEDIAFROMFTP_PLUGIN_URL.'/js/jquery.responsiveTabs.min.js' );
			wp_enqueue_script( 'stacktable', MEDIAFROMFTP_PLUGIN_URL.'/js/stacktable.js' );
			wp_enqueue_script( 'mediafromftp-js', MEDIAFROMFTP_PLUGIN_URL.'/js/jquery.mediafromftp.js', array('jquery') );
			wp_localize_script( 'mediafromftp-js', 'MEDIAFROMFTPJS', array('ajax_url' => admin_url('admin-ajax.php')));
		}
	}

	/* ==================================================
	 * Add Script on footer
	 * @since	1.0
	 */
	function load_custom_wp_admin_style2() {
		if ($this->is_my_plugin_screen2()) {
			if ( !empty($_POST['mediafromftp_select_author']) && !empty($_POST['mediafromftp_xml_file']) ) {

				if ( is_file($_POST['mediafromftp_xml_file']) ) {
					$select_author = array();
					foreach (array_keys($_POST) as $key) {
						if ( $key === 'select_author' || $key === 'mediafromftp_select_author' || $key === 'mediafromftp_xml_file' ) {	// skip
						} else {
							if ( $_POST[$key] <> -1 ) {
								$select_author[$key] = $_POST[$key];
							}
						}
					}
					$filename = $_POST['mediafromftp_xml_file'];
					include_once MEDIAFROMFTP_PLUGIN_BASE_DIR.'/inc/MediaFromFtp.php';
					$mediafromftp = new MediaFromFtp();
					echo $mediafromftp->make_object($filename, $select_author);
					unset($mediafromftp);
					unlink($filename);
				}
			}
		}
	}

	/* ==================================================
	 * For only admin style
	 * @since	8.82
	 */
	function is_my_plugin_screen() {
		$screen = get_current_screen();
		if (is_object($screen) && $screen->id == 'toplevel_page_mediafromftp') {
			return TRUE;
		} else if (is_object($screen) && $screen->id == 'media-from-ftp_page_mediafromftp-settings') {
			return TRUE;
		} else if (is_object($screen) && $screen->id == 'media-from-ftp_page_mediafromftp-search-register') {
			return TRUE;
		} else if (is_object($screen) && $screen->id == 'media-from-ftp_page_mediafromftp-log') {
			return TRUE;
		} else if (is_object($screen) && $screen->id == 'media-from-ftp_page_mediafromftp-import') {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/* ==================================================
	 * For only admin style
	 * @since	8.82
	 */
	function is_my_plugin_screen2() {
		$screen = get_current_screen();
		if (is_object($screen) && $screen->id == 'media-from-ftp_page_mediafromftp-import') {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/* ==================================================
	 * Main
	 */
	function manage_page() {

		if ( !current_user_can( 'upload_files' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$plugin_datas = get_file_data( MEDIAFROMFTP_PLUGIN_BASE_DIR.'/mediafromftp.php', array('version' => 'Version') );
		$plugin_version = __('Version:').' '.$plugin_datas['version'];

		?>

		<div class="wrap">

		<h2 style="float: left;">Media from FTP</h2>
		<div style="display: block; padding: 10px 10px;">
			<form method="post" style="float: left; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=mediafromftp-settings'); ?>">
				<input type="submit" class="button" value="<?php _e('Settings'); ?>" />
			</form>
			<form method="post" style="float: left; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=mediafromftp-search-register'); ?>" />
				<input type="submit" class="button" value="<?php _e('Search & Register', 'media-from-ftp'); ?>" />
			</form>
			<form method="post" style="float: left; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=mediafromftp-log'); ?>" />
				<input type="submit" class="button" value="<?php _e('Log', 'media-from-ftp'); ?>" />
			</form>
			<form method="post" action="<?php echo admin_url('admin.php?page=mediafromftp-import'); ?>" />
				<input type="submit" class="button" value="<?php _e('Import'); ?>" />
			</form>
		</div>
		<div style="clear: both;"></div>

		<h3><?php _e('Register to media library from files that have been uploaded by FTP.', 'media-from-ftp'); ?></h3>
		<h4 style="margin: 5px; padding: 5px;">
		<?php echo $plugin_version; ?> |
		<a style="text-decoration: none;" href="<?php _e('https://wordpress.org/plugins/media-from-ftp/faq', 'media-from-ftp'); ?>" target="_blank">FAQ</a> |<a style="text-decoration: none;" href="https://wordpress.org/support/plugin/media-from-ftp" target="_blank"><?php _e('Support Forums') ?></a> |
		<a style="text-decoration: none;" href="https://wordpress.org/support/view/plugin-reviews/media-from-ftp" target="_blank"><?php _e('Reviews', 'media-from-ftp') ?></a>
		</h4>

		<div style="width: 250px; height: 180px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
		<h3><?php _e('Please make a donation if you like my work or would like to further the development of this plugin.', 'media-from-ftp'); ?></h3>
		<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
<a style="margin: 5px; padding: 5px;" href='https://pledgie.com/campaigns/28307' target="_blank"><img alt='Click here to lend your support to: Various Plugins for WordPress and make a donation at pledgie.com !' src='https://pledgie.com/campaigns/28307.png?skin_name=chrome' border='0' ></a>
		</div>

		</div>
		<?php

	}

	/* ==================================================
	 * Sub Menu
	 */
	function settings_page() {

		if ( !current_user_can( 'upload_files' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$submenu = 1;
		$this->options_updated($submenu);

		$def_max_execution_time = ini_get('max_execution_time');
		$scriptname = admin_url('admin.php?page=mediafromftp-settings');
		$mediafromftp_settings = get_option($this->wp_options_name());

		?>

		<div class="wrap">

		<h2>Media from FTP <?php _e('Settings'); ?>
			<form method="post" style="float: right;" action="<?php echo admin_url('admin.php?page=mediafromftp-import'); ?>" />
				<input type="submit" class="button" value="<?php _e('Import'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 0.5em;" action="<?php echo admin_url('admin.php?page=mediafromftp-log'); ?>" />
				<input type="submit" class="button" value="<?php _e('Log', 'media-from-ftp'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 0.5em;" action="<?php echo admin_url('admin.php?page=mediafromftp-search-register'); ?>" />
				<input type="submit" class="button" value="<?php _e('Search & Register', 'media-from-ftp'); ?>" />
			</form>
		</h2>
		<div style="clear: both;"></div>

		<div id="mediafromftp-settings-tabs">
			<ul>
			<li><a href="#mediafromftp-settings-tabs-1"><?php _e('Settings'); ?></a></li>
			<li><a href="#mediafromftp-settings-tabs-2"><?php _e('Command-line', 'media-from-ftp'); ?></a></li>
			</ul>

			<div id="mediafromftp-settings-tabs-1">
			<div style="display: block; padding: 5px 15px">
			<form method="post" action="<?php echo $scriptname; ?>">

				<div class="item-mediafromftp-settings">
					<h3><?php _e('Execution time', 'media-from-ftp'); ?></h3>
					<div style="display:block; padding:5px 5px">
					<?php _e('This is to suppress the timeout when retrieving a large amount of data when displaying the search screen and log screen.', 'media-from-ftp'); ?>
					<?php _e('It does not matter on the registration screen.', 'media-from-ftp'); ?>
						<?php
							$max_execution_time = $mediafromftp_settings['max_execution_time'];
							if ( !@set_time_limit($max_execution_time) ) {
								$limit_seconds_html =  '<font color="red">'.$def_max_execution_time.__('seconds', 'media-from-ftp').'</font>';
								$command_line_html = '<a href="'.admin_url('admin.php?page=mediafromftp-settings#mediafromftp-settings-tabs-2').'">'.__('Command-line', 'media-from-ftp').'</a>';
								?>
								<?php echo sprintf(__('Execution time for this server is fixed at %1$s. Please note the "Number of files to be registered" or "Number of items per page" so as not to exceed this limit. Please use the %2$s if you do not want to be bound by this restriction.', 'media-from-ftp'), $limit_seconds_html, $command_line_html); ?>
								<input type="hidden" name="mediafromftp_max_execution_time" value="<?php echo $def_max_execution_time; ?>" />
							<?php
							} else {
								$max_execution_time_text = __('The number of seconds a script is allowed to run.', 'media-from-ftp').'('.__('The max_execution_time value defined in the php.ini.', 'media-from-ftp').'[<font color="red">'.$def_max_execution_time.'</font>]'.')';
								?>
								<div style="float: left;"><?php echo $max_execution_time_text; ?>:<input type="text" name="mediafromftp_max_execution_time" value="<?php echo $max_execution_time; ?>" size="3" /></div>
							<?php
							}
						?>
					</div>
					<div style="clear: both;"></div>
				</div>

				<?php
				if ( function_exists('mb_check_encoding') ) {
				?>
				<div class="item-mediafromftp-settings">
					<h3><?php _e('Character Encodings for Server', 'media-from-ftp'); ?></h3>
					<p>
					<?php _e('It may fail to register if you are using a multi-byte name in the file name or folder name. In that case, please change.', 'media-from-ftp');
					$characterencodings_none_html = '<a href="'.__('https://en.wikipedia.org/wiki/Variable-width_encoding', 'media-from-ftp').'" target="_blank" style="text-decoration: none; word-break: break-all;">'.__('variable-width encoding', 'media-from-ftp').'</a>';
					echo sprintf(__('If you do not use the filename or directory name of %1$s, please choose "%2$s".','media-from-ftp'), $characterencodings_none_html, '<font color="red">none</font>');
					?>
					</p>
					<select name="mediafromftp_character_code" style="width: 210px">
					<?php
					if ( 'none' === $mediafromftp_settings['character_code'] ) {
						?>
						<option value="none" selected>none</option>
						<?php
					} else {
						?>
						<option value="none">none</option>
						<?php
					}
					foreach (mb_list_encodings() as $chrcode) {
						if ( $chrcode <> 'pass' && $chrcode <> 'auto' ) {
							if ( $chrcode === $mediafromftp_settings['character_code'] ) {
								?>
								<option value="<?php echo $chrcode; ?>" selected><?php echo $chrcode; ?></option>
								<?php
							} else {
								?>
								<option value="<?php echo $chrcode; ?>"><?php echo $chrcode; ?></option>
								<?php
							}
						}
					}
					?>
					</select>
					<div style="clear: both;"></div>
				</div>
				<?php
				}
				?>

				<div class="item-mediafromftp-settings">
					<h3><?php _e('Display of search results', 'media-from-ftp'); ?></h3>
					<div style="display: block;padding:5px 5px">
					<input type="radio" name="search_display_metadata" value="1" <?php if ($mediafromftp_settings['search_display_metadata'] == TRUE) echo 'checked'; ?>>
					<?php _e('Usual selection. It is user-friendly. It displays a thumbnail and metadata. It is low speed.', 'media-from-ftp'); ?>
					</div>
					<div style="display: block;padding:5px 5px">
					<input type="radio" name="search_display_metadata" value="0" <?php if ($mediafromftp_settings['search_display_metadata'] == FALSE) echo 'checked'; ?>>
					<?php _e('Unusual selection. Only the file name and output. It is suitable for the search of large amounts of data. It is hi speed.', 'media-from-ftp'); ?>
					</div>
					<div style="clear: both;"></div>
				</div>

				<div class="item-mediafromftp-settings">
					<h3><?php _e('Date'); ?></h3>
					<div style="display: block;padding:5px 5px">
					<input type="radio" name="mediafromftp_dateset" value="new" <?php if ($mediafromftp_settings['dateset'] === 'new') echo 'checked'; ?>>
					<?php _e('Update to use of the current date/time.', 'media-from-ftp'); ?>
					</div>
					<div style="display: block;padding:5px 5px">
					<input type="radio" name="mediafromftp_dateset" value="server" <?php if ($mediafromftp_settings['dateset'] === 'server') echo 'checked'; ?>>
					<?php _e('Get the date/time of the file, and updated based on it. Change it if necessary.', 'media-from-ftp'); ?>
					</div>
					<div style="display: block; padding:5px 5px">
					<input type="radio" name="mediafromftp_dateset" value="exif" <?php if ($mediafromftp_settings['dateset'] === 'exif') echo 'checked'; ?>>
					<?php
					_e('Get the date/time of the file, and updated based on it. Change it if necessary.', 'media-from-ftp');
					_e('Get by priority if there is date and time of the Exif information.', 'media-from-ftp');
					?>
					</div>
					<div style="display: block; padding:5px 5px">
					<?php
					if ( current_user_can('manage_options') ) {
						?>
						<input type="checkbox" name="move_yearmonth_folders" value="1" <?php checked('1', get_option('uploads_use_yearmonth_folders')); ?> />
						<?php
					} else {
						?>
						<input type="checkbox" disabled="disabled" value="1" <?php checked('1', get_option('uploads_use_yearmonth_folders')); ?> />
						<input type="hidden" name="move_yearmonth_folders" value="<?php echo get_option('uploads_use_yearmonth_folders'); ?>">
						<?php
					}
					_e('Organize my uploads into month- and year-based folders');
					?>
					</div>
				</div>

				<div class="item-mediafromftp-settings">
					<h3>Exif <?php _e('Caption'); ?></h3>
					<div style="display:block;padding:5px 0">
					<?php _e('Register the Exif data to the caption.', 'media-from-ftp'); ?>
					</div>
					<div style="display:block;padding:5px 0">
					<input type="checkbox" name="mediafromftp_caption_apply" value="1" <?php checked('1', $mediafromftp_settings['caption']['apply']); ?> />
					<?php _e('Apply'); ?>
					</div>
					<div style="display: block; padding:5px 20px;">
						Exif <?php _e('Tags'); ?>
						<input type="submit" style="position:relative; top:-5px;" class="button" name="mediafromftp_exif_default" value="<?php _e('Default') ?>" />
						<div style="display: block; padding:5px 20px;">
						<textarea name="mediafromftp_exif_text" style="width: 100%;"><?php echo $mediafromftp_settings['caption']['exif_text']; ?></textarea>
							<div>
							<a href="https://codex.wordpress.org/Function_Reference/wp_read_image_metadata#Return%20Values" target="_blank" style="text-decoration: none; word-break: break-all;"><?php _e('For Exif tags, please read here.', 'media-from-ftp'); ?></a>
							</div>
						</div>
					</div>
					<div>
					<?php
						if ( is_multisite() ) {
							$exifcaption_install_url = network_admin_url('plugin-install.php?tab=plugin-information&plugin=exif-caption');
						} else {
							$exifcaption_install_url = admin_url('plugin-install.php?tab=plugin-information&plugin=exif-caption');
						}
						$exifcaption_install_html = '<a href="'.$exifcaption_install_url.'" target="_blank" style="text-decoration: none; word-break: break-all;">Exif Caption</a>';
						echo sprintf(__('If you want to insert the Exif in the media that have already been registered in the media library, Please use the %1$s.','media-from-ftp'), $exifcaption_install_html);
					?>
					</div>
				</div>

				<div class="item-mediafromftp-settings">
					<h3><?php _e('Exclude file', 'media-from-ftp'); ?></h3>
					<p><?php _e('Regular expression is possible.', 'media-from-ftp'); ?></p>
					<textarea id="mediafromftp_exclude" name="mediafromftp_exclude" rows="3" style="width: 100%;"><?php echo $mediafromftp_settings['exclude']; ?></textarea>
					<div style="clear: both;"></div>
				</div>

				<div class="item-mediafromftp-settings">
					<h3><?php _e('Search method for the exclusion of the thumbnail', 'media-from-ftp'); ?></h3>
					<div style="display: block;padding:5px 5px">
					<input type="radio" name="mediafromftp_thumb_deep_search" value="0" <?php if ($mediafromftp_settings['thumb_deep_search'] == FALSE) echo 'checked'; ?>>
					<?php _e('Usual selection. It is hi speed.', 'media-from-ftp'); ?>
					</div>
					<div style="display: block;padding:5px 5px">
					<input type="radio" name="mediafromftp_thumb_deep_search" value="1" <?php if ($mediafromftp_settings['thumb_deep_search'] == TRUE) echo 'checked'; ?>>
					<?php _e('Unusual selection. if you want to search for filename that contains such -0x0. It is low speed.', 'media-from-ftp'); ?>
					</div>
					<div style="clear: both;"></div>
				</div>

				<div class="item-mediafromftp-settings">
					<h3><?php _e('Schedule', 'media-from-ftp'); ?></h3>
					<div style="display:block;padding:5px 0">
					<?php _e('Set the schedule.', 'media-from-ftp'); ?>
					<?php _e('Will take some time until the [Next Schedule] is reflected.', 'media-from-ftp'); ?>
					</div>
					<?php
					if ( wp_next_scheduled( 'MediaFromFtpCronHook' ) ) {
						$next_schedule = ' '.get_date_from_gmt(date("Y-m-d H:i:s", wp_next_scheduled( 'MediaFromFtpCronHook' )));
					} else {
						$next_schedule = ' '.__('None');
					}
					?>
					<div style="display:block;padding:5px 0">
					<?php echo __('Next Schedule:', 'media-from-ftp').$next_schedule; ?>
					</div>
					<div style="display:block;padding:5px 0">
					<input type="checkbox" name="mediafromftp_cron_apply" value="1" <?php checked('1', $mediafromftp_settings['cron']['apply']); ?> />
					<?php _e('Apply Schedule', 'media-from-ftp'); ?>
					</div>
					<div style="display:block;padding:5px 10px">
					<input type="radio" name="mediafromftp_cron_schedule" value="hourly" <?php checked('hourly', $mediafromftp_settings['cron']['schedule']); ?>>
					<?php _e('hourly', 'media-from-ftp'); ?>
					</div>
					<div style="display:block;padding:5px 10px">
					<input type="radio" name="mediafromftp_cron_schedule" value="twicedaily" <?php checked('twicedaily', $mediafromftp_settings['cron']['schedule']); ?>>
					<?php _e('twice daily', 'media-from-ftp'); ?>
					</div>
					<div style="display:block;padding:5px 10px">
					<input type="radio" name="mediafromftp_cron_schedule" value="daily" <?php checked('daily', $mediafromftp_settings['cron']['schedule']); ?>>
					<?php _e('daily', 'media-from-ftp'); ?>
					</div>
					<div style="display:block;padding:5px 10px">
					<input type="checkbox" name="mediafromftp_cron_limit_number" value="1" <?php checked('1', $mediafromftp_settings['cron']['limit_number']); ?> />
					<?php _e('Apply limit number of update files.', 'media-from-ftp'); ?>
					</div>
					<div style="display:block;padding:5px 20px">
						<?php echo __('Limit number of update files', 'media-from-ftp').': '.$mediafromftp_settings['pagemax']; ?>
						= <a href="<?php echo admin_url('admin.php?page=mediafromftp-search-register'); ?>"><?php _e('Number of items per page:'); ?></a>
					</div>
					<div style="display:block;padding:5px 10px">
					<input type="checkbox" name="mediafromftp_cron_mail_apply" value="1" <?php checked('1', $mediafromftp_settings['cron']['mail_apply']); ?> />
					<?php _e('Email me whenever'); ?>
					</div>
					<div style="display:block;padding:5px 20px">
						<?php echo __('Your Email').': '.$mediafromftp_settings['cron']['mail']; ?>
					</div>
				</div>

				<div class="item-mediafromftp-settings">
					<h3><?php _e('Log', 'media-from-ftp'); ?></h3>
					<div style="display:block;padding:5px 0">
					<?php _e('Record the registration result.', 'media-from-ftp'); ?>
					</div>
					<div style="display:block;padding:5px 0">
					<input type="checkbox" name="mediafromftp_apply_log" value="1" <?php checked('1', $mediafromftp_settings['log']); ?> />
					<?php _e('Create log', 'media-from-ftp'); ?>
					</div>
				</div>

				<div class="item-mediafromftp-settings">
					<h3><?php _e('Remove Thumbnails Cache', 'media-from-ftp'); ?></h3>
					<div style="display:block;padding:5px 0">
						<?php _e('Remove the cache of thumbnail used in the search screen. Please try out if trouble occurs in the search screen. It might become normal.', 'media-from-ftp'); ?>
					</div>
				</div>

				<div style="clear: both;"></div>
				<p>
				<div>
					<input type="submit" class="button" style="float: left; margin-right: 1em;" value="<?php _e('Save Changes'); ?>" />
				</div>
			</form>

			<form method="post" action="<?php echo $scriptname; ?>" />
				<input type="hidden" name="mediafromftp_clear_cash" value="1" />
				<div>
				<input type="submit" class="button" value="<?php _e('Remove Thumbnails Cache', 'media-from-ftp'); ?>" />
				</div>
			</form>

			</div>
			</div>

			<div id="mediafromftp-settings-tabs-2">
				<h3><?php _e('Command-line', 'media-from-ftp'); ?></h3>
				<div style="display:block; padding:5px 10px; font-weight: bold;">
				1. <?php _e('Please [mediafromftpcmd.php] rewrite the following manner.', 'media-from-ftp'); ?>
				</div>
				<div style="display:block;padding:5px 20px">
				<?php
				$commandline_host = $_SERVER['HTTP_HOST'];
				$commandline_server = $_SERVER['SERVER_NAME'];
				$commandline_uri = untrailingslashit(wp_make_link_relative(MEDIAFROMFTP_PLUGIN_SITE_URL));
				$commandline_wpload = wp_normalize_path(ABSPATH).'wp-load.php';
				$commandline_pg = wp_normalize_path(MEDIAFROMFTP_PLUGIN_BASE_DIR.'/mediafromftpcmd.php');
				$commandline_wget = MEDIAFROMFTP_PLUGIN_URL.'/mediafromftpcmd.php';
$commandline_set = <<<COMMANDLINESET

&#x24_SERVER = array(
"HTTP_HOST" => "$commandline_host",
"SERVER_NAME" => "$commandline_server",
"REQUEST_URI" => "$commandline_uri",
"REQUEST_METHOD" => "GET",
"HTTP_USER_AGENT" => "mediafromftp"
            );
require_once('$commandline_wpload');

COMMANDLINESET;

				$commandline_wp_options_name = $this->wp_options_name();
$commandline_set2 = <<<COMMANDLINESET2

	&#x24mediafromftpcron->CronDo('$commandline_wp_options_name');

COMMANDLINESET2;

				?>
				<?php echo sprintf(__('The line %2$d from line %1$d.', 'media-from-ftp'), 56, 63); ?>
				<textarea readonly rows="9" style="font-size: 12px; width: 100%;">
				<?php echo $commandline_set; ?>
				</textarea>
				<?php echo sprintf(__('The line %1$d.', 'media-from-ftp'), 68); ?>
				<textarea readonly rows="2" style="font-size: 12px; width: 100%;">
				<?php echo $commandline_set2; ?>
				</textarea>
				</div>
				<div style="display:block; padding:5px 10px; font-weight: bold;">
				2. <?php _e('The execution of the command line.', 'media-from-ftp'); ?>
				</div>
				<div style="display:block; padding:5px 10px;">
				<div>% <code>/usr/bin/php <?php echo $commandline_pg; ?></code></div>
				<div style="display:block; padding:5px 15px; color:red;"><code>/usr/bin/php</code> >> <?php _e('Please check with the server administrator.', 'media-from-ftp'); ?></div>
					<div style="display:block;padding:5px 20px">
					<li style="font-weight: bold;"><?php _e('command line argument list', 'media-from-ftp'); ?></li>
						<div style="display:block;padding:5px 40px">
						<div><code>-s</code> <?php _e('Search directory', 'media-from-ftp'); ?></div>
						</div>
							<div style="display:block;padding:5px 60px">
							<div><?php _e('Example:', 'media-from-ftp'); ?> <code>-s wp-content/uploads</code></div>
							</div>
						<div style="display:block;padding:5px 40px">
						<div><code>-d</code> <?php _e('Date time settings', 'media-from-ftp'); ?> (new, server, exif)</div>
						</div>
							<div style="display:block;padding:5px 60px">
							<div><?php _e('Example:', 'media-from-ftp'); ?> <code>-d exif</code></div>
							</div>
						<div style="display:block;padding:5px 40px">
						<div><code>-e</code> <?php _e('Exclude file', 'media-from-ftp'); ?> (<?php _e('Regular expression is possible.', 'media-from-ftp'); ?>)</div>
						</div>
							<div style="display:block;padding:5px 60px">
							<div><?php _e('Example:', 'media-from-ftp'); ?> <code>-e "(.ktai.)|(.backwpup_log.)|(.ps_auto_sitemap.)|\.php|\.js"</code></div>
							</div>
						<div style="display:block;padding:5px 40px">
						<div><code>-t</code> <?php _e('File type:'); ?> (all, image, audio, video, document, spreadsheet, interactive, text, archive, code)</div>
						</div>
							<div style="display:block;padding:5px 60px">
							<div><?php _e('Example:', 'media-from-ftp'); ?> <code>-t image</code></div>
							</div>
						<div style="display:block;padding:5px 40px">
						<div><code>-x</code> <?php _e('File extension' , 'media-from-ftp'); ?></div>
						</div>
							<div style="display:block;padding:5px 60px">
							<div><?php _e('Example:', 'media-from-ftp'); ?> <code>-x jpg</code></div>
							</div>
						<div style="display:block;padding:5px 40px">
						<div><code>-p</code> <?php _e('Limit number of update files' , 'media-from-ftp'); ?></div>
						</div>
							<div style="display:block;padding:5px 60px">
							<div><?php _e('Example:', 'media-from-ftp'); ?> <code>-p 10</code></div>
							</div>
						<div style="display:block;padding:5px 40px">
						<div><code>-c</code> <?php _e('Exif tags for registering in the caption' , 'media-from-ftp'); ?></div>
						</div>
							<div style="display:block;padding:5px 60px">
							<div><?php _e('Example:', 'media-from-ftp'); ?> <code>-c "%title% %credit% %camera% %caption% %created_timestamp% %copyright% %aperture% %shutter_speed% %iso% %focal_length%"</code></div>
							</div>
					<div><?php _e('If the argument is empty, use the set value of the management screen.', 'media-from-ftp'); ?></div>
					</div>
					<div style="display:block;padding:5px 20px">
					<li style="font-weight: bold;"><?php _e('command line switch', 'media-from-ftp'); ?></li>
						<div style="display:block;padding:5px 40px">
						<div><code>-h</code> <?php _e('Hides the display of the log.', 'media-from-ftp'); ?></div>
						</div>
							<div style="display:block;padding:5px 60px">
							<div><?php _e('Example:', 'media-from-ftp'); ?> <code>-h</code></div>
							</div>
						<div style="display:block;padding:5px 40px">
						<div><code>-g</code> <?php _e('Create log to database.', 'media-from-ftp'); ?></div>
						</div>
							<div style="display:block;padding:5px 60px">
							<div><?php _e('Example:', 'media-from-ftp'); ?> <code>-g</code></div>
							</div>
						<div style="display:block;padding:5px 40px">
						<div><code>-m</code> <?php _e('If you want to search for filename that contains such -0x0. It is low speed.', 'media-from-ftp'); ?></div>
						</div>
							<div style="display:block;padding:5px 60px">
							<div><?php _e('Example:', 'media-from-ftp'); ?> <code>-m</code></div>
							</div>
					</div>
					<div style="color:red;"><?php _e('Command-line, please use by activate sure the plug-in.', 'media-from-ftp'); ?></div>
				</div>
				<div style="display:block; padding:5px 10px; font-weight: bold;">
				3. <?php _e('Register the command-line to the server cron.', 'media-from-ftp'); ?> (<?php _e('Example:', 'media-from-ftp'); ?> <?php _e('Run every 10 minutes.', 'media-from-ftp'); ?>)
				</div>
				<div style="display:block; padding:5px 30px;">
				<li style="font-weight: bold;"><?php _e('example:'); ?>1</li>
				<div><code>0,10,20,30,40,50 * * * * /usr/bin/php <?php echo $commandline_pg; ?></code></div>
				<div style="display:block; padding:5px 25px; color:red;"><code>/usr/bin/php</code> >> <?php _e('Please check with the server administrator.', 'media-from-ftp'); ?></div>
				<li style="font-weight: bold;"><?php _e('example:'); ?>2</li>
				<div><code>0,10,20,30,40,50 * * * * /usr/bin/wget <?php echo $commandline_wget; ?></code></div>
				<div style="display:block; padding:5px 25px; color:red;"><code>/usr/bin/wget</code> >> <?php _e('Please check with the server administrator.', 'media-from-ftp'); ?></div>
				</div>
			</div>

		</div>
		</div>
		<?php

	}


	/* ==================================================
	 * Sub Menu
	 */
	function search_register_page(){

		if ( !current_user_can( 'upload_files' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$submenu = 2;
		$this->options_updated($submenu);
		$mediafromftp_settings = get_option($this->wp_options_name());

		$def_max_execution_time = ini_get('max_execution_time');
		$max_execution_time = $mediafromftp_settings['max_execution_time'];

		$limit_seconds_html =  '<font color="red">'.$def_max_execution_time.__('seconds', 'media-from-ftp').'</font>';
		$command_line_html = '<a href="'.admin_url('admin.php?page=mediafromftp-settings#mediafromftp-settings-tabs-2').'">'.__('Command-line', 'media-from-ftp').'</a>';

		if ( !@set_time_limit($max_execution_time) ) {
			echo '<div class="error"><ul><li>'.sprintf(__('Execution time for this server is fixed at %1$s. Please note the "Number of items per page" so as not to exceed this limit. Please use the %2$s if you do not want to be bound by this restriction.', 'media-from-ftp'), $limit_seconds_html, $command_line_html).'</li></ul></div>';
		}

	    ?>
		<div class="wrap">

			<h2>Media from FTP <a href="<?php echo admin_url('admin.php?page=mediafromftp-search-register'); ?>" style="text-decoration: none;"><?php _e('Search & Register', 'media-from-ftp'); ?></a>
				<form method="post" style="float: right;" action="<?php echo admin_url('admin.php?page=mediafromftp-import'); ?>" />
					<input type="submit" class="button" value="<?php _e('Import'); ?>" />
				</form>
				<form method="post" style="float: right; margin-right: 0.5em;" action="<?php echo admin_url('admin.php?page=mediafromftp-log'); ?>" />
					<input type="submit" class="button" value="<?php _e('Log', 'media-from-ftp'); ?>" />
				</form>
				<form method="post" style="float: right; margin-right: 0.5em;" action="<?php echo admin_url('admin.php?page=mediafromftp-settings'); ?>">
					<input type="submit" class="button" value="<?php _e('Settings'); ?>" />
				</form>
			</h2>
			<div style="clear: both;"></div>
			<div id="mediafromftp-loading"><img src="<?php echo MEDIAFROMFTP_PLUGIN_URL.'/css/loading.gif'; ?>"></div>
			<div id="mediafromftp-loading-container">
				<?php
				include_once MEDIAFROMFTP_PLUGIN_BASE_DIR.'/inc/MediaFromFtp.php';
				$mediafromftp = new MediaFromFtp();
				$formhtml = $mediafromftp->form_html($mediafromftp_settings);
				unset($mediafromftp);
				require_once( MEDIAFROMFTP_PLUGIN_BASE_DIR.'/req/MediaFromFtpListTable.php' );
			    $MediaFromFtpListTable = new TT_MediaFromFtp_List_Table();
			    $MediaFromFtpListTable->prepare_items($mediafromftp_settings);
				?>
				<div><?php echo $formhtml; ?></div>
				<form method="post" id="mediafromftp_ajax_update">
					<form id="media-from-ftp-filter" method="get">
						<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
						<?php $MediaFromFtpListTable->display() ?>
					</form>
				</form>
			</div>
		</div>
	    <?php
	}

	/* ==================================================
	 * Sub Menu
	 */
	function log_page() {

		if ( !current_user_can( 'upload_files' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$mediafromftp_settings = get_option($this->wp_options_name());
		if ( !$mediafromftp_settings['log'] ) {
			echo '<div class="error"><ul><li>'.__('Current, log is not created. If you want to create a log, please put a check in the [Create log] in the settings.', 'media-from-ftp').'</li></ul></div>';
		}
		$def_max_execution_time = ini_get('max_execution_time');
		$max_execution_time = $mediafromftp_settings['max_execution_time'];

		$limit_seconds_html =  '<font color="red">'.$def_max_execution_time.__('seconds', 'media-from-ftp').'</font>';
		if ( !@set_time_limit($max_execution_time) ) {
			echo '<div class="error"><ul><li>'.sprintf(__('Execution time for this server is fixed at %1$s. Please run the frequently "Delete log" and "Export to CSV" so as not to exceed this limit.', 'media-from-ftp'), $limit_seconds_html).'</li></ul></div>';
		}

		?>
		<div class="wrap">

		<h2>Media from FTP <?php _e('Log', 'media-from-ftp'); ?>
			<form method="post" style="float: right;" action="<?php echo admin_url('admin.php?page=mediafromftp-import'); ?>" />
				<input type="submit" class="button" value="<?php _e('Import'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 0.5em;" action="<?php echo admin_url('admin.php?page=mediafromftp-search-register'); ?>" />
				<input type="submit" class="button" value="<?php _e('Search & Register', 'media-from-ftp'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 0.5em;" action="<?php echo admin_url('admin.php?page=mediafromftp-settings'); ?>">
				<input type="submit" class="button" value="<?php _e('Settings'); ?>" />
			</form>
		</h2>
		<div style="clear: both;"></div>

		<div id="mediafromftp-loading"><img src="<?php echo MEDIAFROMFTP_PLUGIN_URL.'/css/loading.gif'; ?>"></div>
		<div id="mediafromftp-loading-container">
		<?php
		global $wpdb;

		$user = wp_get_current_user();

		$table_name = $wpdb->prefix.'mediafromftp_log';

		if ( !empty($_POST['mediafromftp_clear_log']) && $_POST['mediafromftp_clear_log'] == 1 ) {
			if ( current_user_can('administrator') ) {
				$wpdb->query("DELETE FROM $table_name");
				echo '<div class="updated"><ul><li>'.__('Removed all of the log.', 'media-from-ftp').'</li></ul></div>';
			} else {
				$delete_count = $wpdb->delete($table_name, array( 'user' => $user->display_name ));
				if ( $delete_count > 0 ) {
					echo '<div class="updated"><ul><li>'.sprintf(__('%1$s of the log has been deleted %2$d.', 'media-from-ftp'), $user->display_name, $delete_count ).'</li></ul></div>';
				} else {
					echo '<div class="error"><ul><li>'.sprintf(__('%1$s do not have a possible deletion log.', 'media-from-ftp'), $user->display_name ).'</li></ul></div>';
				}
			}
		}

		$records = $wpdb->get_results("SELECT * FROM $table_name");

		$html = '<table id="mediafromftp-table"><tr><th>ID</th><th>'.__('Author').':</th><th>'.__('Title').':</th><th>'.__('Permalink:').'</th><th>URL:</th><th>'.__('File name:').'</th><th>'.__('Date/Time').':</th><th>'.__('File type:').'</th><th>'.__('File size:').'</th><th>'.__('Caption').'[Exif]:</th><th>'.__('Length:').'</th><th>'.__('Featured Image').'1:</th><th>'.__('Featured Image').'2:</th><th>'.__('Featured Image').'3:</th><th>'.__('Featured Image').'4:</th><th>'.__('Featured Image').'5:</th><th>'.__('Featured Image').'6:</th></tr>'."\n";

		$csv = '"ID","'.__('Author').'","'.__('Title').':","'.__('Permalink:').'","URL:","'.__('File name:').'","'.__('Date/Time').':","'.__('File type:').'","'.__('File size:').'","'.__('Caption').'[Exif]:","'.__('Length:').'","'.__('Featured Image').'1:","'.__('Featured Image').'2:","'.__('Featured Image').'3:","'.__('Featured Image').'4:","'.__('Featured Image').'5:","'.__('Featured Image').'6:"'."\n";
		foreach ( $records as $record ) {
			$csvs = '"'.$record->id.'","'.$record->user.'","'.$record->title.'","'.$record->permalink.'","'.$record->url.'","'.$record->filename.'","'.$record->time.'","'.$record->filetype.'","'.$record->filesize.'","'.$record->exif.'","'.$record->length.'","'.$record->thumbnail1.'","'.$record->thumbnail2.'","'.$record->thumbnail3.'","'.$record->thumbnail4.'","'.$record->thumbnail5.'","'.$record->thumbnail6.'"'."\n";

			$csv .= $csvs;
			$html .= '<tr>';
			$html .= '<td>'.$record->id.'</td>';
			$html .= '<td>'.$record->user.'</td>';
			$html .= '<td>'.$record->title.'</td>';
			$html .= '<td>'.$record->permalink.'</td>';
			$html .= '<td>'.$record->url.'</td>';
			$html .= '<td>'.$record->filename.'</td>';
			$html .= '<td>'.$record->time.'</td>';
			$html .= '<td>'.$record->filetype.'</td>';
			$html .= '<td>'.$record->filesize.'</td>';
			$html .= '<td>'.$record->exif.'</td>';
			$html .= '<td>'.$record->length.'</td>';
			$html .= '<td>'.$record->thumbnail1.'</td>';
			$html .= '<td>'.$record->thumbnail2.'</td>';
			$html .= '<td>'.$record->thumbnail3.'</td>';
			$html .= '<td>'.$record->thumbnail4.'</td>';
			$html .= '<td>'.$record->thumbnail5.'</td>';
			$html .= '<td>'.$record->thumbnail6.'</td>';
			$html .= '</tr>'."\n";
		}
		$html .= '</table>'."\n";

		$csvFileName = MEDIAFROMFTP_PLUGIN_TMP_DIR.'/'.$table_name.'.csv';
		if ( !empty($_POST['mediafromftp_put_log']) && $_POST['mediafromftp_put_log'] == 1 ) {
			file_put_contents($csvFileName, pack('C*',0xEF,0xBB,0xBF)); //UTF-8 BOM
			file_put_contents($csvFileName, $csv, FILE_APPEND | LOCK_EX);
		} else {
			if ( file_exists($csvFileName) ) {
				unlink($csvFileName);
			}
		}

		if ( !empty($records) ) {
			?>
			<div style="display: block; padding: 10px 10px">
			<form style="float: left;" method="post" action="<?php echo admin_url('admin.php?page=mediafromftp-log'); ?>" />
				<input type="hidden" name="mediafromftp_clear_log" value="1" />
				<div>
				<input type="submit" class="button" value="<?php _e('Delete log', 'media-from-ftp'); ?>" />
				</div>
			</form>
			<form style="float: left; margin-left: 0.5em; margin-right: 0.5em;" method="post" action="<?php echo admin_url('admin.php?page=mediafromftp-log'); ?>" />
				<input type="hidden" name="mediafromftp_put_log" value="1" />
				<div>
				<input type="submit" class="button" value="<?php _e('Export to CSV', 'media-from-ftp'); ?>" />
				</div>
			</form>
			<?php
			if ( file_exists($csvFileName) ) {
				?>
				<form method="post" action="<?php echo MEDIAFROMFTP_PLUGIN_TMP_URL.'/'.$table_name.'.csv'; ?>" />
					<div>
					<input type="hidden" name="mediafromftp_download" value="1" />
					<input type="submit" class="button" value="<?php _e('Download CSV', 'media-from-ftp'); ?>" />
					</div>
				</form>
				<?php
			}
			?>
			</div>
			<div style="clear: both;"></div>
			<div style="display: block; padding: 10px 10px">
			<?php echo $html;
			?>
			</div>
			<?php
		} else {
			if ( $mediafromftp_settings['log'] ) {
				echo '<div class="updated"><ul><li>'.__('There is no log.', 'media-from-ftp').'</li></ul></div>';
			}
		}
		?>
		</div>

		</div>

		<?php

	}

	/* ==================================================
	 * Sub Menu
	 */
	function medialibrary_import_page() {

		if ( !current_user_can( 'upload_files' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$scriptname = admin_url('admin.php?page=mediafromftp-import');

		?>
		<div class="wrap">
		<h2>Media from FTP <a href="<?php echo $scriptname; ?>" style="text-decoration: none;"><?php _e('Import'); ?></a>
			<form method="post" style="float: right;" action="<?php echo admin_url('admin.php?page=mediafromftp-log'); ?>" />
				<input type="submit" class="button" value="<?php _e('Log', 'media-from-ftp'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 0.5em;" action="<?php echo admin_url('admin.php?page=mediafromftp-search-register'); ?>" />
				<input type="submit" class="button" value="<?php _e('Search & Register', 'media-from-ftp'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 0.5em;" action="<?php echo admin_url('admin.php?page=mediafromftp-settings'); ?>">
				<input type="submit" class="button" value="<?php _e('Settings'); ?>" />
			</form>
		</h2>
		<div style="clear: both;"></div>

		<div id="medialibraryimport-loading-container">

		<h3><?php _e('This page does the independent processing from other pages in "Media from FTP".', 'media-from-ftp'); ?></h3>
		<h3><?php _e('To Import the files to Media Library from a WordPress export file.', 'media-from-ftp'); ?></h3>
		<?php
		$upload_dir_html = '<span style="color: red;">'.MEDIAFROMFTP_PLUGIN_UPLOAD_PATH.'</span>';
		?>
		<h3><?php echo sprintf(__('In uploads directory(%1$s), that you need to copy the file to the same state as the import source by FTP.', 'media-from-ftp'), $upload_dir_html); ?></h3>
		<hr>
		<?php
		if ( !empty($_FILES['filename']['name']) ) {
			$filename = $_FILES['filename']['tmp_name'];
			$name = basename($filename);
			move_uploaded_file($filename, MEDIAFROMFTP_PLUGIN_TMP_DIR.'/'.$name);

			include_once MEDIAFROMFTP_PLUGIN_BASE_DIR.'/inc/MediaFromFtp.php';
			$mediafromftp = new MediaFromFtp();
			?>
			<h4><?php _e('Assign Authors', 'media-from-ftp'); ?></h4>
			<?php
			echo $mediafromftp->author_select(MEDIAFROMFTP_PLUGIN_TMP_DIR.'/'.$name);
		} else if ( !empty($_POST['mediafromftp_select_author']) && !empty($_POST['mediafromftp_xml_file']) ) {
			?>
			<h4><?php _e('Ready to import. Press the following button to start the import.', 'media-from-ftp'); ?></h4>
			<form method="post" id="medialibraryimport_ajax_update">
				<input type="submit" class="button-primary button-large" value="<?php _e('Import'); ?>" />
			</form>
			<?php
		} else {
			?>
			<form method="post" action="<?php echo $scriptname; ?>" enctype="multipart/form-data">
			<h4><?php _e('Select File'); ?>[WordPress eXtended RSS (WXR)(.xml)]</h4>
			<div><input name="filename" type="file" size="80" /></div>
			<div><input type="submit" class="button" name="submit" value="<?php _e('File Load', 'media-from-ftp'); ?> " /></div>
			</form>
			<?php
		}
		?>

		</div>
		</div>

		<?php
	}

	/* ==================================================
	 * Update wp_options table.
	 * @param	string	$submenu
	 * @since	2.36
	 */
	function options_updated($submenu){

		include_once MEDIAFROMFTP_PLUGIN_BASE_DIR.'/req/MediaFromFtpCron.php';
		$mediafromftpcron = new MediaFromFtpCron();

		include_once MEDIAFROMFTP_PLUGIN_BASE_DIR.'/inc/MediaFromFtp.php';
		$mediafromftp = new MediaFromFtp();

		$mediafromftp_settings = get_option($this->wp_options_name());

		switch ($submenu) {
			case 1:
				if ( !empty($_POST['mediafromftp_dateset']) ) {
					if ( !empty($_POST['mediafromftp_cron_apply']) ) {
						$mediafromftp_cron_apply = $_POST['mediafromftp_cron_apply'];
					} else {
						$mediafromftp_cron_apply = FALSE;
					}
					if ( !empty($_POST['mediafromftp_cron_limit_number']) ) {
						$mediafromftp_cron_limit_number = $_POST['mediafromftp_cron_limit_number'];
					} else {
						$mediafromftp_cron_limit_number = FALSE;
					}
					if ( !empty($_POST['mediafromftp_cron_mail_apply']) ) {
						$mediafromftp_cron_mail_apply = $_POST['mediafromftp_cron_mail_apply'];
					} else {
						$mediafromftp_cron_mail_apply = FALSE;
					}
					if ( !empty($_POST['mediafromftp_caption_apply']) ) {
						$mediafromftp_caption_apply = $_POST['mediafromftp_caption_apply'];
					} else {
						$mediafromftp_caption_apply = FALSE;
					}
					if ( !empty($_POST['mediafromftp_exif_text']) ) {
						$exif_text = $_POST['mediafromftp_exif_text'];
					} else {
						$exif_text = $mediafromftp_settings['caption']['exif_text'];
					}
					if ( !empty($_POST['mediafromftp_exif_default']) ) {
						$exif_text = '%title% %credit% %camera% %caption% %created_timestamp% %copyright% %aperture% %shutter_speed% %iso% %focal_length%';
					}
					if ( !empty($_POST['mediafromftp_apply_log']) ) {
						$mediafromftp_apply_log = $_POST['mediafromftp_apply_log'];
					} else {
						$mediafromftp_apply_log = FALSE;
					}

					$mediafromftp_tbl = array(
										'pagemax' => $mediafromftp_settings['pagemax'],
										'basedir' => $mediafromftp_settings['basedir'],
										'searchdir' => $mediafromftp_settings['searchdir'],
										'sort' => $mediafromftp_settings['sort'],
										'ext2typefilter' => $mediafromftp_settings['ext2typefilter'],
										'extfilter' => $mediafromftp_settings['extfilter'],
										'search_display_metadata' => $_POST['search_display_metadata'],
										'dateset' => $_POST['mediafromftp_dateset'],
										'max_execution_time' => intval($_POST['mediafromftp_max_execution_time']),
										'character_code' => $_POST['mediafromftp_character_code'],
										'exclude' => stripslashes($_POST['mediafromftp_exclude']),
										'thumb_deep_search' => $_POST['mediafromftp_thumb_deep_search'],
										'cron' => array(
													'apply' => $mediafromftp_cron_apply,
													'schedule' => $_POST['mediafromftp_cron_schedule'],
													'limit_number' => $mediafromftp_cron_limit_number,
													'mail_apply' => $mediafromftp_cron_mail_apply,
													'mail' => $mediafromftp_settings['cron']['mail'],
													'user' => $mediafromftp_settings['cron']['user']
													),
										'caption' => array(
													'apply' => $mediafromftp_caption_apply,
													'exif_text' => $exif_text
													),
										'log' => $mediafromftp_apply_log
										);
					update_option( $this->wp_options_name(), $mediafromftp_tbl );
					if ( !empty($_POST['move_yearmonth_folders']) ) {
						update_option( 'uploads_use_yearmonth_folders', $_POST['move_yearmonth_folders'] );
					} else {
						update_option( 'uploads_use_yearmonth_folders', '0' );
					}
					if ( !$mediafromftp_cron_apply ) {
						$mediafromftpcron->CronStop($this->wp_options_name());
					} else {
						$mediafromftpcron->CronStart($this->wp_options_name());
					}
					echo '<div class="updated"><ul><li>'.__('Settings').' --> '.__('Changes saved.').'</li></ul></div>';
				}
				if ( !empty($_POST['mediafromftp_clear_cash']) ) {
					$del_cash_count = $mediafromftp->delete_all_cash();
					if ( $del_cash_count > 0 ) {
						echo '<div class="updated"><ul><li>'.__('Thumbnails Cache', 'media-from-ftp').' --> '.__('Delete').'</li></ul></div>';
					} else {
						echo '<div class="error"><ul><li>'.__('No Thumbnails Cache', 'media-from-ftp').'</li></ul></div>';
					}
				}
				break;
			case 2:
				if (!empty($_POST['ShowToPage'])){
					$pagemax = intval($_POST['mediafromftp_pagemax']);
					if ( $pagemax <= 0 ) {
						echo '<div class="error"><ul><li>'.__('Number of items per page:').' --> '.__('Incorrect value.', 'media-from-ftp').' '.__('Save failed.').'</li></ul></div>';
						$pagemax = $mediafromftp_settings['pagemax'];
					} else {
						echo '<div class="updated"><ul><li>'.__('Number of items per page:').' --> '.__('Settings saved.').'</li></ul></div>';
					}
				} else {
					$pagemax = $mediafromftp_settings['pagemax'];
				}
				$basedir = $mediafromftp_settings['basedir'];
				if (!empty($_POST['searchdir'])){
					$searchdir = urldecode($_POST['searchdir']);
				} else {
					$searchdir = $mediafromftp_settings['searchdir'];
					if ( MEDIAFROMFTP_PLUGIN_UPLOAD_PATH <> $basedir ) {
						$searchdir = MEDIAFROMFTP_PLUGIN_UPLOAD_PATH;
						$basedir = MEDIAFROMFTP_PLUGIN_UPLOAD_PATH;
					}
				}
				if (!empty($_GET['sort'])){
					$sort = $_GET['sort'];
					if ( $sort <> 'des' && $sort <> 'asc') {
						$sort = 'asc';
					}
				} else {
					$sort = $mediafromftp_settings['sort'];
				}
				if (!empty($_POST['ext2type'])){
					$ext2typefilter = $_POST['ext2type'];
				} else {
					$ext2typefilter = $mediafromftp_settings['ext2typefilter'];
				}
				if (!empty($_POST['extension'])){
					if ( $_POST['extension'] === 'all') {
						$extfilter = 'all';
					} else {
						if ( $ext2typefilter === 'all' || $ext2typefilter === wp_ext2type($_POST['extension']) ) {
							$extfilter = $_POST['extension'];
						} else {
							$extfilter = 'all';
						}
					}
				} else {
					$extfilter = $mediafromftp_settings['extfilter'];
				}
				$mediafromftp_tbl = array(
									'pagemax' => $pagemax,
									'basedir' => $basedir,
									'searchdir' => $searchdir,
									'sort' => $sort,
									'ext2typefilter' => $ext2typefilter,
									'extfilter' => $extfilter,
									'search_display_metadata' => $mediafromftp_settings['search_display_metadata'],
									'dateset' => $mediafromftp_settings['dateset'],
									'max_execution_time' => $mediafromftp_settings['max_execution_time'],
									'character_code' => $mediafromftp_settings['character_code'],
									'exclude' => $mediafromftp_settings['exclude'],
									'thumb_deep_search' => $mediafromftp_settings['thumb_deep_search'],
									'cron' => array(
												'apply' => $mediafromftp_settings['cron']['apply'],
												'schedule' => $mediafromftp_settings['cron']['schedule'],
												'limit_number' => $mediafromftp_settings['cron']['limit_number'],
												'mail_apply' => $mediafromftp_settings['cron']['mail_apply'],
												'mail' => $mediafromftp_settings['cron']['mail'],
												'user' => $mediafromftp_settings['cron']['user']
												),
									'caption' => array(
													'apply' => $mediafromftp_settings['caption']['apply'],
													'exif_text' => $mediafromftp_settings['caption']['exif_text']
												),
									'log' => $mediafromftp_settings['log']
									);
				update_option( $this->wp_options_name(), $mediafromftp_tbl );
				break;
		}

	}

	/* ==================================================
	 * @param	none
	 * @return	string	$wp_options_name
	 * @since	9.18
	 */
	function wp_options_name(){

		$user = wp_get_current_user();
		$cron_user = $user->ID;

		$wp_options_name = 'mediafromftp_settings'.'_'.$cron_user;

		return $wp_options_name;

	}

}

?>