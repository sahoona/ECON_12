<?php
/*
YARPP Template: Final Custom Layout
Description: A completely rewritten custom YARPP template to display related posts in a clean, multi-line format.
Author: Jules
*/
?>

<?php if (have_posts()): ?>
<section class="related-posts-container">
    <h2 class="related-posts-title">Related Posts</h2>
    <ol class="related-posts-list">
        <?php while (have_posts()) : the_post(); ?>
        <li class="related-posts-item">
            <a href="<?php the_permalink() ?>" rel="bookmark" class="related-posts-link">
                <?php if (has_post_thumbnail()):?>
                <div class="related-posts-thumbnail-container">
                    <?php the_post_thumbnail('thumbnail'); ?>
                </div>
                <?php endif; ?>
                <div class="related-posts-content">
                    <h3 class="related-posts-post-title"><?php the_title(); ?></h3>
                    <div class="related-posts-meta">
                        <span class="related-posts-date"><?php echo get_the_date(); ?></span>
                        <span class="related-posts-separator">·</span>
                        <span class="related-posts-reading-time"><?php echo gp_get_reading_time( get_the_ID() ); ?></span>
                    </div>
                    <div class="related-posts-categories">
                        <?php
                        $categories = get_the_category();
                        if ( ! empty( $categories ) ) {
                            echo esc_html( $categories[0]->name );
                        }
                        ?>
                    </div>
                    <div class="related-posts-tags">
                        <?php the_tags('', ' ', ''); ?>
                    </div>
                </div>
            </a>
        </li>
        <?php endwhile; ?>
    </ol
</section>
<?php else: ?>
<!-- No related posts found -->
<?php endif; ?>
