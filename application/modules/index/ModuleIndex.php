<?php

class ModuleIndex extends Module {

    protected function init() {
        $this->addEventPreg('/^index$/i', '/^$/i', 'index');
    }

    protected function index() {
        echo 'Azamat';
    }

}