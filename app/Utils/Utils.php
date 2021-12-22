<?php 
namespace App\HTTP\Utils;

class Utils {

    public static function getFilteredItems($table, $field, $values) {
        $current_model = $table;
        if (array_key_exists('eq', $values)) $current_model = $current_model->where($field, $values['eq']);
        if (array_key_exists('lt', $values)) $current_model = $current_model->where($field, '<', $values['lt']);
        if (array_key_exists('lte', $values)) $current_model = $current_model->where($field, '<=', $values['lte']);
        if (array_key_exists('gt', $values)) $current_model = $current_model->where($field, '>', $values['gt']);
        if (array_key_exists('gte', $values)) $current_model = $current_model->where($field, '>=', $values['gte']);
        if (array_key_exists('like', $values)) $current_model = $current_model->where($field, 'like', $values['like']);
        return $current_model;
    }
}