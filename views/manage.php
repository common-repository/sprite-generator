<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>



<script>



	jQuery(document).ready(function(){



		jQuery('.sprite_images_list .remove').click(function(){



			var id = jQuery(this).attr('rel');



			jQuery.post(ajaxurl, { action: 'sprite_generator_remove_image', id: id, _ajax_nonce: '<?php echo esc_attr(wp_create_nonce( "sprite_generator_remove_image" )); ?>' }, function(){



				jQuery('.sprite_images_list li[rel='+id+']').remove();



			});



			return false;



		});



		jQuery('.form_sg .browse').click(function(e) {

	    	var _this = this;

	        e.preventDefault();

	        var image = wp.media({ 

	            title: 'Upload Image',

	            // mutiple: true if you want to upload multiple files at once

	            multiple: false

	        }).open()

	        .on('select', function(e){

	            // This will return the selected image from the Media Uploader, the result is an object

	            var uploaded_image = image.state().get('selection').first();

	            // We convert uploaded_image to a JSON object to make accessing it easier

	            // Output to the console uploaded_image

	            var file_url = uploaded_image.toJSON().url;

	            // Let's assign the url value to the input field

	            jQuery(_this).parent().find('input[name="'+jQuery(_this).attr('rel')+'"]').val(file_url);

	        });

	    });



	});



</script>



<h2>Manage sprite image "<?php echo esc_html($sprite->name) ?>"</h2>



<?php



	if(!empty($error))

		echo '<div class="message message-error">'.esc_html($error).'</div>';



?>



<form action="" method="post" class="form_sg">



	<input type="hidden" name="id" value="<?php echo esc_attr($sprite_image->id) ?>" />

	<input type="hidden" name="id_sprite" value="<?php echo esc_attr($sprite->id) ?>" />

	<input type="hidden" name="action" , value="sprite_generator_save_icon" />

	<?php wp_nonce_field( "sprite_save_icon" ); ?>



	<label for="">Name:</label> <input type="text" name="name" value="<?php echo esc_attr($sprite_image->name) ?>" /><br />



	<label for="">Image:</label> <input type="text" name="image" value="<?php echo esc_attr($sprite_image->image) ?>" />	<button class="browse button button-secondary" rel="image">Browse...</button><br />



	<input type="submit" value="Save image" class="button button-primary" /> <a href="<?php echo esc_url(admin_url('admin.php?page=sprites_generator_actions')); ?>" class="button">Back to sprite list</a>



</form>



<?php if(isset($_GET['saved'])) : ?>

	<h3>image saved and sprite regenerated!</h3>

<?php endif; ?>



<?php



	if(sizeof($images) > 0)

	{

		echo '<ul class="sprite_images_list">';				



		foreach( $images as $image )

		{

			echo '<li rel="'.esc_attr($image->id).'">

			<img src="'.esc_url($image->image).'" alt="" /><br />

			<a href="'.esc_url(admin_url('admin.php?page=sprites_generator_actions&task=manage&id='.$image->id_sprite.'&id_image='.$image->id)).'"><img src="'.esc_url(plugins_url( 'images/edit.png', dirname(__FILE__))).'" /></a>

			<a href="#" rel="'.esc_attr($image->id).'" class="remove"><img src="'.esc_url(plugins_url( 'images/remove.png', dirname(__FILE__))).'" /></a>

			</li>';



		}



		echo '</ul>';



	}

	else {

	

		echo '<p>No images yet.</p>';



		}	



?>