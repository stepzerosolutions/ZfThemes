<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Zftheme' => 'ZfThemes\Controller\ZfthemeController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'zf-themes' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/zfthemes[/:action]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                     ),
                     'defaults' => array(
                         'controller' => 'Zftheme',
                         'action'     => 'index',
                     ),
                 ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'ZfThemes' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'zfThemes/Zftheme/index' => __DIR__ . '/../view/zf-themes/zftheme/index.phtml',
        )
    ),
);
