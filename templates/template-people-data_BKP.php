<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if (!defined('st_my_plugin_dir_folder')) {
    define('st_my_plugin_dir_folder', plugin_dir_url(__File__));
}
//quiz_details, quiz_user_details, learning_sections
global $wpdb;
$table_name = $wpdb->prefix . 'quiz_details';
$quiz_table_name = $wpdb->prefix . 'quiz_user_details';
$q1 = $wpdb->get_results("SELECT qud.score,qud.userid,qud.quizid,qd.sectionid FROM `wp_quiz_user_details` AS qud INNER JOIN `wp_quiz_details` AS qd ON qd.quizid = qud.quizid order BY qud.userid, qd.sectionid",ARRAY_A );
$kp = array();
foreach ($q1 as $key => $value) {  
    $pscore =  json_decode($value['score']);
    $score = $pscore->percentage; 
    $quiz_score = round($score);
    $kp[$value['userid']][$value['sectionid']][] = $quiz_score;
    
}
?>
    
<div class="learning-main-section">
    <div class="learning-top-header People-top-header">
        <h1>Staff</h1>
    </div>
    <div class="sub-section">
        <div class="sub-sec-left">
            <h3>
                <?php echo "Name"; ?>
            </h3>
        </div>
        <div class="sub-sec-right">
            <h3>
                <?php echo "Score"; ?>
            </h3>
        </div>
    </div>
    <div class="people-list">
        <ul>
            <?php foreach ($kp as $key => $value) {                
                $display_name = myst_staff_training_get_display_name($key);
                $name =  $display_name;   
                $user_id = $key;  
                if($name) {   ?>
                <li>
                    <div class="people-name">                     
                    <!-- Trigger the modal with a button -->
                    <a  class="name-info" data-toggle="modal" data-target="#myModal-<?php echo esc_attr($user_id);?>">
                    <?php echo $name;?></a>                  
                        <!-- Modal -->
                        <div class="modal fade" id="myModal-<?php echo esc_attr($user_id);?>" role="dialog">
                            <div class="modal-dialog">                            
                            <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title"><?php echo esc_attr($name) ;?></h4>
                                    </div>
                                    <div class="modal-body">                                        
                                        <?php
                                           foreach($value as $sid => $sval) {                        
                                                $learning_modules = get_user_meta( $user_id , 'learning_modules_progress', true);                                        
                                                $learning_modules = unserialize($learning_modules);
                                                $results = $wpdb->get_results("SELECT * FROM wp_quiz_details as qt INNER JOIN wp_quiz_user_details as qut ON qt.quizid = qut.quizid WHERE qt.sectionid = $sid AND qut.userid = $user_id",ARRAY_A);  
                                                $final_array= array();
                                                if($results) { ?>
                                                    <h3><?php echo esc_html($learning_modules[$sid]['title']);?></h3>
                                                    <?php
                                                        foreach($results as $row){
                                                            $subsection_title = explode("_",$row['subsection_title'])[1];                                              
                                                            $score = json_decode($row['score'],true);
                                                            $percentage = $score['percentage'];
                                                            $scoredata = $score['scoredata'];                                                                                                         
                                                            echo '<p>'.esc_html($subsection_title).'</p>';
                                                            echo '<p>'.esc_html($percentage).'</p>';                                         
                                                        }    
                                                   }
                                        
                                            } //for each value as sid   
                                        ?>  
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>                            
                            </div>
                        </div>                    
                    </div>
                    <div class="people-per-wrapper">                  
                        <?php
                        foreach($value as $sid => $sval) {  
                            $score = $value[$sid];                          
                            if(!empty($score)) :
                                $quiz_score = array_sum($score) / count($score);
                            endif;
                            if($quiz_score == 100):
                                $cls = 'trophygold.png';
                            elseif($quiz_score >= 75 && $quiz_score <= 99):
                                $cls = 'trophysilver.png';
                            elseif($quiz_score >= 50 && $quiz_score <= 74):
                                $cls = 'trophybronze.png';
                            elseif($quiz_score >= 0 && $quiz_score <= 49):
                                $cls = 'trophyx.png';
                            endif;    
                            ?>
                       
                        <div class="pro-percentage">                
                        <img src="<?php echo st_my_plugin_dir_folder.'/images/'.$cls; ?>" width="25px;"/>
                            <p><?php echo  esc_html(round($quiz_score)); ?>%</p>
                        </div> 
                        <?php } ?>          
                   </div>  
               </li>
            <?php
                } //if name
        } //main foreach
         ?>                         
        </ul>
    </div>
</div>
