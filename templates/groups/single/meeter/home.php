<?php
$action = meeter_get_current_action();
$user_id = get_current_user_id();
$group_id = bp_get_group_id();
$user_rooms = meeter_get_user_rooms($group_id, $user_id);
$current_room = meeter_get_current_user_room_from_path();
?>
    <nav class="<?php bp_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php  esc_attr_e( 'Meeter secondary navigation', 'meeter' ); ?>">
        <ul class="subnav">
            <?php bp_get_options_nav(meeter_get_slug()); ?>

            <?php if($action !== 'group') :?>
                <li id="room-filter-select" class="last">
                    <label for="active-rooms"></label>
                    <select id="active-rooms">
                        <option value=""><?php _e('Select a room', 'meeter') ?></option>
                        <?php foreach ($user_rooms as $user_room) :?>
                            <option value="<?php esc_attr_e($user_room['id'])?>" <?php esc_attr_e(($current_room && $user_room['id'] === $current_room) ? 'selected' : '')?>>
                                <?php esc_html_e($user_room['name']);?>
                            </option>
                        <?php endforeach;?>
                        <?php do_action( 'meeter_group_rooms_filter_options' ); ?>
                    </select>
                </li>
            <?php endif;?>
        </ul>
    </nav>
<?php

if(!$current_room || meeter_is_member_of_room($user_id, $current_room, $group_id)) {
    switch ( $action ) {
        case 'group' :
            bp_get_template_part('groups/single/meeter/group');
            break;
        case 'members' :
            bp_get_template_part('groups/single/meeter/members');
    }
} else {
    echo '<div id="message" class="error"><p>' . __('This content is only available to invited members.', 'career') . '</p></div>';
}

