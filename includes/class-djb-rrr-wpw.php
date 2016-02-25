<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://danieljblumenfeld.com/rrr-wpwn/
 * @since      1.0.0
 *
 * @package    DJB_RRR_WPW
 * @subpackage DJB_RRR_WPW/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    DJB_RRR_WPW
 * @subpackage DJB_RRR_WPW/includes
 * @author     Dan Blumenfeld <dan@danieljblumenfeld.com>
 */
class DJB_RRR_WPW {
    
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

    //Terrain Rating combo = 1 through 5
    private function get_terrain_ratings() {
         $terrain_array = array(
            1 => '1 - Hill mania', 
            2 => '2 - Some long or steep hills', 
            3 => '3 - Mostly rolling, moderate hills', 
            4 => '4 - Mostly flat to rolling', 
            5 => '5 - Flat'
        );

        return $terrain_array;
    }

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'djb-rrr-wpw';
		$this->version = '1.0.0';

       

		$this->define_route_hooks();
	}

	/**
	 * Register all of the hooks used to extend the base RRR plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_route_hooks() {

        //TODO: we should only do this if we've fired up the RCubed plugin
        add_action('djb-rrr-save-route', array( $this, 'save'), 10, 2);

		add_filter('djb-rrr-route-type-data', array($this, 'add_wpw_route_type_data'), 10, 3);
        add_filter('djb-rrr-render-route-summary', array( $this, 'render_route_summary'), 10, 2);
        add_filter('djb-rrr-render-route-details', array( $this, 'render_route_details'), 10, 2);
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

     /**
	 * Add WPW-specific metabox data
	 * 
	 */
    function add_wpw_route_type_data($route_types, $post_id, $currType) {     
        
        $curr_map_id = get_post_meta( $post_id, '_djb_rrr_wpw_map_id', true ); 
        $curr_terrain = get_post_meta( $post_id, '_djb_rrr_wpw_terrain', true );    
        
        $wpw_route_type = new Route_Type_Data();
        $wpw_route_type->type_id ='WPW';
        $wpw_route_type->type_friendly_name = 'WPW';
        $wpw_route_type->general_metabox_markup = '';
        $wpw_route_type->type_specific_markup = '';
        $wpw_route_type->is_route_provider = 'false';

        $terrain_array = $this->get_terrain_ratings();
        $num_terrain_options = count($terrain_array);

        //Old-school WPW map packet id
        $mapId_markup = sprintf('<td>WPW Map ID:</td><td><input type="text" id="djb_rrr_wpw_map_id_val" name="djb_rrr_wpw_map_id_val" value="%s" /></td>', $curr_map_id);

        
        $terrain_markup = '<td>Terrain Rating:</td><td><select name="djb_rrr_wpw_terrain_val" id="djb_rrr_wpw_terrain_val">';
        for ($i = 1; $i <= $num_terrain_options; $i++) {
            $selected = '';
            if($i == $curr_terrain){
                $selected .= ' selected ';
            }
            $new_option = sprintf('<option value="%s"%s>%s</option>', $i, $selected, $terrain_array[$i]);
            $terrain_markup .= $new_option;
        }
        $terrain_markup .= '</select></td>';

        //Wrap it up
        $wpw_route_type->general_metabox_markup = sprintf('<table><tr>%s</tr><tr>%s</tr>', $mapId_markup, $terrain_markup);

        $route_types[] = $wpw_route_type;
        
        return $route_types;
    }

    /**
	 * Save WPW-specific data to the database
	 * 
	 */
    function save( $post_id, $post ) {
        
        // Sanitize the user input.
		$map_id = sanitize_text_field( $_POST['djb_rrr_wpw_map_id_val'] );
		$terrain_rating = sanitize_text_field( $_POST['djb_rrr_wpw_terrain_val'] );

		// Update the meta fields
		update_post_meta( $post_id, '_djb_rrr_wpw_map_id', $map_id );
		update_post_meta( $post_id, '_djb_rrr_wpw_terrain', $terrain_rating );

    }
    
    /**
	 * Render route summary information: map packet id, difficulty rating, etc
	 * 
	 */
    function render_route_summary($output, $post_id) {
        $curr_map_id = get_post_meta( $post_id, '_djb_rrr_wpw_map_id', true ); 
        $curr_terrain = get_post_meta( $post_id, '_djb_rrr_wpw_terrain', true );

        //Render map id
        if(!empty($curr_map_id)) {            
            $output .= sprintf('<div class="djb-rrr-route-info"><span class="djb-rrr-route-info-label">WPW Map ID:</span><span class="djb-rrr-route-info-value">%s</span></div>', $curr_map_id);
        }

        //Render terrain rating
        $terrain_array = $terrain_array = $this->get_terrain_ratings();        
        $output .= sprintf('<div class="djb-rrr-route-info"><span class="djb-rrr-route-info-label">Terrain Rating:</span><span class="djb-rrr-route-info-value">%s</span></div>', $terrain_array[$curr_terrain]);

        return $output;
    }

    /**
	 * Render detailed route information
	 * 
	 */
    function render_route_details($output, $post_id) {                
        $currType = get_post_meta( $post_id, '_djb_rrr_route_type_id', true );

        return $output;
    }

}
