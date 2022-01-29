##
# You should look at the following URL's in order to grasp a solid understanding
# of Nginx configuration files in order to fully unleash the power of Nginx.
# http://wiki.nginx.org/Pitfalls
# http://wiki.nginx.org/QuickStart
# http://wiki.nginx.org/Configuration
#
# Generally, you will want to move this file somewhere, and start with a clean
# file but keep this around for reference. Or just disable in sites-enabled.
#
# Please see /usr/share/doc/nginx-doc/examples/ for more detailed examples.
##

# Default server configuration
#
server {
    listen 80;
    listen [::]:80;

    # SSL configuration
    #
    # listen 443 ssl default_server;
    # listen [::]:443 ssl default_server;
    #
    # Note: You should disable gzip for SSL traffic.
    # See: https://bugs.debian.org/773332
    #
    # Read up on ssl_ciphers to ensure a secure configuration.
    # See: https://bugs.debian.org/765782
    #
    # Self signed certs generated by the ssl-cert package
    # Don't use them in a production server!
    #
    # include snippets/snakeoil.conf;

    root /usr/share/phpmyadmin;

    # Add index.php to the list if you are using PHP
    index index.php index.html index.htm;

    # Errors are handled by PHP
    error_page 404 index.php;

    # No indexes
    autoindex off;

    server_name pma.tw.com www.pma.tw.com;

    location / {
        # Attempt files, folders or 404
        try_files $uri $uri/ =404;

        # Allow local connections
        allow 127.0.0.1;

        # Deny the others
        deny all;
    }

    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
    #
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
    
        # With php7.0-fpm:
        fastcgi_pass unix:/run/php/php7.0-fpm.sock;
    }

    # deny access to sensitive files
    location ~ /\.ht {
        deny all;
    }
}
