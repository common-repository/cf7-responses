<?php
/*
* @Author 		Pluginrox
* Copyright: 	2018 Pluginrox
*/

if ( ! defined('ABSPATH')) exit;  // if direct access

$cf7r_form_id           = get_post_meta( $post->ID, 'cf7r_form_id', true );
$cf7r_submitted_by      = get_post_meta( $post->ID, 'cf7r_submitted_by', true );
$cf7r_submitted_user    = get_user_by( 'ID', $cf7r_submitted_by );
$contact_form           = wpcf7_contact_form( $cf7r_form_id );

if( ! $contact_form ) return;


?>

<div class="cf7r_applicant_mailbox cf7r_section_mini">
    <div class='cf7r_section_inline cf7r_section_title'><?php echo __('Form Title', CF7R_TEXTDOMAIN);?></div>
    <div class='cf7r_section_inline cf7r_section_inputs'>
        <?php printf( '<a href="admin.php?page=wpcf7&post=%s&action=edit">%s</a>', $cf7r_form_id, get_the_title( $cf7r_form_id ) ); ?>
    </div>
</div>


<div class="cf7r_applicant_mailbox cf7r_section_mini">
    <div class='cf7r_section_inline cf7r_section_title'><?php echo __('Date', CF7R_TEXTDOMAIN);?></div>
    <div class='cf7r_section_inline cf7r_section_inputs'>
       <span><?php echo get_the_time( 'F j, Y', true ); ?></span>
    </div>
</div>

<div class="cf7r_applicant_mailbox cf7r_section_mini">
    <div class='cf7r_section_inline cf7r_section_title'><?php echo __('Time', CF7R_TEXTDOMAIN);?></div>
    <div class='cf7r_section_inline cf7r_section_inputs'>
       <span><?php echo get_the_time( 'g:i A', true ); ?></span>
    </div>
</div>

<div class="cf7r_applicant_mailbox cf7r_section_mini">
    <div class='cf7r_section_inline cf7r_section_title'><?php echo __('Submitted by', CF7R_TEXTDOMAIN);?></div>
    <div class='cf7r_section_inline cf7r_section_inputs'>

        <?php
        if( $cf7r_submitted_user ) printf( '<a href="user-edit.php?user_id=%s">%s</a>', $cf7r_submitted_user->ID, $cf7r_submitted_user->display_name );
        else printf('<span>%s</span>', __( 'No data found !', CF7R_TEXTDOMAIN ) );
        ?>

    </div>
</div>

<a class="cf7r-btn cf7r-btn-danger" href="<?php echo get_delete_post_link( $post->ID );?>">
    <?php _e('Move to Trash', CF7R_TEXTDOMAIN ); ?>
</a>