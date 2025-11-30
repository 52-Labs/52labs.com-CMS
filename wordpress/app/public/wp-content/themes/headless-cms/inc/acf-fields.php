<?php

/*
 * 52 LABS NOTE:
 * This file is temporarily disabled because it registers ACF Field Groups using Pro features (Options Page, Gallery, Repeater)
 * which are not available in the installed ACF Free version.
 *
 * Disabling this allows the Field Groups to be managed directly in the WordPress Admin (synced from DB/JSON),
 * where you can update the Location Rules (e.g. to a specific Page instead of Options Page) and fix incompatible Field Types
 * (e.g. change 'Gallery' to 'Group' or 'Image').
 */

if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title'    => 'Site Settings',
        'menu_title'    => 'Site Settings',
        'menu_slug'     => 'site-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false,
        'show_in_graphql' => true,
        'graphql_field_name' => 'siteSettings'
    ));
}

/*
 * ACF Field Groups are now managed entirely via the ACF admin UI.
 * 
 * Previously, field groups were registered here via acf_add_local_field_group(),
 * but this prevented UI changes from persisting. Now you can freely edit
 * Product Fields, Category Fields, and Site Settings in the ACF admin.
 */
