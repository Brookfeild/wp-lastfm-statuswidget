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
    register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_second_line_enabled', array('default' => 1)); // default = TRUE
    register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_second_line_text', array('default' => 'Check out everything I listen to on last.fm!'));
    register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_scroll_enabled', array('default' => 1));
    register_setting('lastfm_nowplaying_options', 'lastfm_nowplaying_scroll_speed', array('default' => 5));

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

    // Second line toggle
    add_settings_field(
        'lastfm_nowplaying_second_line_enabled',
        'Enable Second Line',
        function() {
            $value = get_option('lastfm_nowplaying_second_line_enabled', 1);
            $checked = $value ? 'checked' : '';
            echo '<input type="checkbox" name="lastfm_nowplaying_second_line_enabled" value="1" ' . $checked . ' /> Enable';
        },
        'lastfm_nowplaying',
        'lastfm_nowplaying_section'
    );

    // Second line text
    add_settings_field(
        'lastfm_nowplaying_second_line_text',
        'Second Line Text',
        function() {
            $value = get_option('lastfm_nowplaying_second_line_text', 'Check out everything I listen to on last.fm!');
            echo '<input type="text" style="width:400px" name="lastfm_nowplaying_second_line_text" value="' . esc_attr($value) . '" />';
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
}
add_action('admin_init', 'lastfm_nowplaying_register_settings');


// === Add settings page to WP Admin ===
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
                // Fetch saved or default settings
                $username  = get_option('lastfm_nowplaying_username', '');
                $width     = get_option('lastfm_nowplaying_width', 200);
                $height    = get_option('lastfm_nowplaying_height', 50);
                $text_size = get_option('lastfm_nowplaying_text_size', 14);
                $second_line_enabled = get_option('lastfm_nowplaying_second_line_enabled', 1);
                $second_line_text = get_option(
                    'lastfm_nowplaying_second_line_text',
                    'Check out everything I listen to on last.fm!'
                );

                if ($username) {
                    $api_key = 'fd4bc04c5f3387f5b0b5f4f7bae504b9'; // replace with your key
                    $url = "https://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user={$username}&api_key={$api_key}&format=json&limit=1";
                    $response = wp_remote_get($url);

                    $track_name = $artist_name = 'No track found';
                    $title = 'Last Played:';

                    if (!is_wp_error($response)) {
                        $data = json_decode(wp_remote_retrieve_body($response), true);
                        if (!empty($data['recenttracks']['track'][0])) {
                            $track = $data['recenttracks']['track'][0];
                            $track_name = esc_html($track['name']);
                            $artist_name = esc_html($track['artist']['#text']);
                            $title = isset($track['@attr']['nowplaying']) ? 'Now Playing:' : 'Last Played:';
                        }
                    }

                    echo "<div style='border:1px solid #000; padding:2px; width:{$width}px; font-size:{$text_size}px; overflow:hidden;'>";

                    echo "<strong>{$title}</strong> ";
                    echo "<div class='lastfm-track' style='display:inline-block; width:" . ($width - 20) . "px; overflow:hidden; vertical-align:middle; white-space:nowrap;'>";
                    echo "<span class='lastfm-track-text'>{$track_name} by {$artist_name}</span>";
                    echo "</div>";

                    if ($second_line_enabled) {
                        $link = esc_url("https://www.last.fm/user/" . urlencode($username));
                        echo "<br/><a href='{$link}' target='_blank'>" . esc_html($second_line_text) . "</a>";
                    }
                    echo "</div>";
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
        white-space: nowrap;
        display: inline-block;
    }

    .lastfm-track-text {
        display: inline-block;
        padding-right: 2px; /* minimal gap at the end */
    }

    @keyframes scroll-left-right {
        0% { transform: translateX(0); }
        20% { transform: translateX(0); } /* wait 15s at left */
        80% { transform: translateX(var(--scroll-distance)); } /* scroll fully left */
        90% { transform: translateX(var(--scroll-distance)); } /* wait 5s */
        100% { transform: translateX(0); } /* return to start */
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
            if (text && text.scrollWidth > container.clientWidth) {
                // Only scroll the overflow portion, not the full text width
                const overflowWidth = text.scrollWidth - container.clientWidth;
                text.style.setProperty('--scroll-distance', `-${overflowWidth}px`);
                text.classList.add("scrolling");
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

    public function __construct() {
        parent::__construct(
            'lastfm_nowplaying_widget',
            'Last.fm Now Playing',
            array('description' => 'Displays the currently playing or last played track')
        );
    }

    // Widget settings form in admin
    public function form($instance) {
        $width  = isset($instance['width']) ? $instance['width'] : 200;
        $height = isset($instance['height']) ? $instance['height'] : 50;
        $text_size = isset($instance['text_size']) ? $instance['text_size'] : 14;

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('width'); ?>">Box Width (px):</label>
            <input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" 
                   name="<?php echo $this->get_field_name('width'); ?>" type="number" 
                   value="<?php echo esc_attr($width); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('height'); ?>">Box Height (px):</label>
            <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" 
                   name="<?php echo $this->get_field_name('height'); ?>" type="number" 
                   value="<?php echo esc_attr($height); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('text_size'); ?>">Text Size (px):</label>
            <input class="widefat" id="<?php echo $this->get_field_id('text_size'); ?>" 
                   name="<?php echo $this->get_field_name('text_size'); ?>" type="number" 
                   value="<?php echo esc_attr($text_size); ?>" />
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
        $username = get_option('lastfm_nowplaying_username', '');
        if (!$username) {
            echo $args['before_widget'] . 'Please set a Last.fm username in Settings.' . $args['after_widget'];
            return;
        }

        // Settings
        $second_line_enabled = get_option('lastfm_nowplaying_second_line_enabled', 1);
        $second_line_text    = get_option('lastfm_nowplaying_second_line_text', 'Check out everything I listen to on last.fm!');
        $scroll_enabled      = get_option('lastfm_nowplaying_scroll_enabled', 1);
        $scroll_speed        = get_option('lastfm_nowplaying_scroll_speed', 5); // 1-10
        $width               = isset($instance['width']) ? $instance['width'] : get_option('lastfm_nowplaying_width', 200);
        $height              = isset($instance['height']) ? $instance['height'] : get_option('lastfm_nowplaying_height', 50);
        $text_size           = isset($instance['text_size']) ? $instance['text_size'] : get_option('lastfm_nowplaying_text_size', 14);

        // Map scroll speed (1=slow, 10=fast) to animation duration (s)
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


        // Fetch recent track from Last.fm API
        $api_key = 'fd4bc04c5f3387f5b0b5f4f7bae504b9';
        $url     = "https://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user={$username}&api_key={$api_key}&format=json&limit=1";

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

        $track       = $data['recenttracks']['track'][0];
        $track_name  = esc_html($track['name']);
        $artist_name = esc_html($track['artist']['#text']);
        $now_playing = isset($track['@attr']['nowplaying']);
        $title       = $now_playing ? 'Now Playing:' : 'Last Played:';

        // Output widget HTML
        echo $args['before_widget'];
        ?>
        <div style="border:1px solid #000; padding:2px; width:<?php echo $width; ?>px; font-size:<?php echo $text_size; ?>px; overflow:hidden; display:flex; flex-direction:column; justify-content:center; min-height:<?php echo $height; ?>px;">
            <strong><?php echo $title; ?></strong>
            <div class="lastfm-track" style="display:inline-block; width:<?php echo $width - 20; ?>px; overflow:hidden; white-space:nowrap;">
                <span class="lastfm-track-text"><?php echo "{$track_name} by {$artist_name}"; ?></span>
            </div>

            <?php if ($second_line_enabled && !empty($second_line_text)) : ?>
                <a href="https://www.last.fm/user/<?php echo urlencode($username); ?>" target="_blank"><?php echo esc_html($second_line_text); ?></a>
            <?php endif; ?>
        </div>

        <style>
        .lastfm-track {
            overflow: hidden;
            white-space: nowrap;
            display: inline-block;
        }

        .lastfm-track-text {
            display: inline-block;
            padding-right: 2px; /* minimal space at end */
        }

        @keyframes scroll-left-right {
            0% { transform: translateX(0); }      /* start */
            20% { transform: translateX(0); }     /* wait 15s at left */
            80% { transform: translateX(var(--scroll-distance)); } /* scroll rightmost */
            90% { transform: translateX(var(--scroll-distance)); } /* wait 5s */
            100% { transform: translateX(0); }    /* return to start */
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
                if (text && text.scrollWidth > container.clientWidth) {
                    // Only scroll the overflow portion, not the full text width
                    const overflowWidth = text.scrollWidth - container.clientWidth;
                    text.style.setProperty('--scroll-distance', `-${overflowWidth}px`);
                    text.classList.add("scrolling");
                }
            });
        });
        </script>
        <?php
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
