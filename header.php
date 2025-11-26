<?php
/**
 * Header template.
 *
 * @package hoaihel-mom
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#site-content"><?php esc_html_e('Bỏ qua nội dung', 'hoaihel-mom'); ?></a>

<header class="hhm-site-header">
    <div class="hhm-container" style="display:flex; align-items:center; justify-content:space-between; padding:1rem 0;">
        <div class="hhm-logo" style="display:flex; align-items:center; gap:.75rem;">
            <button class="hhm-menu-toggle" aria-expanded="false" aria-controls="primary-menu">
                <span class="screen-reader-text"><?php esc_html_e('Mở menu', 'hoaihel-mom'); ?></span>
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <path d="M4 7h16M4 12h16M4 17h16" stroke="#1f2329" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="hhm-brand" style="font-weight:700; font-size:1.1rem;">
                Hoaihel<span style="color:var(--hhm-primary);">.mom</span>
            </a>
        </div>
        <nav id="primary-menu" class="hhm-nav" aria-label="<?php esc_attr_e('Menu chính', 'hoaihel-mom'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'fallback_cb'    => '__return_false',
                'items_wrap'     => '<ul class="hhm-nav__list">%3$s</ul>',
            ]);
            ?>
            <a class="hhm-button hhm-button--primary" href="#hoaihel-contact">
                <?php esc_html_e('Đặt lịch tư vấn', 'hoaihel-mom'); ?>
            </a>
        </nav>
    </div>
</header>

<main id="site-content">

