<?php
/**
 * Loop mặc định.
 *
 * @package hoaihel-mom
 */

get_header();
?>

<section class="hhm-section">
    <div class="hhm-container hhm-grid hhm-grid--2">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article class="hhm-card hhm-latest-posts__item">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium_large', ['style' => 'border-radius:var(--hhm-radius);']); ?>
                        </a>
                    <?php endif; ?>
                    <p class="hhm-badge-rose hhm-pill" style="margin-top:1rem;"><?php echo esc_html(get_the_date()); ?></p>
                    <h2 style="margin:1rem 0 .5rem;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p><?php echo wp_trim_words(get_the_excerpt(), 24); ?></p>
                    <a class="hhm-button hhm-button--primary" style="margin-top:1rem;" href="<?php the_permalink(); ?>">
                        <?php esc_html_e('Đọc tiếp', 'hoaihel-mom'); ?>
                    </a>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <p><?php esc_html_e('Chưa có nội dung.', 'hoaihel-mom'); ?></p>
        <?php endif; ?>
    </div>
    <div class="hhm-container" style="text-align:center; margin-top:2rem;">
        <?php the_posts_pagination(); ?>
    </div>
</section>

<?php
get_footer();

