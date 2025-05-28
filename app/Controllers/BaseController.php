<?php
declare(strict_types = 1);

namespace App\Controllers;

require_once __DIR__.'/../TemplateEngine.php';

use App\TemplateEngine;

abstract class BaseController {
    protected TemplateEngine $templateEngine;

    public function __construct(TemplateEngine $templateEngine) {
        $this->templateEngine = $templateEngine;
    }

    protected function render(string $template, array $data = []): void {
        echo $this->templateEngine->render($template, $data);
    }
}