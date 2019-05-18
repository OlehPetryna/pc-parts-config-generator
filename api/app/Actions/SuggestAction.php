<?php
declare(strict_types=1);

namespace App\Actions;

use App\Core\HtmlAction;
use App\Domain\PickerWizard\Wizard;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SuggestAction extends HtmlAction
{
    private $wizard;

    public function __construct(Wizard $wizard)
    {
        $this->wizard = $wizard;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response = $this->wizard->removeState($response);
        return $this->renderer()->render($response, '/suggest.php', ['showErrorMessage' => (bool)$request->getAttribute('error')]);
    }
}