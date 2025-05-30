<?php
if (!defined('ABSPATH')) exit;



$settings = get_option('addweb_ai_chat_settings');
$position_class = $settings['position'] ?? 'bottom_right';



$welcome_message = esc_html($settings['welcome_message'] ?? __('Hello! How can I help you?', 'addweb-ai-chat'));
$bot_image = '';

if (!empty($settings['bot_image']) && filter_var($settings['bot_image'], FILTER_VALIDATE_URL)) {
    $response = wp_remote_head($settings['bot_image']);

    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        $bot_image = esc_url($settings['bot_image']);
    }
}

if (empty($bot_image)) {
    $bot_image = ADDWEB_AI_CHAT_IMAGES . 'chat-board-icon.svg';
}
$header_title = esc_html($settings['header_title'] ?? __('Ticket Bot', 'addweb-ai-chat'));
$header_text_color = esc_attr($settings['header_text_color'] ?? '#000');
$bot_title = esc_html($settings['bot_title'] ?? __('Ticket Bot', 'addweb-ai-chat'));
$bot_sub_title = esc_html($settings['bot_sub_title'] ?? __('Online', 'addweb-ai-chat'));
$bot_sub_color = esc_attr($settings['bot_sub_color'] ?? '#000');
$header_color = esc_attr($settings['header_color'] ?? '#fffff');
$bot_chat_bg = esc_attr($settings['bot_chat_bg'] ?? '#fffff');
$bot_chat_text = esc_attr($settings['bot_chat_text'] ?? '#fffff');
$user_chat_bg = esc_attr($settings['user_chat_bg'] ?? '#6b1dfd');
$user_chat_text = esc_attr($settings['user_chat_text'] ?? '#fffff');
$chat_placeholder = esc_attr($settings['chat_placeholder'] ?? __('Type your message here', 'addweb-ai-chat'));
$chat_ph_bg_color = esc_attr($settings['chat_ph_bg_color'] ?? '#ffffff');
$chat_ph_text_color = esc_attr($settings['chat_ph_text_color'] ?? '#131313');
$chat_bg = esc_attr($settings['chat_bg'] ?? '#f3f3ff');
    

?>
<?php
//change place holder text color
if (!empty($chat_ph_text_color)) : ?>
    <style>
        #addweb-chat-user-input::placeholder {
            color: <?php echo esc_attr($chat_ph_text_color); ?> !important;
        }

        /* Cross-browser support */
        #addweb-chat-user-input::-webkit-input-placeholder {
            color: <?php echo esc_attr($chat_ph_text_color); ?> !important;
        }

        #addweb-chat-user-input:-ms-input-placeholder {
            color: <?php echo esc_attr($chat_ph_text_color); ?> !important;
        }

        #addweb-chat-user-input::-moz-placeholder {
            color: <?php echo esc_attr($chat_ph_text_color); ?> !important;
            opacity: 1;
        }

        #addweb-chat-user-input:-moz-placeholder {
            color: <?php echo esc_attr($chat_ph_text_color); ?> !important;
            opacity: 1;
        }
    </style>
<?php endif; ?>

<div class="add-web-ai-chat">
    <!--chat-bot-->
    <div class="msg-icon">
        <button class="msg-button" id="ChatBtn">
            <img src="<?php echo ADDWEB_AI_CHAT_IMAGES ?>bg-chat-image.svg" alt="msg-bg" class="img-fluid msg-chat-bg" id="chatIcon">
            <img src="<?php echo ADDWEB_AI_CHAT_IMAGES ?>msg-Vector.svg" alt="msg-icon" class="msg-Vector-01" id="msg-Vector">
        </button>
    </div>

    <!-- Chat Board -->
    <div class="chat-board" id="chatBoard">

        <!--chat-bot-heading-->
        <div class="chat-header" style="background-color: <?php echo $header_color; ?>;">
            <div class="chat-bot-icon">
                <img src="<?php echo $bot_image; ?>" alt="chat-board-icon" width="35px" height="35  px">
                <div class="status-online"></div>
            </div>

            <div class="chat-bot-heading">
                <h4 class="chat-bot-title" style="color: <?php echo $header_text_color; ?>;"><?php echo $header_title; ?></h4>
                <p class="chat-bot-status" style="color: <?php echo $bot_sub_color; ?>;"><?php echo $bot_sub_title;    ?></p>
            </div>
            <div class="chat-btn-close" id="closeChatBtn">
                <img src="<?php echo ADDWEB_AI_CHAT_IMAGES ?>chat-bot-close-icon.svg" alt="chat-bot" width="39px" height="39px">
            </div>
        </div>

        <!--chat-bot-body-->
        <div class="chat-body addweb-chat-body" style="background-color: <?php echo $chat_bg; ?>;">
            <div class="bot-chat">
                <div class="msg-heading">
                    <img src="<?php echo $bot_image; ?>" alt="chat-bot-icon" width="22px" height="22px">
                    <p class="msg-title"><?php echo $bot_title; ?></p>
                </div>
                <div class="bot-msg" style="background-color: <?php echo $bot_chat_bg; ?>;color:<?php echo $bot_chat_text; ?>;">
                    <?php echo $welcome_message; ?>
                </div>
            </div>
            <div class="loader addweb-chat-loading" style="display: none;"></div>
        </div>

        <!--msg box-->
        <div class="msg-box" style="background-color: <?php echo $chat_ph_bg_color; ?>;">
            <input type="text" !important;" class="addweb-chat-input" id="addweb-chat-user-input" placeholder="<?php echo $chat_placeholder; ?>" style="background-color: <?php echo $chat_ph_bg_color; ?>;" autocomplete="off">
            <img id="addweb-chat-send-button" src="<?php echo ADDWEB_AI_CHAT_IMAGES ?>msg-send-icon.svg" alt="send-msg" class="send-msg-icon">
        </div>
    </div>
</div>