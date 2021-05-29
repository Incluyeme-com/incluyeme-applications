<?php

function incluyeme_applications_assets()
{
    global $post;
    $js = plugins_url() . '/incluyeme-applications/include/assets/js/';
    $img = plugins_url() . '/incluyeme-applications/include/assets/img/incluyeme-place.svg';
    $css = plugins_url() . '/incluyeme-applications/include/assets/css/';
    if (empty($post)) {
        return $post;
    }
    
    $flag = false;
    if (stripos($post->post_content, '[incluyeme_applications')) {
        $flag = true;
    }
    
    
    if ($flag) {
        
        wp_register_script('popper', $js . 'popper.js', ['jquery'], '1.0.0');
        wp_register_script('bootstrapJs', $js . 'bootstrap.min.js', ['jquery', 'popper'], '1.0.0');
        wp_register_script('FAwesome', $js . 'fAwesome.js', [], '1.0.0', false);
        wp_register_script('vueJS', $js . 'vueDEV.js', ['bootstrapJs'], '1.0.0');
        wp_register_script('Axios', $js . 'axios.min.js', [], '2.0.0');
        wp_register_script('selectJS', $js . 'bootstrap-select.min.js', ['bootstrapJs'], '2.0.0');
        wp_register_script('bootstrap-notify', $js . 'iziToast.js', ['bootstrapJs'], '2.0.0');
        wp_register_script('defaults-es_ES', $js . 'defaults-es_ES.js', ['selectJS'], '2.0.0');
        wp_register_style('bootstrap-css', $css . 'bootstrap.min.css', [], '1.0.0', false);
        wp_register_style('bootstrap-notify-css', $css . 'iziToast.min.css', [], '1.0.0', false);
        wp_register_style('selectB-css', $css . 'bootstrap-select.min.css', ['bootstrap-css'], '1.0.0', false);
        
        wp_enqueue_script('popper');
        wp_enqueue_script('bootstrapJs');
        wp_enqueue_script('vueJS');
        wp_enqueue_script('bootstrap-notify');
        wp_enqueue_script('Axios');
        wp_enqueue_script('selectJS');
        wp_enqueue_script('defaults-es_ES');
        
        wp_enqueue_style('bootstrap-css');
        wp_enqueue_style('bootstrap-notify-css');
        wp_enqueue_style('selectB-css');
    }
}