<?php
/**
 * Tool highlight.
 *
 * @package hoaihel-mom
 */

$tools = [
    [
        'label' => __('Checklist tiêu hoá', 'hoaihel-mom'),
        'desc'  => __('30 câu hỏi phân tầng 3 mức nguy cơ, kèm nhắc nhở đi khám.', 'hoaihel-mom'),
    ],
    [
        'label' => __('Thẻ hành vi', 'hoaihel-mom'),
        'desc'  => __('Workbook hướng dẫn cài thói quen, dùng được cả tại nhà và lớp.', 'hoaihel-mom'),
    ],
    [
        'label' => __('Template nhật ký', 'hoaihel-mom'),
        'desc'  => __('Bảng ghi nhận ăn - ngủ - cảm xúc để liên kết với chuyên gia.', 'hoaihel-mom'),
    ],
];
?>

<section class="hhm-section" style="background:#fff;">
    <div class="hhm-container hhm-grid hhm-grid--2" style="align-items:center;">
        <div>
            <span class="hhm-pill hhm-badge-green"><?php esc_html_e('Bộ công cụ', 'hoaihel-mom'); ?></span>
            <h2 class="hhm-section-title"><?php esc_html_e('Checklist & tài nguyên trọng tâm', 'hoaihel-mom'); ?></h2>
            <p class="hhm-section-desc"><?php esc_html_e('Mỗi công cụ đều có phiên bản online (miễn phí) và workbook tương tác dành riêng cho phụ huynh của Hoaihel.mom.', 'hoaihel-mom'); ?></p>
            <div class="hhm-grid hhm-grid--1" style="gap:1rem;">
                <?php foreach ($tools as $tool) : ?>
                    <div class="hhm-card" style="padding:1.5rem;">
                        <strong><?php echo esc_html($tool['label']); ?></strong>
                        <p style="margin:.4rem 0 0;"><?php echo esc_html($tool['desc']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <a class="hhm-button hhm-button--primary" style="margin-top:1.5rem;" href="<?php echo esc_url(home_url('/toolkit')); ?>">
                <?php esc_html_e('Xem toàn bộ bộ công cụ', 'hoaihel-mom'); ?>
            </a>
        </div>
        <div class="hhm-card" style="background:var(--hhm-muted);">
            <h3><?php esc_html_e('Livestream hướng dẫn dùng checklist', 'hoaihel-mom'); ?></h3>
            <p><?php esc_html_e('Mỗi tối thứ 5 trên cộng đồng Hoaihel.mom, chúng tôi giải đáp các case tiêu hoá và hành vi cụ thể.', 'hoaihel-mom'); ?></p>
            <ul style="margin:1rem 0 0; padding-left:1rem;">
                <li><?php esc_html_e('Demo cách đọc điểm với bác sĩ', 'hoaihel-mom'); ?></li>
                <li><?php esc_html_e('Chia sẻ thực đơn mẫu', 'hoaihel-mom'); ?></li>
                <li><?php esc_html_e('Hỏi đáp mở cho phụ huynh mới', 'hoaihel-mom'); ?></li>
            </ul>
        </div>
    </div>
</section>

