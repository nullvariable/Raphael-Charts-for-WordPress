<?php

class raphael_charts_settings
{
    public  static $defaults = array(
        'field' => array(
            'title' => 'text demo',
            'callback' => 'text_field',
            'var' => 'field',
            'help' => 'help text',
            'classes' => array('ui-state-default', 'ui-corner-all'),
        ),
        'chkbxtest' => array(
            'title' => 'checkbox',
            'callback' => 'checkbox',
            'var' => 'chkbxtest',
            'help' => 'help text',
            'classes' => array('ui-state-default', 'ui-corner-all'),
        ),
    );
    public static $page = 'raphael-charts-settings';
    public static $post_defaults = array(
        /*'field' => array(
            'title' => 'text demo',
            'callback' => 'text_field',
            'var' => 'field',
            'help' => 'help text',
        ),
        'chkbxtest' => array(
            'title' => 'checkbox',
            'callback' => 'checkbox',
            'var' => 'chkbxtest',
            'help' => 'help text',
        ),*/
        'charttype' => array(
            'title' => 'Type of chart',
            'callback' => 'dropdown',
            'var' => 'charttype',
            'help' => 'Select the type of chart you will use most with this content type.',
            'options' => array(
                'Horizontal Bar Chart' => 'hbarchart',
                'Vertical Bar Chart' => 'barchart',
                'Pie Chart' => 'piechart',
            ),
            'classes' => array('ui-state-default', 'ui-corner-all'),
        ),
        'barendtype' => array(
            'title' => 'Bar chart top style',
            'callback' => 'dropdown',
            'var' => 'barendtype',
            'help' => 'Select the type of end that you would like on your lines.',
            'options' => array(
                'Round' => 'round',
                'Soft' => 'soft',
                'Square' => 'square',
                'Sharp' => 'sharp',
            ),
            'hidden' => true,
            'reveal' => array(
                'charttype' => array('hbarchart', 'barchart'),
            ),
            'classes' => array('ui-state-default', 'ui-corner-all'),
        ),
        'dohover' => array(
            'title' => 'show data on hover',
            'callback' => 'checkbox',
            'var' => 'dohover',
            'help' => '',
        ),
        'showlabels' => array(
            'title' => 'display labels',
            'callback' => 'checkbox',
            'var' => 'showlabels',
            'help' => 'show legend text beneath each element',
        ),
        'legendcolor' => array(
            'title' => 'legend label color',
            'callback' => 'text_field',
            'var' => 'legendcolor',
            'help' => '',
            'classes' => array('colorpicker', 'ui-state-default', 'ui-corner-all'),
        ),
        'chartwidth' => array(
            'title' => 'Chart width',
            'callback' => 'text_field',
            'var' => 'chartwidth',
            'help' => 'number of pixels only, do not include em or px',
        ),
        'chartheight' => array(
            'title' => 'Chart height',
            'callback' => 'text_field',
            'var' => 'chartheight',
            'help' => 'number of pixels only, do not include em or px',
        ),
    );
    /**
     * function provides settings related function calls. Should be called via add_action hook for admin_init.
     */
    function settings_init() {
        // Add the section to reading settings so we can add our
        // fields to it

        register_setting(self::$page, RAPHAEL_SETTINGS, array('raphael_charts_settings', 'validate'));
        add_settings_section('raphael-charts-general-settings',
            'Rapha&euml;l Charts General Settings',
            array('raphael_charts_settings', 'general_settings_header'), self::$page
            );
        foreach (self::$defaults as $field) {
            if (isset($field['var']) && isset($field['title']) && isset($field['callback'])) {
                $settings = get_option(RAPHAEL_SETTINGS);
                $field['value'] = $settings[$field['var']];
                $field['name'] = RAPHAEL_SETTINGS.'['.$field['var'].']';
                self::add_settings($field, self::$page, 'raphael-charts-general-settings');
                /*add_settings_field(
                    $field['var'],
                    __($field['title'], 'rahpael-charts'),
                    array('raphael_charts_settings', $field['callback']),
                    self::$page, 'raphael-charts-general-settings', $field
                    );*/
            }
        }
        add_settings_section('raphael-charts-content-types', 'Content Type Settings', array('raphael_charts_settings', 'content_type_settings_header'), self::$page);

        //here we'll setup a generic loop that will output for each post type's settings page, found under the post type > 'chart settings'
        $active_content_types = self::get_active_post_types();
        if (isset($active_content_types) && is_array($active_content_types)) {
            foreach ($active_content_types as $active_content_type) {
                $page = $active_content_type.'-chart-settings';
                $section = 'raphael-charts-settings-post-type-'.$active_content_type;
                register_setting($page, RAPHAEL_SETTINGS.'_'.$active_content_type, array('raphael_charts_settings', 'validate'));
                add_settings_section($section, 'General Settings', array('raphael_charts_settings', 'general_content_type_settings_header'), $page);
                self::do_add_settings($active_content_type, $page, $section);
                add_settings_section($section.'-cfields', 'Custom Field Settings', array('raphael_charts_settings', 'custom_field_type_settings_header'), $page);
                self::add_custom_fields_settings($active_content_type, $page, $section.'-cfields');
                self::add_custom_fields_settings_extended($active_content_type, $page, $section.'-cfields');
                //krumo(get_defined_vars());
            }
        }
    }
    /**
     * function generates a settings field for each item in our array based on inputs.
     * for content type specific pages
     * @see add_settings_field
     * @param $content_type string, content type to add settings fields for.
     * @param $page string, page to save settings with
     * @param $section string, section to save settings with
     */
    public function do_add_settings($content_type, $page, $section) {
        //need to add custom fields for chart values here
        //$custom_fields = self::get_custom_fields($content_type);
        //foreach ()

        foreach (self::$post_defaults as $field) {
            $settings = get_option(RAPHAEL_SETTINGS.'_'.$content_type);//krumo(get_defined_vars());
            if (isset($field['var']) && isset($field['title']) && isset($field['callback'])) {
                $field['var'] = $content_type.'_'.$field['var'];
                $field['value'] = $settings[$field['var']];
                $field['name'] = RAPHAEL_SETTINGS.'_'.$content_type.'['.$field['var'].']';
                self::add_settings($field, $page, $section);
                /*add_settings_field(
                    $field['var'],
                    __($field['title'], 'rahpael-charts'),
                    array('raphael_charts_settings', $field['callback']),
                    $page, $section, $field
                );*/
                //krumo(get_defined_vars());
            }
        }
    }
    /**
     * function to loop over all custom fields and add them as checkboxes to our settings form for default charts
     * WordPress does not offer a good way to determine which custom fields are associated with which content type
     * so we will have to leave this up to the user or developer to determine
     * @param $content_type string, content type to adding settings fields to
     * @param $page string, page to save settings with
     * @param $section string, section to save settings with
     * @TODO update these fields to auto generate options with ajax as fields are added or removed
     */
    public function add_custom_fields_settings($content_type, $page, $section) {
        $fields = self::get_custom_fields();
        $settings = get_option(RAPHAEL_SETTINGS.'_'.$content_type);
        if (!is_array($settings['active_custom_fields'])) { $settings['active_custom_fields'] = array(); }
        $options = array();
        if (is_array($fields)) {
            foreach ($fields as $key => $field) {
                $settings_key = array_search($key, $settings['active_custom_fields']);
                if ( is_integer($settings_key) ) {
                    $val = true;
                } else {
                    $val = false;
                }
                $options[$key] = array(
                    'value' => $field,
                    'selected' => $val,
                    'weight' => $settings_key,
                );
                //dpr(get_defined_vars());
            }
            $field = array(
                'var' => 'active_custom_fields',
                'title' => 'Charted custom fields',
                'callback' => 'multiselect',
                'classes' => array('multiselect', 'ui-multiselect'),
                'multiple' => true,
                'options' => $options,
                'name' => RAPHAEL_SETTINGS.'_'.$content_type.'[active_custom_fields][]',
                'help' => 'you will need to save new fields before you can adjust their settings.',
                'classes' => array('ui-state-default', 'ui-corner-all'),
            );
            self::add_settings($field, $page, $section);
            /*add_settings_field(
                $field['var'],
                __($field['title'], 'rahpael-charts'),
                array('raphael_charts_settings', $field['callback']),
                $page, $section, $field
            );*/
        }
    }
    /**
     * function to loop over all custom fields and add them as checkboxes to our settings form for default charts
     * WordPress does not offer a good way to determine which custom fields are associated with which content type
     * so we will have to leave this up to the user or developer to determine
     * @param $content_type string, content type to adding settings fields to
     * @param $page string, page to save settings with
     * @param $section string, section to save settings with
     */
    public function add_custom_fields_settings_extended($content_type, $page, $section) {
        //$fields = self::get_custom_fields();
        $settings = get_option(RAPHAEL_SETTINGS.'_'.$content_type);
        if (!is_array($settings['active_custom_fields'])) { $settings['active_custom_fields'] = array(); }
        foreach ($settings['active_custom_fields'] as $name) {
            $color_field = array(
                'var' => $name.'_color',
                'title' => $name.' color',
                'callback' => 'text_field',
                'classes' => array('colorpicker', 'ui-state-default', 'ui-corner-all'),
                'value' => $settings['custom_field_colors'][$name],
                'name' => RAPHAEL_SETTINGS.'_'.$content_type."[custom_field_colors][$name]",
                'help' => '(optional) choose the default color for this chart element',

            );
            $legend_field = array(
                'var' => $name.'_label',
                'title' => $name.' display text',
                'callback' => 'text_field',
                'classes' => array('ui-state-default', 'ui-corner-all'),
                'value' => $settings['custom_field_label'][$name],
                'name' => RAPHAEL_SETTINGS.'_'.$content_type."[custom_field_label][$name]",
                'help' => '(optional) choose the text associated with this element or leave blank for none. use %v% to insert the value of the field',

            );
            self::add_settings($legend_field, $page, $section);
            self::add_settings($color_field, $page, $section);
        }
    }
    /**
     * function to add our settings fields based on a standard array.
     * @param $field array, see callbacks for expected values
     * @param $page string, settings page to attach to
     * @param $section string, settings section to attach to
     */
    public function add_settings($field, $page, $section) {
        add_settings_field(
            $field['var'],
            __($field['title'], 'rahpael-charts'),
            array('raphael_charts_settings', $field['callback']),
            $page, $section, $field
        );
    }
    /**
     * function validates settings input before returning it to be stored.
     * @param $input mixed
     * @return mixed
     */
    public function validate($input) {
        /*$p = $_POST;
        dpr(get_defined_vars());
        wp_die('input');*/
        return $input;
    }
    /**
     * function renders a header for general settings on the general settings page
     */
    public function general_settings_header() {
        print '<p>'.__('These settings apply to all charts generated with this plugin', 'raphael-charts').'</p>';
    }
    /**
     * function renders a header for settings sections on the general page for custom post types.
     */
    public function content_type_settings_header() {
        print '<p>'. __('These settings control which content types can display charts. Check any content type that should offer charts.', 'raphael-charts') .'</p>';
        print self::which_content_types();
    }
    /**
     * function renders a header for settings sections on all custom post pages
     */
    public function general_content_type_settings_header() {
        print '<p>'. __('Here you can control the specific chart options for this content type.', 'raphael-charts') .'</p>';
        print '<p>'. __('All of these options can be overridden from the shortcode, these are just the defaults.', 'raphael-charts') .'</p>';
    }
    /**
     * function renders a header for the custom fields section on all custom post pages
     */
    public function custom_field_type_settings_header() {
        print '<p>'. __('Select the custom fields that you would like displayed in your default chart.', 'raphael-charts') .'</p>';
    }
    /**
     * function creates check boxes and settings fields for each post type on the general settings page.
     */
    public function which_content_types() {
        $post_types = get_post_types(array('public'=>1), 'objects');
        //dpr($post_types);
        foreach ($post_types as $post_type) {
            $settings = get_option(RAPHAEL_SETTINGS);
            $args = array(
                'title' => $post_type->label,
                'var' => 'type_'.$post_type->name,
                'help' => '',
                'callback' => 'checkbox',
                'value' => $settings['type_'.$post_type->name],
                'name' => RAPHAEL_SETTINGS.'[type_'.$post_type->name.']',
            );
            self::add_settings($args, self::$page, 'raphael-charts-content-types');
            //add_settings_field($args['var'], $args['title'], $args['callback'], self::$page, 'raphael-charts-content-types', $args);
        }
    }
    public function get_custom_fields() {
        $fields = wp_cache_get(RAPHAEL_FIELDS_CACHE, RAPHAEL_FIELDS_CACHE);
        if (false === $fields) {
            global $wpdb;
            $fields = $wpdb->get_col( $wpdb->prepare(
                "
                    SELECT      DISTINCT meta_key
                    FROM        $wpdb->postmeta
            ") );
            wp_cache_set( RAPHAEL_FIELDS_CACHE, $fields, RAPHAEL_FIELDS_CACHE);
        }
        foreach ($fields as $key => $field) {
            $fields[$field] = $field;
            unset($fields[$key]);
        }
        return $fields;
    }
    /**
     * function for building a checkbox
     * @param $args array,
     *      'var' name of the options variable we are setting
     *      'title' name of the setting as presented to the user
     *      'help' any text added following the field to explain it's usage
     *      'name' full storage name aka raphael_settings[this_setting]
     *      'value' currently set value for this setting
     *      'hidden' boolean.
     *      'classes' string, style tags
     */
    public function checkbox($args) {
        if (isset($args['hidden']) && $args['hidden'] == true) { $args['classes'][] = 'rc_hidden '; }
        if (isset($args['classes']) && is_array($args['classes'])) { $classes = self::render_classes($args['classes']); } else { $classes = ''; }
        print '
            <input type="checkbox"'.
            $classes.'
            name="'. $args['name'] .'"
            id="'.$args['var'].'"
            '.checked($args['value'], 'on', false).'
          /> <label class="ui-helper-hidden ui-state-default" for="'. $args['var'] .'">'. $args['title'] .'</label> <em>'.__($args['help'], 'raphael-charts').'</em>';
    }
    /**
     * Function for building a text field
     * @param $args array,
     *      'var' name of the options variable we are setting
     *      'title' name of the setting as presented to the user
     *      'help' any text added following the field to explain it's usage
     *      'name'
     *      'value'
     *      'hidden'
     *      'classes'
     */
    public function text_field($args) {
        if (isset($args['hidden']) && $args['hidden'] == true) { $args['classes'][] = 'rc_hidden '; }
        if (isset($args['classes']) && is_array($args['classes'])) { $classes = self::render_classes($args['classes']); } else { $classes = ''; }
        print '
            <input
            name="'. $args['name'].'"'.
            $classes.'
            id="'.$args['var'].'"
            type="text"
            value="'.$args['value'].'"
            /> <em>'.__($args['help'], 'raphael-charts').'</em>';
    }
    /**
     * function to render a dropdown box
     * @param $args array,
     *      'var'
     *      'title'
     *      'help'
     *      'options'
     *      'name'
     *      'value'
     *      'hidden'
     *      'classes'
     */
    public function dropdown($args) {
        if (isset($args['hidden']) && $args['hidden'] == true) { $args['classes'][] = 'rc_hidden '; }
        if (isset($args['classes']) && is_array($args['classes'])) { $classes = self::render_classes($args['classes']); } else { $classes = ''; }
        print '<select name="'.$args['name'].'" id="'.$args['var'].'" '.$classes.'>';
        print '<option value=""></option>';
        foreach ($args['options'] as $title => $option) {
            $selected = ($args['value'] == $option) ? ' selected="selected"' :  '';
            print '<option value="'.$option.'"'.$selected.">".$title."</option>";
        }
        print '</select> <em>'.$args['help'].'</em>';
    }
    /**
     * function to render a multiselect box
     * @param $args array,
     *      'var'
     *      'title'
     *      'help'
     *      'options'
     *      'name'
     *      'value'
     *      'hidden'
     *      'classes'
     */
    public function multiselect($args) {
        if (isset($args['hidden']) && $args['hidden'] == true) { $args['classes'][] = 'rc_hidden '; }
        if (isset($args['classes']) && is_array($args['classes'])) { $classes = self::render_classes($args['classes']); } else { $classes = ''; }
        //$multiple = (isset($args['multiple'])) ? ' multiple="multiple"' : '';
        print '<select name="'.$args['name'].'" id="'.$args['var'].'" '.$classes.' multiple="multiple">';
        $success = usort($args['options'], array('raphael_charts_settings', 'sort_option_by_weight'));
        $options = $args['options'];
        foreach ($options as $option) {
            $selected = ($option['selected'] == true) ? ' selected="selected"' :  '';
            print '<option value="'.$option['value'].'"'.$selected.">".$option['value']."</option>";
        }
        print '</select> <em>'.$args['help'].'</em>';
        //krumo($options);
        //krumo($args);
    }
    public function sort_option_by_weight($a, $b) {
        if (is_int($a['weight']) && is_int($b['weight']) ) {
            if ($a['weight'] > $b['weight']) { return 1; } else { return -1; }
        }
        if (is_int($a['weight']) && !is_int($b['weight']) ) { return -1; }
        if (!is_int($a['weight']) && is_int($b['weight']) ) { return 1; }
        return strcmp($a['value'], $b['value']);
    }
    /**
     * helper function to add classes to an element
     * @param $classes array, array of classes
     * @return string
     */
    public function render_classes($classes) {
        if (isset($classes) && is_array($classes)) {
            $return = ' class="';
            foreach ($classes as $class) {
                if (count($class) > 0) { $return .= $class.' '; }
            }
            $return .= '"';
        } else {
            $return = '';
        }
        return rtrim($return);

    }
    /**
     * general settings page under "Settings" > "Raphael Charts" generates here
     */
    public function settings_page() {
        self::print_reveal_js(self::$defaults);
        self::form_header('Rapha&euml;l Charts for WordPress Settings');
        settings_fields('raphael-charts-settings');
        do_settings_sections('raphael-charts-settings');

        print '<p class="submit">
                <input type="submit" class="button-primary" value="'.__('Save Changes') .'" />
               </p>';

        print '</form></div>';
    }
    /**
     * general menu item hooks related to settings pages.
     */
    public function menu_item() {
        add_options_page("Rapha&euml;l Charts", "Rapha&euml;l Charts", 'manage_options', 'raphael-settings', array('raphael_charts_settings', 'settings_page'));

        //for each content type that a user has allowed charts for, add a menu page for it
        $active_post_types = self::get_active_post_types();
        if (isset($active_post_types) && is_array($active_post_types)) {
            foreach ($active_post_types as $typename) {
                add_submenu_page('edit.php?post_type='.$typename, 'Rapha&euml;l Charts Settings', 'Charts Settings', 'manage_options', $typename.'-chart-settings', array('raphael_charts_settings', 'content_type_settings_page'));
            }
        }
    }
    /**
     * any js that we need on our admin pages
     */
    public function admin_enqueue_scripts() {
        $s = wp_parse_args($_SERVER['QUERY_STRING']);
        if (isset($s['page']) && (stristr($s['page'], 'raphael-settings') || stristr($s['page'], '-chart-settings'))) {//only load our JS/CSS on our admin pages.
            $base = RAPHAEL_BASE;
            //basic admin css
            wp_enqueue_style('rc_admin', $base.'/css/admin.css');

            //css and scripts for multiselect box on content type options pages
            wp_register_script('rc_multiselect', $base.'/js/ui.multiselect.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'));
            wp_enqueue_script('rc_multiselect');
            wp_enqueue_style('rc_multiselect', $base.'/css/ui.multiselect.css');

            //jquery ui theme
            //wp_enqueue_style('rc_jquery_ui_theme', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/themes/ui-lightness/jquery-ui.css');
            //wp_enqueue_style('rc_jquery_ui_theme', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/vader/jquery-ui.css');
            //*wp_enqueue_style('rc_jquery_ui_theme', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/redmond/jquery-ui.css');
            wp_enqueue_style('rc_jquery_ui_theme', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/cupertino/jquery-ui.css');

            //jquery ui color picker css and scripts
            wp_register_script('rc_colorpicker', $base.'/js/jquery.colorpicker.js', array('jquery', 'jquery-ui-button', 'jquery-effects-core'));
            wp_enqueue_script('rc_colorpicker');
            wp_enqueue_style('rc_colorpicker', $base.'/css/colorpicker.css');
        }
    }
    /**
     * This returns an array of any active (selected via the settings page) content types
     * these will get a menu etc.
     * @return array, custom fields
     */
    public function get_active_post_types() {
        $return = array();
        $settings = get_option(RAPHAEL_SETTINGS);
        foreach ($settings as $setting =>$value) {
            if (stristr($setting, 'type_')) {
                $typename = str_ireplace('type_', '', $setting);
                $return[] = $typename;
            }
        }
        return $return;
    }
    /**
     * settings page that is generated for each active content type, lives under that content type in the menu > chart settings
     */
    public function content_type_settings_page() {
        $s = wp_parse_args($_SERVER['QUERY_STRING']);
        $post_type_name = $s['post_type'];
        $content_type = get_post_type_object($post_type_name);
        $page = $post_type_name.'-chart-settings';
        //$section = 'raphael-charts-settings-post-type-'.$post_type_name;
        //self::print_reveal_js(self::$post_defaults, $post_type_name);
        self::form_header($content_type->label.' Chart Settings');
        print '
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $("#active_custom_fields").multiselect({sortable: true, searchable: true});
                    $("input.search").val("search");
                    $("input.search").blur(function() {
                        if ($("input.search").val() === "") { $("input.search").val("search"); }
                    });
                    $("input.search").focus(function() {
                        if ($("input.search").val() === "search") { $("input.search").val(""); }
                    });

                    $("input.button-primary").click(function() {
                        $("#raphael-settings").submit();
                    });
                    './/$("#kWh-goal").colorpicker({
                    '$(".colorpicker").colorpicker({
                        showOn: \'both\',
                        onSelect: function(hex, rgba, inst) {
								console.log("select fired");
							},
                        showSwatches: true,
                        showNoneButton: true,
                        buttonColorize: true,
                        buttonImage: \''. RAPHAEL_BASE .'/images/ui-colorpicker.png\',
                        limit: \'websafe\',
                        parts: \'full\',
                        altProperties: \'background-color,color\',
				    });
                });
            </script>
            ';
//@TODO wp_cache_flush()?
        settings_fields($page);
        do_settings_sections($page);
        print '<p class="submit">
                <input type="submit" class="button-primary" value="'.__('Save Changes') .'" />
               </p>';

        print '</form></div>';
    }
    public function form_header($title) {
        print '<script type="text/javascript"> window.rc_base = "'. RAPHAEL_BASE .'";
            jQuery(document).ready(function($) {
		        $( "#raphael-settings :checkbox" ).button();
		        $( "#raphael-settings label.ui-helper-hidden" ).removeClass("ui-helper-hidden");
		        /*$( "#raphael-settings .ui-state-default" ).focus( function() { this.addClass("ui-state-active").removeClass("ui-state-default") });
		        $( "#raphael-settings .ui-state-active" ).blur( function() { this.addClass("ui-state-default").removeClass("ui-state-active") });*/
        	});
        </script> ';
        print '<div class="wrap"> <h2>'.__($title, 'raphael-charts').'</h2>';
        print '<form method="post" action="options.php" id="raphael-settings"> ';
    }
    /**
     * @TODO this doesn't work with our current setupf for forms. Needs to be reworked so it can hide/reveal form elements as it makes sense.
     * function to read through our form settings and add some hide/reveal js based on options
     * @param $variables array
     * @param $content_type string
     */
    public function print_reveal_js($variables, $content_type='') {
        $preappend = ($content_type != '') ? $content_type.'_' : '';
print "
        <script type=\"text/javascript\">
            function rc_swap(id) {
                if (jQuery(id).hasClass('rc_hidden')) {
                    jQuery(id).removeClass('rc_hidden');
                    console.log(id+' removed class');
                } else {
                    jQuery(id).addClass('rc_hidden');
                    console.log(id+' added class');
                }
            }
            jQuery(document).ready(function($) ".'{';
                foreach ($variables as $variable) {
                    /*'reveal' => array(
                        'charttype' => array('hbarchart', 'barchart'),
                    )*/
                    if (isset($variable['reveal'])) {
                        foreach ($variable['reveal'] as $element => $values) {
                            print "$('#$preappend$element').change(function() {
                                var new = 'off';
                            ";
                            foreach($values as $value) {
                                print "
                                    if ( $('#$preappend$element').val() == '$value' ) { new='on'; }
                                ";
                            }
                            print "rc_swap('$preappend{$variable['var']}', new);";
                            print "});";
                        }
                    }
                }
            print "
            $('.rc_hidden').change();
            });
            </script>
            ";

    }
}
