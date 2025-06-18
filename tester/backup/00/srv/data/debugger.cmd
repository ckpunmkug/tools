#/srv/wordpress/www/index.php:1
break /srv/wordpress/www/wp-admin/includes/file.php:930
run
ev var_dump(is_uploaded_file( $file['tmp_name'] ), $file['tmp_name']);
continue
quit

