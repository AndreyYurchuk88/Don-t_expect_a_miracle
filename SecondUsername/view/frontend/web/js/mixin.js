define(['uiComponent', 'jquery'], function (Component, $) {
    'use strict'; //не можем использовать необъявленные переменные
    //mixins принимает параметр target и через extend делаем замену
    return function (target) {
        return target.extend({
            defaults: {
                minChars: 5
            }
        });
    };
});