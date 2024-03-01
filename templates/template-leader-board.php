<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

//quiz_details, quiz_user_details, learning_sections
global $wpdb;
$table_name = $wpdb->prefix . 'quiz_details';
$quiz_table_name = $wpdb->prefix . 'quiz_user_details';
$quiz_section_score = $wpdb->prefix . 'quiz_section_score';

$q1 = $wpdb->get_results("SELECT qud.score,qud.userid,qud.quizid,qd.sectionid FROM {$wpdb->prefix}quiz_user_details AS qud INNER JOIN {$wpdb->prefix}quiz_details AS qd ON qd.quizid = qud.quizid order BY qud.userid, qd.sectionid",ARRAY_A );
$sp = array();
$exclude_users = (!empty(get_option('user_id_list'))) ? get_option('user_id_list') : array() ;
foreach ($q1 as $key => $value) {  
    $pscore =  json_decode($value['score']);
    $score = $pscore->percentage; 
    $quiz_score = round($score);
    $learning_modules = get_user_meta($value['userid'] , 'learning_modules_progress', true);
    $learning_modules = unserialize($learning_modules);
    $isassigned = $learning_modules[$value['sectionid']];                
    if($isassigned['is_all_complated'] == 1 &&  $isassigned['active'] == 1 ){  //
        if(!in_array($value['userid'], $exclude_users)){
            $sp[$value['userid']][$value['sectionid']][] = $quiz_score;
        }
    }
    
}

?>
<div class="learning-main-section">
    <div class="learning-top-header People-top-header">
        <h1>LeaderBoard Score</h1>
    </div>
    <table class="table cell-border" id="lbscore" cellspacing="0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Score</th>
                <th class="no-sort">Gold</th>
                <th class="no-sort">Silver</th>
                <th class="no-sort">Bronze</th>
                <th class="no-sort">Fail</th>
               
            </tr>
        </thead>
   
        <tbody class="people-list">

                <?php 
                foreach ($sp as $key => $value) {               
                    $display_name = mystaff_training_staff_training_get_display_name($key);
                    $name =  $display_name;   
                    $user_id = $key; 
                    
                    $weight_arr = 0;
                    if($name) { 
                     ?>
                    <tr>
                        <td class="people-name">
                            <h3 class="name-info"><?php echo esc_html($name);?></h3>
                        </td>
                        <td>                  
                            <?php
                            $gs = get_option('gold_score_min');
                            $gw = get_option('gold_score_weight');
                            
                            $ss = get_option('silver_score_min');
                            $sw = get_option('silver_score_weight');
                        
                            $bs = get_option('bronze_score_min');
                            $bw = get_option('bronze_score_weight');
                        
                            $fs = get_option('fail_score_min');
                            $fw = get_option('fail_score_weight');
                            
                            $gold = $silver = $bronze = $fail = 0;
                            foreach($value as $sid => $sval) { 
                                $score = $value[$sid];                          
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
                                    $gold += 1; 
                                elseif($weight == $sw):
                                    $weight_arr += $weight;
                                    $silver += 1;
                                    
                                elseif($weight == $bw):
                                    $weight_arr += $weight;
                                    $bronze += 1;
                                    
                                elseif($weight == $fw):
                                    $weight_arr += $weight;
                                    $fail += 1;
                                endif; 

                               /*  $learning_modules_new = get_user_meta($user_id , 'learning_modules_progress', true);
                                $learning_modules_new = unserialize($learning_modules_new);
                                $isassigned_new = $learning_modules_new[$sid];                
                                if($isassigned_new['is_all_complated'] == 1 && $isassigned_new['active'] == 1 ){   */
                                    /* $getweightofuser = $wpdb->get_results("SELECT * FROM {$quiz_section_score} WHERE userid={$user_id} AND sectionid={$sid}",ARRAY_A);
                                    
                                    foreach($getweightofuser as $wght){

                                        $weight_arr += $wght['score_weight'];

                                        if($wght['score_weight'] == get_option('gold_score_weight')):
                                            $gold += 1; 
                                        elseif($wght['score_weight'] == get_option('silver_score_weight')):
                                            $silver += 1;
                                            
                                        elseif($wght['score_weight'] == get_option('bronze_score_weight')):
                                            $bronze += 1;
                                            
                                        elseif($wght['score_weight'] == get_option('fail_score_weight')):
                                            $fail += 1;
                                        endif; 
                                    } */
                                //}
                              
                            }
                            echo esc_html($weight_arr);    
                            ?>
                        </td>  
                        <td>
                            <div class="pro-percentage">
                                <?php if($gold > 0) : ?>
                                    <img src="<?php echo esc_url(mystaff_training_plugin_dir_folder.'/images/trophygold.png'); ?>" width="25px;"/>
                                    <p><?php echo esc_html($gold); ?></p>
                                <?php endif; ?>
                            </div> 
                        </td>         
                        <td>
                            <div class="pro-percentage">
                                <?php if($silver > 0) : ?>
                                    <img src="<?php echo esc_url(mystaff_training_plugin_dir_folder.'/images/trophysilver.png'); ?>" width="25px;"/>
                                    <p><?php echo esc_html($silver); ?></p>
                                <?php endif; ?> 
                            </div>
                        </td>         
                        <td>
                            <div class="pro-percentage">
                                <?php if($bronze > 0) : ?>
                                    <img src="<?php echo esc_url(mystaff_training_plugin_dir_folder.'/images/trophybronze.png'); ?>" width="25px;"/>
                                    <p><?php echo esc_html($bronze); ?></p>
                                <?php endif; ?>
                            </div>  
                        </td>         
                        <td>
                            <div class="pro-percentage">
                                <?php if($fail > 0) : ?>
                                    <img src="<?php echo esc_url(mystaff_training_plugin_dir_folder.'/images/trophyx.png'); ?>" width="25px;"/>
                                    <p><?php echo esc_html($fail); ?></p>
                                <?php endif; ?>
                            </div>
                        </td>         
                    </tr>
                    <?php
                    } //if name
                } //main foreach
            ?>  
        </tbody>
    </table>
</div>


  <script>
    jQuery(document).ready(function () {
        jQuery('#lbscore').DataTable({
            order: [[1, 'desc']],
            paging: false,
            searching: false,
            bInfo: false,
            columnDefs: [{
                orderable: false,
                targets: "no-sort"
            }]
        });
    });
  </script>
 
