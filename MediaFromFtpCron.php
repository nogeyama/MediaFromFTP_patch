<?php
/**
 * Media from FTP
 * 
 * @package    Media from FTP
 * @subpackage MediafromFTPCron Cron
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

class MediaFromFtpCron {


        // AKM from inc/MediaFromFtp
        /* ==================================================
         * @param       string  $str
         * @param       string  $character_code
         * @return      string  $str
         * @since       9.05
         */
        function mb_utf8($str, $character_code) {

                if ( function_exists('mb_convert_encoding') && $character_code <> 'none' ) {
                        $str = mb_convert_encoding($str, "UTF-8", "auto");
                }

                return $str;

        }


	/* ==================================================
	 * Cron Start
	 * @param	string	$option_name
	 * @since	3.0
	 */
	function CronStart($option_name) {

		$mediafromftp_settings = get_option($option_name);
		$args = array( 'wp_options_name' => $option_name );

		if ( $mediafromftp_settings['cron']['apply'] ) {
			if ( !wp_next_scheduled( 'MediaFromFtpCronHook' ) ) {
				wp_schedule_event(time(), $mediafromftp_settings['cron']['schedule'], 'MediaFromFtpCronHook', $args);
			} else {
				if ( wp_get_schedule( 'MediaFromFtpCronHook' ) <> $mediafromftp_settings['cron']['schedule'] ) {
					wp_clear_scheduled_hook('MediaFromFtpCronHook', $args);
					wp_schedule_event(time(), $mediafromftp_settings['cron']['schedule'], 'MediaFromFtpCronHook', $args);
				}
			}
		}

	}


	/* ==================================================
	 * Cron All Start
	 * @since	9.19
	 */
	function CronAllStart() {

		global $wpdb;
		$option_names = array();
		$wp_options = $wpdb->get_results("
						SELECT option_name
						FROM $wpdb->options
						WHERE option_name LIKE '%%mediafromftp_settings%%'
						");
		foreach ( $wp_options as $wp_option ) {
			$option_names[] = $wp_option->option_name;
		}

		if ( !is_multisite() ) {
			foreach ( $option_names as $option_name ) {
				$this->CronStart($option_name);
			}
		} else { // For Multisite
		    // For regular options.
		    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
		    $original_blog_id = get_current_blog_id();
		    foreach ( $blog_ids as $blog_id ) {
		        switch_to_blog( $blog_id );
				foreach ( $option_names as $option_name ) {
					$this->CronStart($option_name);
				}
		    }
		    switch_to_blog( $original_blog_id );
		}

	}

	/* ==================================================
	 * Cron Stop
	 * @param	string	$option_name
	 * @since	3.0
	 */
	function CronStop($option_name) {

		$args = array( 'wp_options_name' => $option_name );
		wp_clear_scheduled_hook('MediaFromFtpCronHook', $args);

	}

	/* ==================================================
	 * Cron All Stop
	 * @since	9.19
	 */
	function CronAllStop() {

		global $wpdb;
		$option_names = array();
		$wp_options = $wpdb->get_results("
						SELECT option_name
						FROM $wpdb->options
						WHERE option_name LIKE '%%mediafromftp_settings%%'
						");
		foreach ( $wp_options as $wp_option ) {
			$option_names[] = $wp_option->option_name;
		}

		if ( !is_multisite() ) {
			foreach ( $option_names as $option_name ) {
				$this->CronStop($option_name);
			}
		} else { // For Multisite
		    // For regular options.
		    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
		    $original_blog_id = get_current_blog_id();
		    foreach ( $blog_ids as $blog_id ) {
		        switch_to_blog( $blog_id );
				foreach ( $option_names as $option_name ) {
					$this->CronStop($option_name);
				}
		    }
		    switch_to_blog( $original_blog_id );
		}

	}

	/* ==================================================
	 * Cron
	 * @param	string	$wp_options_name => wp_schedule_event $args
	 * @since	3.0
	 */
	function CronDo($wp_options_name){

		date_default_timezone_set('Asia/Tokyo'); // AKM

		include_once MEDIAFROMFTP_PLUGIN_BASE_DIR.'/inc/MediaFromFtp.php';
		$mediafromftp = new MediaFromFtp();

		// for mediafromftpcmd.php
		$cmdoptions = getopt("j:s:d:e:t:x:p:c:hgm");  //AKM: add -j

		$mediafromftp_settings = get_option($wp_options_name);
		$yearmonth_folders = get_option('uploads_use_yearmonth_folders');

		$cmdlinedebugs = debug_backtrace();
		if ( basename($cmdlinedebugs['0']['file']) === 'mediafromftpcmd.php' ) {
			$cmdline = TRUE;
		} else {
			$cmdline = FALSE;
			$max_execution_time = $mediafromftp_settings['max_execution_time'];
			set_time_limit($max_execution_time);
		}

		if ( isset($cmdoptions['j']) ) {                      // AKM: -j jpegfile="xxx.jpg" in /upload/
			$akm_jpegfile = $cmdoptions['j'];
                        // 'pic-20161225T231622-pic.jpg'

                        echo "-j:".$akm_jpegfile."\n";
		} else {
			echo "STOP, use -j option";
                        exit;
		}

                if ( isset($cmdoptions['s']) ) {
                        $searchdir = $cmdoptions['s'];
                } else {
                        $searchdir = $mediafromftp_settings['searchdir'];
                }

		if ( isset($cmdoptions['d']) ) {
			if ( $cmdoptions['d'] === 'new' || $cmdoptions['d'] === 'server' || $cmdoptions['d'] === 'exif' ) {
				$dateset = $cmdoptions['d'];
			} else {
				$dateset = $mediafromftp_settings['dateset'];
			}
		} else {
			$dateset = $mediafromftp_settings['dateset'];
		}

		if ( isset($cmdoptions['x']) ) {
			$extfilter = $cmdoptions['x'];
		} else {
			$extfilter = $mediafromftp_settings['extfilter'];
		}

		if ( isset($cmdoptions['p']) ) {
			$pagemax = $cmdoptions['p'];
			$limit_number = TRUE;
		} else {
			$pagemax = $mediafromftp_settings['pagemax'];
			$limit_number = $mediafromftp_settings['cron']['limit_number'];
		}

		$exif_text_tag = NULL;
		if ( isset($cmdoptions['c']) ) {
			$exif_text_tag = $cmdoptions['c'];
		} else {
			if ( $mediafromftp_settings['caption']['apply'] ) {
				$exif_text_tag = $mediafromftp_settings['caption']['exif_text'];
			}
		}

		$hide = FALSE;
		if ( isset($cmdoptions['h']) ) {
			$hide = TRUE;
		}

		$log = FALSE;
		if ( isset($cmdoptions['g']) ) {
			$log = TRUE;
		} else {
			$log = $mediafromftp_settings['log'];
		}

		$thumb_deep_search = FALSE;
		if ( isset($cmdoptions['m']) ) {
			$thumb_deep_search = TRUE;
		} else {
			$thumb_deep_search = $mediafromftp_settings['thumb_deep_search'];
		}

		unset($cmdoptions);

		$document_root = ABSPATH.$searchdir;

		$mediafromftp->mb_initialize($mediafromftp_settings['character_code']);
		$document_root = $mediafromftp->mb_encode_multibyte($document_root, $mediafromftp_settings['character_code']);

		if ( strstr($searchdir, '../') ) {
			$document_root = realpath($document_root);
		}

		global $wpdb;

		$extpattern = $mediafromftp->extpattern($extfilter);
		// $files = $mediafromftp->scan_file($document_root, $extpattern, $mediafromftp_settings);

		$count = 0;
		$output_mail = NULL;
		$mail_apply = $mediafromftp_settings['cron']['mail_apply'];


                echo "--------------------------------------------------------\n";
		// foreach ( $files as $file ){ // AKM:                           // All files in the director
                        $akm_key = wp_normalize_path($akm_jpegfile);
                        echo "A:akm_key="; print_r($akm_key); echo "\n";

                        $akm_upload_path = wp_normalize_path(MEDIAFROMFTP_PLUGIN_UPLOAD_DIR);
                        $file = $akm_upload_path . '/' . $akm_jpegfile;

                        echo "I:file="; print_r($file); echo "\n";
                        // file=/var/www/html/blog/wp-content/uploads/pic-20170108T021113-pic.jpg          ...OK!


                        $akm_exts = explode('.', wp_basename($file));
                        $ext = end($akm_exts);
                        $suffix_file = '.' . $ext;
                        $file = wp_normalize_path($file);
                        


                        // AKM: search $akm_jpegfile in DB
                        $akm_dbres1 = $wpdb->get_results("
                                             SELECT meta_value
                                             FROM wp_posts, wp_postmeta
                                             WHERE wp_posts.post_type = 'attachment'
                                               AND wp_posts.ID = wp_postmeta.post_id
                                               AND wp_postmeta.meta_key = '_wp_attached_file'
                                               AND meta_value = '" . $akm_key . "'
                                               ORDER BY meta_id ASC LIMIT 1
                                             ");
                        echo "wpdb:"; print_r($akm_dbres1); echo "\n";
                        echo "wpdb:["; print_r($akm_dbres1[0]->meta_value); echo "]\n";
                        // wpdb:[2016/12/pic-20161231T023426-pic.jpg]
                        // wpdb:[]
                        $new_file = ! (bool) $akm_dbres1[0]->meta_value;  // fg,  Found:TRUE, NotFound:FALSE

                        echo "A:new_file(fg)="; print_r($new_file); echo "\n";


                        $new_url = MEDIAFROMFTP_PLUGIN_UPLOAD_URL . '/' . $akm_key;
                        $new_titles = explode('/', $new_url);
                        $new_title = str_replace($suffix_file, '', end($new_titles));
                        //$akm_new_title_md5 = md5($akm_new_title);
                        //$akm_new_url_md5 = str_replace($akm_new_title.$akm_suffix_file, '', $akm_new_url).$akm_new_title_md5.$akm_suffix_file;

                        $new_url = $this->mb_utf8($new_url, $mediafromftp_settings['character_code']); //akm
                        echo "A:new_url="; print_r($new_url); echo "\n";
                        // http://192.168.1.33/blog/wp-content/uploads/2016/12/pic-20161225T231622-pic.jpg


			// list($new_file, $ext, $new_url) = $mediafromftp->input_url($file, $attachments, $mediafromftp_settings['character_code'], $thumb_deep_search);



                        echo "R:new_file(fg)=["; print_r( $new_file); echo "]\n";
                        echo "R:ext="; print_r( $ext); echo "\n";
                        echo "R:new_url="; print_r( $new_url); echo "\n";
                        echo "---\n";
			if ($new_file) {
                                echo "if (TRUE) ==>> It is a new file!\n";
				++$count;

				// get_date_check
				$date = $mediafromftp->get_date_check($file, $dateset);
                                echo "dateCheck"; print_r($date); echo "\n";

                                echo "---REGIST---\n";
                                echo "RgI: ext="; print_r($ext); echo "\n"; 
                                echo "RgI: new_url="; print_r($new_url); echo "\n"; 
                                echo "RgI: date="; print_r($date); echo "\n"; 
                                echo "RgI: dateset="; print_r($dateset); echo "\n"; 
                                echo "RgI: yearmonth_folders="; print_r($yearmonth_folders); echo "\n"; 

				// Regist
				list($attach_id, $new_attach_title, $new_url_attach, $metadata) = $mediafromftp->regist($ext, $new_url, $date, $dateset, $yearmonth_folders, $mediafromftp_settings['character_code'], $mediafromftp_settings['cron']['user']);

                                echo "RgO:attach_id"; print_r( $attach_id); echo "\n"; 
                                echo "RgO:attach_title"; print_r( $new_attach_title); echo "\n";
                                echo "RgO:metadata"; print_r( $metadata); echo "\n";


				if ( ($mail_apply && !$cmdline) || (!$hide && $cmdline) || $log ) {
					if ( $attach_id == -1 || $attach_id == -2 ) { // error
						$output_text = NULL;
						$error_title = $new_attach_title;
						$error_url = $new_url_attach;
						if ( $attach_id == -1 ) {
							$output_text .= __('File name:').$error_title."\n";
							$output_text .= __('Directory name:', 'media-from-ftp').$error_url."\n";
							$output_text .= sprintf(__('<div>You need to make this directory writable before you can register this file. See <a href="%1$s" target="_blank">the Codex</a> for more information.</div><div>Or, filename or directoryname must be changed of illegal. Please change Character Encodings for Server of <a href="%2$s">Settings</a>.</div>', 'media-from-ftp'), 'http://codex.wordpress.org/Changing_File_Permissions', admin_url('admin.php?page=mediafromftp-settings'))."\n";
						} else if ( $attach_id == -2 ) {
							$output_text .= __('Title').': '.$error_title."\n";
							$output_text .= 'URL: '.$error_url."\n";
							$output_text .= __('This file could not be registered in the database.', 'media-from-ftp')."\n";
						}
						$output_text .= "\n";
					} else {

						// OutputMetaData
						list($imagethumburls, $mimetype, $length, $stamptime, $file_size) = $mediafromftp->output_metadata($ext, $attach_id, $metadata, $mediafromftp_settings['character_code']);


						$new_url_attachs = explode('/', $new_url_attach);

						$exif_text = NULL;
						$thumbnail = array();
						$thumbnail[1] = NULL;
						$thumbnail[2] = NULL;
						$thumbnail[3] = NULL;
						$thumbnail[4] = NULL;
						$thumbnail[5] = NULL;
						$thumbnail[6] = NULL;

						$output_text = NULL;
						$output_text .= __('Count').': '.$count."\n";
						$output_text .= 'ID: '.$attach_id."\n";
						$output_text .= __('Title').': '.$new_attach_title."\n";
						$output_text .= __('Permalink:').' '.get_attachment_link($attach_id)."\n";
						$output_text .= 'URL: '.$new_url_attach."\n";
						$output_text .= __('File name:').' '.end($new_url_attachs)."\n";
						$output_text .= __('Date/Time').': '.$stamptime."\n";
						if ( !$file_size ) {
							$file_size = __('Could not retrieve.', 'media-from-ftp');
						} else {
							$file_size = size_format($file_size);
						}
						$output_text .= __('File type:').' '.$mimetype."\n";
						$output_text .= __('File size:').' '.$file_size."\n";
						if ( wp_ext2type($ext) === 'image' ) {
							$thumb_count = 0;
							foreach ( $imagethumburls as $thumbsize => $imagethumburl ) {
								$output_text .= $thumbsize.': '.$imagethumburl."\n";
								++$thumb_count;
								$thumbnail[$thumb_count] = $imagethumburl;
							}
							if ( !empty($exif_text_tag) ) {
								$mime_type = $mediafromftp->mime_type($ext);
								if ( $mime_type === 'image/jpeg' || $mime_type === 'image/tiff' ) {
									$exif_text = $mediafromftp->exifcaption($attach_id, $metadata, $exif_text_tag);
									if ( !empty($exif_text) ) {
										$output_text .= __('Caption').'[Exif]: '.$exif_text."\n";
									}
								}
							}
						} else {
							if ( wp_ext2type($ext) === 'video' || wp_ext2type($ext) === 'audio' ) {
								$output_text .= __('Length:').' '.$length."\n";
							}
						}
						$output_text .= "\n";

						if ( $log ) {
							// Log
							$user = get_userdata($mediafromftp_settings['cron']['user']);
							$log_arr = array(
								'id' => $attach_id,
								'user' => $user->display_name,
								'title' => $new_attach_title,
								'permalink' => get_attachment_link($attach_id),
								'url' => $new_url_attach,
								'filename' => end($new_url_attachs),
								'time' => $stamptime,
								'filetype' => $mimetype,
								'filesize' => $file_size,
								'exif' => $exif_text,
								'length' => $length,
								'thumbnail1' => $thumbnail[1],
								'thumbnail2' => $thumbnail[2],
								'thumbnail3' => $thumbnail[3],
								'thumbnail4' => $thumbnail[4],
								'thumbnail5' => $thumbnail[5],
								'thumbnail6' => $thumbnail[6]
								);
							$table_name = $wpdb->prefix.'mediafromftp_log';
							$wpdb->insert( $table_name, $log_arr);
							$wpdb->show_errors();
						}
					}

					if ( $cmdline ) {
						if ( !$hide ) {
							echo $mediafromftp->mb_encode_multibyte($output_text, $mediafromftp_settings['character_code']);
						}
					} else {
						if ( $mail_apply ) {
							$output_mail .= $mediafromftp->mb_utf8($output_text, $mediafromftp_settings['character_code']);
						}
					}
				}
				if ( $count == $pagemax && $limit_number ) {
					break;
				}
			}
                        else {
                                echo "if (FALSE) ==>> This file already exists in DB\n";
                        }
		// } // AKM: comment out
		if ( !empty($output_mail) ) {
			$to = $mediafromftp_settings['cron']['mail'];
			$subject = 'Media from FTP Schedule';
			wp_mail( $to, $subject, $output_mail );
		}

	}

}

?>
