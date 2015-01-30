	<footer class="entry-meta">
		<ul class="footer-wrapper">
			<li class="tag-cloud">Tillh&ouml;r: 
				<?php
					$categories_list = get_the_category_list( ' | ' );
					if ( $categories_list ): ?>
				<span class="cat-links">
					<?php echo $categories_list; ?>
				</span>
				<?php endif; // End if categories ?>
			</li>
			<?php
			$tags_list = get_the_term_list(get_the_ID(), "post_tag",'',' | ','');
			if ( $tags_list ):  ?>
				<li class="tag-cloud">Visa bara: 
					<span class="tag-links">
					<?php echo $tags_list; ?>
					</span>
				</li>
			<?php endif; // End if $tags_list ?>
			<li class="editor vcard author"><span class="fn">Kontakta sidansvarig: <a class="page-editor" href="<?php echo get_permalink() . "?respond=".get_the_ID()."#respond"; ?>" rel='author' title='Kontakta <?php echo get_the_author(); ?>'>
			<?php echo get_the_author(); ?></a></span> <?php edit_post_link( "Redigera inl&auml;gg", " [", "]" ); ?></li>
			<li class="reviewed"><?php echo get_the_reviewed_date(get_the_ID()); ?></li>

			<li class="permalink">Direktl&auml;nk: <a href="<?php echo get_permalink(); ?>" title='Direktl&auml;nk till artikel'><?php echo get_permalink(); ?></a></li>


		</ul>
		<?php comments_template( '', true ); ?>
	</footer><!-- .entry-meta -->

