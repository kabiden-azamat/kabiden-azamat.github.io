<?php

class EventOptions extends Module {

    public function init() {
        $this->addEventPreg('/^options$/i', '/^$/i', 'index');
    }

    public function index() {
        $this->getRender()->addBreadCrumb(Lang::get('admin.options.site_options'));
        $this->getRender()->setTitle(Lang::get('admin.options.site_options'));
        $this->getRender()->setTemplate('options');
        $this->getRender()->Run('main');
    }

}