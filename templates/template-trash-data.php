<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="learning-main-section">

    <div class="learning-top-header trash-top-header">
        <h1>Trash List of Learning Sections</h1>
    </div>

    <?php $learning_sections = myst_staff_training_get_learning_section_archived();?>
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
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.4/sweetalert2.all.js" integrity="sha512-aYkxNMS1BrFK2pwC53ea1bO8key+6qLChadZfRk8FtHt36OBqoKX8cnkcYWLs1BR5sqgjU5SMIMYNa85lZWzAw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.4/sweetalert2.min.js" integrity="sha512-vDRRSInpSrdiN5LfDsexCr56x9mAO3WrKn8ZpIM77alA24mAH3DYkGVSIq0mT5coyfgOlTbFyBSUG7tjqdNkNw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.4/sweetalert2.min.css" integrity="sha512-y4S4cBeErz9ykN3iwUC4kmP/Ca+zd8n8FDzlVbq5Nr73gn1VBXZhpriQ7avR+8fQLpyq4izWm0b8s6q4Vedb9w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"> </script>

<script src="<?php // echo get_stylesheet_directory_uri().'/js/jquery.validate.js'?>"> </script>

<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"> </script> -->

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

                        action: 'myst_staff_training_learning_modules_delete_action_backend',

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

                        action: 'myst_staff_training_learning_modules_revert_action_backend',

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