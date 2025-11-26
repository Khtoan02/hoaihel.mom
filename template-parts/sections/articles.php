<?php
/**
 * Latest articles section.
 *
 * @package hoaihel-mom
 */

$articles = new WP_Query([
    'posts_per_page'      => 3,
    'ignore_sticky_posts' => 1,
]);
?>

<section class="hhm-section hhm-latest-posts">
    <div class="hhm-container">
        <span class="hhm-pill hhm-badge-rose"><?php esc_html_e('Nhật ký chữa lành', 'hoaihel-mom'); ?></span>
        <h2 class="hhm-section-title"><?php esc_html_e('Bài viết mới nhất', 'hoaihel-mom'); ?></h2>
        <div class="hhm-grid hhm-grid--3">
            <?php if ($articles->have_posts()) : ?>
                <?php while ($articles->have_posts()) : $articles->the_post(); ?>
                    <article class="hhm-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium_large', ['style' => 'border-radius:var(--hhm-radius);']); ?>
                            </a>
                        <?php endif; ?>
                        <p class="hhm-pill hhm-badge-green" style="margin-top:1rem;"><?php echo esc_html(get_the_date()); ?></p>
                        <h3 style="margin:1rem 0 .5rem;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p><?php echo wp_trim_words(get_the_excerpt(), 22); ?></p>
                        <a class="hhm-button hhm-button--primary" style="margin-top:1rem;" href="<?php the_permalink(); ?>">
                            <?php esc_html_e('Đọc tiếp', 'hoaihel-mom'); ?>
                        </a>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <p><?php esc_html_e('Đang cập nhật nội dung...', 'hoaihel-mom'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

