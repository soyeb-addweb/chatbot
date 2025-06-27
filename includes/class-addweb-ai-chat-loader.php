<?php
if (!defined('ABSPATH')) exit;

class Addweb_AI_Chat_Loader
{

    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_footer', [$this, 'render_chat_ui']);
        add_action('wp_ajax_addweb_ai_chat_send_message', [$this, 'addweb_handle_ajax']);
        add_action('wp_ajax_nopriv_addweb_ai_chat_send_message', [$this, 'addweb_handle_ajax']);
        add_action('wp_ajax_addweb_end_chat', [$this, 'addweb_handle_end_chat']);
        add_action('wp_ajax_nopriv_addweb_end_chat', [$this, 'addweb_handle_end_chat']);
        add_action('init', [$this, 'addweb_session_management']);
    }
    public function addweb_session_management()
    {
        if (!session_id()) {
            session_start();
        }
    }
    public function enqueue_assets()
    {
        //wp_enqueue_style('addweb-ai-chat-style', plugin_dir_url(__FILE__) . '../assets/css/addweb-ai-chat.css');
        // wp_enqueue_script('addweb-ai-chat-script', plugin_dir_url(__FILE__) . '../assets/js/addweb-ai-chat.js', ['jquery'], null, true);
        $settings = get_option('addweb_ai_chat_settings');
        $bot_title = esc_html($settings['bot_title'] ?? __('Ticket Bot', 'addweb-ai-chat'));
        $bot_chat_bg = esc_attr($settings['bot_chat_bg'] ?? '#fffff');
        $bot_chat_text = esc_attr($settings['bot_chat_text'] ?? '#fffff');
        $user_chat_bg = esc_attr($settings['user_chat_bg'] ?? '#6b1dfd');
        $user_chat_text = esc_attr($settings['user_chat_text'] ?? '#fffff');

        $bot_image = '';

        if (!empty($settings['bot_image']) && filter_var($settings['bot_image'], FILTER_VALIDATE_URL)) {
            $response = wp_remote_head($settings['bot_image']);

            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $bot_image = esc_url($settings['bot_image']);
            }
        }

        if (empty($bot_image)) {
            $bot_image = ADDWEB_AI_CHAT_IMAGES . 'default.png';
        }


        wp_enqueue_style('addweb-ai-chat-style', plugin_dir_url(__FILE__) . '../assets/css/chat-style.css');

        wp_enqueue_script('addweb-ai-chat-script', plugin_dir_url(__FILE__) . '../assets/js/chat-script.js', [], false, true);
        wp_localize_script('addweb-ai-chat-script', 'addweb_ai_chat', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'no_response_text' => __('No response', 'addweb-ai-chat'),
            'error_text' => __('Error communicating with API.', 'addweb-ai-chat'),
            'bot_image' => $bot_image,
            'bot_title' => $bot_title,
            'bot_chat_bg' => $bot_chat_bg,
            'bot_chat_text' => $bot_chat_text,
            'user_chat_bg' => $user_chat_bg,
            'user_chat_text' => $user_chat_text,
            'nonce'    => wp_create_nonce('addweb_ai_chat_nonce'),
            'api_url' => $settings['api_url'],
            'api_token' => $settings['api_token'],
        ]);
    }



    public function render_chat_ui()
    {
        include ADDWEB_AI_CHAT_DIR . 'pages/frontend/chat-ui.php';
    }

    public function addweb_handle_ajax()
    {
        check_ajax_referer('addweb_ai_chat_nonce', 'nonce');
        $message = sanitize_text_field($_POST['message'] ?? '');
        $session_id = sanitize_text_field($_POST['session_id'] ?? ''); // Get session_id from request
        $settings = get_option('addweb_ai_chat_settings');
        $api_url = esc_url_raw($settings['api_url'] ?? '');
        $api_token = sanitize_text_field($settings['api_token'] ?? '');

        if (empty($message) || empty($api_url)) {
            wp_send_json(['success' => false, 'response' => __('Invalid request', 'addweb-ai-chat')]);
        }

        // Prepare request body
        $request_body = [
            'query' => $message,
            'stream' => true
        ];

        // Add session_id to request if it exists (for chat continuity)
        if (!empty($session_id)) {
            $request_body['session_id'] = $session_id;
        }

        error_log("request_body: " . print_r($request_body, true));
        $response = wp_remote_post($api_url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_token,
                'Content-Type'  => 'application/json',
            ],
            'timeout' => 20,
            'body' => json_encode($request_body),
        ]);

        if (is_wp_error($response)) {
            error_log('WP Error: ' . $response->get_error_message());
            wp_send_json(['success' => false, 'response' => __('API request failed', 'addweb-ai-chat')]);
        }

        // Get raw response body
        $raw_response = wp_remote_retrieve_body($response);
        $http_code = wp_remote_retrieve_response_code($response);


        error_log('=== DEBUG START ===');
        error_log('HTTP Status Code: ' . $http_code);
        error_log('Raw API Response: ' . $raw_response);
        error_log('Response Length: ' . strlen($raw_response));

        // Check HTTP status code
        if ($http_code !== 200) {
            error_log('Non-200 HTTP response: ' . $http_code);
            wp_send_json(['success' => false, 'response' => __('API error: HTTP ' . $http_code, 'addweb-ai-chat')]);
        }

        // Check if response is empty
        if (empty($raw_response)) {
            error_log('Empty API response');
            wp_send_json(['success' => false, 'response' => __('Empty response from API', 'addweb-ai-chat')]);
        }

        // Clean the response - remove any text before the JSON starts
        $json_start = strpos($raw_response, '{');
        if ($json_start !== false) {
            $clean_response = substr($raw_response, $json_start);
            error_log('Cleaned Response: ' . $clean_response);
        } else {
            $clean_response = $raw_response;
            error_log('No JSON found in response');
        }

        // Decode JSON with proper parameters
        $body = json_decode($clean_response, true); // TRUE for associative array
        $json_error = json_last_error();

        error_log('JSON Error Code: ' . $json_error);
        error_log('JSON Error Message: ' . json_last_error_msg());

        if ($json_error !== JSON_ERROR_NONE) {
            error_log('JSON Decode Failed: ' . json_last_error_msg());
            wp_send_json([
                'success' => false,
                'response' => __('Invalid JSON response: ' . json_last_error_msg(), 'addweb-ai-chat')
            ]);
        }

        error_log('Decoded Body: ' . print_r($body, true));
        error_log('=== DEBUG END ===');

        // Check if body is valid array
        if (!is_array($body)) {
            error_log('Body is not an array: ' . gettype($body));
            wp_send_json(['success' => false, 'response' => __('Invalid response format', 'addweb-ai-chat')]);
        }

        // Check if the API response structure matches your example
        if (isset($body['success']) && $body['success'] === true) {
            //$reply = $body['data']['response'] ?? __('No reply from AI', 'addweb-ai-chat');
            $reply_data = $body['data']['response'] ?? [];
            $reply = is_array($reply_data) && isset($reply_data['response'])
                ? $reply_data['response']
                : __('No reply from AI', 'addweb-ai-chat');
            $new_session_id = $reply_data['session_id'] ?? '';

            error_log("API Response session id: " . $new_session_id);

            // Convert Markdown to HTML using Parsedown
            if (!class_exists('Parsedown')) {
                require_once plugin_dir_path(__DIR__) . 'includes/libs/Parsedown.php';
            }

            $Parsedown = new Parsedown();
            $reply_html = $Parsedown->text($reply);

            // Sanitize HTML
            $allowed_tags = [
                'p' => [],
                'br' => [],
                'strong' => [],
                'em' => [],
                'ul' => [],
                'ol' => [],
                'li' => [],
                'a' => [
                    'href' => [],
                    'title' => [],
                    'target' => [],
                    'rel' => [],
                ],
                'code' => [],
                'pre' => [],
                'blockquote' => [],
                'h1' => [],
                'h2' => [],
                'h3' => [],
                'h4' => [],
                'h5' => [],
                'h6' => [],
            ];

            $reply_html = wp_kses($reply_html, $allowed_tags);

            // Return response with session_id for frontend to store
            wp_send_json([
                'success' => true,
                'response' => $reply_html,
                'session_id' => $new_session_id
            ]);
        } else {
            // Log the full response for debugging
            error_log('API Response Structure Issue - Body: ' . print_r($body, true));
            error_log('Success field exists: ' . (isset($body['success']) ? 'YES' : 'NO'));
            error_log('Success field value: ' . (isset($body['success']) ? var_export($body['success'], true) : 'N/A'));

            wp_send_json([
                'success' => false,
                'response' => __('Invalid API response structure', 'addweb-ai-chat')
            ]);
        }
    }


    function addweb_handle_end_chat()
    {
        check_ajax_referer('addweb_ai_chat_nonce', 'nonce');
        error_log("post variables: " . print_r($_POST, true));

        $email      = sanitize_email($_POST['email'] ?? '');
        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        $api_token = sanitize_text_field($settings['api_token'] ?? '');

        if (empty($email) || empty($session_id)) {
            wp_send_json_error('Missing email or session ID');
        }

        $settings = get_option('addweb_ai_chat_settings');
        $endpoint = esc_url_raw($settings['end_chat'] ?? '');

        if (empty($endpoint)) {
            wp_send_json_error('End Chat API endpoint not configured');
        }

        $response = wp_remote_post($endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_token,
                'Content-Type'  => 'application/json',
            ],

            'body'    => json_encode([
                'session_id' => $session_id,
                'email'      => $email,
            ]),
            'timeout' => 15,
        ]);
        error_log("API Response: " . print_r($response, true));
        if (is_wp_error($response)) {
            wp_send_json_error('API request failed: ' . $response->get_error_message());
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        if ($code !== 200) {
            wp_send_json_error('API error: ' . $body);
        }

        wp_send_json_success('Chat ended successfully');
    }
}
