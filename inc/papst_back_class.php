<?php

class Papst_BackEnd{

	public $settings;
	function __construct(){
		$get_settings 	= get_option('papst_settings');
		$this->settings = $get_settings?unserialize($get_settings):[];
		$this->papst_settings_submit();
	}

	/*enques all custom stylesheets and css*/
	function papst_enques()
	{
    	wp_enqueue_style( 'papst-style' , PAPST_URL.'css/papst-style.css' , false);
	    wp_enqueue_script( 'papst-custom' , PAPST_URL.'js/papst-custom.js' ,array('jquery'), false);
	}

	/*add plugin to the sttings menu*/
	function papst_title_settings_page()
	{
	    /* Creates a new page on the admin interface */
	    add_options_page( __( "Settings" , "papst" ),__( "Page And Post Secondary Title", "papst" ),"manage_options","papst",array( $this,"papst_title_settings" ) );
	}

	/*sanitize array  and text field*/
	function Papst_sanitize_text_field_and_array($array)
	{
	    foreach ( $array as $key => $value ) {
		    if ( is_array( $value ) ) {
		    	$value = $this->Papst_sanitize_text_field_and_array( $value);
		    }else{
		    	$value = sanitize_text_field( $value );
		    }
	    }
	    return $array;
	}

	/*get secondary title added through meta box*/
	function papst_get_secondary_title( $post_id )
	{
		$secondary_title = get_post_meta( $post_id , "papst_title" , true );
		return $secondary_title ? $secondary_title : false ;
	}

	/*conditi on if any data is inserted through form*/
	function papst_settings_submit()
	{
		if ( isset( $_POST['papst_submit'] ) && $_POST['papst_submit'] == 'SAVE' )
		{
			$title_ 	= isset( $_POST['papst_title_format'] ) ? $_POST['papst_title_format'] : '' ;
			$autoshow_  = isset( $_POST['autoshow'] ) ? $_POST['autoshow'] : '' ;
			$color_ 	= isset( $_POST['Changecolor_Sec'] ) ? $_POST['Changecolor_Sec'] : '' ;
			$fsize_ 	= isset( $_POST['Changefsize'] ) ? $_POST['Changefsize'] : '' ;
			$ptype_ 	= isset( $_POST['papst_post_type'] ) ? $_POST['papst_post_type'] : [] ;
			$send_arr   = array('papst_title' 		=> $title_,
								'papst_autoshow' 	=> $autoshow_,
								'papst_color' 		=> $color_,
								'papst_fsize' 		=> $fsize_,
								'papst_post_types'  => $ptype_);
			$saniti_setting 	= $this->Papst_sanitize_text_field_and_array($send_arr);
			$serialize_setting 	= serialize($saniti_setting);
			$results			= add_option( 'papst_settings' , $serialize_setting );
			if ( !$results ) { update_option( 'papst_settings' , $serialize_setting ) ; }
		}
	}

/*main content visible on index page*/
	function papst_title_settings()
	{
		global $wp_query;
		$counts_li	='';
		$checked 	= '';
		$attr_check = __('checked="checked"','papst');
		/*fetch all post type exist in your wordpress this argument get only custom post type*/
		$post_types_customs = get_post_types(  ['public'=> true , '_builtin' => false] );

		/*setting defaultpost type to not get any other post_type of wordpress in out main aray to show in main ul li*/
		$post_types_customs['post'] = 'post';
		$post_types_customs['page'] = 'page';
		$val_show = get_option('papst_settings')?unserialize(get_option('papst_settings')):'';
		$ptypes_fetch_dta = isset($val_show['papst_post_types']) && is_array($val_show)?$val_show[ 'papst_post_types']:[];

		/*check the value of global array intersect with database value or not*/
		$fdata = array_intersect($post_types_customs,$ptypes_fetch_dta);

		/*here we are getting only keys after array intersect of post types*/
		foreach ( $post_types_customs  as $post_type ){
			$query 		= new WP_Query( ['post_type' => $post_type] );
			$checked    = isset($fdata[ $post_type ]) && $fdata[ $post_type ] == $post_type ? $attr_check : '' ;
			$counts_li .= '<li><input '.$checked.' type="checkbox" value="'.$post_type.'" name="papst_post_type[]" id="papst-'.$post_type.'">';
			$counts_li .= '<label for="papst-'.$post_type.'">'.$post_type.' ( '.$query->post_count.' '.$post_type.' ) </label></li>';
		}
		$defalt_tit = __(" %second_title%:%main_title% ","papst");
		$added_title   = isset($val_show['papst_title']) && $val_show['papst_title']? esc_html($val_show['papst_title']) :$defalt_tit;
		$sect_color    = isset($val_show['papst_color']) && $val_show['papst_color']? esc_attr( $val_show['papst_color'] ) :'' ;
		$font_siz      = isset($val_show['papst_fsize']) && $val_show['papst_fsize']? intval( $val_show['papst_fsize'] ) :'' ;
		$auto_show_on  = isset($val_show['papst_autoshow']) && $val_show['papst_autoshow']=='on'? $attr_check : '' ;
		$auto_show_off = isset($val_show['papst_autoshow']) && $val_show['papst_autoshow']=='off'? $attr_check : '' ;


		echo '<section id="papst_title" class="papstClr">
			<div class="main-title"><h1>'.__("Page And Post Secondary Title","papst").'</h1></div>
			<section class="papst_form_section FL">
			<form method="post">
			<section id="papst_general_settings" class="BR MB">
				<h3>General Settings</h3>
				<div class="row-title-format">
					<div class="papst-autoShow-setting papstClr MT">
						<label class="FL">Auto Show :</label>
					<div  class="papst_input FL">
						<span class="papst_radio">
						<input type="radio" name="autoshow" value="on" id="autoshowon" '.__( $auto_show_on , "papst" ).'>
						<label for="autoshowon">On</label>
						</span>
						<span class="papst_radio">
						<input type="radio" name="autoshow" value="off" id="autoshowoff" '.__( $auto_show_off , "papst" ).'>
						<label for="autoshowoff">Off</label>
						</span>
					</div>
					</div>
					<div class="papst-title-format papstClr MT">
						<label for="papst_title_format" class="FL">'.__("Title Format :","papst").'</label>
						<div class="papst_input FL">
						<input type="text" name="papst_title_format" id="papst_title_format" placeholder=" %second_title%:%main_title%" value="'.__( $added_title , "papst" ).'">
						<p class="description MT">'.__( 'To replace titles Use <code class="pointer" title="Add title to title format input"> %main_title% </code> for the main title and <code class="pointer" title="Add secondary title to title format input"> %second_title% </code> for the secondary title' , 'papst' ).'</p>
					</div>
					</div>
				</div>
			</section>
			<section id="papst_display_settings" class="BR MB">
				<h3>Display Settings</h3>
				<div class="row-aditional-css">
					<div class="papst-second-title-color papstClr MT">
						<label for="ChangeColor_Sec" class="FL">Second Title Color :</label>
						<div class="papst_input FL">
						<input type="color" name="Changecolor_Sec" id="Changecolor_Sec" class="regular-text" placeholder="change color of secondary title" value="'.__( $sect_color , "papst" ).'">
						</div>
					<div class="color_check FL">'.__( "Your title color appear like this" , "papst" ).'<span style="color:'.__( $sect_color ,"papst" ).'">'.__( " %secondary_Title% " , "papst" ).'</span></div>
					</div>
					<div class="papst-main-title-font-size papstClr MT">
						<label for="ChangeFsize" class="FL">Font Size :</label>
						<div class="papst_input FL">
						<input type="number" name="Changefsize" id="Changefsize" class="regular-text" value="'.__( $font_siz , "papst" ).'">
						</div>
					</div>
						<div class="papst-shoe-on-page papstClr MT">
						<label class="FL">Post Type :</label>
						<ul class="papst_input FL">'.__( $counts_li , "papst" ).'</ul>
					</div>
				</div>
			</section>

			<section id="papst_button_settings" class="BR MB">
				<div id="buttons" class="buttons MT MB">
					<input type="submit" name="papst_submit" class="papst_button_des submit" title='.__( "Click to save your changes" , "papst" ).' value="SAVE"/>
					<a type="reset" class="papst_button_des reset" title='.__( "Click to reset settings to their default values" , "papst" ).'>'.__( "RESET" , "papst" ).'</a>
				</div>
			</section>

			</form>
			</section>
			<section class="papst_side_section FL BR">'.$this->papst_sidebar().'</section>
		</section>';
	}


// contain side bar of the plugin
	function papst_sidebar(){

		return '<div>
					<!-- Improve Your Site -->
					<div class="papst_side_section_childs">
					<h3>
					<span>'.__('Wordpress Free Themes','papst').'</span>
					</h3>

					<div class="inside">
					<p>
					'.__("Want to take your site to the next level? Check out our WordPress themes on ","papst").'<a href="https://themehunk.com/free-themes/" target="_blank">click Here</a>.</p>

					<p>
					'.__("Some of our popular themes :","papst").'</p>

					<ul>
					<li>
					<a href="https://themehunk.com/product/big-store/" target="_blank">'.__(" - Big Store Buissness Theme","papst").'</a>
					</li>
					<li>
					<a href="https://themehunk.com/product/open-mart/" target="_blank">'.__("- Open Mart Buissness Theme","papst").'</a>
					</li>
					<li>
					<a href="https://themehunk.com/product/oneline-lite-theme/" target="_blank">'.__(" - One Line Multipurpose Theme","papst").'</a>
					</li>
					</ul>

					</div>
					</div>

					<!-- Donate -->
					<div class="papst_side_section_childs">
					<h3>
					<span>'.__("WordPress Free Plugins","papst").'</span>
					</h3>
					<div>
					<p>
					'.__("Like this plugin? Check out our other WordPress plugins: ","papst").'</p>
					<p>
					<a href="https://wordpress.org/plugins/lead-form-builder/" target="_blank">'.__("Lead Form Builder","papst").'</a>'.__(" - Drag &amp; Drop WordPress Form Builder","papst").'</p>
					<p>
					<a href="https://themehunk.com/product/wp-popup-builder/" target="_blank">'.__("Popup Builder","papst").'</a>'.__(" - Marketing Popup Biulder","papst").'</p>
					<p>
					<a href="https://themehunk.com/product/themehunk-megamenu/" target="_blank">'.__("Mega Menu","papst").'</a> '.__("-Advance Mega Menu","papst").'</p>
					</div>
					</div>

				</div>';
	}


}
