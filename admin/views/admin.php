<p class="hide-if-no-js">
	<a title="Choose slider image" href="javascript:;" id="choose-slider" class="button button-primary button-large">Choose slider image</a>
</p>


<div id="slider-image-container" class="hidden">
	<img src="<?php echo get_post_meta( $post->ID, 'slider-src', true ); ?>"
	     title="<?php echo get_post_meta( $post->ID, 'slider-title', true ); ?>"/>
</div>

<p class="hide-if-no-js hidden">
	<a title="Remove Footer Image" href="javascript:;" id="remove-slider">Remove slider image</a>
</p>

<p id="slider-image-info" class="hidden">
	<input type="hidden" id="slider-src" name="slider-src"
	       value="<?php echo get_post_meta( $post->ID, 'slider-src', true ); ?>"/>
	<input type="hidden" id="slider-title" name="slider-title"
	       value="<?php echo get_post_meta( $post->ID, 'slider-title', true ); ?>"/>
</p>
<hr>


<label for="setAsSlider">
	<input type="checkbox" name="x-slider-selected" value="1"
		   id="setAsSlider" <?php echo get_post_meta( $post->ID, 'x-slider-selected', true ) == 1 ? "checked" : ""; ?>>
	Enable
</label>