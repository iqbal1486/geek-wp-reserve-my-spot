<?php
    /**
     * Plugin Name:       WP Reserve My Spot
     * Plugin URI:        https://profiles.wordpress.org/iqbal1486/
     * Description:       Reverse Engineer to 'reserve my spot' functionality
     * Version:           1.0.0
     * Requires at least: 5.2
     * Requires PHP:      7.2
     * Author:            Geekerhub
     * Author URI:        https://profiles.wordpress.org/iqbal1486/
     * License:           GPL v2 or later
     * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
     * Text Domain:       wp-resplit
     * Domain Path:       /languages
     */

    global $wprms_options;
   if ( ! defined( 'ABSPATH' ) ) {
        exit( 'restricted access' );
    }

    define( 'WPRMS_VERSION', '1.0.0' );

    if (! defined('WPRMS_ADMIN_URL') ) {
        define('WPRMS_ADMIN_URL', get_admin_url());
    }

    if (! defined('WPRMS_PLUGIN_FILE') ) {
        define('WPRMS_PLUGIN_FILE', __FILE__);
    }

    if (! defined('WPRMS_PLUGIN_PATH') ) {
        define('WPRMS_PLUGIN_PATH', plugin_dir_path(WPRMS_PLUGIN_FILE));
    }

    if (! defined('WPRMS_PLUGIN_URL') ) {
        define('WPRMS_PLUGIN_URL', plugin_dir_url(WPRMS_PLUGIN_FILE));
    }

    function wprms_reserve_my_spot_plugin_loaded_callback() {
        require WPRMS_PLUGIN_PATH . 'includes/Twilio/autoload.php';
        require WPRMS_PLUGIN_PATH . 'includes/simpleencryptor.php';
        require_once WPRMS_PLUGIN_PATH . 'includes/functions.php';
        require_once WPRMS_PLUGIN_PATH . 'admin/settings.php';
        require_once WPRMS_PLUGIN_PATH . 'public/shortcode.php';
        require_once WPRMS_PLUGIN_PATH . 'lightbox/lightbox-shortcode.php';

    }

    function wprms_generate_log( $data = "" ){
        $file = WPRMS_PLUGIN_PATH.'debug.log';
        $fileContents = file_get_contents($file);
        $time = "Time : ".date("F j, Y, g:i a")."\n";

        if( is_array($data)){
            $data = print_r($data, true);
        }

        $data = $time.$data."\n***************************************\n";
        file_put_contents($file, $data . $fileContents);
    }
    add_action('plugins_loaded', 'wprms_reserve_my_spot_plugin_loaded_callback');

    function wprms_load_global_options(){
        global $wprms_options;
        $wprms_options = get_fields('options');
    }
    add_action('init', 'wprms_load_global_options');

    function getTimeZoneFromIpAddress(){
        $clientsIpAddress = wprms_client_ip_address();

        $clientInformation = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$clientsIpAddress));

        $clientsLatitude = $clientInformation['geoplugin_latitude'];
        $clientsLongitude = $clientInformation['geoplugin_longitude'];
        $clientsCountryCode = $clientInformation['geoplugin_countryCode'];

        $timeZone = wprms_get_nearest_timezone($clientsLatitude, $clientsLongitude, $clientsCountryCode) ;

        return $timeZone;
    }

    function wprms_client_ip_address() {
        $ipaddress = '';
        if ($_SERVER['HTTP_X_REAL_IP']) {
            $ipaddress = $_SERVER['HTTP_X_REAL_IP'];
        } else if ($_SERVER['HTTP_CLIENT_IP']) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if($_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if($_SERVER['HTTP_X_FORWARDED']) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if(getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if($_SERVER['HTTP_FORWARDED']) {
           $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if($_SERVER['REMOTE_ADDR']){ 
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }

    function wprms_get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
        $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
            : DateTimeZone::listIdentifiers();

        if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

            $time_zone = '';
            $tz_distance = 0;

            //only one identifier?
            if (count($timezone_ids) == 1) {
                $time_zone = $timezone_ids[0];
            } else {

                foreach($timezone_ids as $timezone_id) {
                    $timezone = new DateTimeZone($timezone_id);
                    $location = $timezone->getLocation();
                    $tz_lat   = $location['latitude'];
                    $tz_long  = $location['longitude'];

                    $theta    = $cur_long - $tz_long;
                    $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat)))
                        + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                    $distance = acos($distance);
                    $distance = abs(rad2deg($distance));
                    // echo '<br />'.$timezone_id.' '.$distance;

                    if (!$time_zone || $tz_distance > $distance) {
                        $time_zone   = $timezone_id;
                        $tz_distance = $distance;
                    }

                }
            }
            return  $time_zone;
        }
        return 'unknown';
    }

    function wprms_wp_footer_callback(){
        ?>
        <script type="text/javascript" src="https://cdn.addevent.com/libs/atc/1.6.1/atc.min.js" async defer></script>
        <?php
    }
    add_action('wp_head', 'wprms_wp_footer_callback');