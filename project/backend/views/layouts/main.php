<?php
use yii\helpers\Html;
use yii\widgets\Menu;
use backend\assets\AppAsset;

AppAsset::register($this);

$route = Yii::$app->requestedAction->uniqueId;

$menu = [
    [
        'label' => '系统',
        'url' => '#',
        'items' => [
            'home' => ['label' => '系统信息', 'url' => ['site/index'], 'active' => in_array($route, ['site/index'])],
            'usersuper' => ['label' => '用户管理', 'url' => ['usersuper/index'], 'active' => in_array($route, ['usersuper/index', 'usersuper/create-user', 'usersuper/update-user'])],
        ]
    ],
    [
        'label' => '博客',
        'url' => '#',
        'items' => [
            'category' => ['label' => '分类管理', 'url' => ['categorysuper/index'], 'active' => in_array($route, ['categorysuper/index', 'categorysuper/add-category', 'categorysuper/update-category'])],
            'blog' => ['label' => '博客管理', 'url' => ['blogsuper/index'], 'active' => in_array($route, ['blogsuper/index', 'blogsuper/add-blog', 'blogsuper/update-blog'])],
        ]
    ],
];

?>

<?php $this->beginContent('@app/views/layouts/base.php'); ?>
    <div id="wrapper">
         <nav class="sidebar">
            <?= Html::a('<img src="/img/aura.png" class="logo-rotation">', Yii::$app->homeUrl, ['class' => 'sidebar-logo']) ?>
            <?= Menu::widget([
                'encodeLabels' => false,
                'submenuTemplate' => "\n<ul class=\"nav nav-second-level collapse\">\n{items}\n</ul>\n",
                'options' => [
                    'class' => 'nav',
                    'id' => 'side-menu'
                ],
                'items' => $menu
            ]) ?>
        </nav>

        <div id='page-wrapper'>
            <?= $content ?>
        </div>

    </div>

<?php $this->endContent() ?>