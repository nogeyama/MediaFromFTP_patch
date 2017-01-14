=== Media from FTP ===
Contributors: Katsushi Kawamori
Donate link: http://pledgie.com/campaigns/28307
Tags: admin, attachment, attachments, ftp, gallery, image preview, image upload, images, import, importer, media, media library, schedule, sync, synchronize, upload, uploader
Requires at least: 3.6.0
Tested up to: 4.7
Stable tag: 9.50
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Register to media library from files that have been uploaded by FTP.

== Description ==

= Register to media library from files that have been uploaded by FTP. =
* This create a thumbnail of the image file.
* This create a metadata(Images, Videos, Audios).
* Change the date/time.
* Register the Exif data to the caption.
* Work with [DateTimePicker](http://xdsoft.net/jqplugins/datetimepicker/). jQuery plugin select date/time.
* If use the Schedule options, can periodically run.
* The execution of the command line is supported.(mediafromftpcmd.php)
* Export the log to a CSV file.
* To Import the files to Media Library from a WordPress export file.
* You can register a large number of files without timeout work with Ajax.

= Why I made this? =
* In the media uploader, you may not be able to upload by the environment of server. That's when the files are large. You do not mind the size of the file if FTP.

= Special Thanks! Created Banner & Icon =
* [Li-An](http://www.li-an.fr/blog/)

= Special Thanks! Translator =
* Deutsch [dionysous](https://profiles.wordpress.org/dionysous/)
* Français [Li-An](http://www.echodesplugins.li-an.fr/)

== Installation ==

1. Upload `media-from-ftp` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Certain file types can not be searched. =
* If you want to add the mime type that can be used in the media library to each file type, Please use the <a href="http://wordpress.org/plugins/mime-types-plus/">Mime Types Plus</a>.

= I will not find a file with name like this: a-b-0x0.jpg. =
* Media from FTP Settings -> Settings -> Search method for the exclusion of the thumbnail
* Check of "Unusual selection. if you want to search for filename that contains such -0x0. It is low speed."

= Where is it better to upload files? =
* Upload directory is any of the following locations.
* Single-site wp-content/uploads
* Multisite wp-content/uploads/sites/*

= I want to register file for any folder. =
* Media from FTP Settings -> Settings -> Date.
* Uncheck of "Organize my uploads into month- and year-based folders".

= I want to register file to "month- and year-based folders" without relevant to the timestamp of the file. =
* Media from FTP Settings -> Settings -> Date.
* Uncheck of "Organize my uploads into month- and year-based folders".

= File at the time of registration is moved to another directory. =
* If checked "Organize my uploads into month- and year-based folders", it will move the file at the time of registration to year month-based folders. If you want to register in the same directory, Please uncheck.

= The original file is deleted. =
* The case of the following of this plugin to delete the file.
1. If it contains spaces in the file name. Convert to "-". And remove original file. image example.jpg -> image-example.jpg
2. If the file name is a multi-byte. It makes the MD5 conversion. And remove original file. image例.jpg -> 2edd9ad56212ce13a39f25b429b09012.jpg
3. If checked "Organize my uploads into month- and year-based folders", it copy the file to the "month- and year-based folder" and then delete the original file. wp-content/uploads/sites/2/image-example.jpg -> wp-content/uploads/sites/2/2015/09/image-example.jpg
* Thumbnail creation, database registration, do in file after copy.

= The original file is deleted, it will be the one that has been added to eight characters to the end of the file. =
* When find the same file name in the media library in order to avoid duplication of the file, adds the date and time, minute, and second at the time it was registered in the end.
* image-example.jpg -> image-example03193845.jpg
* Meaning 03193845 -> 3rd 19h38m45s

= 'Fatal error: Maximum execution time of ** seconds exceeded.' get an error message. =
* Media from FTP Settings -> Settings -> Execution time
* Please increasing the number of seconds.

= 'Fatal error: Call to undefined function getopt()' get an error message in Windows Server. =
* Media from FTP uses the [getopt](http://php.net/manual/en/function.getopt.php). In the case of Windows, please use the PHP5.3.0 higher versions.

= I want to change the date at the time of registration. =
* Media from FTP Settings -> Settings -> Date -> Get the date/time of the file, and updated based on it. Change it if necessary.
* Please checked.

= I want to register at the date of the Exif information. =
* Media from FTP Settings -> Settings -> Date -> Get the date/time of the file, and updated based on it. Change it if necessary.Get by priority if there is date and time of the Exif information. 
* Please checked.

= I want to register the Exif information in the caption of the media library. =
* Media from FTP Settings -> Settings -> Exif Caption

= In Exif Caption, I want to change the display order of the Exif. =
* Please swapping the order of the Exif Tags. Please save your settings.

= I would like to hide the files do not need to search & registration screen. =
* Media from FTP Settings -> Settings -> Exclude file
* Please enter the exclusion file. It can be a regular expression.

= Periodically, I would like to register. =
* There is a schedule function.
* Media from FTP Settings -> Settings -> Schedule

= I want to limit the number of registered every once in a schedule. =
* Media from FTP Search & Register -> Number of items per page:
* Enter a numeric value.
* Media from FTP Settings -> Settings -> Schedule -> Apply Schedule
* Please checked.
* Media from FTP Settings -> Settings -> Schedule -> Apply limit number of update files.
* Please checked.

= I would like to apply a more finely schedule. =
* Use the mediafromftpcmd.php, please register on the server cron.

= File is located in a large amount. I would like to register without having to worry about the running time. =
* If you can use the command line, please use the mediafromftpcmd.php.

= mediafromftpcmd.php does not run. =
* Rewriting is need.
* Media from FTP Settings -> Command-line
* Please look at.

= I want to turn off the creation of additional images such as thumbnail. =
* It conforms to the WordPress settings.
* Settings-> Media
* Please change the six values to all zeros. 
* Please comment out the 'set_post_thumbnail_size' or 'add_image_size' of theme's functions.php.

== Screenshots ==

1. Settings1
2. Settings2
3. Search file display
4. Registration file selection
5. File registration result

== Changelog ==

= 9.50 =
Adopted WP_List_Table for search page.

= 9.43 =
Add author assignment to import.

= 9.42 =
Fixed problem of Exif Text display.

= 9.41 =
Change Display for Import.

= 9.40 =
Import Attachments from a WordPress export file.
Fixed problem of Javascript.

= 9.33 =
Fixed problem of Javascript.

= 9.32 =
Changed how to search file.

= 9.31 =
Fixed an issue that freezes at the time of a large amount of file registration.

= 9.30 =
Adopted the Ajax for the file registration.
Add error handler for the database registration.
Add setting 'Display of search results'.
Fixed problem of initial configuration.

= 9.25 =
Fixed an issue that is not translated.

= 9.24 =
Add error handler for the set_time_limit.
Add cron example of the Command-line.

= 9.23 =
Fixed problem of initial configuration.

= 9.22 =
Add search option for filename like a-b-0x0.jpg.
Fixed problem of Initial setting.
Fixed problem of uninstall.
Change mediafromftpcmd.php.

== Upgrade Notice ==

= 9.50 =
= 9.43 =
= 9.42 =
= 9.41 =
= 9.40 =
= 9.33 =
= 9.32 =
= 9.31 =
= 9.30 =
= 9.25 =
= 9.24 =
= 9.23 =
= 9.22 =

