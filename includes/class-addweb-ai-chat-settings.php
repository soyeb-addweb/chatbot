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

        // Tabs and grouped fields
        $tabs = [
            'general' => ['title' => 'General', 'fields' => ['api_url', 'api_token', 'welcome_message', 'chat_placeholder']],
            'header' => ['title' => 'Header', 'fields' => ['header_title', 'header_text_color', 'bot_sub_title', 'bot_sub_color', 'header_color', 'bot_image', 'bot_title']],
            'appearance' => ['title' => 'Appearance', 'fields' => ['bot_chat_bg', 'bot_chat_text', 'user_chat_bg', 'user_chat_text', 'chat_bg', 'chat_ph_bg_color', 'chat_ph_text_color']],
        ];

        // Field definitions (do not modify this)
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

        foreach ($tabs as $tab_key => $tab_data) {
            add_settings_section("addweb_ai_chat_section_$tab_key", '', null, "addweb-ai-chat-$tab_key");

            foreach ($tab_data['fields'] as $field_key) {
                $props = $fields[$field_key];
                add_settings_field(
                    $field_key,
                    __($props['label'], 'addweb-ai-chat'),
                    [$this, 'render_field'],
                    "addweb-ai-chat-$tab_key",
                    "addweb_ai_chat_section_$tab_key",
                    ['id' => $field_key, 'type' => $props['type']]
                );
            }
        }
    }

    public function render_field($args)
    {
        $id     = $args['id'];
        $type   = $args['type'];
        $fields = $this->get_fields_with_defaults(); // Use helper to access defaults
        $default = $fields[$id]['default'] ?? '';

        $options = get_option('addweb_ai_chat_settings', []);
        $value = isset($options[$id]) ? esc_attr($options[$id]) : $default;

        switch ($type) {
            case 'color':
                echo '<input type="color" name="addweb_ai_chat_settings[' . esc_attr($id) . ']" value="' . $value . '">';
                break;

            case 'image':
                $default_image_url = ADDWEB_AI_CHAT_IMAGES . 'default.png';
                $image_url = $value ?: $default_image_url;
                echo '<img src="' . esc_url($image_url) . '" style="max-height:60px; display:block; margin-bottom:10px;" />';
                echo '<input type="text" id="' . esc_attr($id) . '" name="addweb_ai_chat_settings[' . esc_attr($id) . ']" value="' . esc_attr($value) . '" class="regular-text">';
                echo ' <button class="button addweb-upload-button" data-target="' . esc_attr($id) . '">' . __('Upload Image', 'addweb-ai-chat') . '</button>';
                break;

            default:
                echo '<input type="' . esc_attr($type) . '" name="addweb_ai_chat_settings[' . esc_attr($id) . ']" value="' . esc_attr($value) . '" class="regular-text">';
                break;
        }
    }
    private function get_fields_with_defaults()
    {
        return [
            'api_url'               => ['label' => 'API URL', 'type' => 'url', 'default' => 'https://new-suddenly-sailfish.ngrok-free.app/api/chatbot/query'],
            'api_token'             => ['label' => 'API Token', 'type' => 'text', 'default' => 'testtoken'],
            'welcome_message'       => ['label' => 'Welcome Message', 'type' => 'text', 'default' => 'Hello! How can I help you?'],
            'header_title'          => ['label' => 'Header Title', 'type' => 'text', 'default' => 'Ticket Bot'],
            'header_text_color'     => ['label' => 'Header Text Color', 'type' => 'color', 'default' => '#000'],
            'bot_sub_title'         => ['label' => 'Header Sub Title', 'type' => 'text', 'default' => 'Online'],
            'bot_sub_color'         => ['label' => 'Header Sub Title Color', 'type' => 'color', 'default' => '#000'],
            'header_color'          => ['label' => 'Header Background Color', 'type' => 'color', 'default' => '#ffffff'],
            'bot_image'             => ['label' => 'Bot Image', 'type' => 'image', 'default' => ADDWEB_AI_CHAT_IMAGES . '/default.png'],
            'bot_title'             => ['label' => 'Bot Title', 'type' => 'text', 'default' => 'Ticket Bot'],
            'bot_chat_bg'           => ['label' => 'Bot Chat Bubble Background Color', 'type' => 'color', 'default' => '#f1f1f1'],
            'bot_chat_text'         => ['label' => 'Bot Chat Bubble Text Color', 'type' => 'color', 'default' => '#000'],
            'user_chat_bg'          => ['label' => 'User Chat Bubble Background Color', 'type' => 'color', 'default' => '#a82cd2'],
            'user_chat_text'        => ['label' => 'User Chat Bubble Text Color', 'type' => 'color', 'default' => '#ffffff'],
            'chat_bg'               => ['label' => 'Chat Background Color', 'type' => 'color', 'default' => '#deddda'],
            'chat_placeholder'      => ['label' => 'Chat Placeholder', 'type' => 'text', 'default' => 'Type your message here...'],
            'chat_ph_bg_color'      => ['label' => 'Chat Placeholder Background Color', 'type' => 'color', 'default' => '#ffffff'],
            'chat_ph_text_color'    => ['label' => 'Chat Placeholder Text Color', 'type' => 'color', 'default' => '#040404'],
        ];
    }


    public function render_settings_page()
    {
        $tabs = [
            'general' => __('General', 'addweb-ai-chat'),
            'header' => __('Header', 'addweb-ai-chat'),
            'appearance' => __('Appearance', 'addweb-ai-chat'),
        ];

        $active_tab = $_GET['tab'] ?? 'general';
?>
        <div class="wrap">
            <h1><?php _e('AddWeb AI Chat Settings', 'addweb-ai-chat'); ?></h1>

            <h2 class="nav-tab-wrapper">
                <?php foreach ($tabs as $tab_key => $tab_label): ?>
                    <a href="?page=addweb-ai-chat&tab=<?php echo esc_attr($tab_key); ?>" class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html($tab_label); ?>
                    </a>
                <?php endforeach; ?>
            </h2>

            <form method="post" action="options.php">
                <?php
                settings_fields('addweb_ai_chat_settings_group');
                do_settings_sections("addweb-ai-chat-$active_tab");
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
