<?php
if (!defined('ABSPATH')) exit;

$settings = get_option('addweb_ai_chat_settings');
$position_class = $settings['position'] ?? 'bottom_right';
$text_size = intval($settings['text_size'] ?? 14);
$bubble_color = esc_attr($settings['bubble_color'] ?? '#0073aa');
$header_color = esc_attr($settings['header_color'] ?? '#333');
$welcome_message = esc_html($settings['welcome_message'] ?? __('Hello! How can I help you?', 'addweb-ai-chat'));
?>


<div class="add-web-ai-chat">
    <!--chat-bot-->
    <div class="msg-icon">
        <button class="msg-button" id="ChatBtn">
            <img src="https://cdn.prod.website-files.com/675a7db640d825582387a1fc/6830181765c577f8b34f7223_bg-chat-image.svg" alt="msg-bg" class="img-fluid msg-chat-bg" id="chatIcon">
            <img src="https://cdn.prod.website-files.com/675a7db640d825582387a1fc/683009a5a88b808517f5513f_msg-Vector.svg" alt="msg-icon" class="msg-Vector-01" id="msg-Vector">
        </button>
    </div>


    <!-- Chat Board -->
    <div class="chat-board" id="chatBoard">

        <!--chat-bot-heading-->
        <div class="chat-header">
            <div class="chat-bot-icon">
                <img src="https://cdn.prod.website-files.com/675a7db640d825582387a1fc/683009a73cb464c8d9e0e547_chat-board-icon.svg" alt="chat-board-icon" width="24px" height="24px">
                <div class="status-online"></div>
            </div>

            <div class="chat-bot-heading">
                <h4 class="chat-bot-title">Ticket Bot</h4>libxml_disable_entity_loader
                <p class="chat-bot-status">Online</p>
            </div>
            <div class="chat-btn-close" id="closeChatBtn">
                <img src="https://cdn.prod.website-files.com/675a7db640d825582387a1fc/683009a5729398fbd52c78d1_chat-bot-close-icon.svg" alt="chat-bot" width="39px" height="39px">
            </div>
        </div>

        <!--chat-bot-body-->
        <div class="chat-body addweb-chat-body">
            <div class="bot-chat">
                <div class="msg-heading">
                    <img src="https://cdn.prod.website-files.com/675a7db640d825582387a1fc/683009a59bbaa43a2d9487a4_chat-icon.svg" alt="chat-bot-icon" width="22px" height="22px">
                    <p class="msg-title">Ticket Bot</p>
                </div>
                <div class="bot-msg">
                    <?php echo $welcome_message; ?>
                </div>
            </div>
            <div class="loader addweb-chat-loading" style="display: none;"></div>
        </div>

        <!--msg box-->
        <div class="msg-box">
            <input type="text" id="addweb-chat-user-input" placeholder="Type your message here">
            <img id="addweb-chat-send-button" src="https://cdn.prod.website-files.com/675a7db640d825582387a1fc/683009a427e2a2a5a605a2ce_msg-send-icon.svg" alt="send-msg" class="send-msg-icon">
        </div>
    </div>
</div>