<?php
/**
 * Hero section.
 *
 * @package hoaihel-mom
 */

$hero_badge     = hoaihel_mom_option('hero_badge', __('Checklist trục ruột - não', 'hoaihel-mom'));
$hero_title     = hoaihel_mom_option('hero_title', __('Lắng nghe bụng nhỏ, nuôi dưỡng trái tim lớn', 'hoaihel-mom'));
$hero_excerpt   = hoaihel_mom_option('hero_excerpt', __('Hoaihel.mom đồng hành cùng phụ huynh nuôi dạy trẻ đặc biệt thông qua checklist tiêu hoá, tài nguyên mindfulness và góc nhìn đa chuyên gia.', 'hoaihel-mom'));
$hero_cta_text  = hoaihel_mom_option('hero_cta_text', __('Bắt đầu checklist', 'hoaihel-mom'));
$hero_cta_link  = hoaihel_mom_option('hero_cta_link', esc_url(home_url('/checklist')));
$hero_second_text = hoaihel_mom_option('hero_secondary_text', __('Tải tài liệu miễn phí', 'hoaihel-mom'));
$hero_second_link = hoaihel_mom_option('hero_secondary_link', esc_url(home_url('/tai-lieu')));
?>

<section class="hhm-hero">
    <div class="hhm-container hhm-grid hhm-grid--2" style="align-items:center; gap:2.5rem;">
        <div class="hhm-card hhm-hero__card">
            <span class="hhm-pill hhm-badge-green"><?php echo esc_html($hero_badge); ?></span>
            <h1 class="hhm-section-title" style="margin-top:1.5rem;"><?php echo wp_kses_post($hero_title); ?></h1>
            <p style="font-size:1.1rem; color:rgba(31,35,41,.8); margin-bottom:1.5rem;"><?php echo wp_kses_post($hero_excerpt); ?></p>
            <div style="display:flex; gap:1rem; flex-wrap:wrap;">
                <a class="hhm-button hhm-button--primary" href="<?php echo esc_url($hero_cta_link); ?>">
                    <?php echo esc_html($hero_cta_text); ?>
                </a>
                <a class="hhm-button hhm-button--ghost" href="<?php echo esc_url($hero_second_link); ?>">
                    <?php echo esc_html($hero_second_text); ?>
                </a>
            </div>
            <ul style="margin-top:2rem; padding:0; list-style:none; display:grid; gap:.6rem;">
                <li>✔️ <?php esc_html_e('Checklist tiêu hoá 3 mức nguy cơ', 'hoaihel-mom'); ?></li>
                <li>✔️ <?php esc_html_e('Storytelling để bé dễ hợp tác', 'hoaihel-mom'); ?></li>
                <li>✔️ <?php esc_html_e('Gợi ý dinh dưỡng và mindfulness', 'hoaihel-mom'); ?></li>
            </ul>
        </div>
        <div>
            <div class="hhm-card" style="background:var(--hhm-muted); min-height:320px;">
                <p style="font-weight:600; text-transform:uppercase; letter-spacing:.2em; color:var(--hhm-secondary); margin-bottom:1rem;"><?php esc_html_e('Bộ công cụ Hoài Hẻl', 'hoaihel-mom'); ?></p>
                <h3 style="font-size:1.5rem; margin-bottom:1rem;"><?php esc_html_e('Từ kinh nghiệm cá nhân đến tài liệu khoa học', 'hoaihel-mom'); ?></h3>
                <p><?php esc_html_e('Chúng tôi chắt lọc bài học từ hơn 200 buổi đồng hành phụ huynh, kết hợp chuyên gia dinh dưỡng, tâm lý và trị liệu hành vi.', 'hoaihel-mom'); ?></p>
                <div style="display:flex; gap:1.5rem; margin-top:2rem;">
                    <div>
                        <strong style="font-size:2.4rem; color:var(--hhm-secondary); display:block;">200+</strong>
                        <span><?php esc_html_e('Gia đình đã tham gia', 'hoaihel-mom'); ?></span>
                    </div>
                    <div>
                        <strong style="font-size:2.4rem; color:var(--hhm-primary); display:block;">15</strong>
                        <span><?php esc_html_e('Template hành vi', 'hoaihel-mom'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

