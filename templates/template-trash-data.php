<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="learning-main-section">

    <div class="learning-top-header trash-top-header">
        <h1>Trash List of Learning Sections</h1>
    </div>

    <?php $learning_sections = mystaff_training_staff_training_get_learning_section_archived();?>
    <div class="learning-sections-list trash-list">
        <ul>
            <?php
                if(!empty($learning_sections)){
                ?>
                <li class="headings">
                    <div class="section-title">Name</div>
                    <div class="section-action-btn"><a>Revert</a><a>Permenantly Delete</a></div>
                    
                </li>
                <?php
                    foreach ($learning_sections as $key => $learning_section) {
                        
                        ?>
                        <li>
                            <div class="section-title"><?php echo esc_html($learning_section->title);?></div>
                            <div class="section-action-btn">
                                <a href="javascript:void(0)" title="Revert" class="revert_section" data-section_id="<?php echo esc_attr($learning_section->id);?>"><span class="dashicons dashicons-undo" ></span></a>
                                <a href="javascript:void(0)" title="Permenantly Delete" class="delete_section" data-section_id="<?php echo esc_attr($learning_section->id);?>"><span class="dashicons dashicons-dismiss" ></span></a>

                            </div>
                        </li>
                    <?php
                        
                    }

                }else{
                    echo "No learning sections found";
                }

            ?>

        </ul>
    </div>

</div>


<script>
    // Delete sections
    jQuery('.section-action-btn .delete_section').on('click', function(e){

        e.preventDefault();

        var id = jQuery(this).data('section_id');



        Swal.fire({

            title: 'Are you sure?',

            text: "You won't be able to revert this!",

            icon: 'warning',

            cancelButtonColor: '#d33',

            confirmButtonText: 'Yes, delete it!',

            showCancelButton: true,

        }).then((result) =>{

            if (result.isConfirmed) {

                jQuery.ajax({

                    url: '<?php echo esc_url(site_url('/wp-admin/admin-ajax.php')); ?>',

                    type: "POST",

                    data: {

                        action: 'mystaff_training_staff_training_learning_modules_delete_action_backend',

                        section_id : id

                    },

                    success: function(data) {

                        if(data == 'deleted'){

                            Swal.fire({

                                title: 'Deleted!',

                                text: "Section has been deleted",

                                icon: 'success',

                                confirmButtonText: 'Ok',

                            }).then((result) =>{

                                if (result.isConfirmed) {

                                    window.location.href = '<?php echo esc_url(site_url()); ?>/wp-admin/admin.php?page=learning_sections';

                                }

                            });

                        }

                    }

                });

            }

        });

    });


    // revert sections
    jQuery('.section-action-btn .revert_section').on('click', function(e){

        e.preventDefault();

        var id = jQuery(this).data('section_id');



        Swal.fire({

            title: 'Are you sure?',

            text: "You want to revert this section?",

            icon: 'warning',

            cancelButtonColor: '#d33',

            confirmButtonText: 'Yes, Revert it!',

            showCancelButton: true,

        }).then((result) =>{

            if (result.isConfirmed) {

                jQuery.ajax({

                    url: '<?php echo esc_url(site_url('/wp-admin/admin-ajax.php')); ?>',

                    type: "POST",

                    data: {

                        action: 'mystaff_training_staff_training_learning_modules_revert_action_backend',

                        section_id : id

                    },

                    success: function(data) {

                        if(data == 'reverted'){

                            Swal.fire({

                                title: 'Reverted!',

                                text: "Section has been Revereted.",

                                icon: 'success',

                                confirmButtonText: 'Ok',

                            }).then((result) =>{

                                if (result.isConfirmed) {

                                    window.location.href = '<?php echo esc_url(site_url()); ?>/wp-admin/admin.php?page=learning_sections&action=edit&section_id='+id;

                                }

                            });

                        }

                    }

                });

            }

        });

    });
</script>