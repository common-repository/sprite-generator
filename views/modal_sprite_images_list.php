<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?

	if(sizeof($images) > 0)
	{
		foreach($images as $image)
			echo '<img src="'.esc_attr($image->image).'" alt="" rel="'.esc_attr($image->id).'" />';
		echo '<p style="clear:both">Click on images you want to add to your content</p>';
	}
	else
		echo '<p>No image in this sprite!</p>';

?>
</div>
<script>

	jQuery('#sprite_generator_image_selector img').click(function(){

		wp.media.editor.insert('[sprite-generator-image id="'+jQuery(this).attr('rel')+'" align="'+jQuery('#sprite_genrator_list select[name=align]').val()+'"]');
		return false;

	});

</script>