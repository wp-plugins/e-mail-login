<?php
/*
Plugin Name: E-Mail Login
Description: Log-in to your wordpress site using the e-mail address or classic username. Simply and efficient.
Version: 1.0
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
    if ( "Username" == $translated_text )
        $translated_text .= __( ' or Email');
    return $translated_text;
}
