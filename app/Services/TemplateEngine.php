<?php
namespace App\Services;

class TemplateEngine
{
    // Регулярные выражения для парсинга шаблонов
    private const BLOCK_REGEX = '/\{%\s*for\s+(\w+)\s+in\s+(\w+)\s*%\}(.*?)\{%\s*endfor\s*%\}/s';
    private const IF_REGEX = '/\{%\s*if\s+(.+?)\s*%\}(.*?)(?:\{%\s*else\s*%\}(.*?))?\{%\s*endif\s*%\}/s';
    private const UNLESS_REGEX = '/\{%\s*unless\s+(.+?)\s*%\}(.*?)(?:\{%\s*else\s*%\}(.*?))?\{%\s*endunless\s*%\}/s';
    private const INCLUDE_REGEX = '/\{%\s*include\s*[\'"](.+?)[\'"]\s*%\}/';
    private const EXTENDS_REGEX = '/\{%\s*extends\s*[\'"](.+?)[\'"]\s*%\}/';
    private const BLOCK_DEF_REGEX = '/\{%\s*block\s+(\w+)\s*%\}(.*?)\{%\s*endblock\s*%\}/s';
    private const VAR_REGEX = '/\{\{\s*([^\|]+?)(?:\s*\|\s*([^\}]+))?\s*\}\}/';
    private const COMMENT_REGEX = '/\{\#.*?\#\}/s';
    private const FUNCTION_REGEX = '/\{%\s*(\w+)\s*(.*?)\s*%\}/';

    // Хранилище блоков и текущего контекста
    private $blocks = [];
    private $currentBlock = null;
    private $parentTemplate = null;

    public function render(string $templatePath, array $data = []): string
    {
        $fullPath = $this->resolveTemplatePath($templatePath);
        $content = file_get_contents($fullPath);

        // Обработка наследования шаблонов
        if (preg_match(self::EXTENDS_REGEX, $content, $matches)) {
            $this->parentTemplate = $matches[1];
            $content = preg_replace(self::EXTENDS_REGEX, '', $content);
        }

        // Обработка определения блоков
        $content = $this->processBlockDefinitions($content);

        // Последовательная обработка всех конструкций
        $processingOrder = [
            'processComments',
            'processIncludes',
            'processFunctions',
            'processIfConditions',
            'processUnlessConditions',
            'processForLoops',
            'processVariables'
        ];

        foreach ($processingOrder as $method) {
            $content = $this->$method($content, $data);
        }

        // Если есть родительский шаблон, рендерим его
        if ($this->parentTemplate) {
            $parentContent = $this->render($this->parentTemplate, $data);
            $content = $this->applyBlocks($parentContent);
        }

        return $content;
    }

    private function resolveTemplatePath(string $path): string
    {
        $fullPath = realpath(__DIR__.'/../../public/templates/'.$path);

        if ($fullPath === false) {
            throw new \RuntimeException("Template not found: $path");
        }

        return $fullPath;
    }

    private function processBlockDefinitions(string $content): string
    {
        return preg_replace_callback(
            self::BLOCK_DEF_REGEX,
            function($matches) {
                $this->blocks[$matches[1]] = $matches[2];
                return '';
            },
            $content
        );
    }

    private function applyBlocks(string $content): string
    {
        return preg_replace_callback(
            self::BLOCK_DEF_REGEX,
            function($matches) {
                return $this->blocks[$matches[1]] ?? $matches[2];
            },
            $content
        );
    }

    private function processComments(string $content): string
    {
        return preg_replace(self::COMMENT_REGEX, '', $content);
    }

    private function processIncludes(string $content, array $data): string
    {
        return preg_replace_callback(
            self::INCLUDE_REGEX,
            function($matches) use ($data) {
                try {
                    return $this->render($matches[1], $data);
                } catch (\RuntimeException $e) {
                    error_log("Template include error: " . $e->getMessage());
                    return '';
                }
            },
            $content
        );
    }

    private function processFunctions(string $content, array $data): string
    {
        return preg_replace_callback(
            self::FUNCTION_REGEX,
            function($matches) use ($data) {
                $function = $matches[1];
                $args = $matches[2];

                switch ($function) {
                    case 'csrf':
                        return $this->generateCsrfToken();
                    case 'dump':
                        return $this->dumpVariables($args, $data);
                    case 'now':
                        return date('Y-m-d H:i:s');
                    // Добавьте другие функции по необходимости
                    default:
                        return $matches[0]; // Оставляем как есть, если функция не распознана
                }
            },
            $content
        );
    }

    private function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return '<input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'">';
    }

    private function dumpVariables(string $args, array $data): string
    {
        $vars = array_map('trim', explode(',', $args));
        $result = [];

        foreach ($vars as $var) {
            $value = $this->getVariableValue($var, $data);
            $result[] = "$var: " . print_r($value, true);
        };

        return '<pre>'.htmlspecialchars(implode("\n", $result)).'</pre>';
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

    private function processUnlessConditions(string $content, array $data): string
    {
        return preg_replace_callback(
            self::UNLESS_REGEX,
            function($matches) use ($data) {
                $condition = trim($matches[1]);
                $unlessContent = $matches[2] ?? '';
                $elseContent = $matches[3] ?? '';

                if (!$this->evaluateCondition($condition, $data)) {
                    return $unlessContent;
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

                if (!isset($data[$itemsKey])) {
                    return '';
                }

                $result = '';
                foreach ($data[$itemsKey] as $item) {
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
        // Обработка операторов сравнения
        if (preg_match('/^(.+?)\s*(==|!=|>=|<=|>|<)\s*(.+)$/', $condition, $matches)) {
            $left = $this->getVariableValue(trim($matches[1]), $data);
            $operator = $matches[2];
            $right = trim($matches[3], '\'" ');

            switch ($operator) {
                case '==': return $left == $right;
                case '!=': return $left != $right;
                case '>=': return $left >= $right;
                case '<=': return $left <= $right;
                case '>': return $left > $right;
                case '<': return $left < $right;
            }
        }

        // Проверка на существование переменной
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
            // Попробуем получить свойство через метод
            $method = 'get'.ucfirst($property);
            if (method_exists($context, $method)) {
                return $context->$method();
            }
            // Попробуем получить свойство напрямую
            if (property_exists($context, $property)) {
                return $context->$property;
            }
            return null;
        } elseif (is_array($context) && array_key_exists($property, $context)) {
            return $context[$property];
        }
        return null;
    }

    private function applyFilter(string $filter, $value)
    {
        $filterParts = explode(':', $filter, 2);
        $filterName = trim($filterParts[0]);
        $filterArgs = isset($filterParts[1]) ? explode(',', $filterParts[1]) : [];

        switch ($filterName) {
            case 'upper': return strtoupper((string)$value);
            case 'lower': return strtolower((string)$value);
            case 'capitalize': return ucwords((string)$value);
            case 'trim': return trim((string)$value);
            case 'date':
                $format = $filterArgs[0] ?? 'Y-m-d';
                return date($format, strtotime((string)$value));
            case 'default':
                $default = $filterArgs[0] ?? '';
                return empty($value) ? $default : $value;
            // Добавьте другие фильтры по необходимости
            default:
                return $value;
        }
    }
}