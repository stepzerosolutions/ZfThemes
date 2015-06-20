<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'ZfThemes\Controller\Zftheme' => 'ZfThemes\Controller\ZfthemeController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'zf-themes' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/zftheme',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'ZfThemes\Controller',
                        'controller'    => 'Zftheme',
                        'action'        => 'index',
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
