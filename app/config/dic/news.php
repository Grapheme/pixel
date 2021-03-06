<?php

return array(

    'fields_i18n' => function() {

        return array(
            'title' => array(
                'title' => 'Заголовок новости',
                'type' => 'text',
            ),
            'published_at' => array(
                'title' => 'Дата публикации',
                'type' => 'date',
                'others' => array(
                    'class' => 'text-center',
                    'style' => 'width: 221px',
                    'placeholder' => 'Нажмите для выбора'
                ),
                'handler' => function($value) {
                    return $value ? @date('Y-m-d', strtotime($value)) : $value;
                },
                'value_modifier' => function($value) {
                    return $value ? date('d.m.Y', strtotime($value)) : date('d.m.Y');
                },
            ),
            'preview' => array(
                'title' => 'Анонс новости',
                'type' => 'textarea',
            ),
            /*
            'content' => array(
                'title' => 'Полный текст новости',
                'type' => 'textarea_redactor',
            ),
            */
            'link' => array(
                'title' => 'Ссылка на полный текст новости',
                'type' => 'text',
            ),
            'image' => array(
                'title' => 'Изображение',
                'type' => 'image',
            ),
        );
    },

    'slug_label' => 'URL записи',

    'second_line_modifier' => function($line, $dic, $dicval) {
        return (isset($dicval->published_at) && $dicval->published_at ? '<i>' . $dicval->published_at . '</i> &mdash; ' : '') . $dicval->slug;
    },

    'seo' => ['title', 'description', 'keywords'],
);