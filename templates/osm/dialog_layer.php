<?php
/* @var string $css_modifier CSS modifier added to dialog root element to
 *      distinguish from other dialogs */
?>
<?php echo '<?php' ?>


use Osm\Ui\Forms\Views\Form;
use Osm\Ui\MenuBars\Views\MenuBar;
use Osm\Ui\Menus\Items\Type;

return [
    '@include' => ['modal_dialog'],
    '#dialog' => [
        'modifier' => '<?php echo $css_modifier ?>',
        'header' => osm_t("{dialog_title}"),
        'items' => [
            'form' => Form::new([
                'route' => 'POST {form_route}',
                'submitting_message' => osm_t("{submitting_message}"),
                'items' => [
                    // add input fields here
                ],
            ]),
        ],
        'footer' => MenuBar::new([
            'modifier' => '-center',
            'items' => [
                'ok' => [
                    'type' => Type::COMMAND,
                    'title' => osm_t("OK"),
                    'modifier' => '-filled',
                ],
                'cancel' => [
                    'type' => Type::COMMAND,
                    'title' => osm_t("Cancel"),
                ],
            ],
        ]),
    ],
];