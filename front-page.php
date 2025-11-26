<?php
/**
 * Trang chủ tùy biến.
 *
 * Cho phép người quản trị gán bất kỳ Page Template nào làm trang chủ
 * thông qua Settings ▸ Reading. Nếu trang được chọn sử dụng một page
 * template cụ thể (ví dụ: Khảo sát CFD), chúng ta nạp trực tiếp template đó
 * thay vì ép dùng layout mặc định.
 *
 * @package hoaihel-mom
 */

$front_page_id = get_queried_object_id();
$assigned_template = $front_page_id ? get_page_template_slug($front_page_id) : '';

if ($assigned_template && 'front-page.php' !== $assigned_template) {
    $located = locate_template($assigned_template);
    if ($located) {
        include $located;
        return;
    }
}

get_header();

get_template_part('template-parts/sections/hero');
get_template_part('template-parts/sections/intro');
get_template_part('template-parts/sections/tools');
get_template_part('template-parts/sections/articles');
get_template_part('template-parts/sections/community');
get_template_part('template-parts/sections/cta');

get_footer();

