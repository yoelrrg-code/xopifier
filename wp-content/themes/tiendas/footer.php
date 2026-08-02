		</div>

		<div class="how-to-modal" style="display: none;">
			<div class="how-to-modal-box">
				<i class="how-to-modal-box-close"></i>
				<div class="how-to-modal-box-content">
					<?php 
						$how_it_works = get_field('how_it_works', 'option');
						if (isset($how_it_works['description'])) {
							echo $how_it_works['description'];
						}
					?>
				</div>
			</div>
		</div>

    	<?php wp_footer(); ?>

    </body>
</html>
 