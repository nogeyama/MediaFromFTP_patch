#!/bin/sh
#
#

D_DAV='/var/www/html/raspi01'
D_WP_UPL='/var/www/html/blog/wp-content/uploads'


while :
do
  ls $D_DAV/*.jpg 2> /dev/null
  [ $? -eq 0 ] && mv $D_DAV/*.jpg $D_WP_UPL/

  #echo -n "."
  echo "."
  ls $D_WP_UPL/*.jpg 2> /dev/null
  [ $? -eq 0 ] && \
    ls $D_WP_UPL/*.jpg | \
    sed "s:$D_WP_UPL/::1" | \
    xargs -I {} /usr/bin/php /var/www/html/blog/wp-content/plugins/media-from-ftp/mediafromftpcmd.php -t image -x jpg -d exif -j {}

    # xargs -I {} ls -al $D_WP_UPL/{}

  #set -x
  sleep 2
  #set +x

done
