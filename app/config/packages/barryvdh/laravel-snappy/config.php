<?php

return array(


    'pdf' => array(
        'enabled' => true,
        'binary' => "\"D:\\WebRoot\\ServisTakip\\vendor\\wkhtmltopdf\\bin\\wkhtmltopdf.exe\"",
        'timeout' => false,
        'options' => array('page-size'=>'letter'),
    ),
    'image' => array(
        'enabled' => true,
        'binary' => '/usr/local/bin/wkhtmltoimage',
        'timeout' => false,
        'options' => array(),
    ),


);
