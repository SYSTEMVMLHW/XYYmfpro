<?php

return [
    'host' => 'localhost',//数据库地址
    'port' => 3306,//数据库端口
    'database' => 'sidebar_system',//数据库名称
    'username' => 'root',//数据库用户名
    'password' => '',//数据库密码
    // API
    'api_domain' => 'app1.xuanyiy.cn',//
    // 积分管理系统配置
    'points_system' => [
        'domain' => 'app1.xuanyiy.cn',
        'login_url' => 'https://app1.xuanyiy.cn/admin123/#/login',
        'username' => 'JFGL',
        'password' => 'JFadmin123',
        'urls' => [
            'add' => 'https://app1.xuanyiy.cn/admin123/addons?_plugin=points_mall&_controller=admin_index&_action=add&languagesys=CN',
            'manage' => 'https://app1.xuanyiy.cn/admin123/addons?_plugin=points_mall&_controller=admin_index&_action=showprizes&languagesys=CN',
            'exchange' => 'https://app1.xuanyiy.cn/admin123/addons?_plugin=points_mall&_controller=admin_index&_action=exchange&languagesys=CN',
            'logs' => 'https://app1.xuanyiy.cn/admin123/addons?_plugin=points_mall&_controller=admin_index&_action=logs&languagesys=CN'
        ]
    ]
];

function getConfigJS() {
    $config = include __FILE__;
    return json_encode($config, JSON_UNESCAPED_UNICODE);
}


function getApiDomain() {
    $config = include __FILE__;
    return $config['api_domain'];
}



function getPointsSystemConfig() {
    $config = include __FILE__;
    return $config['points_system'];
}
?>
