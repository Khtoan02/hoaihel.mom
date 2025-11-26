<?php
/**
 * Theme bootstrap.
 *
 * @package hoaihel-mom
 */

if (!defined('HOAIHEL_MOM_VERSION')) {
    define('HOAIHEL_MOM_VERSION', '0.1.0');
}

/**
 * Setup theme supports & menus.
 */
function hoaihel_mom_setup() {
    load_theme_textdomain('hoaihel-mom', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('editor-styles');
    add_editor_style('style.css');

    register_nav_menus([
        'primary' => __('Menu chính', 'hoaihel-mom'),
        'footer'  => __('Menu chân trang', 'hoaihel-mom'),
    ]);
}
add_action('after_setup_theme', 'hoaihel_mom_setup');

/**
 * Enqueue assets.
 */
function hoaihel_mom_assets() {
    wp_enqueue_style(
        'hoaihel-mom-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        [],
        null
    );

    wp_enqueue_style('hoaihel-mom-style', get_stylesheet_uri(), ['hoaihel-mom-fonts'], HOAIHEL_MOM_VERSION);

    wp_enqueue_script(
        'hoaihel-mom-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        HOAIHEL_MOM_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'hoaihel_mom_assets');

/**
 * Widget area.
 */
function hoaihel_mom_widgets_init() {
    register_sidebar([
        'name'          => __('Chân trang', 'hoaihel-mom'),
        'id'            => 'footer',
        'description'   => __('Hiển thị dưới cột liên hệ.', 'hoaihel-mom'),
        'before_widget' => '<div class="hhm-footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="hhm-footer-widget__title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'hoaihel_mom_widgets_init');

/**
 * Customizer controls cho hero.
 */
function hoaihel_mom_customize_register(WP_Customize_Manager $wp_customize) {
    $wp_customize->add_section('hoaihel_mom_hero', [
        'title'       => __('Hero trang chủ', 'hoaihel-mom'),
        'description' => __('Chỉnh nội dung hero đầu trang.', 'hoaihel-mom'),
    ]);

    $fields = [
        'hero_badge'    => __('Badge nhỏ', 'hoaihel-mom'),
        'hero_title'    => __('Tiêu đề', 'hoaihel-mom'),
        'hero_excerpt'  => __('Mô tả ngắn', 'hoaihel-mom'),
        'hero_cta_text' => __('Nút chính', 'hoaihel-mom'),
        'hero_cta_link' => __('Liên kết nút chính', 'hoaihel-mom'),
        'hero_secondary_text' => __('Nút phụ', 'hoaihel-mom'),
        'hero_secondary_link' => __('Liên kết nút phụ', 'hoaihel-mom'),
    ];

    foreach ($fields as $key => $label) {
        $wp_customize->add_setting("hoaihel_mom_{$key}", [
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
        ]);

        $wp_customize->add_control("hoaihel_mom_{$key}", [
            'section' => 'hoaihel_mom_hero',
            'label'   => $label,
            'type'    => 'text',
        ]);
    }
}
add_action('customize_register', 'hoaihel_mom_customize_register');

/**
 * Helper lấy option với fallback.
 */
function hoaihel_mom_option($key, $default = '') {
    return get_theme_mod("hoaihel_mom_{$key}", $default);
}

/**
 * Đăng ký CPT lưu khảo sát.
 */
function hoaihel_mom_register_survey_cpt() {
    $labels = [
        'name'               => __('Khảo sát CFD', 'hoaihel-mom'),
        'singular_name'      => __('Khảo sát CFD', 'hoaihel-mom'),
        'menu_name'          => __('Khảo sát CFD', 'hoaihel-mom'),
        'add_new'            => __('Thêm mới', 'hoaihel-mom'),
        'add_new_item'       => __('Thêm khảo sát mới', 'hoaihel-mom'),
        'edit_item'          => __('Xem khảo sát', 'hoaihel-mom'),
        'view_item'          => __('Xem chi tiết', 'hoaihel-mom'),
        'search_items'       => __('Tìm khảo sát', 'hoaihel-mom'),
        'not_found'          => __('Chưa có khảo sát nào', 'hoaihel-mom'),
        'not_found_in_trash' => __('Không có khảo sát trong thùng rác', 'hoaihel-mom'),
    ];

    $args = [
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-forms',
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
        'supports'           => ['title'],
    ];

    register_post_type('hhm_survey', $args);
}
add_action('init', 'hoaihel_mom_register_survey_cpt');

/**
 * Xử lý lưu khảo sát từ frontend.
 */
function hoaihel_mom_handle_survey_submission() {
    if (!check_ajax_referer('hoaihel_survey_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => __('Phiên làm việc không hợp lệ.', 'hoaihel-mom')], 400);
    }

    $payload = isset($_POST['payload']) ? wp_unslash($_POST['payload']) : '';
    $data    = json_decode($payload, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        wp_send_json_error(['message' => __('Dữ liệu gửi lên không hợp lệ.', 'hoaihel-mom')], 400);
    }

    $title = sprintf(
        /* translators: %s: Thời gian gửi khảo sát */
        __('Khảo sát CFD - %s', 'hoaihel-mom'),
        current_time('d/m/Y H:i')
    );

    $content = wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    $post_id = wp_insert_post(
        [
            'post_type'   => 'hhm_survey',
            'post_status' => 'private',
            'post_title'  => $title,
            'post_content'=> $content,
        ],
        true
    );

    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => __('Không thể lưu khảo sát, vui lòng thử lại.', 'hoaihel-mom')], 500);
    }

    update_post_meta($post_id, 'hhm_survey_data', $data);
    if (!empty($data['contact']['name'])) {
        update_post_meta($post_id, 'hhm_parent_name', sanitize_text_field($data['contact']['name']));
    }
    if (!empty($data['contact']['zalo'])) {
        update_post_meta($post_id, 'hhm_parent_zalo', sanitize_text_field($data['contact']['zalo']));
    }
    if (!empty($data['contact']['email'])) {
        update_post_meta($post_id, 'hhm_parent_email', sanitize_email($data['contact']['email']));
    }

    wp_send_json_success(['message' => __('Đã lưu khảo sát thành công.', 'hoaihel-mom')]);
}
add_action('wp_ajax_hoaihel_submit_survey', 'hoaihel_mom_handle_survey_submission');
add_action('wp_ajax_nopriv_hoaihel_submit_survey', 'hoaihel_mom_handle_survey_submission');

/**
 * Tùy biến giao diện xem khảo sát trong Admin.
 */
function hoaihel_mom_survey_metabox() {
    add_meta_box(
        'hhm_survey_details',
        __('Chi tiết khảo sát', 'hoaihel-mom'),
        'hoaihel_mom_render_survey_metabox',
        'hhm_survey',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'hoaihel_mom_survey_metabox');

function hoaihel_mom_render_survey_metabox(WP_Post $post) {
    $data = get_post_meta($post->ID, 'hhm_survey_data', true);
    if (!is_array($data)) {
        echo '<p>' . esc_html__('Không tìm thấy dữ liệu khảo sát.', 'hoaihel-mom') . '</p>';
        return;
    }

    $contact = isset($data['contact']) && is_array($data['contact']) ? $data['contact'] : [];
    $symptom_html = __('Không chọn', 'hoaihel-mom');
    if (!empty($data['symptoms']) && is_array($data['symptoms'])) {
        $symptom_html = '<ul class="hhm-survey-chips">';
        foreach ($data['symptoms'] as $symptom) {
            $symptom_html .= '<li>' . esc_html($symptom) . '</li>';
        }
        $symptom_html .= '</ul>';
    }

    $sections = [
        __('Quan sát & đánh giá', 'hoaihel-mom') => [
            __('Triệu chứng ghi nhận', 'hoaihel-mom') => $symptom_html,
            __('Lịch sử thăm khám', 'hoaihel-mom') => esc_html($data['diagnosis_history'] ?? __('Không cung cấp', 'hoaihel-mom')),
        ],
        __('Nhận thức & điều trị', 'hoaihel-mom') => [
            __('Đang bổ sung', 'hoaihel-mom') => esc_html($data['current_treatment'] ?? __('Không cung cấp', 'hoaihel-mom')),
            __('Nhận định về Leucovorin', 'hoaihel-mom') => esc_html($data['knowledge_gap'] ?? __('Không cung cấp', 'hoaihel-mom')),
            __('Vai trò bác sĩ', 'hoaihel-mom') => esc_html($data['doctor_role'] ?? __('Không cung cấp', 'hoaihel-mom')),
        ],
        __('Khó khăn & mong muốn', 'hoaihel-mom') => [
            __('Khó khăn lớn nhất', 'hoaihel-mom') => esc_html($data['challenges'] ?? __('Không cung cấp', 'hoaihel-mom')),
            __('Mục tiêu ưu tiên', 'hoaihel-mom') => esc_html($data['priority_goal'] ?? __('Không cung cấp', 'hoaihel-mom')),
            __('Sẵn sàng kiêng sữa', 'hoaihel-mom') => esc_html($data['dairy_free'] ?? __('Không cung cấp', 'hoaihel-mom')),
            __('Câu hỏi gửi bác sĩ', 'hoaihel-mom') => !empty($data['question']) ? nl2br(esc_html($data['question'])) : __('(Không có)', 'hoaihel-mom'),
        ],
    ];

    $receive_info = esc_html($data['receive_info'] ?? __('Không rõ', 'hoaihel-mom'));
    $submitted_at = !empty($data['submittedAt']) ? esc_html(date_i18n('d/m/Y H:i', strtotime($data['submittedAt']))) : esc_html(get_the_date('', $post));
    ?>
    <div class="hhm-survey-admin">
        <div class="hhm-survey-admin__summary">
            <div>
                <p class="hhm-survey-admin__label"><?php esc_html_e('Ngày gửi', 'hoaihel-mom'); ?></p>
                <p class="hhm-survey-admin__value"><?php echo esc_html($submitted_at); ?></p>
            </div>
            <div>
                <p class="hhm-survey-admin__label"><?php esc_html_e('Nhận thông tin mới', 'hoaihel-mom'); ?></p>
                <p class="hhm-survey-admin__value"><?php echo esc_html($receive_info); ?></p>
            </div>
            <div>
                <p class="hhm-survey-admin__label"><?php esc_html_e('Phụ huynh', 'hoaihel-mom'); ?></p>
                <p class="hhm-survey-admin__value"><?php echo esc_html($contact['name'] ?? __('Chưa cung cấp', 'hoaihel-mom')); ?></p>
            </div>
            <div>
                <p class="hhm-survey-admin__label"><?php esc_html_e('Liên hệ', 'hoaihel-mom'); ?></p>
                <p class="hhm-survey-admin__value">
                    <?php
                    $contact_lines = [];
                    if (!empty($contact['zalo'])) {
                        $contact_lines[] = sprintf(__('Zalo/SĐT: %s', 'hoaihel-mom'), esc_html($contact['zalo']));
                    }
                    if (!empty($contact['email'])) {
                        $contact_lines[] = sprintf(__('Email: %s', 'hoaihel-mom'), esc_html($contact['email']));
                    }
                    echo !empty($contact_lines) ? implode('<br>', $contact_lines) : esc_html__('Chưa cung cấp', 'hoaihel-mom');
                    ?>
                </p>
            </div>
        </div>
        <?php foreach ($sections as $section_title => $items) : ?>
            <section class="hhm-survey-admin__section">
                <h3><?php echo esc_html($section_title); ?></h3>
                <dl>
                    <?php foreach ($items as $label => $value) : ?>
                        <div class="hhm-survey-admin__row">
                            <dt><?php echo esc_html($label); ?></dt>
                            <dd><?php echo wp_kses_post($value); ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </section>
        <?php endforeach; ?>
    </div>
    <?php
}

function hoaihel_mom_admin_assets($hook) {
    global $post_type;
    if (($hook === 'post.php' || $hook === 'post-new.php') && $post_type === 'hhm_survey') {
        if (!wp_style_is('hoaihel-admin-survey', 'registered')) {
            wp_register_style('hoaihel-admin-survey', false);
        }
        wp_enqueue_style('hoaihel-admin-survey');
        $css = '
            .hhm-survey-admin { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
            .hhm-survey-admin__summary {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 1rem;
                margin-bottom: 1.5rem;
            }
            .hhm-survey-admin__label {
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: .08em;
                color: #6b7280;
                margin: 0 0 .25rem;
            }
            .hhm-survey-admin__value {
                font-size: 15px;
                font-weight: 600;
                color: #111827;
                margin: 0;
            }
            .hhm-survey-admin__section {
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                padding: 20px 24px;
                margin-bottom: 1rem;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
            }
            .hhm-survey-admin__section h3 {
                margin: 0 0 1rem;
                font-size: 16px;
                font-weight: 700;
                color: #4c1d95;
            }
            .hhm-survey-admin__section dl {
                margin: 0;
            }
            .hhm-survey-admin__row {
                display: grid;
                grid-template-columns: 220px 1fr;
                gap: 1rem;
                padding: .6rem 0;
                border-top: 1px solid #f3f4f6;
            }
            .hhm-survey-admin__row:first-child {
                border-top: none;
                padding-top: 0;
            }
            .hhm-survey-admin__row dt {
                margin: 0;
                font-weight: 600;
                color: #1f2937;
            }
            .hhm-survey-admin__row dd {
                margin: 0;
                color: #374151;
                line-height: 1.5;
            }
            @media (max-width: 768px) {
                .hhm-survey-admin__row {
                    grid-template-columns: 1fr;
                }
            }
            .hhm-survey-chips {
                list-style: none;
                margin: 0;
                padding: 0;
                display: flex;
                flex-wrap: wrap;
                gap: .4rem;
            }
            .hhm-survey-chips li {
                background: #eef2ff;
                border: 1px solid #c7d2fe;
                color: #312e81;
                padding: .25rem .65rem;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 600;
            }
        ';
        wp_add_inline_style('hoaihel-admin-survey', $css);
    }
}
add_action('admin_enqueue_scripts', 'hoaihel_mom_admin_assets');

/**
 * Cột danh sách cho CPT khảo sát.
 */
function hoaihel_mom_survey_columns($columns) {
    $new_columns = [];
    foreach ($columns as $key => $label) {
        $new_columns[$key] = $label;
        if ($key === 'title') {
            $new_columns['parent_name'] = __('Phụ huynh', 'hoaihel-mom');
            $new_columns['contact']     = __('Liên hệ', 'hoaihel-mom');
            $new_columns['receive']     = __('Nhận tin', 'hoaihel-mom');
        }
    }
    return $new_columns;
}
add_filter('manage_hhm_survey_posts_columns', 'hoaihel_mom_survey_columns');

function hoaihel_mom_survey_column_content($column, $post_id) {
    $data = get_post_meta($post_id, 'hhm_survey_data', true);
    if (!is_array($data)) {
        echo '&mdash;';
        return;
    }
    $contact = $data['contact'] ?? [];
    switch ($column) {
        case 'parent_name':
            echo esc_html($contact['name'] ?? __('Chưa có', 'hoaihel-mom'));
            break;
        case 'contact':
            $parts = [];
            if (!empty($contact['zalo'])) {
                $parts[] = esc_html($contact['zalo']);
            }
            if (!empty($contact['email'])) {
                $parts[] = esc_html($contact['email']);
            }
            echo !empty($parts) ? implode('<br>', $parts) : '&mdash;';
            break;
        case 'receive':
            echo esc_html($data['receive_info'] ?? __('Không rõ', 'hoaihel-mom'));
            break;
    }
}
add_action('manage_hhm_survey_posts_custom_column', 'hoaihel_mom_survey_column_content', 10, 2);

/**
 * Đảm bảo wp_body_open tồn tại (WP < 5.2).
 */
if (!function_exists('wp_body_open')) {
    function wp_body_open() {
        do_action('wp_body_open');
    }
}

