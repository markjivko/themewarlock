<?php
/**
 * Theme Warlock - Config_Items
 * 
 * @title      Configuration Items
 * @desc       Automatically Generated class containing configuration items descriptions; only used for code completion
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

abstract class Config_Items {
    
    /**
     * Version
     * 
     * @var string
     */
    public $version;
        
    /**
     * User; populated with `php -f index.php install alias`
     * 
     * @var string
     */
    public $user;
        
    /**
     * Group; populated with `php -f index.php install alias`
     * 
     * @var string
     */
    public $group;
        
    /**
     * Application operation mode
     * 
     * @var string
     * @allowed DEVELOPMENT, PRODUCTION
     */
    public $appMode;
        
    /**
     * Log level
     * 
     * @var string
     * @allowed DEBUG, INFO, WARNING, ERROR
     */
    public $logLevel;
        
    /**
     * Log file Maximum Size
     * 
     * @var size
     */
    public $logSize;
        
    /**
     * Log file maximum cache time in days
     * 
     * @var int
     */
    public $logCacheDays;
        
    /**
     * Database host
     * 
     * @var string
     */
    public $dbHost;
        
    /**
     * Database username
     * 
     * @var string
     */
    public $dbUsername;
        
    /**
     * Database password
     * 
     * @var string
     */
    public $dbPassword;
        
    /**
     * Database name
     * 
     * @var string
     */
    public $dbName;
        
    /**
     * Database salt
     * 
     * @var string
     */
    public $dbSalt;
        
    /**
     * Version modulo
     * 
     * @var int
     */
    public $versionModulo;
        
    /**
     * What's new text - version prefix
     * 
     * @var string
     */
    public $versionWhatsNewPrefix;
        
    /**
     * WordPress Database name
     * 
     * @var string
     */
    public $wpDbName;
        
    /**
     * WordPress sandbox path
     * 
     * @var string
     */
    public $wpPath;
        
    /**
     * WordPress sandbox version; auto-downloaded from WordPress.org
     * 
     * @var string
     * @allowed 5.5.1, 5.6, 5.7, 5.8
     */
    public $wpVersion;
        
    /**
     * Local server domain
     * 
     * @var string
     */
    public $myDomain;
        
    /**
     * Notifier User Filtering - Regular Expression
     * 
     * @var string
     */
    public $notifierUserFilter;
        
    /**
     * Notifier - Receiver e-mail address, usually the website admin
     * 
     * @var string
     */
    public $notifierAdminEmail;
        
    /**
     * Notifier SMTP Host
     * 
     * @var string
     */
    public $notifierSmtpHost;
        
    /**
     * Notifier SMTP Email
     * 
     * @var string
     */
    public $notifierSmtpEmail;
        
    /**
     * Notifier SMTP Password
     * 
     * @var string
     */
    public $notifierSmtpPassword;
        
    /**
     * Author name
     * 
     * @var string
     */
    public $authorName;
        
    /**
     * Author URL
     * 
     * @var string
     */
    public $authorUrl;
        
    /**
     * Common Themes URL for this author
     * 
     * @var string
     */
    public $authorThemesUrl;
        
    /**
     * API Key for communicating with the Live Preview website(s); no single-quotes!
     * 
     * @var string
     */
    public $authorThemesApiKey;
        
    /**
     * Author e-mail
     * 
     * @var string
     */
    public $authorEmail;
        
    /**
     * Author address
     * 
     * @var string
     */
    public $authorAddress;
        
    /**
     * Author Phone Number
     * 
     * @var string
     */
    public $authorPhone;
        
    /**
     * Copyright year
     * 
     * @var string
     */
    public $authorYear;
        
    /**
     * Marketplace IDs for the current author, comma separated
Example: ThemeForest:Author,Mojo:Author
     * 
     * @var string
     */
    public $authorMarketIds;
        
    /**
     * The Google Maps API Key
     * 
     * @var string
     */
    public $apiKeyGoogleMaps;
        
    /**
     * The reCAPTCHA Site Key
     * 
     * @var string
     */
    public $apiKeyReCaptchaSite;
        
    /**
     * The reCAPTCHA Secret Key
     * 
     * @var string
     */
    public $apiKeyReCaptchaSecret;
    
}

/*EOF*/