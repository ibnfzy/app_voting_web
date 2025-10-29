<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cors extends BaseConfig
{
    public array $default = [
        'supportsCredentials' => false,
        'allowedOrigins'      => ['*'],
        'allowedOriginsPatterns' => [],
        'allowedHeaders' => ['*'],
        'exposedHeaders' => [],
        'allowedMethods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'maxAge' => 7200,
    ];
}