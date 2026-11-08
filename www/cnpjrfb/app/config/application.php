<?php
return [
    'general' =>  [
        'timezone' => 'America/Sao_Paulo',
        'language' => 'pt',
        'application' => 'cnpjrfb',
        'title' => 'Consulta Pública de CNPJ da RFB',
        'theme' => 'adminbs5_v7',
        'debug' => '1',
        'strict_request' => '0'
    ],
    'template' => [
        'navbar' => [
            'has_menu_mode_switch' => '1',
            'has_main_mode_switch' => '1'
        ],
        'dialogs' => [
            'use_swal' => '1'
        ],
        'theme' => [
            'menu_mode' => 'dark',
            'main_mode' => 'light'
        ]
    ],
    'system' =>  [
        'system_version' => '2.4.0',
        'system_name_sub' => 'Consulta Pública de CNPJ da RFB',
        'adianti_min_version' => '8.6.0',
        'formdin_min_version' => '5.9.0',
    ],
    // Grafo de relacionamentos gerado pelo CNPJ-full (app/CNPJ-full).
    // Caminhos relativos são a partir da pasta www/cnpjrfb.
    'cnpj_full' => [
        'path'    => 'app/CNPJ-full',          // pasta do git clone do CNPJ-full
        'script'  => 'cnpj_full/consulta.py',  // relativo à pasta do CNPJ-full
        'python'  => '',                       // vazio: python3 no Linux e python no Windows
        'nivel'   => '3',                      // profundidade do grafo (--nivel)
        'cache'   => '1',                      // 1: reaproveita grafo já gerado (--cache)
        'grafos'  => 'app/output/grafos',      // onde o consulta.py grava os grafos
    ],
];
