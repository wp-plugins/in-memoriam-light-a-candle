<?php
 
get_header(); ?>
 
<?php while ( have_posts() ) : the_post(); ?>
 
        <?php if ( is_single() ) : ?>
 
                <div class="row" role="main">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        
        uuuuuuuuuuuuuuuuuuuuuuuuuuuuuu
                <header class="project-header large-12 columns">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                        <p><?php the_terms( $post->ID, 'project_type', '', '  <span class="color">|</span> ', ' ' ); ?></p>
                </header>
 
                <div class="large-6 columns">
                        <?php the_field( 'project_description' ); ?>
                        <table>
                                <tbody>
                                        <tr>
                                                <td>Client:</td>
                                                <td><?php the_field( 'project_client' ); ?></td>
                                        </tr>
                                        <tr>
                                                <td>Date:</td>
                                                <td><?php the_field( 'project_date' ); ?></td>
                                        </tr>
                                        <tr>
                                                <td>Skills:</td>
                                                <td>
                                                        <?php
                                                        $values = get_field( 'project_skills' );
                                                        if ( $values ) {
                                                                foreach ( $values as $value ) {
                                                                        echo $value . ', ';
                                                                }
                                                        } ?>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                        <?php if ( get_field( 'project_url' ) ): ?>
                                <a class="button small radius" href="<?php the_field( 'project_url' ); ?>" target="_blank">Launch Project</a>
                        <?php endif; ?>
                </div>
 
                <div class="large-6 columns">
                </div>
        </article>
</div>
        <?php endif; // is_single() ?>
 
<?php endwhile; ?>
 
<?php get_footer(); ?>