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

// register custom admin column headers
add_filter('manage_edit-slb_subscriber_columns', 'slb_subscriber_column_headers');
add_filter('manage_edit-slb_list_columns', 'slb_list_column_headers');

// register custom admin column data
add_filter('manage_slb_subscriber_posts_custom_column', 'slb_subscriber_column_data', 1, 2);
add_action(
    'admin_head-edit.php',
    'slb_register_custom_admin_titles'
);
add_filter('manage_slb_list_posts_custom_column', 'slb_list_column_data', 1, 2);


// register ajax actions
add_action('wp_ajax_nopriv_slb_save_subscription', 'slb_save_subscription'); // regular website visitor
add_action('wp_ajax_slb_save_subscription', 'slb_save_subscription'); // admin user


// load external files to public website
add_action('wp_enqueue_scripts', 'slb_public_scripts');


// advanced custom fields settings
add_filter('acf/settings/path', 'slb_acf_settings_path');
add_filter('acf/settings/dir', 'slb_acf_settings_dir');
add_filter('acf/settings/show_admin', 'slb_acf_show_admin');
//if(!defined('ACF_LITE')) define('ACF_LITE', true); // turn off ACF plugin menu


// SHORTCODES
// registers all our custom shortcodes
function slb_register_shortcodes() {
    add_shortcode('slb_form', 'slb_form_shortcode');
}

function slb_form_shortcode($args, $content="") {

    // get the list id
    $list_id = 0;
    if(isset($args['id'])) $list_id = (int)$args['id'];

    $title = 0;
    if(isset($args['title'])) $title = (string)$args['title'];

    // setout our output variable - the form html
    $output = '
        <div class="slb">

            <form id="slb_form" name="slb_form" class="slb-form" method="post"
            action="/wp-admin/admin-ajax.php?action=slb_save_subscription" method="post">

                <input type="hidden" name="slb_list" value="'.$list_id.'">';

                if(strlen($title)):
                    $output .= '<h3 class="slb-title">'. $title .'</h3>';
                endif;

                $output .= '<p class="slb-input-container">
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

// FILTERS
function slb_subscriber_column_headers($columns) {
    // creating custom column header data
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Subscriber Name'),
        'email' => __('Email Address'),
    );

    // returning new columns
    return $columns;
}

function slb_subscriber_column_data($column, $post_id) {
    // setup our return text
    $output = '';

    switch($column) {
        case 'title':
            // get the custom name data
            $fname = get_field('slb_fname', $post_id);
            $lname = get_field('slb_lname', $post_id);
            $output .= $fname .' '. $lname;
            break;
        case 'email':
            // get the custom email data
            $email = get_field('slb_email', $post_id);
            $output .= $email;
            break;
    }
    echo $output;
}

// registers special custom admin title columns
function slb_register_custom_admin_titles() {
    add_filter(
        'the_title',
        'slb_custom_admin_titles',
        99,
        2
    );
}

// handles custom admin title "title" column data for post types without titles
function slb_custom_admin_titles($title, $post_id) {
    global $post;
    $output = $title;
    if(isset($post->post_type)):
        switch($post->post_type) {
            case 'slb_subscriber':
                $fname = get_field('slb_fname', $post_id);
                $lname = get_field('slb_lname', $post_id);
                $output = $fname . ' '. $lname;
                break;
        }
    endif;

    return $output;
}

function slb_list_column_headers($columns) {
    // creating custom column header data
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('List Name'),
        'shortcode' => __('Shortcode'),
    );

    // returning new columns
    return $columns;
}

function slb_list_column_data($column, $post_id) {
    // setup our return text
    $output = '';

    switch($column) {
        case 'shortcode':
            $output .= '[slb_form id="'. $post_id . '"]';
            break;
    }
    echo $output;
}


// EXTERNAL SCRIPTS
// include ACF
include_once(plugin_dir_path(__FILE__) . 'lib/advanced-custom-fields/acf.php');

// loads external files into PUBLIC website
function slb_public_scripts() {
    // register scripts with WordPress's internal library
    wp_register_script('snappy-list-builder-js-public', plugins_url('/js/public/snappy-list-builder.js', __FILE__),
        array('jquery'), '', true);

    wp_register_style('snappy-list-builder-css-public', plugins_url('/css/public/snappy-list-builder.css', __FILE__));

    // add to que of scripts that get loaded into every page
    wp_enqueue_script('snappy-list-builder-js-public');
    wp_enqueue_style('snappy-list-builder-css-public');
}


// ACTIONS
function slb_save_subscription() {
    // setup default result data
    $result = array(
        'status' => 0,
        'message' => 'Subscription was not saved. ',
    );

    // array for storing errors
    $errors = array();

    try {
        // get list_id
        $list_id = (int)$_POST['slb_list'];

        // prepare subscriber data
        $subscriber_data = array(
            'fname' => esc_attr($_POST['slb_fname']),
            'lname' => esc_attr($_POST['slb_lname']),
            'email' => esc_attr($_POST['slb_email']),
        );

        // attempt to create/save subscriber
        $subscriber_id = slb_save_subscriber($subscriber_data);

        // IF subscriber was saved successfully $subscriber_id will be greater than 0
        if($subscriber_id):
            // IF subscriber already has this subscription
            if(slb_subscriber_has_subscription($subscriber_id, $list_id)):
                // get list object
                $list = get_post($list_id);

                // return detailed error
                $result['message'] .= esc_attr($subscriber_data['email'] .' is already subscribed to '.$list->post_title . '.');
            else:
                // save new subscription
                $subscription_saved = slb_add_subscription($subscriber_id, $list_id);

                // IF subscription was saved successfully
                if($subscription_saved):
                    // subscription saved!
                    $result['status'] = 1;
                    $result['message'] = 'Subscription saved';
                endif;
            endif;
        endif;
    } catch (Exception $e) {
        // a php error occurred
        $result['message'] = 'Caught exception: '. $e->getMessage();
    }

    // return result as json
    slb_return_json($result);
}

function slb_save_subscriber($subscriber_data) {
    // setup default subs id
    // 0 means the subs was not saved
    $subscriber_id = 0;

    try {
        $subscriber_id = slb_get_subscriber_id($subscriber_data['email']);

        // IF the subscriber does not already exists...
        if(!$subscriber_id):
            // add new subscriber to database
            $subscriber_id = wp_insert_post(
                array(
                    'post_type'=>'slb_subscriber',
                    'post_title'=>$subscriber_data['fname'] .' '. $subscriber_data['lname'],
                    'post_status'=>'publish',
                ),
                true
            );
        endif;

        // add/update custom meta data
        // TODO (?) why update_field
        update_field(slb_get_acf_key('slb_fname'), $subscriber_data['fname'], $subscriber_id);
        update_field(slb_get_acf_key('slb_lname'), $subscriber_data['lname'], $subscriber_id);
        update_field(slb_get_acf_key('slb_email'), $subscriber_data['email'], $subscriber_id);
    } catch(Exception $e) {
        // a php error occurred
    }

    // reset the Wordpress post object
    wp_reset_query();

    // return subscriber_id
    return $subscriber_id;
}

// adds list to subscribers subscriptions
function slb_add_subscription($subscriber_id, $list_id) {
    // setup default return value
    $subscription_saved = false;

    // IF the subscriber does not have the current list subscription
    if(!slb_subscriber_has_subscription($subscriber_id, $list_id)):

        // get subscriptions and append new $list_id
        $subscriptions = slb_get_subscriptions($subscriber_id);
        array_push($subscriptions, $list_id);

        // update slb_subscriptions
        update_field(slb_get_acf_key('slb_subscriptions'), $subscriptions, $subscriber_id);

        // subscriptions updated!
        $subscription_saved = true;
    endif;

    // return result
    return $subscription_saved;
}

// HELPERS
function slb_subscriber_has_subscription($subscriber_id, $list_id) {
    // setup defailt return value
    $has_subscription = false;

    // get subscriber
    $subscriber = get_post($subscriber_id);

    // get subscriptions
    $subscriptions = slb_get_subscriptions($subscriber_id);

    // check subscriptions for $list_id
    if(in_array($list_id, $subscriptions)):
        // found the $list_id in $subscriptions
        // this subscriber is already subscribed to this list
        $has_subscription = true;
    else:
        // did not find $list_id in $subscriptions
        // this subscriber is not yet subscribed to this list
    endif;

    return $has_subscription;
}

function slb_get_subscriber_id($email) {
    $subscriber_id = 0;

    try {
        // check if subscriber already exists
        $subscriber_query = new WP_Query(
            array(
                'post_type' => 'slb_subscriber',
                'posts_per_page' => 1,
                'meta_key' => 'slb_email',
                'meta_query' => array(
                    array(
                        'key' => 'slb_email',
                        'value' => $email, // or whatever it is you're using here
                        'compare' => '=',
                    ),
                ),
            )
        );

        // IF the subscriber exists ...
        if($subscriber_query->have_posts()):
            // get the subscriber_id
            $subscriber_query->the_post();
            $subscriber_id = get_the_ID();
        endif;
    } catch(Exception $e) {
        // a php error occurred
    }

    // reset the Wordpress post object
    wp_reset_query();

    return (int)$subscriber_id;
}

function slb_get_subscriptions($subscriber_id) {
    $subscriptions = array();

    // get subscriptions (returns array of list objects)
    $lists = get_field(slb_get_acf_key('slb_subscriptions'), $subscriber_id);

    // IF $lists returns something
    if($lists):

        // IF $lists is an array and there is one or more items
        if(is_array($lists) && count($lists)):
            // build subscriptions: array of list id's
            foreach($lists as &$list):
                $subscriptions[]= (int)$list->ID; // array_push($subscriptions, $new_value)
            endforeach;
        elseif(is_numeric($lists)):
            // single result returned
            $subscriptions[]= $lists;
        endif;
    endif;

    return (array)$subscriptions;
}

function slb_return_json($php_array){
    // encode result as json string
    $json_result = json_encode($php_array);

    // return result
    die($json_result);

    // stop all other processing
    exit;
}

// get the unique act field key from the field name
function slb_get_acf_key($field_name) {
    $field_key = $field_name;
    switch($field_name) {
        case 'slb_fname':
            $field_key = 'field_5cfc92d46e79d';
            break;
        case 'slb_lname':
            $field_key = 'field_5cfc92f76e79e';
            break;
        case 'slb_email':
            $field_key = 'field_5cfc93176e79f';
            break;
        case 'slb_subscription':
            $field_key = 'field_5cfc93206e7a0';
            break;
    }

    return $field_key;
}

// returns an array of subscriber data includin g subscription
function slb_get_subscriber_data($subscriber_id) {
    // setup subscriber_data
    $subscriber_data = array();

    // get subscriber object
    $subscriber = get_post($subscriber_id);

    // IF subscriber object is valid
    if(isset($subscriber->post_type) && $subscriber->post_type == 'slb_subscriber'):

        $fname = get_field(slb_get_acf_key('slb_fname'), $subscriber_id);
        $lname = get_field(slb_get_acf_key('slb_lname'), $subscriber_id);

        // build subscriber_data for return
        $subscriber_data = array(
            'name' => $fname. ' ' . $lname,
            'fname' => $fname,
            'lname' => $lname,
            'email' => get_field(slb_get_acf_key('slb_email'), $subscriber_id),
            'subscriptions' => slb_get_subscriptions($subscriber_id)
        );
    endif;

    // return subscriber_data
    return $subscriber_data;
}


