<?php
/**
 * Intro section.
 *
 * @package hoaihel-mom
 */

$pillars = [
    [
        'title' => __('Quan sát cơ thể', 'hoaihel-mom'),
        'desc'  => __('Theo dõi tiêu hoá, giấc ngủ và phản ứng giác quan của bé.', 'hoaihel-mom'),
    ],
    [
        'title' => __('Kể chuyện chữa lành', 'hoaihel-mom'),
        'desc'  => __('Storytelling được cá nhân hoá giúp bé hợp tác với chế độ ăn.', 'hoaihel-mom'),
    ],
    [
        'title' => __('Cộng đồng phụ huynh', 'hoaihel-mom'),
        'desc'  => __('Không gian an toàn để chia sẻ tiến trình và nhận lời khuyên thiết thực.', 'hoaihel-mom'),
    ],
];
?>

<section class="hhm-section">
    <div class="hhm-container">
        <span class="hhm-pill hhm-badge-rose"><?php esc_html_e('Triết lý Hoài Hẻl', 'hoaihel-mom'); ?></span>
        <h2 class="hhm-section-title"><?php esc_html_e('Khoa học đủ sâu, cảm xúc đủ gần', 'hoaihel-mom'); ?></h2>
        <p class="hhm-section-desc"><?php esc_html_e('Giao diện được thiết kế để phụ huynh nhanh chóng nắm bắt bức tranh toàn cảnh sức khoẻ đường ruột, đồng thời giữ lại sự dịu dàng cần thiết trong hành trình nuôi dạy.', 'hoaihel-mom'); ?></p>
        <div class="hhm-grid hhm-grid--3">
            <?php foreach ($pillars as $pillar) : ?>
                <article class="hhm-card">
                    <h3 style="margin-top:0;"><?php echo esc_html($pillar['title']); ?></h3>
                    <p><?php echo esc_html($pillar['desc']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

