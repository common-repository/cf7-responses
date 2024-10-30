<?php
/*
* @Author 		Pluginrox
* Copyright: 	2018 Pluginrox
*/

if ( ! defined('ABSPATH')) exit;  // if direct access

$cf7r_form_id = get_post_meta( $post->ID, 'cf7r_form_id', true );
$contact_form = wpcf7_contact_form( $cf7r_form_id );

if( ! $contact_form ) return;


foreach( $contact_form->collect_mail_tags() as $form_key ) {

    $form_key_value = get_post_meta( $post->ID, $form_key, true );

    $item_label     = str_replace( array( '-', '_' ), ' ', $form_key );
    $item_label     = ucwords( $item_label );

    ?>
    <div class='cf7r_section'>
        <div class='cf7r_section_inline cf7r_section_title'><?php echo $item_label; ?></div>
        <div class="cf7r_section_inline cf7r_section_hint tt--top tt--info" aria-label="<?php echo $form_key; ?>">?</div>
        <div class='cf7r_section_inline cf7r_section_inputs'>
            <?php echo $form_key_value; ?>
        </div>
    </div>
    <?php
}
