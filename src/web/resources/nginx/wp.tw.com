# Default server configuration
#
server {
    listen 80;
    listen [::]:80;

    # Add index.php to the list if you are using PHP
    index index.php index.html index.htm;

    # Errors are handled by PHP
    error_page 404 index.php;

    # No indexes
    autoindex off;

    server_name "~^wp(?P<sub>(\-u\d+)?)\.tw\.com$";
    root "/home/stephino/projects/wordpress$sub";

    location / {
        # Attempt files, folders or 404
        try_files $uri $uri/ /index.php?$args;

        # Allow local connections
        allow 127.0.0.1;
        allow 192.168.0.0/16;

        # Also allow authenticated users
        include /home/stephino/projects/tw/web/temp/wp-ip-filter/*;

        # Deny the others
        deny all;
    }

    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
    #
    location ~ \.php$ {
            # Allow iFrames
            add_header "Content-Security-Policy" "frame-src *;";
            add_header "Access-Control-Allow-Origin" "$http_origin";
            add_header "Access-Control-Allow-Credentials" "true";

            include snippets/fastcgi-php.conf;

            # With php7.0-fpm:
            fastcgi_pass unix:/run/php/php7.0-fpm.sock;
    }

    # deny access to sensitive files
    location ~ /\.ht {
        deny all;
    }
}
