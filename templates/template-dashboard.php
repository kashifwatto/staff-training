<?php

/*
Template Name: User Dashboard Template
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<?php 
get_header();

global $wpdb;
if ( !is_user_logged_in() ) {
    wp_redirect( site_url().'/wp-admin');
    exit;
}
$user_id = '1';
if(is_user_logged_in()){
    $user_id = get_current_user_id();
}


$learning_modules = get_user_meta( $user_id , 'learning_modules_progress', true);


$flag_unassigned=true;

if(empty($learning_modules)){
    $learning_sections = mystaff_training_staff_training_get_learning_section();

    $user_assigned_section = array();

    if(!empty($learning_sections)){

        foreach ($learning_sections as $key => $items) {

            $assigned_users = json_decode(stripslashes($items->assigned_users));

            if( !empty($assigned_users) && in_array($user_id, $assigned_users) ){

                $user_assigned_section[] = $items;

            }

        }

    }




    $learning_modules = array();

    foreach ($user_assigned_section as $key => $value) {
        
        $data = array();

        $learning_subsection = unserialize($value->learning_subsection);

        foreach ($learning_subsection as $key => $learn_sub) {

            $data[$learn_sub['sub_title']] = array(

                'sub_title' => $learn_sub['sub_title'],

                'sub_start_url' => $learn_sub['sub_start_url'],

                'sub_completed_url' => $learn_sub['sub_completed_url'],

                'status' => ''

            );

        }

        $img = '';
        if(!empty($value->image)){

            $image = wp_get_attachment_image_src($value->image, 'full');

            $img = $image[0];

        }

        $learning_modules[$value->id] = array(

            'id' => $value->id,

            'image_icon' => $img, 

            'title' => $value->title,

            'pages' => $data,

            'is_all_complated' => '',

            'active' => 1

        );

    }

    update_user_meta($user_id, 'learning_modules_progress',serialize($learning_modules));

} else{

    $learning_modules = unserialize($learning_modules);


}




///////////////////////////////////////////////////
if(true){
    $learning_sections = mystaff_training_staff_training_get_learning_section();

    $user_unassigned_section = array();
    
    if(!empty($learning_sections)){
    
        foreach ($learning_sections as $key => $items) {
    
            $assigned_users = json_decode(stripslashes($items->assigned_users));
    
            if( !empty($assigned_users) && in_array($user_id, $assigned_users) ){
                //assigned
            }else{
                $user_unassigned_section[] = $items;
            } 
    
        }
    
    }
    
    $learning_modules_unassigned = array();
    
    foreach ($user_unassigned_section as $key => $value) {
    
        $data = array();
    
        $learning_subsection = unserialize($value->learning_subsection);
    
        foreach ($learning_subsection as $key => $learn_sub) {
    
            $data[$learn_sub['sub_title']] = array(
    
                'sub_title' => $learn_sub['sub_title'],
    
                'sub_start_url' => $learn_sub['sub_start_url'],
    
                'sub_completed_url' => $learn_sub['sub_completed_url'],
    
                'status' => ''
    
            );
    
        }
    
        $img = '';
    
        if(!empty($value->image)){
    
            $image = wp_get_attachment_image_src($value->image, 'full');
    
            $img = $image[0];
    
        }
    
        $learning_modules_unassigned[$value->id] = array(
    
            'id' => $value->id,
    
            'image_icon' => $img, 
    
            'title' => $value->title,
            
            'cat' => $value->cat,
    
            'pages' => $data,
    
            'is_all_complated' => '',
    
            'active' => 1
    
        );
    
    }
    

}
///////////////////////////////////////////////////

// this code commented by kashif at 25/01/2024 4:31 PM
// include(plugin_dir_path( __FILE__ ) .'/Mobile_Detect.php');
// $detect = new Mobile_Detect;
?>

<div class="dashboard-section">

    <div class="main-content">

        <div class="content-left">
            
            <div class="logout_link">
                <a href="javascript:void(0);" class="show-popup accordian-text ldrbrd-popup" data-showpopup="leaderbrdm">
                    <span class="lg-text">Leaderboard</span>
                    <span class="lg-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard2-data" viewBox="0 0 16 16">
                            <path d="M9.5 0a.5.5 0 0 1 .5.5.5.5 0 0 0 .5.5.5.5 0 0 1 .5.5V2a.5.5 0 0 1-.5.5h-5A.5.5 0 0 1 5 2v-.5a.5.5 0 0 1 .5-.5.5.5 0 0 0 .5-.5.5.5 0 0 1 .5-.5h3Z"/>
                            <path d="M3 2.5a.5.5 0 0 1 .5-.5H4a.5.5 0 0 0 0-1h-.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1H12a.5.5 0 0 0 0 1h.5a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-12Z"/>
                            <path d="M10 7a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0V7Zm-6 4a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0v-1Zm4-3a1 1 0 0 0-1 1v3a1 1 0 1 0 2 0V9a1 1 0 0 0-1-1Z"/>
                        </svg>
                    </span>
                </a>
                <div class="overlay-content popup" id="cmplt_leaderbrdm">
                    <button class="close-btn">x</button>
                    <div class="learning-modules"> 
                        
                            <?php 
                            
                            $table_name = $wpdb->prefix . 'quiz_details';
                            $quiz_table_name = $wpdb->prefix . 'quiz_user_details';
                            $quiz_section_score = $wpdb->prefix . 'quiz_section_score';

                            $q1 = $wpdb->get_results("SELECT qud.score,qud.userid,qud.quizid,qd.sectionid FROM {$wpdb->prefix}quiz_user_details AS qud INNER JOIN {$wpdb->prefix}quiz_details AS qd ON qd.quizid = qud.quizid order BY qud.userid, qd.sectionid",ARRAY_A );
                            $sp = array();
                            $exclude_users = (!empty(get_option('user_id_list'))) ? get_option('user_id_list') : array() ;
                            foreach ($q1 as $key1 => $value1) {  
                                $pscore =  json_decode($value1['score']);
                                $score = $pscore->percentage; 
                                $quiz_score = round($score);
                                $learning_modules1 = get_user_meta($value1['userid'] , 'learning_modules_progress', true);
                                $learning_modules1 = unserialize($learning_modules1);
                                $isassigned = $learning_modules1[$value1['sectionid']];                
                                if($isassigned['is_all_complated'] == 1 &&  $isassigned['active'] == 1 ){  //
                                    if(!in_array($value1['userid'], $exclude_users)){
                                        $sp[$value1['userid']][$value1['sectionid']][] = $quiz_score;
                                    }
                                }
                                
                            }
                            ?>
                            <div class="learning-main-section dash-lb"><?php
                            
                            $sortingarr = array();
                                            foreach ($sp as $lbk => $lbv) {               
                                                $display_name = mystaff_training_staff_training_get_display_name($lbk);
                                                $name =  $display_name;
                                                $weight_arr = 0;
                                                $gs = get_option('gold_score_min');
                                                $gw = get_option('gold_score_weight');
                                                
                                                $ss = get_option('silver_score_min');
                                                $sw = get_option('silver_score_weight');
                                            
                                                $bs = get_option('bronze_score_min');
                                                $bw = get_option('bronze_score_weight');
                                            
                                                $fs = get_option('fail_score_min');
                                                $fw = get_option('fail_score_weight');
                                                
                                                if($name) { 
                                                    foreach($lbv as $sid => $sval) {  
                                                        $score = $lbv[$sid];                          
                                                        if(!empty($score)) :
                                                            $t_quiz_score = array_sum($score) / count($score);
                                                        endif;
                                                
                                                        $quiz_score = round($t_quiz_score);
                        
                                                        if($quiz_score == $gs):
                                                            
                                                            $weight = $gw;
                                                        elseif($quiz_score >= $ss && $quiz_score <= ($gs - 1)):
                                                            
                                                            $weight = $sw;
                                                        elseif($quiz_score >= $bs && $quiz_score <= ($ss - 1)):
                                                            
                                                            $weight = $bw;
                                                        elseif($quiz_score >= $fs && $quiz_score <= ($bs - 1)):
                                                            
                                                            $weight = $fw;
                                                        endif;
                                                            
                                                        if($weight == $gw): 
                                                            $weight_arr += $weight;
                                                             
                                                        elseif($weight == $sw):
                                                            $weight_arr += $weight;
                                                            
                                                            
                                                        elseif($weight == $bw):
                                                            $weight_arr += $weight;
                                                            
                                                        elseif($weight == $fw):
                                                            $weight_arr += $weight;
                                                            
                                                        endif; 
                                                    }
                                                       
                                                }
                                                $sortingarr[] = array('name' => $name, 'score' => $weight_arr);
                                            }
                                            
                                            $sortingarr = wp_list_sort( $sortingarr, 'score', 'DESC' );
                                            $uid=get_current_user_id();
                                            $user_info = get_userdata($uid);
                                            $myScores=0;
                                            $myLeague=0;
                                            $leaguesEnabled=get_option('if_leagues');
                                            $league1Score=get_option('league1_score_min');
                                            $league2Score=get_option('league2_score_min');
                                            $league3Score=get_option('league3_score_min');
                                            $league1Title=get_option('league1_title');
                                            $league2Title=get_option('league2_title');
                                            $league3Title=get_option('league3_title');
                                            
                                            
                                            foreach($sortingarr as $idx => $srtv) {
                                                if($srtv['name']==$user_info->display_name){
                                                    $myScores=$srtv['score'];
                                                    break;
                                                }   
                                            }
                                //          $myScores=35; //for testing
                                            
                                            if($myScores>=$league1Score){
                                                $myLeague=1;
                                            }
                                            if($myScores>=$league2Score){
                                                $myLeague=2;
                                            }
                                            if($myScores>=$league3Score){
                                                $myLeague=3;
                                            }
                                
                            
                            ?>
                                <div class="learning-top-header leader-top-header">
                                    <h5 style="margin-left:10px">Leaderboard
                                    <?php
                                    if($leaguesEnabled=='y'){
                                        if($myLeague==1){
                                            echo " - " . esc_html($league1Title);
                                        }else if($myLeague==2){
                                            echo " - " . esc_html($league2Title);

                                        }else if($myLeague==3){
                                            echo " - " . esc_html($league3Title);
                                        }else{
                                            echo " - No League";
                                        }   
                                    }
                                    ?>
                                    </h5>
                                    
                                    <small style="font-size:12px;margin-left:15px">
                                    <?php
                                        if($leaguesEnabled=='y'){
                                            if($myLeague==1){
                                                echo "To enter the ".esc_html($league2Title).", score ".esc_html($league2Score)." or more.";
                                            }else if($myLeague==2){
                                                echo "To enter the ".esc_html($league3Title).", score ".esc_html($league3Score)." or more.";
                                            }else if($myLeague==3){
                                                echo "Well done! You are now in the ".esc_html($league3_title).".";
                                            }else{
                                                echo "To enter the ".esc_html($league1Title).", score ".esc_html($league1Score)." or more.";
                                            }                                           
                                        }
                                    ?>
                                    </small>
                                </div>
                                <table class="table cell-border" id="lbscore" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                            
                                    <tbody class="people-list">
                                        <?php
                                            foreach($sortingarr as $idx => $srtv) {
                                                if($leaguesEnabled=='y'){
                                                    if(($myLeague==1 && $srtv['score']<$league2Score) || ($myLeague==2 && $srtv['score']>=$league2Score && $srtv['score']<$league3Score) || ($myLeague==3 && $srtv['score']>=$league3Score)){
                                                        //do nothing for now
                                                    }else{
                                                        continue;   
                                                    }                                                   
                                                }
                                                ?>
                                                <tr>
                                                    <td class="people-name">
                                                        <?php echo  esc_html($srtv['name']);?>
                                                    </td>
                                                    <td>                  
                                                    <?php echo esc_html($srtv['score']);?>
                                                    </td>  
                                                    
                                                </tr>
                                                <?php
                                            } 
                                            
                                        ?>  
                                    </tbody>
                                </table>
                            </div>
                        
                    </div>
                </div>
                <a href="<?php echo esc_url(site_url()).'/wp-login.php?action=lostpassword'; ?>">
                    <span class="lg-text">Change Password</span>
                    <span class="lg-icon">
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                            viewBox="0 0 390 391" style="enable-background:new 0 0 390 391;" xml:space="preserve">
                        <!-- <style type="text/css">
                            .st0{fill:#4d9ffc;}
                        </style> -->
                        <g>
                            <path class="st0" d="M387.9,332.1c0,1.8-0.2,2.7-2.4,2.7c-30.3-0.1-60.7,0-91.4,0c0.6-2,2.1-3,3.1-4.3
                                c10.2-11.8,21.3-22.8,30.4-35.6c15.2-21.6,24.6-45.5,28.6-71.6c0.4-2.6,0.7-5.2,1-7.8c0.5-9.8,0.9-19.7,0-29.6
                                c-2.6-29.8-12.4-56.8-29.6-81.2C314,85.6,297.2,70,277.1,58c-19.5-11.6-40.6-18.9-63.2-21.8c-24.2-3.1-47.8-1-71,6.4
                                c-2.3,0.7-2.8-0.2-3.3-2c-2.5-7.6-4.9-15.2-7.7-22.7c-1-2.7-0.1-3.4,2.2-4.1c13.2-4.1,26.6-6.7,40.3-8.2c0.9-0.1,2.1,0.3,2.7-0.8
                                h30c4.9,1.6,10,1.4,15,2.2c35.4,5.4,66.9,19.5,94.5,42.1c16.6,13.6,30.6,29.5,41.9,47.8c10.8,17.5,18.9,36.2,23.7,56.2
                                c4.9,20.6,7.1,41.3,4.8,62.5c-0.8,8.3-1.6,16.5-3.4,24.7c-3.6,16.5-9,32.3-16.8,47.4c-2.9,5.6-6,11.1-9.4,17.2h24.7
                                c5.8,0,5.8,0,5.8,5.6C387.8,317.6,387.8,324.9,387.9,332.1z"/>
                            <path class="st0" d="M261.1,378.6c-14.5,4.8-29.4,7.9-44.6,9.4c-0.9,0.1-2-0.4-2.7,0.7h-30.8c-4.1-1.7-8.5-1.4-12.7-2
                                c-50.9-7.9-92.5-32.1-124.6-72.3C24,287.3,10.9,256.2,5.9,221.8c-0.6-4.1-0.5-8.3-2-12.3v-30.8c0.9-0.4,0.7-1.2,0.7-2
                                c2.7-30.4,12-58.6,27.9-84.7c0.5-0.9,1.5-1.7,1.2-3.3H3.8v-30h93.2c0.6,1.3-0.5,1.9-1.1,2.5c-5.4,6.1-10.7,12.2-16.3,18.1
                                c-6.3,6.8-12,14-17.2,21.5c-7.9,11.4-14,23.8-18.8,36.8c-8,21.7-10.6,44.2-9.6,67.2c1.3,11.8,3.1,23.5,6.4,34.9
                                c8,27.3,22.1,50.9,42.2,70.9c25.6,25.5,56.5,40.8,92.2,46.3c25.7,3.9,51,1.6,75.8-6.6c3-1,4.3-0.8,5.3,2.5
                                c2.2,7.1,4.8,14.2,7.6,21.1C264.6,376.9,263.8,377.7,261.1,378.6z"/>
                            <path class="st0" d="M285.9,298.3c-0.2-5.4-0.1-10.7-0.1-16.1c0-31.7-0.1-63.4-0.1-95.2c0-10.9,0-21.8,0.2-32.6
                                c0-2.2-0.6-2.8-2.8-2.7c-6.1,0.1-12.2,0-18.4,0c-8.9,0-8.8,0-9-8.8c-0.2-10.6,1-21.3-0.9-31.8c-5.5-30.4-33.5-51.9-63.9-49.2
                                c-31.3,2.8-55,28.5-55.1,60c0,9-0.1,18,0,27c0,2.4-0.7,2.9-3,2.9c-8.1-0.1-16.2,0-24.4-0.1c-2.1,0-2.8,0.5-2.8,2.7
                                c0.1,12.9,0.1,25.7,0.2,38.6c0,35,0,69.9-0.2,104.9c0,3.1,0.8,3.9,3.9,3.9c25.6-0.1,51.2-0.1,76.8-0.1c32,0,64,0,95.9,0.1
                                C285.2,301.8,286,301.2,285.9,298.3z M166.6,115.9c2.9-15.7,15.9-25.2,31.9-24c15,1.1,26.9,13.9,27.3,28.9c0.2,9.4,0,18.7,0.1,28.1
                                c0,2.3-0.7,2.9-3,2.9c-9-0.1-18,0-27,0c-9.1,0-18.2,0-27.4,0c-1.3,0-2.8,0.4-2.8-1.9C166.2,138.5,164.5,127,166.6,115.9z
                                M255.9,267.6c0,3.4-0.9,4.3-4.3,4.3c-37.3-0.1-74.7-0.1-112,0c-3.1,0-3.9-0.8-3.9-3.9c0.1-25,0-50-0.1-74.9
                                c0.1-2.8,0.3-5.5,0.1-8.2c-0.2-2.5,0.7-3.1,3.1-3.1c10.7,0.1,21.5,0.1,32.2,0.1h79c5.5,0,5.5,0,5.8,5.3
                                C255.9,213.9,255.8,240.7,255.9,267.6z"/>
                            <path class="st0" d="M210.8,226.6c0.1,8.3-6.6,15.1-14.8,15.1c-8.3,0.1-15.1-6.6-15.1-14.8c-0.1-8.3,6.6-15.1,14.8-15.1
                                C203.9,211.7,210.7,218.3,210.8,226.6z"/>
                        </g>
                        </svg>
                    </span>
                </a>
                <a href="<?php echo wp_logout_url(site_url().'/wp-admin/');?>">
                    <span class="lg-text">
                        Logout
                    </span>
                    <span class="lg-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 368 390" style="enable-background:new 0 0 368 390;" xml:space="preserve">
                        <g>
                            <path d="M181.7,381.1c-37.6,0-75.9,4.1-112.7-1.1c-40.4-5.7-58.3-37.1-58.8-76.4C9.3,231.3,8.8,159,10.4,86.7   c1.1-51.3,29.2-79,81.4-79.8c30.2-0.5,60.5-0.1,90.7-0.1c0.4,5.8,0.9,11.6,1.3,17.5c-19,1.6-37.9,4.5-56.9,4.7   c-91.1,1.1-94.6,4.5-94.7,97.6c0,52.8,1.9,105.6-0.5,158.3c-2.5,55.2,20,80.4,75.9,76.1c24.4-1.9,49-0.3,73.6-0.3   C181.3,367.5,181.5,374.3,181.7,381.1z"/>
                            <path d="M271.8,260.3c12.1-14.6,24.3-29.1,44.3-53.1c-76.5,0-145.6,0-214.7,0c-0.2-6.5-0.5-13-0.7-19.5c68.9,0,137.8,0,220.1,0   c-23.4-27.8-36.2-43.1-49.1-58.3c3.8-3.9,7.6-7.9,11.5-11.8c25.6,24.2,51.2,48.4,74.2,70.2c-27.4,31.1-50.7,57.6-74,84   C279.5,268,275.7,264.1,271.8,260.3z"/>
                        </g>
                    </svg>
                    </span>
                    <!-- <img src="<?php //echo mystaff_training_plugin_dir_folder.'/images/logout.png'; ?>" /> -->
                </a>
            </div>

            <div class="custom-site-logo">
            
                <?php
                mystaff_training_staff_training_get_custom_logo_function();
                ?>

            </div>



            <?php 
    
            $completed_learning_modules;
            
            foreach ($learning_modules as $key => $value) {

                if($value['is_all_complated'] != '' && $value['active'] == 1 ){

                    $completed_learning_modules[] = $value;
                    
                }

            }



            if(!empty($completed_learning_modules)){

                echo '<div class="completed-list"><label>Completed</label><ul>';

                // pr($completed_learning_modules);

                foreach ($completed_learning_modules as $key => $value) {
                    $score_arr = $score_quizarr = array();
                    $quiztable = $wpdb->prefix.'quiz_details';
                    $quiz_user = $wpdb->prefix.'quiz_user_details';
                    $score_sql = $wpdb->get_results("SELECT quizid,score FROM {$wpdb->prefix}quiz_user_details WHERE userid={$user_id} and quizid IN (SELECT quizid FROM {$wpdb->prefix}quiz_details WHERE sectionid={$value['id']}) ",ARRAY_A);
                    foreach($score_sql as $row) {
                        
                        $score_data = json_decode($row['score'],true);
                        $score_arr[] = $score_data['percentage'];
                        // $score_sql_row = $wpdb->get_row("SELECT subsection_title from {$wpdb->prefix}quiz_details where quizid = {$row['quizid']}",ARRAY_A);
                        $score_sql_row = $wpdb->get_row(
                            $wpdb->prepare(
                                "SELECT subsection_title FROM {$wpdb->prefix}quiz_details WHERE quizid = %d",
                                $row['quizid']
                            ),
                            ARRAY_A
                        );
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
                        $average = '<img src="'.mystaff_training_plugin_dir_folder.'/images/'.$cls.'"/>';
                    }else{
                        $average = '';
                    }
                    
                    ?>
                    <li class="accordion-title">
                        <div class="accordian-image">
                            <?php echo $average; ?>
                        </div>
                        <a href="javascript:void(0);" class="show-popup accordian-text" data-showpopup="<?php echo esc_attr($key); ?>">
                            <?php echo esc_html($value['title']); ?>
                            <!-- <span class="dashicons dashicons-plus-alt2"></span> -->
                        </a>
                    </li>

                    <div class="overlay-content popup" id="cmplt_<?php echo esc_attr($key); ?>">
                        <button class="close-btn">x</button>
                        <div class="learning-modules box-shadows"> 

                            <div class="top-section">

                                <div class="learning-img-btn">

                                    <div class="img-wrapper">

                                        <?php if(!empty($value['image_icon'])){                                 
                                                
                                            echo '<img src="'.esc_url($value['image_icon']).'"/>';

                                        } ?>

                                    </div>

                                    <h3><?php echo esc_html($value['title']);?></h3>

                                    <?php echo mystaff_training_get_current_steps($value);?>

                                </div>

                                <div class="learning-modules-content">

                                    <ul>

                                        <?php 

                                        if(!empty($value['pages'])){
                                            
                                            $total_steps = 0;

                                            $total_completed_step = 0;

                                            foreach ($value['pages'] as $key => $learning_subsec) {
                                                
                                                $complated_class = '';

                                                if($learning_subsec['status'] == 'completed'){

                                                    $complated_class = 'completed';

                                                    $total_completed_step++;
                                                    $sis = round($score_quizarr[$key]); //$sis = section individual score
                                                    
                                                    if($sis == '100') {
                                                        $complated_class .= ' green-check';
                                                    }elseif($sis < '100' || $sis == ''){
                                                        $complated_class .= ' red-check';
                                                    }

                                                }else{
                                                    $complated_class = '';
                                                }
                                                
                                                echo '<li class="'.esc_attr($complated_class).'"  ><a href="'.$learning_subsec['sub_start_url'].'?step='.urlencode($learning_subsec['sub_title']).'">'.$learning_subsec['sub_title'].'</a></li>';

                                                $total_steps++;
                                                
                                            }

                                        }

                                        ?>

                                    </ul>

                                </div>

                            </div>

                            <?php 
                            if($avg == ''){
                                echo mystaff_training_get_progress_bar($total_steps, $total_completed_step );
                            }else{
                                echo mystaff_training_get_quiz_progress_bar($avg);
                            } 
                            ?>
                        </div>
                    </div>
                    <?php
                }

                echo '</ul></div>';

            }
            ?>
            <div class="overlay-bg">
            </div>
            <?php  //if(current_user_can( 'administrator' )) {  
                $wallet_balance = mystaff_training_staff_training_coin_shopping_get_wallet_balance($user_id);
                ?>
            <div class="overlay-content popup" id="atlshopmodal" tabindex="-1" role="dialog" aria-labelledby="atlshopmodalLabel" aria-hidden="true" style="display:none;">
                <div class="modal-dialog atl-sel-prod" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="atlshopmodalLabel">Items</h5>
                        <a href="javascript:void(0);" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>
                    </div>
                    <div class="modal-body">
                        <?php  
                        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}atl_products",ARRAY_A);
                            if(!empty($results)) { ?>
                                <ul>
                                <?php foreach($results as $plist) { 
                                    if($wallet_balance >= $plist['product_price']) {
                                        $text = 'Buy Now';
                                        $disabled = '';
                                    }else{
                                        $text = 'Buy Now';
                                        $disabled = 'disabled';
                                    }
                                    ?>
                                    <li class="<?php echo esc_attr($class); ?>">
                                        <div class="image">
                                            <img src="<?php echo wp_get_attachment_image_src($plist['image_icon'])[0]; ?>" height="75px" width="75px"/>
                                        </div>
                                        <div class="info">
                                            <span><?php echo esc_html($plist['product_name']);?></span>
                                            <span><?php echo esc_html($plist['product_price']);?>pts</span>
                                        </div>
                                        <div class="purchase">
                                            <a href="javascript:void(0);" data-pid="<?php echo esc_attr($plist['id']); ?>" data-price="<?php echo esc_attr($plist['product_price']);?>" class="add-to-cart <?php echo esc_html($disabled); ?>"><?php echo esc_html($text); ?></a>
                                        </div>
                                        <div class="shop-loader">
                                        <img src="<?php echo esc_url(mystaff_training_plugin_dir_folder.'/templates/loading.gif'); ?>" height="30px" width="30px">
                                        </div>
                                    </li>
                                    
                                <?php } ?>
                                </ul>
                            <?php } ?>
                    </div>
                    <div class="modal-footer">
                        
                        <!-- <a href="javascript:void(0);" class="btn btn-primary">Proceed to buy</a> -->
                    </div>
                    </div>
                </div>
            </div>
            <?php //} ?>
            <div class="shop-button">
                <?php //if(current_user_can( 'administrator' )) {
                    $wallet_balance = mystaff_training_staff_training_coin_shopping_get_wallet_balance($user_id);
                    $wallet_balance = !empty($wallet_balance) ? $wallet_balance : 0;
                ?>
                    <button class="btn btn-shop" data-toggle="modal" data-target="#atlshopmodal">Shop <?php echo esc_html($wallet_balance).'pts'; ?></button>
                    
                <?php //} ?>
            </div>


        </div>

        <div class="content-right">
            
            <div class="logout_link">
                
                <a href="javascript:void(0);" class="show-popup accordian-text ldrbrd-popup" data-showpopup="leaderbrd">
                    <span class="lg-text">Leaderboard</span>
                    <span class="lg-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard2-data" viewBox="0 0 16 16">
                            <path d="M9.5 0a.5.5 0 0 1 .5.5.5.5 0 0 0 .5.5.5.5 0 0 1 .5.5V2a.5.5 0 0 1-.5.5h-5A.5.5 0 0 1 5 2v-.5a.5.5 0 0 1 .5-.5.5.5 0 0 0 .5-.5.5.5 0 0 1 .5-.5h3Z"/>
                            <path d="M3 2.5a.5.5 0 0 1 .5-.5H4a.5.5 0 0 0 0-1h-.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1H12a.5.5 0 0 0 0 1h.5a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-12Z"/>
                            <path d="M10 7a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0V7Zm-6 4a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0v-1Zm4-3a1 1 0 0 0-1 1v3a1 1 0 1 0 2 0V9a1 1 0 0 0-1-1Z"/>
                        </svg>
                    </span>
                </a>
                <div class="overlay-content popup" id="cmplt_leaderbrd">
                    <button class="close-btn">x</button>
                    <div class="learning-modules"> 
                        
                            <?php 
                            
                            $table_name = $wpdb->prefix . 'quiz_details';
                            $quiz_table_name = $wpdb->prefix . 'quiz_user_details';
                            $quiz_section_score = $wpdb->prefix . 'quiz_section_score';
                            $q1 = $wpdb->get_results("SELECT qud.score,qud.userid,qud.quizid,qd.sectionid FROM {$wpdb->prefix}quiz_user_details AS qud INNER JOIN {$wpdb->prefix}quiz_details AS qd ON qd.quizid = qud.quizid order BY qud.userid, qd.sectionid",ARRAY_A );
                            $sp = array();
                            $exclude_users = (!empty(get_option('user_id_list'))) ? get_option('user_id_list') : array() ;
                            foreach ($q1 as $key2 => $value2) {  
                                $pscore =  json_decode($value2['score']);
                                $score = $pscore->percentage; 
                                $quiz_score = round($score);
                                $learning_modules2 = get_user_meta($value2['userid'] , 'learning_modules_progress', true);
                                $learning_modules2 = unserialize($learning_modules2);
                                $isassigned = $learning_modules2[$value2['sectionid']];                
                                if($isassigned['is_all_complated'] == 1 &&  $isassigned['active'] == 1 ){  //
                                    if(!in_array($value2['userid'], $exclude_users)){
                                        $sp[$value2['userid']][$value2['sectionid']][] = $quiz_score;
                                    }
                                }
                                
                            }
                            ?>
                            <div class="learning-main-section dash-lb">
                                
                                <?php 
                                    $sortingarr = array();
                                            foreach ($sp as $lbk => $lbv) {               
                                                $display_name = mystaff_training_staff_training_get_display_name($lbk);
                                                $name =  $display_name;
                                                $weight_arr = 0;
                                                $gs = get_option('gold_score_min');
                                                $gw = get_option('gold_score_weight');
                                                
                                                $ss = get_option('silver_score_min');
                                                $sw = get_option('silver_score_weight');
                                            
                                                $bs = get_option('bronze_score_min');
                                                $bw = get_option('bronze_score_weight');
                                            
                                                $fs = get_option('fail_score_min');
                                                $fw = get_option('fail_score_weight');
                                                
                                                if($name) { 
                                                    foreach($lbv as $sid => $sval) {  
                                                        $score = $lbv[$sid];                          
                                                        if(!empty($score)) :
                                                            $t_quiz_score = array_sum($score) / count($score);
                                                        endif;
                                                
                                                        $quiz_score = round($t_quiz_score);
                        
                                                        if($quiz_score == $gs):
                                                            
                                                            $weight = $gw;
                                                        elseif($quiz_score >= $ss && $quiz_score <= ($gs - 1)):
                                                            
                                                            $weight = $sw;
                                                        elseif($quiz_score >= $bs && $quiz_score <= ($ss - 1)):
                                                            
                                                            $weight = $bw;
                                                        elseif($quiz_score >= $fs && $quiz_score <= ($bs - 1)):
                                                            
                                                            $weight = $fw;
                                                        endif;
                                                            
                                                        if($weight == $gw): 
                                                            $weight_arr += $weight;
                                                             
                                                        elseif($weight == $sw):
                                                            $weight_arr += $weight;
                                                            
                                                            
                                                        elseif($weight == $bw):
                                                            $weight_arr += $weight;
                                                            
                                                        elseif($weight == $fw):
                                                            $weight_arr += $weight;
                                                            
                                                        endif; 
                                                    }

                                                }
                                                $sortingarr[] = array('name' => $name, 'score' => $weight_arr);
                                            }
                                            
                                            $sortingarr = wp_list_sort( $sortingarr, 'score', 'DESC' );
                                            $uid=get_current_user_id();
                                            $user_info = get_userdata($uid);
                                            $myScores=0;
                                            $myLeague=0;
                                            $leaguesEnabled=get_option('if_leagues');
                                            $league1Score=get_option('league1_score_min');
                                            $league2Score=get_option('league2_score_min');
                                            $league3Score=get_option('league3_score_min');
                                            $league1Title=get_option('league1_title');
                                            $league2Title=get_option('league2_title');
                                            $league3Title=get_option('league3_title');
                                            
                                            
                                            foreach($sortingarr as $idx => $srtv) {
                                                if($srtv['name']==$user_info->display_name){
                                                    $myScores=$srtv['score'];
                                                    break;
                                                }   
                                            }
                                //          $myScores=34; //for testing
                                            
                                            if($myScores>=$league1Score){
                                                $myLeague=1;
                                            }
                                            if($myScores>=$league2Score){
                                                $myLeague=2;
                                            }
                                            if($myScores>=$league3Score){
                                                $myLeague=3;
                                            }
                                
                                
                                ?>
                                <div class="learning-top-header leader-top-header">
                                    <h4  style="margin-bottom: -5px;">Leaderboard
                                    <?php 
                                    
                                    if($leaguesEnabled=='y'){
                                        if($myLeague==1){
                                            echo " - ". esc_html($league1Title);
                                        }else if($myLeague==2){
                                            echo " - ".esc_html($league2Title);
                                        }else if($myLeague==3){
                                            echo " - ".esc_html($league3Title);
                                        }else{
                                            echo " - No League";
                                        }   
                                    }
                                    ?>
                                    </h4>
                                    
                                    <small style="font-size:12px;margin-left:15px">
    <?php
    if ($leaguesEnabled == 'y') {
        if ($myLeague == 1) {
            echo esc_html("To enter the " . $league2Title . ", score " . $league2Score . " or more.");
        } else if ($myLeague == 2) {
            echo esc_html("To enter the " . $league3Title . ", score " . $league3Score . " or more.");
        } else if ($myLeague == 3) {
            echo esc_html("Well done! You are now in the " . $league3_title . ".");
        } else {
            echo esc_html("To enter the " . $league1Title . ", score " . $league1Score . " or more.");
        }
    }
    ?>
</small>

                                </div>
                                <table class="table cell-border" id="lbscore" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                            
                                    <tbody class="people-list">
                                            <?php   
                                            foreach($sortingarr as $idx => $srtv) {
                                                if($leaguesEnabled=='y'){
                                                    if(($myLeague==1 && $srtv['score']<$league2Score) || ($myLeague==2 && $srtv['score']>=$league2Score && $srtv['score']<$league3Score) || ($myLeague==3 && $srtv['score']>=$league3Score)){
                                                        //do nothing for now
                                                    }else{
                                                        continue;   
                                                    }                                                   
                                                }
                                                ?>
                                                <tr>
                                                    <td class="people-name">
                                                        <?php echo  esc_html($srtv['name']);?>
                                                    </td>
                                                    <td>                  
                                                    <?php echo esc_html($srtv['score']);?>
                                                    </td>
        
                                                    
                                                </tr>
                                                <?php
                                                } 
                                            
                                        ?>  
                                    </tbody>
                                </table>
                            </div>
                        
                    </div>
                </div>
                
                <a href="<?php echo esc_url(site_url()).'/wp-login.php?action=lostpassword'; ?>">
                    <span class="lg-text">Change Password</span>
                    <span class="lg-icon">
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                            viewBox="0 0 390 391" style="enable-background:new 0 0 390 391;" xml:space="preserve">
                            <!-- <style type="text/css">
                                .st0{fill:#4d9ffc;}
                            </style> -->
                            <g>
                                <path class="st0" d="M387.9,332.1c0,1.8-0.2,2.7-2.4,2.7c-30.3-0.1-60.7,0-91.4,0c0.6-2,2.1-3,3.1-4.3
                                    c10.2-11.8,21.3-22.8,30.4-35.6c15.2-21.6,24.6-45.5,28.6-71.6c0.4-2.6,0.7-5.2,1-7.8c0.5-9.8,0.9-19.7,0-29.6
                                    c-2.6-29.8-12.4-56.8-29.6-81.2C314,85.6,297.2,70,277.1,58c-19.5-11.6-40.6-18.9-63.2-21.8c-24.2-3.1-47.8-1-71,6.4
                                    c-2.3,0.7-2.8-0.2-3.3-2c-2.5-7.6-4.9-15.2-7.7-22.7c-1-2.7-0.1-3.4,2.2-4.1c13.2-4.1,26.6-6.7,40.3-8.2c0.9-0.1,2.1,0.3,2.7-0.8
                                    h30c4.9,1.6,10,1.4,15,2.2c35.4,5.4,66.9,19.5,94.5,42.1c16.6,13.6,30.6,29.5,41.9,47.8c10.8,17.5,18.9,36.2,23.7,56.2
                                    c4.9,20.6,7.1,41.3,4.8,62.5c-0.8,8.3-1.6,16.5-3.4,24.7c-3.6,16.5-9,32.3-16.8,47.4c-2.9,5.6-6,11.1-9.4,17.2h24.7
                                    c5.8,0,5.8,0,5.8,5.6C387.8,317.6,387.8,324.9,387.9,332.1z"/>
                                <path class="st0" d="M261.1,378.6c-14.5,4.8-29.4,7.9-44.6,9.4c-0.9,0.1-2-0.4-2.7,0.7h-30.8c-4.1-1.7-8.5-1.4-12.7-2
                                    c-50.9-7.9-92.5-32.1-124.6-72.3C24,287.3,10.9,256.2,5.9,221.8c-0.6-4.1-0.5-8.3-2-12.3v-30.8c0.9-0.4,0.7-1.2,0.7-2
                                    c2.7-30.4,12-58.6,27.9-84.7c0.5-0.9,1.5-1.7,1.2-3.3H3.8v-30h93.2c0.6,1.3-0.5,1.9-1.1,2.5c-5.4,6.1-10.7,12.2-16.3,18.1
                                    c-6.3,6.8-12,14-17.2,21.5c-7.9,11.4-14,23.8-18.8,36.8c-8,21.7-10.6,44.2-9.6,67.2c1.3,11.8,3.1,23.5,6.4,34.9
                                    c8,27.3,22.1,50.9,42.2,70.9c25.6,25.5,56.5,40.8,92.2,46.3c25.7,3.9,51,1.6,75.8-6.6c3-1,4.3-0.8,5.3,2.5
                                    c2.2,7.1,4.8,14.2,7.6,21.1C264.6,376.9,263.8,377.7,261.1,378.6z"/>
                                <path class="st0" d="M285.9,298.3c-0.2-5.4-0.1-10.7-0.1-16.1c0-31.7-0.1-63.4-0.1-95.2c0-10.9,0-21.8,0.2-32.6
                                    c0-2.2-0.6-2.8-2.8-2.7c-6.1,0.1-12.2,0-18.4,0c-8.9,0-8.8,0-9-8.8c-0.2-10.6,1-21.3-0.9-31.8c-5.5-30.4-33.5-51.9-63.9-49.2
                                    c-31.3,2.8-55,28.5-55.1,60c0,9-0.1,18,0,27c0,2.4-0.7,2.9-3,2.9c-8.1-0.1-16.2,0-24.4-0.1c-2.1,0-2.8,0.5-2.8,2.7
                                    c0.1,12.9,0.1,25.7,0.2,38.6c0,35,0,69.9-0.2,104.9c0,3.1,0.8,3.9,3.9,3.9c25.6-0.1,51.2-0.1,76.8-0.1c32,0,64,0,95.9,0.1
                                    C285.2,301.8,286,301.2,285.9,298.3z M166.6,115.9c2.9-15.7,15.9-25.2,31.9-24c15,1.1,26.9,13.9,27.3,28.9c0.2,9.4,0,18.7,0.1,28.1
                                    c0,2.3-0.7,2.9-3,2.9c-9-0.1-18,0-27,0c-9.1,0-18.2,0-27.4,0c-1.3,0-2.8,0.4-2.8-1.9C166.2,138.5,164.5,127,166.6,115.9z
                                    M255.9,267.6c0,3.4-0.9,4.3-4.3,4.3c-37.3-0.1-74.7-0.1-112,0c-3.1,0-3.9-0.8-3.9-3.9c0.1-25,0-50-0.1-74.9
                                    c0.1-2.8,0.3-5.5,0.1-8.2c-0.2-2.5,0.7-3.1,3.1-3.1c10.7,0.1,21.5,0.1,32.2,0.1h79c5.5,0,5.5,0,5.8,5.3
                                    C255.9,213.9,255.8,240.7,255.9,267.6z"/>
                                <path class="st0" d="M210.8,226.6c0.1,8.3-6.6,15.1-14.8,15.1c-8.3,0.1-15.1-6.6-15.1-14.8c-0.1-8.3,6.6-15.1,14.8-15.1
                                    C203.9,211.7,210.7,218.3,210.8,226.6z"/>
                            </g>
                        </svg>
                    </span>
                </a>
                <a href="<?php echo wp_logout_url(site_url().'/wp-admin/');?>">
                    <span class="lg-text">
                        Logout
                    </span>
                    <span class="lg-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 368 390" style="enable-background:new 0 0 368 390;" xml:space="preserve">
                            <g>
                                <path d="M181.7,381.1c-37.6,0-75.9,4.1-112.7-1.1c-40.4-5.7-58.3-37.1-58.8-76.4C9.3,231.3,8.8,159,10.4,86.7   c1.1-51.3,29.2-79,81.4-79.8c30.2-0.5,60.5-0.1,90.7-0.1c0.4,5.8,0.9,11.6,1.3,17.5c-19,1.6-37.9,4.5-56.9,4.7   c-91.1,1.1-94.6,4.5-94.7,97.6c0,52.8,1.9,105.6-0.5,158.3c-2.5,55.2,20,80.4,75.9,76.1c24.4-1.9,49-0.3,73.6-0.3   C181.3,367.5,181.5,374.3,181.7,381.1z"/>
                                <path d="M271.8,260.3c12.1-14.6,24.3-29.1,44.3-53.1c-76.5,0-145.6,0-214.7,0c-0.2-6.5-0.5-13-0.7-19.5c68.9,0,137.8,0,220.1,0   c-23.4-27.8-36.2-43.1-49.1-58.3c3.8-3.9,7.6-7.9,11.5-11.8c25.6,24.2,51.2,48.4,74.2,70.2c-27.4,31.1-50.7,57.6-74,84   C279.5,268,275.7,264.1,271.8,260.3z"/>
                            </g>
                        </svg>
                    </span>
                    
                </a>
            </div>
            
            <div class="info-content box-shadows">
                <?php 
                
                    if(empty($completed_learning_modules) || count($completed_learning_modules) == 0 && get_option('first_wlcm_message') != '') {
                        echo '<p>'.esc_attr( get_option('first_wlcm_message') ).'</p>';
                        
                    }else if(!empty($completed_learning_modules) && count($completed_learning_modules) > 0 && get_option('scnd_cmpltd_message') != '') {
                        echo '<div class="second-msgs-slider-w">';
                            echo '<div class="second-msgs-slider">';
                            foreach(get_option('scnd_cmpltd_message') as $k => $msg) {

                                echo '<p class="second-msg-p">'.esc_attr( $msg ).'</p>';
                            }
                            echo '</div>';
                            if(count(get_option('scnd_cmpltd_message')) > 1) {
                                echo '<div class="nav-wrap">';
                                    echo '<button id="prev" class="navigation">Back</button>';
                                    echo '<button id="next" class="navigation">Next</button>';
                                echo '</div>';
                            }
                        echo '</div>';
                    }else{
                        echo 'Welcome to Wp staff training. Here’s where you’ll find the training courses available to you. Complete the sections below. Click START to begin or CONTINUE to resume where you left off.';
                        
                    }
                ?>
                
            </div>
        



            <div class="block-section" data-tg-tour="This is the second step" data-tg-order="1">

                <?php 
    
    
                if(!empty($learning_modules)){
                    
                    foreach ($learning_modules as $key => $user_assigned) {
                        
                        if($user_assigned['active'] == 1 && $user_assigned['is_all_complated'] == ''){
                            $flag_unassigned=false;                 
                            ?>

                            <div class="learning-modules box-shadows" >

                                <div class="top-section">

                                    <div class="learning-img-btn">

                                        <div class="img-wrapper">

                                            <?php 
                                            
                                            if(!empty($user_assigned['image_icon'])){                                   

                                                echo '<img src="' . esc_url($user_assigned['image_icon']) . '"/>';

                                            } ?>

                                        </div>

                                        <h3><?php echo esc_html($user_assigned['title']);?></h3>

                                        <?php echo mystaff_training_get_current_steps($user_assigned);?>

                                    </div>

                                    <div class="learning-modules-content">

                                        <ul>

                                            <?php 

                                            if(!empty($user_assigned['pages'])){

                                                $total_steps = 0;

                                                $total_completed_step = 0;

                                                foreach ($user_assigned['pages'] as $key => $learning_subsec) {

                                                    $complated_class = '';

                                                    if($learning_subsec['status'] == 'completed'){

                                                        $complated_class = 'class="completed"';

                                                        $total_completed_step++;

                                                    }
                                                    
                                                    echo '<li '.esc_attr($complated_class).' ><a href="'.$learning_subsec['sub_start_url'].'?step='.urlencode($learning_subsec['sub_title']).'">'.$learning_subsec['sub_title'].'</a></li>';

                                                    $total_steps++;

                                                }

                                            }

                                            ?>

                                        </ul>

                                    </div>

                                </div>

                                <?php echo mystaff_training_get_progress_bar($total_steps, $total_completed_step ); ?>

                            </div>

                    <?php } }
                    
                    

                }if($flag_unassigned) {
                    
                    $uid=get_current_user_id();
                    $user_info = get_userdata($uid);
                    $self_assigned_date = $user_info->self_assigned_date;
                    $user_cat=$user_info->user_cat;
                    $currentDate = new DateTime();
                    $selfAssignedDate = new DateTime($self_assigned_date);
                    $interval = $currentDate->diff($selfAssignedDate);
                    $daysDifference = $interval->days;
                    $daysWait=get_option('self_assign_wait_days');
                            
                     if ($self_assigned_date==null || $daysDifference >=$daysWait){
                         $allowSelfAssign=true;      
                     }else{
                        $allowSelfAssign=false;// temporarily false
                     }
                             
                    foreach ($learning_modules_unassigned as $key => $user_assigned) {
                        
                        if(get_option('if_self_assign')!='y' ||$user_assigned['cat']=="None"){
                            continue;
                        }
                             
                              if($user_assigned['active'] == 1 && $user_assigned['is_all_complated'] == '' && ($user_assigned['cat']=='All' || $user_assigned['cat']==$user_cat)){
                        
                        ?>

                        <div class="learning-modules box-shadows" style="box-shadow: 0 0 10px #C15EAB">
                            <div class="top-section">
                                <div class="learning-img-btn">
                                    <div class="img-wrapper">
                                        <?php
                                        if(!empty($user_assigned['image_icon'])){
                                            echo '<img src="' . esc_url($user_assigned['image_icon']) . '"/>';                                        } ?>
                                    </div>
                                    <h3><?php echo esc_html($user_assigned['title']); ?></h3>
                                </div>
                                <div class="learning-modules-content">
                                    <ul>
                                        <?php
                                        if(!empty($user_assigned['pages'])){
                                            foreach ($user_assigned['pages'] as $key2 => $learning_subsec) {
                                                echo '<li><a>' . esc_html($learning_subsec['sub_title']) . '</a></li>';
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <?php
                            $sectionId=$key;
                            ?>
                            <!--<button class="btn " id="selfAssignButton" data-section-id="52" style="height:40px; padding: 10px 20px; font-size: 16px; font-weight:400">Self Assign</button>-->
                            <button
                            class="btn selfAssignButton" data-section-id="<?php 
                           echo esc_attr($sectionId); 
                            ?>" 
                            style="height:40px; padding: 10px 20px; font-size: 16px; font-weight:400 "
                            <?php echo $allowSelfAssign ? '' : 'disabled'; ?>
                            >
                            <?php
                            if($allowSelfAssign){
                              echo   "Self Assign";
                            }else{
                                echo esc_html("Wait for " . ($daysWait - $daysDifference) . " days");
                            }
                            ?>
                            </button>
                        </div>
                <?php }    
                    }
                }
                ?>
                
            

            </div>

        </div>

    </div>

    

</div>


<style>
    .tooltip {
  position: relative;
  display: inline-block;
  margin-top:20px;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 250px;
  background-color: black;
  color: #fff;
  font-size:12px;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;

  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
}
    
</style>




<script>

    jQuery(document).ready(function($) {
    // $('#selfAssignButton').on('click', function(e) {
        $(document).on('click', '.selfAssignButton', function(e) {


        var sectionId = $(this).data('section-id');

        <?php
            $user_ids=array();
            $user_ids[]=get_current_user_id();
            $user_ids_json = json_encode($user_ids);
        ?>
    
    
    
    
        e.preventDefault();
        var user_ids = <?php echo $user_ids_json; ?>;
        var user_ids = JSON.stringify(user_ids);
        var section_id = sectionId; //Slab Course


console.log(user_ids);
        
        jQuery.ajax({

            url: '<?php echo site_url(); ?>/wp-admin/admin-ajax.php',

            type: 'post',

            data: {

                action: "mystaff_training_staff_training_learning_modules_assign_users",

                section_id: section_id,

                user_ids : user_ids,
                
                add_user : true,

            },

            success:function(data) {
                
                
                if(data == 'assigned'){

                    Swal.fire({

                        title: 'Assigned!',

                        text: "Section has been assigned to you",

                        icon: 'success',

                        confirmButtonText: 'Ok',

                    }).then((result) =>{

                        if (result.isConfirmed) {

                            window.location.href = '<?php echo site_url(); ?>/dashboard';

                        }

                    });

                }

            }

        });
    });
});
</script>


<?php get_footer(); ?>