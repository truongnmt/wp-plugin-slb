<?php

/*
Plugin Name: Snappy List Builderr
Plugin URI: https://truongnmt.github.io
Description: The ultimate email list building plugin for WordPress. Capture new subscribers. Reward subs with a custom download upon opt-in. Build unlimited lists. Import and export subs easily with .csv
Version: 1.0
Author: Joel Long @ Home
Author URI: https://truongnmt.github.io
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: snappy-list-builderr
*/

// HOOKS
// registers all our custom shortcodes on init
add_action('init', 'slb_register_shortcodes');

// SHORTCODES
// registers all our custom shortcodes
function slb_register_shortcodes() {
    add_shortcode('slb_form', 'slb_form_shortcode');
}

function slb_form_shortcode($args, $content="") {
    // setout our output variable - the form html
    $output = '
        <div class="slb">
            <form id="slb_form" name="slb_form" class="slb-form" method="post">
                <p class="slb-input-container">
                    <label>Your Name</label><br />
                    <input type="text" name="slb_fname" placeholder="First Name" />
                    <input type="text" name="slb_lname" placeholder="Last Name" />
                </p>

                <p class="slb-input-container">
                    <label>Your Email</label><br />
                    <input type="email" name="slb_email" placeholder="ex. you@email.com" />
                </p>';

                // including content in our form html if content is passed into the function
                if (strlen($content)):
                    $output .= '<div class="slb-content>' . wpautop($content) . '</div>';
                endif;

                // completing our form html
                $output .= '<p class="slb-input-container">
                    <input type="submit" name="slb_submit" value="Sign Me Up!" />
                </p>
            </form>
        </div>
    ';

    // return our results/html
    return $output;
}