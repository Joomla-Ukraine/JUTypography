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
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use PHP_Typography\PHP_Typography;
use PHP_Typography\Settings;

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
	 * @param \Joomla\Event\Event $event
	 *
	 * @return  void
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
		$article->title    = $this->content($article->title, true);
		$article->metadesc = $this->content($article->metadesc, true);

		if(isset($article->text))
		{
			$article->text = $this->content($article->text);
		}

		if(isset($article->introtext))
		{
			$article->introtext = $this->content($article->introtext, false, false);
		}

		if(isset($article->fulltext))
		{
			$article->fulltext = $this->content($article->fulltext);
		}


	}

	/**
	 * @param      $html
	 * @param bool $strip
	 * @param bool $removeAttr
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function content($html, bool $strip = false, bool $removeAttr = true): string
	{
		$html = $this->protectBlocks($html);

		$html = $this->typography($html, $strip, $removeAttr);

		return $this->restoreBlocks($html);
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
			if(stripos($text, '<p') === false)
			{
				$text = $this->autoParagraphs($text);
			}

			if($removeAttr === true)
			{
				$text = $this->removeAttributesFromTags($text);
			}

			$lang     = Factory::getApplication()->getLanguage();
			$settings = new Settings();

			$settings->set_tags_to_ignore();
			$settings->set_classes_to_ignore();
			// Smart characters.
			$settings->set_smart_quotes();
			$settings->set_smart_quotes_primary('doubleGuillemets');
			$settings->set_smart_quotes_secondary();
			$settings->set_smart_quotes_exceptions();
			$settings->set_smart_dashes();
			$settings->set_smart_dashes_style();
			$settings->set_smart_ellipses();
			$settings->set_smart_diacritics();
			$settings->set_diacritic_language($lang->getTag());
			$settings->set_diacritic_custom_replacements();
			$settings->set_smart_marks();
			$settings->set_smart_ordinal_suffix();
			$settings->set_smart_ordinal_suffix_match_roman_numerals(true);
			$settings->set_smart_math();
			$settings->set_smart_fractions();
			$settings->set_smart_exponents();
			$settings->set_smart_area_units();
			// Smart spacing.
			$settings->set_single_character_word_spacing();
			$settings->set_fraction_spacing();
			$settings->set_unit_spacing();
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
			$settings->set_hyphenation(false);

			$typo = new PHP_Typography();
			$text = $typo->process($text, $settings);

			$text = $this->removeStrongHeaders($text);
			$text = $this->removeDashList($text);
			$text = $this->removeEmptyParagraphs($text);
		}

		return $text;
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
		return str_replace([ '<li>-', '<li> -', '<li> &bull;' ], '<li>', $text);
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