<?php
/*
Plugin Name: Last.fm Status Widget
Description: Display the currently playing or last played track for a Last.fm username.
Version: 1.0
Author: Tyler Ricketts
*/

if (!defined('ABSPATH')) exit;

// === Register Widget Settings ===
// === Register settings and fields ===
function lastfm_nowplaying_register_settings() {
    // Register options with defaults
    register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_username');
    register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_width', array('default' => 200));
    register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_height', array('default' => 50));
    register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_text_size', array('default' => 14));
    register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_scroll_enabled', array('default' => 1));
    register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_scroll_speed', array('default' => 5));
        // Album art toggle
        register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_album_art', [
            'type' => 'boolean',
            'default' => 1,
            'sanitize_callback' => 'absint',
        ]);

        // Playcount toggle
        register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_playcount', [
            'type' => 'boolean',
            'default' => 1,
            'sanitize_callback' => 'absint',
        ]);

        register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_username_link', [
            'type' => 'boolean',
            'default' => 1,
            'sanitize_callback' => 'absint',
        ]);

    // Add section
    add_settings_section(
        'lastfm_nowplaying_section',
        'Last.fm Settings',
        null,
        'lastfm_nowplaying'
    );

    // Register API key option
    register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_api_key');

    // API Key field
    add_settings_field(
        'lastfm_nowplaying_api_key',
        'Last.fm API Key',
        function () {
            $value = get_option('lastfm_nowplaying_api_key', '');
            echo '<input type="text" name="lastfm_nowplaying_api_key" value="' . esc_attr($value) . '" class="regular-text" />';
            echo '<p class="description">Enter your personal Last.fm API key. Required for the widget to work.</p>';
        },
        'lastfm_nowplaying',
        'lastfm_nowplaying_section'
    );

    // Username
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

    // Width
    add_settings_field(
        'lastfm_nowplaying_width',
        'Box Width (px)',
        function() {
            $value = get_option('lastfm_nowplaying_width', 200);
            echo '<input type="number" name="lastfm_nowplaying_width" value="' . esc_attr($value) . '" />';
        },
        'lastfm_nowplaying',
        'lastfm_nowplaying_section'
    );

    // Height
    add_settings_field(
        'lastfm_nowplaying_height',
        'Box Height (px)',
        function() {
            $value = get_option('lastfm_nowplaying_height', 50);
            echo '<input type="number" name="lastfm_nowplaying_height" value="' . esc_attr($value) . '" />';
        },
        'lastfm_nowplaying',
        'lastfm_nowplaying_section'
    );

    // Text size
    add_settings_field(
        'lastfm_nowplaying_text_size',
        'Text Size (px)',
        function() {
            $value = get_option('lastfm_nowplaying_text_size', 14);
            echo '<input type="number" name="lastfm_nowplaying_text_size" value="' . esc_attr($value) . '" />';
        },
        'lastfm_nowplaying',
        'lastfm_nowplaying_section'
    );


    add_settings_field(
        'lastfm_nowplaying_scroll_enabled',
        'Enable Scrolling',
        function() {
            $value = get_option('lastfm_nowplaying_scroll_enabled', 1); // default TRUE
            $checked = $value ? 'checked' : '';
            echo '<input type="checkbox" name="lastfm_nowplaying_scroll_enabled" value="1" ' . $checked . ' /> Enable scrolling for long song titles';
        },
        'lastfm_nowplaying',
        'lastfm_nowplaying_section'
    );

    add_settings_field(
        'lastfm_nowplaying_scroll_speed',
        'Scroll Speed (1-10)',
        function() {
            $value = get_option('lastfm_nowplaying_scroll_speed', 5);
            echo '<input type="number" name="lastfm_nowplaying_scroll_speed" min="1" max="10" value="' . esc_attr($value) . '" />';
            echo ' (1 = slowest, 10 = fastest)';
        },
        'lastfm_nowplaying',
        'lastfm_nowplaying_section'
    );
        // Album Art Toggle
        add_settings_field(
            'lastfm_nowplaying_album_art',
            'Show Album Art',
            function () {
                $value = get_option('lastfm_nowplaying_album_art', 1);
                ?>
                <input type="checkbox" name="lastfm_nowplaying_album_art" value="1" <?php checked(1, $value); ?> />
                <label for="lastfm_nowplaying_album_art">Enable album art in widget</label>
                <?php
            },
            'lastfm_nowplaying',
            'lastfm_nowplaying_section'
        );

        // Playcount Toggle
        add_settings_field(
            'lastfm_nowplaying_playcount',
            'Show Playcount Line',
            function () {
                $value = get_option('lastfm_nowplaying_playcount', 1);
                ?>
                <input type="checkbox" name="lastfm_nowplaying_playcount" value="1" <?php checked(1, $value); ?> />
                <label for="lastfm_nowplaying_playcount">Show "USERNAME has streamed this PLAYCOUNT times"</label>
                <?php
            },
            'lastfm_nowplaying',
            'lastfm_nowplaying_section'
        );

        add_settings_field(
            'lastfm_nowplaying_username_link',
            'Hyperlink Username',
            function () {
                $value = get_option('lastfm_nowplaying_username_link', 1);
                ?>
                <input type="checkbox" name="lastfm_nowplaying_username_link" value="1" <?php checked(1, $value); ?> />
                <label for="lastfm_nowplaying_username_link">Make username a clickable red link in playcount line</label>
                <?php
            },
            'lastfm_nowplaying',
            'lastfm_nowplaying_section'
        );
}
add_action('admin_init', 'lastfm_nowplaying_register_settings');


// === Admin Settings Page ===
function lastfm_nowplaying_settings_page() {
    add_options_page(
        'Last.fm Widget',
        'Last.fm Widget',
        'manage_options',
        'lastfm_nowplaying',
        function() {
            ?>
            <div class="wrap">
                <h1>Last.fm Widget Settings</h1>
                <form method="post" action="options.php">
                    <?php
                    settings_fields('lastfm_nowplaying_options');
                    do_settings_sections('lastfm_nowplaying');
                    submit_button();
                    ?>
                </form>

                <h2>Widget Preview</h2>
                <?php
                // Fetch saved options
                $username  = get_option('lastfm_nowplaying_username', '');
                $api_key   = get_option('lastfm_nowplaying_api_key', '');

                if (!$username || !$api_key) {
                    echo '<em>Please enter your Last.fm username and API key above to see a preview.</em>';
                } else {
                    try {
                        if (class_exists('LastFM_NowPlaying_Widget')) {
                            $widget = new LastFM_NowPlaying_Widget();

                            // Build an $instance array like WP would normally pass
                            $instance = [
                                'username'        => $username,
                                'width'           => get_option('lastfm_nowplaying_width', 200),
                                'height'          => get_option('lastfm_nowplaying_height', 50),
                                'text_size'       => get_option('lastfm_nowplaying_text_size', 14),
                                'scroll_enabled'  => get_option('lastfm_nowplaying_scroll_enabled', 1),
                                'scroll_speed'    => get_option('lastfm_nowplaying_scroll_speed', 1),
                                'show_album_art'  => get_option('lastfm_nowplaying_album_art', 1),
                                'show_playcount'  => get_option('lastfm_nowplaying_playcount', 1),
                            ];

                            if (method_exists($widget, 'render_lastfm_widget_output')) {
                                $widget->render_lastfm_widget_output($instance, true);
                            } else {
                                echo '<em>Preview not available (render method missing).</em>';
                            }
                        } else {
                            echo '<em>Preview not available (widget class missing).</em>';
                        }
                    } catch (\Throwable $e) {
                            echo '<em>Preview error: ' . esc_html($e->getMessage()) . '</em>';
                        }
                }
                ?>
            </div>
            <?php
        }
    );
}
add_action('admin_menu', 'lastfm_nowplaying_settings_page');

// === Shared CSS + JS for scrolling text ===
function lastfm_nowplaying_enqueue_scripts() {
    $scroll_enabled = get_option('lastfm_nowplaying_scroll_enabled', 1);
    $scroll_speed = get_option('lastfm_nowplaying_scroll_speed', 5); // 1-10

    // New mapping: speed 1 = slowest (15s wait at left), speed 10 = fastest
    // Base duration for movement: slower speed → longer duration
    $speed_map = [
        1 => 20,  // extra slow
        2 => 18,
        3 => 16,
        4 => 14,
        5 => 12,
        6 => 10,
        7 => 8,
        8 => 6,
        9 => 4,
        10 => 2  // fastest
    ];

    $animation_duration = isset($speed_map[$scroll_speed]) ? $speed_map[$scroll_speed] : 12;

    ?>
    <style>
    .lastfm-track {
        overflow: hidden;
        flex-grow: 1;
        display: block;
    }
    .lastfm-track-text {
        display: inline-block;
        white-space: nowrap;
        padding-right: 0;
    }
    @keyframes scroll-left-right {
        0% { transform: translateX(0); }
        20% { transform: translateX(0); }
        80% { transform: translateX(var(--scroll-distance)); }
        90% { transform: translateX(var(--scroll-distance)); }
        100% { transform: translateX(0); }
    }
    .lastfm-track-text.scrolling {
        animation: scroll-left-right <?php echo $animation_duration; ?>s linear infinite;
    }
    </style>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const scrollEnabled = <?php echo $scroll_enabled ? 'true' : 'false'; ?>;
        if (!scrollEnabled) return;

        document.querySelectorAll(".lastfm-track").forEach(function(container) {
            const text = container.querySelector(".lastfm-track-text");
            if (!text) return;

            const containerWidth = container.clientWidth;
            const textWidth = text.scrollWidth;

            if (textWidth > containerWidth) {
                text.style.setProperty('--scroll-distance', `-${textWidth - containerWidth}px`);
                text.classList.add("scrolling");
            } else {
                text.classList.remove("scrolling");
                text.style.removeProperty('--scroll-distance');
            }
        });
    });
    </script>

    <?php
}

// Load on frontend and admin preview
add_action('wp_footer', 'lastfm_nowplaying_enqueue_scripts');
add_action('admin_footer', 'lastfm_nowplaying_enqueue_scripts');

class LastFM_NowPlaying_Widget extends WP_Widget {

    public function render_lastfm_widget_output($instance, $preview = false) {
        // --- Settings ---
        $width = isset($instance['width']) ? $instance['width'] : get_option('lastfm_nowplaying_width', 200);
        $height = isset($instance['height']) ? $instance['height'] : get_option('lastfm_nowplaying_height', 50);
        $text_size = isset($instance['text_size']) ? intval($instance['text_size']) : get_option('lastfm_nowplaying_text_size', 14);
        $show_album_art = isset($instance['show_album_art']) ? $instance['show_album_art'] : get_option('lastfm_nowplaying_album_art', 1);
        $show_playcount = isset($instance['show_playcount']) ? $instance['show_playcount'] : get_option('lastfm_nowplaying_playcount', 1);
        $link_username = get_option('lastfm_nowplaying_username_link', 1);
        $username = isset($instance['username']) ? $instance['username'] : get_option('lastfm_nowplaying_username', 'demoUser');
        $scroll_enabled = get_option('lastfm_nowplaying_scroll_enabled', 1);
        $scroll_speed = get_option('lastfm_nowplaying_scroll_speed', 5);

        // --- Track info (preview or live) ---
        if ($preview) {
            $track_name = "911 / Mr. Lonely (feat. Frank Ocean & Steve Lacy)";
            $artist_name = "Tyler, The Creator";
            $track_url = "https://www.last.fm/music/Tyler,+The+Creator/_/911+%2F+Mr.+Lonely+(feat.+Frank+Ocean+&+Steve+Lacy)";
            $artist_url = "https://www.last.fm/music/Tyler,+The+Creator";
            $album_url = "https://www.last.fm/music/Tyler,+The+Creator/Flower+Boy";
            $album_img = "https://lastfm.freetls.fastly.net/i/u/770x0/52a7f32bdc99238080b0f17e859b3b4d.jpg#52a7f32bdc99238080b0f17e859b3b4d";
            $album_title = "Flower Boy";
            $user_playcount = 999;
        } else {
            // Read API key from settings
            $api_key = get_option('lastfm_nowplaying_api_key', '');
            if (empty($api_key)) {
                echo '<em>Please set your Last.fm API key in the settings.</em>';
                return;
            }

            $recent_url = "https://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user=" . urlencode($username) . "&api_key={$api_key}&format=json&limit=1";
            $recent_response = wp_remote_get($recent_url);

            if (is_wp_error($recent_response)) {
                echo '<em>Error contacting Last.fm.</em>';
                return;
            }

            $recent_body = wp_remote_retrieve_body($recent_response);
            $recent_data = json_decode($recent_body, true);
            if (!$recent_data || empty($recent_data['recenttracks']['track'][0])) {
                // If Last.fm returned an error message, show it for easier debugging
                if (!empty($recent_data['message'])) {
                    echo '<em>Last.fm error: ' . esc_html($recent_data['message']) . '</em>';
                } else {
                    echo '<em>No track data available.</em>';
                }
                return;
            }

            $track = $recent_data['recenttracks']['track'][0];
            $track_name = isset($track['name']) ? $track['name'] : 'Unknown Track';
            $artist_name = isset($track['artist']['#text']) ? $track['artist']['#text'] : 'Unknown Artist';
            $track_url = esc_url(isset($track['url']) ? $track['url'] : '');
            $artist_url = esc_url(isset($track['artist']['url']) ? $track['artist']['url'] : '');

            // Keep a raw album title for building fallback URLs, and an escaped title for display
            $raw_album_title = isset($track['album']['#text']) ? $track['album']['#text'] : 'Unknown Album';
            $album_title = esc_html($raw_album_title);

            // Use API-provided album URL when available, otherwise fallback to Last.fm album page
            $album_url = esc_url(isset($track['album']['url']) ? $track['album']['url'] : '');
            if (empty($album_url)) {
                $album_url = 'https://www.last.fm/music/' . urlencode($artist_name) . '/' . urlencode($raw_album_title);
            }

            $album_img = '';
            $user_playcount = null; // default to null when unavailable

            // Prefer detailed track.getInfo which can include userplaycount and better album images
            $track_info_url = "https://ws.audioscrobbler.com/2.0/?method=track.getInfo&api_key={$api_key}&artist="
                . urlencode($artist_name)
                . "&track=" . urlencode($track_name)
                . "&username=" . urlencode($username)
                . "&format=json";

            $info_response = wp_remote_get($track_info_url);
            if (!is_wp_error($info_response)) {
                $info_data = json_decode(wp_remote_retrieve_body($info_response), true);
                $info_track = $info_data['track'] ?? null;

                if ($info_track) {
                    // userplaycount is only present when username is provided to track.getInfo
                    if (isset($info_track['userplaycount'])) {
                        $user_playcount = intval($info_track['userplaycount']);
                    }

                    // Album URL from track.getInfo when available
                    if (!empty($info_track['album']['url'])) {
                        $album_url = esc_url($info_track['album']['url']);
                    }

                    // Pick the 'large' image when possible, otherwise fallback to other sizes
                    if (!empty($info_track['album']['image']) && is_array($info_track['album']['image'])) {
                        $preferred = '';
                        $fallback = '';
                        foreach ($info_track['album']['image'] as $img) {
                            if (empty($img['#text'])) continue;
                            // prefer 'large', then 'extralarge', then any available
                            if (isset($img['size']) && $img['size'] === 'large') {
                                $preferred = $img['#text'];
                                break;
                            }
                            if (isset($img['size']) && $img['size'] === 'extralarge' && empty($preferred)) {
                                $preferred = $img['#text'];
                            }
                            if (empty($fallback)) {
                                $fallback = $img['#text'];
                            }
                        }
                        $album_img = esc_url($preferred ?: $fallback);
                    }
                }
            }

            // If track.getInfo didn't provide an image, fall back to the recenttracks data
            if (empty($album_img) && !empty($track['album']['image'])) {
                // prefer large if available
                $found = '';
                $fallback = '';
                foreach ($track['album']['image'] as $img) {
                    if (empty($img['#text'])) continue;
                    if ($img['size'] === 'large') {
                        $found = $img['#text'];
                        break;
                    }
                    if ($img['size'] === 'extralarge' && empty($found)) {
                        $found = $img['#text'];
                    }
                    if (empty($fallback)) {
                        $fallback = $img['#text'];
                    }
                }
                $album_img = esc_url($found ?: $fallback);
            }

            // Final fallback: plugin placeholder image (only if it exists)
            if (empty($album_img)) {
                $placeholder_path = plugin_dir_path(__FILE__) . 'assets/placeholder.png';
                if (file_exists($placeholder_path)) {
                    $album_img = esc_url(plugin_dir_url(__FILE__) . 'assets/placeholder.png');
                } else {
                    $album_img = '';
                }
            }
        }

        // --- Output Widget ---
        ?>
        <div class="lastfm-widget"<?php if ($height) echo ' style="height:' . intval($height) . 'px"'; ?>>
            <?php if ($show_album_art): ?>
                <?php if (!empty($album_img)): ?>
                    <a href="<?php echo $album_url; ?>" target="_blank">
                        <img src="<?php echo $album_img; ?>" alt="<?php echo $album_title; ?>" class="lastfm-album-art" />
                    </a>
                <?php else: ?>
                    <a href="<?php echo $album_url; ?>" target="_blank" style="display:flex; align-items:center; justify-content:center; width:48px; height:48px; border:1px solid #ccc; font-size:10px; text-align:center; background:#f9f9f9; color:#333; text-decoration:none;">
                        <?php echo $album_title; ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <div class="text-column">
                <div class="lastfm-track" style="overflow:hidden;">
                    <div class="lastfm-track-text">
                        <strong>Now Playing:</strong>
                        <a href="<?php echo $track_url; ?>" target="_blank"><?php echo esc_html($track_name); ?></a>
                        by
                        <a href="<?php echo $artist_url; ?>" target="_blank"><?php echo esc_html($artist_name); ?></a>
                    </div>
                </div>

                <?php if ($show_playcount): ?>
                    <div class="playcount-line">
                        <?php
                        $username_html = esc_html($username);
                        if ($link_username) {
                            $username_html = '<a href="https://www.last.fm/user/' . urlencode($username) . '" target="_blank" class="lastfm-username">' . esc_html($username) . '</a>';
                        }

                        if ($user_playcount === 0) {
                            echo $username_html . "'s first scrobble!";
                        } elseif ($user_playcount > 0) {
                            echo $username_html . ' has scrobbled this ' . intval($user_playcount) . ' times';
                        } elseif ($user_playcount === null) {
                            echo 'Playcount unavailable';
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <style>
        .lastfm-widget {
            border: 1px solid #000;
            padding: 5px;
            width: <?php echo $width; ?>px;
            overflow: hidden;
            display: flex;
            flex-direction: row;
            align-items: center;
        }
        .lastfm-widget .text-column {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            overflow: hidden;
        }
        .lastfm-album-art {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border: 1px solid #ccc;
            margin-right: 8px;
            flex-shrink: 0;
        }
        .lastfm-track-text {
            font-size: <?php echo intval($text_size); ?>px;
            line-height: 1.4em;
            white-space: nowrap;
        }
        .playcount-line {
            font-size: <?php echo intval($text_size); ?>px;
            line-height: 1.4em;
            color: #000;
            margin-top: 4px;
        }
        .playcount-line .lastfm-username {
            color: #d51007;
            font-weight: bold;
            text-decoration: none;
        }
        .playcount-line .lastfm-username:hover {
            text-decoration: underline;
        }
        @keyframes scroll-left-right {
            0% { transform: translateX(0); }
            20% { transform: translateX(0); }
            80% { transform: translateX(var(--scroll-distance)); }
            90% { transform: translateX(var(--scroll-distance)); }
            100% { transform: translateX(0); }
        }
        .lastfm-track-text.scrolling {
            animation: scroll-left-right <?php echo 12; ?>s linear infinite;
        }
        </style>

        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const scrollEnabled = <?php echo $scroll_enabled ? 'true' : 'false'; ?>;
            if (!scrollEnabled) return;

            document.querySelectorAll(".lastfm-track").forEach(function(container) {
                const text = container.querySelector(".lastfm-track-text");
                if (!text) return;

                const containerWidth = container.clientWidth;
                const overflowWidth = text.scrollWidth - containerWidth;

                if (overflowWidth > 0) {
                    text.style.setProperty('--scroll-distance', `-${overflowWidth}px`);
                    text.classList.add("scrolling");
                } else {
                    text.classList.remove("scrolling");
                    text.style.removeProperty('--scroll-distance');
                }
            });
        });
        </script>
        <?php
    }

    public function __construct() {
        parent::__construct(
            'lastfm_nowplaying_widget',
            'Last.fm Now Playing',
            array('description' => 'Displays the currently playing or last played track')
        );
    }

    public function form($instance) {
        $this->render_lastfm_widget_output($instance, true);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('width'); ?>">Box Width (px):</label>
            <input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" 
                   name="<?php echo $this->get_field_name('width'); ?>" type="number" 
                   value="<?php echo esc_attr(isset($instance['width']) ? $instance['width'] : 200); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('height'); ?>">Box Height (px):</label>
            <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" 
                   name="<?php echo $this->get_field_name('height'); ?>" type="number" 
                   value="<?php echo esc_attr(isset($instance['height']) ? $instance['height'] : 50); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('text_size'); ?>">Text Size (px):</label>
            <input class="widefat" id="<?php echo $this->get_field_id('text_size'); ?>" 
                   name="<?php echo $this->get_field_name('text_size'); ?>" type="number" 
                   value="<?php echo esc_attr(isset($instance['text_size']) ? $instance['text_size'] : 14); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['width'] = intval($new_instance['width']);
        $instance['height'] = intval($new_instance['height']);
        $instance['text_size'] = intval($new_instance['text_size']);
        return $instance;
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        $this->render_lastfm_widget_output($instance);
        echo $args['after_widget'];
    }
}

// === Register the widget with WordPress ===
function lastfm_nowplaying_register_widget() {
    register_widget('LastFM_NowPlaying_Widget');
}
add_action('widgets_init', 'lastfm_nowplaying_register_widget');

function lastfm_nowplaying_shortcode() {
    ob_start();
    the_widget('LastFM_NowPlaying_Widget');
    return ob_get_clean();
}
add_shortcode('lastfm_nowplaying', 'lastfm_nowplaying_shortcode');
