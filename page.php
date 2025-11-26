<?php
/**
 * Template trang tĩnh.
 *
 * @package hoaihel-mom
 */

get_header();
?>

<section class="hhm-section">
    <div class="hhm-container">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article class="hhm-card">
                <h1 class="hhm-section-title"><?php the_title(); ?></h1>
                <div class="hhm-entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; endif; ?>
    </div>
</section>

<?php
get_footer();

