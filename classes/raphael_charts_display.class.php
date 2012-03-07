<?php

class raphael_charts_display
{
    public static $footer_js;
    /**
     * any js that we need on regular site pages. should only load on pages where our short codes appear...
     */
    public function register_scripts() {
        wp_register_script('rc_raphaeljs', RAPHAEL_BASE .'/js/g.raphael/raphael-min.js', array('jquery'));
        wp_register_script('rc_raphaeljs_bar', RAPHAEL_BASE .'/js/g.bar-fixed.js', array('jquery', 'rc_raphaeljs', 'rc_raphaeljs_g')); //using hacked/fixed version
        wp_register_script('rc_raphaeljs_dot', RAPHAEL_BASE .'/js/g.raphael/g.dot.js', array('jquery', 'rc_raphaeljs', 'rc_raphaeljs_g'));
        wp_register_script('rc_raphaeljs_line', RAPHAEL_BASE .'/js/g.raphael/g.line.js', array('jquery', 'rc_raphaeljs', 'rc_raphaeljs_g'));
        wp_register_script('rc_raphaeljs_pie', RAPHAEL_BASE .'/js/g.raphael/g.pie.js', array('jquery', 'rc_raphaeljs', 'rc_raphaeljs_g'));
        wp_register_script('rc_raphaeljs_g', RAPHAEL_BASE .'/js/g.raphael/g.raphael.js', array('jquery', 'rc_raphaeljs'));

        wp_register_script('rc_barchart', RAPHAEL_BASE.'/js/barchart.js', array('rc_raphaeljs_bar'));
    }
    public function script_bar_chart($values, $colors = array(), $labels = array(), $endtype = 'square', $id = 'chart-wrapper', $hover = true) {
        wp_enqueue_script('rc_raphaeljs_bar');
        wp_enqueue_script('rc_barchart');
        //$hoverjs = '';
        //if ($hover == true || $hover == 'on') { $hoverjs = '.hover(fin, fout)'; }
        if (!is_array($values)) { throw new Exception('input value must be an array'); }
        if (count($colors) < 1 || count($values) != count($colors)) { $colorjs = '[] '; } else {
            $colorjs = self::prep_js_array($colors, '#');
            /*foreach ($colors as $color) {
                $colorjs .= "'#$color', ";
            }
            $colorjs = rtrim($colorjs, ', ');
            //$colorjs .= ']';*/
        }
        $data = self::prep_js_array(preg_replace("/[^0-9]/", '', $values));//'';
        /*foreach ($values as $value) {
            $value = preg_replace("/[^0-9]/", '', $value);
            $data .= "'$value', ";
        }
        $data = rtrim($data, ', ');*/
        $labelsjs = self::prep_js_array($labels);
        return '
<script type="text/javascript">
    function bchartsettings(id, vals, endtype, colors, labels, hover)
    {
        this.id=id;
        this.vals=vals;
        this.endtype=endtype;
        this.colors=colors;
        this.labels=labels;
        this.hover=hover;
    }
    window.rc_chart_settings = new bchartsettings("'.$id.'", ['.$data.'], "'.$endtype.'", ['.$colorjs.'], ['.$labelsjs.'], "'.$hover.'");


</script>';


    }
    /**
     * function preps javascript safe array strings
     * @param array $array input arguments
     * @param string $pre optional preappend string
     * @param string $post optional string to append to the end
     * @return string js ready string
     */
    public function prep_js_array($array, $pre = '', $post = '') {
        $return = '';
        foreach ($array as $item) {
            $return .= "'$pre$item$post', ";
        }
        return rtrim($return, ', ');
    }

    public function do_shortcode($args) {
        $content_type = get_post_type();
        $ct_settings = get_option(RAPHAEL_SETTINGS.'_'.$content_type);
        $settings = get_option(RAPHAEL_SETTINGS);
        $custom = get_post_custom();
        //dpr(get_defined_vars());
        $values = $colors = $labels = array();
        foreach ($ct_settings['active_custom_fields'] as $field) {
            $values[] = $custom[$field][0];
            $colors[] = $ct_settings['custom_field_colors'][$field];
            if ($ct_settings[$content_type.'_showlabels']) { $labels[] = str_replace("%v%", $custom[$field][0], $ct_settings['custom_field_label'][$field]); }
        }

        //dpr(get_defined_vars());dpr($ct_settings[$content_type.'_dohover']);
        $js = self::script_bar_chart($values, $colors, $labels, $ct_settings[$content_type.'_barendtype'], 'chart-wrapper', $ct_settings[$content_type.'_dohover']);
        //$func = function() use ($js) { print $js; };
        //$code = "print '$js';";
        //$func = create_function('', $code);
        //dpr(get_defined_vars());
        //add_action('wp_footer', $func );
        self::$footer_js .= $js;
        return '<div id="chart-wrapper" style="width:'.$ct_settings[$content_type.'_chartwidth'].'px; height:'.$ct_settings[$content_type.'_chartheight'].'px;"></div>';
    }
    public function print_footer_js() {
        if (isset(self::$footer_js)) {
            print self::$footer_js;
        }
    }
}
