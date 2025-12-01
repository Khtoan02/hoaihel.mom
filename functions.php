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
 * Tính toán các chỉ số tổng quan từ dữ liệu khảo sát.
 *
 * @param array $data
 * @return array
 */
function hoaihel_mom_survey_metrics(array $data) {
    $symptom_total = 6;
    $symptom_count = isset($data['symptoms']) && is_array($data['symptoms']) ? count($data['symptoms']) : 0;
    $observation   = $symptom_total ? min(100, round(($symptom_count / $symptom_total) * 100)) : 0;

    $doctor_map = [
        __('Rất cần thiết', 'hoaihel-mom')        => 95,
        __('Cần thiết một phần', 'hoaihel-mom')   => 70,
        __('Phân vân', 'hoaihel-mom')             => 50,
        __('Chưa thấy cần thiết', 'hoaihel-mom')  => 30,
    ];
    $doctor_score = $doctor_map[$data['doctor_role'] ?? ''] ?? 50;

    $dairy_map = [
        __('Sẵn sàng', 'hoaihel-mom')        => 90,
        __('E ngại', 'hoaihel-mom')          => 60,
        __('Cần tư vấn', 'hoaihel-mom')      => 45,
    ];
    $dairy_score = $dairy_map[$data['dairy_free'] ?? ''] ?? 50;

    $overall = round(($observation + $doctor_score + $dairy_score) / 3);

    return [
        'observation'   => $observation,
        'doctor'        => $doctor_score,
        'compliance'    => $dairy_score,
        'overall'       => $overall,
        'symptom_count' => $symptom_count,
    ];
}

/**
 * Định nghĩa câu hỏi & giá trị lựa chọn.
 *
 * @return array
 */
function hoaihel_mom_survey_question_config() {
    return [
        'symptoms' => [
            'label'   => __('Câu 1. Triệu chứng ghi nhận', 'hoaihel-mom'),
            'type'    => 'multi',
            'note'    => __('Có thể chọn nhiều đáp án – % dựa trên tổng lượt gửi', 'hoaihel-mom'),
            'options' => [
                'Thoái triển phát triển'             => __('Thoái triển phát triển (Mất dần kỹ năng)', 'hoaihel-mom'),
                'Động kinh hoặc co giật'             => __('Động kinh hoặc co giật', 'hoaihel-mom'),
                'Giảm trương lực cơ/Mất điều hòa'    => __('Giảm trương lực cơ / Mất điều hòa', 'hoaihel-mom'),
                'Cáu kỉnh, rối loạn giấc ngủ'        => __('Cáu kỉnh, rối loạn giấc ngủ', 'hoaihel-mom'),
                'Tật đầu nhỏ mắc phải'               => __('Tật đầu nhỏ mắc phải', 'hoaihel-mom'),
                'Không rõ ràng'                      => __('Không thấy các dấu hiệu rõ ràng', 'hoaihel-mom'),
            ],
        ],
        'diagnosis_history' => [
            'label'   => __('Câu 2. Lịch sử thăm khám chuyên sâu', 'hoaihel-mom'),
            'type'    => 'single',
            'options' => [
                'Chưa từng'           => __('Chưa từng thăm khám chuyên sâu', 'hoaihel-mom'),
                'Xét nghiệm máu'      => __('Đã xét nghiệm máu thông thường', 'hoaihel-mom'),
                'Xét nghiệm gen'      => __('Đã xét nghiệm gen (MTHFR, FOLR1...)', 'hoaihel-mom'),
                'Xét nghiệm FRAA'     => __('Đã xét nghiệm FRAA', 'hoaihel-mom'),
                'Chọc dò dịch não tủy'=> __('Đã chọc dò dịch não tủy (CSF)', 'hoaihel-mom'),
            ],
        ],
        'current_treatment' => [
            'label'   => __('Câu 3. Gia đình đang bổ sung gì?', 'hoaihel-mom'),
            'type'    => 'single',
            'options' => [
                'Không'                         => __('Không bổ sung', 'hoaihel-mom'),
                'Vitamin tổng hợp (Axit Folic)' => __('Vitamin tổng hợp (Axit Folic)', 'hoaihel-mom'),
                '5-MTHF'                        => __('5-MTHF (L-Methylfolate)', 'hoaihel-mom'),
                'Folinic Acid TPCN'             => __('Folinic Acid dạng TPCN', 'hoaihel-mom'),
                'Thuốc Leucovorin'              => __('Thuốc Leucovorin theo đơn', 'hoaihel-mom'),
            ],
        ],
        'knowledge_gap' => [
            'label'   => __('Câu 4. Nhận định về sự khác biệt Folinic Acid & Leucovorin', 'hoaihel-mom'),
            'type'    => 'single',
            'options' => [
                'Giống hệt nhau'                => __('Giống hệt nhau, chỉ khác tên', 'hoaihel-mom'),
                'Leucovorin liều cao tinh khiết'=> __('Leucovorin liều cao, cần kê đơn', 'hoaihel-mom'),
                'Chưa phân biệt được'           => __('Chưa phân biệt được hai loại', 'hoaihel-mom'),
                'TPCN an toàn hơn'              => __('Tin rằng TPCN an toàn hơn', 'hoaihel-mom'),
            ],
        ],
        'doctor_role' => [
            'label'   => __('Câu 5. Vai trò bác sĩ chuyên khoa', 'hoaihel-mom'),
            'type'    => 'single',
            'options' => [
                'Rất cần thiết'     => __('Rất cần thiết', 'hoaihel-mom'),
                'Cần thiết một phần'=> __('Cần thiết một phần', 'hoaihel-mom'),
                'Chưa cần thiết'    => __('Chưa thấy cần thiết', 'hoaihel-mom'),
                'Phân vân'          => __('Phân vân / chưa biết tìm ai', 'hoaihel-mom'),
            ],
        ],
        'challenges' => [
            'label'   => __('Câu 6. Khó khăn lớn nhất khi tìm thông tin', 'hoaihel-mom'),
            'type'    => 'single',
            'options' => [
                'Thông tin nhiễu loạn' => __('Thông tin nhiễu loạn', 'hoaihel-mom'),
                'Không biết nơi khám'  => __('Không biết nơi khám uy tín', 'hoaihel-mom'),
                'Lo sợ tác dụng phụ'   => __('Lo sợ tác dụng phụ thuốc', 'hoaihel-mom'),
                'Chi phí'              => __('Chi phí xét nghiệm/điều trị', 'hoaihel-mom'),
            ],
        ],
        'priority_goal' => [
            'label'   => __('Câu 7. Mục tiêu can thiệp ưu tiên', 'hoaihel-mom'),
            'type'    => 'single',
            'options' => [
                'Ngôn ngữ' => __('Ngôn ngữ & giao tiếp', 'hoaihel-mom'),
                'Động kinh'=> __('Giảm cơn co giật', 'hoaihel-mom'),
                'Hành vi'  => __('Hành vi / giấc ngủ', 'hoaihel-mom'),
                'Vận động' => __('Vận động & trương lực cơ', 'hoaihel-mom'),
            ],
        ],
        'dairy_free' => [
            'label'   => __('Câu 8. Mức độ sẵn sàng kiêng sữa động vật', 'hoaihel-mom'),
            'type'    => 'single',
            'options' => [
                'Sẵn sàng'   => __('Hoàn toàn sẵn sàng', 'hoaihel-mom'),
                'E ngại'     => __('Sẵn sàng dùng thuốc nhưng e ngại cắt sữa', 'hoaihel-mom'),
                'Cần tư vấn' => __('Cần tư vấn dinh dưỡng thêm', 'hoaihel-mom'),
            ],
        ],
        'receive_info' => [
            'label'   => __('Câu 10. Muốn nhận phản hồi cá nhân hoá?', 'hoaihel-mom'),
            'type'    => 'single',
            'options' => [
                'Có'    => __('Có, muốn nhận ý kiến bác sĩ', 'hoaihel-mom'),
                'Không' => __('Không, cảm ơn', 'hoaihel-mom'),
            ],
        ],
    ];
}

/**
 * Lấy toàn bộ dữ liệu khảo sát đã lưu.
 *
 * @return array[]
 */
function hoaihel_mom_get_all_survey_entries() {
    $posts = get_posts([
        'post_type'      => 'hhm_survey',
        'post_status'    => 'private',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    if (empty($posts)) {
        return [];
    }

    $entries = [];
    foreach ($posts as $post_id) {
        $data = get_post_meta($post_id, 'hhm_survey_data', true);
        if (is_array($data)) {
            $entries[] = $data;
        }
    }

    return $entries;
}

/**
 * Phân loại mức độ ưu tiên của một khảo sát.
 *
 * @param array $data
 * @return array
 */
function hoaihel_mom_segment_survey_record(array $data) {
    $symptom_weights = [
        'Thoái triển phát triển (Mất dần kỹ năng ngôn ngữ/vận động)' => 3,
        'Động kinh hoặc co giật (kể cả những cơn vắng ý thức nhỏ)'   => 3,
        'Giảm trương lực cơ (mềm nhão) hoặc mất điều hòa vận động'    => 1,
        'Cáu kỉnh nghiêm trọng, la hét, rối loạn giấc ngủ nặng'       => 1,
        'Kích thước vòng đầu phát triển chậm lại (Tật đầu nhỏ mắc phải)' => 2,
    ];
    $symptom_score = 0;
    if (!empty($data['symptoms']) && is_array($data['symptoms'])) {
        foreach ($data['symptoms'] as $symptom) {
            $symptom_score += $symptom_weights[$symptom] ?? 0;
        }
    }

    $current_treatment = $data['current_treatment'] ?? '';
    $treatment_score_map = [
        'Vitamin tổng hợp (Axit Folic)' => -5,
        'Không'                         => 0,
        '5-MTHF'                        => 1,
        'Folinic Acid TPCN'             => 1,
        'Thuốc Leucovorin'              => 5,
    ];
    $med_score = $treatment_score_map[$current_treatment] ?? 0;

    $dairy_free = $data['dairy_free'] ?? '';
    $dairy_map = [
        'Sẵn sàng'   => 3,
        'E ngại'     => 1,
        'Cần tư vấn' => 2,
    ];
    $readiness_score = $dairy_map[$dairy_free] ?? 0;
    if (($data['receive_info'] ?? '') === 'Có') {
        $readiness_score += 1;
    }

    $segment = 'WARM';
    $label   = __('Đang tìm hiểu', 'hoaihel-mom');
    $badge   = 'is-warm';
    $note    = __('Nuôi dưỡng thêm thông tin chuẩn xác.', 'hoaihel-mom');

    if ($current_treatment === 'Vitamin tổng hợp (Axit Folic)') {
        $segment = 'SOS';
        $label   = __('SOS: Đang dùng Axit Folic', 'hoaihel-mom');
        $badge   = 'is-sos';
        $note    = __('Cần liên hệ ngay để dừng Axit Folic và hướng dẫn lại.', 'hoaihel-mom');
    } elseif ($symptom_score >= 4) {
        $segment = 'HIGH_RISK';
        $label   = __('Nguy cơ cao', 'hoaihel-mom');
        $badge   = 'is-risk';
        $note    = __('Ưu tiên kết nối bác sĩ chuyên khoa thần kinh/y sinh.', 'hoaihel-mom');
    } elseif ($readiness_score >= 3) {
        $segment = 'HOT';
        $label   = __('Tiềm năng cao', 'hoaihel-mom');
        $badge   = 'is-hot';
        $note    = __('Sẵn sàng kiêng sữa & làm việc với bác sĩ ngay.', 'hoaihel-mom');
    } elseif ($current_treatment === 'Thuốc Leucovorin') {
        $segment = 'EDUCATED';
        $label   = __('Đã có kiến thức', 'hoaihel-mom');
        $badge   = 'is-educated';
        $note    = __('Đang theo đơn Leucovorin, cần theo dõi sát phản hồi.', 'hoaihel-mom');
    }

    return [
        'code'        => $segment,
        'label'       => $label,
        'badge_class' => $badge,
        'note'        => $note,
        'scores'      => [
            'symptom'   => $symptom_score,
            'medicine'  => $med_score,
            'readiness' => $readiness_score,
        ],
    ];
}

/**
 * Lấy danh sách khảo sát kèm thông tin phục vụ dashboard tuỳ biến.
 *
 * @return array[]
 */
function hoaihel_mom_get_survey_records() {
    $posts = get_posts([
        'post_type'      => 'hhm_survey',
        'post_status'    => 'private',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    if (empty($posts)) {
        return [];
    }

    $records = [];
    foreach ($posts as $post) {
        $data = get_post_meta($post->ID, 'hhm_survey_data', true);
        if (!is_array($data)) {
            continue;
        }
        $contact = isset($data['contact']) && is_array($data['contact']) ? $data['contact'] : [];
        $metrics = hoaihel_mom_survey_metrics($data);
        $segment = hoaihel_mom_segment_survey_record($data);

        $records[] = [
            'id'               => $post->ID,
            'name'             => !empty($contact['name']) ? sanitize_text_field($contact['name']) : __('Ẩn danh', 'hoaihel-mom'),
            'contact'          => [
                'phone' => !empty($contact['zalo']) ? sanitize_text_field($contact['zalo']) : '',
                'email' => !empty($contact['email']) ? sanitize_email($contact['email']) : '',
            ],
            'submitted_iso'    => get_post_time('c', true, $post),
            'submitted_human'  => get_post_time('d/m/Y H:i', true, $post),
            'symptoms'         => array_map('sanitize_text_field', array_filter((array) ($data['symptoms'] ?? []))),
            'current_treatment'=> sanitize_text_field($data['current_treatment'] ?? ''),
            'dairy_free'       => sanitize_text_field($data['dairy_free'] ?? ''),
            'priority_goal'    => sanitize_text_field($data['priority_goal'] ?? ''),
            'receive_info'     => sanitize_text_field($data['receive_info'] ?? ''),
            'question'         => trim(wp_strip_all_tags($data['question'] ?? '')),
            'metrics'          => $metrics,
            'segment'          => $segment,
            'scores'           => $segment['scores'],
            'edit_url'         => get_edit_post_link($post->ID, ''),
        ];
    }

    return $records;
}

/**
 * Tính phân bố lựa chọn cho tất cả câu hỏi.
 *
 * @return array
 */
function hoaihel_mom_calculate_overview_breakdown() {
    $config  = hoaihel_mom_survey_question_config();
    $entries = hoaihel_mom_get_all_survey_entries();
    $total   = count($entries);

    $result = [
        'total'      => $total,
        'questions'  => [],
        'question_9' => [
            'label'   => __('Câu 9. Số lượt để lại câu hỏi cho bác sĩ', 'hoaihel-mom'),
            'filled'  => 0,
            'blank'   => 0,
            'percent' => 0,
        ],
    ];

    foreach ($config as $field => $question) {
        $result['questions'][$field] = [
            'label'    => $question['label'],
            'type'     => $question['type'],
            'note'     => $question['note'] ?? '',
            'options'  => [],
            'no_answer'=> 0,
            'other'    => 0,
        ];
        foreach ($question['options'] as $value => $label) {
            $result['questions'][$field]['options'][$value] = [
                'value'   => $value,
                'label'   => $label,
                'count'   => 0,
                'percent' => 0,
            ];
        }
    }

    if (!$total) {
        return $result;
    }

    foreach ($entries as $entry) {
        foreach ($config as $field => $question) {
            $selected = $entry[$field] ?? null;
            $is_multi = $question['type'] === 'multi';

            if ($is_multi) {
                $choices = is_array($selected) ? array_unique(array_filter(array_map('trim', $selected))) : [];
                if (empty($choices)) {
                    $result['questions'][$field]['no_answer']++;
                    continue;
                }
                foreach ($choices as $choice) {
                    if (isset($result['questions'][$field]['options'][$choice])) {
                        $result['questions'][$field]['options'][$choice]['count']++;
                    } else {
                        $result['questions'][$field]['other']++;
                    }
                }
                continue;
            }

            $choice = is_string($selected) ? trim($selected) : '';
            if ($choice === '') {
                $result['questions'][$field]['no_answer']++;
            } elseif (isset($result['questions'][$field]['options'][$choice])) {
                $result['questions'][$field]['options'][$choice]['count']++;
            } else {
                $result['questions'][$field]['other']++;
            }
        }

        $question_text = isset($entry['question']) ? trim(wp_strip_all_tags($entry['question'])) : '';
        if ($question_text !== '') {
            $result['question_9']['filled']++;
        }
    }

    foreach ($result['questions'] as $field => &$question_stats) {
        foreach ($question_stats['options'] as $value => &$option_stats) {
            $option_stats['percent'] = round(($option_stats['count'] / $total) * 100, 1);
        }
        unset($option_stats);

        $question_stats['no_answer_percent'] = round(($question_stats['no_answer'] / $total) * 100, 1);
        $question_stats['other_percent']     = round(($question_stats['other'] / $total) * 100, 1);

        uasort($question_stats['options'], static function ($a, $b) {
            if ($a['count'] === $b['count']) {
                return strcmp($a['label'], $b['label']);
            }
            return $b['count'] <=> $a['count'];
        });

        $question_stats['options'] = array_values($question_stats['options']);
    }
    unset($question_stats);

    $result['question_9']['blank']   = max(0, $total - $result['question_9']['filled']);
    $result['question_9']['percent'] = round(($result['question_9']['filled'] / $total) * 100, 1);
    $result['question_9']['blank_percent'] = round(($result['question_9']['blank'] / $total) * 100, 1);

    return $result;
}

/**
 * Định dạng phần trăm gọn gàng.
 *
 * @param float $value
 * @return string
 */
function hoaihel_mom_format_percent($value) {
    $value = (float) $value;
    $rounded = round($value);
    if (abs($value - $rounded) < 0.1) {
        return number_format_i18n($rounded);
    }
    return number_format_i18n($value, 1);
}

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
    $metrics = hoaihel_mom_survey_metrics($data);
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
        <div class="hhm-survey-admin__metrics">
            <?php foreach (['observation' => __('Quan sát', 'hoaihel-mom'), 'doctor' => __('Làm việc với bác sĩ', 'hoaihel-mom'), 'compliance' => __('Sẵn sàng can thiệp', 'hoaihel-mom')] as $key => $label) : ?>
                <div class="hhm-survey-admin__metric">
                    <p><?php echo esc_html($label); ?></p>
                    <div class="hhm-survey-admin__metric-bar">
                        <span style="width: <?php echo esc_attr($metrics[$key]); ?>%;"></span>
                    </div>
                    <strong><?php echo esc_html($metrics[$key]); ?>%</strong>
                </div>
            <?php endforeach; ?>
            <div class="hhm-survey-admin__metric hhm-survey-admin__metric--overall">
                <p><?php esc_html_e('Tổng quan', 'hoaihel-mom'); ?></p>
                <div class="hhm-survey-admin__metric-bar">
                    <span style="width: <?php echo esc_attr($metrics['overall']); ?>%;"></span>
                </div>
                <strong><?php echo esc_html($metrics['overall']); ?>%</strong>
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
            .hhm-survey-admin__metrics {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 1rem;
                margin-bottom: 1.5rem;
            }
            .hhm-survey-admin__metric {
                padding: 1rem 1.2rem;
                border-radius: 12px;
                border: 1px solid #e0e7ff;
                background: #f8fafc;
            }
            .hhm-survey-admin__metric p {
                margin: 0 0 .4rem;
                font-weight: 600;
                color: #312e81;
            }
            .hhm-survey-admin__metric-bar {
                height: 6px;
                border-radius: 999px;
                background: #e5e7eb;
                overflow: hidden;
                margin-bottom: .35rem;
            }
            .hhm-survey-admin__metric-bar span {
                display: block;
                height: 100%;
                background: linear-gradient(90deg, #a855f7, #6366f1);
            }
            .hhm-survey-admin__metric strong {
                font-size: 18px;
                color: #111827;
                display: inline-block;
            }
            .hhm-survey-admin__metric--overall {
                border-color: #fcd34d;
                background: #fffbeb;
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
            @media (max-width: 768px) {
                .hhm-survey-admin__row {
                    grid-template-columns: 1fr;
                }
            }
        ';
        wp_add_inline_style('hoaihel-admin-survey', $css);
    }
}
add_action('admin_enqueue_scripts', 'hoaihel_mom_admin_assets');

/**
 * Hiển thị thống kê tổng quan ngay đầu trang danh sách khảo sát.
 */
function hoaihel_mom_render_survey_overview_block() {
    $screen = get_current_screen();
    if (!$screen || $screen->base !== 'edit' || $screen->post_type !== 'hhm_survey') {
        return;
    }

    $stats    = hoaihel_mom_calculate_overview_breakdown();
    $records  = hoaihel_mom_get_survey_records();
    $total    = $stats['total'];

    echo '<div class="hhm-survey-overview-page">';
    echo '<p class="description">' . esc_html__('Dashboard khảo sát CFD – trực quan hoá dữ liệu & phân loại ưu tiên.', 'hoaihel-mom') . '</p>';

    if (!$total) {
        echo '<div class="notice notice-warning"><p>' . esc_html__('Chưa có khảo sát nào, vui lòng chờ dữ liệu mới.', 'hoaihel-mom') . '</p></div>';
        echo '</div>';
        return;
    }

    $symptom_stats = $stats['questions']['symptoms']['options'] ?? [];
    $chart_payload = [
        'symptoms' => array_map(
            static fn($option) => [
                'label' => wp_strip_all_tags($option['label']),
                'value' => (int) $option['count'],
            ],
            array_slice($symptom_stats, 0, 6)
        ),
        'supplements' => array_map(
            static fn($option) => [
                'label' => wp_strip_all_tags($option['label']),
                'value' => (int) $option['count'],
            ],
            $stats['questions']['current_treatment']['options'] ?? []
        ),
        'doctor' => array_map(
            static fn($option) => [
                'label' => wp_strip_all_tags($option['label']),
                'value' => (int) $option['count'],
            ],
            $stats['questions']['doctor_role']['options'] ?? []
        ),
    ];

    $segment_counts = [
        'SOS'        => 0,
        'HIGH_RISK'  => 0,
        'HOT'        => 0,
    ];
    foreach ($records as $record) {
        $code = $record['segment']['code'];
        if (isset($segment_counts[$code])) {
            $segment_counts[$code]++;
        }
    }

    $cards = [
        [
            'title'   => __('Tổng khảo sát', 'hoaihel-mom'),
            'value'   => number_format_i18n($total),
            'caption' => __('Lượt gửi đã ghi nhận', 'hoaihel-mom'),
            'primary' => true,
        ],
        [
            'title'   => __('SOS - Dừng Axit Folic', 'hoaihel-mom'),
            'value'   => number_format_i18n($segment_counts['SOS']),
            'caption' => __('Cần gọi ngay để dừng sai thuốc', 'hoaihel-mom'),
        ],
        [
            'title'   => __('Ca nguy cơ cao', 'hoaihel-mom'),
            'value'   => number_format_i18n($segment_counts['HIGH_RISK']),
            'caption' => __('Có động kinh/thoái triển', 'hoaihel-mom'),
        ],
        [
            'title'   => __('Hot lead - Sẵn sàng', 'hoaihel-mom'),
            'value'   => number_format_i18n($segment_counts['HOT']),
            'caption' => __('Đã sẵn sàng kiêng sữa & bác sĩ', 'hoaihel-mom'),
        ],
    ];

    echo '<div class="hhm-survey-kpi-grid">';
    foreach ($cards as $card) {
        $classes = 'hhm-survey-card';
        if (!empty($card['primary'])) {
            $classes .= ' hhm-survey-card--highlight';
        }
        echo '<article class="' . esc_attr($classes) . '">';
        echo '<header><span>' . esc_html($card['title']) . '</span></header>';
        echo '<strong>' . esc_html($card['value']) . '</strong>';
        echo '<p>' . esc_html($card['caption']) . '</p>';
        echo '</article>';
    }
    echo '</div>';

    $question_percent = hoaihel_mom_format_percent($stats['question_9']['percent']) . '%';
    $question_count   = number_format_i18n($stats['question_9']['filled']);
    $receive_count    = 0;
    $receive_percent_value = 0;
    if (!empty($stats['questions']['receive_info']['options'])) {
        foreach ($stats['questions']['receive_info']['options'] as $option) {
            if ($option['value'] === 'Có') {
                $receive_count         = $option['count'];
                $receive_percent_value = $option['percent'];
                break;
            }
        }
    }
    $receive_percent = hoaihel_mom_format_percent($receive_percent_value) . '%';

    echo '<div class="hhm-survey-mini-stats">';
    echo '<div><span>' . esc_html__('Có câu hỏi cho bác sĩ', 'hoaihel-mom') . '</span><strong>' . esc_html($question_percent) . '</strong><p>' . sprintf(esc_html__('%s lượt cần bác sĩ phản hồi', 'hoaihel-mom'), esc_html($question_count)) . '</p></div>';
    echo '<div><span>' . esc_html__('Muốn nhận tư vấn riêng', 'hoaihel-mom') . '</span><strong>' . esc_html($receive_percent) . '</strong><p>' . sprintf(esc_html__('%s gia đình để lại liên hệ', 'hoaihel-mom'), esc_html(number_format_i18n($receive_count))) . '</p></div>';
    echo '</div>';

    echo '<div class="hhm-survey-visual-grid">';
    echo '<div class="hhm-survey-visual hhm-survey-visual--wide">';
    echo '<header><h3>' . esc_html__('Phân bố triệu chứng', 'hoaihel-mom') . '</h3><p>' . esc_html__('Top biểu hiện được tick nhiều nhất (Câu 1)', 'hoaihel-mom') . '</p></header>';
    echo '<canvas id="hhmSymptomChart" height="220"></canvas>';
    echo '</div>';
    echo '<div class="hhm-survey-visual">';
    echo '<header><h3>' . esc_html__('Gia đình đang bổ sung gì?', 'hoaihel-mom') . '</h3><p>' . esc_html__('Câu 3 – Tình trạng bổ sung Folate', 'hoaihel-mom') . '</p></header>';
    echo '<canvas id="hhmSupplementPie" height="220"></canvas>';
    echo '</div>';
    echo '<div class="hhm-survey-visual">';
    echo '<header><h3>' . esc_html__('Quan điểm về bác sĩ', 'hoaihel-mom') . '</h3><p>' . esc_html__('Câu 5 – Vai trò bác sĩ chuyên khoa', 'hoaihel-mom') . '</p></header>';
    echo '<canvas id="hhmDoctorChart" height="220"></canvas>';
    echo '</div>';
    echo '</div>';

    echo '<div class="hhm-survey-overview__grid">';
    foreach ($stats['questions'] as $field => $question) {
        echo '<section class="hhm-survey-overview__section">';
        echo '<header><h2>' . esc_html($question['label']) . '</h2>';
        if (!empty($question['note'])) {
            echo '<p class="hhm-survey-overview__note">' . esc_html($question['note']) . '</p>';
        } elseif ($question['type'] === 'multi') {
            echo '<p class="hhm-survey-overview__note">' . esc_html__('Có thể >100% vì phụ huynh chọn nhiều đáp án.', 'hoaihel-mom') . '</p>';
        }
        echo '</header>';
        echo '<ul class="hhm-progress-list">';
        foreach ($question['options'] as $index => $option) {
            $classes = $index === 0 ? 'is-leading' : '';
            $width   = min(100, $option['percent']);
            echo '<li class="' . esc_attr($classes) . '">';
            echo '<div class="hhm-progress-list__label">' . esc_html($option['label']) . '</div>';
            echo '<div class="hhm-progress-list__bar"><span style="width:' . esc_attr($width) . '%;"></span></div>';
            echo '<div class="hhm-progress-list__value">' . esc_html(hoaihel_mom_format_percent($option['percent'])) . '% · ' . esc_html(number_format_i18n($option['count'])) . '</div>';
            echo '</li>';
        }
        if ($question['other'] > 0) {
            echo '<li class="hhm-progress-list__meta"><span>' . esc_html__('Khác/Không khớp tùy chọn:', 'hoaihel-mom') . '</span><strong>' . esc_html(number_format_i18n($question['other'])) . '</strong><small>' . esc_html(hoaihel_mom_format_percent($question['other_percent'])) . '%</small></li>';
        }
        if ($question['no_answer'] > 0) {
            echo '<li class="hhm-progress-list__meta"><span>' . esc_html__('Chưa trả lời:', 'hoaihel-mom') . '</span><strong>' . esc_html(number_format_i18n($question['no_answer'])) . '</strong><small>' . esc_html(hoaihel_mom_format_percent($question['no_answer_percent'])) . '%</small></li>';
        }
        echo '</ul>';
        echo '</section>';
    }
    echo '</div>';

    $csv_url = wp_nonce_url(
        add_query_arg('hoaihel_export', 'all', admin_url('edit.php?post_type=hhm_survey')),
        'hoaihel_export_surveys'
    );

    echo '<div class="hhm-dashboard-table">';
    echo '<div class="hhm-dashboard-table__toolbar">';
    echo '<div><h3>' . esc_html__('Danh sách khảo sát', 'hoaihel-mom') . '</h3><p id="hhmSurveyListCount"></p></div>';
    echo '<div class="hhm-dashboard-table__actions">';
    echo '<div class="hhm-search-input"><span class="dashicons dashicons-search"></span><input type="search" id="hhmSurveySearch" placeholder="' . esc_attr__('Tìm tên phụ huynh, Zalo, email...', 'hoaihel-mom') . '" /></div>';
    echo '<div class="hhm-filter-group">';
    foreach (['ALL' => __('Tất cả', 'hoaihel-mom'), 'SOS' => 'SOS', 'HOT' => 'Hot', 'HIGH_RISK' => __('Nguy cơ', 'hoaihel-mom')] as $filter => $label) {
        echo '<button type="button" class="button hhm-filter-button' . ($filter === 'ALL' ? ' is-active' : '') . '" data-hhm-filter="' . esc_attr($filter) . '">' . esc_html($label) . '</button>';
    }
    echo '</div>';
    echo '<a class="button button-primary" href="' . esc_url($csv_url) . '">' . esc_html__('Xuất CSV tổng', 'hoaihel-mom') . '</a>';
    echo '<a class="button" href="' . esc_url(admin_url('post-new.php?post_type=hhm_survey')) . '">' . esc_html__('Thêm khảo sát thủ công', 'hoaihel-mom') . '</a>';
    echo '</div>';
    echo '</div>';
    echo '<div id="hhmSurveyList" class="hhm-survey-table"></div>';
    echo '</div>';

    echo '<script type="application/json" id="hhm-survey-chart-data">' . wp_json_encode($chart_payload) . '</script>';
    echo '<script type="application/json" id="hhm-survey-records">' . wp_json_encode($records, JSON_UNESCAPED_UNICODE) . '</script>';
    echo '</div>';
}
add_action('all_admin_notices', 'hoaihel_mom_render_survey_overview_block');

/**
 * Style riêng cho block thống kê trong trang danh sách.
 *
 * @param string $hook
 */
function hoaihel_mom_admin_overview_assets($hook) {
    if ($hook !== 'edit.php') {
        return;
    }
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'hhm_survey') {
        return;
    }

    if (!wp_style_is('hoaihel-admin-survey-overview', 'registered')) {
        wp_register_style('hoaihel-admin-survey-overview', false);
    }

    wp_enqueue_style('hoaihel-admin-survey-overview');
    wp_enqueue_script(
        'chartjs',
        'https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js',
        [],
        '4.4.6',
        true
    );

    $css = '
        body.post-type-hhm_survey .wrap > h1,
        body.post-type-hhm_survey .wrap > .page-title-action,
        body.post-type-hhm_survey .wrap > .subsubsub,
        body.post-type-hhm_survey .wrap > form#posts-filter,
        body.post-type-hhm_survey .wrap > .tablenav,
        body.post-type-hhm_survey .wrap > .search-box,
        body.post-type-hhm_survey .wrap > .clear {
            display: none !important;
        }
        .hhm-survey-overview-page {
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            border-radius: 24px;
            border: 1px solid #e5e7eb;
            background: linear-gradient(135deg, #f5f3ff, #ffffff);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.7), 0 12px 30px rgba(99,102,241,.12);
        }
        .hhm-survey-overview-page > p.description {
            margin: 0 0 1.25rem;
            color: #6b7280;
            font-size: 13px;
        }
        .hhm-survey-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .hhm-survey-card {
            border-radius: 18px;
            border: 1px solid rgba(124,58,237,.18);
            background: #fff;
            padding: 1rem 1.5rem;
            box-shadow: 0 15px 35px rgba(124,58,237,.08);
            display: flex;
            flex-direction: column;
            gap: .35rem;
            position: relative;
            overflow: hidden;
        }
        .hhm-survey-card:after {
            content: "";
            position: absolute;
            inset: 0;
            opacity: .08;
            background: linear-gradient(90deg, #7c3aed, transparent);
            pointer-events: none;
        }
        .hhm-survey-card header {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #6d28d9;
        }
        .hhm-survey-card strong {
            font-size: 34px;
            font-weight: 700;
            color: #111827;
            z-index: 1;
        }
        .hhm-survey-card p,
        .hhm-survey-card footer {
            margin: 0;
            font-size: 13px;
            color: #4b5563;
            z-index: 1;
        }
        .hhm-survey-card footer {
            font-size: 12px;
            color: #9ca3af;
        }
        .hhm-survey-card--highlight {
            background: radial-gradient(circle at top, #ede9fe, #ffffff);
            border-color: #c4b5fd;
        }
        .hhm-survey-mini-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .hhm-survey-mini-stats > div {
            border-radius: 16px;
            border: 1px dashed rgba(99,102,241,.4);
            padding: 1rem 1.2rem;
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(15,23,42,.05);
        }
        .hhm-survey-mini-stats span {
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: .08em;
            color: #6b7280;
        }
        .hhm-survey-mini-stats strong {
            display: block;
            font-size: 24px;
            margin: .35rem 0;
            color: #111827;
        }
        .hhm-survey-mini-stats p {
            margin: 0;
            font-size: 13px;
            color: #4b5563;
        }
        .hhm-survey-visual-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .hhm-survey-visual {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 1.25rem;
            box-shadow: 0 12px 28px rgba(15,23,42,.08);
        }
        .hhm-survey-visual--wide {
            grid-column: span 2;
        }
        @media (max-width: 1080px) {
            .hhm-survey-visual--wide {
                grid-column: auto;
            }
        }
        .hhm-survey-visual header h3 {
            margin: 0;
            font-size: 16px;
            color: #312e81;
        }
        .hhm-survey-visual header p {
            margin: .2rem 0 0;
            font-size: 12px;
            color: #6b7280;
        }
        .hhm-survey-overview__grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1rem;
        }
        .hhm-survey-overview__section {
            border: 1px solid #ede9fe;
            border-radius: 18px;
            padding: 1.25rem;
            background: #fdfbff;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
        }
        .hhm-survey-overview__section h2 {
            margin: 0;
            font-size: 18px;
            color: #312e81;
        }
        .hhm-survey-overview__note {
            margin: .35rem 0 0;
            font-size: 12px;
            color: #6b7280;
        }
        .hhm-progress-list {
            list-style: none;
            margin: 1rem 0 0;
            padding: 0;
        }
        .hhm-progress-list li {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: .65rem 0;
            border-top: 1px solid #f3f4f6;
        }
        .hhm-progress-list li:first-child {
            border-top: none;
            padding-top: 0;
        }
        .hhm-progress-list li.is-leading {
            background: linear-gradient(90deg, rgba(124,58,237,.08), transparent);
            border-radius: 10px;
            padding-left: .75rem;
            padding-right: .75rem;
        }
        .hhm-progress-list__label {
            flex: 0 0 240px;
            font-weight: 600;
            color: #1f2937;
        }
        @media (max-width: 960px) {
            .hhm-progress-list li {
                flex-direction: column;
                align-items: flex-start;
            }
            .hhm-progress-list__label {
                flex: 1 1 auto;
            }
        }
        .hhm-progress-list__bar {
            flex: 1;
            height: 8px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
        }
        .hhm-progress-list__bar span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, #c084fc, #6366f1);
        }
        .hhm-progress-list__value {
            flex: 0 0 120px;
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }
        .hhm-progress-list__meta {
            font-size: 13px;
            color: #4b5563;
            justify-content: flex-start;
        }
        .hhm-progress-list__meta strong {
            margin-left: .5rem;
            margin-right: .35rem;
        }
        .hhm-dashboard-table {
            border-radius: 24px;
            border: 1px solid #e5e7eb;
            background: #fff;
            padding: 1.5rem;
            margin-top: 1.5rem;
            box-shadow: 0 18px 45px rgba(15,23,42,.08);
        }
        .hhm-dashboard-table__toolbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1.25rem;
        }
        .hhm-dashboard-table__toolbar h3 {
            margin: 0;
            font-size: 18px;
            color: #111827;
        }
        .hhm-dashboard-table__toolbar p {
            margin: .25rem 0 0;
            color: #6b7280;
        }
        .hhm-dashboard-table__actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .75rem;
        }
        .hhm-search-input {
            position: relative;
        }
        .hhm-search-input input {
            padding: .4rem .9rem .4rem 2rem;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            min-width: 220px;
        }
        .hhm-search-input .dashicons-search {
            position: absolute;
            top: 50%;
            left: .7rem;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        .hhm-filter-group {
            display: flex;
            gap: .4rem;
        }
        .hhm-filter-button {
            border-radius: 999px !important;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
        }
        .hhm-filter-button.is-active {
            background: linear-gradient(90deg, #7c3aed, #5b21b6);
            border-color: transparent;
            color: #fff;
        }
        .hhm-survey-table {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }
        .hhm-survey-row {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 1rem 1.25rem;
            display: grid;
            grid-template-columns: 1.3fr 1fr 0.9fr 0.9fr 0.5fr;
            gap: 1rem;
            align-items: center;
            background: #fdfbff;
        }
        @media (max-width: 1280px) {
            .hhm-survey-row {
                grid-template-columns: 1fr;
            }
        }
        .hhm-survey-row__name {
            margin: 0;
            font-weight: 600;
            color: #111827;
        }
        .hhm-survey-row__contact,
        .hhm-survey-row__time {
            margin: .15rem 0;
            font-size: 12px;
            color: #6b7280;
        }
        .hhm-question-note {
            margin: .4rem 0 0;
            font-size: 12px;
            color: #7c3aed;
            font-weight: 600;
        }
        .hhm-chip {
            display: inline-flex;
            align-items: center;
            padding: .25rem .55rem;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid #e5e7eb;
            margin: 0 .3rem .3rem 0;
            color: #374151;
            background: #fff;
        }
        .hhm-chip.is-muted {
            background: #f3f4f6;
            border-color: #e5e7eb;
            color: #6b7280;
        }
        .hhm-scores {
            display: flex;
            gap: .6rem;
            justify-content: center;
        }
        .hhm-score-pill {
            width: 46px;
            height: 46px;
            border-radius: 999px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 12px;
            font-weight: 700;
            color: #111827;
            background: #eef2ff;
        }
        .hhm-score-pill span {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #6b7280;
        }
        .hhm-status {
            display: flex;
            flex-direction: column;
            gap: .35rem;
        }
        .hhm-status-badge {
            display: inline-flex;
            align-items: center;
            padding: .25rem .7rem;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        .hhm-status-badge.is-sos {
            background: #fee2e2;
            color: #b91c1c;
        }
        .hhm-status-badge.is-hot {
            background: #dcfce7;
            color: #047857;
        }
        .hhm-status-badge.is-risk {
            background: #ffedd5;
            color: #c2410c;
        }
        .hhm-status-badge.is-educated {
            background: #dbeafe;
            color: #1d4ed8;
        }
        .hhm-status-badge.is-warm {
            background: #e5e7eb;
            color: #374151;
        }
        .hhm-status p {
            margin: 0;
            font-size: 12px;
            color: #6b7280;
        }
        .hhm-survey-row__actions {
            display: flex;
            gap: .35rem;
            justify-content: flex-end;
        }
        .hhm-survey-row__actions a {
            padding: .4rem .6rem;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #fff;
            text-decoration: none;
        }
        .hhm-survey-row__actions a.button-primary {
            background: #eef2ff;
            border-color: #c7d2fe;
            color: #4338ca;
        }
        .hhm-empty-state {
            padding: 2rem;
            text-align: center;
            color: #6b7280;
            border: 1px dashed #d1d5db;
            border-radius: 16px;
        }
    ';
    wp_add_inline_style('hoaihel-admin-survey-overview', $css);
}
add_action('admin_enqueue_scripts', 'hoaihel_mom_admin_overview_assets');

/**
 * Render inline script tạo biểu đồ Chart.js cho block tổng quan.
 */
function hoaihel_mom_admin_overview_charts_script() {
    $screen = get_current_screen();
    if (!$screen || $screen->base !== 'edit' || $screen->post_type !== 'hhm_survey') {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') {
            return;
        }
        const dataEl = document.getElementById('hhm-survey-chart-data');
        if (!dataEl) {
            return;
        }
        let chartData;
        try {
            chartData = JSON.parse(dataEl.textContent);
        } catch (error) {
            console.error('Invalid survey chart data', error);
            return;
        }
        const palette = ['#7c3aed', '#6366f1', '#38bdf8', '#34d399', '#fbbf24', '#f472b6', '#f97316', '#0ea5e9'];

        const symptomCanvas = document.getElementById('hhmSymptomChart');
        if (symptomCanvas && chartData.symptoms) {
            new Chart(symptomCanvas, {
                type: 'bar',
                data: {
                    labels: chartData.symptoms.map(item => item.label),
                    datasets: [{
                        label: 'Lượt chọn',
                        data: chartData.symptoms.map(item => item.value),
                        backgroundColor: palette,
                        borderRadius: 8,
                        barThickness: 22,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: { beginAtZero: true, grid: { color: '#e5e7eb' } },
                        y: { grid: { display: false } },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.raw} lượt chọn`,
                            },
                        },
                    },
                },
            });
        }

        const supplementCanvas = document.getElementById('hhmSupplementPie');
        if (supplementCanvas && chartData.supplements) {
            new Chart(supplementCanvas, {
                type: 'doughnut',
                data: {
                    labels: chartData.supplements.map(item => item.label),
                    datasets: [{
                        data: chartData.supplements.map(item => item.value),
                        backgroundColor: palette,
                        borderWidth: 1,
                    }],
                },
                options: {
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.label}: ${ctx.raw} lượt`,
                            },
                        },
                    },
                },
            });
        }

        const doctorCanvas = document.getElementById('hhmDoctorChart');
        if (doctorCanvas && chartData.doctor) {
            new Chart(doctorCanvas, {
                type: 'bar',
                data: {
                    labels: chartData.doctor.map(item => item.label),
                    datasets: [{
                        label: 'Lượt chọn',
                        data: chartData.doctor.map(item => item.value),
                        backgroundColor: palette.slice(0, chartData.doctor.length),
                        borderRadius: 6,
                    }],
                },
                options: {
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: '#e5e7eb' } },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.raw} lượt`,
                            },
                        },
                    },
                },
            });
        }

        const recordsEl = document.getElementById('hhm-survey-records');
        const listEl = document.getElementById('hhmSurveyList');
        const countEl = document.getElementById('hhmSurveyListCount');
        const searchEl = document.getElementById('hhmSurveySearch');
        const filterButtons = document.querySelectorAll('[data-hhm-filter]');
        let records = [];
        if (recordsEl && listEl) {
            try {
                records = JSON.parse(recordsEl.textContent) || [];
            } catch (error) {
                console.error('Invalid survey records data', error);
            }
        }
        if (!listEl) {
            return;
        }

        let keyword = '';
        let activeFilter = 'ALL';

        const renderScore = (label, value) => {
            const display = typeof value === 'number' ? value : 0;
            return `<div class="hhm-score-pill">${display}<span>${label}</span></div>`;
        };

        const renderRow = (record) => {
            const symptomHtml = record.symptoms.length
                ? record.symptoms.map(sym => `<span class="hhm-chip">${sym}</span>`).join('')
                : '<span class="hhm-chip is-muted">' + (record.current_treatment || 'Chưa ghi nhận') + '</span>';
            const contactParts = [];
            if (record.contact.phone) {
                contactParts.push(record.contact.phone);
            }
            if (record.contact.email) {
                contactParts.push(record.contact.email);
            }
            const contactText = contactParts.length ? contactParts.join(' · ') : 'Chưa cung cấp liên hệ';
            const questionNote = record.question ? `<p class="hhm-question-note">Hỏi bác sĩ: ${record.question}</p>` : '';
            return `
                <div class="hhm-survey-row">
                    <div>
                        <p class="hhm-survey-row__name">${record.name}</p>
                        <p class="hhm-survey-row__contact">${contactText}</p>
                        <p class="hhm-survey-row__time">${record.submitted_human}</p>
                        ${questionNote}
                    </div>
                    <div>${symptomHtml}</div>
                    <div class="hhm-scores">
                        ${renderScore('S', record.scores.symptom)}
                        ${renderScore('M', record.scores.medicine)}
                        ${renderScore('R', record.scores.readiness)}
                    </div>
                    <div class="hhm-status">
                        <span class="hhm-status-badge ${record.segment.badge_class}">${record.segment.label}</span>
                        <p>${record.segment.note}</p>
                    </div>
                    <div class="hhm-survey-row__actions">
                        <a class="button button-primary" href="${record.edit_url}">Xem</a>
                    </div>
                </div>
            `;
        };

        const applyRender = () => {
            const normalized = keyword.trim().toLowerCase();
            const filtered = records.filter((record) => {
                const segmentMatch = activeFilter === 'ALL' ? true : record.segment.code === activeFilter;
                const haystack = [
                    record.name || '',
                    record.contact.phone || '',
                    record.contact.email || '',
                    record.question || '',
                ].join(' ').toLowerCase();
                const keywordMatch = normalized ? haystack.includes(normalized) : true;
                return segmentMatch && keywordMatch;
            });
            if (countEl) {
                countEl.textContent = filtered.length
                    ? `Hiển thị ${filtered.length}/${records.length} hồ sơ`
                    : 'Không có hồ sơ phù hợp.';
            }
            listEl.innerHTML = filtered.length
                ? filtered.map(renderRow).join('')
                : '<p class="hhm-empty-state">Chưa có dữ liệu phù hợp với bộ lọc hiện tại.</p>';
        };

        if (searchEl) {
            searchEl.addEventListener('input', (event) => {
                keyword = event.target.value;
                applyRender();
            });
        }

        filterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                activeFilter = button.dataset.hhmFilter || 'ALL';
                filterButtons.forEach(btn => btn.classList.remove('is-active'));
                button.classList.add('is-active');
                applyRender();
            });
        });

        applyRender();
    });
    </script>
    <?php
}
add_action('admin_print_footer_scripts', 'hoaihel_mom_admin_overview_charts_script');

/**
 * Cột danh sách cho CPT khảo sát.
 */
function hoaihel_mom_survey_columns($columns) {
    $new_columns = [];
    foreach ($columns as $key => $label) {
        $new_columns[$key] = $label;
        if ($key === 'title') {
            $new_columns['overview']   = __('Chỉ số tổng quan', 'hoaihel-mom');
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
    $metrics = hoaihel_mom_survey_metrics($data);
    switch ($column) {
        case 'overview':
            echo '<div class="hhm-survey-list-meter"><span>' . esc_html__('Tổng quan', 'hoaihel-mom') . ': ' . esc_html($metrics['overall']) . '%</span>';
            echo '<div class="hhm-survey-list-meter__bar"><span style="width:' . esc_attr($metrics['overall']) . '%"></span></div>';
            echo '<small>' . sprintf(
                /* translators: 1: observation, 2: doctor, 3: compliance */
                esc_html__('Quan sát %1$s%% · Bác sĩ %2$s%% · Sẵn sàng %3$s%%', 'hoaihel-mom'),
                esc_html($metrics['observation']),
                esc_html($metrics['doctor']),
                esc_html($metrics['compliance'])
            ) . '</small></div>';
            break;
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
 * Style list view & nút xuất CSV.
 */
function hoaihel_mom_admin_list_styles($hook) {
    if ($hook !== 'edit.php') {
        return;
    }
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'hhm_survey') {
        $css = '
            .column-overview { width: 28%; }
            .hhm-survey-list-meter {
                font-size: 13px;
                font-weight: 600;
                color: #111827;
            }
            .hhm-survey-list-meter__bar {
                height: 6px;
                border-radius: 999px;
                background: #e5e7eb;
                margin: 6px 0;
                overflow: hidden;
            }
            .hhm-survey-list-meter__bar span {
                display: block;
                height: 100%;
                background: linear-gradient(90deg, #34d399, #059669);
            }
            .hhm-survey-list-meter small {
                font-size: 11px;
                text-transform: uppercase;
                color: #6b7280;
            }
        ';
        wp_add_inline_style('wp-admin', $css);
    }
}
add_action('admin_enqueue_scripts', 'hoaihel_mom_admin_list_styles');

/**
 * Hiển thị nút tải CSV ở phần tablenav.
 */
function hoaihel_mom_survey_export_button($which) {
    $screen = get_current_screen();
    if ($screen->post_type !== 'hhm_survey' || $which !== 'top') {
        return;
    }
    $url = wp_nonce_url(
        add_query_arg('hoaihel_export', 'all', admin_url('edit.php?post_type=hhm_survey')),
        'hoaihel_export_surveys'
    );
    echo '<a class="button button-primary" style="margin-left:6px;" href="' . esc_url($url) . '">' . esc_html__('Tải CSV tổng', 'hoaihel-mom') . '</a>';
}
add_action('manage_posts_extra_tablenav', 'hoaihel_mom_survey_export_button', 10, 1);

/**
 * Export handler.
 */
function hoaihel_mom_survey_export_handler() {
    if (empty($_GET['hoaihel_export'])) {
        return;
    }
    if (!current_user_can('edit_posts')) {
        wp_die(__('Bạn không có quyền xuất dữ liệu.', 'hoaihel-mom'));
    }
    check_admin_referer('hoaihel_export_surveys');

    $type = sanitize_key($_GET['hoaihel_export']);
    $posts = [];
    if ('single' === $type && !empty($_GET['survey_id'])) {
        $id = absint($_GET['survey_id']);
        $post = get_post($id);
        if ($post && $post->post_type === 'hhm_survey') {
            $posts = [$post];
        }
    } else {
        $posts = get_posts([
            'post_type'      => 'hhm_survey',
            'post_status'    => 'private',
            'posts_per_page' => -1,
        ]);
    }

    if (empty($posts)) {
        wp_die(__('Không có dữ liệu để xuất.', 'hoaihel-mom'));
    }

    $filename = 'hoaihel-surveys-' . gmdate('Ymd-His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    $output = fopen('php://output', 'w');

    fputcsv($output, [
        'ID',
        'Ngày gửi',
        'Phụ huynh',
        'Liên hệ',
        'Nhận tin',
        'Triệu chứng',
        'Lịch sử khám',
        'Đang bổ sung',
        'Vai trò bác sĩ',
        'Khó khăn',
        'Mục tiêu',
        'Câu hỏi',
        'Điểm Quan sát (%)',
        'Điểm Bác sĩ (%)',
        'Điểm Sẵn sàng (%)',
        'Điểm Tổng quan (%)',
    ]);

    foreach ($posts as $post) {
        $data    = get_post_meta($post->ID, 'hhm_survey_data', true);
        $metrics = is_array($data) ? hoaihel_mom_survey_metrics($data) : [];
        $contact = $data['contact'] ?? [];
        fputcsv($output, [
            $post->ID,
            get_post_time('Y-m-d H:i', true, $post),
            $contact['name'] ?? '',
            trim(($contact['zalo'] ?? '') . ' ' . ($contact['email'] ?? '')),
            $data['receive_info'] ?? '',
            isset($data['symptoms']) ? implode(' | ', (array) $data['symptoms']) : '',
            $data['diagnosis_history'] ?? '',
            $data['current_treatment'] ?? '',
            $data['doctor_role'] ?? '',
            $data['challenges'] ?? '',
            $data['priority_goal'] ?? '',
            $data['question'] ?? '',
            $metrics['observation'] ?? '',
            $metrics['doctor'] ?? '',
            $metrics['compliance'] ?? '',
            $metrics['overall'] ?? '',
        ]);
    }
    fclose($output);
    exit;
}
add_action('admin_init', 'hoaihel_mom_survey_export_handler');

/**
 * Row action cho xuất CSV từng khảo sát.
 */
function hoaihel_mom_survey_row_actions($actions, $post) {
    if ($post->post_type !== 'hhm_survey') {
        return $actions;
    }
    $url = wp_nonce_url(
        add_query_arg(
            [
                'hoaihel_export' => 'single',
                'survey_id'      => $post->ID,
            ],
            admin_url('edit.php?post_type=hhm_survey')
        ),
        'hoaihel_export_surveys'
    );
    $actions['export'] = '<a href="' . esc_url($url) . '">' . esc_html__('Tải CSV', 'hoaihel-mom') . '</a>';
    return $actions;
}
add_filter('post_row_actions', 'hoaihel_mom_survey_row_actions', 10, 2);

/**
 * Đảm bảo wp_body_open tồn tại (WP < 5.2).
 */
if (!function_exists('wp_body_open')) {
    function wp_body_open() {
        do_action('wp_body_open');
    }
}

