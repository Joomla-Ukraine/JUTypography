<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025-2026 Denys Nosov
 * @license     GNU General Public License version 3 or later.
 */

namespace JU\Plugin\Content\JUTypography\Extension;

defined('_JEXEC') or die;

use DOMDocument;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use JUTypography\Typograf;

require_once __DIR__.'/vendor/autoload.php';

final class JUTypography extends CMSPlugin implements SubscriberInterface
{
    protected array $placeholders = [];
    protected int $placeholderIndex = 0;

    /**
     * Returns the event this subscriber will listen to.
     *
     * @return  array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onContentBeforeSave' => 'onContentBeforeSave',
        ];
    }

    /**
     * Returns the command class for the JUTypography plugin.
     *
     * @param \Joomla\Event\Event $event
     *
     * @return  void
     * @throws \Exception
     */
    public function onContentBeforeSave(Event $event): void
    {
        $context = $event->getArgument('context');
        $article = $event->getArgument('item');

        if ($context !== 'com_content.article') {
            return;
        }

        $article->title = $this->typography($article->title, true);
        $article->metadesc = $this->typography($article->metadesc, true);

        if (isset($article->introtext)) {
            $article->introtext = $this->content($article->introtext, false);
        }

        if (isset($article->fulltext)) {
            $article->fulltext = $this->content($article->fulltext, false);
        }

        if (isset($article->text)) {
            $article->text = $this->content($article->text, false);
        }
    }

    /**
     * @param      $html
     * @param      $strip
     *
     * @return string
     * @throws \Exception
     */
    protected function content($html, $strip): string
    {
        $html = $this->protectBlocks($html);

        if (strpos($html, '<table') !== false && $this->params->get(
                'fixtable',
                1
            ) == 1) {
            $html = $this->fixTableStructure($html);
        }

        $html = $this->typography($html, $strip);

        $html = $this->restoreBlocks($html);

        $html = preg_replace('/^<\?xml[^>]*\?>\s*/i', '', $html);
        $html = str_replace([
            '<?xml encoding="UTF-8">',
            '<p><?xml encoding="UTF-8"></p>',
            '<!--?xml encoding="UTF-8"-->',
        ], '', $html);

        return $html;
    }

    /**
     * @param        $html
     *
     * @return string
     * @throws \Exception
     */
    protected function protectBlocks($html): string
    {
        $html = preg_replace_callback(
            '/\[(\w+)](.*?)\[\/\1]/si',
            function ($matches) {
                return $this->storePlaceholder($matches[0]);
            },
            $html
        );

        $html = preg_replace_callback(
            '/\{(\w+)}(.*?)\{\/\1}/si',
            function ($matches) {
                return $this->storePlaceholder($matches[0]);
            },
            $html
        );

        return preg_replace_callback('/\{.*?\}/s', function ($matches) {
            return $this->storePlaceholder($matches[0]);
        }, $html);
    }

    /**
     * @param        $html
     *
     * @return string
     * @throws \Exception
     */
    protected function restoreBlocks($html): string
    {
        foreach ($this->placeholders as $key => $original) {
            $html = str_replace($key, $original, $html);
        }

        return $html;
    }

    /**
     * @param        $html
     *
     * @return string
     * @throws \Exception
     */
    protected function storePlaceholder($html): string
    {
        $key = '__PLACEHOLDER_'.$this->placeholderIndex++.'__';
        $this->placeholders[$key] = $html;

        return $key;
    }

    /**
     * @param         $html
     * @param bool $strip
     *
     * @return string
     * @throws \Exception
     */
    protected function typography($html, bool $strip = false): string
    {
        $typo = new Typograf();
        $typo->enableRule('*');
        $html = $typo->apply($html);

        if ($strip === true) {
            $html = strip_tags($html);
        }

        if ($strip === false) {
            $p = [];
            if ($this->params->get('p', 0) == 1) {
                $p = ['p'];
            }

            $headdings = [];
            if ($this->params->get('headdings', 0) == 1) {
                $headdings = [
                    'h1',
                    'h2',
                    'h3',
                    'h4',
                    'h5',
                    'h6',
                ];
            }

            $div = [];
            if ($this->params->get('div', 0) == 1) {
                $div = ['div'];
            }

            $span = [];
            if ($this->params->get('span', 0) == 1) {
                $span = ['span'];
            }

            $table = [];
            if ($this->params->get('table', 0) == 1) {
                $table = [
                    'table',
                    'thead',
                    'tbody',
                    'tr',
                    'th',
                    'td',
                ];
            }

            $tags = array_merge(
                $p,
                $headdings,
                $div,
                $span,
                $table
            );

            $html = $this->removeAttributesFromTags(
                $html,
                $tags
            );

            $html = $this->removeStrongHeaders($html);
            $html = $this->removeDashList($html);
            $html = $this->removeEmptyParagraphs($html);

            if ($this->params->get('style', 0) == 1) {
                $html = $this->removeStyles($html);
            }

            if ($this->params->get('attr', 0) == 1) {
                $html = $this->removeDataAttributes($html);
            }
        }

        return $text;
        return $html;
    }

    /**
     * @param DOMDocument $dom
     * @param string $html
     *
     * @return bool
     *
     * @since 1.0
     */
    private function loadHTMLSafely(
        DOMDocument $dom,
        string $html
    ): bool {
        libxml_use_internal_errors(true);
        libxml_clear_errors();

        $html = '<?xml encoding="UTF-8"?>'.$html;

        $result = $dom->loadHTML(
            $html,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR
        );

        foreach ($dom->childNodes as $item) {
            if ($item->nodeType === XML_PI_NODE) {
                $dom->removeChild($item);

                break;
            }
        }

        $dom->encoding = 'UTF-8';

        return $result;
    }

    /**
     * @param DOMDocument $dom
     *
     * @return string
     *
     * @since 1.0
     */
    private function getInnerHTML(DOMDocument $dom): string
    {
        $body = $dom->getElementsByTagName('body')->item(0);

        if (!$body) {
            return $dom->saveHTML();
        }

        $innerHTML = '';

        foreach ($body->childNodes as $child) {
            $innerHTML .= $dom->saveHTML($child);
        }

        return $innerHTML;
    }

    protected function removeDataAttributes(string $html): string
    {
        if (empty(trim($html))) {
            return $html;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $this->loadHTMLSafely($dom, $html);

        $xpath = new \DOMXPath($dom);

        /*
         * TinyMCE
         */
        foreach ($xpath->query('//@*[starts-with(name(), "data-mce-")]') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        foreach ($xpath->query('//@*[starts-with(name(), "mce-")]') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        foreach ($xpath->query('//@data-path') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        /*
         * HTML attributes
         */
        foreach ($xpath->query('//@lang') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        foreach ($xpath->query('//@align') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        foreach ($xpath->query('//@dir') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        foreach ($xpath->query('//@aria-level') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        foreach ($xpath->query('//@role') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        /*
         * AI
         */
        foreach ($xpath->query('//@*[contains(name(), "ng-")]') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        foreach ($xpath->query('//@*[contains(name(), "_ngcontent-ng-")]') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        foreach ($xpath->query('//@data-section-id') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        foreach ($xpath->query('//@data-start') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        foreach ($xpath->query('//@data-end') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        foreach ($xpath->query('//@data-is-last-node') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        foreach ($xpath->query('//@data-is-only-node') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        foreach ($xpath->query('//@data-col-size') as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }

        $body = $xpath->query('/html/body')->item(0);
        if ($body) {
            $cleanHtml = '';
            foreach ($body->childNodes as $node) {
                $cleanHtml .= $dom->saveHTML($node);
            }

            return $cleanHtml;
        }

        return $this->getInnerHTML($dom);
    }

    /**
     * @param $html
     *
     * @return false|string
     */
    protected function removeStyles($html)
    {
        if (empty(trim($html))) {
            return $html;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $this->loadHTMLSafely($dom, $html);

        $elements = $dom->getElementsByTagName('*');

        foreach ($elements as $element) {
            if ($element->hasAttribute('style')) {
                $style_attribute_value = $element->getAttribute('style');

                if (preg_match('/text-align:\s*(.*?)(;|$)/i', $style_attribute_value, $matches)) {
                    $align_value = trim($matches[1]);

                    if (stripos($align_value, 'justify') !== false) {
                        $element->removeAttribute('style');
                    } else {
                        $element->setAttribute('style', 'text-align: '.$align_value.';');
                    }
                } else {
                    $element->removeAttribute('style');
                }
            }
        }

        return $this->getInnerHTML($dom);
    }

    /**
     * @param $html
     *
     * @return false|string
     *
     * @throws \DOMException
     */
    protected function fixTableStructure($html): bool|string
    {
        if (empty(trim($html))) {
            return $html;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $this->loadHTMLSafely($dom, $html);

        $tables = $dom->getElementsByTagName('table');

        foreach ($tables as $table) {
            $thead = $table
                ->getElementsByTagName('thead')
                ->item(0);

            if ($thead) {
                continue;
            }

            $firstTr = $table
                ->getElementsByTagName('tr')
                ->item(0);

            if (!$firstTr) {
                continue;
            }

            $thead = $dom->createElement('thead');
            $table->insertBefore($thead, $firstTr->parentNode);

            $thead->appendChild($firstTr);

            $strongTags = $thead->getElementsByTagName('strong');
            while ($strongTags->length > 0) {
                $strong = $strongTags->item(0);
                $textNode = $dom->createTextNode($strong->textContent);

                $strong->parentNode->replaceChild($textNode, $strong);
            }

            $tdTags = $thead->getElementsByTagName('td');
            while ($tdTags->length > 0) {
                $td = $tdTags->item(0);
                $th = $dom->createElement('th');

                while ($td->hasChildNodes()) {
                    $th->appendChild($td->firstChild);
                }

                foreach ($td->attributes as $attr) {
                    $th->setAttribute($attr->name, $attr->value);
                }

                $td->parentNode->replaceChild($th, $td);
            }

            $tbody = $table->getElementsByTagName('tbody')->item(0);
            if (!$tbody) {
                $tbody = $dom->createElement('tbody');

                $table->insertBefore($tbody, $thead->nextSibling);
            }

            $trNodes = $table->getElementsByTagName('tr');
            $trCount = $trNodes->length;
            for ($i = 0; $i < $trCount; $i++) {
                $tr = $trNodes->item(0);

                if ($tr->parentNode !== $thead && $tr->parentNode !== $tbody) {
                    $tbody->appendChild($tr);
                }
            }
        }

        return $this->getInnerHTML($dom);
    }

    /**
     * @param             $html
     * @param string[] $tags
     *
     * @return string
     */
    protected function removeAttributesFromTags(
        $html,
        array $tags = [
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'div',
            'span',
            'p',
            'table',
            'tr',
            'td',
        ]
    ): string {
        if (empty(trim($html))) {
            return $html;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $this->loadHTMLSafely($dom, $html);

        foreach ($tags as $tag) {
            $elements = $dom->getElementsByTagName($tag);

            for ($i = $elements->length - 1; $i >= 0; $i--) {
                $el = $elements->item($i);

                while ($el->attributes->length) {
                    $el->removeAttribute($el->attributes->item(0)->nodeName);
                }
            }
        }

        return $this->getInnerHTML($dom);
    }

    /**
     * @param        $html
     *
     * @return string
     */
    protected function removeDashList($html): string
    {
        if (empty(trim($html))) {
            return $html;
        }

        return str_replace(['<li>-', '<li> -', '<li> &bull;'], '<li>', $html);
    }

    /**
     * @param        $html
     *
     * @return string
     */
    protected function removeStrongHeaders($html): string
    {
        if (empty(trim($html))) {
            return $html;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $this->loadHTMLSafely($dom, $html);

        foreach (['h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $tag) {
            $headings = $dom->getElementsByTagName($tag);

            for ($i = 0; $i < $headings->length; $i++) {
                $heading = $headings->item($i);

                $strongs = [];
                foreach ($heading->getElementsByTagName('strong') as $strong) {
                    $strongs[] = $strong;
                }

                foreach ($strongs as $strong) {
                    while ($strong->firstChild) {
                        $strong->parentNode->insertBefore(
                            $strong->firstChild,
                            $strong
                        );
                    }

                    $strong->parentNode->removeChild($strong);
                }
            }
        }

        return $this->getInnerHTML($dom);
    }

    /**
     * @param        $html
     *
     * @return string
     */
    protected function removeEmptyParagraphs($html): string
    {
        if (empty(trim($html))) {
            return $html;
        }

        $html = str_replace(['  ', '  '], ' ', $html);

        return preg_replace(
            '~<p[^>]*>(?:\s|&nbsp;|&#160;| |&thinsp;|&ensp;|&emsp;|&ZeroWidthSpace;|&#8203;|&#xfeff;)*</p>~iu',
            '',
            $html
        );
    }
}