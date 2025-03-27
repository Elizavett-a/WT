<?php
class TemplateEngine {
    public function render(string $templatePath, array $data = []): string {
        $content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/templates/$templatePath");

        foreach ($data as $key => $value) {
            if (is_array($value)) continue;
            $content = str_replace("{{ $key }}", htmlspecialchars($value), $content);
        }

        $content = preg_replace_callback(
            '/\{% for (\w+) in (\w+) %\}(.*?)\{% endfor %\}/s',
            function($matches) use ($data) {
                $items = $data[$matches[2]] ?? [];
                $result = '';
                foreach ($items as $item) {
                    $block = $matches[3];
                    foreach ($item as $key => $value) {
                        $block = str_replace("{{ {$matches[1]}.{$key} }}", htmlspecialchars($value), $block);
                    }
                    $result .= $block;
                }
                return $result;
            },
            $content
        );

        return $content;
    }
}