<?= \yii\bootstrap\Nav::widget([
    'options' => [
        'class' => 'nav-tabs',
        'style' => 'margin-bottom: 15px'
    ],
    'items' => [
        [
            'label' => 'Profile',
            'url' => ['/user/setting/index'],
        ],
        [
            'label' => 'Change Password',
            'url' => ['/user/setting/password'],
        ]
    ],
]) ?>