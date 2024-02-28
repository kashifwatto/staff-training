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

<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.4/sweetalert2.min.css" integrity="sha512-y4S4cBeErz9ykN3iwUC4kmP/Ca+zd8n8FDzlVbq5Nr73gn1VBXZhpriQ7avR+8fQLpyq4izWm0b8s6q4Vedb9w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.4/sweetalert2.all.js" integrity="sha512-aYkxNMS1BrFK2pwC53ea1bO8key+6qLChadZfRk8FtHt36OBqoKX8cnkcYWLs1BR5sqgjU5SMIMYNa85lZWzAw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.4/sweetalert2.min.js" integrity="sha512-vDRRSInpSrdiN5LfDsexCr56x9mAO3WrKn8ZpIM77alA24mAH3DYkGVSIq0mT5coyfgOlTbFyBSUG7tjqdNkNw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"> </script>

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/select/1.6.2/js/dataTables.select.min.js"></script>
<script src="<?php // echo get_stylesheet_directory_uri(); ?>/js/editor.dataTables.editor.min.js"></script> -->

<script>
    jQuery(document).ready(function($){
        $('#users_table').DataTable({
            bLengthChange:false,
            searching:false,
            paging:false,
            bInfo:false,
            //order: [[1, 'desc']],
            //paging: false,
            // columnDefs: [{
            //     orderable: false,
            //     targets: "no-sort"
            // }]
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
                    action: 'myst_staff_training_modify_user_wallet_points',
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
                        // window.location.href = '<?php //echo admin_url('admin.php?page=shop-items'); ?>';
                            
                    }
                }
            });
        });

        

        
    });
</script>