<?php
/**
* 
* @Coustom api Admin setting option for Authntication
*
*/

/**
 * Custom add setting page in admin setting
 */
function custom_add_settings_page()
{
    add_options_page('Custom API Setting', 'Custom API Setting', 'manage_options', 'custom-api-setting', 'wp_custom_api_settings');
}
add_action('admin_menu', 'custom_add_settings_page');

/**
 * Custom api setting function
 */

function wp_custom_api_settings()
{
?>
    <h2>Custom API Settings</h2>
    <form action="options.php" method="post">
        <?php
    settings_fields('custom_plugin_options');
    do_settings_sections('custom_api_plugin'); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save'); ?>" />
    </form>
    <?php
}

/**
 * Custom field option page in custom api setting
 */
function custom_field_settings()
{
    register_setting('custom_plugin_options', 'custom_plugin_options', 'custom_plugin_options_validate');
    add_settings_section('api_settings', 'API Settings', 'custom_plugin_section_text', 'custom_api_plugin');

    add_settings_field('custom_plugin_setting_consumer_key', 'Consumer Key', 'custom_plugin_setting_consumer_key', 'custom_api_plugin', 'api_settings');
    add_settings_field('custom_plugin_setting_consumer_secret', 'Consumer Secret', 'custom_plugin_setting_consumer_secret', 'custom_api_plugin', 'api_settings');
}
add_action('admin_init', 'custom_field_settings');

/**
 * coustom api related content text
 */
function custom_plugin_section_text()
{
    echo '<p>Here you can set all the options for using the API</p>';
}

/**
 * function for Consumer_key @get Consumer_key
 */
function custom_plugin_setting_consumer_key()
{
    $options = get_option('custom_plugin_options'); ?>
    <input id='custom_plugin_setting_consumer_key' name='custom_plugin_options[consumer_key]' type='text' value='<?php echo esc_attr($options['consumer_key']); ?>' />
    <?php
}

/**
 * function for Consumer_key @get consumer_secret
 */
function custom_plugin_setting_consumer_secret()
{
    $options = get_option('custom_plugin_options'); ?>
    <input id='custom_plugin_setting_consumer_secret' name='custom_plugin_options[consumer_secret]' type='text' value='<?php echo esc_attr($options['consumer_secret']); ?>' />
    <?php
}


?>
