<?php
declare(strict_types=1);

namespace App;

class TemplateEngine
{
    private const BLOCK_REGEX = '/\{%\s*for\s+(\w+)\s+in\s+([\w\.\(\)]+)\s*%\}(.*?)\{%\s*endfor\s*%\}/s';
    private const IF_REGEX = '/\{%\s*if\s+(.+?)\s*%\}(.*?)(?:\{%\s*else\s*%\}(.*?))?\{%\s*endif\s*%\}/s';
    private const VAR_REGEX = '/\{\{\s*([^\|\}]+?)(?:\s*\|\s*([^\}]+))?\s*\}\}/';
    private const CONDITION_PATTERN = '/^(.+?)\s*(==|!=)\s*(.+)$/';

    private array $blocks = [];

    public function render(string $templatePath, array $data = []): string
    {
        $fullPath = $this->resolveTemplatePath($templatePath);
        $content = file_get_contents($fullPath);

        $processingOrder = [
            'processIfConditions',
            'processForLoops',
            'processVariables'
        ];

        foreach ($processingOrder as $method) {
            $content = $this->$method($content, $data);
        }

        return $content;
    }

    private function resolveTemplatePath(string $path): string
    {
        $fullPath = realpath(__DIR__.'/../public/templates/'.$path);

        if ($fullPath === false) {
            throw new \RuntimeException("Template not found: $path");
        }

        return $fullPath;
    }

    private function processIfConditions(string $content, array $data): string
    {
        return preg_replace_callback(
            self::IF_REGEX,
            function($matches) use ($data) {
                $condition = trim($matches[1]);
                $ifContent = $matches[2] ?? '';
                $elseContent = $matches[3] ?? '';

                if ($this->evaluateCondition($condition, $data)) {
                    return $ifContent;
                } else {
                    return $elseContent;
                }
            },
            $content
        );
    }

    private function processForLoops(string $content, array $data): string
    {
        return preg_replace_callback(
            self::BLOCK_REGEX,
            function($matches) use ($data) {
                $itemName = $matches[1];
                $itemsKey = $matches[2];
                $template = $matches[3];

                $items = $this->getVariableValue($itemsKey, $data);

                if (!is_iterable($items)) {
                    return '';
                }

                $result = '';
                foreach ($items as $item) {
                    $result .= $this->renderTemplatePart($template, $itemName, $item);
                }

                return $result;
            },
            $content
        );
    }

    private function renderTemplatePart(string $template, string $itemName, $item): string
    {
        return preg_replace_callback(
            self::VAR_REGEX,
            function($matches) use ($itemName, $item) {
                $varPath = $matches[1];
                $filters = isset($matches[2]) ? explode('|', $matches[2]) : [];

                if (str_starts_with($varPath, "$itemName.")) {
                    $property = substr($varPath, strlen($itemName) + 1);
                    $value = $this->extractValue($item, $property);
                } else {
                    $value = $this->extractValue($item, $varPath);
                }

                foreach ($filters as $filter) {
                    $value = $this->applyFilter(trim($filter), $value);
                }

                return htmlspecialchars((string)$value);
            },
            $template
        );
    }

    private function processVariables(string $content, array $data): string
    {
        return preg_replace_callback(
            self::VAR_REGEX,
            function($matches) use ($data) {
                $varPath = $matches[1];
                $filters = isset($matches[2]) ? explode('|', $matches[2]) : [];
                $value = $this->getVariableValue($varPath, $data);

                foreach ($filters as $filter) {
                    $value = $this->applyFilter(trim($filter), $value);
                }

                return htmlspecialchars((string)$value);
            },
            $content
        );
    }

    private function evaluateCondition(string $condition, array $data): bool
    {
        if (preg_match(self::CONDITION_PATTERN, $condition, $matches)) {
            $left = $this->getVariableValue(trim($matches[1]), $data);
            $operator = $matches[2];
            $right = trim($matches[3], '\'" ');

            return match ($operator) {
                '==' => $left == $right,
                '!=' => $left != $right,
                default => false,
            };
        }

        $value = $this->getVariableValue($condition, $data);
        return !empty($value);
    }

    private function getVariableValue(string $path, array $data)
    {
        $parts = explode('.', $path);
        $value = $data;

        foreach ($parts as $part) {
            $value = $this->extractValue($value, $part);
            if ($value === null) {
                return null;
            }
        }

        return $value;
    }

    private function extractValue($context, string $property)
    {
        if (is_object($context)) {
            $method = 'get'.ucfirst($property);
            if (method_exists($context, $method)) {
                return $context->$method();
            }
            if (property_exists($context, $property)) {
                return $context->$property;
            }
            return null;
        } elseif (is_array($context) && array_key_exists($property, $context)) {
            return $context[$property];
        }
        return null;
    }

    private function applyFilter(string $filter, $value): string
    {
        return match ($filter) {
            'escape' => htmlspecialchars((string)$value),
            default => $value,
        };
    }
}