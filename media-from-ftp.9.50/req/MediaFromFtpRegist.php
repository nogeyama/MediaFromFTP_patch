<?php
/**
 * Media from FTP
 * 
 * @package    Media from FTP
 * @subpackage MediaFromFtpRegist registered in the database
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

class MediaFromFtpRegist {

	/* ==================================================
	 * Settings Log Settings
	 * @since	9.19
	 */
	function log_settings(){

	    $mediafromftp_log_db_version = '1.0';
		$installed_ver = get_option( 'mediafromftp_log_version' );

		if( $installed_ver != $mediafromftp_log_db_version ) {
			global $wpdb;
			$log_name = $wpdb->prefix.'mediafromftp_log';

			// << version 9.19
			$sql = "CREATE TABLE " . $log_name . " (
			meta_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			id bigint(20),
			user text,
			title text,
			permalink text,
			url text,
			filename text,
			time datetime,
			filetype text,
			filesize text,
			exif text,
			length text,
			thumbnail1 text,
			thumbnail2 text,
			thumbnail3 text,
			thumbnail4 text,
			thumbnail5 text,
			thumbnail6 text,
			UNIQUE KEY meta_id (meta_id)
			)
			CHARACTER SET 'utf8';";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			update_option( 'mediafromftp_log_version', $mediafromftp_log_db_version );
		}

	}

	/* ==================================================
	 * Settings register
	 * @since	2.3
	 */
	function register_settings(){

		$user = wp_get_current_user();
		$cron_mail = $user->user_email;
		$cron_user = $user->ID;

		$wp_options_name = 'mediafromftp_settings'.'_'.$cron_user;

		$pagemax = 20;
		$basedir = MEDIAFROMFTP_PLUGIN_UPLOAD_PATH;
		$searchdir = MEDIAFROMFTP_PLUGIN_UPLOAD_PATH;
		$sort = 'asc';
		$ext2typefilter = 'all';
		$extfilter = 'all';
		$search_display_metadata = TRUE;
		$dateset = 'new';
		$max_execution_time = 300;
		if( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && get_locale() === 'ja' ) { // Japanese Windows
			$character_code = 'CP932';
		} else {
			$character_code = 'UTF-8';
		}
		$exclude = '(.ktai.)|(.backwpup_log.)|(.ps_auto_sitemap.)|\.php|\.js';
		$thumb_deep_search = FALSE;
		$cron_apply = FALSE;
		$cron_schedule = 'hourly';
		$cron_limit_number = FALSE;
		$cron_mail_apply = TRUE;

		$caption_apply = FALSE;
		$exif_text = '%title% %credit% %camera% %caption% %created_timestamp% %copyright% %aperture% %shutter_speed% %iso% %focal_length%';
		$log = FALSE;

		// << version 2.35
		if ( get_option('mediafromftp_exclude_file') ) {
			$exclude = get_option('mediafromftp_exclude_file');
			delete_option( 'mediafromftp_exclude_file' );
		}

		if ( !get_option($wp_options_name) ) {
			if ( get_option('mediafromftp_settings') ) { // old settings
				$mediafromftp_settings = get_option('mediafromftp_settings');
				if ( array_key_exists( "pagemax", $mediafromftp_settings ) ) {
					$pagemax = $mediafromftp_settings['pagemax'];
				}
				if ( array_key_exists( "basedir", $mediafromftp_settings ) ) {
					$basedir = $mediafromftp_settings['basedir'];
				}
				if ( array_key_exists( "searchdir", $mediafromftp_settings ) ) {
					$searchdir = $mediafromftp_settings['searchdir'];
				}
				if ( array_key_exists( "sort", $mediafromftp_settings ) ) {
					$sort = $mediafromftp_settings['sort'];
				}
				if ( array_key_exists( "ext2typefilter", $mediafromftp_settings ) ) {
					$ext2typefilter = $mediafromftp_settings['ext2typefilter'];
				}
				if ( array_key_exists( "extfilter", $mediafromftp_settings ) ) {
					$extfilter = $mediafromftp_settings['extfilter'];
				}
				if ( array_key_exists( "search_display_metadata", $mediafromftp_settings ) ) {
					$search_display_metadata = $mediafromftp_settings['search_display_metadata'];
				}
				if ( array_key_exists( "dateset", $mediafromftp_settings ) ) {
					$dateset = $mediafromftp_settings['dateset'];
				}
				if ( array_key_exists( "max_execution_time", $mediafromftp_settings ) ) {
					$max_execution_time = $mediafromftp_settings['max_execution_time'];
				}
				if ( array_key_exists( "character_code", $mediafromftp_settings ) ) {
					$character_code = $mediafromftp_settings['character_code'];
				}
				if ( array_key_exists( "exclude", $mediafromftp_settings ) ) {
					$exclude = $mediafromftp_settings['exclude'];
				}
				if ( array_key_exists( "thumb_deep_search", $mediafromftp_settings ) ) {
					$thumb_deep_search = $mediafromftp_settings['thumb_deep_search'];
				}
				if ( array_key_exists( "apply", $mediafromftp_settings["cron"] ) ) {
					$cron_apply = $mediafromftp_settings['cron']['apply'];
				}
				if ( array_key_exists( "schedule", $mediafromftp_settings["cron"] ) ) {
					$cron_schedule = $mediafromftp_settings['cron']['schedule'];
				}
				if ( array_key_exists( "limit_number", $mediafromftp_settings["cron"] ) ) {
					$cron_limit_number = $mediafromftp_settings['cron']['limit_number'];
				}
				if ( array_key_exists( "mail_apply", $mediafromftp_settings["cron"] ) ) {
					$cron_mail_apply = $mediafromftp_settings['cron']['mail_apply'];
				}
				if ( array_key_exists( "apply", $mediafromftp_settings["caption"] ) ) {
					$caption_apply = $mediafromftp_settings['caption']['apply'];
				}
				if ( array_key_exists( "exif_text", $mediafromftp_settings["caption"] ) ) {
					$exif_text = $mediafromftp_settings['caption']['exif_text'];
				}
				if ( array_key_exists( "log", $mediafromftp_settings ) ) {
					$log = $mediafromftp_settings['log'];
				}
				delete_option( 'mediafromftp_settings' );
			}
		} else {
			$mediafromftp_settings = get_option($wp_options_name);
			if ( array_key_exists( "pagemax", $mediafromftp_settings ) ) {
				$pagemax = $mediafromftp_settings['pagemax'];
			}
			if ( array_key_exists( "basedir", $mediafromftp_settings ) ) {
				$basedir = $mediafromftp_settings['basedir'];
			}
			if ( array_key_exists( "searchdir", $mediafromftp_settings ) ) {
				$searchdir = $mediafromftp_settings['searchdir'];
			}
			if ( array_key_exists( "sort", $mediafromftp_settings ) ) {
				$sort = $mediafromftp_settings['sort'];
			}
			if ( array_key_exists( "ext2typefilter", $mediafromftp_settings ) ) {
				$ext2typefilter = $mediafromftp_settings['ext2typefilter'];
			}
			if ( array_key_exists( "extfilter", $mediafromftp_settings ) ) {
				$extfilter = $mediafromftp_settings['extfilter'];
			}
			if ( array_key_exists( "search_display_metadata", $mediafromftp_settings ) ) {
				$search_display_metadata = $mediafromftp_settings['search_display_metadata'];
			}
			if ( array_key_exists( "dateset", $mediafromftp_settings ) ) {
				$dateset = $mediafromftp_settings['dateset'];
			}
			if ( array_key_exists( "max_execution_time", $mediafromftp_settings ) ) {
				$max_execution_time = $mediafromftp_settings['max_execution_time'];
			}
			if ( array_key_exists( "character_code", $mediafromftp_settings ) ) {
				$character_code = $mediafromftp_settings['character_code'];
			}
			if ( array_key_exists( "exclude", $mediafromftp_settings ) ) {
				$exclude = $mediafromftp_settings['exclude'];
			}
			if ( array_key_exists( "thumb_deep_search", $mediafromftp_settings ) ) {
				$thumb_deep_search = $mediafromftp_settings['thumb_deep_search'];
			}
			if ( array_key_exists( "apply", $mediafromftp_settings["cron"] ) ) {
				$cron_apply = $mediafromftp_settings['cron']['apply'];
			}
			if ( array_key_exists( "schedule", $mediafromftp_settings["cron"] ) ) {
				$cron_schedule = $mediafromftp_settings['cron']['schedule'];
			}
			if ( array_key_exists( "limit_number", $mediafromftp_settings["cron"] ) ) {
				$cron_limit_number = $mediafromftp_settings['cron']['limit_number'];
			}
			if ( array_key_exists( "mail_apply", $mediafromftp_settings["cron"] ) ) {
				$cron_mail_apply = $mediafromftp_settings['cron']['mail_apply'];
			}
			if ( array_key_exists( "apply", $mediafromftp_settings["caption"] ) ) {
				$caption_apply = $mediafromftp_settings['caption']['apply'];
			}
			if ( array_key_exists( "exif_text", $mediafromftp_settings["caption"] ) ) {
				$exif_text = $mediafromftp_settings['caption']['exif_text'];
			}
			if ( array_key_exists( "log", $mediafromftp_settings ) ) {
				$log = $mediafromftp_settings['log'];
			}
		}

		$mediafromftp_tbl = array(
							'pagemax' => $pagemax,
							'basedir' => $basedir,
							'searchdir' => $searchdir,
							'sort' => $sort,
							'ext2typefilter' => $ext2typefilter,
							'extfilter' => $extfilter,
							'search_display_metadata' => $search_display_metadata,
							'dateset' => $dateset,
							'max_execution_time' => $max_execution_time,
							'character_code' => $character_code,
							'exclude' => $exclude,
							'thumb_deep_search' => $thumb_deep_search,
							'cron' => array(
										'apply' => $cron_apply,
										'schedule' => $cron_schedule,
										'limit_number' => $cron_limit_number,
										'mail_apply' => $cron_mail_apply,
										'mail' => $cron_mail,
										'user' => $cron_user
										),
							'caption' => array(
											'apply' => $caption_apply,
											'exif_text' => $exif_text
										),
							'log' => $log
						);
		update_option( $wp_options_name, $mediafromftp_tbl );

	}

}

?>