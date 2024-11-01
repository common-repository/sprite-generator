<?php

/*

Plugin Name: Sprite Generator

Plugin URI: 

Version: 1.01

Description: Create one sprite from different images to optimize your website!

Author: Manu225

Author URI: 

Network: false

Text Domain: sprite-generator

Domain Path: 

*/

define('SPRITES_GENERATOR_FOLDER', 'sprites');

register_activation_hook( __FILE__, 'sprite_generator_install' );

register_uninstall_hook(__FILE__, 'sprite_generator_desinstall');



function sprite_generator_install() {

	global $wpdb;

	$sprite_table = $wpdb->prefix . "sprite_generator";

	$sprite_image_table = $wpdb->prefix . "sprite_generator_image";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$sql = "

        CREATE TABLE `".$sprite_table."` (

          id int(11) NOT NULL AUTO_INCREMENT,

          name varchar(50) NOT NULL,

          type tinyint(1),

          PRIMARY KEY  (id)

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

    ";

    dbDelta($sql);

    $sql = "

        CREATE TABLE `".$sprite_image_table."` (

          id int(11) NOT NULL AUTO_INCREMENT,

          name varchar(50) NOT NULL,

          image varchar(255) NOT NULL,

          width smallint(1) NOT NULL,

          height smallint(1) NOT NULL,

          x mediumint(4) NOT NULL,

          y mediumint(4) NOT NULL,

          id_sprite int(11),

          PRIMARY KEY (id)

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

    ";    

    dbDelta($sql);

}



function sprite_generator_desinstall() {



	global $wpdb;

	$sprite_table = $wpdb->prefix . "sprite_generator";

	$sprite_image_table = $wpdb->prefix . "sprite_generator_image";

	//suppression des tables

	$sql = "DROP TABLE ".$sprite_table.";";

	$wpdb->query($sql);

    $sql = "DROP TABLE ".$sprite_image_table.";";   

	$wpdb->query($sql);

}



add_action( 'admin_menu', 'register_sprite_generator_menu' );

function register_sprite_generator_menu() {

	add_menu_page('Sprite Generator', 'Sprite Generator', 'edit_pages', 'sprites_generator_actions', 'sprites_generator_actions', plugins_url( 'images/icon.png', __FILE__), 38);

}



add_action('admin_print_styles', 'sprite_generator_css' );

function sprite_generator_css() {

    wp_enqueue_style( 'SpriteGeneratorStylesheet', plugins_url('css/admin.css', __FILE__) );

}



add_action( 'admin_enqueue_scripts', 'sprite_generator_load_script_generator' );

function sprite_generator_load_script_generator() {

    wp_enqueue_script('jquery-ui-core');

    wp_register_script( 'sprite_generator_admin_js', plugins_url( 'js/admin.js', __FILE__ ) );

	wp_enqueue_script( 'sprite_generator_admin_js');

}



function sprites_generator_actions() {

	global $wpdb;

	$sprite_table = $wpdb->prefix . "sprite_generator";

	$sprite_image_table = $wpdb->prefix . "sprite_generator_image";

	if(current_user_can('edit_pages'))

	{

		if(isset($_GET['task']))

		{

			switch($_GET['task'])

			{

				case 'new':

				case 'edit':

					if(sizeof($_POST))

					{

						$query = "REPLACE INTO ".$sprite_table." (`id`, `name`, `type`)	VALUES (%d, %s, %d)";

						$query = $wpdb->prepare( $query, (int)$_POST['id'], sanitize_text_field(stripslashes_deep($_POST['name'])), (int)$_POST['type']);

						$wpdb->query( $query );

						//recreate sprite

						sprite_generator_recreate_sprite($_POST['id']);

						//on affiche tous les circle contents

						$sprites = $wpdb->get_results("SELECT * FROM ".$sprite_table." ORDER BY name");

						echo '<div class="notice notice-success">Sprite updated!</div>';

						include(plugin_dir_path( __FILE__ ) . 'views/sprites_list.php');

					}

					else

					{

						//édition d'un graph existant ?

						if(is_numeric($_GET['id']))

						{

							$q = "SELECT * FROM ".$sprite_table." WHERE id = %d";

							$query = $wpdb->prepare( $q, (int)$_GET['id']);

							$sprite = $wpdb->get_row( $query );

						}

						if(empty($sprite))

						{

							$sprite = new stdClass();

							$sprite->id = '';

							$sprite->name = '';

						}



						include(plugin_dir_path( __FILE__ ) . 'views/edit.php');

					}

				break;

				case 'manage':

					if(is_numeric($_GET['id']))

					{

						$q = "SELECT * FROM ".$sprite_table." WHERE id = %d";

						$query = $wpdb->prepare( $q, (int)$_GET['id']);

						$sprite = $wpdb->get_row( $query );

						if($sprite)

						{

							$need_recreate = false;

							if(sizeof($_POST))

							{

								$size = getimagesize($_POST['image']);

								if($size !== false)

								{

									//get las image to know the x

									$query = $wpdb->prepare("SELECT * FROM ".$sprite_image_table." WHERE id_sprite=%d ORDER BY id DESC LIMIT 1", (int)$sprite->id);

									$last_sprite = $wpdb->get_row($query);

									if($last_sprite != false)									

										$x = $last_sprite->width+$last_sprite->x;

									else

										$x = 0;

									$y = 0;

									$query = "REPLACE INTO ".$sprite_image_table." (`id`, `id_sprite`, `name`, `image`, `width`, `height`, `x`, `y`) VALUES (%d, %d, %s, %s, %d, %d, %d, %d)";

									$query = $wpdb->prepare( $query, (int)$_POST['id'], $_POST['id_sprite'], sanitize_text_field(stripslashes_deep($_POST['name'])), sanitize_text_field($_POST['image']), (int)$size[0], (int)$size[1], (int)$x, (int)$y);

									$wpdb->query( $query );

									$need_recreate = true;

									echo '<div class="notice notice-success">Image updated!</div>';

								}

								else

									$error = "Can't get image size!";

							}

						

							$q = "SELECT * FROM ".$sprite_image_table." WHERE id_sprite = %d";

							$query = $wpdb->prepare( $q, (int)$_GET['id']);

							$images = $wpdb->get_results( $query );

							if($need_recreate)

							{

								sprite_generator_recreate_sprite((int)$_GET['id']);								

							}

							if(is_numeric($_GET['id_image']))

							{

								$q = "SELECT * FROM ".$sprite_image_table." WHERE id = %d";

								$query = $wpdb->prepare( $q, (int)$_GET['id_image']);

								$sprite_image = $wpdb->get_row( $query );

							}

							else

							{

								$sprite_image = new stdClass();

								$sprite_image->id = $sprite_image->name = $sprite_image->image = '';

							}

							include(plugin_dir_path( __FILE__ ) . 'views/manage.php');

						}					

					}

				break;

				case 'remove':

					if(is_numeric($_GET['id']))

					{

						$q = "SELECT * FROM ".$sprite_table." WHERE id = %d";

						$query = $wpdb->prepare( $q, (int)$_GET['id']);

						$sprite = $wpdb->get_row( $query );

						if($sprite)

						{

							//on supprime les données et le graph

							$q = "DELETE FROM ".$sprite_image_table." WHERE id_sprite = %d";

							$query = $wpdb->prepare( $q, (int)$_GET['id']);

							$wpdb->query( $query );



							$q = "DELETE FROM ".$sprite_table." WHERE id = %d";

							$query = $wpdb->prepare( $q, (int)$_GET['id']);

							$wpdb->query( $query );

							if($sprite->type == 1)

								$ext = '.jpg';						

							else if($sprite->type == 2)

								$ext = '.png';

							else

								$ext = '.gif';

							unlink(dirname(__FILE__).'/'.SPRITES_GENERATOR_FOLDER.'/'.$sprite->id.$ext);

						}

					}

					//on affiche tous les graphs

					$sprites = $wpdb->get_results("SELECT * FROM ".$sprite_table." ORDER BY name");

					include(plugin_dir_path( __FILE__ ) . 'views/sprites_list.php');

				break;

			}

		}

		else

		{

			if(!is_numeric($_GET['id']))

			{

				//on affiche tous les graphs

				$sprites = $wpdb->get_results("SELECT * FROM ".$sprite_table." ORDER BY name");

				include(plugin_dir_path( __FILE__ ) . 'views/sprites_list.php');

			}

		}

	}

}

function sprite_generator_recreate_sprite($id)

{

	global $wpdb;

	$sprite_table = $wpdb->prefix . "sprite_generator";

	$sprite_image_table = $wpdb->prefix . "sprite_generator_image";

	$q = "SELECT * FROM ".$sprite_table." WHERE id = %d";

	$query = $wpdb->prepare( $q, (int)$id );

	$sprite = $wpdb->get_row( $query );

	if($sprite)

	{

		$q = "SELECT * FROM ".$sprite_image_table." WHERE id_sprite = %d";

		$query = $wpdb->prepare( $q, (int)$id);

		$images = $wpdb->get_results( $query );

		$sprite_width = 0;

		$sprite_height = 0;

		//recreate the sprite

		foreach($images as $image)

		{

			$sprite_width += $image->width;

			if($sprite_height < $image->height)

				$sprite_height = $image->height;

		}

		$the_sprite = imagecreatetruecolor($sprite_width, $sprite_height);

		if($the_sprite !== false)

		{

			if($sprite->type == 2 || $sprite->type == 3)

			{

				imagealphablending($the_sprite, false);

				$transparency = imagecolorallocatealpha($the_sprite, 0, 0, 0, 127);

				imagefill($the_sprite, 0, 0, $transparency);

				imagesavealpha($the_sprite, true);

			}

			foreach($images as $image)

			{

				$info = getimagesize($image->image);

				if($info !== false)

				{

					if($info[2] == IMAGETYPE_JPEG)

						$the_img = imagecreatefromjpeg($image->image);

					else if ($info[2] == IMAGETYPE_PNG)

						$the_img = imagecreatefrompng($image->image);

					else if($info[2] == IMAGETYPE_GIF)

						$the_img = imagecreatefromgif($image->image);

					if($the_img !== false)

						imagecopy($the_sprite, $the_img, $image->x, $image->y, 0, 0, $image->width, $image->height);

					else 

						echo "Can't add ".esc_url($image->image).' to sprite!';

				}

			}

			if($sprite->type == 1)

				imagejpeg($the_sprite, dirname(__FILE__).'/'.SPRITES_GENERATOR_FOLDER.'/'.$sprite->id.'.jpg');

			else if($sprite->type == 2)

				imagepng($the_sprite, dirname(__FILE__).'/'.SPRITES_GENERATOR_FOLDER.'/'.$sprite->id.'.png');

			else

				imagegif($the_sprite, dirname(__FILE__).'/'.SPRITES_GENERATOR_FOLDER.'/'.$sprite->id.'.gif');

		}

		else

			echo "Can't create sprite!";

	}

}

add_shortcode('sprite-generator-image', 'display_sprite_generator_image');

function display_sprite_generator_image($atts) {

	if(is_numeric($atts['id']))

	{

		global $wpdb;

		$sprite_table = $wpdb->prefix . "sprite_generator";

		$sprite_image_table = $wpdb->prefix . "sprite_generator_image";

		$q = "SELECT * FROM ".$sprite_image_table." WHERE id = %d";

		$query = $wpdb->prepare( $q, (int)$atts['id'] );

		$sprite_image  = $wpdb->get_row( $query );

		if($sprite_image)

		{

			wp_enqueue_style( 'SpriteGeneratorStylesheet', plugins_url('css/front.css', __FILE__) );

			$q = "SELECT * FROM ".$sprite_table." WHERE id = %d";

			$query = $wpdb->prepare( $q, (int)$sprite_image->id_sprite);

			$sprite = $wpdb->get_row( $query );

			if(isset($atts['align']))

			{

				switch($atts['align'])

				{

					case 'left':

						$align = 'left';

					break;

					case 'right':

						$align = 'right';

					break;

					default:

						$align = 'center';

				}

			}

			else

				$align = 'left';

			switch($sprite->type)

			{

				case 1: $ext = '.jpg'; break;

				case 2: $ext = '.png'; break;

				default: $ext = '.gif'; break;

			}

			$html = '<div class="sprite_generator_image '.$align.'" style="background-image: url('.plugin_dir_url(__FILE__).'/'.SPRITES_GENERATOR_FOLDER.'/'.$sprite->id.$ext.'); background-position: -'.$sprite_image->x.'px '.$sprite_image->y.'px; width: '.$sprite_image->width.'px; height: '.$sprite_image->height.'px;"></div>';

			return $html;

		}

		else

			return 'Sprite image ID '.$atts['id'].' not found !';	

	}

}

//Ajax remove image

add_action( 'wp_ajax_sprite_generator_remove_image', 'sprite_generator_remove_image' );

function sprite_generator_remove_image() {

	if(wp_verify_nonce( $_POST['_ajax_nonce'], 'sprite_generator_remove_image' ))

	{

		if(is_numeric($_POST['id']))

		{

			global $wpdb;

			$sprite_image_table = $wpdb->prefix . "sprite_generator_image";

			//get width of image

			$query = "SELECT id_sprite, width FROM ".$sprite_image_table." WHERE id = %d";

			$query = $wpdb->prepare( $query, (int)$_POST['id'] );

			$image = $wpdb->get_row($query);

			//recalculate other image x

			$query = "UPDATE ".$sprite_image_table." SET x=x-%d WHERE x >= %d AND id_sprite = %d";

			$query = $wpdb->prepare( $query, (int)$image->width, (int)$image->width, (int)$image->id_sprite );

			$wpdb->query($query);

			//echo $query;

			//delete image

			$query = "DELETE FROM ".$sprite_image_table." WHERE id = %d";

			$query = $wpdb->prepare( $query, (int)$_POST['id'] );

			$wpdb->query( $query );

			//recreate the sprite

			sprite_generator_recreate_sprite($image->id_sprite);	

		}

	}

	wp_die();

}

//Ajax modal to add shortcode

add_action( 'wp_ajax_sprite_generator_get_modal', 'sprite_generator_add_shortcode_modal' );

function sprite_generator_add_shortcode_modal() {

	if(wp_verify_nonce( $_GET['nonce'], 'sprite_generator_get_modal' ))

	{

		global $wpdb;

		$sprite_table = $wpdb->prefix . "sprite_generator";

		$sprite_image_table = $wpdb->prefix . "sprite_generator_image";

		if(!is_numeric($_GET['id']))

		{

			$q = "SELECT * FROM ".$sprite_table." ORDER BY name ASC";

			$query = $wpdb->prepare( $q );

			$sprites  = $wpdb->get_results( $query );

			include(plugin_dir_path( __FILE__ ) . 'views/modal_sprites_list.php');

		}

		else

		{

			$q = "SELECT * FROM ".$sprite_image_table." WHERE id_sprite = %d ORDER BY name ASC";

			$query = $wpdb->prepare( $q, (int)$_GET['id'] );

			$images  = $wpdb->get_results( $query );

			include(plugin_dir_path( __FILE__ ) . 'views/modal_sprite_images_list.php');

		}

		wp_die();

	}

	else

		wp_die('Error nonce!');



}

//Add button to add sprite image

add_action( 'media_buttons', 'sprite_generator_add_button' );

function sprite_generator_add_button(){

    $html = '<a href="'.admin_url( 'admin-ajax.php?action=sprite_generator_get_modal&nonce='.wp_create_nonce('sprite_generator_get_modal') ).'" class="button-secondary thickbox" id="sprite_generator_add_shortcode">Add a sprite image</a>';

    echo wp_kses_post($html);

}

?>