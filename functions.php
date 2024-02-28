<?php
/*
 * Plugin Name:       WP-Staff Training
 * Description:       WP Staff Training is a comprehensive plugin for WordPress, designed to facilitate the creation and management of learning courses, quizzes, and add products on your website.
 * Version:           1.0.0
 * Requires at least: 4.0
 * Requires PHP:      5.4
 * Author:           Adam
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */
//get color from the settings and it will be applied for all text and button
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function st_add_custom_color_css_variable() {
    $custom_color = get_option('global_color');
    if (!empty($custom_color)) {
        echo '<style>:root { --global-color: ' . esc_attr($custom_color) . '; }</style>';
    }
}
add_action('wp_head', 'st_add_custom_color_css_variable');
add_action('admin_head', 'st_add_custom_color_css_variable');
if (!defined('st_my_plugin_dir_folder')) {
    define('st_my_plugin_dir_folder', plugin_dir_url(__File__));
}
// Enqueue scripts and styles
add_action("admin_enqueue_scripts", "st_enqueue_custom_scripts");
add_action("wp_enqueue_scripts", "st_enqueue_custom_scripts");

function st_enqueue_custom_scripts() {
    wp_enqueue_style("admin-css", st_my_plugin_dir_folder . '/css/admin.css');
    wp_enqueue_style("child-css", st_my_plugin_dir_folder . '/style.css');
    wp_enqueue_style("quizstyle-css", st_my_plugin_dir_folder . '/css/quiz-style.css');
    wp_enqueue_style("datatable-css", st_my_plugin_dir_folder . '/css/datatables.css');
    wp_enqueue_style("sweetalert-css", st_my_plugin_dir_folder . '/css/sweetalert2.css');
    wp_enqueue_style("bootstrap-css", st_my_plugin_dir_folder . '/css/bootstrap.css');
  

    wp_enqueue_media();

 wp_enqueue_script("timer-js", plugins_url("/js/timer.js", __FILE__), array('jquery'), time(), true);
    wp_enqueue_script("tagsinput", plugins_url("/js/tagsinput.js", __FILE__), array('jquery'), time(), true);
    wp_enqueue_script("validate", plugins_url("/js/jquery.validate.js", __FILE__), array('jquery'), time(), true);
    
    wp_enqueue_script("datatable-js", plugins_url("/js/datatables.js", __FILE__), array('jquery'), time(), true);
    wp_enqueue_script("sweetaleert-js", plugins_url("/js/sweetalert2.js", __FILE__), array('jquery'), time(), true);
    wp_enqueue_script("bootstrap-js", plugins_url("/js/bootstrap.js", __FILE__), array('jquery'), time(), true);

    
       // Enqueue your plugin script
       wp_enqueue_script('your-plugin-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), '1.0', true);

       // Localize script with dynamic AJAX URL
       wp_localize_script('your-plugin-script', 'myAjax', array(
           'ajax_url' => admin_url('admin-ajax.php'),
           'site_url' => site_url(),
       ));
}

register_activation_hook(__FILE__, 'st_my_plugin_activation_create_new_page');

// Activation callback function
function st_my_plugin_activation_create_new_page(){
    
  $page_title = "Dashboard";

    // Check if a page with the title exists
    $existing_page = get_page_by_title($page_title, OBJECT, 'page');

    if (!$existing_page) {
        // Create a new page
        $new_page_id = wp_insert_post(array(
            'post_title'   => $page_title,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ));

        
    } 
   

}

add_filter('page_template', 'st_checkwp_page_template');

function st_checkwp_page_template( $page_template )
{
    if ( is_page( 'Dashboard' ) ) {
        $page_template = plugin_dir_path( __FILE__ ) . 'templates/template-dashboard.php';
    }
    return $page_template;
}

function st_mycustom_plugin_create_database_table(){

    global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );



// Table names
$quiz_score_tbl = $wpdb->prefix . 'quiz_section_score';
$coin_wallet = $wpdb->prefix . 'coin_wallet';
$quiz_details = $wpdb->prefix . 'quiz_details';
$quiz_user_details = $wpdb->prefix . 'quiz_user_details';
$coin_table_name = $wpdb->prefix . 'coin_wallet';
$atl_products_table_name = $wpdb->prefix . 'atl_products';
$atl_orders_table_name = $wpdb->prefix . 'atl_orders';
$table_name = $wpdb->prefix . 'learning_sections';

// Add column in Wp-user;
// Add your custom column name
$custom_column_name = 'self_assigned_date';

// Get the prefixed table name
$custom_table_name = $wpdb->prefix . 'users';

// Check if the column exists before adding it
if ($wpdb->get_var("SHOW COLUMNS FROM {$custom_table_name} LIKE '{$custom_column_name}'") != $custom_column_name) {
    // Define the SQL query to add the new column
    $sql = "ALTER TABLE {$custom_table_name} ADD COLUMN {$custom_column_name} DATE DEFAULT NULL";

    // Execute the SQL query
    $wpdb->query($sql);
}



$sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    image varchar(255) NOT NULL,
    learning_subsection longtext NOT NULL,
    assigned_users longtext NOT NULL,
    is_trash tinyint(1) NOT NULL,
    sort_order int NOT NULL,
    cat varchar(255) NOT NULL,
    PRIMARY KEY (id)
) $charset_collate;";

$wpdb->query($sql);




// Table structure for quiz_section_score

$sql = "CREATE TABLE IF NOT EXISTS $quiz_score_tbl (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    quizid mediumint(9) NOT NULL, 
    userid mediumint(9) NOT NULL,
    sectionid mediumint(9) NOT NULL,
    average_score varchar(255),
    score_weight varchar(255),
    coins int(11),
    PRIMARY KEY (id)
) $wpdb->charset_collate;";

$wpdb->query($sql);

// Table structure for coin_wallet
$sql = "CREATE TABLE IF NOT EXISTS $coin_wallet (
    id int(11) NOT NULL AUTO_INCREMENT,
    user_id int(11) NOT NULL,
    total_coins int(11) NOT NULL,
    PRIMARY KEY (id)
) $wpdb->charset_collate;";

$wpdb->query($sql);

// Table structure for quiz_details
$sql = "CREATE TABLE IF NOT EXISTS $quiz_details (
    quizid INT NOT NULL AUTO_INCREMENT,
    sectionid INT NOT NULL,
    subsection_title VARCHAR(255) NOT NULL,
    question_list LONGTEXT NOT NULL,
    PRIMARY KEY (quizid),
    INDEX (sectionid)
) $charset_collate;";

$wpdb->query($sql);

// Table structure for quiz_user_details
$sql = "CREATE TABLE IF NOT EXISTS $quiz_user_details (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    quizid mediumint(9) NOT NULL,
    userid mediumint(9) NOT NULL,
    quizdata TEXT NOT NULL,
    score varchar(255),
    PRIMARY KEY (id)
) $wpdb->charset_collate;";

$wpdb->query($sql);



// Table structure for atl_atl_products
$sql = "CREATE TABLE IF NOT EXISTS $atl_products_table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    product_name varchar(255) NOT NULL,
    product_desc TEXT NOT NULL,
    product_price varchar(255) NOT NULL,
    image_icon varchar(255) NOT NULL,
    PRIMARY KEY (id)
) $wpdb->charset_collate;";

$wpdb->query($sql);

// Table structure for atl_orders
$sql = "CREATE TABLE IF NOT EXISTS $atl_orders_table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    product_id int(11) NOT NULL,
    user_id int(11) NOT NULL,
    order_created datetime NOT NULL,
    order_total varchar(255) NOT NULL,
    payment_method varchar(255) NOT NULL,
    order_status varchar(255) NOT NULL,
    PRIMARY KEY (id)
) $wpdb->charset_collate;";

$wpdb->query($sql);



}
register_activation_hook( __FILE__, 'st_mycustom_plugin_create_database_table' );
// Settings

function myst_childthemewpdotcom_register_settings() {

    register_setting( "childthemewpdotcom_theme_options_group", "childthemewpdotcom_setting_x", "ctwp_callback" );
    register_setting( 'ls-settings-group', 'first_wlcm_message' );
    register_setting( 'ls-settings-group', 'scnd_cmpltd_message' );
    register_setting( 'ls-settings-group', 'em_id_list' );
    register_setting( 'ls-settings-group', 'user_id_list' );
    register_setting( 'ls-settings-group', 'gold_score_min' );
    register_setting( 'ls-settings-group', 'gold_score_weight' );
    register_setting( 'ls-settings-group', 'gold_score_points' );
    register_setting( 'ls-settings-group', 'silver_score_min' );
    register_setting( 'ls-settings-group', 'silver_score_weight' );
    register_setting( 'ls-settings-group', 'silver_score_points' );
    register_setting( 'ls-settings-group', 'bronze_score_min' );
    register_setting( 'ls-settings-group', 'bronze_score_weight' );
    register_setting( 'ls-settings-group', 'fail_score_min' );
    register_setting( 'ls-settings-group', 'fail_score_weight' );
    register_setting( 'ls-settings-group', 'update_weight' );
    register_setting( 'ls-settings-group', 'update_points' );
    register_setting( 'ls-settings-group', 'if_timer' );
    register_setting( 'ls-settings-group', 'quiz_timer' );
    register_setting( 'ls-settings-group', 'atls_img' );
    register_setting( 'ls-settings-group', 'global_color' );
    
    register_setting( 'ls-settings-group', 'self_assign_wait_days' );   
    register_setting( 'ls-settings-group', 'if_leagues' );
    register_setting( 'ls-settings-group', 'if_self_assign' );
    register_setting( 'ls-settings-group', 'league1_score_min' );
    register_setting( 'ls-settings-group', 'league2_score_min' );
    register_setting( 'ls-settings-group', 'league3_score_min' );
    register_setting( 'ls-settings-group', 'league1_title' );
    register_setting( 'ls-settings-group', 'league2_title' );
    register_setting( 'ls-settings-group', 'league3_title' );

}

add_action( "admin_init", "myst_childthemewpdotcom_register_settings" );



// Options Page

function myst_childthemewpdotcom_register_options_page() {

    add_options_page("Child Theme Settings", "My Child Theme", "manage_options", "childthemewpdotcom", "myst_childthemewpdotcom_theme_options_page");

}

add_action("admin_menu", "myst_childthemewpdotcom_register_options_page");



//ChildThemeWP.com Options Form

function myst_childthemewpdotcom_theme_options_page() { ?>

<div>

    <style>
        table.childthemewpdotcom {
            table-layout: fixed;
            width: 100%;
            vertical-align: top;
        }

        table.childthemewpdotcom td {
            width: 50%;
            vertical-align: top;
            padding: 0px 20px;
        }

        #childthemewpdotcom_settings {
            padding: 0px 20px;
        }
    </style>

    <div id="childthemewpdotcom_settings">

        <h1>Child Theme Options</h1>

    </div>

    <table class="childthemewpdotcom">

        <tr>

            <td>

                <form method="post" action="options.php">

                    <h2>Parent Theme Stylesheet Include or Exclude</h2>

                    <?php settings_fields( "childthemewpdotcom_theme_options_group" ); ?>

                    <p><label><input size="76" type="checkbox" name="childthemewpdotcom_setting_x"
                                id="childthemewpdotcom_setting_x" <?php
                                if((esc_attr(get_option("childthemewpdotcom_setting_x"))=="Yes" )) {
                                echo " checked='checked' " ; } ?>

                            value="Yes" >

                            TICK To DISABLE The Parent Stylesheet style.css In Your Site HTML<br><br>

                            ONLY TICK This Box If When You Inspect Your Source Code It Contains Your Parent Stylesheet
                            style.css Two Times. Ticking This Box Will Only Include It Once.</label></p>

                    <?php submit_button(); ?>

                </form>

            </td>

        </tr>

    </table>

</div>

<?php
}

function myst_pr($data){
    echo "<pre>";
        print_r($data);
    echo "</pre>";
}
function myst_get_progress_bar($total_steps, $total_completed_step){?>

<div class="progress-section">

    <?php

            $pro_percentage = 0;

            if($total_completed_step > 0){

                $pro_percentage = 100 / $total_steps;

                $pro_percentage = $pro_percentage * $total_completed_step;

            }

        ?>

    <div class="progres-bar">

        <div class="bar-top">

            <label>My Progress</label>

            <div class="pro-percentage">
                <?php echo esc_html(round($pro_percentage));?>
            </div>

        </div>

        <div class="pro-percentage-bar">

            <div class="current-progress" style="width: <?php echo esc_attr(round($pro_percentage));?>%;"></div>

        </div>

    </div>

</div>

<?php }


/**
 * Get quiz progress bar
 */
function myst_get_quiz_progress_bar($quiz_score){
    
    $quiz_score = round($quiz_score);
    if($quiz_score == get_option('gold_score_min')):
        $cls = 'trophygold.png';
    elseif($quiz_score >= get_option('silver_score_min') && $quiz_score <= (get_option('gold_score_min') - 1)):
        $cls = 'trophysilver.png';
    elseif($quiz_score >= get_option('bronze_score_min') && $quiz_score <= (get_option('silver_score_min') - 1)):
        $cls = 'trophybronze.png';
    elseif($quiz_score >= get_option('fail_score_min') && $quiz_score <= (get_option('bronze_score_min') - 1)):
        $cls = 'trophyx.png';
    endif;
    ?>
<div class="progress-section">
    <div class="progres-bar">
        <div class="bar-top">
            <label>My Score</label>
            <div class="pro-percentage">
                <span>
                    <?php echo esc_html($quiz_score); ?>%
                </span>
                <img src="<?php echo st_my_plugin_dir_folder.'/images/'.$cls; ?>" width="25px;" />
            </div>
        </div>
        <div class="pro-percentage-bar">
            <div class="current-progress" style="width: <?php echo esc_html($quiz_score); ?>%;"></div>
        </div>
    </div>
</div>
<?php }

/* Get Current Steps */

function myst_get_current_steps($learning_modules){

    $count = 0;

    foreach ($learning_modules['pages'] as $key => $value) {
        if($value['status'] == 'completed' ){
            $sectionid = $learning_modules['id'];
            $count++;
        }
    }
    foreach ($learning_modules['pages'] as $key => $value) {
        if($value['status'] != 'completed' ){
            
            $link = $value['sub_start_url'].'?step='.urlencode($value['sub_title']);
            break;
        }
    }

    if($learning_modules['is_all_complated'] == 1){

        echo '<a href="javascript:void(0)">Completed</a>';

        echo '<a href="javascript:void(0)" class="display-results show-popup" data-showpopup="quizresult_' . esc_attr($sectionid) . '" data-sid="' . esc_attr($sectionid) . '">Results</a>';
        
            ?>
<div class="overlay-content popup" id="cmplt_quizresult_<?php echo esc_attr($sectionid); ?>">
    <button class="close-btn">x</button>
    <div class="learning-modules box-shadows">
        <?php myst_get_wrong_answer_list(get_current_user_id(),$sectionid); ?>
    </div>
</div>
<?php
        echo '<a href="javascript:void(0)" class="redo-course" data-sid="' . esc_attr($sectionid) . '">Redo Course</a>';


    }
    
    
    else if($count == 0){

        echo '<a href="' . esc_url($link) . '">Start</a>';

    } else{

        echo '<a href="'.esc_url($link).'">Continue</a>';

    }

}

function myst_get_wrong_answer_list($user_id,$section_id){
    global $wpdb;
    $quiz_table = $wpdb->prefix.'quiz_details';
    $quiz_user_table = $wpdb->prefix.'quiz_user_details';
    $quiz_section_score = $wpdb->prefix.'quiz_section_score';
    
    $section_data = myst_staff_training_get_specific_section_by_id($section_id);
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}quiz_details as qt 
            INNER JOIN {$wpdb->prefix}quiz_user_details as qut 
            ON qt.quizid = qut.quizid 
            WHERE qt.sectionid = %d AND qut.userid = %d",
            $section_id,
            $user_id
        ),
        ARRAY_A
    );
    
    //echo 'before email sending <pre> '; print_r($results); die();
    $final_array= $prarray = array();
    foreach($results as $row){
        
        $subsection_title = explode("_",$row['subsection_title'])[1];
        $score = json_decode($row['score'],true);
        $percentage = $score['percentage'];
        $prarray[] = $score['percentage'];
        
        $scoredata = $score['scoredata'];
        //if Score is 0 then incorrect answer
        $question_number = $questionarray = array();
        foreach($scoredata as $ind => $sc){
            if($sc == 0) {
                $question_number[] = $ind;
            }
        }
        $question_list = json_decode($row['question_list'],true);
        $quizdata = json_decode($row['quizdata'],true);
        
        
        foreach($question_number as $qn){
            
            $ca = $question_list[$qn]['CorrectAnswer'];
            
            $questionarray[] = array(
                'Question' => $question_list[$qn]['Question'], 
                'Allanswers' => $question_list[$qn]['Answers'],
                'CorrectAnswer' => $question_list[$qn]['CorrectAnswer'], //$ca." : ".$question_list[$qn]['Answers'][$ca], 
                'useranswer' => $quizdata[$qn] //. " : ".$question_list[$qn]['Answers'][$quizdata[$qn]]
            );
        }
        $final_array[$subsection_title] = array('percentage' => $percentage, 'answerdata' => $questionarray);
        
    }
    if(!empty($prarray)) {
        
    
        $avg = array_sum($prarray) / count($prarray);
        $quiz_score = round($avg);
        if($quiz_score == get_option('gold_score_min')):
            $cls = 'trophygold.png';
            $weight = get_option('gold_score_weight');
            
        elseif($quiz_score >= get_option('silver_score_min') && $quiz_score <= (get_option('gold_score_min') - 1)):
            $cls = 'trophysilver.png';
            $weight = get_option('silver_score_weight');
            
        elseif($quiz_score >= get_option('bronze_score_min') && $quiz_score <= (get_option('silver_score_min') - 1)):
            $cls = 'trophybronze.png';
            $weight = get_option('bronze_score_weight');
        
            
        elseif($quiz_score >= get_option('fail_score_min') && $quiz_score <= (get_option('bronze_score_min') - 1)):
            $cls = 'trophyx.png';
            $weight = get_option('fail_score_weight');
            
        endif;
    }
    ?>
<div class='quiz-information'>

    <h3>
    <?php echo esc_html($section_data->title); ?>

    </h3>


    <div class="progress-section" style="margin-bottom:5px">
        <div class="progres-bar">
            <div class="bar-top">
                <div class="pro-percentage" style="padding-bottom: 5px;">
                    <span>
                        <?php echo esc_html($quiz_score); ?>%
                    </span>
                    <img src="<?php echo st_my_plugin_dir_folder.'/images/'.$cls; ?>" width="25px;" />
                </div>
            </div>
            <div class="pro-percentage-bar">
                <div class="current-progress" style="width:<?php echo esc_attr($quiz_score);?>%;"></div>
            </div>

        </div>
    </div>

    <table class='info-tab' border='1'>
        <thead>
            <tr>
                <th>Incorrect Answers</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($final_array as $sbtitle => $ansdata){ ?>

            <tr>
                <td>
                    <h6>
                        <?php echo esc_html($sbtitle); ?>
                    </h6>
                    <ol class="quiz-result-list">
                        <?php if(!empty($ansdata['answerdata'])) {
                            foreach($ansdata['answerdata'] as $k => $qs ){
                                $ct =$cc= array(); 
                            ?>
                        <li style='border-bottom:1px dashed #777;padding: 10px 0;list-style:decimal;'>
                            <div>
                                <span><b>Question</b> : </span><span>
                                <?php echo esc_html($qs['Question']); ?>
                                </span>
                            </div>
                            <div>
                                <p style="color:green;"><b>Correct Answer</b> : </p>
                                <p>
                                    <?php foreach($qs['CorrectAnswer'] as $ca ){
                                                
                                                $ct[] = $ca." : ".$qs['Allanswers'][$ca];
                                            }
                                            echo implode('<br/>',$ct);
                                            ?>
                                </p>
                            </div>
                            <?php foreach($qs['useranswer'] as $cp ){
                                        
                                        $cc[] = $cp." : ".$qs['Allanswers'][$cp];
                                    } 

                                    
                                    ?>
                            <div>
                                <p style="color:red;"><b>Given Answer</b> :</p>
                                <p>
                                    <?php echo implode('<br/>',$cc); ?>
                                </p>
                            </div>
                        </li>
                        <?php }
                        }else{
                            echo 'All answers are correct';
                        } ?>
                    </ol>
                </td>
            </tr>
            <?php } ?>

        </tbody>
    </table>

</div>
<?php echo wp_kses_post($body);

}


/* Custom Shortcode */

add_shortcode('myst_learning_modules_action_btn', 'myst_learning_modules_action_btn');

function myst_learning_modules_action_btn(){

    global $wp, $wpdb;
    ob_start();

    if ( is_user_logged_in() ) {

        if(is_user_logged_in()){
            $user_id = get_current_user_id();
        }        
        $learning_modules = get_user_meta( $user_id , 'learning_modules_progress', true);

        $learning_modules = unserialize($learning_modules);
        $page_link = home_url( add_query_arg( array(), $wp->request ) );
        foreach ($learning_modules as $key => $val) {
            

            $numItems = count($val['pages']);
            $count = 0;

            foreach ($val['pages'] as $page_keys => $page_val){


                if($page_val['sub_completed_url'] == $page_link || $page_val['sub_completed_url'] ==  $page_link.'/'){
                    /* if($user_id == 3 || $user_id == 2) :  */
                        $subtitle = $val['id'].'_'.$page_val['sub_title'];
                        $results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}quiz_details where sectionid = {$val['id']} AND subsection_title = '$subtitle' ",ARRAY_A);

                    
                        if(!empty($results)){
                            $userdata = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}quiz_user_details where userid = {$user_id} AND quizid = {$results['quizid']} ", ARRAY_A);
                         
                            
                        }
                        

                        if(!empty($results) && empty($userdata)) {
                            echo '<div class="quiz-wrapper">';
                                // echo '<h1>Quiz available in ... </h1>';
                                if(esc_attr( get_option('if_timer') ) == 'y') {
                                    echo '<p name="quiz_abailable_text" id="quiz_abailable_text">Quiz available in ... </p><div class="countdown"></div>';
                                    echo '<input type="hidden" value=" '.esc_attr( get_option('quiz_timer')).'" name="quiz_btn_timer" id="quiz_btn_timer">';
                                }

                                echo '<div class="myst_learning_modules_action_btn_quiz"><a href="javascript:void(0);" class="start_quiz" data-quizid="'.$results['quizid'].'">Start Quiz</a></div><style>.myst_learning_modules_action_btn_quiz a {display: block;width: 100%;text-align: center;color: #1288cc !important;border: 1px solid #1288cc;font-size: 18px;margin: 0 auto;font-weight: bold;text-transform: uppercase;text-decoration: none !important;padding: 15px;font-family: "Roboto";max-width: 200px;} .myst_learning_modules_action_btn_quiz a:hover {background: #1288cc;color: #fff !important;}</style>';
                                wp_quiz_wizard_popup($results['quizid'],$user_id);

                            echo '</div>';
                            echo '<style>
                            /* Timer css */
                            .base-timer {
                                position: relative;
                                width: 60px;
                                height: 60px;
                            }
                            
                            .base-timer__svg {
                                transform: scaleX(-1);
                            }
                            
                            .base-timer__circle {
                                fill: none;
                                stroke: none;
                            }
                            
                            .base-timer__path-elapsed {
                                stroke-width: 10px;
                                stroke: grey;
                            }
                            
                            .base-timer__path-remaining {
                                stroke-width: 10px;
                                stroke-linecap: round;
                                transform: rotate(90deg);
                                transform-origin: center;
                                transition: 1s linear all;
                                fill-rule: nonzero;
                                stroke: currentColor;
                            }
                            
                            .base-timer__path-remaining.green {
                                color: #13a89e;
                            }
                            
                            .base-timer__path-remaining.orange {
                                color: orange;
                            }
                            
                            .base-timer__path-remaining.red {
                                color: red;
                            }
                            
                            .base-timer__label {
                                position: absolute;
                                width: 60px;
                                height: 60px;
                                top: 0;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 18px;
                                color:#000;
                            }

                            /* Timer css */
                            .quiz-wrapper{
                                position:relative;
                                display:flex;
                                justify-content:center;
                                align-items:center;
                                width:100%;
                            }
                            .quiz-wrapper .countdown {
                                margin-right:10px;
                                font-size:28px;
                                line-height:30px;
                                color:#39b54a;
                                font-weight:600;
                            }
                            body.has-popup{
                                overflow:hidden;
                            }
                            .popup-quiz-overlay {

                                display: none;
                                position: fixed;
                                z-index: 1;
                                left: 0;
                                top: 0;
                                width: 100%;
                                height: 100%;
                                overflow: auto;
                                background-color: rgb(0,0,0);
                                background-color: rgba(0,0,0,0.4);
                            }
                            .popup-content{
                                background-color: #fefefe;
                                margin: auto;
                                border: 1px solid #888;
                                width: 80%;
                                position: absolute;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                            }
                            .popup-content .close {
                                color: #aaa;
                                float: right;
                                font-size: 28px;
                                font-weight: bold;
                            }
                            .popup-content .close:hover,
                            .popup-content .close:focus {
                              color: black;
                              text-decoration: none;
                              cursor: pointer;
                            }
                            .modal-body {
                                padding: 15px;
                                text-align: center;
                                max-height: 500px;
                                overflow: auto;
                            }
                            .modal-header {
                                padding: 12px 16px;
                                background-color: #fffefe;
                                border-bottom: 1px solid #ddd;
                                color: white;
                                text-align:center;
                            }
                            .modal-header h2 {
                                margin: 0 auto;
                                font-size: 30px !important;
                            }
                            .modal-content {
                                position: relative;
                                background-color: #fefefe;
                                margin: auto;
                                padding: 0;
                                border: 1px solid #888;
                                width: 80%;
                                box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);

                            }
                            .popup-content{
                                border-radius:14px;
                            }
                            .popup-content .modal-header{
                                border-radius: 14px 14px 0 0;
                                    padding: 12px 26px;
                            }
                            .popup-content .modal-body{
                                    padding: 15px 30px;
                            }

                            .ans-wrap{
                                display: flex;
                                justify-content: center;
                                flex-wrap: wrap;
                                justify-content: center;
                            }
                            .ans-d {
                                display: flex;
                                flex-wrap: wrap;
                                justify-content: center;
                                width: calc(50% - 20px);
                                margin: 10px;
                            }
                            .ans-d span.options {
                                border: 1px solid;
                                border-radius: 10px;
                                width: 120%;
                                overflow:hidden;
                            }
                            .ans-d span.options input{
                                display:none;
                            }
                            .options label {
                                position: relative;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                padding: 20px 10px;
                                cursor:pointer;
                                z-index: 9;
                                height: 100%;
                                box-sizing: border-box;
                            }
                            .options label::before {
                                content: "";
                                display: block;
                                width: 100%;
                                height: 100%;
                                position: absolute;
                                top: 0;
                                left: 0;
                                right: 0;
                                bottom: 0;
                                z-index: -1;
                                border-radius: 10px;
                            }
                            .options input:checked + label::before {
                                background: #d5ebc2;
                            }

                            .question-wrapper h6{
                                font-weight:bold !important;
                            }

                            .next-btn, button.continue-btn {
                                border: 0;
                                appearance: none;
                                cursor:pointer;
                                background: #298b24;
                                border:1px solid #298b24;
                                padding: 10px;
                                width: 120px;
                                display: inline-block;
                                vertical-align: middle;
                                border-radius: 14px;
                                color: #fff;
                                font-weight: bold;
                                font-size: 16px;
                                transition:all 0.3s ease-in-out;
                            }

                            .next-btn:hover, button.continue-btn:hover{
                                background:transparent;
                                color:#298b24;
                            }

                            .ques-next {
                                text-align: right;
                                margin: auto;
                                margin-top: 20px;
                            }
                            .question-wrapper:not(:first-child) {
                                display: none;
                            }
                            button.continue-btn{
                                margin-top:10px;
                            }
                            .show-status {
                                font-size: 16px;
                                font-weight: 600;
                                letter-spacing: 1.0px;
                                line-height: 18px;
                            }
                            .show-status.success{
                                color:green;
                            }
                            .show-status.error{
                                color:red;
                            }
                            .modal-body.overlay {
                                opacity: 0.5;
                                pointer-events: none;
                            }
                            .People-top-header.learning-top-header{
                                padding-bottom: 30px;
                            }
                            .myst_learning_modules_action_btn_quiz a.start_quiz.disabled {
                                opacity: 0.5;
                                pointer-events: none;
                                cursor: not-allowed !important;
                            }
                            @media all and (max-width:1199px){
                                .question-wrapper h6{
                                    margin: 0px 0 10px;
                                    font-size: 18px !important;
                                }
                                .options label{
                                    padding: 15px 10px;
                                }
                                .modal-header h2{
                                    font-size: 26px !important;
                                }
                            }

                            @media all and (max-width:767px){
                                .modal-header h2 {
                                    font-size: 22px !important;
                                }
                                .question-wrapper h6{
                                    font-size: 18px !important;
                                }
                                .options label{
                                    padding:10px;
                                }
                                .ans-d{
                                    width:100%;
                                    margin-left:0;
                                    margin-right:0;
                                }
                            }

                            @media all and (max-width:575px){
                                .question-wrapper h6,
                                .next-btn {
                                    font-size: 14px !important;
                                }

                                .modal-header h2 {
                                    font-size: 20px !important;
                                }
                            }
                            </style>';
                            break;
                        }else{
                            echo '<div class="myst_learning_modules_action_btn"><a href="">Complete</a></div><style>.myst_learning_modules_action_btn a {display: block;width: 100%;text-align: center;color: #1288cc !important;border: 1px solid #1288cc;font-size: 18px;margin: 0 auto;font-weight: bold;text-transform: uppercase;text-decoration: none !important;padding: 15px;font-family: "Roboto";max-width: 200px;} .myst_learning_modules_action_btn a:hover {background: #1288cc;color: #fff !important;}</style>';
                            break;
                        }
                   
                }

            }
        }

    }

    $btn_result = ob_get_contents();

        ob_end_clean();

        return $btn_result;

}

/**Quiz Wizard popup */
function wp_quiz_wizard_popup($quizid,$userid){

    global $wpdb;

    $results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}quiz_details where quizid = {$quizid} ",ARRAY_A);

    $title = explode("_",$results['subsection_title'])[1];
    $secid = $results['sectionid'];
    ?>
<div class="popup-quiz-overlay">
    <div class="popup-content">
        
        <div class="modal-header">
            <span class="close">×</span>
            <h2 data-title="<?php echo esc_attr($title); ?>">
                <?php echo esc_html($title); ?> Quiz
            </h2>
            <input type="hidden" value="<?php echo esc_attr($quizid); ?>" name="quiz_id" id="quiz_id" />
            <input type="hidden" value="<?php echo esc_attr($userid); ?>" name="user_id" id="user_id" />
        </div>
        <div class="modal-body">
            <?php
                $list = json_decode($results['question_list']);
                foreach($list as $key => $value){
                    ?>
            <div class="question-wrapper" data-qid="<?php echo $key; ?>">
                <h6 class="q-title">Question
                    <?php echo $key.'. '.$value->Question; ?>
                </h6>
                <div class="ans-wrap">
                    <?php
                        $count_correct = count($value->CorrectAnswer);
                        foreach($value->Answers as $k => $ans) {

                            if(in_array($k,$value->CorrectAnswer)){
                                $selected = 'yes';
                            }else{
                                $selected = 'no';
                            }
                            if($count_correct > 1) {
                            ?>

                    <div class="ans-d">
                        <span class="options"><input type="checkbox" name="q_answer[]"
                                id="q_answer_<?php echo $key . '_' . $k; ?>" data-isselected="<?php echo $selected; ?>"
                                value="<?php echo $k; ?>" />
                            <label for="q_answer_<?php echo $key . '_' . $k; ?>" name="answerlabel">
                                <?php echo esc_attr($ans); ?>
                            </label>
                        </span>

                    </div>
                    <?php
                            }else{
                            ?>
                    <div class="ans-d">
                        <span class="options"><input type="radio" name="q_answer_<?php echo $key; ?>"
                                id="q_answer_<?php echo $key . '_' . $k;?>" data-isselected="<?php echo $selected; ?>"
                                value="<?php echo $k;  ?>" />
                            <label for="q_answer_<?php echo $key . '_' . $k;?>" name="answerlabel">
                                <?php echo esc_attr($ans);?>
                            </label>
                        </span>

                    </div>
                    <?php
                            }
                        }
                        ?>
                </div><!-- .ans-wrap -->
                <div class="ques-next">
                    <button class="next-btn" data-nextid="<?php  echo $key+1;  ?>">Next</button>
                </div>
            </div><!-- .question-wrapper -->
            <?php
                }
                ?>
            <div class="final-screen" style="display:none">
                <p>Your recent score for this quiz has been added to your total for this section.</p>
                <div class="ques-complete">
                    <button class="continue-btn">Continue</button>
                </div>
            </div>
            <div class="loading-gif" style="display:none">
                <img src="<?php echo st_my_plugin_dir_folder ?>/templates/loading.gif" alt="" class="loader"
                    width="30px" height="30px">
            </div>
        </div>
        
    </div>
</div>

<?php
}

add_action('wp_ajax_myst_quiz_modules_save_action_frontend','myst_quiz_modules_save_action_frontend');
add_action('wp_ajax_nopriv_myst_quiz_modules_save_action_frontend','myst_quiz_modules_save_action_frontend');
function myst_quiz_modules_save_action_frontend(){
    global $wpdb;
    
    $quizid = sanitize_text_field( $_POST['quizid'] );
    $userid = sanitize_text_field($_POST['userid']);
    $quizdata = $_POST['quizdata'];
    $correctCount = 0;
    

    $quiz_table_name = $wpdb->prefix . 'quiz_user_details';
    $if_exists = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}quiz_user_details where quizid = {$quizid} AND userid = {$userid}",ARRAY_A);

    $results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}quiz_details where quizid = {$quizid}",ARRAY_A);

    
    $qlist = json_decode($results['question_list'],true);
    $total_no_qu = count($qlist);
    foreach($qlist as $q => $ans){
        $c_list = $ans['CorrectAnswer'];
        $user_ans = $quizdata[$q]['answerid'];
        $submit_data[$q] = $quizdata[$q]['answerid'];
        $check_ans = array_diff($c_list,$user_ans);
        if(!empty($check_ans)) {
            //wrong answer
            $scoreData[$q] = '0';

        }else if(empty($check_ans)){
            //right answer
            $scoreData[$q] = '1';
            $correctCount++;
        }

    }
    //percentage = correct * 100 / total_no_question
    $quiz_percentage = ( ($correctCount * 100) / $total_no_qu);

    $finalscore = array('scoredata' => $scoreData, 'percentage' => number_format($quiz_percentage,2,'.','') );

    if(!empty($if_exists)) {
        //update the quiz score
        $update = $wpdb->update(
            $quiz_table_name,
            array(

                'quizdata' => json_encode($submit_data),
                'score' => json_encode($finalscore),
            ),
            array(
                'quizid' => $quizid,
                'userid' => $userid,
            )
        );

        if($update) {
            $response['status'] = 'update_success';
            $response['message'] = 'Quiz score updated successfully.';
        }else{
            $response['status'] = 'update_error';
            $response['message'] = 'There is some issue in updating quiz score. Please try again.';
        }

    }else{

        $insert = $wpdb->insert(
            $quiz_table_name,
            array(
                'quizid' => $quizid,
                'userid' => $userid,
                'quizdata' => json_encode($submit_data),
                'score' => json_encode($finalscore),
            )
        );
        if($insert) {
            $response['status'] = 'insert_success';
            $response['message'] = 'Quiz completed successfully.';
        }else{
            $response['status'] = 'insert_error';
            $response['message'] = 'There is some issue in completing quiz. Please try again.';
        }
    }

    echo json_encode($response);
    die;
}


/* Store the step date */

function myst_learning_modules_save_action() {
    global $wpdb;
    if(is_user_logged_in()){
        $user_id = get_current_user_id();
    }    
    //$sid = $_POST['sectionid'];
    $learning_modules = get_user_meta( $user_id , 'learning_modules_progress', true);
    $flag = false;
    $learning_modules = unserialize($learning_modules);

    $page_link = $_POST['data'];
    
    $userdata = get_userdata($user_id);
    $username = $userdata->data->user_login;
    $send_email = $userdata->data->user_email;
    $display_name = $userdata->data->display_name;
    
    foreach ($learning_modules as $key => $val) {
        
        $numItems = count($val['pages']);
        $count = 0;
        
        $pagesname = array_keys($val['pages']);
        foreach ($val['pages'] as $page_keys => $page_val){

            if($page_val['sub_completed_url'] == $page_link || $page_val['sub_completed_url'] == $_POST['data'].'/'){
                
                /**Redirect user to another subsection after completing the first subsection quiz 
                 * fetching next page url and 
                 * if all subsection completed then page url should be dashboard - it will be changed in (flag == false) condition
                */
                $k = array_search($page_keys, $pagesname);
                $newpagekey = $pagesname[$k+1];
                if($newpagekey == '') {
                    $pageurl = 'dashboard';
                }else{
                    $pageurl = $learning_modules[$key]['pages'][$newpagekey]['sub_start_url'].'?step='.urlencode($learning_modules[$key]['pages'][$newpagekey]['sub_title']);
                }

                $learning_modules[$key]['pages'][$page_keys]['status'] = 'completed';
                $section_id = $key;
                $section_title = $learning_modules[$key]['title'];
                if($learning_modules[$key]['is_all_complated'] == '1'){
                    $flag = true;
                    continue;
                }
                break;

            }
            
        }
        foreach($learning_modules[$key]['pages'] as $page_k => $page_v){
            if($page_v['status'] == 'completed' ){
                $count++;
            }
        }
        if($count === $numItems) {

            $learning_modules[$key]['is_all_complated'] = '1';
            
        }
    }
    
    if($flag == false) {
        $quiz_table = $wpdb->prefix.'quiz_details';
        // $if_quiz_exist = $wpdb->get_results("SELECT * FROM $quiz_table WHERE sectionid = $section_id",ARRAY_A);
        $if_quiz_exist = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}quiz_details WHERE sectionid = %d",
                $section_id
            ),
            ARRAY_A
        );

        $updated_learning_modules = serialize($learning_modules);

        $updated = update_user_meta($user_id, 'learning_modules_progress',$updated_learning_modules);
    
        
        if($learning_modules[$section_id]['is_all_complated'] == '1' && !empty($if_quiz_exist)) {
            /* if all sub section completed then next page url should be dashboard */
            $pageurl = "dashboard"; 
        
            $subject = "{$display_name} has completed {$section_title} ";
            $subject2 = "You have completed Section '{$section_title}' ";
            $quiz_table = $wpdb->prefix.'quiz_details';
            $quiz_user_table = $wpdb->prefix.'quiz_user_details';
            $quiz_section_score = $wpdb->prefix.'quiz_section_score';
            $coin_wallet = $wpdb->prefix.'coin_wallet';
            //wrong answer - 0, right answer -1 
            
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}quiz_details as qt 
                    INNER JOIN {$wpdb->prefix}quiz_user_details as qut 
                    ON qt.quizid = qut.quizid 
                    WHERE qt.sectionid = %d AND qut.userid = %d",
                    $section_id,
                    $user_id
                ),
                ARRAY_A
            );
          
            //echo 'before email sending <pre> '; print_r($results); die();
            $final_array= $prarray = array();
            foreach($results as $row){
                $subsection_title = explode("_",$row['subsection_title'])[1];
                $score = json_decode($row['score'],true);
                $percentage = $score['percentage'];
                $prarray[] = $score['percentage'];
                $scoredata = $score['scoredata'];
                //if Score is 0 then incorrect answer
                $question_number = $questionarray = array();
                foreach($scoredata as $ind => $sc){
                    if($sc == 0) {
                        $question_number[] = $ind;
                    }
                }
                $question_list = json_decode($row['question_list'],true);
                $quizdata = json_decode($row['quizdata'],true);
                
                
                foreach($question_number as $qn){
                    /* $questionsdata[] = $question_list[$qn]['Question'];
                    $useranswer[] = $quizdata[$qn];
                    $correctans[] = $question_list[$qn]['CorrectAnswer']; */
                    $ca = $question_list[$qn]['CorrectAnswer'];
                    
                    $questionarray[] = array(
                        'Question' => $question_list[$qn]['Question'], 
                        'Allanswers' => $question_list[$qn]['Answers'],
                        'CorrectAnswer' => $question_list[$qn]['CorrectAnswer'], //$ca." : ".$question_list[$qn]['Answers'][$ca], 
                        'useranswer' => $quizdata[$qn] //. " : ".$question_list[$qn]['Answers'][$quizdata[$qn]]
                    );
                }
                $final_array[$subsection_title] = array('percentage' => $percentage, 'answerdata' => $questionarray);
                
            }
            // echo 'before email sending <pre> '; print_r($prarray); 

            $avg = array_sum($prarray) / count($prarray);
            $quiz_score = round($avg);
            if($quiz_score == get_option('gold_score_min')):
                $cls = 'trophygold.png';
                $weight = get_option('gold_score_weight');
                $coin = get_option('gold_score_points') + get_option( 'silver_score_points' );
                $pbs_cn = get_option('gold_score_points') + get_option( 'silver_score_points' );
            elseif($quiz_score >= get_option('silver_score_min') && $quiz_score <= (get_option('gold_score_min') - 1)):
                $cls = 'trophysilver.png';
                $weight = get_option('silver_score_weight');
                $coin = get_option( 'silver_score_points' );
                $pbs_cn = get_option( 'silver_score_points' );
            elseif($quiz_score >= get_option('bronze_score_min') && $quiz_score <= (get_option('silver_score_min') - 1)):
                $cls = 'trophybronze.png';
                $weight = get_option('bronze_score_weight');
                $coin = 0;
                
            elseif($quiz_score >= get_option('fail_score_min') && $quiz_score <= (get_option('bronze_score_min') - 1)):
                $cls = 'trophyx.png';
                $weight = get_option('fail_score_weight');
                $coin = 0;
            endif;
            // echo 'here before coins';
            $getcoins = json_decode(get_user_meta($user_id,'redo_course_coins',true),true);
            // echo 'redo points '.$getcoins[$section_id];
            if(!empty($getcoins) && $coin != 0) {
                $pbs_coins = json_decode(get_user_meta( $user_id, 'points_by_section', true ),true);
                
                //if not empty and redo coins are less than <25 then add the remaining points.
                if(!empty($getcoins[$section_id]) && $getcoins[$section_id] != (get_option('gold_score_points') + get_option( 'silver_score_points' ))) {
                    if($pbs_coins[$section_id] != (get_option('gold_score_points') + get_option( 'silver_score_points' )) ) {

                        $coin = abs($coin - $getcoins[$section_id]);                        
                    }
            
                }else if($getcoins[$section_id] == (get_option('gold_score_points') + get_option( 'silver_score_points' ))){
                    if($pbs_coins != (get_option('gold_score_points') + get_option( 'silver_score_points' )) ) {

                        //if redo coins are already 25 then do not add any
                        $coin = $getcoins[$section_id];
                    }
                    
                }
                unset($getcoins[$section_id]);
                if(empty($getcoins) || $getcoins == '[]') {
                    delete_user_meta($user_id,'redo_course_coins');
                }else{
                    update_user_meta($user_id,'redo_course_coins', json_encode($getcoins));
                }

            }
            
            $scoreinsert = $wpdb->insert($quiz_section_score,array('userid' => $user_id,'sectionid'=>$section_id,'average_score' => $quiz_score,'score_weight' => $weight,'coins' => $pbs_cn));
            
            $coin_data = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}coin_wallet WHERE user_id = {$user_id} ",ARRAY_A);
           
            
            if(empty($coin_data)) {
                $insert = $wpdb->insert($coin_wallet,array('user_id' => $row['userid'], 'total_coins'=>$coin));
                add_user_meta( $user_id, 'points_by_section', json_encode(array($section_id => $coin)) );

            }else{
                $coin_amt = (int)$coin_data['total_coins'];
                // if(!empty($getcoins[$section_id])) {
                //  if(($coin == (get_option('gold_score_points') + get_option( 'silver_score_points' ))) || ($coin == get_option( 'silver_score_points' ))) {
                //      $coin = 0;
                //  }
                // }
                $total_amt = $coin_amt + (int)$coin;    
                
                $lastpoints = get_user_meta( $user_id, 'points_by_section', true );
                $lastpoints2 = json_decode($lastpoints,true);
                // echo 'current sec id '.$section_id;
                // print_r($lastpoints2); 
                if($lastpoints2[$section_id] == '') {
                    $lastpoints2[$section_id] = $pbs_cn;
                    update_user_meta( $user_id, 'points_by_section', json_encode($lastpoints2) );
                    $update = $wpdb->update($coin_wallet,array('total_coins'=> $total_amt),array('user_id'=>$row['userid']));
                    // echo 'in if';
                }else if($lastpoints2[$section_id] != (get_option('gold_score_points') + get_option( 'silver_score_points' ))){

                    $lastpoints2[$section_id] = $pbs_cn;
                    update_user_meta( $user_id, 'points_by_section', json_encode($lastpoints2) );
                    $update = $wpdb->update($coin_wallet,array('total_coins'=> $total_amt),array('user_id'=>$row['userid']));
                    // echo 'in else if';
                }
                // die();

            }
            
            $body = "<div class='quiz-information'>";

                $body .= "<h3>{$username} </h3>";
                $body .= "<h3>{$section_title} </h3>";
                
                $body .= '<style>';
                    $body .='.pro-percentage-bar {height: 10px;width: 300px;border-radius: 10px;background: #d2d1d3;margin-top: 0px;position: sticky;z-index: 0;
                    }';
                    $body .= '.pro-percentage-bar .current-progress {background: #4d9ffc;content: "";display: block;height: 10px;border-radius: 10px;z-index: 1;
                    }';
                $body .='</style>';
                $body .='<div class="progress-section">
                    <div class="progres-bar" style="display: -webkit-box;display: -ms-flexbox;display: flex;-ms-flex-wrap: wrap;flex-wrap: wrap;-webkit-box-orient: horizontal;-webkit-box-align: center;-ms-flex-align: center;align-items: center;">                  
                        <div class="pro-percentage-bar" style="height: 10px; max-width:300px;width:100%;border-radius: 10px;background: #d2d1d3;margin-top: 0px;position: sticky;z-index: 0; width: calc(100% - 100px);">
                            <div class="current-progress" style="width: '.$quiz_score.'%;"></div>
                        </div>
                        <div class="bar-top" style="width: 100px ;text-align: right;-webkit-box-pack: end;-ms-flex-pack: end;justify-content: flex-end;">                       
                            <div class="pro-percentage" style="padding-bottom: 0;">
                                <span>'.$quiz_score.'%</span>
                                <img src="'.st_my_plugin_dir_folder.'/images/'.$cls.'" width="25px;"/>
                            </div>
                        </div>
                    </div>
                </div>';
                
                
                    //$ansdata['percentage'];
                    $body .= "<table class='info-tab' border='1' style='width:100%'>";
                        
                    $body .= "<thead>";
                        $body .= "<tr>";
                            
                            $body .= "<th>Incorrect Answers</th>";
                            
                        $body .= "</tr>";
                    $body .= "</thead>";
                    
                    $body .= "<tbody>";
                    foreach($final_array as $sbtitle => $ansdata){
                        
                        $body .= "<tr>";
                            
                            $body .= "<td style=padding: 10px 10px'><p style='margin: 10px 10px' >{$sbtitle}</p>";
                                $body .="<ol>";
                                if(!empty($ansdata['answerdata'])) {
                                    foreach($ansdata['answerdata'] as $k => $qs ){
                                        $ct =$cc= array();
                                        $body .= "<li style='border-bottom:1px solid #777;padding: 10px 0;'> <b>Question</b> : {$qs['Question']} <br/> <b>Correct Answer</b> : ";foreach($qs['CorrectAnswer'] as $ca ){
                                            
                                            $ct[] = $ca." : ".$qs['Allanswers'][$ca];
                                        }
                                        foreach($qs['useranswer'] as $cp ){
                                            
                                            $cc[] = $cp." : ".$qs['Allanswers'][$cp];
                                        }
                                        $body .= implode('<br/>',$ct);
                                        $body.=" <br/> <b>Given Answer</b> : ".implode('<br/>',$cc) ."</li>";
                                    }
                                }else{
                                    $body .='All answers are correct';
                                }
                                $body .="</ol>";
                            $body .= "</td>";
                        $body .= "</tr>";
                    }
                        
                    $body .= "</tbody>";
                    $body .= "</table>";
                
            $body .= "</div>";
            $serverHostname = $_SERVER['HTTP_HOST'];
            $currentDomain = preg_replace('/:\d+$/', '', $serverHostname);
            $headers = array('Content-Type: text/html; charset=UTF-8','From:  <noreply@' . $currentDomain . '>');
            //$to = get_bloginfo('admin_email') .", dholtby@wpstafflandscaping.ca, simon@wpstafflandscaping.ca";//comment by previous developer
            $to = esc_attr( get_option('em_id_list') );
            //$to = 'rs@narola.email'; //comment by previous developer


         $sent = wp_mail($to,$subject,$body,$headers);
            $sent1 = wp_mail($send_email,$subject2,$body,$headers);

          

        }
    }
    
    echo wp_json_encode( array("success" => "success", "pageurl" => $pageurl) );

    die;

}

add_action( 'wp_ajax_myst_learning_modules_save_action', 'myst_learning_modules_save_action' );

add_action( 'wp_ajax_nopriv_myst_learning_modules_save_action', 'myst_learning_modules_save_action' );


/**Update all existing weight of sections */
add_action('wp_ajax_myst_lm_score_weight_update','myst_lm_score_weight_update_bkp');
add_action('wp_ajax_nopriv_myst_lm_score_weight_update','myst_lm_score_weight_update_bkp');
function myst_lm_score_weight_update_bkp(){
    global $wpdb;

     
    $gs = get_option('gold_score_min');
    $gw = get_option('gold_score_weight');
    
    $ss = get_option('silver_score_min');
    $sw = get_option('silver_score_weight');

    $bs = get_option('bronze_score_min');
    $bw = get_option('bronze_score_weight');

    $fs = get_option('fail_score_min');
    $fw = get_option('fail_score_weight');

    $quiz_section_score = $wpdb->prefix.'quiz_section_score';
    
    // $allr = $wpdb->get_results("SELECT * FROM {$quiz_section_score}", ARRAY_A);
    $allr = $wpdb->get_results(
        "SELECT * FROM {$wpdb->prefix}quiz_section_score",
        ARRAY_A
    );
    foreach($allr as $row){
        if($row['average_score'] == $gs):
            
            $weight = $gw;
        elseif($row['average_score'] >= $ss && $row['average_score'] <= ($gs - 1)):
            
            $weight = $sw;
        elseif($row['average_score'] >= $bs && $row['average_score'] <= ($ss - 1)):
            
            $weight = $bw;
        elseif($row['average_score'] >= $fs && $row['average_score'] <= ($bs - 1)):
            
            $weight = $fw;
        endif;
        $scoreupdate = $wpdb->update($quiz_section_score,array('score_weight' => $weight),array('id' => $row['id']));
    }

        
    echo 'success';
    die;
}


/**Update all existing points/amount earned by users */
add_action('wp_ajax_myst_lm_points_update','myst_lm_points_update_bkp');
add_action('wp_ajax_nopriv_myst_lm_points_update','myst_lm_points_update_bkp');
function myst_lm_points_update_bkp(){
    global $wpdb;

     
    $gs = get_option('gold_score_min');
    $gw = get_option('gold_score_points');
    
    $ss = get_option('silver_score_min');
    $sw = get_option('silver_score_points');

    $bs = get_option('bronze_score_min');
    
    $fs = get_option('fail_score_min');

    $quiz_section_score = $wpdb->prefix.'quiz_section_score';
    $coin_wallet = $wpdb->prefix.'coin_wallet';
    
    // $allr = $wpdb->get_results("SELECT * FROM {$quiz_section_score}", ARRAY_A);
    $allr = $wpdb->get_results(
        "SELECT * FROM {$wpdb->prefix}quiz_section_score",
        ARRAY_A
    );
    foreach($allr as $row){
        if($row['average_score'] == $gs):
            
            $coins = $sw + $gw;
        elseif($row['average_score'] >= $ss && $row['average_score'] <= ($gs - 1)):
            
            $coins = $sw;
        elseif($row['average_score'] >= $bs && $row['average_score'] <= ($ss - 1)):
            
            $coins = $bw;
        elseif($row['average_score'] >= $fs && $row['average_score'] <= ($bs - 1)):
            
            $coins = $fw;
        endif;
        $section_id = $row['sectionid'];
        //$scoreupdate = $wpdb->update($quiz_section_score,array('coins' => $coins),array('id' => $row['id']));
        $coin_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}coin_wallet WHERE user_id = {$row['userid']} ",ARRAY_A);
        
        
        if($coins != 0) {
            $lastpoints = get_user_meta( $row['userid'], 'points_by_section', true );
            
            if(empty($lastpoints)) {
                add_user_meta( $row['userid'], 'points_by_section', json_encode(array($section_id => $coins)) );
            }else{

                $lastpoints2 = json_decode($lastpoints,true);
                $lastpoints2[$section_id] = $coins;
                
                update_user_meta( $row['userid'], 'points_by_section', json_encode($lastpoints2) );
            }
        }
            if(empty($coin_data)) {
                $insert = $wpdb->insert($coin_wallet,array('user_id' => $row['userid'], 'total_coins'=>$coins ));
            
            }else{
                $coin_amt = (int)$coin_data['total_coins'];
                $total_amt = $coin_amt + (int)$coin;    
                $update = $wpdb->update($coin_wallet,array('total_coins'=> $total_amt),array('user_id'=>$row['userid']));

                
            }
    }   
    echo 'success';
    die;
}


/* Redirect user to Training Dashboard after successfully log in */

function myst_login_redirect( $redirect_to, $request, $user ){

    return '/dashboard';

}

add_filter( 'login_redirect', 'myst_login_redirect', 15, 3 );



//add_filter( 'wp_mail_from', 'myst_custom_mail_from' ); 
function myst_custom_mail_from( $old ) { 
    return get_option( 'admin_email' ); 
} 

add_filter( 'wp_mail_from_name', 'myst_custom_mail_from_name' ); 
function myst_custom_mail_from_name( $old ) { 
    return get_option( 'blogname' ); 
}

/* Redirect User to Login page if User Not looged in*/

// add_action( 'wp_head', 'myst_redirect_if_user_not_logged_in' );

// function myst_redirect_if_user_not_logged_in() {

//  if ( !is_user_logged_in() && is_page(['how-we-work','entry-equipment', 'service']) ) {

//          wp_safe_redirect( 'https://wpstafflearning.com/wp-admin');

//          exit;

//      }

// }



/* Redirect User to Login page if User Not looged in*/

add_action( 'template_redirect', 'myst_redirect_if_user_not_logged_in',5 );

function myst_redirect_if_user_not_logged_in() {

    if ( !is_user_logged_in() && !is_page(501)) {

        wp_redirect( site_url().'/wp-admin');
        exit;
    }
    if(is_user_logged_in() && is_page(501)){
        
        wp_redirect(site_url().'/dashboard',301);
        exit;
    }

}

add_action('admin_footer','myst_myst_draggable_script');
function myst_myst_draggable_script(){
    if(is_admin()) {
        
        ?>
<script>
    var temp = <?php echo (isset($select_keyword_json) && $select_keyword_json != '') ? $select_keyword_json : 'null';?>;
    jQuery(document).ready(function ($) {
        $('input[name="em_id_list"]').tagsinput({
            trimValue: true,
            confirmKeys: [13, 44, 32],
            focusClass: 'has-focus',
            maxTags: 5,
            allowDuplicates: false,
            typehead: {
                source: '<?php echo  get_option('em_id_list') ; ?>'
                    }
        });

      //  jQuery("#user_id_list").select2({
       //     tags: false,
      //      placeholder: "Select users",

      //  });


        $('.bootstrap-tagsinput input').on('focus', function () {
            $(this).closest('.bootstrap-tagsinput').addClass('has-focus');
        }).on('blur', function () {
            $(this).closest('.bootstrap-tagsinput').removeClass('has-focus');
        });



        // var itemList = $('#ui-sortable');

        // itemList.sortable({
        //  update: function(event, ui) {
        //      //$('#loading-animation').show(); // Show the animate loading gif while waiting

        //      opts = {
        //          url: '<?php //echo site_url(); ?>/wp-admin/admin-ajax.php', 
        //          type: 'POST',
        //          async: true,
        //          cache: false,
        //          dataType: 'json',
        //          data:{
        //              action: 'item_sort', // Tell WordPress how to handle this ajax request
        //              order: itemList.sortable('toArray').toString() // Passes ID's of list items in  1,3,2 format
        //          },
        //          success: function(response) {
        //              //$('#loading-animation').hide(); // Hide the loading animation
        //              return; 
        //          },
        //          error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
        //              alert(e);
        //              // alert('There was an error saving the updates');
        //              //$('#loading-animation').hide(); // Hide the loading animation
        //              return; 
        //          }
        //      };
        //      $.ajax(opts);
        //  }
        // }); 

        jQuery('#update_weight').click(function () {

            jQuery('#update_weight').attr('disabled', 'disabled');

            jQuery.ajax({
                url: '<?php echo esc_url(site_url('/wp-admin/admin-ajax.php')); ?>',
                type: "POST",
                data: {
                    action: 'myst_lm_score_weight_update',
                },
                success: function (data) {

                    if (data == 'success') {
                        jQuery('#update_weight').removeAttr('disabled');
                        window.location.href = '<?php echo  esc_url(site_url()); ?>/wp-admin/admin.php?page=message-settings';

                    }

                }

            });
        });
        jQuery('#update_points').click(function () {

            jQuery('#update_points').attr('disabled', 'disabled');

            jQuery.ajax({
                url: '<?php echo esc_url(site_url('/wp-admin/admin-ajax.php')); ?>',
                type: "POST",
                data: {
                    action: 'myst_lm_points_update',
                },
                success: function (data) {

                    if (data == 'success') {
                        jQuery('#update_points').removeAttr('disabled');
                        window.location.href = '<?php echo  esc_url(site_url());?>/wp-admin/admin.php?page=message-settings';

                    }

                }

            });
        });
    });
</script>
<?php
    }
}

/*Redirect user tp Parent Sub section*/



add_action( 'wp_footer', 'myst_added_tab_switching' );

function myst_added_tab_switching() {

    if ( is_page(['how-we-work','entry-equipment', 'service']) ) {

        if(isset($_GET)){?>

<script>

    jQuery(document).ready(function () {

        var current_step = '<?php echo esc_js($_GET['step']);?>';

        setTimeout(function () {

            jQuery(".thrv_tabs_shortcode ul.tve_clearfix li").each(function (index) {

                var inner_text = jQuery.trim(jQuery(this).text());

                if (inner_text == current_step) {

                    jQuery(this).trigger('click');

                }

            });

        }, 300);

    });

</script>

<?php

        }

    }

}



function myst_my_save_item_order() {
    global $wpdb;

    $order = explode(',', $_POST['order']);
    $counter = 0;
    foreach ($order as $item_id) {
        //$wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $item_id) );
        $counter++;
    }
    die(1);
}
add_action('wp_ajax_item_sort', 'myst_my_save_item_order');
add_action('wp_ajax_nopriv_item_sort', 'myst_my_save_item_order');





function myst_staff_training_menu() {

    add_menu_page(

        __( 'Learning Sections', 'wpstaff_training' ),

        __( 'Learning Sections', 'wpstaff_training' ),

        'manage_options',

        'learning_sections',

        'myst_staff_wpstaff_training_page_contents',

        'dashicons-schedule',

        3

    );
    add_submenu_page( 'admin.php?page=quiz-section', __('Quiz', 'wpstaff_training' ), '', 'manage_options', 'quiz-section', 'myst_staff_training_quiz_page_contents');
    add_submenu_page( 'learning_sections', __('Staff', 'wpstaff_training' ), __( 'Staff', 'wpstaff_training' ), 'manage_options', 'staff-data', 'myst_staff_training_staff_list',6);
    add_submenu_page( 'learning_sections', __('Leaderboard', 'wpstaff_training' ), __( 'Leaderboard', 'wpstaff_training' ), 'manage_options', 'leaderboard-data', 'myst_staff_training_lb_data',7);
    add_submenu_page( 'learning_sections', __('Shop', 'wpstaff_training' ), __( 'Shop', 'wpstaff_training' ), 'manage_options', 'shop-items', 'myst_staff_training_shop_item',8);
    add_submenu_page( 'learning_sections', __('Orders', 'wpstaff_training' ), __( 'Orders', 'wpstaff_training' ), 'manage_options', 'manage-orders', 'myst_staff_training_order_data',9);
    add_submenu_page( 'learning_sections', __('User points', 'wpstaff_training' ), __( 'User points', 'wpstaff_training' ), 'manage_options', 'manage-userpoints', 'myst_staff_training_user_points',10);
    add_submenu_page( 'learning_sections', __('Settings', 'wpstaff_training' ), __( 'Settings', 'wpstaff_training' ), 'administrator', 'message-settings', 'myst_staff_training_settings',11);
    add_submenu_page( 'learning_sections', __('Trash List', 'wpstaff_training' ), __( 'Trash', 'wpstaff_training' ), 'manage_options', 'trash-list', 'myst_staff_training_trash_list',12);
    //add_submenu_page( 'learning_sections', __('Staff', 'wpstaff_training' ), __( 'Staff', 'wpstaff_training' ), 'manage_options', 'people-list', 'wpstaff_people_list',6);

}



add_action( 'admin_menu', 'myst_staff_training_menu',5 );



function myst_staff_wpstaff_training_page_contents() {

    include_once 'templates/template-learning-section.php';

}
function myst_staff_training_quiz_page_contents() {
    include_once 'templates/template-quiz-section.php';
}
function myst_staff_training_trash_list() {
    include_once 'templates/template-trash-data.php';
}
/* function wpstaff_people_list() {
    include_once 'templates/template-people-data.php';
} */
function myst_staff_training_staff_list() {
    include_once 'templates/template-people-data.php';
}

function myst_staff_training_lb_data() {
    include_once 'templates/template-leader-board.php';
}

function myst_staff_training_shop_item(){
    include_once 'templates/template-shop-items.php';
}

function myst_staff_training_order_data(){
    include_once 'templates/template-orders.php';
}

function myst_staff_training_user_points(){
    include_once 'templates/template-user-points.php';
}

function myst_staff_training_fetch_img_url($turl){
    return '<img src="'.st_my_plugin_dir_folder.'/images/'.$turl.'" height="30px;" width="30px;">';
}

function myst_staff_training_settings() {
?>
<div class="wrap">
    <h1>Settings</h1>

    <form method="post" action="options.php" class="options-form">
        <?php settings_fields( 'ls-settings-group' ); ?>
        <?php do_settings_sections( 'ls-settings-group' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label>Welcome Message </label>
                    <!-- <span>Message that will be displayed first for all new users who haven't completed any section yet.</span> -->
                </th>
                <td>
                    <textarea rows="5" cols="100"
                        name="first_wlcm_message"><?php echo esc_attr( get_option('first_wlcm_message') ); ?></textarea>

                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label>Message after user has completed any section </label>
                    <!-- <span>Message that will be displayed after user has completed any section.</span> -->
                </th>
                <td>
                    <div class="msgs-wrap">
                        <?php 
                            foreach(get_option('scnd_cmpltd_message') as $key => $val) {
                            ?>
                        <div>
                            <textarea rows="3" cols="100" name="scnd_cmpltd_message[]"
                                class="message-after"><?php echo esc_attr( $val ); ?></textarea>
                            <a href="javascript:void(0)" onclick="deletethis(jQuery(this))">X</a>
                        </div>
                        <?php } ?>

                    </div>
                    <div class="add-new-msg">
                        <a href="#" class="button">+ Add New Message</a>
                    </div>
                </td>

            </tr>


        </table>
        <hr>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label>Email IDs for sending emails</label>

                </th>
                <td>

                    <input type="text" name="em_id_list" value="<?php echo get_option('em_id_list'); ?>">
                    <small class="form-text text-muted">Separate with a comma, space bar, or enter key. You can add max
                        5 email ids. It will be used for sending emails at the time of course completion.</small>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label>User IDs to exclude from Leaderboard page</label>

                </th>
                <td>
                    <select class="excl_users" name="user_id_list[]" id="user_id_list" multiple="multiple"
                        placeholder="Select users">
                        <?php
                            $exclude_users = get_option('user_id_list'); 
                            foreach(get_users() as $d => $uvalue) {
                                
                                //$exclude_users = explode(",",get_option('user_id_list'));
                                if(!empty($exclude_users)) {

                                    $selected = (in_array($uvalue->ID,$exclude_users)) ? 'selected' : '';
                                }
                                echo '<option value="'.$uvalue->ID.'" '.$selected.'>'.$uvalue->user_login.'</option>';
                            }
                            ?>
                    </select>

                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <p><label>Select score for trophy</label> </p>

                    <p class="trp-scr"><small>Minimum score</small></p>
                    <p class="trp-scr"><small>Trohpy Weight</small></p>
                    <p class="trp-scr"><small>Points/Amount to be collected</small></p>

                    <!-- <span>Message that will be displayed first for all new users who haven't completed any section yet.</span> -->
                </th>
                <td>
                    <div class="trophydata">
                        <div class="range-cls">
                            <p>
                                <?php echo myst_staff_training_fetch_img_url('trophygold.png'); ?>
                            </p>
                            <label><input type="number" name="gold_score_min" min="0" max="100" placeholder="eg. 100"
                                    value="<?php echo esc_attr( get_option('gold_score_min') ); ?>"></label>
                            <label><input type="number" name="gold_score_weight" min="-10" max="100" placeholder="eg. 5"
                                    value="<?php echo esc_attr( get_option('gold_score_weight') ); ?>"></label>
                            <label><input type="number" name="gold_score_points" min="0" max="30" placeholder="eg. 15"
                                    value="<?php echo esc_attr( get_option('gold_score_points') ); ?>"></label>
                        </div>
                        <div class="range-cls">
                            <p>
                                <?php echo myst_staff_training_fetch_img_url('trophysilver.png'); ?>
                            </p>
                            <label><input type="number" name="silver_score_min" min="0" max="100" placeholder="eg. 100"
                                    value="<?php echo esc_attr( get_option('silver_score_min') ); ?>"></label>
                            <label><input type="number" name="silver_score_weight" min="-10" max="100"
                                    placeholder="eg. 5"
                                    value="<?php echo esc_attr( get_option('silver_score_weight') ); ?>"></label>
                            <label><input type="number" name="silver_score_points" min="0" max="30" placeholder="eg. 15"
                                    value="<?php echo esc_attr( get_option('silver_score_points') ); ?>"></label>
                        </div>
                        <div class="range-cls">
                            <p>
                                <?php echo myst_staff_training_fetch_img_url('trophybronze.png'); ?>
                            </p>
                            <label><input type="number" name="bronze_score_min" min="0" max="100" placeholder="eg. 100"
                                    value="<?php echo esc_attr( get_option('bronze_score_min') ); ?>"></label>

                            <label><input type="number" name="bronze_score_weight" min="-10" max="100"
                                    placeholder="eg. 5"
                                    value="<?php echo esc_attr( get_option('bronze_score_weight') ); ?>"></label>
                            <label for="" style="height:30px"><input type="hidden"></label>
                        </div>
                        <div class="range-cls">
                            <p>
                                <?php echo myst_staff_training_fetch_img_url('trophyx.png'); ?>
                            </p>
                            <label><input type="number" name="fail_score_min" min="0" max="100" placeholder="eg. 100"
                                    value="<?php echo esc_attr( get_option('fail_score_min') ); ?>"></label>
                            <label><input type="number" name="fail_score_weight" min="-10" max="100" placeholder="eg. 5"
                                    value="<?php echo esc_attr( get_option('fail_score_weight') ); ?>"></label>
                            <label for="" style="height:30px"><input type="hidden"></label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <p><label>Click the button to update all existing weight data</label></p>
                </th>
                <td>
                    <input type="button" value="Update Weight for all scores" name="update_weight" id="update_weight"
                        class="button">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <p><label>Click the button to update all existing points earned by users</label></p>
                </th>
                <td>
                    <input type="button" value="Update points for all users" name="update_points" id="update_points"
                        class="button">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <p><label>Check this checkbox to set the timer on "Take quiz" button</label></p>
                </th>
                <td>
                    <input type="checkbox" value="y" name="if_timer" id="if_timer" <?php echo (esc_attr(
                        get_option('if_timer') )=='y' ) ? 'checked' :''; ?>>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <p><label>Leaderboard Leagues</label></p>
                </th>
                <td>
                    <input type="checkbox" value="y" name="if_leagues" id="if_leagues" <?php echo (esc_attr(
                        get_option('if_leagues') )=='y' ) ? 'checked' :''; ?>>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <p><label>Allow Self Assign</label></p>
                </th>
                <td>
                    <input type="checkbox" value="y" name="if_self_assign" id="if_self_assign" <?php echo (esc_attr(
                        get_option('if_self_assign') )=='y' ) ? 'checked' :''; ?>>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <p><label>Self Assign wait days</label></p>
                </th>
                <td>
                    <input type="number" name="self_assign_wait_days" min="0" max="" placeholder="eg. 7"
                        value="<?php echo esc_attr( get_option('self_assign_wait_days') ); ?>">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <p><label>Select score for leagues</label> </p>
                    <p class="trp-scr"><small>Minimum score</small></p>
                </th>
                <td>
                    <div class="trophydata">
                        <div class="range-cls">
                            <p style="margin-left:10px">League 1</p>
                            <label><input type="number" name="league1_score_min" min="0" max="100" placeholder="eg. 100"
                                    value="<?php echo esc_attr( get_option('league1_score_min') ); ?>"></label>
                        </div>
                        <div class="range-cls">
                            <p style="margin-left:10px">League 2</p>
                            <label><input type="number" name="league2_score_min" min="0" max="100" placeholder="eg. 100"
                                    value="<?php echo esc_attr( get_option('league2_score_min') ); ?>"></label>
                        </div>
                        <div class="range-cls">
                            <p style="margin-left:10px">League 3</p>
                            <label><input type="number" name="league3_score_min" min="0" max="100" placeholder="eg. 100"
                                    value="<?php echo esc_attr( get_option('league3_score_min') ); ?>"></label>
                        </div>
                    </div>
                </td>

            </tr>
            <tr valign="top">
                <th scope="row">
                    <p class="trp-scr">League Titles</p>
                </th>
                <td>
                    <div class="trophydata">
                        <div class="range-cls">
                            <label><input type="text" name="league1_title" placeholder="Lower League"
                                    value="<?php echo esc_attr( get_option('league1_title') ); ?>"></label>
                        </div>
                        <div class="range-cls">
                            <label><input type="text" name="league2_title" placeholder="Middle League"
                                    value="<?php echo esc_attr( get_option('league2_title') ); ?>"></label>
                        </div>
                        <div class="range-cls">
                            <label><input type="text" name="league3_title" placeholder="Big League"
                                    value="<?php echo esc_attr( get_option('league3_title') ); ?>"></label>
                        </div>
                    </div>
                </td>
            </tr>


            <tr valign="top">
                <th scope="row">
                    <p><label>Add time in seconds for timer</label></p>
                </th>
                <td>
                    <input type="number" name="quiz_timer" min="0" max="" placeholder="eg. 60"
                        value="<?php echo esc_attr( get_option('quiz_timer') ); ?>">

                </td>
            </tr>




            <tr valign="top">
                <th scope="row">
                    <p><label>Upload custom logo here</label></p>
                </th>
                <td>

                    <?php
                        $image_id = esc_attr (get_option( 'atls_img' ));
                    
                        if( $image = wp_get_attachment_image_url( $image_id, 'medium' ) ) : ?>
                    <a href="#" class="atls-upload">
                        <img src="<?php echo esc_url($image )?>"  />
                    </a>
                    <a href="#" class="atls-remove">Remove image</a>
                    <input type="hidden" name="atls_img" value="<?php echo absint( $image_id ) ?>">
                    <?php else : ?>
                    <a href="#" class="button atls-upload">Upload image</a>
                    <a href="#" class="atls-remove" style="display:none">Remove image</a>
                    <input type="hidden" name="atls_img" value="">
                    <?php endif; ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <p><label>Global Color</label> <small>(This will change the color in the whole website including
                            text and button colors)</small></p>
                </th>
                <td>
                    <input type="color" name="global_color"
                        value="<?php echo esc_attr( get_option('global_color') ); ?>"
                        style="cursor:pointer;"
                        >

                </td>
            </tr>
        </table>

        <?php submit_button(); ?>

    </form>
</div>
<script>
    jQuery('.add-new-msg').on('click', function (e) {

        e.preventDefault();

        var numItems = jQuery('.message-after').length;

        numItems = numItems + 1;

        var fields = '<div><textarea rows="3" cols="100" name="scnd_cmpltd_message[]" class="message-after"></textarea><a href="javascript:void(0)" onclick="deletethis(jQuery(this))">X</a></div>';

        jQuery('.msgs-wrap').append(fields);

        jQuery('.msgs-wrap a').show();

    });

    function deletethis(e) {

        jQuery(e).parent().remove();

        var numItems = jQuery('.message-after').length;

        if (numItems == 1) {

            jQuery('.msgs-wrap a').hide();

        } else {

            jQuery('.msgs-wrap a').show();

        }

    }

    jQuery('body').on('click', '.atls-upload', function (event) {
        event.preventDefault(); // prevent default link click and page refresh

        const button = jQuery(this)
        const imageId = button.next().next().val();

        const customUploader = wp.media({
            title: 'Insert image', // modal window title
            library: {
                // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                type: 'image'
            },
            button: {
                text: 'Use this image' // button label text
            },
            multiple: false
        }).on('select', function () { // it also has "open" and "close" events
            const attachment = customUploader.state().get('selection').first().toJSON();
            button.removeClass('button').html('<img src="' + attachment.url + '">'); // add image instead of "Upload Image"
            button.next().show(); // show "Remove image" link
            button.next().next().val(attachment.id); // Populate the hidden field with image ID
        })

        // already selected images
        customUploader.on('open', function () {

            if (imageId) {
                const selection = customUploader.state().get('selection')
                attachment = wp.media.attachment(imageId);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            }

        })

        customUploader.open()

    });
    // on remove button click
    jQuery('body').on('click', '.atls-remove', function (event) {
        event.preventDefault();
        const button = jQuery(this);
        button.next().val(''); // emptying the hidden field
        button.hide().prev().addClass('button').html('Upload image'); // replace the image with text
    });
</script>
<?php 
}



function myst_get_all_users($data = NULL){

    $users = get_users( );

    $user_ids = json_decode(stripslashes($data->assigned_users));
    $secid = isset($_GET['section_id']) ? sanitize_text_field($_GET['section_id']) : '';

    foreach($users as $user){

        $user_info = get_userdata($user->ID);?>

<li>
    <div class="section-title user-name">
        <?php 
                if (!empty($user_ids) && in_array($user->ID, $user_ids)){ 
                    echo myst_staff_training_fetch_quiz_by_u_s($secid, $user->ID); 
                }
                ?>
        <?php echo  esc_html($user_info->display_name); ?>
    </div>

    <div class="section-action-btn">

        <!-- Default checked -->

        <div class="custom-control custom-switch">

            <input type="checkbox" class="custom-control-input"
                id="<?php echo $user_info->user_nicename.'_'.$user->ID; ?>" data-id="<?php echo $user->ID; ?>" <?php
                if(!empty($user_ids) && in_array($user->ID, $user_ids)){ echo 'checked';}?> >

            <label class="custom-control-label" for="<?php echo $user_info->user_nicename.'_'.$user->ID; ?>"></label>

        </div>

        <span class="complete-per">


            <?php if(!empty($user_ids) && in_array($user->ID, $user_ids)){

                        $learning_modules = get_user_meta( $user->ID , 'learning_modules_progress', true);

                        $learning_modules = unserialize($learning_modules);

                        if(!empty($learning_modules[$data->id]['pages'])){

                            $total_steps = 0;

                            $total_completed_step = 0;

                            foreach ($learning_modules[$data->id]['pages'] as $key => $learning_subsec) {

                                $complated_class = '';

                                if($learning_subsec['status'] == 'completed'){

                                    $total_completed_step++;

                                }

                                $total_steps++;

                            }



                            $pro_percentage = 0;

                            if($total_completed_step > 0){

                                $pro_percentage = 100 / $total_steps;

                                $pro_percentage = $pro_percentage * $total_completed_step;

                            }

                            echo esc_html(round((int)$pro_percentage)) . '%';

                        } else {

                            echo '';

                        }

                    }

                    ?>

        </span>

        <span>
            <?php 
                    if(!empty($user_ids) && in_array($user->ID, $user_ids)) {
                        ?>
            <a href="javascript:void(0);" class="notify-user" data-userid="<?php echo $user->ID; ?>">
                <img src="<?php echo st_my_plugin_dir_folder?>/images/email_64.png" alt="img" />
            </a>
            <?php
                    }
                    ?>
        </span>

    </div>

</li>

<?php }

}



function myst_staff_training_get_learning_section(){

    global $wpdb;

    $table_name = $wpdb->prefix . 'learning_sections';

    $learning_sections = $wpdb->get_results(
       
            "SELECT * FROM {$wpdb->prefix}learning_sections WHERE is_trash = 0 ORDER BY sort_order ASC"
            
        
    );

    return $learning_sections;

}
function myst_staff_training_get_learning_section_archived(){

    global $wpdb;

    $table_name = $wpdb->prefix . 'learning_sections';

    $learning_sections = $wpdb->get_results(
        
            "SELECT * FROM {$wpdb->prefix}learning_sections WHERE is_trash = 1"
        
    );
    return $learning_sections;

}



function myst_staff_training_get_specific_section_by_id($section_id){

    global $wpdb;

    $table_name = $wpdb->prefix . 'learning_sections';

    $learning_details = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}learning_sections WHERE id=$section_id");
   

    return $learning_details;

}

//define cron job schedule
// add_action('init','activate_reminder_email_cron');
// function activate_reminder_email_cron(){
//  if (!wp_next_scheduled('myst_staff_training_send_reminder_emails')) {
//         wp_schedule_event(time(), 'every_48_hours', 'myst_staff_training_send_reminder_emails');
//     }
//  // if(get_current_user_id() == 51) {
//  //  myst_staff_training_send_reminder_emails();
//  // }
// }

// // Define the custom cron interval for every 48 hours
// function custom_cron_intervals($schedules) {
//     $schedules['every_48_hours'] = array(
//         'interval' => 172800, //48 * 60 * 60, // 48 hours in seconds
//         'display'  => __('Every 48 Hours', 'wpstaff_training'),
//     );
//     return $schedules;
// }
// add_filter('cron_schedules', 'custom_cron_intervals');

// Define the function to send reminder emails
function myst_staff_training_send_reminder_emails() {
    // Fetch the users you want to send reminders to
    $users_to_remind = get_users();
    ob_start();
    foreach ($users_to_remind as $user) {
        $sid_array = [];
        $userid = $user->ID;
        $useremail = $user->user_email;
        $username = $user->display_name;
        
        $lms = unserialize(get_user_meta($userid, 'learning_modules_progress',true));
        foreach($lms as $lid => $lval){
            if((isset($lval['is_all_complated']) && $lval['is_all_complated'] != 1) && isset($lval['active']) && $lval['active'] == 1){
                $sid_array[] = $lid;
            }
        }
        //check if uncompleted courses are more than 1, then pick the random section
        if(count($sid_array) > 1) {
            
            $randomvalue = array_rand($sid_array,1);
            $section_id = $sid_array[$randomvalue];
        }else{
            
            $section_id = $sid_array[0];
        }

        $value = $lms[$section_id];
        
        
        // locate_template('/templates/email-template.php',true,true,array('val' => $value, 'userid' => $userid, 'username' => $username,'email' => $useremail));

        

global $wpdb;

$value =$value;
$userid =  $userid;
$username =  $username;
$useremail =  $useremail;
$score_arr = $score_quizarr = array();
$quiztable = $wpdb->prefix.'quiz_details';
$quiz_user = $wpdb->prefix.'quiz_user_details';

$score_sql = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT quizid, score FROM {$wpdb->prefix}quiz_user_details WHERE userid = %d AND quizid IN (SELECT quizid FROM {$wpdb->prefix}quiz_details WHERE sectionid = %d)",
        $userid,
        $value['id']
    ),
    ARRAY_A
);

if(!empty($score_sql)) :
    foreach($score_sql as $row) {
        
        $score_data = json_decode($row['score'],true);
        $score_arr[] = $score_data['percentage'];
        $score_sql_row = $wpdb->get_row("SELECT subsection_title from {$wpdb->prefix}quiz_details where quizid = {$row['quizid']}",ARRAY_A);
     
        $subsectitle = explode("_",$score_sql_row['subsection_title'])[1];
        $score_quizarr[$subsectitle] = $score_data['percentage'];
    }
    
    if(!empty($score_arr)) :
        $avg = array_sum($score_arr) / count($score_arr);
    endif;

    $quiz_score = round($avg);

    if($avg != ''){
        if($quiz_score == get_option('gold_score_min')):
            $cls = 'trophygold.png';
        elseif($quiz_score >= get_option('silver_score_min') && $quiz_score <= (get_option('gold_score_min') - 1)):
            $cls = 'trophysilver.png';
        elseif($quiz_score >= get_option('bronze_score_min') && $quiz_score <= (get_option('silver_score_min') - 1)):
            $cls = 'trophybronze.png';
        elseif($quiz_score >= get_option('fail_score_min') && $quiz_score <= (get_option('bronze_score_min') - 1)):
            $cls = 'trophyx.png';
        endif;
        $average = '<img src="'.st_my_plugin_dir_folder.'/images/'.$cls.'" style="width:25px;" />';
    }else{
        $average = '';
    }
endif;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
    <title>EmailTemp</title>

    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
        body {
            font-family: 'Roboto', sans-serif !important;
            height: 100% !important;
            width: 100% !important;
            -ms-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            border: none;
        }

        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        img {
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
        }

        a {
            text-decoration: none;
        }

        p,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0;
        }

        a,
        a:link,
        a:visited {
            text-decoration: none;
            color: #00788a;
        }

        a:hover {
            text-decoration: none;
        }

        h2,
        h2 a,
        h2 a:visited,
        h3,
        h3 a,
        h3 a:visited,
        h4,
        h5,
        h6,
        .t_cht {
            color: #000 !important;
        }

        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td {
            line-height: 100%;
        }
    </style>
</head>
<body>
    <table class="table-pdf" width="100%" cellpadding="0" cellspacing="0" align="center"
        style="border:1px solid #eee;max-width:600px;margin:auto;font-family: 'Roboto', sans-serif !important;">
        <tbody>
            <tr>
                <td>
                    <!-- logo starts here  -->
                    <table width="100%" cellpadding="0" cellspacing="0" align="center" bgcolor="#fff" style="font-family: 'Roboto', sans-serif !important;">
                        <tbody>
                            <tr>
                                <td style="padding:15px 10px;" align="center">
                                    <?php myst_staff_training_get_custom_logo_function(); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- logo ends here  -->
                    <!-- content starts here  -->
                    <table width="100%" cellpadding="0" cellspacing="0" align="center">
                        <tbody>
                            <tr>
                                <td style="padding: 40px 30px;" bgcolor="#f9f7fa">
                                    <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                        <tbody>
                                            <tr>
                                                <td style="color: #757376;">
                                                    <h1 style="margin-bottom: 15px;">Hey <?php echo esc_html($username); ?>,</h1>
                                                    <p style="margin-bottom: 15px;font-size: 20px;">You have a new course ready to complete</p>
                                                    <p style="text-align:center;margin-bottom: 15px;">
                                                        <a href="" style="color: #14a59c;display: inline-block;font-weight:300;font-size:22px;">Click the start button to begin</a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- how we work parent table block starts here  -->
                                    <table width="100%" cellpadding="0" cellspacing="0" align="center"
                                        style="margin-top: 10px;">
                                        <tbody>
                                            <tr>
                                                <td bgcolor="#fff" style="padding:40px 30px;border-radius:8px;">
                                                    <!-- how we work table starts  -->
                                                    <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                                        <tr>
                                                            <td width="25%" align="left">
                                                                
                                                                <span style="display: inline-block; vertical-align: middle; width: 100px; height: 100px; border-radius: 50%; background-color: #e6f7ff; text-align: center; line-height: 100px; padding: 10px;">
                                                                    <?php if (!empty($value['image_icon'])) : ?>
                                                                        <img src="<?php echo esc_url($value['image_icon']); ?>" alt="learning icon" style="max-width: 100%; max-height: 100%; border-radius: 50%;">
                                                                    <?php endif; ?>
                                                                </span>

                                                            </td>
                                                            <td width="75%" align="left" style="padding-left:15px ;">
                                                                <h3 style="color:#000;margin-bottom: 10px;"><?php echo esc_html($value['title']); ?> </h3>
                                                                <?php 
                                                                $count = 0;

                                                                    foreach ($value['pages'] as $key => $v) {
                                                                        if($v['status'] == 'completed' ){
                                                                            $sectionid = $value['id'];
                                                                            $count++;
                                                                        }
                                                                    }
                                                                    foreach ($value['pages'] as $key => $vv) {
                                                                        if($vv['status'] != 'completed' ){
                                                                            $link = $vv['sub_start_url'].'?step='.$vv['sub_title'];
                                                                            break;
                                                                        }
                                                                    }

                                                                    if($value['is_all_complated'] == 1){ ?>
                                                                       
                                                                        <a href="javascript:void(0)" style="display:inline-block;color:#5699e0;text-transform: uppercase;padding: 8px 30px;border: 1px solid #5699e0;font-size: 16px;">Completed</a>
                                                                        <br/>
                                                                        <a href="javascript:void(0)" class="redo-course" data-sid="<?php echo esc_attr($sectionid); ?>" style="display:inline-block;color:#5699e0;text-transform: uppercase;padding: 8px 30px;border: 1px solid #5699e0;font-size: 16px;margin-top:10px;">Redo Course</a>
                                                                    
                                                                    <?php } else if($count == 0){ ?>
                                                                        <a href="<?php echo esc_url($link); ?>" style="display:inline-block;color:#5699e0;text-transform: uppercase;padding: 8px 30px;border: 1px solid #5699e0;font-size: 16px;">Start</a>
                                                                        
                                                                    <?php } else{ ?>
                                                                        <a href="<?php echo esc_url($link); ?>" style="display:inline-block;color:#5699e0;text-transform: uppercase;padding: 8px 30px;border: 1px solid #5699e0;font-size: 16px;">Continue</a>
                                                                    <?php } ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <!-- how we work table ends  -->

                                                    <!-- list table starts  -->
                                                    <?php if(!empty($value['pages'])){ ?>
                                                    <table width="100%" cellpadding="0" cellspacing="0" align="center"
                                                        style="margin-top:70px; margin-bottom: 30px;">
                                                        <tr>
                                                            <td style="padding-bottom: 70px;border-bottom: 1px solid #eee;">
                                                            <?php 
                                                                $total_steps = 0;
                                                                $total_completed_step = 0;
                                                                foreach ($value['pages'] as $key => $learning_subsec) { 
                                                                    ?>
                                                                    <table  width="100%" cellpadding="0" cellspacing="0" align="center">
                                                                        <tbody>
                                                                            <tr>
                                                                            <?php
                                                                            $complated_class = '';
                                                                            if($learning_subsec['status'] == 'completed'){
                                                                                $complated_class = 'completed';
                                                                                $total_completed_step++;
                                                                                $sis = round($score_quizarr[$key]); //$sis = section individual score
                                                                                
                                                                                if($sis == '100') {
                                                                                    $complated_class = ' green-check';
                                                                                    ?>
                                                                                    <td width="50%" align="left" valign="middle" style="padding-bottom: 20px;">
                                                                                        <p style="font-size:18px;font-weight:600;">
                                                                                            <a href="<?php echo $learning_subsec['sub_start_url'].'?step='.urlencode($learning_subsec['sub_title']); ?>" style="color:#757376;"><?php echo $learning_subsec['sub_title']; ?></a>
                                                                                        </p>
                                                                                    </td>
                                                                                    <td width="50%" align="right" valign="middle" style="padding-bottom: 20px;">
                                                                                        <img src="<?php echo esc_url(site_url()); ?>/wp-content/uploads/2022/02/check-3-1.png" style="height:20px;width:20px;"/>
                                                                                    </td>
                                                                                    <?php
                                                                        
                                                                                }elseif($sis < '100' || $sis == ''){
                                                                                    $complated_class = ' red-check';
                                                                                    ?>
                                                                                    <td width="50%" align="left" valign="middle" style="padding-bottom: 20px;">
                                                                                        <p style="font-size:18px;font-weight:600;">
                                                                                            <a href="<?php echo $learning_subsec['sub_start_url'].'?step='.urlencode($learning_subsec['sub_title']); ?>" style="color:#757376;"><?php echo $learning_subsec['sub_title']; ?></a>
                                                                                        </p>
                                                                                    </td>
                                                                                    <td width="50%" align="right" valign="middle" style="padding-bottom: 20px;">
                                                                                        <img src="<?php echo esc_url(site_url()); ?>/wp-content/uploads/2022/10/check-red.png" style="height:20px;width:20px;"/>
                                                                                    </td>
                                                                                    <?php
                                                                                }  

                                                                            }else{
                                                                                $complated_class = '';
                                                                                ?>
                                                                                <td width="50%" align="left" valign="middle" style="padding-bottom: 20px;">
                                                                                    <p style="font-size:18px;font-weight:600;">
                                                                                        <a href="<?php echo $learning_subsec['sub_start_url'].'?step='.urlencode($learning_subsec['sub_title']); ?>" style="color:#757376;"><?php echo $learning_subsec['sub_title']; ?></a>
                                                                                    </p>
                                                                                </td>
                                                                                <td width="50%" align="right" valign="middle" style="padding-bottom: 20px;">
                                                                                    
                                                                                </td>
                                                                                <?php
                                                                                
                                                                            }
                                                                            ?>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                <?php
                                                                } ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <?php 
                                                    } 
                                                    ?>
                                                    <!-- list table ends  -->
                                                    <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                                        <tr>
                                                            <td width="50%" align="left" style="padding-bottom:10px;">
                                                                <h3 style="color: #000;">
                                                                    <?php 
                                                                    if($average == '') {
                                                                        echo 'My Progress';
                                                                    }else{
                                                                        echo 'My Score';
                                                                    }
                                                                    ?>
                                                                </h3>
                                                            </td>
                                                            <td width="50%" align="right" style="padding-bottom:10px;" valign="middle">
                                                                <p style="color: #757376;" valign="middle">
                                                                    <?php 
                                                                    if($average == '') {
                                                                        $pro_percentage = 0;
                                                                        if($total_completed_step > 0){
                                                                            $pro_percentage = 100 / $total_steps;
                                                                            $pro_percentage = $pro_percentage * $total_completed_step;
                                                                        }
                                                                        echo '<span>'.round($pro_percentage).'%</span>';
                                                                        $final_per = round($pro_percentage);
                                                                    }else{

                                                                        echo '<span>'.$quiz_score.'%</span>';
                                                                        echo esc_html($average);
                                                                        $final_per = $quiz_score;
                                                                    }
                                                                    ?>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2">
                                                                <div class="pro-percentage-bar" style="height: 10px;border-radius: 10px;background: #d2d1d3;margin-top: 0px;position: sticky;z-index: 0;">
                                                                    <div class="current-progress" style="background: #4d9ffc;content: '';display: block;height: 10px;border-radius: 10px;z-index: 1;width: <?php echo esc_attr($final_per); ?>%;"></div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- how we work parent table block ends here  -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- content ends here  -->

                    <!-- green content stripe starts here  -->
                    <table width="100%" cellpadding="0" cellspacing="0" align="center" bgcolor="#00b832">
                        <tbody>
                            <tr>
                                <td style="padding: 10px; font-size: 14px;">
                                    <table width="80%" cellpadding="0" cellspacing="0" align="center">
                                        <tr>
                                            <td width="28%" align="left" style="text-align:center;">
                                                <a href="" style="color: #fff;font-weight: 300;">
                                                    <b>Login Details</b>
                                                </a>
                                            </td>
                                            <td width="40%" align="left" style="text-align:center;">
                                                <a href="" style="color: #fff;font-weight: 300;">Username : <?php echo esc_html($username); ?></a>
                                            </td>
                                            <td width="32%" align="left" style="text-align:center;">
                                                <a href="" style="color: #fff;font-weight: 300;">Password : ******</a>
                                            </td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <!-- green content stripe ends here  -->
                    <table width="100%" align="center" cellpadding="0" cellspacing="0">
                        <tbody> 
                            <tr>
                                <td style="background-image: url(<?php echo esc_url(site_url()); ?>/wp-content/uploads/2022/11/email-footer-banner.png);background-repeat:no-repeat;background-size: 100%;background-position:left;">
                                    <!-- seperatorder table starts here  -->
                                    <table width="100%" cellpadding="0" cellspacing="0" align="center" bgcolor="">
                                        <tbody>
                                            <tr>
                                                <td colspan="3" height="55"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- seperatorder table ends here  -->
                
                                    <!-- footer table starts here -->
                                    <table width="100%" cellpadding="0" cellspacing="0" align="center" bgcolor="">
                                        <tbody>
                                            <tr>
                                                <td height="100">
                                                    
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- footer table ends here -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
<?php
        $mail_html .= ob_get_clean();
        $serverHostname = $_SERVER['HTTP_HOST'];
$currentDomain = preg_replace('/:\d+$/', '', $serverHostname);
            $headers = array('Content-Type: text/html; charset=UTF-8','From:  <noreply@' . $currentDomain . '>');
        $to = $useremail;
        $subject = 'New Course ready to complete';
        
        $sent = wp_mail($to,$subject,$mail_html,$headers);
        
    }
}
/**
 * Send email to user from admin panel
 * Admin can send user email for completing new section which is assigned to user
 */
add_action('wp_ajax_notify_user_with_email','myst_staff_training_notify_user_with_email_callback');
add_action('wp_ajax_nopriv_notify_user_with_email','myst_staff_training_notify_user_with_email_callback');
function myst_staff_training_notify_user_with_email_callback(){
    global $wpdb;
    $mail_body = '';
    $userid = sanitize_text_field($_POST['user_id']);
    $sectionid = sanitize_text_field($_POST['section_id']);

    $userdata = get_userdata($userid);
    $useremail = $userdata->user_email;
    $username = $userdata->display_name;
    
    $learning_modules_section = unserialize(get_user_meta($userid, 'learning_modules_progress',true));
    $value = $learning_modules_section[$sectionid];
    
    ob_start();
    // locate_template('/templates/email-template.php',true,true,array('val' => $value, 'userid' => $userid, 'username' => $username,'email' => $useremail));

       
   
    global $wpdb;
    
    $value =$value;
    $userid =  $userid;
    $username =  $username;
    $useremail =  $useremail;
    $score_arr = $score_quizarr = array();
    $quiztable = $wpdb->prefix.'quiz_details';
    $quiz_user = $wpdb->prefix.'quiz_user_details';
    $score_sql = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT quizid, score FROM {$wpdb->prefix}quiz_user_details WHERE userid = %d AND quizid IN (SELECT quizid FROM {$wpdb->prefix}quiz_details WHERE sectionid = %d)",
            $userid,
            $value['id']
        ),
        ARRAY_A
    );
    
    
    if(!empty($score_sql)) :
        foreach($score_sql as $row) {
            
            $score_data = json_decode($row['score'],true);
            $score_arr[] = $score_data['percentage'];
            $score_sql_row = $wpdb->get_row("SELECT subsection_title from {$wpdb->prefix}quiz_details where quizid = {$row['quizid']}",ARRAY_A);
           
            $subsectitle = explode("_",$score_sql_row['subsection_title'])[1];
            $score_quizarr[$subsectitle] = $score_data['percentage'];
        }
        
        if(!empty($score_arr)) :
            $avg = array_sum($score_arr) / count($score_arr);
        endif;
    
        $quiz_score = round($avg);
    
        if($avg != ''){
            if($quiz_score == get_option('gold_score_min')):
                $cls = 'trophygold.png';
            elseif($quiz_score >= get_option('silver_score_min') && $quiz_score <= (get_option('gold_score_min') - 1)):
                $cls = 'trophysilver.png';
            elseif($quiz_score >= get_option('bronze_score_min') && $quiz_score <= (get_option('silver_score_min') - 1)):
                $cls = 'trophybronze.png';
            elseif($quiz_score >= get_option('fail_score_min') && $quiz_score <= (get_option('bronze_score_min') - 1)):
                $cls = 'trophyx.png';
            endif;
            $average = '<img src="'.st_my_plugin_dir_folder.'/images/'.$cls.'" style="width:25px;" />';
        }else{
            $average = '';
        }
    endif;
    ?>
    
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    <html lang="en">
    
    <head>
        <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
        <title>EmailTemp</title>
    
        <style type="text/css">
            @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
            body {
                font-family: 'Roboto', sans-serif !important;
                height: 100% !important;
                width: 100% !important;
                -ms-text-size-adjust: 100%;
                margin: 0;
                padding: 0;
            }
    
            table {
                border-collapse: collapse;
                border: none;
            }
    
            table,
            td {
                mso-table-lspace: 0pt !important;
                mso-table-rspace: 0pt !important;
            }
    
            img {
                -ms-interpolation-mode: bicubic;
                max-width: 100%;
            }
    
            a {
                text-decoration: none;
            }
    
            p,
            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                margin: 0;
            }
    
            a,
            a:link,
            a:visited {
                text-decoration: none;
                color: #00788a;
            }
    
            a:hover {
                text-decoration: none;
            }
    
            h2,
            h2 a,
            h2 a:visited,
            h3,
            h3 a,
            h3 a:visited,
            h4,
            h5,
            h6,
            .t_cht {
                color: #000 !important;
            }
    
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td {
                line-height: 100%;
            }
        </style>
    </head>
    <body>
        <table class="table-pdf" width="100%" cellpadding="0" cellspacing="0" align="center"
            style="border:1px solid #eee;max-width:600px;margin:auto;font-family: 'Roboto', sans-serif !important;">
            <tbody>
                <tr>
                    <td>
                        <!-- logo starts here  -->
                        <table width="100%" cellpadding="0" cellspacing="0" align="center" bgcolor="#fff" style="font-family: 'Roboto', sans-serif !important;">
                            <tbody>
                                <tr>
                                    <td style="padding:15px 10px;" align="center">
                                        <?php myst_staff_training_get_custom_logo_function(); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- logo ends here  -->
                        <!-- content starts here  -->
                        <table width="100%" cellpadding="0" cellspacing="0" align="center">
                            <tbody>
                                <tr>
                                    <td style="padding: 40px 30px;" bgcolor="#f9f7fa">
                                        <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                            <tbody>
                                                <tr>
                                                    <td style="color: #757376;">
                                                        <h1 style="margin-bottom: 15px;">Hey <?php echo esc_html($username); ?>,</h1>
                                                        <p style="margin-bottom: 15px;font-size: 20px;">You have a new course ready to complete</p>
                                                        <p style="text-align:center;margin-bottom: 15px;">
                                                            <a href="" style="color: #14a59c;display: inline-block;font-weight:300;font-size:22px;">Click the start button to begin</a>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
    
                                        <!-- how we work parent table block starts here  -->
                                        <table width="100%" cellpadding="0" cellspacing="0" align="center"
                                            style="margin-top: 10px;">
                                            <tbody>
                                                <tr>
                                                    <td bgcolor="#fff" style="padding:40px 30px;border-radius:8px;">
                                                        <!-- how we work table starts  -->
                                                        <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                                            <tr>
                                                                <td width="25%" align="left">
                                                                    
                                                                    <span style="display: inline-block; vertical-align: middle; width: 100px; height: 100px; border-radius: 50%; background-color: #e6f7ff; text-align: center; line-height: 100px; padding: 10px;">
                                                                        <?php if (!empty($value['image_icon'])) : ?>
                                                                            <img src="<?php echo $value['image_icon']; ?>" alt="learning icon" style="max-width: 100%; max-height: 100%; border-radius: 50%;">
                                                                        <?php endif; ?>
                                                                    </span>
    
                                                                </td>
                                                                <td width="75%" align="left" style="padding-left:15px ;">
                                                                    <h3 style="color:#000;margin-bottom: 10px;"><?php echo $value['title']; ?> </h3>
                                                                    <?php 
                                                                    $count = 0;
    
                                                                        foreach ($value['pages'] as $key => $v) {
                                                                            if($v['status'] == 'completed' ){
                                                                                $sectionid = $value['id'];
                                                                                $count++;
                                                                            }
                                                                        }
                                                                        foreach ($value['pages'] as $key => $vv) {
                                                                            if($vv['status'] != 'completed' ){
                                                                                $link = $vv['sub_start_url'].'?step='.$vv['sub_title'];
                                                                                break;
                                                                            }
                                                                        }
    
                                                                        if($value['is_all_complated'] == 1){ ?>
                                                                           
                                                                            <a href="javascript:void(0)" style="display:inline-block;color:#5699e0;text-transform: uppercase;padding: 8px 30px;border: 1px solid #5699e0;font-size: 16px;">Completed</a>
                                                                            <br/>
                                                                            <a href="javascript:void(0)" class="redo-course" data-sid="<?php echo esc_attr($sectionid); ?>" style="display:inline-block;color:#5699e0;text-transform: uppercase;padding: 8px 30px;border: 1px solid #5699e0;font-size: 16px;margin-top:10px;">Redo Course</a>
                                                                        
                                                                        <?php } else if($count == 0){ ?>
                                                                            <a href="<?php echo esc_url($link); ?>" style="display:inline-block;color:#5699e0;text-transform: uppercase;padding: 8px 30px;border: 1px solid #5699e0;font-size: 16px;">Start</a>
                                                                            
                                                                        <?php } else{ ?>
                                                                            <a href="<?php echo esc_url($link); ?>" style="display:inline-block;color:#5699e0;text-transform: uppercase;padding: 8px 30px;border: 1px solid #5699e0;font-size: 16px;">Continue</a>
                                                                        <?php } ?>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <!-- how we work table ends  -->
    
                                                        <!-- list table starts  -->
                                                        <?php if(!empty($value['pages'])){ ?>
                                                        <table width="100%" cellpadding="0" cellspacing="0" align="center"
                                                            style="margin-top:70px; margin-bottom: 30px;">
                                                            <tr>
                                                                <td style="padding-bottom: 70px;border-bottom: 1px solid #eee;">
                                                                <?php 
                                                                    $total_steps = 0;
                                                                    $total_completed_step = 0;
                                                                    foreach ($value['pages'] as $key => $learning_subsec) { 
                                                                        ?>
                                                                        <table  width="100%" cellpadding="0" cellspacing="0" align="center">
                                                                            <tbody>
                                                                                <tr>
                                                                                <?php
                                                                                $complated_class = '';
                                                                                if($learning_subsec['status'] == 'completed'){
                                                                                    $complated_class = 'completed';
                                                                                    $total_completed_step++;
                                                                                    $sis = round($score_quizarr[$key]); //$sis = section individual score
                                                                                    
                                                                                    if($sis == '100') {
                                                                                        $complated_class = ' green-check';
                                                                                        ?>
                                                                                        <td width="50%" align="left" valign="middle" style="padding-bottom: 20px;">
                                                                                            <p style="font-size:18px;font-weight:600;">
                                                                                                <a href="<?php echo $learning_subsec['sub_start_url'].'?step='.urlencode($learning_subsec['sub_title']); ?>" style="color:#757376;"><?php echo $learning_subsec['sub_title']; ?></a>
                                                                                            </p>
                                                                                        </td>
                                                                                        <td width="50%" align="right" valign="middle" style="padding-bottom: 20px;">
                                                                                            <img src="<?php echo esc_url(site_url()); ?>/wp-content/uploads/2022/02/check-3-1.png" style="height:20px;width:20px;"/>
                                                                                        </td>
                                                                                        <?php
                                                                            
                                                                                    }elseif($sis < '100' || $sis == ''){
                                                                                        $complated_class = ' red-check';
                                                                                        ?>
                                                                                        <td width="50%" align="left" valign="middle" style="padding-bottom: 20px;">
                                                                                            <p style="font-size:18px;font-weight:600;">
                                                                                                <a href="<?php echo $learning_subsec['sub_start_url'].'?step='.urlencode($learning_subsec['sub_title']); ?>" style="color:#757376;"><?php echo $learning_subsec['sub_title']; ?></a>
                                                                                            </p>
                                                                                        </td>
                                                                                        <td width="50%" align="right" valign="middle" style="padding-bottom: 20px;">
                                                                                            <img src="<?php echo esc_url(site_url()); ?>/wp-content/uploads/2022/10/check-red.png" style="height:20px;width:20px;"/>
                                                                                        </td>
                                                                                        <?php
                                                                                    }  
    
                                                                                }else{
                                                                                    $complated_class = '';
                                                                                    ?>
                                                                                    <td width="50%" align="left" valign="middle" style="padding-bottom: 20px;">
                                                                                        <p style="font-size:18px;font-weight:600;">
                                                                                            <a href="<?php echo $learning_subsec['sub_start_url'].'?step='.urlencode($learning_subsec['sub_title']); ?>" style="color:#757376;"><?php echo $learning_subsec['sub_title']; ?></a>
                                                                                        </p>
                                                                                    </td>
                                                                                    <td width="50%" align="right" valign="middle" style="padding-bottom: 20px;">
                                                                                        
                                                                                    </td>
                                                                                    <?php
                                                                                    
                                                                                }
                                                                                ?>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    <?php
                                                                    } ?>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <?php 
                                                        } 
                                                        ?>
                                                        <!-- list table ends  -->
                                                        <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                                            <tr>
                                                                <td width="50%" align="left" style="padding-bottom:10px;">
                                                                    <h3 style="color: #000;">
                                                                        <?php 
                                                                        if($average == '') {
                                                                            echo 'My Progress';
                                                                        }else{
                                                                            echo 'My Score';
                                                                        }
                                                                        ?>
                                                                    </h3>
                                                                </td>
                                                                <td width="50%" align="right" style="padding-bottom:10px;" valign="middle">
                                                                    <p style="color: #757376;" valign="middle">
                                                                        <?php 
                                                                        if($average == '') {
                                                                            $pro_percentage = 0;
                                                                            if($total_completed_step > 0){
                                                                                $pro_percentage = 100 / $total_steps;
                                                                                $pro_percentage = $pro_percentage * $total_completed_step;
                                                                            }
                                                                            echo '<span>'.round($pro_percentage).'%</span>';
                                                                            $final_per = round($pro_percentage);
                                                                        }else{
    
                                                                            echo '<span>'.$quiz_score.'%</span>';
                                                                            echo esc_html($average);
                                                                            $final_per = $quiz_score;
                                                                        }
                                                                        ?>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <div class="pro-percentage-bar" style="height: 10px;border-radius: 10px;background: #d2d1d3;margin-top: 0px;position: sticky;z-index: 0;">
                                                                        <div class="current-progress" style="background: #4d9ffc;content: '';display: block;height: 10px;border-radius: 10px;z-index: 1;width: <?php echo esc_attr($final_per); ?>%;"></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </table>
    
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!-- how we work parent table block ends here  -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- content ends here  -->
    
                        <!-- green content stripe starts here  -->
                        <table width="100%" cellpadding="0" cellspacing="0" align="center" bgcolor="#00b832">
                            <tbody>
                                <tr>
                                    <td style="padding: 10px; font-size: 14px;">
                                        <table width="80%" cellpadding="0" cellspacing="0" align="center">
                                            <tr>
                                                <td width="28%" align="left" style="text-align:center;">
                                                    <a href="" style="color: #fff;font-weight: 300;">
                                                        <b>Login Details</b>
                                                    </a>
                                                </td>
                                                <td width="40%" align="left" style="text-align:center;">
                                                    <a href="" style="color: #fff;font-weight: 300;">Username : <?php echo esc_html($username); ?></a>
                                                </td>
                                                <td width="32%" align="left" style="text-align:center;">
                                                    <a href="" style="color: #fff;font-weight: 300;">Password : ******</a>
                                                </td>
                                            </tr>
    
                                        </table>
                                    </td>
                                </tr>
    
                            </tbody>
                        </table>
                        <!-- green content stripe ends here  -->
                        <table width="100%" align="center" cellpadding="0" cellspacing="0">
                            <tbody> 
                                <tr>
                                    <td style="background-image: url(<?php echo esc_url(site_url()); ?>/wp-content/uploads/2022/11/email-footer-banner.png);background-repeat:no-repeat;background-size: 100%;background-position:left;">
                                        <!-- seperatorder table starts here  -->
                                        <table width="100%" cellpadding="0" cellspacing="0" align="center" bgcolor="">
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" height="55"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!-- seperatorder table ends here  -->
                    
                                        <!-- footer table starts here -->
                                        <table width="100%" cellpadding="0" cellspacing="0" align="center" bgcolor="">
                                            <tbody>
                                                <tr>
                                                    <td height="100">
                                                        
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!-- footer table ends here -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
    
    </html>
    <?php
    $mail_html .= ob_get_clean();

    $serverHostname = $_SERVER['HTTP_HOST'];
    $currentDomain = preg_replace('/:\d+$/', '', $serverHostname);
    $headers = array('Content-Type: text/html; charset=UTF-8','From:  <noreply@' . $currentDomain . '>');
    $to = $useremail;
    $subject = 'New Course ready to complete';
    
    $sent = wp_mail($to,$subject,$mail_html,$headers);
    //echo $mail_html;
    // echo 'success';
    die;
}

//sort learning sections
add_action('wp_ajax_myst_staff_training_sort_learning_section','myst_staff_training_sort_learning_section');
add_action('wp_ajax_nopriv_myst_staff_training_sort_learning_section','myst_staff_training_sort_learning_section');
function myst_staff_training_sort_learning_section(){
    global $wpdb;
    $orders = sanitize_text_field($_POST['sortingorder']);
    
    $ls_table = $wpdb->prefix.'learning_sections';
    foreach($orders as $id => $orderval){
        $lid = explode("=", $orderval)[0];
        $sortnumber = explode("=", $orderval)[1];
        $orderupdate = $wpdb->update($ls_table,array('sort_order' => $sortnumber),array('id' => $lid));
    }
    echo 'success';
    die;
}

/**
 * Save quiz data
 * question answer - insert and update
 */
add_action('wp_ajax_myst_staff_training_save_question_answers_module','myst_staff_training_save_question_answers_module');
add_action('wp_ajax_nopriv_myst_staff_training_save_question_answers_module','myst_staff_training_save_question_answers_module');
function myst_staff_training_save_question_answers_module(){
    global $wpdb;

    $table_name = $wpdb->prefix . 'quiz_details';
    $sectionid = sanitize_text_field($_POST['sectionid']);
    $title = $sectionid."_".$_POST['subtitle'];
    $questitle = stripslashes($_POST['questitle']);
    $answertitle = $_POST['answertitle'];
    $correctanswer =$_POST['correctanswer'];
    $quesid = explode("_",$_POST['quesid'])[2];
    $quizid = sanitize_text_field($_POST['quizid']);

    

    if($quizid == ''){

        $i=0;
        $ansarray = array();
        $caarray = array();
        foreach($answertitle as $ansname){
            if($i <= count($answertitle)){
                $option = explode("_",$answertitle[$i]['id'])[2];
                //$ansarray[$option] = $answertitle[$i]['val'];
                $ansarray[$option] = stripslashes($answertitle[$i]['val']);
            }
            $i++;
        }

        foreach($correctanswer as $ca){
            $caarray[] = explode("_",$ca)[2];
        }
        $questionslist = array(
            $quesid => array(
                'Question' => $questitle,
                'Answers' => $ansarray,
                'CorrectAnswer' => $caarray,
            ),
        );

        $insert = $wpdb->insert($table_name,
            array(
                'sectionid' => $sectionid,
                'subsection_title' => $title,
                'question_list' => json_encode($questionslist),
            ),
        );
        if($insert) {
            $response['code'] = 'new_quiz';
            $response['message'] = 'Successfully Created quiz';
            $response['quizid'] = $wpdb->insert_id;
        }
    }else if($quizid != ''){

        $i=0;
        $selectedquiz = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}quiz_details WHERE quizid = {$quizid}", ARRAY_A);
      
        //print_r($selectedquiz['question_list']);
        $questions_arr = json_decode($selectedquiz['question_list'],true);

        $ansarray = array();
        $caarray = array();
        foreach($answertitle as $ansname){
            if($i <= count($answertitle)){
                $option = explode("_",$answertitle[$i]['id'])[2];
                $ansarray[$option] = stripslashes($answertitle[$i]['val']);
            }
            $i++;
        }
        foreach($correctanswer as $ca){
            $caarray[] = explode("_",$ca)[2];
        }

        //if 2 number question is exists then update in array.
        if(isset($questions_arr[$quesid])){

            $questions_arr[$quesid]['Question'] = $questitle;
            $questions_arr[$quesid]['Answers'] = $ansarray;
            $questions_arr[$quesid]['CorrectAnswer'] = $caarray;


        }else if(empty($questions_arr[$quesid])){
            //if new number question doesn't exists then insert in array.
            $questionslist = array(
                'Question' => $questitle,
                'Answers' => $ansarray,
                'CorrectAnswer' => $caarray,
            );

            $questions_arr[$quesid] = $questionslist;
        }

        $update = $wpdb->update($table_name,
            array(
                'question_list' => json_encode($questions_arr)
            ),
            array('quizid' => $quizid,'subsection_title'=>$title)
        );
        if($update) {
            $response['code'] = 'update_quiz';
            $response['message'] = 'Quiz updated Successfully';
            $response['quizid'] = $quizid;
        }
    }else{
        $response['code'] = 'none';
    }

    echo json_encode($response);
    die;
}

//delete answer from quiz
add_action('wp_ajax_myst_staff_training_quiz_module_delete_answer_backend','myst_staff_training_quiz_module_delete_answer_backend');
add_action('wp_ajax_nopriv_myst_staff_training_quiz_module_delete_answer_backend','myst_staff_training_quiz_module_delete_answer_backend');
function myst_staff_training_quiz_module_delete_answer_backend(){
    global $wpdb;

    $table_name = $wpdb->prefix . 'quiz_details';

    $quesid = sanitize_text_field($_POST['quesid']);
    $answerid = sanitize_text_field($_POST['answerid']);
    $quizid = sanitize_text_field($_POST['quizid']);
    $sectionid = sanitize_text_field($_POST['sectionid']);
    $subsectiontitle = sanitize_text_field($_POST['subsectiontitle']);
    $title = $sectionid."_".$subsectiontitle;

    $res = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}quiz_details WHERE quizid={$quizid} AND subsection_title = '{$title}' ",ARRAY_A);

    
    $sql = json_decode($res['question_list'],true);
    if(in_array($answerid,$sql[$quesid]['CorrectAnswer'])){
        if (($key = array_search($answerid, $sql[$quesid]['CorrectAnswer'])) !== false) {
            unset($sql[$quesid]['CorrectAnswer'][$key]);
        }
    }
    if(isset($sql[$quesid]['Answers'][$answerid])){

        unset($sql[$quesid]['Answers'][$answerid]);
    }
    $update = $wpdb->update($table_name, array('question_list' => json_encode($sql)),array('quizid' => $quizid, 'subsection_title' => $title));
    if($update) {
        $response['code'] = 'delete_answer';
        $response['message'] = 'Answer Deleted Successfully';
        $response['quizid'] = $quizid;
    }

    echo json_encode($response);
    die;
}

//delete question from quiz
add_action('wp_ajax_myst_staff_training_quiz_module_delete_question_backend','myst_staff_training_quiz_module_delete_question_backend');
add_action('wp_ajax_nopriv_myst_staff_training_quiz_module_delete_question_backend','myst_staff_training_quiz_module_delete_question_backend');
function myst_staff_training_quiz_module_delete_question_backend(){
    global $wpdb;

    $table_name = $wpdb->prefix . 'quiz_details';

    $quesid = sanitize_text_field($_POST['quesid']);
    $quizid = sanitize_text_field($_POST['quizid']);
    $sectionid = sanitize_text_field($_POST['sectionid']);
    $subsectiontitle = sanitize_text_field($_POST['subsectiontitle']);
    $title = $sectionid."_".$subsectiontitle;

    $res = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}quiz_details WHERE quizid={$quizid} AND subsection_title = '{$title}' ",ARRAY_A);

    $sql = json_decode($res['question_list'],true);

    if(isset($sql[$quesid])){
        unset($sql[$quesid]);
    }

    if($quesid != 1) {
        array_splice($sql, $quesid); //shift array index and starts from 0
    }
    array_unshift($sql, ""); //adds blank value in 0 index and shifts others to down 1,2,3
    unset($sql[0]); //remove 0 index and make array starts from 1.

    $update = $wpdb->update($table_name, array('question_list' => json_encode($sql)),array('quizid' => $quizid, 'subsection_title' => $title));
    if($update) {
        $response['code'] = 'delete_question';
        $response['message'] = 'Question Deleted Successfully';
        $response['quizid'] = $quizid;
    }

    echo json_encode($response);
    die;
}

//delete quiz from section
add_action('wp_ajax_myst_staff_training_quiz_module_delete_quiz_backend','myst_staff_training_quiz_module_delete_quiz_backend');
add_action('wp_ajax_nopriv_myst_staff_training_quiz_module_delete_quiz_backend','myst_staff_training_quiz_module_delete_quiz_backend');
function myst_staff_training_quiz_module_delete_quiz_backend(){
    global $wpdb;

    $table_name = $wpdb->prefix . 'quiz_details';
    $quiz_table_name = $wpdb->prefix . 'quiz_user_details';
    $quiz_section_score = $wpdb->prefix . 'quiz_section_score';

    $quizid = sanitize_text_field($_POST['quizid']);

    $res = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}quiz_user_details WHERE quizid = %d",
            $quizid
        ),
        ARRAY_A
    );
    foreach($res as $row) {
        $delete = $wpdb->delete($quiz_table_name, array('id'=>$row['id']));

    }

    $getsectionid = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}quiz_details WHERE quizid = %d",
            $quizid
        ),
        ARRAY_A
    );
    foreach($getsectionid as $gr) {
        $scoreresult = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, sectionid FROM {$wpdb->prefix}quiz_section_score WHERE sectionid = %d",
                $gr['sectionid']
            ),
            ARRAY_A
        );
        foreach($scoreresult as $sr){
            $scoredelete = $wpdb->delete($quiz_section_score, array('sectionid'=> $sr['sectionid']));
        }
    }

    $quizdelete = $wpdb->delete($table_name, array('quizid'=>$quizid));
    if($quizdelete) {
        $response['code'] = 'delete_quiz';
        $response['message'] = 'Quiz was deleted';
        $response['quizid'] = $quizid;
    }

    echo json_encode($response);
    die;
}


/* SAVE the DATA */
function myst_learning_modules_save_action_backend() {

    global $wpdb;

    parse_str($_REQUEST['data'], $output);

    $section_title = $output['section_title'];
    
    $section_cat = $output['section_cat'];

    $section_icon_attachment = $output['section_icon_attachment'];

    $table_name = $wpdb->prefix . 'learning_sections';

    $subsection = array();



    foreach($output['sub_section_title'] as $key => $title){

        $subsection[$key]['sub_title'] =$title;

        $subsection[$key]['sub_start_url'] = $output['sub_section_start_url'][$key];

        $subsection[$key]['sub_completed_url'] = $output['sub_section_completed_url'][$key];

    }

    $insert_data = $wpdb->insert( $table_name , array( 'title' => $section_title, 'cat' => $section_cat, 'image' => $section_icon_attachment, 'learning_subsection' => serialize($subsection), 'assigned_users' => '', 'is_trash' => 0, 'sort_order' => 1 ));

    if($wpdb->last_error == '') {

        echo 'success';

    }

    die;



}

add_action( 'wp_ajax_myst_learning_modules_save_action_backend', 'myst_learning_modules_save_action_backend' );

add_action( 'wp_ajax_nopriv_myst_learning_modules_save_action_backend', 'myst_learning_modules_save_action_backend' );


/* EDIT the DATA */

function myst_staff_training_learning_modules_edit_action_backend() {

    global $wpdb;

    parse_str($_REQUEST['data'], $output);

    $section_title = $output['section_title'];
    
    $section_cat = $output['section_cat'];

    $section_icon_attachment = $output['section_icon_attachment'];

    $section_id = $output['section_id'];

    $table_name = $wpdb->prefix . 'learning_sections';

    $current_section = myst_staff_training_get_specific_section_by_id($section_id);

    $old_assigned_users = json_decode(stripslashes($current_section->assigned_users));

    $subsection = array();

    foreach($output['sub_section_title'] as $key => $title){

        $subsection[$key]['sub_title'] =$title;

        $subsection[$key]['sub_start_url'] = $output['sub_section_start_url'][$key];

        $subsection[$key]['sub_completed_url'] = $output['sub_section_completed_url'][$key];

    }



    $update_data = $wpdb->update(

        $table_name ,

        array(

            'title' => $section_title,
            
            'cat' => $section_cat,

            'image' => $section_icon_attachment,

            'learning_subsection' => serialize($subsection),

        ),

        array(

            'id' => $section_id

        )

    );



    if($wpdb->last_error == '') {

        if(!empty($old_assigned_users)){

            foreach ($old_assigned_users as $key_user => $value_user) {

                $learning_modules = get_user_meta( $value_user , 'learning_modules_progress', true);

                $learning_modules = unserialize($learning_modules);

                if(!empty($learning_modules)){

                    foreach($output['sub_section_title'] as $key => $title){

                        if (!array_key_exists($title,$learning_modules[$current_section->id]['pages'])){

                            $learning_modules[$current_section->id]['pages'][$title]['sub_title'] = $title;

                            $learning_modules[$current_section->id]['pages'][$title]['sub_start_url'] = $output['sub_section_start_url'][$key];

                            $learning_modules[$current_section->id]['pages'][$title]['sub_completed_url']= $output['sub_section_completed_url'][$key];

                            $learning_modules[$current_section->id]['pages'][$title]['status'] = '';

                            $learning_modules[$current_section->id]['is_all_complated'] = '';

                        }

                    }



                    $img = '';

                    if(!empty($section_icon_attachment)){

                        $image = wp_get_attachment_image_src($section_icon_attachment, 'full');

                        $img = $image[0];

                    }

                    $learning_modules[$current_section->id]['image_icon'] = $img;

                    update_user_meta($value_user, 'learning_modules_progress',serialize($learning_modules));

                }

            }

        }

        echo "updated";

    }



    die;

}

add_action( 'wp_ajax_myst_staff_training_learning_modules_edit_action_backend', 'myst_staff_training_learning_modules_edit_action_backend' );

add_action( 'wp_ajax_nopriv_myst_staff_training_learning_modules_edit_action_backend', 'myst_staff_training_learning_modules_edit_action_backend' );





/* DELETE the DATA */

function myst_staff_training_learning_modules_delete_action_backend() {

    global $wpdb;

    $section_id = sanitize_text_field($_POST['section_id']);

    $table_name = $wpdb->prefix . 'learning_sections';
    $quiz_tbl = $wpdb->prefix . 'quiz_details';
    $quiz_user_tbl = $wpdb->prefix . 'quiz_user_details';
    $quiz_section_score = $wpdb->prefix . 'quiz_section_score';

    $current_section = myst_staff_training_get_specific_section_by_id($section_id);

    $old_assigned_users = json_decode(stripslashes($current_section->assigned_users));

    $delete_data = $wpdb->delete( $table_name , array('id' => $section_id) );

    // $res = $wpdb->get_results("SELECT * FROM {$quiz_tbl} WHERE sectionid={$section_id} ",ARRAY_A);
    $res = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}quiz_details WHERE sectionid = %d",
            $section_id
        ),
        ARRAY_A
    );
    foreach($res as $row) {
        // $results = $wpdb->get_results("SELECT * FROM {$quiz_user_tbl} WHERE quizid={$row['quizid']} ",ARRAY_A);
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}quiz_user_details WHERE quizid = %d",
                $row['quizid']
            ),
            ARRAY_A
        );
        
        foreach($results as $r){
            $delete = $wpdb->delete($quiz_user_tbl, array('quizid'=>$r['quizid']));

        }
        $quizdelete = $wpdb->delete($quiz_tbl, array('quizid'=>$row['quizid']));
    }

    // $scoreresult = $wpdb->get_results("SELECT id,sectionid FROM $quiz_section_score WHERE sectionid={$section_id}",ARRAY_A);
    $scoreresult = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT id, sectionid FROM {$wpdb->prefix}quiz_section_score WHERE sectionid = %d",
            $section_id
        ),
        ARRAY_A
    );
    foreach($scoreresult as $sr){
        $scoredelete = $wpdb->delete($quiz_section_score, array('sectionid'=> $sr['sectionid']));
    }
    
    if($wpdb->last_error == '') {

        if(!empty($old_assigned_users)){

            foreach ($old_assigned_users as $key_user => $value_user) {

                $learning_modules = get_user_meta( $value_user , 'learning_modules_progress', true);

                $learning_modules = unserialize($learning_modules);

                if(!empty($learning_modules)){

                    unset($learning_modules[$section_id]);

                    update_user_meta($value_user, 'learning_modules_progress',serialize($learning_modules));

                }

            }

        }

        echo 'deleted';

    }

    die;

}

add_action( 'wp_ajax_myst_staff_training_learning_modules_delete_action_backend', 'myst_staff_training_learning_modules_delete_action_backend' );

add_action( 'wp_ajax_nopriv_myst_staff_training_learning_modules_delete_action_backend', 'myst_staff_training_learning_modules_delete_action_backend' );

/* Move section to trash*/

function myst_staff_training_learning_modules_trash_action_backend() {

    global $wpdb;

    $section_id = sanitize_text_field($_POST['section_id']);

    $table_name = $wpdb->prefix . 'learning_sections';

    $current_section = myst_staff_training_get_specific_section_by_id($section_id);

    $old_assigned_users = json_decode(stripslashes($current_section->assigned_users));

    $delete_data = $wpdb->update( $table_name , array('is_trash' => 1), array('id' => $section_id) ); //1 move to trash, 0 not moved to trash

    if($wpdb->last_error == '') {

        if(!empty($old_assigned_users)){

            foreach ($old_assigned_users as $key_user => $value_user) {

                $learning_modules = get_user_meta( $value_user , 'learning_modules_progress', true);

                $learning_modules = unserialize($learning_modules);

                if(!empty($learning_modules)){

                    $learning_modules[$current_section->id]['active'] = 0;

                    update_user_meta($value_user, 'learning_modules_progress',serialize($learning_modules));

                }

            }

        }

        echo 'deleted';

    }

    die;

}

add_action( 'wp_ajax_myst_staff_training_learning_modules_trash_action_backend', 'myst_staff_training_learning_modules_trash_action_backend' );

add_action( 'wp_ajax_nopriv_myst_staff_training_learning_modules_trash_action_backend', 'myst_staff_training_learning_modules_trash_action_backend' );

/* Revert the section from trash*/

function myst_staff_training_learning_modules_revert_action_backend() {

    global $wpdb;

    $section_id = sanitize_text_field($_POST['section_id']);

    $table_name = $wpdb->prefix . 'learning_sections';

    $current_section = myst_staff_training_get_specific_section_by_id($section_id);

    $old_assigned_users = json_decode(stripslashes($current_section->assigned_users));

    $delete_data = $wpdb->update( $table_name , array('is_trash' => 0), array('id' => $section_id) ); //1 move to trash, 0 removed from trash

    if($wpdb->last_error == '') {

        if(!empty($old_assigned_users)){

            foreach ($old_assigned_users as $key_user => $value_user) {

                $learning_modules = get_user_meta( $value_user , 'learning_modules_progress', true);

                $learning_modules = unserialize($learning_modules);

                if(!empty($learning_modules)){

                    $learning_modules[$current_section->id]['active'] = 1;

                    update_user_meta($value_user, 'learning_modules_progress',serialize($learning_modules));

                }

            }

        }

        echo 'reverted';

    }

    die;

}

add_action( 'wp_ajax_myst_staff_training_learning_modules_revert_action_backend', 'myst_staff_training_learning_modules_revert_action_backend' );

add_action( 'wp_ajax_nopriv_myst_staff_training_learning_modules_revert_action_backend', 'myst_staff_training_learning_modules_revert_action_backend' );

/* ASSIGN USER */

function myst_staff_training_learning_modules_assign_users() {


    global $wpdb;

    $section_id = sanitize_text_field($_POST['section_id']);

    $table_name = $wpdb->prefix . 'learning_sections';

    $current_section = myst_staff_training_get_specific_section_by_id($section_id);

    $old_assigned_users = json_decode(stripslashes($current_section->assigned_users));

    $assigned_users = json_decode(stripslashes($_POST['user_ids']));

    $addUserFlag = isset($_POST['add_user']) && $_POST['add_user'] == true;// farhan

    
    if($addUserFlag){
        // comment by kashif at 30/01/24
//         $merged_users = array_merge($old_assigned_users, $assigned_users);
              $merged_users = array_merge((array)$old_assigned_users, (array)$assigned_users);

        $assigned_users=$merged_users;
    }

    // recheck this
     if (WP_DEBUG === true) {
            $log_file = ABSPATH . 'wp-content/my-plugin-logs.log'; // Adjust the path as needed
            $timestamp = date("Y-m-d H:i:s");
            
            // Convert arrays to string using json_encode or print_r
            $log_message = $timestamp . ' - Old Assigned Users: ' . json_encode($old_assigned_users) . PHP_EOL;
            $log_message2 = $timestamp . ' - New Assigned Users: ' . json_encode($assigned_users) . PHP_EOL;
            $log_message3 = $timestamp . ' - All Users: ' . json_encode($merged_users) . PHP_EOL;
            $log_message4 = $timestamp . ' - JSON: ' . json_encode($_POST['user_ids']) . PHP_EOL;
    
            // Write to log file
            error_log($log_message, 3, $log_file);
            error_log($log_message2, 3, $log_file);
            error_log($log_message3, 3, $log_file);
            error_log($log_message4, 3, $log_file);
        }
        
    if(!empty($assigned_users)){

        if(!empty($old_assigned_users)) {
            $diff_array_user = array_diff($old_assigned_users, $assigned_users);
            
            if(!empty($diff_array_user)){

                foreach ($diff_array_user as $key => $users){
                    
                    $learning_modules = get_user_meta( $users , 'learning_modules_progress', true);
                    $getcoins = json_decode(get_user_meta( $users , 'redo_course_coins', true),true);
                    $lastpoints2 = json_decode(get_user_meta( $users , 'points_by_section', true),true);
                    
                    if(!empty($learning_modules)){

                        $learning_modules = unserialize($learning_modules);

                        $learning_modules[$current_section->id]['active'] = 0;

                        update_user_meta($users, 'learning_modules_progress',serialize($learning_modules));
                        
                    }
                    //remove the old points from user meta not from coin_wallet on unassigning the user
                    if(!empty($lastpoints2)) {
                            
                        unset($lastpoints2[$current_section->id]);
                        update_user_meta( $users, 'points_by_section', json_encode($lastpoints2) );
                    }

                    //remove the redo course from user meta on unassigning the user
                    if(!empty($getcoins)) {
                            
                        unset($getcoins[$current_section->id]);
                        if(empty($getcoins) || $getcoins == '[]') {
                            delete_user_meta($users,'redo_course_coins');
                        }else{
                            
                            update_user_meta($users,'redo_course_coins', json_encode($getcoins));
                        }
                    }
                }

            }
        }else{
            foreach ($assigned_users as $key => $users){
                
                $learning_modules = get_user_meta( $users , 'learning_modules_progress', true);
                $getcoins = json_decode(get_user_meta( $users , 'redo_course_coins', true),true);
                $lastpoints2 = json_decode(get_user_meta( $users , 'points_by_section', true),true);

                if(!empty($learning_modules)){

                    $learning_modules = unserialize($learning_modules);

                    $learning_modules[$current_section->id]['active'] = 0;

                    update_user_meta($users, 'learning_modules_progress',serialize($learning_modules));

                }
                //remove the old points from user meta not from coin_wallet on unassigning the user
                if(!empty($lastpoints2)) {
                            
                    unset($lastpoints2[$current_section->id]);
                    update_user_meta( $users, 'points_by_section', json_encode($lastpoints2) );
                }

                //remove the redo course from user meta on unassigning the user
                if(!empty($getcoins)) {
                        
                    unset($getcoins[$current_section->id]);
                    if(empty($getcoins) || $getcoins == '[]') {
                        delete_user_meta($users,'redo_course_coins');
                    }else{
                        
                        update_user_meta($users,'redo_course_coins', json_encode($getcoins));
                    }
                }

            }
        }

        foreach ($assigned_users as $key => $users) {

            $learning_modules = get_user_meta( $users , 'learning_modules_progress', true);

            if(empty($learning_modules)){

                $learning_modules = array();

                $learning_modules = myst_staff_training_update_learning_section_module($current_section, $learning_modules);

                update_user_meta($users, 'learning_modules_progress',serialize($learning_modules));

            } else{

                $learning_modules = unserialize($learning_modules);

                if(!array_key_exists($section_id , $learning_modules)){

                    $learning_modules = myst_staff_training_update_learning_section_module($current_section, $learning_modules);

                } else{

                    $learning_subsection = unserialize($current_section->learning_subsection);

                    foreach ($learning_subsection as $key => $value) {

                        $final_array[$value['sub_title']] = $value;

                    }

                    $checked_array = $learning_modules[$current_section->id]['pages'];

                    foreach ($checked_array as $ch_key => $ch_value) {

                        unset($checked_array[$ch_key]['status']);

                    }

                    if($checked_array !== $final_array){

                        unset($learning_modules[$current_section->id]);

                        $learning_modules = myst_staff_training_update_learning_section_module($current_section, $learning_modules);

                    }else{

                        $learning_modules[$current_section->id]['active'] = 1;

                    }

                }

                update_user_meta($users, 'learning_modules_progress',serialize($learning_modules));

            }

        }

    } else{

        if(!empty($old_assigned_users)){

            foreach ($old_assigned_users as $key => $users) {

                $learning_modules = get_user_meta( $users , 'learning_modules_progress', true);
                $getcoins = json_decode(get_user_meta( $users , 'redo_course_coins', true),true);
                $lastpoints2 = json_decode(get_user_meta( $users , 'points_by_section', true),true);

                if(!empty($learning_modules)){

                    $learning_modules = unserialize($learning_modules);

                    $learning_modules[$current_section->id]['active'] = 0;

                    update_user_meta($users, 'learning_modules_progress',serialize($learning_modules));

                }
                //remove the old points from user meta not from coin_wallet on unassigning the user
                if(!empty($lastpoints2)) {
                            
                    unset($lastpoints2[$current_section->id]);
                    update_user_meta( $users, 'points_by_section', json_encode($lastpoints2) );
                }

                //remove the redo course from user meta on unassigning the user
                if(!empty($getcoins)) {
                        
                    unset($getcoins[$current_section->id]);
                    if(empty($getcoins) || $getcoins == '[]') {
                        delete_user_meta($users,'redo_course_coins');
                    }else{
                        
                        update_user_meta($users,'redo_course_coins', json_encode($getcoins));
                    }
                }

            }

        }

    }

    if($addUserFlag){
         $assigned_data = $wpdb->update( $table_name , array( 'assigned_users' => json_encode($merged_users)  ), array( 'id' => $section_id ) );
         if(is_user_logged_in()){
            $user_id = get_current_user_id();
        }
                 $f=$wpdb->update($wpdb->users,array('self_assigned_date' => current_time('mysql', 1),),array('ID' => $user_id,));

    }else{
        $assigned_data = $wpdb->update( $table_name , array( 'assigned_users' => $_POST['user_ids']  ), array( 'id' => $section_id ) );
    }

    
    
    if($wpdb->last_error == '') {

        echo "assigned";

    }else{
        
         echo $wpdb->last_error;
    }
    

    exit;

}
add_action( 'wp_ajax_myst_staff_training_learning_modules_assign_users', 'myst_staff_training_learning_modules_assign_users' );

add_action( 'wp_ajax_nopriv_myst_staff_training_learning_modules_assign_users', 'myst_staff_training_learning_modules_assign_users' );









// Update user car to for self assign feature

function myst_staff_training_update_user_cat(){
    
    global $wpdb;
    

    $table_name = $wpdb->prefix . 'learning_sections';
    
    $cat_value = $_POST['cat_value'];
    $user_id = sanitize_text_field($_POST['user_id']);
    
    $wpdb->update(
        $table_name,
        array('cat' => $cat_value),
        array('id' => $user_id)
    ); 
    
    
    if($wpdb->last_error == '') {
        echo "assigned";
    }else{
         echo $wpdb->last_error;
    }


    exit;
    
}
add_action( 'wp_ajax_myst_staff_training_update_user_cat', 'myst_staff_training_update_user_cat' );






/* DELETE the DATA */

function myst_staff_training_check_section_title($title, $section_id = null) {

    global $wpdb;

    $table_name = $wpdb->prefix . 'learning_sections';

    $learning_details = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}learning_sections WHERE title= '$title'");
  
    

    myst_pr($learning_details);

}



// add_action('admin_init','myst_straff_training_update_table');

function myst_straff_training_update_table(){

    global $wpdb;

    $table_name = $wpdb->prefix . 'learning_sections';

    $sql = "ALTER TABLE $table_name ADD assigned_users text NULL";

    $wpdb->query($sql);

}



function myst_staff_training_update_learning_section_module($current_section, $learning_modules){

    $data = array();

    $learning_subsection = unserialize($current_section->learning_subsection);

    foreach ($learning_subsection as $key => $learn_sub) {

        $data[$learn_sub['sub_title']] = array(

            'sub_title' => $learn_sub['sub_title'],

            'sub_start_url' => $learn_sub['sub_start_url'],

            'sub_completed_url' => $learn_sub['sub_completed_url'],

            'status' => ''

        );

    }

    $img = '';

    if(!empty($current_section->image)){

        $image = wp_get_attachment_image_src($current_section->image, 'full');

        $img = $image[0];

    }

    $learning_modules[$current_section->id] = array(

        'id' => $current_section->id,

        'image_icon' => $img,

        'title' => $current_section->title,

        'pages' => $data,

        'is_all_complated' => '',

        'active' => 1

    );



    return $learning_modules;

}

/**Redo course from dashboard */
add_action('wp_ajax_myst_staff_training_redo_course_module','myst_staff_training_redo_course_module');
add_action('wp_ajax_nopriv_myst_staff_training_redo_course_module','myst_staff_training_redo_course_module');
function myst_staff_training_redo_course_module(){
    global $wpdb;
    $data = array();
    if(is_user_logged_in()){
        $user_id = get_current_user_id();
    }
    $secid = sanitize_text_field($_POST['sectionid']);
    $quiz_table = $wpdb->prefix . 'quiz_details';
    $quiz_user_tbl = $wpdb->prefix . 'quiz_user_details';
    $quiz_section_score = $wpdb->prefix . 'quiz_section_score';
    $coin_wallet = $wpdb->prefix . 'coin_wallet';
    $redocourse_coins = array();
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT id FROM %s WHERE userid = %d AND quizid IN (SELECT quizid FROM %s WHERE sectionid = %d)",
            $quiz_user_tbl,
            $user_id,
            $quiz_table,
            $secid
        ),
        ARRAY_A
    );
    if(!empty($results)) {
        foreach($results as $value) {
            $delete = $wpdb->delete($quiz_user_tbl, array('id' => $value['id']));
        }
    }
    $wallet_result = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}coin_wallet where user_id = $user_id",ARRAY_A);
    $scoreresult = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT id, sectionid, userid, coins FROM {$wpdb->prefix}quiz_section_score WHERE sectionid = %d",
            $secid
        ),
        ARRAY_A
    );
    foreach($scoreresult as $sr){
        // $totalamount = $wallet_result['total_coins'] - $sr['coins'];
        // $update = $wpdb->update($coin_wallet,array('total_coins' => $totalamount),array('user_id'=>$sr['userid']));
        $getcoins = json_decode(get_user_meta($user_id,'redo_course_coins',true),true);
        if(empty($getcoins)) {
            $getcoins[$sr['sectionid']] = $sr['coins'];
            add_user_meta( $user_id, 'redo_course_coins', json_encode($getcoins) );
        }else{
            //if(!empty($getcoins[$sr['sectionid']])) {

            $getcoins[$sr['sectionid']] = $sr['coins'];
            //}
            update_user_meta( $user_id, 'redo_course_coins', json_encode($getcoins) );
        }

        $scoredelete = $wpdb->delete($quiz_section_score, array('sectionid'=> $sr['sectionid']));
    }

    $learning_modules = get_user_meta( $user_id , 'learning_modules_progress', true);
    $learning_subsection = unserialize($learning_modules);

    foreach ($learning_subsection[$secid]['pages'] as $key => $learn_sub) {
        $data[$learn_sub['sub_title']] = array(
            'sub_title' => $learn_sub['sub_title'],
            'sub_start_url' => $learn_sub['sub_start_url'],
            'sub_completed_url' => $learn_sub['sub_completed_url'],
            'status' => ''
        );
    }

    $learning_subsection[$secid] = array(
        'id' => $secid,
        'image_icon' => $learning_subsection[$secid]['image_icon'],
        'title' => $learning_subsection[$secid]['title'],
        'pages' => $data,
        'is_all_complated' => '',
        'active' => 1
    );

    update_user_meta($user_id, 'learning_modules_progress',serialize($learning_subsection));

    echo "success";
    die();
    //return $learning_modules;
}

add_action('wp_ajax_myst_staff_training_atl_create_order','myst_staff_training_atl_create_order');
add_action('wp_ajax_nopriv_myst_staff_training_atl_create_order','myst_staff_training_atl_create_order');
function myst_staff_training_atl_create_order(){
    global $wpdb;
    $orders_tbl = $wpdb->prefix.'atl_orders';
    $products_tbl = $wpdb->prefix.'atl_products';
    $coin_wallet = $wpdb->prefix.'coin_wallet';
    $data = array();
    if(is_user_logged_in()){
        $user_id = get_current_user_id();
    }
    $productid = sanitize_text_field($_POST['productid']);
    $productprice = sanitize_text_field($_POST['productprice']);
    $current_date = date('d-m-Y H:i:s');
    $insert = $wpdb->insert($orders_tbl,array('product_id' => $productid,'user_id'=>$user_id,'order_created' => current_time('mysql', 1),'order_total'=> $productprice, 'payment_method' => 'COD','order_status' => 'Processing'));
    $orderid = $wpdb->insert_id;
    // $row = $wpdb->get_row("SELECT total_coins FROM $coin_wallet WHERE user_id = $user_id",ARRAY_A);
    $row = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT total_coins FROM {$wpdb->prefix}coin_wallet WHERE user_id = %d",
        $user_id
    ),
    ARRAY_A
);
    $total_coins = $row['total_coins'] - $productprice;

    $update = $wpdb->update($coin_wallet, array('total_coins' => $total_coins), array('user_id'=>$user_id));

    /* $get_coins = $wpdb->get_results("SELECT total_coins from $coin_wallet where user_id=".$user_id, ARRAY_A);
    if($get_coins[0]['total_coins'] > $product_price){
        $disabled = false; //user can buy again this product - remove class disable
    }else{
        $disabled = true; //user doesn't have enough coins to buy this product again.
    } */

    
        // add code from order email template to functions.php by kashif at 12feb 5:47pm
    ob_start();

    global $wpdb;
    $product_table = $wpdb->prefix.'atl_products';

    $product_id = $productid;
     $userid = $user_id;
      $order_created = $current_date;
    $order_total= $productprice;
      $payment_method = 'COD';
      $order_status ='Processing';
      $orderid= $orderid;
      $userdata = get_userdata( $userid );
      $username = $userdata->user_login;

    
    $get_product_detail = $wpdb->get_row("SELECT product_name FROM {$wpdb->prefix}atl_products WHERE id = $product_id",ARRAY_A);

    $product_name = $get_product_detail['product_name'];
    

    
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    <html lang="en">
    
    <head>
        <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
        <title>Order Created</title>
    
        <style type="text/css">
            @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
            body {
                font-family: 'Roboto', sans-serif !important;
                height: 100% !important;
                width: 100% !important;
                -ms-text-size-adjust: 100%;
                margin: 0;
                padding: 0;
            }
    
            table {
                border-collapse: collapse;
                border: none;
            }
            table th{
                text-align:left;
                font-size:15px;
            }
            table,
            td {
                mso-table-lspace: 0pt !important;
                mso-table-rspace: 0pt !important;
            }
    
            img {
                -ms-interpolation-mode: bicubic;
                max-width: 100%;
            }
    
            a {
                text-decoration: none;
            }
    
            p,
            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                margin: 0;
            }
    
            a,
            a:link,
            a:visited {
                text-decoration: none;
                color: #00788a;
            }
    
            a:hover {
                text-decoration: none;
            }
    
            h2,
            h2 a,
            h2 a:visited,
            h3,
            h3 a,
            h3 a:visited,
            h4,
            h5,
            h6,
            .t_cht {
                color: #000 !important;
            }
    
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td {
                line-height: 100%;
            }
        </style>
    </head>
    <body>
        <table class="table-pdf" width="100%" cellpadding="0" cellspacing="0" align="center"
            style="border:1px solid #eee;max-width:600px;margin:auto;font-family: 'Roboto', sans-serif !important;">
            <tbody>
                <tr>
                    <td>
                        <!-- logo starts here  -->
                        <table width="100%" cellpadding="0" cellspacing="0" align="center" bgcolor="#fff" style="font-family: 'Roboto', sans-serif !important;">
                            <tbody>
                                <tr>
                                    <td style="padding:15px 10px;" align="center">
                                        <?php  myst_staff_training_get_custom_logo_function(); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- logo ends here  -->
                        <!-- content starts here  -->
                        <table width="100%" cellpadding="0" cellspacing="0" align="center">
                            <tbody>
                                <tr>
                                    <td style="padding: 40px 30px;" bgcolor="#f9f7fa">
                                        <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                            <tbody>
                                                <tr>
                                                    <td style="color: #757376;">
                                                        <h1 style="margin-bottom: 15px;">Hey Admin,</h1>
                                                        <p style="margin-bottom: 15px;font-size: 20px;">You have received an order from <?php echo esc_html($username); ?>. The order is as follows:</p>
                                                        
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
    
                                        <!-- Order id starts  -->
                                        <table width="100%" cellpadding="0" cellspacing="0" align="center"
                                            style="margin-top: 10px;">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p style="text-align:center;margin-bottom: 15px;">
                                                            <a href="<?php echo esc_url(admin_url('admin.php?page=manage-orders')); ?>" style="color: #14a59c;display: inline-block;font-weight:300;font-size:22px;">Order #<?php echo esc_html($orderid); ?> (<?php echo date('F j, Y',strtotime($order_created)); ?>)</a>
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
    
                                                    <td style="padding:40px 30px;border-radius:8px;">
                                                        <!-- order details start  -->
                                                        <table width="100%" cellpadding="10" cellspacing="10" border="1" align="center">
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Price</th>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td><?php echo esc_html($product_name); ?></td>
                                                                <td><?php echo esc_html($order_total); ?> Pts</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Payment Method:</th>
                                                                <td><?php echo esc_html($payment_method); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total:</th>
                                                                <td><?php echo esc_html($order_total); ?> Pts</td>
                                                            </tr>
                                                           
                                                        </table>
                                                        <!-- order details end  -->
    
                                                        <!-- customer details start  -->
                                                        
                                                        <table width="100%" cellpadding="0" cellspacing="0" align="center"
                                                            style="margin-top:30px; margin-bottom: 20px;">
                                                            <tr>
                                                                <td style="padding-bottom: 30px;border-bottom: 1px solid #eee;">
                                                                    <h4>Customer Details</h4>
                                                                    <p>User Name : <?php echo esc_html($username); ?></p>
                                                                    <p>User Email : <?php echo esc_html($userdata->user_email); ?></p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                       
                                                        <!-- customer details end  -->
                                                
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!-- order id ends  -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- content ends here  -->
    
                        <!-- green content stripe ends here  -->
                        <table width="100%" align="center" cellpadding="0" cellspacing="0">
                            <tbody> 
                                <tr>
                                    <td style="background-image: url(<?php echo esc_url(site_url()); ?>/wp-content/uploads/2022/11/email-footer-banner.png);background-repeat:no-repeat;background-size: 100%;background-position:left;">
                                        <!-- seperatorder table starts here  -->
                                        <table width="100%" cellpadding="0" cellspacing="0" align="center" bgcolor="">
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" height="55"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!-- seperatorder table ends here  -->
                    
                                        <!-- footer table starts here -->
                                        <table width="100%" cellpadding="0" cellspacing="0" align="center" bgcolor="">
                                            <tbody>
                                                <tr>
                                                    <td height="100">
                                                        
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!-- footer table ends here -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
    
    </html>
    <?php
    $mail_html .= ob_get_clean();
    $serverHostname = $_SERVER['HTTP_HOST'];
$currentDomain = preg_replace('/:\d+$/', '', $serverHostname);
            $headers = array('Content-Type: text/html; charset=UTF-8','From:  <noreply@' . $currentDomain . '>'); 
    $to = esc_attr( get_option('em_id_list') );
    $subject = 'New Order has been created';
    
    
    
 
      


    
        // Respond after the API call
        echo json_encode(array("success" => "success", "disabled" => ''));
        $sent = wp_mail($to,$subject,$mail_html,$headers);
        die();
}

//update order status from wp admin
add_action('wp_ajax_update_order_status','myst_staff_training_atl_update_order_status');
add_action('wp_ajax_nopriv_update_order_status','myst_staff_training_atl_update_order_status');
function myst_staff_training_atl_update_order_status(){
    global $wpdb;
    $response = array();
    $orderid = sanitize_text_field($_POST['order_id']);
    $order_status = sanitize_text_field($_POST['order_status']);
    $orders_tbl = $wpdb->prefix.'atl_orders';
    $update = $wpdb->update($orders_tbl,array('order_status' => $order_status),array('id' => $orderid));
    
    if($update) {
        $response['order_status'] = $order_status;
        $response['success'] = 'changed';
    }else{
        
        print_r($wpdb->print_error());
    }
    echo json_encode($response);
    die();
}

//Delete order from wp-admin
function myst_staff_training_atl_delete_order(){
    global $wpdb;
    $orderid = sanitize_text_field($_POST['order_id']);
    $table_name = $wpdb->prefix . 'atl_orders';
    $delete_data = $wpdb->delete($table_name,array('id'=>$orderid));
    if($wpdb->last_error == '') {
        echo 'deleted';
    }
    die;
}
add_action('wp_ajax_myst_staff_training_atl_delete_order','myst_staff_training_atl_delete_order');
add_action('wp_ajax_nopriv_myst_staff_training_atl_delete_order','myst_staff_training_atl_delete_order');

//update user wallet points from wp admin
function myst_staff_training_modify_user_wallet_points(){
    $response = array();
    global $wpdb;
    $points = sanitize_text_field($_POST['points']);
    $user_id = sanitize_text_field($_POST['user_id']);
    $table_name = $wpdb->prefix . 'coin_wallet';
    $delete_data = $wpdb->update($table_name,array('total_coins'=>$points),array('user_id' => $user_id));
    if($wpdb->last_error == '') {
        $response['success'] = 'updated';
        $response['updated_points'] = $points;
    }
    echo json_encode($response);
    die;
}
add_action('wp_ajax_myst_staff_training_modify_user_wallet_points','myst_staff_training_modify_user_wallet_points');
add_action('wp_ajax_nopriv_myst_staff_training_modify_user_wallet_points','myst_staff_training_modify_user_wallet_points');

//get quiz data from section id and user id
function myst_staff_training_fetch_quiz_by_u_s($sectionid, $userid){
    global $wpdb;
    $quiztable = $wpdb->prefix.'quiz_details';
    $quiz_user = $wpdb->prefix.'quiz_user_details';
    // $score_sql = $wpdb->get_results("SELECT quizid,score FROM {$quiz_user} WHERE userid={$userid} and quizid IN (SELECT quizid FROM {$quiztable} WHERE sectionid={$sectionid}) ",ARRAY_A);
    
$score_sql = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT quizid, score FROM {$wpdb->prefix}quiz_user_details WHERE userid = %d AND quizid IN (SELECT quizid FROM {$wpdb->prefix}quiz_details WHERE sectionid = %d)",
        $userid,
        $sectionid
    ),
    ARRAY_A
);
    $score_arr = [];
    foreach($score_sql as $row) {
                        
        $score_data = json_decode($row['score'],true);
        $score_arr[] = $score_data['percentage'];
        
    }
    $learning_modules = get_user_meta( $userid , 'learning_modules_progress', true);
    $learning_modules = unserialize($learning_modules);
    $value = $learning_modules[$sectionid];

    if(!empty($score_arr)) :
        $avg = array_sum($score_arr) / count($score_arr);
    endif;
    if($value['is_all_complated'] == 1 && $value['active'] == 1 ){
        $quiz_score = round($avg);
        
        if($quiz_score != ''){
            if($quiz_score == get_option('gold_score_min')):
                $cls = 'trophygold.png';
                $bgcolor = '';
            elseif($quiz_score >= get_option('silver_score_min') && $quiz_score <= (get_option('gold_score_min') - 1)):
                $cls = 'trophysilver.png';
                $bgcolor = '';
            elseif($quiz_score >= get_option('bronze_score_min') && $quiz_score <= (get_option('silver_score_min') - 1)):
                $cls = 'trophybronze.png';
                $bgcolor = '';
            elseif($quiz_score >= get_option('fail_score_min') && $quiz_score <= (get_option('bronze_score_min') - 1)):
                $cls = 'trophyx.png';
                $bgcolor = 'style="background:#000;"';
            endif;
            $average = '<img src="'.st_my_plugin_dir_folder.'/images/'.$cls.'" width="40px" height="40px" '.$bgcolor.' />';
        }else if($quiz_score == 0 || $quiz_score == ''){
            $average = '';
        }
        if($quiz_score != 0) {
        ?>

<div class="trophy-image">
    <?php echo esc_html($average); ?>
    <p>
        <?php echo esc_html($quiz_score); ?>
    </p>
</div>
<?php   
        }
    }
}

add_action('after_setup_theme', 'myst_staff_training_remove_admin_bar');

function myst_staff_training_remove_admin_bar() {

if (!current_user_can('administrator') && !is_admin()) {

    show_admin_bar(false);

}

}



// add_filter('the_content', 'add_content_after');



// function add_content_after($content) {

//     $after_content = "[myst_learning_modules_action_btn]";

//     $fullcontent = $content . $after_content;

//     return $fullcontent;

// }

// add_filter('the_content', 'add_content_for_completed_sections');
// for adding quiz section in subsection complted url
function myst_staff_training_add_content_for_completed_subsections_pages($content) {
    global $wpdb;

    // Replace 'your_custom_table' with the actual name of your custom table
    $custom_table_name = $wpdb->prefix . 'learning_sections';

    // Get serialized data from the database
    $serialized_data = $wpdb->get_col("SELECT learning_subsection FROM $custom_table_name");

    // Check if there is any serialized data
    if (!empty($serialized_data)) {
        foreach ($serialized_data as $data) {
            // Deserialize the data
            $subsection_data = maybe_unserialize($data);

            // Check if 'sub_completed_url' is set and print the URL
            foreach ($subsection_data as $sub_section) {
                if (isset($sub_section['sub_completed_url'])) {
                    $sub_completed_url = $sub_section['sub_completed_url'];

                    if ($sub_completed_url === get_permalink()) {
                        // Apply the shortcode if the condition is met
                        $after_content = '[myst_learning_modules_action_btn]';
                        $fullcontent = $content . $after_content;
                        return $fullcontent;
                    }
                }
            }
        }
    }

    return $content;
}

// Add the filter
add_filter('the_content', 'myst_staff_training_add_content_for_completed_subsections_pages');


function myst_staff_training_get_display_name($user_id) {
    if (!$user = get_userdata($user_id))
        return false;
    return $user->data->display_name;
}

add_filter( 'recovery_mode_email', function( $email ) {
    $email['to'] = array('demo.narola@narola.email');
    return $email;
} );

function myst_staff_training_coin_shopping_get_wallet_balance($userid){
    global $wpdb;
    $tablename= $wpdb->prefix.'coin_wallet';
    $total_coins = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}coin_wallet WHERE user_id = {$userid}",ARRAY_A);

    
    return $total_coins['total_coins'];
}

/* SAVE the product */
function myst_staff_training_insert_product_shop_items() {

    global $wpdb;
    if($_POST['status'] == 'add') {

        parse_str($_REQUEST['data'], $output);
        
        $product_title = $output['product_title'];
        $product_desc = $output['product_desc'];
        $product_price = $output['product_price'];
        $prod_icon_attachment = $output['prod_icon_attachment'];
        
        $table_name = $wpdb->prefix . 'atl_products';
        $insert_data = $wpdb->insert( $table_name , array( 'product_name' => $product_title, 'product_desc' => $product_desc, 'product_price' => $product_price, 'image_icon' => $prod_icon_attachment));
        
        if($wpdb->last_error == '') {
            echo 'success';
        }
    }else if ($_POST['status'] == 'update'){
        parse_str($_REQUEST['data'], $output);
        
        $product_title = $output['product_title'];
        $product_desc = $output['product_desc'];
        $product_price = $output['product_price'];
        $prod_icon_attachment = $output['prod_icon_attachment'];
        
        $table_name = $wpdb->prefix . 'atl_products';
        $insert_data = $wpdb->update( $table_name , array( 'product_name' => $product_title, 'product_desc' => $product_desc, 'product_price' => $product_price, 'image_icon' => $prod_icon_attachment), array('id' => $output['prodid']));
        
        if($wpdb->last_error == '') {
            echo 'success';
        }
    }
    die;
}
add_action( 'wp_ajax_myst_staff_training_insert_product_shop_items', 'myst_staff_training_insert_product_shop_items' );
add_action( 'wp_ajax_nopriv_myst_staff_training_insert_product_shop_items', 'myst_staff_training_insert_product_shop_items' );

function myst_staff_training_delete_product_shop_items(){
    global $wpdb;
    $productid = sanitize_text_field($_POST['product_id']);
    $table_name = $wpdb->prefix . 'atl_products';
    $delete_data = $wpdb->delete($table_name,array('id'=>$productid));
    if($wpdb->last_error == '') {
        echo 'deleted';
    }
    die;
}
add_action('wp_ajax_myst_staff_training_delete_product_shop_items','myst_staff_training_delete_product_shop_items');
add_action('wp_ajax_nopriv_myst_staff_training_delete_product_shop_items','myst_staff_training_delete_product_shop_items');

function myst_staff_training_get_custom_logo_function(){
     
    if( get_option( 'atls_img' ) != '') : ?>
<img src="<?php echo wp_get_attachment_image_url(get_option( 'atls_img' ),'medium'); ?>" class="login-logo" />
<?php else: 
        echo get_custom_logo(); 
    endif; 
}