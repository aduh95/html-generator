# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

This file follows the [Keep A Changelog](http://keepachangelog.com/en/0.3.0/) principles.  
This file is written using [Markdown syntax](http://daringfireball.net/projects/markdown/syntax).

## [Unreleased]
### Changed
- If a string is passed as `src` attribute for a `<video>` element, it will no longer create a `<source>` child.
- Patch a bug on `HTMLList` when trying to use the usual `HTMLElement` behaviour
- When passing `<td>` elements to a table row, it will be used to create the actual table cell

### Added
- `Document::createTextNode` method, which is just a shortcut for the DOM method


## [0.3.0] - 2016-09-13
### Added
- The `HTMLList` class
- The `Form` class and the `<input>` generation method
- The `<video>` generation method
- The `Head::script` and `Head::style` methods


## [0.2.0] - 2016-08-21
### Added
- HTML entities support
- The string parsing now support non 'XML valid' strings (such as JS or CSS code)
- Add the `Head` class which represents the HTML `<head>` element

### Changed
- Rename `BodyElement` class to `Body`
- The `EmptyElement`s are now `DOMNode`s
- The `HTMLElement::append` and `HTMLElement::prepend` methods now return always the HTMLElement, to replicate the jQuery's methods behaviour

### Removed
- The `wa72/htmlpagedom` dependency to improve performances

## [0.1.0] - 2016-08-14
### Added
- Initial beta release
