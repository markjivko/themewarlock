<?php

/**
 * Theme Warlock - Input
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Installer {
    
    /**
     * Run the installer tasks, passing along the arguments
     * 
     * @param string $tool (optional) Tool to run
     */
    public static function run($tool = null) {
        // Prepare the tasksv
        $methods = array_map(
            function($item) {
                return $item->name;
            },
            array_filter((new ReflectionClass(__CLASS__))->getMethods(), function($item) {
                return 'run' !== $item->name && 0 !== strpos($item->name, '_');
            }) 
        );
        
        // No tool specified
        if (!in_array($tool, $methods)) {
            $tool = $methods[Console::options($methods, 'Please select an installer tool')];
        }
        
        // Run the action
        return call_user_func(array(__CLASS__, $tool));
    }

    /**
     * Prepare the user, group and alias
     */
    public static function alias() {
        Console::h1('Alias');
        if (0 === posix_getuid()) {
            throw new Exception('Alias installer must run as non-root');
        }
        
        // User/group not specified
        $updateConfig = false;
        if (!strlen(Config::get()->user)) {
            $updateConfig = true;
            Config::get()->user = trim(`whoami`);
        }
        if (!strlen(Config::get()->group)) {
            $updateConfig = true;
            $groups = explode(' ', trim(`id -Gn`));
            Config::get()->group = in_array(Config::get()->user, $groups)
                ? Config::get()->user
                : $groups[0];
        }
        if ($updateConfig) {
            file_put_contents(
                ROOT . '/web/config/config.ini', 
                preg_replace(
                    [
                        '%^user\s*=\s*[\'"].*?[\'"]%m',
                        '%^group\s*=\s*[\'"].*?[\'"]%m',
                    ],
                    [
                        'user = "' . Config::get()->user . '"',
                        'group = "' . Config::get()->group . '"',
                    ],
                    file_get_contents(ROOT . '/web/config/config.ini')
                )
            );
        }
        
        $bashAliasesFile = '/home/' . Config::get()->user . '/.bash_aliases';
        
        // Prepare the entry point path
        $rootPath = ROOT . '/index.php';

        // Get the app version
        $version = Config::get()->version;

        // Prepare the alias command
        $bashCommand = <<<"COM"
# Theme Warlock CLI v.$version
alias tw='php -f "$rootPath"'
COM;

        do {
            if (is_file($bashAliasesFile)) {
                $bashAliasesContent = file_get_contents($bashAliasesFile);
                if (preg_match('%\balias\s+tw\b%i', $bashAliasesContent)) {
                    $bashAliasesContent = preg_replace(
                        '%#\s*Theme\s*Warlock.*?\n\s*alias\s+tw\b.*?(?:\n|$)%ims',
                        $bashCommand,
                        $bashAliasesContent . PHP_EOL
                    );

                    // Update the file
                    file_put_contents($bashAliasesFile, $bashAliasesContent);
                    Console::p('Updated `tw` alias');
                    break;
                }
            }

            // Append the command to aliases
            if (false === $fh = @fopen($bashAliasesFile, 'a')) {
                throw new Exception('Could not open "' . $bashAliasesFile . '"');
            }
            fwrite($fh, PHP_EOL . $bashCommand);
            fclose($fh);
            passthru('chown ' . Config::get()->user . '.' . Config::get()->group . ' "' . $bashAliasesFile . '"');

            Console::p('Created `tw` alias');
        } while(false); 
    }

    /**
     * Set the correct user and group to apache config.<br/>
     * Set the correct owner to /var/www and /var/lib/phpmyadmin/*.<br/>
     * Load PHPMyAdmin configuration in apache.
     */
    public static function _apache() {
        Console::header('Apache & PHPMyAdmin');
        if (0 !== posix_getuid()) {
            throw new Exception('Apache installer must run as root');
        }

        // Validate dependencies
        if (!is_dir('/var/www')) {
            throw new Exception('Apache is not installed');
        }
        if (!is_dir('/var/lib/phpmyadmin')) {
            throw new Exception('PHPMyAdmin is not installed');
        }
        if (!is_dir('/etc/mysql')) {
            throw new Exception('MySQL is not installed');
        }

        // Folder ownership
        Console::p('Updating owners...');
        passthru('chown ' . Config::get()->user() . '.' . Config::get()->group() . ' -R /var/www');
        passthru('chown ' . Config::get()->user() . '.' . Config::get()->group() . ' -R /var/log/apache2');
        passthru('chown ' . Config::get()->user() . '.' . Config::get()->group() . ' -R /var/lib/phpmyadmin');
        passthru('chown ' . Config::get()->user() . '.' . Config::get()->group() . ' -R /etc/phpmyadmin');
        passthru('chown ' . Config::get()->user() . '.' . Config::get()->group() . ' -R /usr/share/phpmyadmin');

        // Hosts
        $hostsContent = file_get_contents('/etc/hosts');
        if (!preg_match('%127\.0\.0\.1\s+' . preg_quote(Config::get()->domainWp()) . '%i', $hostsContent)) {
            Console::p('Setting Faux Domain...');
            file_put_contents(
                '/etc/hosts',
                $hostsContent 
                    . PHP_EOL . PHP_EOL . '# Theme Warlock Faux Domain' 
                    . PHP_EOL . '127.0.0.1 ' . Config::get()->domainWp() 
                    . PHP_EOL . '127.0.0.1 ' . Config::get()->domainTest() 
            );
        }

        // Configure apache user and group
        Console::p('Updating Apache user and group...');
        $apacheConfig = preg_replace(
            array(
                '%export\s+APACHE_RUN_USER\s*=\s*[\w\-]+%i',
                '%export\s+APACHE_RUN_GROUP\s*=\s*[\w\-]+%i'
            ),
            array(
                'export APACHE_RUN_USER=' . Config::get()->user(),
                'export APACHE_RUN_GROUP=' . Config::get()->group()
            ),
            file_get_contents($apachePath = '/etc/apache2/envvars')
        );
        file_put_contents($apachePath, $apacheConfig);
        
        // Configure Apache wordpress host
        $apacheConfig = file_get_contents($apachePath = '/etc/apache2/sites-enabled/000-default.conf');
        if (!preg_match('%\/var\/www\/wordpress\b%', $apacheConfig)) {
            Console::p('Updating wordpress host...');
            file_put_contents(
                $apachePath, 
                preg_replace(
                    '%DocumentRoot\s+.*?\n%i',
                    'DocumentRoot /var/www/wordpress' . PHP_EOL,
                    $apacheConfig
                )
            );
        }

        // Configure PHPMyAdmin
        $apacheConfig = file_get_contents($apachePath = '/etc/apache2/apache2.conf');
        if (!preg_match('%\bphpmyadmin\b%i', $apacheConfig)) {
            Console::p('Updating phpmyadmin host...');
            file_put_contents(
                $apachePath, 
                $apacheConfig 
                    . PHP_EOL . PHP_EOL . '# PHPMyAdmin'
                    . PHP_EOL . 'Include /etc/phpmyadmin/apache.conf'
            );
        }
        
        // Update log rotate
        $logRotatecontent = file_get_contents($logRotatePath = '/etc/logrotate.d/apache2');
        if (!preg_match('%\bcreate 666%i', $logRotatecontent)) {
            Console::p('Updating log rotate permissions...');
            file_put_contents(
                $logRotatePath, 
                preg_replace(
                    '%\bcreate\s*\d+\s*(\w+\s+\w+)%i', 
                    'create 666 $1', 
                    $logRotatecontent
                )
            );
        }
        
        // Update umask
        $envVarsContent = file_get_contents($envVarsPath = '/etc/apache2/envvars');
        if (!preg_match('%\bumask 000\b%i', $envVarsContent)) {
            Console::p('Updating umask...');
            file_put_contents(
                $envVarsPath, 
                preg_replace(
                    '%\bumask\s*\d+%ims', 
                    '', 
                    $envVarsContent
                ) . PHP_EOL . 'umask 000'
            );
        }
        
        // Configure the test domain
        $apacheConfig = file_get_contents($apachePath = '/etc/apache2/sites-available/000-default.conf');
        if (!preg_match('%\b' . preg_quote(Config::get()->domainTest()) . '\b%i', $apacheConfig)) {
            Console::p('Adding preview host "' . Config::get()->domainTest() . '"...');
            file_put_contents(
                $apachePath,
                PHP_EOL . '<VirtualHost *:80>'
                    . PHP_EOL. '    ServerName ' . Config::get()->domainTest()
                    . PHP_EOL. '    DocumentRoot ' . Config::get()->outputPath()
                    . PHP_EOL. '    ServerAdmin webmaster@localhost'
                    . PHP_EOL. '    ErrorLog ${APACHE_LOG_DIR}/error.log'
                    . PHP_EOL. '    CustomLog ${APACHE_LOG_DIR}/access.log combined'
                    . PHP_EOL
                    . PHP_EOL. '    <Directory ' . Config::get()->outputPath() . '>'
                    . PHP_EOL. '        Require all granted'
                    . PHP_EOL. '    </Directory>'
                . PHP_EOL. '</VirtualHost>',
                FILE_APPEND
            );
        } else {
            if (!preg_match('%ServerName ' . preg_quote(Config::get()->domainTest()) . '\n\s+DocumentRoot\s+' . preg_quote(Config::get()->outputPath()) . '\b%ims', $apacheConfig)) {
                Console::p('Updating preview host "' . Config::get()->domainTest() . '"...');
                file_put_contents(
                    $apachePath,
                    preg_replace(
                        '%(ServerName ' . preg_quote(Config::get()->domainTest()) . '\n\s+DocumentRoot)\s+.*?(?=\n)%i', 
                        '$1 ' . Config::get()->outputPath(), 
                        $apacheConfig
                    )
                );
            }
        }

        // Elevate phpmyadmin
        $mysqli = new mysqli('localhost', 'root', '');
        if (!$mysqli->connect_error) {
            Console::p('Granting all privileges to phpmyadmin...');
            $mysqli->query("GRANT ALL PRIVILEGES ON *.* TO 'phpmyadmin'@'localhost';");
            $mysqli->query("FLUSH PRIVILEGES;");
        }

        // Restart Apache
        passthru('/etc/init.d/apache2 restart');
    }

    /**
     * Prepare the WordPress workarea
     */
    public static function _wordpress() {
        Console::header('WordPress');
        if (0 !== posix_getuid()) {
            throw new Exception('WordPress installer must run as root');
        }
        
        Console::p('Clean-up...');
        shell_exec('rm -rf /var/www/*');

        Console::p('Downloading the latest WordPress...');
        $archivePath = Temp::getPath(Temp::FOLDER_DOWN) . '/latest.zip';
        !Config::get()->cacheDownload() && is_file($archivePath) && unlink($archivePath);

        // Attempt to download
        if (!Config::get()->cacheDownload() || !is_file(Temp::getPath(Temp::FOLDER_DOWN) . '/latest.zip')) {
            passthru('wget -q --show-progress "https://wordpress.org/latest.zip" -P "' . Temp::getPath(Temp::FOLDER_DOWN) . '"');
        }
        passthru('chown ' . Config::get()->user() . '.' . Config::get()->group() . ' ' . escapeshellarg($archivePath));

        Console::p('Deploying WordPress...');
        passthru('su -c "unzip -o -qq \'' . Temp::getPath(Temp::FOLDER_DOWN) . '/latest.zip\' -d /var/www/" ' . Config::get()->user());
        !Config::get()->cacheDownload() && is_file($archivePath) && unlink($archivePath);

        Console::p('Installing WordPress...');
        Api::run('siteInstall');
        
        Console::p('Exporting database...');
        passthru('mysqldump -u ' . Config::get()->dbUser() . ' -p' . Config::get()->dbPass() . ' ' . Config::get()->dbName() . ' > ' . escapeshellarg($sqlPath = '/var/www/wordpress.sql') . ' 2>&1');
        passthru('chown ' . Config::get()->user() . '.' . Config::get()->group() . ' ' . escapeshellarg($sqlPath));
        passthru('printf "%s%s" "-- " "$(cat \'' . $sqlPath . '\')" > \'' . $sqlPath . '\'');

        Console::p('Initializing repo...');
        file_put_contents('/var/www/wordpress/.gitignore', 'wp-api.php');
        passthru('rm -rf /var/www/wordpress/.git');
        passthru('rm -rf /var/www/wordpress/wp-content/plugins/akismet');
        passthru('git init -q /var/www/wordpress');
        passthru('git -C /var/www/wordpress config --local user.email "stephino.team@gmail.com"');
        passthru('git -C /var/www/wordpress config --local user.name "Mark Jivko"');
        passthru('git -C /var/www/wordpress add -A');
        passthru('git -C /var/www/wordpress commit -q -m "first"');
        passthru('chown ' . Config::get()->user() . '.' . Config::get()->group() . ' -R ' . escapeshellarg('/var/www/wordpress'));

        // Initialize the admin pages list
        if (is_file($getPagesCache = '/var/www/wp-admin.json')) {
            unlink($getPagesCache);
        }
        Run_Plugin::getOptions(true);
        
        // Inform the user
        Console::info('You can now login at http://' . Config::get()->domainWp() . '/wp-admin with "' . Config::get()->siteUser() . '/' . Config::get()->siteUser() . '"');
    }
}

/*EOF*/