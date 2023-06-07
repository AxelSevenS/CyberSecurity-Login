<?php


class IndexController {

    public static function resolveIndex() {
        ob_start();
        require_once __DIR__.'/../view/indexView.php';
        $content = ob_get_clean();
        $title = "Index Page";

        require_once __DIR__.'/../view/template.php';
    }
}