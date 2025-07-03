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
use PHP_Typography\PHP_Typography;
use PHP_Typography\Settings;

require_once __DIR__ . '/vendor/autoload.php';

final class JUTypography extends CMSPlugin implements SubscriberInterface
{
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
	 * @param \Joomla\Event\Event $event
	 *
	 * @return  void
	 */
	public function onContentBeforeSave(Event $event): void
	{
		$context = $event->getArgument('context');
		$article = $event->getArgument('item');

		if($context !== 'com_content.article' || empty($article->introtext))
		{
			return;
		}

		$article->title = $this->typography($article->title, true);

		if(isset($article->text))
		{
			$article->text = $this->typography($article->text);
		}

		if(isset($article->introtext))
		{
			$article->introtext = $this->typography($article->introtext, false, false);
		}

		if(isset($article->fulltext))
		{
			$article->fulltext = $this->typography($article->fulltext);
		}

		$article->metadesc = $this->typography($article->metadesc, true);
	}

	/**
	 * @param        $text
	 * @param bool   $strip
	 * @param bool   $removeAttr
	 *
	 * @return string
	 */
	private function typography($text, bool $strip = false, bool $removeAttr = true): string
	{
		if($strip === true)
		{
			$text = strip_tags($text);
		}
		else
		{
			$text = $this->links($text);

			if(stripos($text, '<p') === false)
			{
				$text = $this->autoParagraphs($text);
			}

			if($removeAttr === true)
			{
				$text = $this->removeAttributesFromTags($text);
			}

			$settings = new Settings();
			$settings->set_tags_to_ignore();
			$settings->set_classes_to_ignore();
			$settings->set_ids_to_ignore();
			// Smart characters.
			$settings->set_smart_quotes();
			$settings->set_smart_quotes_primary();
			$settings->set_smart_quotes_secondary();
			$settings->set_smart_quotes_exceptions();
			$settings->set_smart_dashes();
			$settings->set_smart_dashes_style();
			$settings->set_smart_ellipses();
			$settings->set_smart_diacritics();
			$settings->set_diacritic_language();
			$settings->set_diacritic_custom_replacements();
			$settings->set_smart_marks();
			$settings->set_smart_ordinal_suffix();
			$settings->set_smart_ordinal_suffix_match_roman_numerals();
			$settings->set_smart_math();
			$settings->set_smart_fractions();
			$settings->set_smart_exponents();
			$settings->set_smart_area_units();
			// Smart spacing.
			$settings->set_single_character_word_spacing();
			$settings->set_fraction_spacing();
			$settings->set_unit_spacing();
			$settings->set_french_punctuation_spacing();
			$settings->set_units();
			$settings->set_dash_spacing();
			$settings->set_dewidow();
			$settings->set_max_dewidow_length();
			$settings->set_max_dewidow_pull();
			$settings->set_dewidow_word_number();
			$settings->set_wrap_hard_hyphens();
			$settings->set_url_wrap();
			$settings->set_email_wrap();
			$settings->set_min_after_url_wrap();
			$settings->set_space_collapse();
			// Character styling.
			$settings->set_style_ampersands(false);
			$settings->set_style_caps(false);
			$settings->set_style_initial_quotes(false);
			$settings->set_style_numbers(false);
			$settings->set_style_hanging_punctuation(false);
			$settings->set_initial_quote_tags();
			// Hyphenation.
			$settings->set_hyphenation();
			$settings->set_hyphenation_language('uk-UA');
			$settings->set_min_length_hyphenation();
			$settings->set_min_before_hyphenation();
			$settings->set_min_after_hyphenation();
			$settings->set_hyphenate_headings();
			$settings->set_hyphenate_all_caps();
			$settings->set_hyphenate_title_case();
			$settings->set_hyphenate_compounds();
			$settings->set_hyphenation_exceptions();
			// Parser error handling.
			$settings->set_ignore_parser_errors();

			$typo = new PHP_Typography();
			$text = $typo->process($text, $settings);

			$text = $this->removeStrongHeaders($text);
			$text = $this->removeDashList($text);
			$text = $this->removeEmptyParagraphs($text);
		}

		return $text;
	}

	/**
	 * @param string $html
	 * @param array  $excludeDomains
	 * @param string $currentHost
	 *
	 * @return string
	 */
	protected function links($html, array $excludeDomains = [], $currentHost = null): string
	{
		libxml_use_internal_errors(true);
		if($currentHost === null)
		{
			$currentHost = $_SERVER[ 'HTTP_HOST' ] ?? '';
		}

		$html = trim($html);
		if($html === '')
		{
			return $html;
		}

		$dom = new DOMDocument();
		$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		$links = $dom->getElementsByTagName('a');

		foreach($links as $link)
		{
			$href = $link->getAttribute('href');
			if(empty($href))
			{
				continue;
			}

			if(preg_match('/^(mailto:|tel:|javascript:|#)/i', $href))
			{
				continue;
			}

			$host = parse_url($href, PHP_URL_HOST);
			if(empty($host))
			{
				continue;
			}

			$cleanHost           = preg_replace('/^www\./i', '', $host);
			$cleanCurrent        = preg_replace('/^www\./i', '', $currentHost);
			$excludeDomainsClean = array_map(static fn($d) => preg_replace('/^www\./i', '', $d), $excludeDomains);

			if($cleanHost !== $cleanCurrent && !in_array($cleanHost, $excludeDomainsClean, true))
			{
				$link->setAttribute('target', '_blank');
				$link->setAttribute('rel', 'nofollow noopener noreferrer');
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
	 * @param          $html
	 * @param string[] $tags
	 *
	 * @return string
	 */
	protected function removeAttributesFromTags($html, array $tags = [
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
		libxml_use_internal_errors(true);

		$dom = new DOMDocument();
		$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

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
		return str_replace([ '<li>-', '<li> -' ], '<li>', $text);
	}

	/**
	 * @param        $text
	 *
	 * @return string
	 */
	protected function removeStrongHeaders($text): string
	{
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

	/**
	 * @param        $text
	 *
	 * @return string
	 */
	protected function autoParagraphs($text): string
	{
		$text       = trim($text);
		$paragraphs = preg_split('/\R{2,}/u', $text);

		foreach($paragraphs as &$p)
		{
			$p = '<p>' . nl2br(trim($p)) . '</p>';
		}

		return implode("\n", $paragraphs);
	}
}