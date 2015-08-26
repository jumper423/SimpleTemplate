# SimpleTemplate
##Простой шаблонизатор

####Пример

```php
$t = new \jumper423\SimpleTemplate\SimpleTemplate();

$t->mainTemplate(__DIR__ . '/templates/layouts/main1.html');

$t->blockTemplate(__DIR__ . '/templates/blocks/header.html');
$t->insertBlock('header');

$t->blockTemplate(__DIR__ . '/templates/blocks/menu.html');
$menu = [
    'Главная',
    'Контакты',
    'О нас',
];
foreach ($menu as $text) {
    $t->setVar('text', $text);
    $t->parse('block');
}
$t->insertBlock('menu');

$t->mainOutput();
```

### Install

Either run

```
$ php composer.phar jumper423/SimpleTemplate "*"
```

or add

```
"jumper423/SimpleTemplate": "*"
```
