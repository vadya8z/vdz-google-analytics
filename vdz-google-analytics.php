<?php
/*
Plugin Name: VDZ Google Tag Manager (GTM) & Google Analytics Plugin
Plugin URI:  http://online-services.org.ua
Description: Simple add Google Analytics or Google Tag Manager (GTM) code on your site
Version:     1.6.9
Author:      VadimZ
Author URI:  http://online-services.org.ua#vdz-google-analytics
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VDZ_GA_API', 'vdz_info_google_analytics' );

$plugin_data    = get_file_data( __FILE__, array( 'Version' => 'Version' ), false );
$plugin_version = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : time();
define( 'VDZ_GA_VERSION', $plugin_version );

require_once 'api.php';
require_once 'updated_plugin_admin_notices.php';

// Код активации плагина
register_activation_hook( __FILE__, 'vdz_ga_activate_plugin' );
function vdz_ga_activate_plugin() {
	global $wp_version;
	if ( version_compare( $wp_version, '5.7', '<' ) ) {
		// Деактивируем плагин
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( 'This plugin required WordPress version 5.7 or higher' );
	}

	// add_option('vdz_google_analytics_front_section', 'head');
	add_option( 'vdz_google_analytics_type', 'vdz_gtm' );

	do_action( VDZ_GA_API, 'on', plugin_basename( __FILE__ ) );
}

// Код деактивации плагина
register_deactivation_hook( __FILE__, function () {
	$plugin_name = preg_replace( '|\/(.*)|', '', plugin_basename( __FILE__ ));
	$response = wp_remote_get( "http://api.online-services.org.ua/off/{$plugin_name}" );
	if ( ! is_wp_error( $response ) && isset( $response['body'] ) && ( json_decode( $response['body'] ) !== null ) ) {
		//TODO Вывод сообщения для пользователя
	}
} );
//Сообщение при отключении плагина
add_action( 'admin_init', function (){
	if(is_admin()){
		$plugin_data = get_plugin_data(__FILE__);
		$plugin_slug    = isset( $plugin_data['slug'] ) ? $plugin_data['slug'] : sanitize_title( $plugin_data['Name'] );
		$plugin_id_attr = $plugin_slug;
		$plugin_name = isset($plugin_data['Name']) ? $plugin_data['Name'] : ' us';
		$plugin_dir_name = preg_replace( '|\/(.*)|', '', plugin_basename( __FILE__ ));
		$handle = 'admin_'.$plugin_dir_name;
		wp_register_script( $handle, '', null, false, true );
		wp_enqueue_script( $handle );
		$msg = '';
		if ( function_exists( 'get_locale' ) && in_array( get_locale(), array( 'uk', 'ru_RU' ), true ) ) {
			$msg .= "Спасибо, что были с нами! ({$plugin_name}) Хорошего дня!";
		}else{
			$msg .= "Thanks for your time with us! ({$plugin_name}) Have a nice day!";
		}
		if(substr_count( $_SERVER['REQUEST_URI'], 'plugins.php')){
			wp_add_inline_script( $handle, "if(document.getElementById('deactivate-".esc_attr($plugin_id_attr)."')){document.getElementById('deactivate-".esc_attr($plugin_id_attr)."').onclick=function (e){alert('".esc_attr( $msg )."');}}" );
		}
	}
} );




/*Добавляем новые поля для в настройках шаблона шаблона для верификации сайта*/
function vdz_ga_theme_customizer( $wp_customize ) {

	if ( ! class_exists( 'WP_Customize_Control' ) ) {
		exit;
	}

	// Добавляем секцию для идетнтификатора GA
	$wp_customize->add_section(
		'vdz_google_analytics_section',
		array(
			'title'    => __( 'VDZ Google Analytics' ),
			'priority' => 10,
		// 'description' => __( 'Google Analytics code on your site' ),
		)
	);
	// Добавляем настроеки
	$wp_customize->add_setting(
		'vdz_google_analytics_code',
		array(
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	// $wp_customize->add_setting( 'vdz_google_analytics_front_section', array(
	// 'type' => 'option',
	// 'sanitize_callback'    => 'sanitize_text_field',
	// ));
	$wp_customize->add_setting(
		'vdz_google_analytics_type',
		array(
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	// Google
	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'vdz_google_analytics_code',
			array(
				'label'       => __( 'Google Analytics' ),
				'section'     => 'vdz_google_analytics_section',
				'settings'    => 'vdz_google_analytics_code',
				'type'        => 'text',
				'description' => __( 'Add Google Analytics ID here:' ),
				'input_attrs' => array(
					'placeholder' => 'XX-XXXXXXXX-X', // для примера
				),
			)
		)
	);

	// GTM OR ANALYTICS.
	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'vdz_google_analytics_type',
			array(
				'label'       => __( 'Analytic TYPE' ),
				'section'     => 'vdz_google_analytics_section',
				'settings'    => 'vdz_google_analytics_type',
				'type'        => 'select',
				'description' => __( 'GTM or Analytics code' ),
				'choices'     => array(
					'vdz_gtm'      => 'Google Tag Manager',
					'vdz_analytic' => 'Google Analytics',
				),
			)
		)
	);

	// Добавляем ссылку на сайт
	$wp_customize->add_setting(
		'vdz_google_analytics_link',
		array(
			'type' => 'option',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'vdz_google_analytics_link',
			array(
				// 'label'    => __( 'Link' ),
							'section' => 'vdz_google_analytics_section',
				'settings'            => 'vdz_google_analytics_link',
				'type'                => 'hidden',
				'description'         => '<br/><a href="//online-services.org.ua#vdz-google-analytics" target="_blank">VadimZ</a>',
			)
		)
	);
}
add_action( 'customize_register', 'vdz_ga_theme_customizer', 1 );


// Добавляем допалнительную ссылку настроек на страницу всех плагинов
add_filter(
	'plugin_action_links_' . plugin_basename( __FILE__ ),
	function( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'customize.php?autofocus[section]=vdz_google_analytics_section' ) ) . '">' . esc_html__( 'Settings' ) . '</a>';
		array_unshift( $links, $settings_link );
		array_walk( $links, 'wp_kses_post' );
		return $links;
	}
);

// Добавляем мета теги в head
// $vdz_google_analytics_front_section = get_option('vdz_google_analytics_front_section') ? get_option('vdz_google_analytics_front_section') : 'head';
// if($vdz_google_analytics_front_section === 'head'){
// add_action('wp_head', 'vdz_ga_show_code', 1000);
// }else{
// add_action('wp_footer', 'vdz_ga_show_code', 1000);
// }

function vdz_ga_show_code() {
	$vdz_ga_code = get_option( 'vdz_google_analytics_code' );
	$vdz_ga_code = trim( $vdz_ga_code );
	$vdz_ga_type = get_option( 'vdz_google_analytics_type' ) ? get_option( 'vdz_google_analytics_type' ) : 'vdz_gtm';
	if ( ! empty( $vdz_ga_code ) ) {
		if ( 'vdz_gtm' === $vdz_ga_type ) {
			wp_enqueue_script( 'vdz_gtm', 'https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $vdz_ga_code ), null, VDZ_GA_VERSION, false );
			wp_add_inline_script( 'vdz_gtm', "window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '" . esc_attr( $vdz_ga_code ) . "');" );
		} else {
			// for register vdz_ga
			wp_register_script( 'vdz_ga', '', null, VDZ_GA_VERSION, false );
			wp_enqueue_script( 'vdz_ga' );
			wp_add_inline_script( 'vdz_ga', "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');ga('create', '" . esc_attr( $vdz_ga_code ) . "', 'auto');ga('send', 'pageview');" );
		}
	}
	// GA Events
	wp_enqueue_script( 'vdz_google_events', plugin_dir_url( __FILE__ ) . 'assets/js/vdz_google_events.js', null, VDZ_GA_VERSION, false, true );
}
add_action( 'wp_enqueue_scripts', 'vdz_ga_show_code' );

