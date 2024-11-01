<?php
add_action( 'admin_menu', 'ukfhr_add_admin_menu' );
add_action( 'admin_init', 'ukfhr_settings_init' );

function ukfhr_add_admin_menu(  ) {
    add_options_page( 'UK Food Hygiene Rating', 'UK Food Hygiene Rating', 'manage_options', 'uk-food-hygiene-rating', 'ukfhr_options_page' );
}

function ukfhr_settings_init() {
    register_setting( 'ukfhr_page', 'ukfhr_settings' );

    add_settings_section(
        'ukfhr_pluginPage_section',
        'Settings',
        'ukfhr_settings_section_callback',
        'ukfhr_page'
    );

    add_settings_field(
        'ukfhr_locations',
        'Filter by Addresses',
        'ukfhr_locations_render',
        'ukfhr_page',
        'ukfhr_pluginPage_section'
    );

    add_settings_field(
        'ukfhr_link',
        'Include link to food.gov.uk',
        'ukfhr_link_render',
        'ukfhr_page',
        'ukfhr_pluginPage_section'
    );

    add_settings_field(
        'ukfhr_rating_text',
        'Include the rating text under the image',
        'ukfhr_rating_render',
        'ukfhr_page',
        'ukfhr_pluginPage_section'
    );

    add_settings_field(
        'ukfhr_inspected_text',
        'Include the inspected date text under the image',
        'ukfhr_inspected_render',
        'ukfhr_page',
        'ukfhr_pluginPage_section'
    );

    add_settings_field(
        'ukfhr_image_size',
        'Rating image size',
        'ukfhr_image_size_render',
        'ukfhr_page',
        'ukfhr_pluginPage_section',
        array( 'label_for' => 'ukfhr_image_size-id' )
    );
}


function ukfhr_locations_render() {
    $options = get_option( 'ukfhr_settings' );
    ?>
    <input type='text' name='ukfhr_settings[ukfhr_locations]' value='<?php echo $options['ukfhr_locations']; ?>'>
    <br>
    Comma seperated list of addresses to restrict the search to, ie towns, roads etc.
    <?php
}

function ukfhr_link_render() {
    $options = get_option( 'ukfhr_settings' );
    if (!isset($options['ukfhr_link'])){
        $options['ukfhr_link'] = 1;
    }

    ?>
    <label for="ukfhr_link_yes">Yes</label>
    <input type='radio' id='ukfhr_link_yes' name='ukfhr_settings[ukfhr_link]' <?php checked( $options['ukfhr_link'], 1 ); ?> value='1'>
    <label for="ukfhr_link_no">No</label>
    <input type='radio' id='ukfhr_link_no' name='ukfhr_settings[ukfhr_link]' <?php checked( $options['ukfhr_link'], 0 ); ?> value='0'>
    <br>
    Include link to the Food Agency's page for the business the rating is for, on: http://ratings.food.gov.uk/
    <?php
}

function ukfhr_rating_render() {
    $options = get_option( 'ukfhr_settings' );
    if (!isset($options['ukfhr_rating_text'])){
        $options['ukfhr_rating_text'] = 1;
    }

    ?>
    <label for="ukfhr_rating_text_yes">Yes</label>
    <input type='radio' id='ukfhr_rating_text_yes' name='ukfhr_settings[ukfhr_rating_text]' <?php checked( $options['ukfhr_rating_text'], 1 ); ?> value='1'>
    <label for="ukfhr_rating_text_no">No</label>
    <input type='radio' id='ukfhr_rating_text_no' name='ukfhr_settings[ukfhr_rating_text]' <?php checked( $options['ukfhr_rating_text'], 0 ); ?> value='0'>
    <br>
    Include the text stating the rating, ie: Food hygiene rating: 5 Very good
    <?php
}

function ukfhr_inspected_render() {
    $options = get_option( 'ukfhr_settings' );
    if (!isset($options['ukfhr_inspected_text'])){
        $options['ukfhr_inspected_text'] = 1;
    }
    ?>
    <label for="ukfhr_inspected_text_yes">Yes</label>
    <input type='radio' id='ukfhr_inspected_text_yes' name='ukfhr_settings[ukfhr_inspected_text]' <?php checked( $options['ukfhr_inspected_text'], 1 ); ?> value='1'>
    <label for="ukfhr_inspected_text_no">No</label>
    <input type='radio' id='ukfhr_inspected_text_no' name='ukfhr_settings[ukfhr_inspected_text]' <?php checked( $options['ukfhr_inspected_text'], 0 ); ?> value='0'>
    <br>
    Include the text stating the inspected date, ie: Inspected on 5 May 2010 by Foods Standards Agency
    <?php
}


function ukfhr_image_size_render(  ) {

    $options = get_option( 'ukfhr_settings' );
    if (!isset($options['ukfhr_image_size'])){
        $options['ukfhr_image_size'] = 'large';
    }
    ?>
    <select name='ukfhr_settings[ukfhr_image_size]'>
        <option value='small' <?php selected( $options['ukfhr_image_size'], "small" ); ?>>Small</option>
        <option value='medium' <?php selected( $options['ukfhr_image_size'], "medium" ); ?>>Medium</option>
        <option value='large' <?php selected( $options['ukfhr_image_size'], "large" ); ?>>Large</option>
    </select>

    <br>
    Large:
    <img src="<?php echo plugins_url( plugin_basename( dirname(__FILE__) ).'/images/large/72ppi/fhrs_5_en-gb.jpg')?>"  />
    Medium:
    <img src="<?php echo plugins_url( plugin_basename( dirname(__FILE__) ).'/images/medium/72ppi/fhrs_5_en-gb.jpg')?>"  />
    Small:
    <img src="<?php echo plugins_url( plugin_basename( dirname(__FILE__) ).'/images/small/72ppi/fhrs_5_en-gb.jpg')?>"  />
<?php
}

function ukfhr_settings_section_callback() {
    ?>
    <?php
}

function ukfhr_options_page() {
    ?>

    <h1>UK Food Hygiene Rating Plugin</h1>

    This plugin calls and uses the UK food hygiene rating data API, see: <a href="http://ratings.food.gov.uk/open-data/en-GB" />ratings.food.gov.uk</a>.

    <p>
    <h2>How to use this plugin</h2>
    Add this shortcode to your blog post, and the plugin will use the post's title as the business name to search for:
    <br>
    <code>[food]</code>
    <br><br>
    Or include the business name within the shortcode:
    <br>
    <code>[food business="business name"]</code>
    <br><br>
    </p>

    <form action='options.php' method='post'>
        <?php
        settings_fields( 'ukfhr_page' );
        do_settings_sections( 'ukfhr_page' );
        submit_button();
        ?>
    </form>
    <?php
}

?>