<?php
/**
 * Sekcja wideo demonstracyjne
 */
$video_url   = get_theme_mod( 'sjp_video_url', '' );
$video_title = get_theme_mod( 'sjp_video_title', __( 'Zobacz jak to działa', 'flavor-flavor-flavor' ) );
$video_desc  = get_theme_mod( 'sjp_video_description', '' );

if ( ! $video_url ) return;

$is_youtube = ( strpos( $video_url, 'youtube.com' ) !== false || strpos( $video_url, 'youtu.be' ) !== false );
$is_vimeo   = strpos( $video_url, 'vimeo.com' ) !== false;

if ( $is_youtube ) {
    preg_match( '/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video_url, $matches );
    $video_id = $matches[1] ?? '';
    $embed_url = 'https://www.youtube-nocookie.com/embed/' . $video_id . '?rel=0&modestbranding=1';
} elseif ( $is_vimeo ) {
    preg_match( '/vimeo\.com\/(\d+)/', $video_url, $matches );
    $video_id = $matches[1] ?? '';
    $embed_url = 'https://player.vimeo.com/video/' . $video_id;
} else {
    $embed_url = '';
}
?>

<section class="sjp-section sjp-video" id="sjp-video">
    <div class="sjp-container">
        <?php if ( $video_title ) : ?>
            <h2 class="sjp-section__title"><?php echo esc_html( $video_title ); ?></h2>
        <?php endif; ?>
        <?php if ( $video_desc ) : ?>
            <p class="sjp-section__desc"><?php echo esc_html( $video_desc ); ?></p>
        <?php endif; ?>

        <div class="sjp-video__wrapper">
            <?php if ( $embed_url ) : ?>
                <div class="sjp-video__embed">
                    <iframe src="<?php echo esc_url( $embed_url ); ?>"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            loading="lazy"
                            title="<?php echo esc_attr( $video_title ); ?>">
                    </iframe>
                </div>
            <?php else : ?>
                <video class="sjp-video__player"
                       src="<?php echo esc_url( $video_url ); ?>"
                       controls
                       playsinline
                       preload="metadata"
                       muted>
                </video>
            <?php endif; ?>
        </div>
    </div>
</section>
