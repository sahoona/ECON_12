<?php
/*
YARPP Template: List View
Description: A custom YARPP template that displays related posts in a list format with thumbnails, date, and reading time.
Author: Jules
*/
?>

<?php if (have_posts()): ?>
<div class="related-posts-container">
    <h3 class="related-posts-title">Related Posts</h3>
    <ol class="related-posts-list">
        <?php while (have_posts()) : the_post(); ?>
        <li class="related-posts-item">
            <a href="<?php the_permalink() ?>" rel="bookmark" class="related-posts-link">
                <div class="related-posts-thumbnail-container">
                    <?php if (has_post_thumbnail()):?>
                        <?php the_post_thumbnail('thumbnail'); ?>
                    <?php endif; ?>
                    <div class="related-posts-content">
                        <h4 class="related-posts-post-title"><?php the_title(); ?></h4>
                        <div class="related-posts-details">
                            <span class="related-posts-date"><?php echo get_the_date(); ?></span>
                            <span class="related-posts-reading-time"><?php echo gp_get_reading_time( get_the_ID() ); ?></span>
                            <span class="related-posts-categories"><?php the_category(', '); ?></span>
                        </div>
                        <div class="related-posts-tags">
                            <?php echo get_the_term_list( get_the_ID(), 'post_tag', '', ', ', '' ); ?>
                        </div>
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
