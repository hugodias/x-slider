<p class="hide-if-no-js x-slider-no-bottom">
	<a title="Choose slider image" href="javascript:;" id="x-slider-choose-slider" class="button button-primary button-large">
		Choose image
	</a>
</p>


<div id="x-slider-slider-image-container" class="hidden">
	<img src="<?php echo get_post_meta( $post->ID, 'slider-src', true ); ?>"
	     title="<?php echo get_post_meta( $post->ID, 'slider-title', true ); ?>"/>
</div>

<p id="x-slider-slider-image-info" class="hidden">
	<input type="hidden" id="x-slider-slider-src" name="slider-src"
	       value="<?php echo get_post_meta( $post->ID, 'slider-src', true ); ?>"/>
	<input type="hidden" id="x-slider-slider-title" name="slider-title"
	       value="<?php echo get_post_meta( $post->ID, 'slider-title', true ); ?>"/>
</p>


<div class="x-slider-controls hidden">
	<hr>

	<div class="x-slider-enable-container">
		<label for="setAsSlider">
			<input type="checkbox" name="x-slider-selected" value="1"
			       id="setAsSlider" <?php echo get_post_meta( $post->ID, 'x-slider-selected', true ) == 1 ? "checked" : ""; ?>>
			Active
		</label>
	</div>

	<div class="x-slider-remove-slider-container">
		<p class="hide-if-no-js">
			<a title="Remove Footer Image" href="javascript:;" id="x-slider-remove-slider">
				Remove image
			</a>
		</p>
	</div>
</div>


<div class="clear"></div>
