<?php
/**
 * Template bài viết.
 *
 * @package hoaihel-mom
 */

get_header();
?>

<section class="hhm-section">
    <div class="hhm-container">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article class="hhm-card">
                <p class="hhm-pill hhm-badge-rose"><?php echo esc_html(get_the_date()); ?></p>
                <h1 class="hhm-section-title"><?php the_title(); ?></h1>
                <?php if (has_post_thumbnail()) : ?>
                    <figure style="margin:2rem 0;">
                        <?php the_post_thumbnail('large', ['style' => 'border-radius:var(--hhm-radius); width:100%; height:auto;']); ?>
                    </figure>
                <?php endif; ?>
                <div class="hhm-entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; endif; ?>
        <div style="margin-top:2rem;">
            <?php the_post_navigation(); ?>
        </div>
    </div>
</section>

<?php
get_footer();

