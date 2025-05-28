<?php
if (!defined('ABSPATH')) exit;

$settings = get_option('addweb_ai_chat_settings');
$position_class = $settings['position'] ?? 'bottom_right';
$text_size = intval($settings['text_size'] ?? 14);
$bubble_color = esc_attr($settings['bubble_color'] ?? '#0073aa');
$header_color = esc_attr($settings['header_color'] ?? '#333');
$welcome_message = esc_html($settings['welcome_message'] ?? __('Hello! How can I help you?', 'addweb-ai-chat'));
?>
<div id="addweb-ai-chat-container" class="addweb-chat-position-<?php echo esc_attr($position_class); ?>">
    <div id="addweb-ai-chat-header" style="background-color: <?php echo esc_attr($header_color); ?>; font-size: <?php echo esc_attr($text_size); ?>px;">
        <span><?php echo esc_html__('Chat with us', 'addweb-ai-chat'); ?></span>
    </div>
    <div id="addweb-ai-chat-box" style="display: none; font-size: <?php echo esc_attr($text_size); ?>px;">
        <div class="addweb-chat-body">
            <div class="addweb-chat-message bot"><?php echo $welcome_message; ?></div>
        </div>
        <div class="addweb-chat-input">
            <input type="text" id="addweb-chat-user-input" placeholder="<?php echo esc_attr__('Type your message...', 'addweb-ai-chat'); ?>" aria-label="<?php echo esc_attr__('Chat input', 'addweb-ai-chat'); ?>">
            <button id="addweb-chat-send-button" aria-label="<?php echo esc_attr__('Send message', 'addweb-ai-chat'); ?>">&rarr;</button>
        </div>
        <div class="addweb-chat-loading" style="display: none;"><?php echo esc_html__('Loading...', 'addweb-ai-chat'); ?></div>
    </div>
</div>