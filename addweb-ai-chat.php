<?php

/**
 * Plugin Name: AddWeb AI Chat
 * Description: Lightweight AI chat popup with configurable design and API integration.
 * Version: 1.0
 * Author: AddWeb
 * Text Domain: addweb-ai-chat
 */

if (!defined('ABSPATH')) exit;

define('ADDWEB_AI_CHAT_VERSION', '1.0');
define('ADDWEB_AI_CHAT_DIR', plugin_dir_path(__FILE__));
define('ADDWEB_AI_CHAT_URL', plugin_dir_url(__FILE__));
define('ADDWEB_AI_CHAT_IMAGES', ADDWEB_AI_CHAT_URL . 'assets/images/');
register_activation_hook(__FILE__, 'addweb_ai_chat_set_default_settings');
function addweb_ai_chat_set_default_settings()
{
    $settings = get_option('addweb_ai_chat_settings');
    if (!$settings) {
        $fields = addweb_ai_chat_get_settings_fields();
        $defaults = [];

        foreach ($fields as $key => $field) {
            $defaults[$key] = $field['default'] ?? '';
        }

        update_option('addweb_ai_chat_settings', $defaults);
    }
}
function addweb_ai_chat_get_settings_fields()
{
    return [
        'api_url'               => ['label' => 'API URL', 'type' => 'url', 'default' => 'https://new-suddenly-sailfish.ngrok-free.app/api/chatbot/query'],
        'api_token'             => ['label' => 'API Token', 'type' => 'text', 'default' => ''],
        'welcome_message'       => ['label' => 'Welcome Message', 'type' => 'text', 'default' => 'Hello! How can I help you?'],
        'header_title'          => ['label' => 'Header Title', 'type' => 'text', 'default' => 'Ticket Bot'],
        'header_text_color'     => ['label' => 'Header Text Color', 'type' => 'color', 'default' => '#000'],
        'bot_sub_title'         => ['label' => 'Header Sub Title', 'type' => 'text', 'default' => 'Online'],
        'bot_sub_color'         => ['label' => 'Header Sub Title Color', 'type' => 'color', 'default' => '#000'],
        'header_color'          => ['label' => 'Header Background Color', 'type' => 'color', 'default' => '#ffffff'],
        'bot_image'             => ['label' => 'Bot Image', 'type' => 'image', 'default' => ADDWEB_AI_CHAT_URL . 'assets/images/default.png'],
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
require_once plugin_dir_path(__FILE__) . 'includes/class-addweb-ai-chat-loader.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-addweb-ai-chat-settings.php';
new Addweb_AI_Chat_Loader();
new Addweb_AI_Chat_Settings();
