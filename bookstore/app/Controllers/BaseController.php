<?php
namespace App\Controllers;

abstract class BaseController {
    protected $entityManager;
    protected $templateEngine;

    public function __construct($entityManager, $templateEngine) {
        $this->entityManager = $entityManager;
        $this->templateEngine = $templateEngine;
    }

    abstract public function listAction();
    abstract public function viewAction($id);
}