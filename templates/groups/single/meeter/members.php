<?php
if ( !defined( 'ABSPATH' ) ) exit;
global $bp;
$room = meeter_get_current_user_room();

do_action( 'bp_before_group_meet_member_content' ) ?>

<form action="#" method="post" id="send-invite-form">

    <div id="meet-wrapper" class="members-meet-parent">
        <?php if($room):?>
            <?php meeter_render_jitsi_meet($room['id'], $room['name']); ?>
            <input type="hidden" name="room" id="room" value="<?php esc_attr_e($room['id']); ?>" />
        <?php endif;?>
    </div>
    <?php wp_nonce_field( 'meeter_members_delete_room', '_wpnonce_members_delete_room' ) ?>

    <label for="room_name"><?php esc_html_e( 'Room Name', 'meeter' ); ?></label>
    <input type="text" name="room_name" id="room_name" value="<?php esc_attr_e($room['name']) ?>"/>

    <label for="send-to-input"><?php esc_html_e( 'Search for members to invite in the room', 'meeter' ); ?></label>
    <input type="text" name="send-to-input" class="send-to-input" id="send-to-input" />

    <?php wp_nonce_field( 'meeter_members_add_invite', '_wpnonce_members_add_invite' ) ?>

    <div id="members-invite-list" class="members-invite-list">
        <?php do_action( 'bp_before_group_meet_member_list' ) ?>

        <ul id="meet-invite-list" class="item-list"></ul>

        <?php do_action( 'bp_after_group_meet_member_list' ) ?>

        <div class="submit">
            <input type="submit" name="stopCall" id="stopCall" class="submit-btn" value="<?php _e( 'Leave Room','meeter' ) ?>" />
            <input type="submit" name="submit" id="sendInviteButton" class="submit-btn" value="<?php _e( 'Send Invites','meeter' ) ?>" />
        </div>
    </div>

    <?php wp_nonce_field( 'meeter_send_invites', '_wpnonce_send_invites') ?>
    <?php wp_nonce_field( 'meeter_members_autocomplete', '_wpnonce_members_autocomplete') ?>

    <input type="hidden" name="group_id" id="group_id" value="<?php bp_group_id() ?>" />

</form>

<?php do_action( 'bp_after_group_meet_member_content' ) ?>



