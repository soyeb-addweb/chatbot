<?php
if (!defined('ABSPATH')) exit;

$settings = get_option('addweb_ai_chat_settings');
?>
<div class="wrap">
    <h1><?php _e('Addweb AI Chat Settings', 'addweb-ai-chat'); ?></h1>
    <form method="post" action="options.php">
        <?php settings_fields('addweb_ai_chat_settings_group'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="welcome_message"><?php _e('Welcome Message', 'addweb-ai-chat'); ?></label></th>
                <td><input type="text" name="addweb_ai_chat_settings[welcome_message]" value="<?php echo esc_attr($settings['welcome_message'] ?? ''); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="bubble_color"><?php _e('Bubble Color', 'addweb-ai-chat'); ?></label></th>
                <td><input type="color" name="addweb_ai_chat_settings[bubble_color]" value="<?php echo esc_attr($settings['bubble_color'] ?? '#0073aa'); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="header_color"><?php _e('Header Background Color', 'addweb-ai-chat'); ?></label></th>
                <td><input type="color" name="addweb_ai_chat_settings[header_color]" value="<?php echo esc_attr($settings['header_color'] ?? '#333'); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="text_size"><?php _e('Text Size', 'addweb-ai-chat'); ?></label></th>
                <td><input type="number" name="addweb_ai_chat_settings[text_size]" value="<?php echo esc_attr($settings['text_size'] ?? '14'); ?>" min="10" max="24" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="position"><?php _e('Position on Screen', 'addweb-ai-chat'); ?></label></th>
                <td>
                    <select name="addweb_ai_chat_settings[position]">
                        <option value="bottom_right" <?php selected($settings['position'] ?? '', 'bottom_right'); ?>><?php _e('Bottom Right', 'addweb-ai-chat'); ?></option>
                        <option value="bottom_left" <?php selected($settings['position'] ?? '', 'bottom_left'); ?>><?php _e('Bottom Left', 'addweb-ai-chat'); ?></option>
                        <option value="top_right" <?php selected($settings['position'] ?? '', 'top_right'); ?>><?php _e('Top Right', 'addweb-ai-chat'); ?></option>
                        <option value="top_left" <?php selected($settings['position'] ?? '', 'top_left'); ?>><?php _e('Top Left', 'addweb-ai-chat'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="auto_close"><?php _e('Close Chat on Page Load', 'addweb-ai-chat'); ?></label></th>
                <td><input type="checkbox" name="addweb_ai_chat_settings[auto_close]" value="1" <?php checked($settings['auto_close'] ?? '', 1); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="api_url"><?php _e('API URL', 'addweb-ai-chat'); ?></label></th>
                <td><input type="url" name="addweb_ai_chat_settings[api_url]" value="<?php echo esc_attr($settings['api_url'] ?? ''); ?>" class="regular-text code" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="api_token"><?php _e('API Token', 'addweb-ai-chat'); ?></label></th>
                <td><input type="text" name="addweb_ai_chat_settings[api_token]" value="<?php echo esc_attr($settings['api_token'] ?? ''); ?>" class="regular-text code" /></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>