<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<h2>All sprites</h2>

<a href="<?php echo esc_url(admin_url('admin.php?page=sprites_generator_actions&task=new')) ?>" class="button button-primary">Add a new sprite</a>

<div id="sprite_genrator_list">

<?php



	if(sizeof($sprites) > 0)

	{

		foreach($sprites as $sprite)

		{

			echo '<div class="sprite"><h3>'.esc_html($sprite->name).'</h3>

			<a href="'.esc_url(admin_url('admin.php?page=sprites_generator_actions&task=manage&id='.$sprite->id)).'" title="Manage images"><img src="'.esc_url(plugins_url( 'images/manage.png', dirname(__FILE__))).'" /></a>

			<a href="'.esc_url(admin_url('admin.php?page=sprites_generator_actions&task=edit&id='.$sprite->id)).'" title="Edit circle content"><img src="'.esc_url(plugins_url( 'images/edit.png', dirname(__FILE__))).'" /></a>

			<a href="'.esc_url(admin_url('admin.php?page=sprites_generator_actions&task=remove&id='.$sprite->id)).'" title="Remove circle content"><img src="'.esc_url(plugins_url( 'images/remove.png', dirname(__FILE__))).'" /></a>

			</div>';

		}

	}

	else

		echo '<p>No sprite created yet !</p>';

?>

</div>

<a href="https://www.info-d-74.com/en/shop/" target="_blank">Discover my Pro Wordpress plugins</a>