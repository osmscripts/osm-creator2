<?php
/* @var string $css_modifier CSS modifier added to <body> element to
 * distinguish this page type from the others */
?>
<?php echo '<?php' ?>


return [
    '@include' => ['page'],
    '#page.modifier' => '<?php echo $css_modifier ?>',
    '#content.items' => [
        // add page-specific views here
    ],
];