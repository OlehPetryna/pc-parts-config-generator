<?php
declare(strict_types=1);

namespace App\Parser;

use PHPHtmlParser\Curl;
use PHPHtmlParser\Dom;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class PartParser implements LoggerAwareInterface
{
    private $sleepTime;
    private $partsPagesAmount;
    private $baseUrl;
    private $baseListUrl;
    /**@var callable $onPartParsed*/
    private $onPartParsed;

    /**@var LoggerInterface $logger*/
    private $logger;

    private $parsedParts = [];

    public static function instantiate(): self
    {
        return new self();
    }

    public function setSleepTime(int $time): self
    {
        $this->sleepTime = $time;
        return $this;
    }

    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function setBasePaginationUrl(string $baseUrl): self
    {
        $this->baseListUrl = $baseUrl;
        return $this;
    }

    public function setPagesAmount(int $amount): self
    {
        $this->partsPagesAmount = $amount;
        return $this;
    }

    public function setOnPartParsed(callable $fn): self
    {
        $this->onPartParsed = $fn;
        return $this;
    }

    public function parse(): array
    {
        $this->logger->info("Starting parsing ... {$this->baseListUrl}");
        $this->parsePartPages();
        return $this->parsedParts;
    }

    private function parsePartPages(): void
    {
        $this->parsedParts = [];
        foreach (range(1, $this->partsPagesAmount) as $page) {
            $this->logger->info("Will parse page #$page");

            try {
                $pageUrl = "{$this->baseListUrl}/fetch/?mode=list&xslug=&search=&page={$page}";
                $this->parsePartPage($pageUrl);
                $this->wait();
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                continue;
            }
        }
    }

    private function parsePartPage(string $url): void
    {
        $this->logger->info("Parsing page ... {$url}");

        $onPartParsed = $this->onPartParsed;

        $page = $this->loadPageFromJson('html', $url);

        /**@var Dom\HtmlNode[] $detailedViewLinkNodes*/
        $detailedViewLinkNodes = $page->find('.tr__product .td__name a');
        foreach ($detailedViewLinkNodes as $node) {
            $this->logger->info("Parsing detailed page ... {$node->getAttribute('href')}");

            try {
                $part = $this->parseDetailedPartPage($this->baseUrl . $node->getAttribute('href'));
                $onPartParsed($part);
                $this->wait();
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                continue;
            }
        }
    }

    private function parseDetailedPartPage(string $url): array
    {
        $dom = $this->loadGenericPage($url);

        $imgNode = $this->findOne($dom, '.gallery__stage img');
        if (strtolower($imgNode->getTag()->name()) !== 'img') {
            $imgNode = $this->findOne($dom, '.product__image img');
        }

        return [
            'title' => $this->findOne($dom, 'h1.pageTitle')->text(),
            'img' => $imgNode->getAttribute('src'),
            'price' => $this->findOne($dom, '#prices .td__finalPrice a')->text(),
            'specifications' => $this->parseSpecifications($this->findOne($dom, '.specs')),
        ];
    }

    private function loadGenericPage(string $url): Dom
    {
        $dom = new Dom();
        $dom->loadFromUrl($url);

        return $dom;
    }

    private function findOne(Dom $dom, string $selector): Dom\HtmlNode
    {
        $node = $dom->find($selector, 0);
        if ($node === null) {
            $this->logger->info("Cannot find node for ... $selector");
            $node = new Dom\HtmlNode('div');
        }

        return $node;
    }

    private function parseSpecifications(Dom\HtmlNode $specs): array
    {
        $data = [];
        foreach ($specs->find('.group--spec') as $specNode) {
            /**@var Dom\HtmlNode $specNode*/

            $key = trim($specNode->find('.group__title')->text());

            /**@var Dom\HtmlNode $value*/
            $value = $specNode->find('.group__content');
            if ($valueChild = $value->find('p', 0)) {
                $value = $valueChild->text();
            } elseif (($valueChild = $value->find('ul li')) && count($valueChild)) {
                $value = implode(', ', array_map(function (Dom\HtmlNode $li) {
                    return $li->text();
                }, $valueChild->toArray()));
            } else {
                $value = $value->text();
            }

            $data[str_replace('.', '_', $key)] = [
                'key' => $key,
                'value' => trim($value)
            ];
        }

        return $data;
    }

    private function loadPageFromJson(string $htmlKeyInJson, string $url): Dom
    {
        $dom = new Dom();

        $curl = new Curl();
        $content = $curl->get($url);

        $dom->loadStr(json_decode($content, true)['result'][$htmlKeyInJson]);
        return $dom;
    }

    private function wait(): void
    {
//        sleep($this->sleepTime);
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return PartParser
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }
}