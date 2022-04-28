<?php 

class Papst_FrontEnd extends Papst_BackEnd{
	function __construct(){
		// ::-> this is static method we want to use __construct() function of parent class thatswhy we use this
		parent::__construct();
		add_action( "admin_menu", array( $this , "papst_title_settings_page" ) );
		add_action( "admin_enqueue_scripts" ,array( $this , "papst_enques" ) );
		if (isset( $this->settings['papst_autoshow'] ) && $this->settings['papst_autoshow'] == 'on') {
			if (!is_admin()) {add_filter('the_title', array( $this ,'papst_fnc_on_load') , 10, 2);}
		}
	}

	/*interaction with frontend and backend result shows on front page*/
	function papst_fnc_on_load($title)
	{
    	global $wp_query;
		global $post;
		$return_data 	= '';
		$current_ptype 	= get_post($post)->post_type;
		$front_val 		= $this->settings;
		$new_title 		= isset($front_val['papst_title'])? esc_html( $front_val['papst_title'] ) : '';
		$secT_color    	= isset($front_val['papst_color'])? esc_attr( $front_val['papst_color'] ) : '';
		$Ft_size_sec_T  = isset($front_val['papst_fsize'])&&$front_val['papst_fsize']!==''?intval($front_val['papst_fsize']):'30';
		$ptypes    		= !empty($front_val['papst_post_types']) ? $front_val['papst_post_types'] : [];

		/* replacing %main_title% from string */
		$main_title 	= $new_title !== '' ? str_replace( '%main_title%' , $title, $new_title) : $title;

		/*fetch secondary title enterd by metabox*/
		$get_mets_title = $this->papst_get_secondary_title($post->ID);
 		$sectitle_meta 	= ($get_mets_title !== false)?stripslashes( $get_mets_title ):'';
		$output_sec_t 	= '<span style="color:'.$secT_color.';font-size:'.$Ft_size_sec_T.'px">'.$sectitle_meta.'</span>';

		/* replacing %second_title% from string if blank then replace : and return only text*/
		if ($sectitle_meta !== '') {
			$return_data = str_replace( "%second_title%" , $output_sec_t , $main_title );
		}else{
			$replce 	 = str_replace( "%second_title%" , '' , $main_title );
			$return_data = str_replace(" ","",$replce) == ':'.$title?str_replace( ":" , " " , $replce ):str_replace( ":" , " : " , $replce );
		}

    	if ( is_singular() && $wp_query->in_the_loop && in_array( $current_ptype , $ptypes ) ) { return $return_data; }
		return $title;
	}

}