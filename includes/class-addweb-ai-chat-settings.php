<?php
if (!defined('ABSPATH')) exit;

class Addweb_AI_Chat_Settings
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
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
            'position' => __('Chat Position', 'addweb-ai-chat'),
            'text_size' => __('Text Size (px)', 'addweb-ai-chat'),
            'bubble_color' => __('Send Button Color', 'addweb-ai-chat'),
            'header_color' => __('Header Background Color', 'addweb-ai-chat'),
            'welcome_message' => __('Welcome Message', 'addweb-ai-chat'),
            'api_url' => __('API URL', 'addweb-ai-chat'),
            'api_token' => __('API Token', 'addweb-ai-chat'),
        ];

        foreach ($fields as $id => $label) {
            add_settings_field($id, $label, [$this, 'render_field'], 'addweb-ai-chat', 'addweb_ai_chat_main', ['id' => $id]);
        }
    }

    public function render_field($args)
    {
        $id = $args['id'];
        $options = get_option('addweb_ai_chat_settings');
        $value = esc_attr($options[$id] ?? '');

        if ($id === 'position') {
            $positions = ['bottom_right', 'bottom_left', 'top_right', 'top_left'];
            echo '<select name="addweb_ai_chat_settings[position]">';
            foreach ($positions as $pos) {
                echo '<option value="' . esc_attr($pos) . '"' . selected($value, $pos, false) . '>' . ucfirst(str_replace('_', ' ', $pos)) . '</option>';
            }
            echo '</select>';
        } else {
            $type = ($id === 'text_size') ? 'number' : 'text';
            echo '<input type="' . $type . '" name="addweb_ai_chat_settings[' . esc_attr($id) . ']" value="' . $value . '" class="regular-text">';
        }
    }

    public function render_settings_page()
    {
?>
        <div class="wrap">
            <h1><?php _e('AddWeb AI Chat Settings', 'addweb-ai-chat'); ?></h1>
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
}
