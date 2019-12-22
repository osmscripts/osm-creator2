<?php
/* @var string $method Controller method name which renders the page */
/* @var string $module Module name */
/* @var string $layer Layer which renders the dialog */
?>

    public function <?php echo $method ?>() {
        return osm_layout('<?php echo $layer ?>');
    }
