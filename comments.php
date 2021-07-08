<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains comments and the comment form.
 * Based on WordPress' Twenty Fourteen template
 *
 * @since 1.0.0
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() ) {
  return;
}
?>

<?php if ( have_comments() ) : ?>

  <h2>
    <?php
    printf( _n( 'One comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', get_comments_number(), 'stanford_text_domain' ),
      number_format_i18n( get_comments_number() ), get_the_title() );
    ?>
  </h2>

  <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
    <nav id="comment-nav-above" class="navigation comment-navigation" aria-label="comment">
      <div class="nav-previous" rel="prev"><?php previous_comments_link( __( '<i class="fa fa-chevron-circle-left"></i> Older Comments', 'stanford_text_domain' ) ); ?></div>
      <div class="nav-next" rel="next"><?php next_comments_link( __( 'Newer Comments <i class="fa fa-chevron-circle-right"></i>', 'stanford_text_domain' ) ); ?></div>
    </nav><!-- #comment-nav-above -->
  <?php endif; // Check for comment navigation. ?>

  <ol class="comment-list">
    <?php
    wp_list_comments( array(
      'style'      => 'ol'
    , 'short_ping' => true
    , 'avatar_size'=> 60
    ) );
    ?>
  </ol><!-- .comment-list -->

  <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
    <nav id="comment-nav-below" class="navigation comment-navigation" aria-label="comment">
      <div class="nav-previous" rel="prev"><?php previous_comments_link( __( '<i class="fa fa-chevron-circle-left"></i> Older Comments', 'stanford_text_domain' ) ); ?></div>
      <div class="nav-next" rel="next"><?php next_comments_link( __( 'Newer Comments <i class="fa fa-chevron-circle-right"></i>', 'stanford_text_domain' ) ); ?></div>
    </nav><!-- #comment-nav-below -->
  <?php endif; // Check for comment navigation. ?>

  <?php if ( ! comments_open() ) : ?>
    <p class="no-comments"><?php _e( 'Comments are closed.', 'stanford_text_domain' ); ?></p>
  <?php endif; ?>


<?php endif; // have_comments() ?>

<?php comment_form(array(
  'title_reply' => __('Leave a comment', 'stanford_text_domain')
, 'cancel_reply_link' => __('Cancel reply <i class="fa fa-times-circle"></i>')
)); ?>

