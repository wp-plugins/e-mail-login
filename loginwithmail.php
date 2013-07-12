<?php
/*
Plugin Name: E-Mail Login
Description: Log-in to your wordpress site using the e-mail address or classic username. Simply and efficient.
Version: 1.3
Author: Marco Milesi
*/
 
add_filter('authenticate', 'mml_mail_login', 20, 3);

function mml_mail_login( $user, $username, $password ) {
    if ( is_email( $username ) ) {
        $user = get_user_by_email( $username );
        if ( $user ) $username = $user->user_login;
    }
    return wp_authenticate_username_password(null, $username, $password );
}
 
add_filter( 'gettext', 'mml_login_hack', 20, 3 );

function mml_login_hack( $translated_text, $text, $domain ) {
    if ( "Username" == $translated_text ) {
        $translated_text .= __( ' or email');
    return $translated_text;
	} elseif ( "Identifiant" == $translated_text ) {
        $translated_text .= __( ' ou email');
    return $translated_text;
	} elseif ( "Nome utente" == $translated_text ) {
        $translated_text .= __( ' o email');
    return $translated_text;
	} elseif ( "Benutzername" == $translated_text ) {
        $translated_text .= __( ' oder email');
    return $translated_text;
	} else {
	return $translated_text;
	}
}

	function presstrends_Maillogin_plugin() {

		// PressTrends Account API Key
		$api_key = 'abt3ep7uq9b2jzohwmefm3y5koqcsxguqx0a';
		$auth    = '31yul29ilrrw90hkjtngqx3vmqm0bc7e7';

		// Start of Metrics
		global $wpdb;
		$data = get_transient( 'presstrends_cache_data' );
		if ( !$data || $data == '' ) {
			$api_base = 'http://api.presstrends.io/index.php/api/pluginsites/update/auth/';
			$url      = $api_base . $auth . '/api/' . $api_key . '/';

			$count_posts    = wp_count_posts();
			$count_pages    = wp_count_posts( 'page' );
			$comments_count = wp_count_comments();

			// wp_get_theme was introduced in 3.4, for compatibility with older versions, let's do a workaround for now.
			if ( function_exists( 'wp_get_theme' ) ) {
				$theme_data = wp_get_theme();
				$theme_name = urlencode( $theme_data->Name );
			} else {
				$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
				$theme_name = $theme_data['Name'];
			}

			$plugin_name = '&';
			foreach ( get_plugins() as $plugin_info ) {
				$plugin_name .= $plugin_info['Name'] . '&';
			}
			// CHANGE __FILE__ PATH IF LOCATED OUTSIDE MAIN PLUGIN FILE
			$plugin_data         = get_plugin_data( __FILE__ );
			$posts_with_comments = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='post' AND comment_count > 0" );
			$data                = array(
				'url'             => 'null',
				'posts'           => $count_posts->publish,
				'pages'           => $count_pages->publish,
				'comments'        => $comments_count->total_comments,
				'approved'        => $comments_count->approved,
				'spam'            => $comments_count->spam,
				'pingbacks'       => $wpdb->get_var( "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_type = 'pingback'" ),
				'post_conversion' => ( $count_posts->publish > 0 && $posts_with_comments > 0 ) ? number_format( ( $posts_with_comments / $count_posts->publish ) * 100, 0, '.', '' ) : 0,
				'theme_version'   => $plugin_data['Version'],
				'theme_name'      => $theme_name,
				'site_name'       => 'null',
				'plugins'         => count( get_option( 'active_plugins' ) ),
				'plugin'          => urlencode( $plugin_name ),
				'wpversion'       => get_bloginfo( 'version' ),
			);

			foreach ( $data as $k => $v ) {
				$url .= $k . '/' . $v . '/';
			}
			wp_remote_get( $url );
			set_transient( 'presstrends_cache_data', $data, 60 * 60 * 24 );
		}
	}

// PressTrends WordPress Action
add_action('admin_init', 'presstrends_Maillogin_plugin');
		
		
?>
