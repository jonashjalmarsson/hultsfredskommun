<?php

class hk_perm {
    static function quick() {
        if (!function_exists("get_field")) return false;
        return (!get_field('user_permissions_hk_quick', 'options') || get_field('user_can_edit_hk_quick', 'user_' . get_current_user_id()));
    }
    static function forum() {
        if (!function_exists("get_field")) return false;
        return (!get_field('user_permissions_hk_forum', 'options') || get_field('user_can_edit_hk_forum', 'user_' . get_current_user_id()));
    }
    static function bubble() {
        if (!function_exists("get_field")) return false;
        return (!get_field('user_permissions_hk_bubble', 'options') || get_field('user_can_edit_hk_bubble', 'user_' . get_current_user_id()));
    }
    static function kontakter() {
        if (!function_exists("get_field")) return false;
        return (!get_field('user_permissions_hk_kontakter', 'options') || get_field('user_can_edit_hk_kontakter', 'user_' . get_current_user_id()));
    }
    static function driftstorningar() {
        if (!function_exists("get_field")) return false;
        return (!get_field('user_permissions_driftstorningar', 'options') || get_field('user_can_edit_driftstorningar', 'user_' . get_current_user_id()));
    }
}