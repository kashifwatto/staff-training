<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $wpdb;

?>
<div class="atl-main-section">
    <div class="atl-top-header">
        <h1>Orders</h1>
        <!-- <a href="<?php echo admin_url('admin.php?page=shop-items'); ?>&action=add">+ Add New Product</a> -->
    </div>
    <?php 
    
    $results = $wpdb->get_results("SELECT *,ao.id as orderid FROM {$wpdb->prefix}atl_orders as ao INNER JOIN {$wpdb->prefix}atl_products as ap ON ao.product_id = ap.id",ARRAY_A);
    
    if(!empty($results)) {
    ?>
    <div class="atl-content shopping-list">
    <div class="table-responsive">
    <table id="orders-table"  class="table cell-border dataTable no-footer" cellspacing="0">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>User name</th>
                    <th>Order Total</th>
                    <th>Order Status</th>
                    <th>Order Date</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($results as $row) : ?>
    <tr>
        <td><?php echo esc_html($row['orderid']); ?></td>
        <td><?php echo esc_html($row['product_name']); ?></td>
        <td><?php echo esc_html(get_userdata($row['user_id'])->data->user_login); ?></td>
        <td><?php echo esc_html($row['order_total']); ?></td>
        <td class="edit-order" data-orderid="<?php echo esc_attr($row['orderid']); ?>">
            <span><?php echo esc_html($row['order_status']); ?></span>
            <select name="change_status" class="change-status" style="display:none;">
                <option value="Processing" <?php selected($row['order_status'], 'Processing'); ?>>Processing</option>
                <option value="Completed" <?php selected($row['order_status'], 'Completed'); ?>>Completed</option>
                <option value="On Hold" <?php selected($row['order_status'], 'On Hold'); ?>>On Hold</option>
                <option value="Cancelled" <?php selected($row['order_status'], 'Cancelled'); ?>>Cancelled</option>
            </select>
        </td>
        <td><?php echo esc_html($row['order_created']); ?></td>
        <td>
            <a href="javascript:void(0);" class="delete-order" data-orid="<?php echo esc_attr($row['orderid']); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                    <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                </svg>
            </a>
        </td>
    </tr>
<?php endforeach; ?>

            </tbody>
        </table>
    </div>
        
    </div>
    <?php 
    } ?>
</div>



<script>
    jQuery(document).ready(function($){
        $('#orders-table').DataTable({
            bLengthChange:false,
            //order: [[1, 'desc']],
            paging: false,
            searching:false,
            bInfo: false,
            // columnDefs: [{
            //     orderable: false,
            //     targets: "no-sort"
            // }]
        });
       
        $('.edit-order').on('click',function(e){
            e.stopPropagation();
            $(this).find('span').hide();
            $(this).find('select').show();

        });

        $('body').on('click',function(){
            $('.edit-order').find('span').show();
            $('.edit-order').find('select').hide();
        });
        
        $('select[name="change_status"]').on('change',function(){
            var elem = $(this);
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: "POST",
                data: {
                    action: 'update_order_status',
                    order_id : $(this).parents('.edit-order').attr('data-orderid'),
                    order_status : $(this).val(),
                },
                success: function(response) {
                    var res = $.parseJSON(response); 
                    console.log(res);
                    if(res.success == 'changed'){
                        
                        Swal.fire({
                            title: 'Status updated!',
                            text: "Order status has been changed.",
                            icon: 'success',
                            confirmButtonText: 'Ok',
                        }).then((result) =>{
                            if (result.isConfirmed) {
                                elem.siblings('span').text(res.order_status).show();
                                elem.hide();
                                // window.location.href = '<?php //echo admin_url('admin.php?page=shop-items'); ?>';
                            }
                        });
                    }
                }
            });
        });

        jQuery('.delete-order').on('click', function(e){

            e.preventDefault();

            var id = jQuery(this).data('orid');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete the order?",
                icon: 'warning',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                showCancelButton: true,
            }).then((result) =>{
                if (result.isConfirmed) {
                    jQuery.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: "POST",
                        data: {
                            action: 'myst_staff_training_atl_delete_order',
                            order_id : id
                        },
                        success: function(data) {
                            if(data == 'deleted'){
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: "Order has been deleted.",
                                    icon: 'success',
                                    confirmButtonText: 'Ok',
                                }).then((result) =>{
                                    if (result.isConfirmed) {
                                        window.location.href = '<?php echo admin_url('admin.php?page=manage-orders'); ?>';
                                    }
                                });
                            }
                        }
                    });
                }
            });
        });

        
    });
</script>