<?php
/*
Plugin Name: UK Food Hygiene Rating
Plugin URI: https://wordpress.org/plugins/uk-food-hygiene-rating
Description: Show UK Food Agency hygiene rating on your wordpress blog posts
Version: 1.0.0
Author: runawaycoin
Author URI: http://runawaycoin.com
Text Domain: uk-food-hygiene-rating
*/

include WP_PLUGIN_DIR.'/'.plugin_basename( dirname(__FILE__) ).'/'.'options.php';

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function food_shortcodes_init() {
    function get_data($d1,$d2) {

        $d1 = html_entity_decode($d1);
        $d1 = urlencode($d1);
        $url = "http://api.ratings.food.gov.uk/Establishments?name=".$d1."&address=".$d2."&pageNumber=1&pageSize=1";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('x-api-version: 2'));
        $result=curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    function food_shortcode($atts = [], $content = null) {
        $defaults = [];
        $defaults['ukfhr_inspected_text'] = 1;
        $defaults['ukfhr_rating_text'] = 1;
        $defaults['ukfhr_link'] = 1;
        $defaults['ukfhr_image_size'] = 'large';
        $options = wp_parse_args(get_option('ukfhr_settings'), $defaults);

        $addresses = explode(',', $options['ukfhr_locations']);

        $ratingTexts = [
            "0 Urgent improvement necessary",
            "1 Marjor improvement necessary",
            "2 Improvement necessary",
            "3 Generally Satisfactory",
            "4 Good",
            "5 Very good"
        ];

        if (isset($atts['business'])) {
            $searchBusinesName = $atts['business'];
        } else {
            $searchBusinesName = get_the_title();
        }

        foreach ($addresses as $address) {
            $result = get_data($searchBusinesName,$address);
            if (!isset($result->establishments) || count($result->establishments)!=1){
                break;
            }
        }

        // still no luck - end
        if (!isset($result->establishments) || count($result->establishments)!=1){
            return "";
        }

        $ratingValue = $result->establishments[0]->RatingValue;
        $ratingKey = $result->establishments[0]->RatingKey;
        $businessName = $result->establishments[0]->BusinessName;
        $ratingDate = date('j F Y',strtotime($result->establishments[0]->RatingDate));
        $id = $result->establishments[0]->FHRSID;

        $img = "<img src=\"".plugins_url( 'images/'.$options['ukfhr_image_size'].'/72ppi/'.$ratingKey.'.jpg', __FILE__ ). "\" />";

        $text = '';
        if ( $options['ukfhr_rating_text'] == 1 ){
            $text .= "<br>Food hygiene rating: ".$ratingTexts[$ratingValue];
        }
        if ( $options['ukfhr_inspected_text'] == 1 ){
            $text .= "<br>Inspected on ".$ratingDate." by Foods Standards Agency";
        }

        if ( $options['ukfhr_link'] == 1 ){
            return "<p><a href=\"http://ratings.food.gov.uk/business/en-GB/".$id."/\" target=\"_blank\" >".$img.$text."</a></p>";
        } else {
            return "<p>".$img.$text."</a></p>";
        }

    }
    add_shortcode('food', 'food_shortcode');
}
add_action('init', 'food_shortcodes_init');


function plugin_action_links( $links ) {
    $links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=uk-food-hygiene-rating') ) .'">Settings</a>';
    return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'plugin_action_links' );
?>