# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

This file follows the [Keep A Changelog](http://keepachangelog.com/en/0.3.0/) principles.  
This file is written using [Markdown syntax](http://daringfireball.net/projects/markdown/syntax).

## [Unreleased]


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
