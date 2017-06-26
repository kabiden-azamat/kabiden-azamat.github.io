<?php

class ModuleIndex extends Module {

    protected function init() {
        $this->addEventPreg('/^index$/i', '/^$/i', 'index');
        $this->addEventPreg('/^test$/i', '/^page$/i', '/^$/i', 'test');
    }

    protected function index() {
        //$this->getRender()->putVar('text', 'Azamat12');

        $this->getRender()->putVarArray(['text' => ['my' => 'TEST']]);
        $this->getRender()->setTemplate('test');
        $this->getRender()->Run('index');
    }

    protected function test() {
        echo 'test';
    }

}