<?php
/**
 * Footer template.
 *
 * @package hoaihel-mom
 */
?>
</main>

<footer class="hhm-site-footer" id="hoaihel-contact">
    <div class="hhm-container hhm-grid hhm-grid--3">
        <div>
            <h3 style="color:#fff; font-size:1.1rem; margin-bottom:1rem;">Hoaihel.mom</h3>
            <p>Không gian đồng hành phụ huynh nuôi dạy trẻ đặc biệt với góc nhìn trục ruột - não, checklist dễ hiểu và nội dung đáng tin cậy.</p>
            <div style="margin-top:1.2rem; display:flex; gap:.8rem;">
                <a href="https://facebook.com" aria-label="Facebook">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <path d="M15 3h3V0h-3c-2.76 0-5 2.24-5 5v3H7v3h3v10h3V11h3l1-3h-4V5c0-1.1.9-2 2-2Z" fill="#fff"/>
                    </svg>
                </a>
            </div>
        </div>
        <div>
            <h3 style="color:#fff; font-size:1.1rem; margin-bottom:1rem;"><?php esc_html_e('Điều hướng', 'hoaihel-mom'); ?></h3>
            <?php
            wp_nav_menu([
                'theme_location' => 'footer',
                'container'      => false,
                'fallback_cb'    => '__return_false',
                'items_wrap'     => '<ul style="list-style:none; padding:0; margin:0; display:grid; gap:.5rem;">%3$s</ul>',
            ]);
            ?>
        </div>
        <div>
            <h3 style="color:#fff; font-size:1.1rem; margin-bottom:1rem;"><?php esc_html_e('Góc thư phụ huynh', 'hoaihel-mom'); ?></h3>
            <form class="hhm-card" style="background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15);">
                <label for="newsletter-email" style="display:block; font-weight:600; margin-bottom:.4rem;"><?php esc_html_e('Nhận bản tin', 'hoaihel-mom'); ?></label>
                <input type="email" id="newsletter-email" placeholder="<?php esc_attr_e('Email của bạn', 'hoaihel-mom'); ?>" style="width:100%; padding:.85rem 1rem; border-radius:999px; border:none; margin-bottom:.8rem;">
                <button class="hhm-button hhm-button--primary" type="submit"><?php esc_html_e('Đăng ký', 'hoaihel-mom'); ?></button>
            </form>
            <?php if (is_active_sidebar('footer')) : ?>
                <div class="hhm-footer-widgets">
                    <?php dynamic_sidebar('footer'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="hhm-container" style="margin-top:2rem; border-top:1px solid rgba(255,255,255,.1); padding-top:1.5rem; font-size:.9rem; display:flex; flex-wrap:wrap; gap:1rem; justify-content:space-between;">
        <span>&copy; <?php echo esc_html(date('Y')); ?> Hoaihel.mom</span>
        <span><?php esc_html_e('Thiết kế bởi Hoài Hẻl', 'hoaihel-mom'); ?></span>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

