<?php
/**
 * Community/testimonial block.
 *
 * @package hoaihel-mom
 */

$stories = [
    [
        'name'    => __('Chị Thanh – mẹ bé Che', 'hoaihel-mom'),
        'quote'   => __('“Sau 4 tuần ghi lại checklist, con dễ ngủ hơn và giảm cáu gắt hẳn. Mình đọc được tín hiệu của con thay vì đoán mò.”', 'hoaihel-mom'),
    ],
    [
        'name'    => __('Anh Huy – bố bé Lạc', 'hoaihel-mom'),
        'quote'   => __('“Template nhật ký giúp mình nói chuyện với bác sĩ rõ ràng. Cả gia đình đỡ stress.”', 'hoaihel-mom'),
    ],
];
?>

<section class="hhm-section" style="background:var(--hhm-dark); color:#fff;">
    <div class="hhm-container">
        <span class="hhm-pill" style="background:rgba(255,255,255,.1);"><?php esc_html_e('Cộng đồng', 'hoaihel-mom'); ?></span>
        <h2 class="hhm-section-title"><?php esc_html_e('Giữ nhịp cùng nhau', 'hoaihel-mom'); ?></h2>
        <p class="hhm-section-desc" style="color:rgba(255,255,255,.8);"><?php esc_html_e('Group Facebook kín & bản tin hàng tuần giúp phụ huynh không còn cảm giác đi một mình.', 'hoaihel-mom'); ?></p>
        <div class="hhm-grid hhm-grid--2">
            <?php foreach ($stories as $story) : ?>
                <article class="hhm-card" style="background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12); color:#fff;">
                    <p style="font-style:italic;"><?php echo esc_html($story['quote']); ?></p>
                    <strong style="display:block; margin-top:1rem;"><?php echo esc_html($story['name']); ?></strong>
                </article>
            <?php endforeach; ?>
            <div class="hhm-card" style="background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12);">
                <h3><?php esc_html_e('Hoaihel Circle', 'hoaihel-mom'); ?></h3>
                <p><?php esc_html_e('Nơi cập nhật lịch workshop, livestream và tài liệu mới nhất.', 'hoaihel-mom'); ?></p>
                <a class="hhm-button hhm-button--primary" href="https://facebook.com">
                    <?php esc_html_e('Tham gia cộng đồng', 'hoaihel-mom'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

