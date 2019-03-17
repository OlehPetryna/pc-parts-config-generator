<?php
declare(strict_types=1);

namespace App\Core;

abstract class HtmlAction extends Action
{
    const TEMPLATES_VIEW = __DIR__ . '/../Views';

    private $renderer;
    protected function renderer(): Renderer
    {
        if (!isset($this->renderer)) {
            $this->renderer = new Renderer(self::TEMPLATES_VIEW, self::TEMPLATES_VIEW);
        }

        return $this->renderer;
    }
}