/* Javascript */
(function( $ ) {

    'use strict';
    
    jQuery('.mystaff_training_learning_modules_action_btn_quiz a').click(function(e){
        jQuery(".popup-quiz-overlay, .popup-content").show();
        jQuery('body').addClass('has-popup');
    });
    jQuery(".close").on("click", function() {
        jQuery(".popup-quiz-overlay, .popup-content").hide();
        jQuery('body').removeClass('has-popup');
    });

    if($('#quiz_btn_timer').length == 1) {
        $(window).on('load', function(){
            //console.log($('#quiz_btn_timer').val());
            startTimer();
            $('#quiz_btn_timer').siblings('.mystaff_training_learning_modules_action_btn_quiz').children('.start_quiz').addClass('disabled');

            setTimeout(function(){

                $('#quiz_abailable_text').hide();

                $('#quiz_btn_timer').siblings('.mystaff_training_learning_modules_action_btn_quiz').children('.start_quiz').removeClass('disabled');
            
            }, ($('#quiz_btn_timer').val() * 1000) );


           
        });
    }
      /* jQuery(window).on('click',function(event) {
        if(event.target == jQuery(".popup-quiz-overlay")) {
            jQuery(".popup-quiz-overlay").hide();
        }
      });
 */
      var success = 1;
      var arr = new Array();
    jQuery(document).on('click','.ques-next',function(){
        var nextid = jQuery(this).children('.next-btn').data('nextid');
        var queswrap = jQuery(this).parents('.question-wrapper');
        var questionid= queswrap.data('qid');
        var answrap = jQuery(this).prev('.ans-wrap');
        var count = 0;
        var anscount = 0;
        
        answrap.find('.ans-d').each(function(){
            if(jQuery(this).find('input').is(':checked')){
                count = count + 1;
            }
            if(jQuery(this).find('input').data('isselected') == 'yes'){
                anscount = anscount + 1;
            }
        });
        
        //if(count == anscount) {
        if(count != 0) {
            if(jQuery('div[data-qid='+nextid+']').length != 0) {
                jQuery(this).parents('.question-wrapper').hide();
                jQuery('div[data-qid='+nextid+']').show();
                arr[success] = {answerid : jQuery.makeArray(answrap.find('input:checked').map(function(){ return jQuery(this).val(); })) };
                success = success + 1;
            }else{
                //submit data
                
                arr[success] = {answerid : jQuery.makeArray(answrap.find('input:checked').map(function(){ return jQuery(this).val(); })) };

                if(success == jQuery('.question-wrapper').length){
console.log(arr);
                    $.ajax({
                        url: myAjax.ajax_url,
                        type: 'post',
                        data: {
                            action: "mystaff_training_quiz_modules_save_action_frontend",
                            quizid: jQuery('input[name=quiz_id]').val(),
                            userid: jQuery('input[name=user_id]').val(),
                            quizdata: arr
                        },
                        beforeSend: function() {
                            // setting a timeout
                            jQuery('.loading-gif').show();
                            jQuery('.modal-body').addClass('overlay');
                        },
                        success:function(data) {
                            jQuery('.loading-gif').hide();
                            jQuery('.modal-body').removeClass('overlay');
                            var res = jQuery.parseJSON(data);
                            if(res.status == "insert_success" || res.status == "update_success"){
                                var header_title = jQuery('.modal-header h2').attr('data-title');
                                jQuery('.modal-header h2').text(header_title + ' Complete');
                                queswrap.find('.show-status').remove();
                                queswrap.append('<span class="show-status success">'+res.message+'</span>').fadeOut(500);
                                jQuery('.question-wrapper').hide();
                                jQuery('.final-screen').show();
                            }else if(res.status == "insert_error" || res.status == "update_error"){
                                queswrap.find('.show-status').remove();
                                queswrap.append('<span class="show-status error">'+res.message+'</span>');
                            }
            
                        }
            
                    });
                
                    
                
                }
            }
        }else{
            queswrap.find('.show-status').remove();
            queswrap.append('<span class="show-status error">Select an answer.</span>');
            jQuery('span.show-status').fadeOut(3000);

        }

        
    });
   


    jQuery('.mystaff_training_learning_modules_action_btn a, .continue-btn').click(function(e){
        jQuery(this).attr('disabled',true).css({'opacity':'0.6', 'pointer-events':'none', 'cursor':'not-allowed'});
        
        e.preventDefault();
        var url = window.location.href;

            url = url.replace(/\/$/, ''); 

        $.ajax({

            url: myAjax.ajax_url,

            type: 'post',

            data: {

                action: "mystaff_training_learning_modules_save_action",

                data: url,

            },
            beforeSend: function() {
                // setting a timeout
                jQuery('.loading-gif').show();
                
            },
            success:function(data) {
                jQuery('.loading-gif').hide();
                var res = jQuery.parseJSON(data);
                if(res.success == "success" && res.pageurl == "dashboard"){

                    window.location.href = myAjax.site_url+"/dashboard/";

                }else if(res.success == "success" && res.pageurl != "dashboard"){

                    window.location.href = res.pageurl;

                }

            }

        });

    });





    jQuery(document).ready(function(){



        setTimeout(function(){



            jQuery( ".thrv_tabs_shortcode ul.tve_clearfix li" ).each(function( index ) {

                var inner_text = jQuery.trim(jQuery( this ).text());

                var urlObject = new URL(window.location.href);

                var params = urlObject.searchParams;
                var step = decodeURIComponent(params.get("step"));
                
                if( inner_text == step ){

                    jQuery(this).trigger('click');

                }

            });

        }, 100);



    });

    jQuery('.btn-shop').click(function(e){
        e.preventDefault();
        var docHeight = jQuery(document).height();
        jQuery('.overlay-bg').show().css({'height' : docHeight});
        jQuery('#atlshopmodal').show();
    });
    jQuery('.close').click(function(){
        jQuery('.overlay-bg, #atlshopmodal').hide();
        
    });
    jQuery('a.ldrbrd-popup, a.display-results').click(function(e){
        e.preventDefault();
        var selectedPopup = jQuery(this).data('showpopup');
        showPopup(selectedPopup);
    });

    jQuery('.dashboard-section .main-content .content-left .completed-list ul li a.show-popup').click(function(e){
        e.preventDefault();
        var selectedPopup = jQuery(this).data('showpopup');
        showPopup(selectedPopup);
       
       /*  if(jQuery(window).width() <= 767 ){
            if(!jQuery(this).hasClass('open')){

                jQuery('.dashboard-section .main-content .content-left .completed-list ul li').removeClass('open');
                jQuery(this).addClass('open');   
            }else{
                jQuery(this).removeClass('open'); 
            }

        }else{
            jQuery(this).toggleClass('open');   

        } */

    });
    jQuery('.close-btn, .overlay-bg').click(function(){
        closePopup();
    });
    jQuery(document).keyup(function(e) {
        if (e.keyCode == 27) {
            closePopup();
        }
    });

    /**
     * Dashboard -Redo course 
     */
    jQuery(document).on('click','.redo-course',function(e){
        e.preventDefault();
        var iconobj = jQuery(this);
        var sectionid = jQuery(this).data('sid');
       
        Swal.fire({
            title: 'Are you sure you want to retake this course?',
            text: "This will erase your current score.",
            icon: 'question',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, retake course!',
            showCancelButton: true,
            }).then((result) =>{

            if (result.isConfirmed) {

                jQuery.ajax({
                    url: myAjax.ajax_url, 
                    type: "POST",             
                    data: { 
                        action: 'mystaff_training_staff_training_redo_course_module', 
                        sectionid: sectionid,
                    },      
                    success: function(data) {
                        if(data == 'success'){
                            location.reload();
                        } 
                    }
                });

            }

        });
        
    });


    /**Add to cart */
    jQuery(document).off('click', '.add-to-cart').on('click','.add-to-cart',function(e){

        e.preventDefault();
        var elem = jQuery(this);
         jQuery('.add-to-cart').addClass('disabled').prop('disabled',true);
        jQuery.ajax({
            url: myAjax.ajax_url, 
            type: "POST",             
            data: { 
                action: 'mystaff_training_staff_training_atl_create_order', 
                productid: jQuery(this).attr('data-pid'),
                productprice : jQuery(this).attr('data-price'),
            },
            beforeSend: function(){
                elem.addClass('disabled');
                elem.parents('.purchase').siblings('.shop-loader').addClass('show');
            },   
            success: function(response) {
                var data = jQuery.parseJSON(response);
                if(data.success == 'success'){
                    $('.modal-footer').html('<p class="green-msg" style="color:#008f00;">Your order has been created. You will be contacted soon.</p>').fadeOut(5000);
                   // console.log(data.disabled);
                    // if(data.disabled == false) {
                    //     elem.removeClass('disabled');  
                    // }else{
                    //     elem.addClass('disabled');
                    // }
                    
                    setTimeout(function(){
                        elem.parents('.purchase').siblings('.shop-loader').removeClass('show');
                        location.reload();
                    },3000);
                } 
            }
        });
    });


    // const tg = new tourguide.TourGuideClient();
    // tg.setOptions({
    //     dialogPlacement: 'Side',
    // })
    // tg.start();
    $(".second-msgs-slider p").each(function(e) {
        if (e != 0)
            $(this).hide();
    });

    $(".second-msgs-slider-w #next").click(function(){
        $(".second-msgs-slider-w #prev").removeAttr('disabled');

        
        if ($(".second-msgs-slider p:visible").next().length != 0) {
            //$(this).removeAttr('disabled');
            $(".second-msgs-slider p:visible").next().show().prev().hide();
        }else {
            $(".second-msgs-slider p:visible").hide();
            $(".second-msgs-slider p:first").show();
            //$(this).attr('disabled','disabled');
        }
        return false;
    });

    $(".second-msgs-slider-w #prev").click(function(){
        $(".second-msgs-slider-w #next").removeAttr('disabled');
        
       /*  if($(".second-msgs-slider p:visible").prev() == $(".second-msgs-slider p:last")) {
            $(this).attr('disabled','disabled');
        }
        else */ 
        if ($(".second-msgs-slider p:visible").prev().length != 0){
            //$(this).removeAttr('disabled');
            $(".second-msgs-slider p:visible").prev().show().next().hide();
        }else {
            $(".second-msgs-slider p:visible").hide();
            $(".second-msgs-slider p:last").show();
            //$(this).attr('disabled','disabled');
        }
        return false;
    });

})( jQuery );

function showPopup(whichpopup){
    var docHeight = jQuery(document).height();
    var scrollTop = jQuery(window).scrollTop();
    jQuery('.overlay-bg').show().css({'height' : docHeight});
    jQuery('#cmplt_'+whichpopup).show();
}
   // function to close our popups
function closePopup(){
    jQuery('.overlay-bg, .overlay-content').hide();
}



