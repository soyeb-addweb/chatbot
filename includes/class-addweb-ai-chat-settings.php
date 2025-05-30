<?php
if (!defined('ABSPATH')) exit;

class Addweb_AI_Chat_Settings
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_media_uploader']);
    }

    public function add_settings_page()
    {
        add_options_page(
            __('AI Chat Settings', 'addweb-ai-chat'),
            __('AI Chat', 'addweb-ai-chat'),
            'manage_options',
            'addweb-ai-chat',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings()
    {
        register_setting('addweb_ai_chat_settings_group', 'addweb_ai_chat_settings');
        add_settings_section('addweb_ai_chat_main', '', null, 'addweb-ai-chat');

        $fields = [
            'api_url'               => ['label' => 'API URL', 'type' => 'url'],
            'api_token'             => ['label' => 'API Token', 'type' => 'text'],
            'welcome_message'       => ['label' => 'Welcome Message', 'type' => 'text'],
            'header_title'          => ['label' => 'Header Title', 'type' => 'text'],
            'header_text_color'     => ['label' => 'Header Text Color', 'type' => 'color'],
            'bot_sub_title'         => ['label' => 'Header Sub Title', 'type' => 'text'],
            'bot_sub_color'         => ['label' => 'Header Sub Title Color', 'type' => 'color'],
            'header_color'          => ['label' => 'Header Background Color', 'type' => 'color'],
            'bot_image'             => ['label' => 'Bot Image', 'type' => 'image'],
            'bot_title'             => ['label' => 'Bot Title', 'type' => 'text'],
            'bot_chat_bg'           => ['label' => 'Bot Chat Bubble Background Color', 'type' => 'color'],
            'bot_chat_text'         => ['label' => 'Bot Chat Bubble Text Color', 'type' => 'color'],
            'user_chat_bg'          => ['label' => 'User Chat Bubble Background Color', 'type' => 'color'],
            'user_chat_text'        => ['label' => 'User Chat Bubble Text Color', 'type' => 'color'],
            'chat_bg'               => ['label' => 'Chat Background Color', 'type' => 'color'],
            'chat_placeholder'      => ['label' => 'Chat Placeholder', 'type' => 'text'],
            'chat_ph_bg_color'      => ['label' => 'Chat Placeholder Background Color', 'type' => 'color'],
            'chat_ph_text_color'    => ['label' => 'Chat Placeholder Text Color', 'type' => 'color'],
        ];

        foreach ($fields as $id => $props) {
            add_settings_field($id, __($props['label'], 'addweb-ai-chat'), [$this, 'render_field'], 'addweb-ai-chat', 'addweb_ai_chat_main', ['id' => $id, 'type' => $props['type']]);
        }
    }

    public function render_field($args)
    {
        $id = $args['id'];
        $type = $args['type'];
        $default = $args['default'];
        $options = get_option('addweb_ai_chat_settings');
        $value = esc_attr($options[$id] ?? '');

        switch ($type) {
            case 'color':
                echo '<input type="color" name="addweb_ai_chat_settings[' . esc_attr($id) . ']" value="' . $value . '">';
                break;

            case 'image':
                $default_image_url = ADDWEB_AI_CHAT_IMAGES . 'chat-board-icon.svg';
                $image_url = $value ?: $default_image_url;

                echo '<img src="' . esc_url($image_url) . '" style="max-height:60px; display:block; margin-bottom:10px;" />';
                echo '<input type="text" id="' . esc_attr($id) . '" name="addweb_ai_chat_settings[' . esc_attr($id) . ']" value="' . esc_attr($value) . '" class="regular-text">';
                echo ' <button class="button addweb-upload-button" data-target="' . esc_attr($id) . '">' . __('Upload Image', 'addweb-ai-chat') . '</button>';
                break;

            default:
                echo '<input type="' . esc_attr($type) . '" name="addweb_ai_chat_settings[' . esc_attr($id) . ']" value="' . $value . '" class="regular-text">';
                break;
        }
    }

    public function render_settings_page()
    {
?>
        <div class="wrap">
            <h1><?php _e('AddWeb AI Chat Settings', 'addweb-ai-chat'); ?></h1>
            <?php

            $settings = get_option('addweb_ai_chat_settings');
            ?>
            <form method="post" action="options.php">
                <?php
                settings_fields('addweb_ai_chat_settings_group');
                do_settings_sections('addweb-ai-chat');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }

    public function enqueue_media_uploader($hook)
    {
        if ($hook !== 'settings_page_addweb-ai-chat') return;

        wp_enqueue_media();
        wp_enqueue_script('addweb-ai-chat-admin', ADDWEB_AI_CHAT_URL . 'assets/js/admin-settings.js', ['jquery'], false, true);
    }
}
