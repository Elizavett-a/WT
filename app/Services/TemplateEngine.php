<?php
namespace App\Services;

class TemplateEngine {
    private const BLOCK_REGEX = '/\{% for (\w+) in (\w+) %\}(.*?)\{% endfor %\}/s';
    private const VAR_REGEX = '/\{\{ (\w+(?:\.\w+)*) \}\}/';

    public function render(string $templatePath, array $data = []): string {
        $fullPath = $this->resolveTemplatePath($templatePath);
        $content = file_get_contents($fullPath);

        $content = $this->processBlocks($content, $data);
        $content = $this->processVariables($content, $data);

        return $content;
    }

    private function resolveTemplatePath(string $path): string {
        $fullPath = realpath(__DIR__.'/../../public/templates/'.$path);

        if ($fullPath === false) {
            throw new \RuntimeException("Template not found: $path");
        }

        return $fullPath;
    }

    private function processBlocks(string $content, array $data): string {
        return preg_replace_callback(
            self::BLOCK_REGEX,
            function($matches) use ($data) {
                return $this->processBlock($matches, $data);
            },
            $content
        );
    }

    private function processBlock(array $matches, array $data): string {
        if (!isset($data[$matches[2]])) {
            return '';
        }

        $result = '';
        $items = $data[$matches[2]];
        $template = $matches[3];

        foreach ($items as $item) {
            $result .= $this->renderItem($template, $matches[1], $item);
        }

        return $result;
    }

    private function renderItem(string $template, string $itemName, $item): string {
        return preg_replace_callback(
            self::VAR_REGEX,
            function($matches) use ($itemName, $item) {
                return $this->getPropertyValue($itemName, $matches[1], $item);
            },
            $template
        );
    }

    private function processVariables(string $content, array $data): string {
        return preg_replace_callback(
            self::VAR_REGEX,
            function($matches) use ($data) {
                return $this->getVariableValue($matches[1], $data);
            },
            $content
        );
    }

    private function getPropertyValue(string $itemName, string $path, $item): string {
        if (strpos($path, $itemName.'.') !== 0) {
            return '';
        }

        $property = substr($path, strlen($itemName) + 1);

        if (is_object($item)) {
            $method = 'get'.ucfirst($property);
            if (method_exists($item, $method)) {
                return htmlspecialchars((string)$item->$method());
            }
            return '';
        }

        return htmlspecialchars((string)($item[$property] ?? ''));
    }

    private function getVariableValue(string $path, array $data) {
        $parts = explode('.', $path);
        $value = $data;

        foreach ($parts as $part) {
            if (is_object($value) && method_exists($value, 'get'.ucfirst($part))) {
                $value = $value->{'get'.ucfirst($part)}();
            } elseif (is_array($value) && isset($value[$part])) {
                $value = $value[$part];
            } else {
                return '';
            }
        }

        return htmlspecialchars((string)$value);
    }
}