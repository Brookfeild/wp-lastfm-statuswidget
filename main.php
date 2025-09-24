<?php
/*
Plugin Name: Last.fm Status Widget
Description: Display the currently playing or last played track for a Last.fm username.
Version: 1.0
Author: Tyler
*/

if (!defined('ABSPATH')) exit;

// === Register Widget Settings ===
function lastfm_nowplaying_register_settings() {
    register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_username');
    add_settings_section('lastfm_nowplaying_section', 'Last.fm Settings', null, 'lastfm_nowplaying');
    add_settings_field(
        'lastfm_nowplaying_username',
        'Last.fm Username',
        function() {
            $value = get_option('lastfm_nowplaying_username', '');
            echo '<input type="text" name="lastfm_nowplaying_username" value="' . esc_attr($value) . '" />';
        },
        'lastfm_nowplaying',
        'lastfm_nowplaying_section'
    );
}
add_action('admin_init', 'lastfm_nowplaying_register_settings');

// === Add settings page to WP Admin ===
function lastfm_nowplaying_settings_page() {
    add_options_page(
        'Last.fm Now Playing',
        'Last.fm Now Playing',
        'manage_options',
        'lastfm_nowplaying',
        function() {
            ?>
            <div class="wrap">
                <h1>Last.fm Now Playing Settings</h1>
                <form method="post" action="options.php">
                    <?php
                    settings_fields('lastfm_nowplaying_options');
                    do_settings_sections('lastfm_nowplaying');
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }
    );
}
add_action('admin_menu', 'lastfm_nowplaying_settings_page');

// === Register widget ===
class LastFM_NowPlaying_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'lastfm_nowplaying_widget',
            'Last.fm Now Playing',
            array('description' => 'Displays the currently playing or last played track')
        );
    }

    public function widget($args, $instance) {
        $username = get_option('lastfm_nowplaying_username', '');
        if (!$username) {
            echo $args['before_widget'] . 'Please set a Last.fm username in Settings.' . $args['after_widget'];
            return;
        }

        $api_key = 'fd4bc04c5f3387f5b0b5f4f7bae504b9'; // Replace with your API key
        $url = "https://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user={$username}&api_key={$api_key}&format=json&limit=1";

        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            echo $args['before_widget'] . 'Error fetching track.' . $args['after_widget'];
            return;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($data['recenttracks']['track'][0])) {
            echo $args['before_widget'] . 'No tracks found.' . $args['after_widget'];
            return;
        }

        $track = $data['recenttracks']['track'][0];
        $track_name = esc_html($track['name']);
        $artist_name = esc_html($track['artist']['#text']);
        $now_playing = isset($track['@attr']['nowplaying']) ? true : false;

        $title = $now_playing ? 'Now Playing:' : 'Last Played:';
        $output = "<strong>{$title}</strong> {$track_name} by {$artist_name}";

        echo $args['before_widget'] . $output . $args['after_widget'];
    }
}

// === Register the widget with WordPress ===
function lastfm_nowplaying_register_widget() {
    register_widget('LastFM_NowPlaying_Widget');
}
add_action('widgets_init', 'lastfm_nowplaying_register_widget');
