<tr id="loading_screen_text" style="display:none;" class="lp_ls_section">
	<td colspan="2">
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Enter the website name or another short text', 'loading-page' ); ?></th>
				<td>
					<?php
						$loading_page_text  				= '';
						$loading_page_text_color 			= '#ffffff';
						$loading_page_text_background_color = '#ff5c35';

						if (
								isset( $loading_page_options['lp_ls'] ) &&
								isset( $loading_page_options['lp_ls']['text'] )
						) {
							if ( isset( $loading_page_options['lp_ls']['text']['text'] ) ) {
								$loading_page_text = $loading_page_options['lp_ls']['text']['text'];
							}

							if ( isset( $loading_page_options['lp_ls']['text']['color'] ) ) {
								$loading_page_text_color = $loading_page_options['lp_ls']['text']['color'];
							}

							if ( isset( $loading_page_options['lp_ls']['text']['background'] ) ) {
								$loading_page_text_background_color = $loading_page_options['lp_ls']['text']['background'];
							}
						}
					?>
					<input aria-label="<?php esc_attr_e( 'Website name or short text', 'loading-page' ); ?>" type='text' name="lp_ls[text][text]" id="lp_ls_text" value="<?php print esc_attr( $loading_page_text ); ?>" style="width:100%" />
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Text color', 'loading-page' ); ?></th>
				<td>
					<input type="text" name="lp_ls[text][color]" aria-label="<?php esc_attr_e( 'Text color', 'loading-page' ); ?>" value="<?php print esc_attr( $loading_page_text_color ); ?>" />
					<div id="lp_ls_text_color_picker"></div>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Text background color', 'loading-page' ); ?></th>
				<td>
					<input type="text" name="lp_ls[text][background]" aria-label="<?php esc_attr_e( 'Text background color', 'loading-page' ); ?>" value="<?php print esc_attr( $loading_page_text_background_color ); ?>" />
					<div id="lp_ls_text_background_color_picker"></div>
				</td>
			</tr>
		</table>
		<script>
			jQuery(window).on( 'load', function(){
				setTimeout( function(){
					lp_ls_setColorPicker( '[name="lp_ls[text][color]"]', '#lp_ls_text_color_picker' );
					lp_ls_setColorPicker( '[name="lp_ls[text][background]"]', '#lp_ls_text_background_color_picker' );
				}, 1000 );
			});
		</script>
	</td>
</tr>