<?php

/**
 * Plugin Name: AddWeb AI Chat
 * Description: Lightweight AI chat popup with configurable design and API integration.
 * Version: 1.0
 * Author: AddWeb
 * Text Domain: addweb-ai-chat
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/class-addweb-ai-chat-loader.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-addweb-ai-chat-settings.php';

// function addweb_ai_chat_enqueue_assets()
// {
//     $settings = get_option('addweb_ai_chat_settings');
//     $bubble_color = esc_attr($settings['bubble_color'] ?? '#0073aa');

//     wp_enqueue_style('addweb-ai-chat-style', plugin_dir_url(__FILE__) . 'assets/css/chat-style.css');
//     wp_add_inline_style('addweb-ai-chat-style', "
//         .addweb-chat-input button { background: {$bubble_color}; }
//     ");

//     wp_enqueue_script('addweb-ai-chat-script', plugin_dir_url(__FILE__) . 'assets/js/chat-script.js', [], false, true);
//     wp_localize_script('addweb-ai-chat-script', 'addweb_ai_chat', [
//         'ajax_url' => admin_url('admin-ajax.php'),
//         'no_response_text' => __('No response', 'addweb-ai-chat'),
//         'error_text' => __('Error communicating with API.', 'addweb-ai-chat')
//     ]);
// }
// add_action('wp_enqueue_scripts', 'addweb_ai_chat_enqueue_assets');


new Addweb_AI_Chat_Loader();
new Addweb_AI_Chat_Settings();
