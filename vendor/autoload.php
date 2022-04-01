<?php
require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'vendor\gravity-wiz\GFRandomFields.php';
// Intialize randomization object to randomized questions.
$make_questions_random = new GFRandomFields( 1 ,5, array(1,4,7,16,15), false );
