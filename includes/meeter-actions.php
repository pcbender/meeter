<?php
/**
 * Meeter Actions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// BuddyPress / WordPress actions to Meeter ones
add_action( 'bp_init',                  'meeter_init',                     14 );
add_action( 'bp_ready',                 'meeter_ready',                    10 );
add_action( 'bp_setup_current_user',    'meeter_setup_current_user',       10 );
add_action( 'bp_setup_theme',           'meeter_setup_theme',              10 );
add_action( 'bp_after_setup_theme',     'meeter_after_setup_theme',        10 );
add_action( 'bp_enqueue_scripts',       'meeter_register_scripts',          1 );
add_action( 'bp_admin_enqueue_scripts', 'meeter_register_scripts',          1 );
add_action( 'bp_enqueue_scripts',       'meeter_enqueue_scripts',          10 );
add_action( 'bp_setup_admin_bar',       'meeter_setup_admin_bar',          10 );
add_action( 'bp_actions',               'meeter_actions',                  10 );
add_action( 'bp_screens',               'meeter_screens',                  10 );
add_action( 'admin_init',               'meeter_admin_init',               10 );
add_action( 'admin_head',               'meeter_admin_head',               10 );

function meeter_init(){
	do_action( 'meeter_init' );
}

function meeter_ready(){
	do_action( 'meeter_ready' );
}

function meeter_setup_current_user(){
	do_action( 'meeter_setup_current_user' );
}

function meeter_setup_theme(){
	do_action( 'meeter_setup_theme' );
}

function meeter_after_setup_theme(){
	do_action( 'meeter_after_setup_theme' );
}

function meeter_register_scripts() {
	do_action( 'meeter_register_scripts' );
}

function meeter_enqueue_scripts(){
	do_action( 'meeter_enqueue_scripts' );
}

function meeter_setup_admin_bar(){
	do_action( 'meeter_setup_admin_bar' );
}

function meeter_actions(){
	do_action( 'meeter_actions' );
}

function meeter_screens(){
	do_action( 'meeter_screens' );
}

function meeter_admin_init() {
	do_action( 'meeter_admin_init' );
}

function meeter_admin_head() {
	do_action( 'meeter_admin_head' );
}