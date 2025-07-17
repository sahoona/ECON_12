<?php
/*
YARPP Template: List View
Description: A custom YARPP template that displays related posts in a list format with thumbnails, date, and reading time.
Author: Jules
*/
?>

<?php if (have_posts()): ?>
<div class="yarpp-related-list">
    <h3 class="yarpp-related-title">관련 글</h3>
    <ol>
        <?php while (have_posts()) : the_post(); ?>
        <li>
            <a href="<?php the_permalink() ?>" rel="bookmark">
                <?php if (has_post_thumbnail()):?>
                <div class="yarpp-thumbnail-container">
                    <?php the_post_thumbnail('thumbnail'); ?>
                </div>
                <?php endif; ?>
                <div class="yarpp-post-content">
                    <span class="yarpp-post-title"><?php the_title(); ?></span>
                    <div class="yarpp-post-meta">
                        <span class="yarpp-post-date"><?php echo get_the_date(); ?></span>
                        <span class="yarpp-reading-time"><?php echo do_shortcode('[rt_reading_time]'); ?> min read</span>
                    </div>
                </div>
            </a>
        </li>
        <?php endwhile; ?>
    </ol>
</div>
<?php else: ?>
<!-- No related posts found -->
<?php endif; ?>
