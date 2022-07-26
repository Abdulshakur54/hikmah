<?php 
    $url = '/hikmah/portal/superadmin/role_edit.php?role_id=1&page_token=59d4bc9202207364342796dcd2c152642e9b975afa62271f0126a714ab233ede';
$tokenIndex = strpos($url, 'page_token');
echo $tokenIndex;

    // view role:
    //     url: /hikmah/portal/superadmin/role_list.php?page_token=2dacb0ea837cc020165f17ace7454cf7aa87c9e64b2e9f3531690bc0a73ccd8e
    //     tokenIndex: 40
    //     len: 24
    //     page_url: superadmin/role_list.php

    // edit role
    //     url: /hikmah/portal/superadmin/role_edit.php?role_id=1&page_token=14fbf5746f2e61f12e1241aebb3e6c28626e8b524e19129a903d1223441238c3
    //     tokenIndex: 54
    //     len: 38
    //     page_url: superadmin/role_edit.php?role_id=1&
?>