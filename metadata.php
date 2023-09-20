<?php
/**
 * @category      module
 * @package       category_extend
 * @author        Dmitry Novikov
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Category extends for media files, urls and documents
 */
$aModule = [
    'id' => 'oe_category_extend',
    'title' => [
        'de' => 'Category attachment and media',
        'en' => 'Category attachment and media',
    ],
    'description' => [
        'de' => 'Category attachment and media',
        'en' => 'Category attachment and media',
    ],
    'thumbnail' => 'out/admin/src/img/logo.png',
    'version' => '1.0.0',
    'author' => 'Dmitry Novikov',
    'url' => 'http://www.oxid-esales.com/en/',
    'email' => 'dmitry.novikov@jaydevs.com',
    'extend' => [
        OxidEsales\Eshop\Application\Model\Category::class => \Novikovdvpoit\CategoryExtendModule\Application\Model\Category::class,
        OxidEsales\Eshop\Application\Controller\Admin\CategoryMain::class => \Novikovdvpoit\CategoryExtendModule\Application\Controller\Admin\CategoryMain::class,
    ],
    'blocks' => [
        [
            'template' => 'include/category_main_form.tpl',
            'block' => 'admin_category_main_form',
            'file' => 'Application/views/admin/tpl/blocks/category_extend.tpl',
            'position' => '2'
        ],
    ]
];
