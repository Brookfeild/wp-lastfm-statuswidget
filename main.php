<?php
/*
Plugin Name: Last.fm Status Widget
Description: Display the currently playing or last played track for a Last.fm username.
Version: 1.0
Author: Tyler
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

                if ($username) {
                    if (class_exists('LastFM_NowPlaying_Widget')) {
                        $widget = new LastFM_NowPlaying_Widget();

                        // Build an $instance array like WP would normally pass
                        $instance = [
                            'username'                 => $username,
                            'width'                    => get_option('lastfm_nowplaying_width', 200),
                            'height'                   => get_option('lastfm_nowplaying_height', 50),
                            'text_size'                => get_option('lastfm_nowplaying_text_size', 14),
                            'scroll_enabled'           => get_option('lastfm_nowplaying_scroll_enabled', 1),
                            'scroll_speed'             => get_option('lastfm_nowplaying_scroll_speed', 1),
                            'album_art_enabled'        => get_option('lastfm_nowplaying_album_art_enabled', 1),
                            'show_playcount'           => get_option('lastfm_nowplaying_show_playcount', 1),
                        ];

                        // Make sure render method is public
                        if (method_exists($widget, 'render_lastfm_widget_output')) {
                            $widget->render_lastfm_widget_output($instance, true);
                        } else {
                            echo '<em>Preview unavailable: render method not found.</em>';
                        }
                    } else {
                        echo '<em>Preview unavailable: widget class not loaded.</em>';
                    }
                } else {
                    echo '<em>Enter a Last.fm username above to see a preview.</em>';
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
        $width = isset($instance['width']) ? $instance['width'] : get_option('lastfm_nowplaying_width', 200);
        $height = isset($instance['height']) ? $instance['height'] : get_option('lastfm_nowplaying_height', 50);
        $text_size = isset($instance['text_size']) ? intval($instance['text_size']) : get_option('lastfm_nowplaying_text_size', 14);
        $show_album_art = isset($instance['show_album_art']) ? $instance['show_album_art'] : get_option('lastfm_nowplaying_album_art', 1);
        $show_playcount = isset($instance['show_playcount']) ? $instance['show_playcount'] : get_option('lastfm_nowplaying_playcount', 1);
        $username = isset($instance['username']) ? $instance['username'] : get_option('lastfm_nowplaying_username', 'demoUser');
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
            $api_key = 'fd4bc04c5f3387f5b0b5f4f7bae504b9';
            $recent_url = "https://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user={$username}&api_key={$api_key}&format=json&limit=1";
            $recent_response = wp_remote_get($recent_url);
            if (is_wp_error($recent_response)) {
                echo 'Error fetching track.';
                return;
            }
            $recent_data = json_decode(wp_remote_retrieve_body($recent_response), true);
            if (empty($recent_data['recenttracks']['track'][0])) {
                echo 'No tracks found.';
                return;
            }
            $track = $recent_data['recenttracks']['track'][0];
            $track_name  = $track['name'];
            $artist_name = $track['artist']['#text'];
            $title       = isset($track['@attr']['nowplaying']) ? 'Now Playing:' : 'Last Played:';
            $info_url = "https://ws.audioscrobbler.com/2.0/?method=track.getInfo&api_key={$api_key}&artist=" . urlencode($artist_name) . "&track=" . urlencode($track_name) . "&username=" . urlencode($username) . "&format=json";
            $info_res = wp_remote_get($info_url);
            $track_info = $track;
            if (!is_wp_error($info_res)) {
                $info_data = json_decode(wp_remote_retrieve_body($info_res), true);
                if (!empty($info_data['track'])) {
                    $track_info = array_merge($track, $info_data['track']);
                }
            }
            $track_url   = esc_url($track_info['url'] ?? '');
            $artist_url  = esc_url($track_info['artist']['url'] ?? '');
            $album_url   = esc_url($track_info['album']['url'] ?? '');
            $album_title = esc_html($track_info['album']['title'] ?? ($track_info['album']['#text'] ?? 'Unknown Album'));
            $album_img = '';
            $images = [];
            if (!empty($track_info['album']['image'])) {
                $images = $track_info['album']['image'];
            } elseif (!empty($track_info['image'])) {
                $images = $track_info['image'];
            }
            foreach (array_reverse($images) as $img) {
                if (!empty($img['#text'])) {
                    $album_img = esc_url($img['#text']);
                    break;
                }
            }
            $user_playcount = !empty($track_info['userplaycount']) ? intval($track_info['userplaycount']) : 0;
        }
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
            <div class="lastfm-track" style="overflow:hidden; flex-grow:1;">
                <div class="lastfm-track-text">
                    <strong>Now Playing:</strong>
                    <a href="<?php echo $track_url; ?>" target="_blank"><?php echo esc_html($track_name); ?></a>
                    by
                    <a href="<?php echo $artist_url; ?>" target="_blank"><?php echo esc_html($artist_name); ?></a>
                </div>
            </div>
            <?php if ($show_playcount) : ?>
                <div class="playcount-line">
                    <?php
                    $link_username = get_option('lastfm_nowplaying_username_link', 1);

                    $username_html = esc_html($username);
                    if ($link_username) {
                        $username_html = '<a href="https://www.last.fm/user/' . urlencode($username) . '" target="_blank" class="lastfm-username">' . esc_html($username) . '</a>';
                    }

                    if ($user_playcount === 0) {
                        echo $username_html . '\'s first scrobble!';
                    } else {
                        echo $username_html . ' has scrobbled this ' . intval($user_playcount) . ' times';
                    }
                    ?>
                </div>
            <?php endif; ?>
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
            min-height: <?php echo $height; ?>px;
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
            display: inline-block;
            padding-right: 0;
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
        </style>
        <?php
    }

    public function __construct() {
        parent::__construct(
            'lastfm_nowplaying_widget',
            'Last.fm Now Playing',
            array('description' => 'Displays the currently playing or last played track')
        );
    }

    // Widget settings form in admin
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

    // Update widget options
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['width']     = intval($new_instance['width']);
        $instance['height']    = intval($new_instance['height']);
        $instance['text_size'] = intval($new_instance['text_size']);
        return $instance;
    }

    // Frontend display
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
