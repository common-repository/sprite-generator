<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>



<h2>Add/edit a sprite</h2>



<form action="" method="post" class="form_sg">



	<input type="hidden" name="id" value="<?php echo esc_attr($sprite->id) ?>" />



	<label for="">Name: </label> <input type="text" name="name" value="<?php echo esc_attr($sprite->name) ?>" /><br />



	<label for="">Type: </label> 

	<select name="type">

		<option value="1">JPG</option>

		<option value="2" <?php echo ($sprite->type == 2 ? 'selected' : '') ?>>PNG</option>

		<option value="3" <?php echo ($sprite->type == 3 ? 'selected' : '') ?>>GIF</option>

	</select><br />



	<input type="submit" value="Save sprite" class="button button-primary" /> <a href="<?php echo esc_url(admin_url('admin.php?page=sprites_generator_actions')); ?>" class="button">Back to sprites list</a>



</form>