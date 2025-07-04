# JUTypography - Joomla 5 Content Plugin for improving typography

![JUTypography](https://repository-images.githubusercontent.com/1012677337/5e82fefa-937b-4142-b450-87f1521d452f)

JUTypography is a plugin built on the PHP-Typography library, designed to enhance the typographic quality
of articles by automatically applying formatting rules after saving content.

## Donate: Buy Me a Coffee

* [Monobank (Google Pay, Apple Pay or Bank Card)](https://send.monobank.ua/jar/7u4x6vNRZJ)
* [PayPal](https://www.paypal.com/donate/?hosted_button_id=WQJNDPDPDMKP8)

## Core Functionality

1. Applies typography rules to articles upon saving.
2. Removes redundant paragraphs.
3. Strips `<strong>` tags from H1-H6 headings.
4. Eliminates hyphens at the start of lines within `<li>` tags.

**Processed Content**:

1. Fields: `title`, `metadesc`.
2. Article components: `$article->text`, `$article->introtext`, `$article->fulltext`.
3.

**Typography Features**:

- **Hyphenation**: Supports over 50 languages.
- **Space Control**:
    - Prevents widows in text.
    - Glues values to units (e.g., 10px).
    - Forces internal wrapping for long URLs and email addresses.
- **Intelligent Character Replacement**:
    - Smart handling of single (‘ ’) and double (“ ”) quote marks.
    - Proper formatting of dashes (–).
    - Correct rendering of ellipses (…).
    - Accurate display of trademarks (™), copyright (©), and service marks.
    - Support for math symbols (e.g., 5×5×5=5³).
    - Proper formatting of fractions (e.g., 1⁄16).
    - Automatic ordinal suffixes (e.g., 1st, 2nd).

