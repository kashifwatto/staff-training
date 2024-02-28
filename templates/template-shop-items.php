<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $wpdb;
if (!defined('st_my_plugin_dir_folder')) {
    define('st_my_plugin_dir_folder', plugin_dir_url(__File__));
}
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add') {
        ?>
        <div class="products-top-header">
        <a href="<?php echo esc_url(admin_url('admin.php?page=shop-items')); ?>">
                <img src="<?php echo st_my_plugin_dir_folder?>/images/back.png">
            </a>
            <h1>Add Product</h1>
        </div>
        <div class="atl-product-wrapper">
            <div class="wrapper">
                <form action="" method="" id="add_product_form" name="add_product_form">
                    <div class="top-section-details">
                        <div class="section_upload_icon">
                            <label for="prod_icon"><img width="50px" src="<?php echo st_my_plugin_dir_folder?>/icons-camera.png"/></label>
                            <input id="prod_icon" name="prod_icon_attachment" type="hidden" />
                        </div>
                        <div class="form-field">
                            <input type="text" name="product_title" placeholder="Product Name"/>
                        </div>
                        <div class="form-field">
                            <input type="text" name="product_price" placeholder="Product price"/>
                        </div>
                        <div class="form-field">
                            <textarea rows="5" name="product_desc" placeholder="Product description"></textarea>
                        </div>
                       
                        <div class="form-field">
                            <input type="submit" value="Save" name="submit_product">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
    } elseif ($_GET['action'] == 'edit') { 
        $pid = $_GET['pid'];
         //echo "SELECT * FROM {$wpdb->prefix}atl_products WHERE id = {$pid}";
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}atl_products WHERE id = {$pid}",ARRAY_A);
        
        if(empty($results)) {
            wp_redirect( admin_url('admin.php?page=shop-items&message=notfound'));
            exit;
        }
        if($results[0]['image_icon'] != '') {
            $img = wp_get_attachment_image_src( $results[0]['image_icon'] )[0];
        }else{
            $img = st_my_plugin_dir_folder.'/icons-camera.png';
        }
        ?>
        <div class="products-top-header">
        <a href="<?php echo esc_url(admin_url('admin.php?page=shop-items')); ?>"><img src="<?php echo esc_url(st_my_plugin_dir_folder . '/images/back.png'); ?>"></a>
            <h1>Edit Product</h1>
        </div>
        <div class="atl-product-wrapper">
            <div class="wrapper">
                <form action="" method="" id="edit_product_form" name="edit_product_form">
                    <div class="top-section-details">
                        <input type="hidden" name="prodid" value="<?php echo esc_attr($results[0]['id']); ?>">
                        <div class="section_upload_icon">
                            <label for="prod_icon"><img width="50px" src="<?php echo esc_attr($img); ?>"/></label>
                            <input id="prod_icon" name="prod_icon_attachment" type="hidden" value="<?php echo esc_attr($results[0]['image_icon']); ?>" />
                        </div>
                        <div class="form-field">
                            <input type="text" name="product_title" placeholder="Product Title" value="<?php echo esc_attr($results[0]['product_name']); ?>"/>
                        </div>
                        <div class="form-field">
                            <input type="text" name="product_price" placeholder="Product price" value="<?php echo esc_attr($results[0]['product_price']); ?>"/>
                        </div>
                        <div class="form-field">
                            <textarea rows="5" name="product_desc" placeholder="Product description"><?php echo esc_attr($results[0]['product_desc']); ?></textarea>
                        </div>
                        <div class="form-field">
                            <input type="submit" value="Update" name="update_product">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php }
} else {
    
    if(isset($_GET['message']) && $_GET['message'] == 'notfound'){

        echo '<div class="warning"> Something went wrong ! </div>';

    }

    ?>
    <div class="atl-main-section">
        <div class="atl-top-header">
            <h1>Products</h1>
            <a href="<?php echo esc_url(add_query_arg('action', 'add', admin_url('admin.php?page=shop-items'))); ?>">+ Add New Product</a>
        </div>
        <?php 
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}atl_products",ARRAY_A);
        if(!empty($results)) {
        ?>
        <div class="atl-content shopping-list">
        <div class="table-responsive">
        <table id="listing-table"  class="table cell-border dataTable no-footer" cellspacing="0">
                <thead>
                    <tr>
                        
                        <th>Image</th>
                        <th>Item</th>
                        <th>Product Description</th>
                        <th>Price</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($results as $row) : ?>
                    <tr>
                        <td><img src="<?php echo wp_get_attachment_image_src( $row['image_icon'])[0]; ?>" width="50px" height="50px"></td>
                        <td><?php echo esc_html($row['product_name']); ?></td>
<td><?php echo esc_html($row['product_desc']); ?></td>
<td><?php echo esc_html($row['product_price']); ?></td>

                        <td>
                        <a class="edit-product" href="<?php echo esc_url(add_query_arg(array('action' => 'edit', 'pid' => $row['id']), admin_url('admin.php?page=shop-items'))); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                </svg>
                            </a>
                        </td>
                        <td>
                            <a href="#" class="delete-product" data-pid="<?php echo esc_attr($row['id']); ?>">
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
<?php
}
?>

<script>
    jQuery(document).ready(function($){
        /*Media upload button */
        jQuery('.top-section-details .section_upload_icon label').on('click', function(e) {

            e.preventDefault();
            var button = jQuery(this);
            var id = button.next();
            wp.media.editor.send.attachment = function(props, attachment) {
                id.val(attachment.id);
                jQuery('.top-section-details .section_upload_icon label img').attr('src',attachment.url);
                jQuery('.top-section-details .section_upload_icon label img').addClass('w-100');
            };
            wp.media.editor.open(button);
            return false;
        });

        jQuery.validator.addMethod("lettersonly", function(value, element) {

            return this.optional(element) || /^[a-zA-Z\s]+$/i.test(value);

        }, "Please enter only characters");

        jQuery.validator.addMethod("alphanumeric", function(value, element) {

            return this.optional(element) || /^[a-zA-Z0-9!@#$%&*\s]+$/i.test(value);

        }, "Allow alphabets, numbers and special characters only.");



        // Add Product screen

        jQuery( "#add_product_form" ).validate({

            

            rules: {
                'product_title': {
                    required: true,
                    alphanumeric : true
                },
                'prod_icon_attachment': {
                    required: true,
                },
                'product_desc': {
                    required: true,
                },
                'product_price': {
                    required: true,
                    digits:true,
                },
            },

            submitHandler: function(form) {

                var formData = jQuery('#add_product_form').serialize(); // You need to use standard javascript object here
                jQuery.ajax({
                    url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                    type: "POST",
                    data: {
                        action: 'myst_staff_training_insert_product_shop_items',
                        status: 'add',
                        data: formData
                    },
                    success: function(data) {
                        if(data == 'success'){
                            Swal.fire({
                                title: 'Added',
                                text: "Product has been Added",
                                icon: 'success',
                                confirmButtonText: 'Ok',
                            }).then((result) =>{
                                if (result.isConfirmed) {
                                    window.location.href = '<?php echo esc_url(site_url()); ?>/wp-admin/admin.php?page=shop-items';
                                }
                            });
                        }
                    }
                });
                return false;
            }


        });

        //Edit product
        jQuery( "#edit_product_form" ).validate({

            rules: {
                'product_title': {
                    required: true,
                    alphanumeric : true
                },
                'prod_icon_attachment': {
                    required: true,
                },
                'product_desc': {
                    required: true,
                },
                'product_price': {
                    required: true,
                    digits:true,
                },
            },

            submitHandler: function(form) {

                var formData = jQuery('#edit_product_form').serialize(); // You need to use standard javascript object here
                jQuery.ajax({
                    url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                    type: "POST",
                    data: {
                        action: 'myst_staff_training_insert_product_shop_items',
                        status: 'update',
                        data: formData
                    },
                    success: function(data) {
                        if(data == 'success'){
                            Swal.fire({
                                title: 'Updated',
                                text: "Product has been Updated",
                                icon: 'success',
                                confirmButtonText: 'Ok',
                            }).then((result) =>{
                                if (result.isConfirmed) {
                                    window.location.href = '<?php echo esc_url(site_url()); ?>/wp-admin/admin.php?page=shop-items';
                                }
                            });
                        }
                    }
                });
                return false;
            }

        });

        jQuery('.delete-product').on('click', function(e){

            e.preventDefault();

            var id = jQuery(this).data('pid');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete the product?",
                icon: 'warning',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                showCancelButton: true,
            }).then((result) =>{
                if (result.isConfirmed) {
                    jQuery.ajax({
                        url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',

                        type: "POST",
                        data: {
                            action: 'myst_staff_training_delete_product_shop_items',
                            product_id : id
                        },
                        success: function(data) {
                            if(data == 'deleted'){
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: "Product has been deleted.",
                                    icon: 'success',
                                    confirmButtonText: 'Ok',
                                }).then((result) =>{
                                    if (result.isConfirmed) {
                                        window.location.href = '<?php echo esc_url(admin_url('admin.php?page=shop-items')); ?>';
                                    }
                                });
                            }
                        }
                    });
                }
            });
        });

        $('#listing-table').DataTable({
            bLengthChange:false,
            //order: [[1, 'desc']],
            paging: false,
            searching: false,
            bInfo: false,
            // columnDefs: [{
            //     orderable: false,
            //     targets: "no-sort"
            // }]
        });
    });
</script>