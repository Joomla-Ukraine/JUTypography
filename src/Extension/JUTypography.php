<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025 Denes Nosov.
 * @license     GNU General Public License version 3 or later.
 */

namespace JU\Plugin\Content\JUTypography\Extension;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;

// phpcs:enable PSR1.Files.SideEffects

use DOMDocument;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use JUTypography\Typograf;

require_once __DIR__ . '/vendor/autoload.php';

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
			'onContentBeforeSave' => 'onContentBeforeSave'
		];
	}

	/**
	 * Returns the command class for the JUTypography plugin.
	 *
	 * @param   \Joomla\Event\Event  $event
	 *
	 * @return  void
	 * @throws \Exception
	 */
	public function onContentBeforeSave(Event $event): void
	{
		$context = $event->getArgument('context');
		$article = $event->getArgument('item');

		if($context !== 'com_content.article')
		{
			return;
		}

		$article->title    = $this->typography($article->title, true);
		$article->metadesc = $this->typography($article->metadesc, true);

		if(isset($article->introtext))
		{
			$article->introtext = $this->content($article->introtext, false);
		}

		if(isset($article->fulltext))
		{
			$article->fulltext = $this->content($article->fulltext, false);
		}

		if(isset($article->text))
		{
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

		if(strpos($html, '<table') !== false && $this->params->get('fixtable', 1) == 1)
		{
			$html = $this->fixTableStructure($html);
		}

		$html = $this->typography($html, $strip);

		$html = $this->restoreBlocks($html);

		$html = preg_replace('/^<\?xml[^>]*\?>\s*/i', '', $html);
		$html = str_replace('<p><?xml encoding="UTF-8"></p>', '', $html);

		return $html;
	}

	/**
	 * @param        $text
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function protectBlocks($text): string
	{
		$text = preg_replace_callback('/\[(\w+)](.*?)\[\/\1]/si', function ($matches)
		{
			return $this->storePlaceholder($matches[ 0 ]);
		}, $text);

		$text = preg_replace_callback('/\{(\w+)}(.*?)\{\/\1}/si', function ($matches)
		{
			return $this->storePlaceholder($matches[ 0 ]);
		}, $text);

		$text = preg_replace_callback('/\{.*?\}/s', function ($matches)
		{
			return $this->storePlaceholder($matches[ 0 ]);
		}, $text);

		return $text;
	}

	/**
	 * @param        $text
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function restoreBlocks($text): string
	{
		foreach($this->placeholders as $key => $original)
		{
			$text = str_replace($key, $original, $text);
		}

		return $text;
	}

	/**
	 * @param        $text
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function storePlaceholder($text): string
	{
		$key                        = '__PLACEHOLDER_' . $this->placeholderIndex++ . '__';
		$this->placeholders[ $key ] = $text;

		return $key;
	}

	/**
	 * @param         $text
	 * @param   bool  $strip
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function typography($text, bool $strip = false): string
	{
		$typo = new Typograf();
		$typo->enableRule('*');

		$text = $typo->apply($text);
		$text = str_replace([ '  ', '  ' ], ' ', $text);

		if($strip === true)
		{
			$text = strip_tags($text);
			$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		}

		if($strip === false)
		{
			$p = [];
			if($this->params->get('p', 0) == 1)
			{
				$p = [ 'p' ];
			}

			$headdings = [];
			if($this->params->get('headdings', 0) == 1)
			{
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
			if($this->params->get('div', 0) == 1)
			{
				$div = [ 'div' ];
			}

			$span = [];
			if($this->params->get('span', 0) == 1)
			{
				$span = [ 'span' ];
			}

			$table = [];
			if($this->params->get('table', 0) == 1)
			{
				$table = [
					'table',
					'thead',
					'tbody',
					'tr',
					'th',
					'td'
				];
			}

			$tags = array_merge($p, $headdings, $div, $span, $table);
			$text = $this->removeAttributesFromTags($text, $tags);

			$text = $this->removeStrongHeaders($text);
			$text = $this->removeDashList($text);
			$text = $this->removeEmptyParagraphs($text);
		}

		return $text;
	}

	protected function fixTableStructure($html)
	{
		$dom = new \DOMDocument();

		libxml_use_internal_errors(true);
		$dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_clear_errors();

		$tables = $dom->getElementsByTagName('table');
		foreach($tables as $table)
		{
			$thead = $table->getElementsByTagName('thead')->item(0);
			if($thead)
			{
				continue;
			}

			$firstTr = $table->getElementsByTagName('tr')->item(0);
			if(!$firstTr)
			{
				continue;
			}

			$thead = $dom->createElement('thead');
			$table->insertBefore($thead, $firstTr->parentNode);

			$thead->appendChild($firstTr);

			$strongTags = $thead->getElementsByTagName('strong');
			while($strongTags->length > 0)
			{
				$strong   = $strongTags->item(0);
				$textNode = $dom->createTextNode($strong->textContent);
				$strong->parentNode->replaceChild($textNode, $strong);
			}

			$tdTags = $thead->getElementsByTagName('td');
			while($tdTags->length > 0)
			{
				$td = $tdTags->item(0);
				$th = $dom->createElement('th');
				while($td->hasChildNodes())
				{
					$th->appendChild($td->firstChild);
				}

				foreach($td->attributes as $attr)
				{
					$th->setAttribute($attr->name, $attr->value);
				}

				$td->parentNode->replaceChild($th, $td);
			}

			$tbody = $table->getElementsByTagName('tbody')->item(0);
			if(!$tbody)
			{
				$tbody = $dom->createElement('tbody');
				$table->insertBefore($tbody, $thead->nextSibling);
			}

			$trNodes = $table->getElementsByTagName('tr');
			$trCount = $trNodes->length;
			for($i = 0; $i < $trCount; $i++)
			{
				$tr = $trNodes->item(0);
				if($tr->parentNode !== $thead && $tr->parentNode !== $tbody)
				{
					$tbody->appendChild($tr);
				}
			}
		}

		return $dom->saveHTML();
	}

	/**
	 * @param             $text
	 * @param   string[]  $tags
	 *
	 * @return string
	 */
	protected function removeAttributesFromTags($text, array $tags = [
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
	]): string
	{
		if(empty($text))
		{
			return $text;
		}

		libxml_use_internal_errors(true);

		$dom = new DOMDocument();
		$dom->loadHTML(mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8'));

		foreach($tags as $tag)
		{
			$elements = $dom->getElementsByTagName($tag);
			for($i = $elements->length - 1; $i >= 0; $i--)
			{
				$el = $elements->item($i);
				while($el->attributes->length)
				{
					$el->removeAttribute($el->attributes->item(0)->nodeName);
				}
			}
		}

		$body      = $dom->getElementsByTagName('body')->item(0);
		$innerHTML = '';
		foreach($body->childNodes as $child)
		{
			$innerHTML .= $dom->saveHTML($child);
		}

		return $innerHTML;
	}

	/**
	 * @param        $text
	 *
	 * @return string
	 */
	protected function removeDashList($text): string
	{
		return str_replace([ '<li>-', '<li> -', '<li> &bull;' ], '<li>', $text);
	}

	/**
	 * @param        $text
	 *
	 * @return string
	 */
	protected function removeStrongHeaders($text): string
	{
		if(empty($text))
		{
			return $text;
		}

		libxml_use_internal_errors(true);

		$dom = new DOMDocument();
		$dom->loadHTML(mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8'));

		foreach([ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ] as $tag)
		{
			$headings = $dom->getElementsByTagName($tag);
			for($i = 0; $i < $headings->length; $i++)
			{
				$heading = $headings->item($i);
				$strongs = [];
				foreach($heading->getElementsByTagName('strong') as $strong)
				{
					$strongs[] = $strong;
				}

				foreach($strongs as $strong)
				{
					while($strong->firstChild)
					{
						$strong->parentNode->insertBefore($strong->firstChild, $strong);
					}

					$strong->parentNode->removeChild($strong);
				}
			}
		}

		$body      = $dom->getElementsByTagName('body')->item(0);
		$innerHTML = '';
		foreach($body->childNodes as $child)
		{
			$innerHTML .= $dom->saveHTML($child);
		}

		return $innerHTML;
	}

	/**
	 * @param        $text
	 *
	 * @return string
	 */
	protected function removeEmptyParagraphs($text): string
	{
		return preg_replace('~<p[^>]*>(?:\s|&nbsp;|&#160;| |&thinsp;|&ensp;|&emsp;|&ZeroWidthSpace;|&#8203;|&#xfeff;)*</p>~iu', '', $text);
	}
}