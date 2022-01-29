<?php
/**
 * {Plugin.getNameClass}
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

class {Plugin.getNameClass}Tweets {

    // Tweets cache keys
    const CACHE_KEY = '{project.prefix}_{Plugin.getNameVar}_tweets';
    
    /**
     * Consumer Api Key
     * 
     * @see https://dev.twitter.com
     * @var string
     */
    protected $_consumerKey = '';
    
    /**
     * Consumer Secret Key
     * 
     * @see https://dev.twitter.com
     * @var string
     */
    protected $_consumerSecret = '';
    
    /**
     * Access Token
     * 
     * @see https://dev.twitter.com
     * @var string
     */
    protected $_accessToken = '';
    
    /**
     * Access Token Secret
     * 
     * @see https://dev.twitter.com
     * @var string
     */
    protected $_accessTokenSecret = '';
    
    /**
     * Cache time (in seconds)
     * 
     * @var int
     */
    protected $_cacheTime = 3600;
    
    /**
     * {Plugin.getNameClass}Tweets
     * 
     * @param string $consumerKey       Consumer Key
     * @param string $consumerSecret    Consumer Secret
     * @param string $accessToken       Access Token
     * @param string $accessTokenSecret Access Token Secret
     * @param int    $cacheTime         Cache time (in seconds)
     * @return {Plugin.getNameClass}Tweets
     */
    public function __construct($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $cacheTime = 3600) {
        // Store the Twitter Keys
        $this->_consumerKey = trim($consumerKey);
        $this->_consumerSecret = trim($consumerSecret);
        
        // Store the OAuth Access Token
        $this->_accessToken = trim($accessToken);
        $this->_accessTokenSecret = trim($accessTokenSecret);
        
        // Store the cache time
        $this->_cacheTime = $cacheTime;

        // Require TwitterOAuth
        require_once dirname(__FILE__) . '/twitteroauth/TwitterOAuth.php';

        // Content type
        header('content-type:text/plain;charset=utf-8');
    }

    /**
     * Get the tweets from cache
     * 
     * @return array|null Valid Tweets array or null on error
     */
    protected function _cacheGet() {
        // Prepare the result
        $result = null;
        
        // Caching enabled
        if ($this->_cacheTime > 0) {
            // Get the JSON data
            $jsonData = get_option(self::CACHE_KEY, false);

            // Value stored in cache
            if (false !== $jsonData) {
                // Decode the cached info
                $jsonArray = json_decode($jsonData, true);
                
                // Valid result
                if (is_array($jsonArray) && 2 === count($jsonArray)) {
                    // Get the cached data and time
                    list($cachedData, $cachedTime) = array_values($jsonArray);
                    
                    // Sanitize the time
                    $cachedTime = intval($cachedTime);

                    // Valid time provided
                    if ($cachedTime > 0) {
                        // Tweets are still valid
                        if (time() - $cachedTime <= $this->_cacheTime) {
                            // The cached data appears valid
                            if (is_array($cachedData)) {
                                $result = $cachedData;
                            }
                        }
                    }
                }
            }
        }
        
        // Invalid cache
        return $result;
    }
    
    /**
     * Store the tweets to cache
     */
    protected function _cacheSet($tweetsArray) {
        if (is_array($tweetsArray)) {
            update_option(
                self::CACHE_KEY, 
                json_encode(
                    array(
                        $tweetsArray, 
                        time()
                    )
                )
            );
        }
    }
    
    /**
     * Echo the latest tweets in JSON format
     * 
     * @param string  $twitterUsername   Twitter username
     * @param int     $noOfTweets        Number of tweets you would like to display
     * @param boolean $ignoreReplies     Ignore replies from the timeline
     * @param boolean $includeRts        Include retweets
     * @param boolean $twitterStyleDates Twitter style days. [about an hour ago]
     * @param string  $dateFormat        Date formatting (http://php.net/manual/en/function.date.php)
     */
    public function run($twitterUsername = {addon.defUsername}, $noOfTweets = 8, $ignoreReplies = false, $includeRts = true, $twitterStyleDates = true, $dateFormat = 'g:i A M jS') {
        // Validate the keys
        foreach (array($this->_consumerKey, $this->_consumerSecret, $this->_accessToken, $this->_accessTokenSecret) as $keyToCheck) {
            if (!strlen($keyToCheck)) {
                return '[]';
            }
        }
        
        // Get the JSON from cache
        $tweetsArray = $this->_cacheGet();
        
        // Show cached version of tweets, if it's less than $this->_cacheTime.
        if (null === $tweetsArray) {
            // Get the tweets
            $tweetsArray = array();
                
            // Cache file not found, or old. Authenticate app.
            $connection = new TwitterOAuth($this->_consumerKey, $this->_consumerSecret, $this->_accessToken, $this->_accessTokenSecret);

            // Get the latest tweets from Twitter
            $query = http_build_query(array(
                'screen_name'     => $twitterUsername,
                'count'           => $noOfTweets,
                'include_rts'     => $includeRts,
                'exclude_replies' => $ignoreReplies,
            ));
            $getTweets = $connection->get('https://api.twitter.com/1.1/statuses/user_timeline.json?' . $query);

            // Error check: Make sure there is at least one item.
            if (count($getTweets) && (!isset($getTweets['errors']) || !count($getTweets['errors']))) {
                // Define tweet_count as zero
                $tweetCount = 0;

                // Iterate over tweets.
                foreach ($getTweets as $tweet) {
                    // Get the twitter description
                    $tweetDesc = html_entity_decode($tweet['text']);

                    // Add hyperlink html tags to any urls, twitter ids or hashtags in the tweet.
                    $tweetDesc = preg_replace("/https?:\/\/[^<>\s]+/i", '<a rel="nofollow" href="$0" target="_blank">$0</a>', $tweetDesc);
                    $tweetDesc = preg_replace("/@([a-z0-9_]+)/i", '<a rel="nofollow" href="https://twitter.com/$1" target="_blank">$0</a>', $tweetDesc);
                    $tweetDesc = preg_replace("/#([a-z0-9_\-]+)/i", '<a rel="nofollow" href="https://twitter.com/search?q=%23$1" target="_blank">$0</a>', $tweetDesc);

                    // Convert Tweet display time to a UNIX timestamp. Twitter timestamps are in UTC/GMT time.
                    $tweetTime = strtotime($tweet['created_at']);
                    if ($twitterStyleDates) {
                        // Get the time diff
                        $timeDiff = abs(time() - $tweetTime);
                        switch ($timeDiff) {
                            case ($timeDiff < 60):
                                $displayTime = $timeDiff . ' second' . ($timeDiff == 1 ? '' : 's') . ' ago';
                                break;
                            case ($timeDiff >= 60 && $timeDiff < 3600):
                                $min = floor($timeDiff / 60);
                                $displayTime = $min . ' minute' . ($min == 1 ? '' : 's') . ' ago';
                                break;
                            case ($timeDiff >= 3600 && $timeDiff < 86400):
                                $hour = floor($timeDiff / 3600);
                                $displayTime = 'about ' . $hour . ' hour' . ($hour == 1 ? '' : 's') . ' ago';
                                break;
                            default:
                                $displayTime = date($dateFormat, $tweetTime);
                                break;
                        }
                    } else {
                        $displayTime = date($dateFormat, $tweetTime);
                    }

                    // Render the tweet.
                    if ($tweetDesc) {
                        $tweetsArray[] = array(
                            'desc' => $tweetDesc,
                            'time' => $displayTime
                        );
                    }

                    // If we have processed enough tweets, stop.
                    if (++$tweetCount >= $noOfTweets) {
                        break;
                    }
                }

                // Save to cache
                $this->_cacheSet($tweetsArray);
            }
        }
        
        // Output the result
        return json_encode($tweetsArray);
    }
}

// Element Class 
class {project.prefix}_{Plugin.getNameVar} extends WPBakeryShortCode {
     
    // Element Init
    function __construct() {
        // VC MApping
        add_action('init', array($this, 'vc_infobox_mapping'), 12);
        add_action('wp_enqueue_scripts', array($this, 'vc_scripts'));
        add_shortcode('{project.prefix}_{Plugin.getNameVar}', array($this, 'vc_infobox_html'));
        
        // AJAX form
        add_action('wp_ajax_nopriv_st_{Plugin.getNameVar}_get_tweets', array($this, 'ajaxHandler'));
        add_action('wp_ajax_st_{Plugin.getNameVar}_get_tweets', array($this, 'ajaxHandler'));
        
        // Customizer
        add_action('customize_register', array($this, 'customizer'));
    }
    
    /**
     * Store the API information in a private place
     * 
     * Note: These settings are theme-specific, so changing the theme requires 
     * re-setting the Twitter Credentials
     */
    public function customizer($wp_customize) {
        // Add the '{addon.title}' section
        $wp_customize->add_section('st_section_plugin_{Plugin.getNameVar}', array(
            'priority'       => 160,
            'panel'          => function_exists('{project.prefix}_setup') ? {call.core.getThemePanel.getId} : '',
            'title'          => esc_html__({addon.title}, '{project.destDir}'),
            'description'    => esc_html__({addon.description}, '{project.destDir}') . ' - <a href="//dev.twitter.com" target="_blank">' . esc_html__('Get your Twitter API Keys', '{project.destDir}') . '</a>',
            'capability'     => 'edit_theme_options',
            'theme_supports' => '',
        ));

        // Prepare the list
        $optionsList = array(
            'api_key'                 => esc_html__('Twitter API Key', '{project.destDir}'),
            'api_key_secret'          => esc_html__('Twitter API Key Secret', '{project.destDir}'),
            'api_access_token'        => esc_html__('Twitter Access Token', '{project.destDir}'),
            'api_access_token_secret' => esc_html__('Twitter Access Token Secret', '{project.destDir}'),
        );
        
        // Store the options
        foreach ($optionsList as $optionKey => $optionTitle) {
            // Setting
            $wp_customize->add_setting('st_setting_plugin_{Plugin.getNameVar}_' . $optionKey, array(
                'default'           => '',
                'transport'         => 'refresh',
                'sanitize_callback' => 'wp_filter_nohtml_kses',
            ));

            // Control
            $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'st_setting_plugin_{Plugin.getNameVar}_' . $optionKey, array(
                'section'     => 'st_section_plugin_{Plugin.getNameVar}',
                'type'        => 'text',
                'label'       => esc_html__($optionTitle, '{project.destDir}'),
                'description' => '',
            )));
        }
    }
    
    /**
     * Handle the Tweets request
     */
    public function ajaxHandler() {
        // Get the Twitter username
        $userName = isset($_REQUEST['username']) ? strval($_REQUEST['username']) : {addon.defUsername};
        if (!preg_match('%^[\w\-]{1,20}$%i', $userName)) {
            $userName = {addon.defUsername};
        }
        
        // Get the cache time in seconds; default 1 day
        $cacheTime = isset($_REQUEST['cache_hours']) ? (intval($_REQUEST['cache_hours']) * 3600) : 86400;
        
        // Cache time between 0 and 48 hours, expressed in seconds
        $cacheTime = $cacheTime < 0 ? 0 : ($cacheTime > 172800 ? 172800 : $cacheTime);
        
        // Get the tweets count
        $tweetsCount = isset($_REQUEST['tweets_count']) ? intval($_REQUEST['tweets_count']) : 8;
        
        // Tweets count between 1 and 15
        $tweetsCount = $tweetsCount < 1 ? 1 : ($tweetsCount > 15 ? 15 : $tweetsCount);
        
        // Get the "ignore replies" flag
        $ignoreReplies = isset($_REQUEST['ignore_replies']) ? ('true' == $_REQUEST['ignore_replies']) : false;
        
        // Get the "include retweets" flag
        $includeRetweets = isset($_REQUEST['include_retweets']) ? ('true' == $_REQUEST['include_retweets']) : true;
        
        // Get the "style timestamp" flag
        $styleTimestamp = isset($_REQUEST['style_timestamp']) ? ('true' == $_REQUEST['style_timestamp']) : true;
        
        // Run the tweets class
        $twitterClient = new {Plugin.getNameClass}Tweets(
            get_theme_mod('st_setting_plugin_{Plugin.getNameVar}_api_key', ''),                 // Api Key
            get_theme_mod('st_setting_plugin_{Plugin.getNameVar}_api_key_secret', ''),          // Api Secret Key
            get_theme_mod('st_setting_plugin_{Plugin.getNameVar}_api_access_token', ''),        // Access Token
            get_theme_mod('st_setting_plugin_{Plugin.getNameVar}_api_access_token_secret', ''), // Access Token Secret
            $cacheTime                                                                                   // Cache Time
        );

        // Output the result
        echo $twitterClient->run(
            $userName,        // Twitter Username
            $tweetsCount,     // Number of tweets you would like to display
            $ignoreReplies,   // Ignore replies from the timeline
            $includeRetweets, // Include retweets
            $styleTimestamp   // Twitter style days. [about an hour ago]
        );
        
        // Prevent any other output
        exit();
    }
    
    // Element Mapping
    public function vc_infobox_mapping() {
        // Stop all if VC is not enabled
        if (!defined('WPB_VC_VERSION')) {
            return;
        }
        
        // Prepare the cache hours
        $cacheHours = array(
            esc_html__('Disabled', '{project.destDir}') => 0
        );
        
        // Add the options
        foreach (array(1, 6, 12, 24, 48) as $hours) {
            $cacheHours[sprintf(_n('%d hour', '%d hours', $hours, '{project.destDir}'), $hours)] = $hours;
        }
        
        // Tweets to display
        $tweetsCount = array();
        for($tweets = 1; $tweets <= 15; $tweets++) {
            $tweetsCount[sprintf(_n('%d tweet', '%d tweets', $tweets, '{project.destDir}'), $tweets)] = $tweets;
        }

        // Prepare the warning
        $setKeysWarning = '';
        if (!strlen(get_theme_mod('st_setting_plugin_{Plugin.getNameVar}_api_key', ''))) {
            $setKeysWarning = '<br/> <a href="' . esc_url(admin_url('customize.php' )) . '" target="_blank">' . esc_html__('In order for this plugin to work you must first set your Twitter API keys!', '{project.destDir}') . '</a>';
        }
        
        // Map the block with vc_map()
        vc_map( 
            array(
                'name' => '{project.destProjectName}: ' . esc_html__({addon.title}, '{project.destDir}'),
                'base' => '{project.prefix}_{Plugin.getNameVar}',
                'description' => esc_html__({addon.description}, '{project.destDir}'),
                'category' => '{project.destProjectName}',   
                'icon' => plugins_url() . '/{Call.core.getVcBundleName}/{Plugin.getSlug}/vc-elements/icon.png',   
                'admin_enqueue_js' => plugins_url() . '/{Call.core.getVcBundleName}/{Plugin.getSlug}/js/functions.js',
                'front_enqueue_js' => plugins_url() . '/{Call.core.getVcBundleName}/{Plugin.getSlug}/js/functions.js',
                'params' => array(

                    array(
                        'type' => 'textfield',
                        'holder' => 'h3',
                        'class' => 'title-class',
                        'heading' => esc_html__('Title', '{project.destDir}'),
                        'param_name' => 'title',
                        'value' => esc_html__({addon.defSectTitle}, '{project.destDir}'),
                        'description' => esc_html__('Section Title', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Options', '{project.destDir}'),
                    ), 
                    
                    array(
                        'type' => 'textfield',
                        'holder' => 'h3',
                        'class' => 'title-class',
                        'heading' => esc_html__('Twitter username', '{project.destDir}'),
                        'param_name' => 'username',
                        'value' => {addon.defUsername},
                        'description' => esc_html__('Set the Twitter handle who\'s tweets you want to display', '{project.destDir}') . $setKeysWarning,
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Options', '{project.destDir}'),
                    ),  
                    
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Caching', '{project.destDir}'),
                        'param_name' => 'cache_hours',
                        'value' => $cacheHours,
                        'std' => 24,
                        'description' => esc_html__('Limit API request by caching your tweets', '{project.destDir}'),
                        'group' => esc_html__('Options', '{project.destDir}'),
                    ),
                    
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Number of tweets', '{project.destDir}'),
                        'param_name' => 'tweets_count',
                        'value' => $tweetsCount,
                        'std' => 8,
                        'description' => esc_html__('Set the number of tweets to display', '{project.destDir}'),
                        'group' => esc_html__('Options', '{project.destDir}'),
                    ),
                    
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Slider speed', '{project.destDir}'),
                        'param_name' => 'slider_speed',
                        'value' => array(
                            esc_html__('Very fast', '{project.destDir}') => 2000,
                            esc_html__('Fast', '{project.destDir}') => 3000,
                            esc_html__('Normal', '{project.destDir}') => 4000,
                            esc_html__('Slow', '{project.destDir}') => 5000,
                            esc_html__('Very slow', '{project.destDir}') => 6000,
                        ),
                        'std' => 4000,
                        'description' => esc_html__('Set slider speed', '{project.destDir}'),
                        'group' => esc_html__('Options', '{project.destDir}'),
                    ),
                    
                    array(
                        'type' => 'checkbox',
                        'heading' => esc_html__('Ignore replies', '{project.destDir}'),
                        'param_name' => 'ignore_replies',
                        'description' => esc_html__('Ignore the replies from the timeline', '{project.destDir}'),
                        'group' => esc_html__('Options', '{project.destDir}'),
                        'std' => '',
                    ),
                    
                    array(
                        'type' => 'checkbox',
                        'heading' => esc_html__('Include retweets', '{project.destDir}'),
                        'param_name' => 'include_retweets',
                        'std' => 'true',
                        'description' => esc_html__('Include retweets from the timeline', '{project.destDir}'),
                        'group' => esc_html__('Options', '{project.destDir}'),
                    ),
                    
                    array(
                        'type' => 'checkbox',
                        'heading' => esc_html__('Style timestamp', '{project.destDir}' ),
                        'param_name' => 'style_timestamp',
                        'std' => 'true',
                        'description' => esc_html__('Use the Twitter style for the Tweet timestamp', '{project.destDir}'),
                        'group' => esc_html__('Options', '{project.destDir}'),
                    ),
                    
                    array(
                        'type' => 'attach_image',
                        'heading' => esc_html__('Background image', '{project.destDir}' ),
                        'param_name' => 'background',
                        'value' => plugins_url() . '/{Call.core.getVcBundleName}/{Plugin.getSlug}/img/background.jpg',
                        'description' => esc_html__('Set the plugin\'s background image', '{project.destDir}'),
                        'group' => esc_html__('Style', '{project.destDir}'),
                    ),
{if.core.useStoryline}
                    array(
                        'type' => 'checkbox',
                        'heading' => esc_html__('Append to menu', '{project.destDir}'),
                        'param_name' => 'storyline_append_to_menu',
                        'description' => esc_html__('Append this block to the "Dynamic menu", if it was enabled in "Customizer - Menu area"', '{project.destDir}'),
                        'group' => 'StoryLine.js',
                        'std' => 'true',
                    ),
{/if.core.useStoryline}
                ),
            )
        );                                
    }
     
    // Add the scripts
    public function vc_scripts() {
        {utils.common.enqueueScripts}
        
        // Prepare the script URL
        $scriptUrl = plugins_url() . '/{Call.core.getVcBundleName}/{Plugin.getSlug}';
        
        // Prepare the CSS scripts
        $cssScripts = array(
            '{project.destDir}-{Plugin.getSlug}-style' => array('style', {plugin.getVersion}),
        );

        // Enqueue the CSS
        foreach ($cssScripts as $cssScriptName => $cssScriptData) {
            list($cssScriptFile, $cssScriptVersion) = $cssScriptData;
            wp_enqueue_style($cssScriptName, $scriptUrl . '/css/' . $cssScriptFile . '.css', array(), $cssScriptVersion);
        }

        // Prepare the JS scripts
        $jsScripts = array(
            '{project.destDir}-{Plugin.getSlug}-functions' => array('functions', {plugin.getVersion}),
        );

        // Enqueue the JS
        foreach ($jsScripts as $jsScriptName => $jsScriptData) {
            list($jsScriptFile, $jsScriptVersion) = $jsScriptData;
            wp_enqueue_script(
                $jsScriptName, 
                preg_match('%^https?\:\/\/%', $jsScriptFile) ? $jsScriptFile : $scriptUrl . '/js/' . $jsScriptFile . '.js', 
                array(), 
                $jsScriptVersion,
                true
            );
        }
    }
     
    // Element HTML
    public function vc_infobox_html($atts) {
        // Params extraction
        extract(
            $atts = shortcode_atts(
                vc_map_get_defaults('{project.prefix}_{Plugin.getNameVar}'), 
                $atts
            )
        );

        // Numeric value given for the background
        if (is_numeric($background)) {
            // Get the Image data
            $backgroundData = wp_get_attachment_image_src($background, 'full');
            
            // Valid attachment found
            $background = is_array($backgroundData) ? current($backgroundData) : '';
        }
                    
        // Prepare the placeholder
        $placeholder = strlen($username) ? 
            sprintf(esc_html__('The latest tweets from "%s" will be shown here...', '{project.destDir}'), $username) : 
            esc_html__('Please start by setting the desired Twitter username', '{project.destDir}');
        
        // Prepare the holder
        $html = '<div ' . ($storyline_append_to_menu ? '' : ' data-storyline-unlisted="true"') . ' class="{Plugin.getSlug} row no-gutters d-flex" data-name="' . htmlspecialchars($title) . '" style="background-image: url(' . esc_html($background) . ');">' .
            '<div class="col-12 offset-0 col-sm-10 offset-sm-1 h-100">' .
                '<div class="row align-items-center h-100">';
              
        // Add the tweets holder
        $html .= '<div class="tweets" ' . 
            'data-loading="' . esc_attr__('Loading...', '{project.destDir}') . '" ' .
            'data-no-tweets="' . esc_attr__('Twitter API keys not set or no tweets found...', '{project.destDir}') . '" ' .
            'data-username="' . esc_attr($username) . '" ' . 
            'data-speed="' . esc_attr($slider_speed) . '" ' . 
            'data-cache="' . $cache_hours . '" ' . 
            'data-count="' . $tweets_count . '" ' . 
            'data-ignore-replies="' . ($ignore_replies ? 'true' : 'false') . '" ' . 
            'data-include-retweets="' . ($include_retweets ? 'true' : 'false') . '" ' . 
            'data-style-timestamp="' . ($style_timestamp ? 'true' : 'false') . '" ' . 
            'data-action="' . admin_url('admin-ajax.php?action=st_{Plugin.getNameVar}_get_tweets') . '">' . 
                '<h2>' . $placeholder . '</h2>' . 
        '</div>';
        
        // Close the holders
        $html .= '</div></div></div>';
        
        // All done
        return $html;
    }
     
}

// Element Class Init
new {project.prefix}_{Plugin.getNameVar}();

/*EOF*/