<?php
/**
 * Created by PhpStorm.
 * User: Azamat
 * Date: 23.06.2017
 * Time: 23:07
 */

class EventIndex extends Module {

    public function init() {
        $this->addEventPreg('/^index$/i', 'index');
    }

    public function index() {
        $this->getRender()->setTitle('test');
        $this->getRender()->setTemplate('test');
        $this->getRender()->Run('main');
    }

}