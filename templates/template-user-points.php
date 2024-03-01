<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $wpdb;
?>
<div class="atl-main-section">
    <div class="atl-top-header">
        <h1>Points</h1>
        
    </div>
    <?php 
    
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}coin_wallet",ARRAY_A);
    
    if(!empty($results)) {
    ?>
    <div class="atl-content shopping-list">
    <div class="table-responsive">
    <table id="users_table"  class="table cell-border dataTable no-footer" cellspacing="0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Points</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($results as $row) : 
                        if(get_userdata( $row['user_id'])) :     
                ?>
                <tr>
                    
                    <td><?php echo esc_html(get_userdata($row['user_id'])->data->user_login); ?></td>
                    <td class="update-userpoints" data-userid="<?php echo esc_attr($row['user_id']); ?>">
                        <span><?php echo esc_html($row['total_coins']); ?></span>
                        <div class="frm_updateuser_points" style="display:none;">
                            <input type="text" name="user_coins" class="user_coins" value="<?php echo esc_attr($row['total_coins']); ?>" >
                            <button class="sbmt_uc"><i class="dashicons dashicons-yes"></i></button>
                        </div>
                    </td>
                    
                </tr>
                <?php 
                    endif;
                endforeach; ?>
            </tbody>
        </table>
    </div>
        
    </div>
    <?php 
    } ?>
</div>



<script>
    jQuery(document).ready(function($){
        $('#users_table').DataTable({
            bLengthChange:false,
            searching:false,
            paging:false,
            bInfo:false,

        });
       
        $('#users_table').on('click','.update-userpoints',function(e){
            e.stopPropagation();
            $(this).find('span').hide();
            $(this).find('.frm_updateuser_points').show();

        });

        $('body').on('click',function(){
            $('.update-userpoints').find('span').show();
            $('.update-userpoints').find('.frm_updateuser_points').hide();
        });
        
        $('#users_table').on('click','.sbmt_uc',function(e){
            e.preventDefault();
            var frm_elem = $(this).parents('.frm_updateuser_points');
            var elem = frm_elem.children('input[name="user_coins"]');
            
            jQuery.ajax({
                url: '<?php echo esc_url(site_url('/wp-admin/admin-ajax.php')); ?>',
                type: "POST",
                data: {
                    action: 'mystaff_training_staff_training_modify_user_wallet_points',
                    user_id : $(this).parents('td').attr('data-userid'),
                    points : elem.val(),
                },
                success: function(response) {
                    var res = $.parseJSON(response); 
                    console.log(res);
                    if(res.success == 'updated'){
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Points updated!',
                            text: "Points has been changed.",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        frm_elem.siblings('span').text(res.updated_points).show();
                        frm_elem.hide();
                            
                    }
                }
            });
        });

        

        
    });
</script>