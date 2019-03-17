<?php
declare(strict_types=1);

namespace App\Core;

use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;

class Renderer extends PhpRenderer
{
    private $layoutRenderer;

    public function __construct(string $layoutPath, string $templatePath = "", array $attributes = [])
    {
        $this->layoutRenderer = new PhpRenderer($layoutPath);
        $this->setLayoutPath($layoutPath);
        parent::__construct($templatePath, $attributes);
    }

    public function setLayoutPath(string $layoutPath): void
    {
        $this->layoutRenderer->setTemplatePath($layoutPath);
    }

    public function render(ResponseInterface $response, $template, array $data = [])
    {
        $viewContent = $this->fetch($template, $data);

        return $this->layoutRenderer->render($response, '/layout.php', ['content' => $viewContent]);
    }

}