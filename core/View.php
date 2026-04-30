<?php
class View
{
    public static function render(string $template, array $data = []): void
    {
        extract($data, EXTR_OVERWRITE);
        $viewPath = __DIR__ . '/../views/' . $template;

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "La vista solicitada no existe: {$template}";
            return;
        }

        require $viewPath;
    }
}
