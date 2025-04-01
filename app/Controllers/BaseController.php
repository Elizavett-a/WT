<?php
namespace App\Controllers;

require_once __DIR__.'/../Services/TemplateEngine.php';

use App\Services\TemplateEngine;

abstract class BaseController {
    protected TemplateEngine $templateEngine;

    public function __construct(TemplateEngine $templateEngine) {
        $this->templateEngine = $templateEngine;
    }

    protected function render(string $template, array $data = []): void {
        echo $this->templateEngine->render($template, $data);
    }

    abstract public function listAction();
    abstract public function viewAction($id);
}