<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<h2>Select your sprite and your image</h2>

<div id="sprite_genrator_list">

	<p><label>Align: </label> <select name="align">

		<option value="left">Left</option>

		<option value="right">Right</option>

		<option value="center">Center</option>

	</select></p>

<?php



	if(sizeof($sprites) > 0)

	{

		echo '<p><label>Sprite</label> <select id="sprite_generator_selector">

		<option value="">Select a sprite</option>';

		foreach($sprites as $sprite)

			echo '<option value="'.esc_attr($sprite->id).'">'.esc_html($sprite->name).'</option>';

		echo '</select><img src="'.esc_url(plugins_url( 'images/loading.gif', dirname(__FILE__))).'" alt="Loading..." id="sprite_generator_loading" /></p>

		<div id="sprite_generator_image_selector"></div>';

	}

	else

		echo '<p>No sprite created yet !</p>';

?>

</div>

<script>

	

	jQuery('#sprite_generator_selector').change(function(){



		if(jQuery(this).val() != '')

		{

			jQuery('#sprite_generator_loading').show();



			jQuery.get(ajaxurl, {action: 'sprite_generator_get_modal', id: jQuery(this).val(), nonce: '<?php echo esc_attr(wp_create_nonce( "sprite_generator_get_modal" )); ?>' }, function(data){



				jQuery('#sprite_generator_loading').hide();



				jQuery('#sprite_generator_image_selector').html(data);



			});

		}



	});



</script>