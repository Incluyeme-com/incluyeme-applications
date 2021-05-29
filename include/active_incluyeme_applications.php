<?php
include_once 'incluyeme_applicants_shortcode.php';
function active_incluyeme_applications()
{
    add_shortcode('incluyeme_applications', 'incluyeme_applicants_shortcode');
}
