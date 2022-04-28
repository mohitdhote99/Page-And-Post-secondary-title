<?php

class Papst_MetaBox extends Papst_BackEnd{

	function __construct(){
		parent::__construct();
		if(isset( $this->settings['papst_autoshow'] ) && $this->settings['papst_autoshow'] == 'on' ){
			add_action( "add_meta_boxes", array( $this , "papst_secondary_title_add_meta_box" ) );
			add_action( "init", array( $this , "secondary_title_register_meta" ) );
			add_action( "save_post", array( $this , "papst_secondary_title_edit_post" ) );
		}
	}

	/* update secondary title on wp_post meta // note : on 1st time inserted by default wordpress functions // note : on enter any value mannuly them inserted value submitted by this fuction */
	function papst_secondary_title_edit_post()
	{
		global  $post;
	    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) { return false ;}
	    if ( !isset($_POST["secondary_title_meta_box"]) && $_POST["secondary_title_meta_box"] == '' ) { return false; }
	    $update = update_post_meta( $post->ID , "papst_title" , stripslashes( esc_attr( $_POST["secondary_title_meta_box"] ) ) );
	}

	/*content visible on meta box are coded here*/
	function papst_secondary_title_content()
	{
		global $post;
	    $secondary_title = get_post_meta($post->ID, "papst_title", true);
	    $set 			 = (isset($secondary_title) && $secondary_title !=='')?$secondary_title:'';
	    $title           = __("Enter secondary title here", "papst");
	    $placeholder     = $title . "...";
		echo '<input type="text" value="'.$set.'" id="secondary_title_meta_box" class="components-text-control__input" placeholder="'.$placeholder.'" name="secondary_title_meta_box" />';
	}

	/*add meta box on page/post/links/editor page of wordpress*/
	function papst_secondary_title_add_meta_box() 
	{
	    $screen =  isset($this->settings['papst_post_types'])?array_flip($this->settings['papst_post_types']):[];
	    add_meta_box("secondary_title_meta_box",__("Add Secondary Title", "papst"),array( $this,"papst_secondary_title_content"),$screen,'side','default');
	}

	/*register post meta creating table name of post_meta*/
	function secondary_title_register_meta() 
	{
	    register_meta("any","papst_title",["type"=> "string","single"=> true,"show_in_rest" => true]);
	}
 
}
