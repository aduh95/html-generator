#HTML Generator

This project aims to generate valid and XSS-safe HTML from friendly PHP commands. You can use some of the the [jQuery DOM manipulations](http://api.jquery.com/category/manipulation/) methods, because I missed some of them in PHP. The project is built on PHP's [DOM functions](http://php.net/manual/en/book.dom.php), though the performance are quite good.
The goal is to improve readability (even for people who do not know PHP at all) and make it easier to detect and to avoid XSS. You won't need the `?>` closing tag anymore!


If you think this librairy lacks a feature or have some bad design, feel free to contribute or to raise an issue.

## Installation

The easiest way: using [Composer](http://getcomposer.com)

```sh
composer install aduh95/html-generator
```

If you don't use Composer (and really you should!), you can also clone this repo and include the PHP classes which follow the [PSR-4](www.php-fig.org/psr/psr-4/).

## Getting started

I would recommend to subclass the `Document` class to include your usual html tags. As soon as I have time, I will put examples in the wiki.

Here is an overview of the main features:

```php
<?php
require 'vendor/autoload.php';

$doc = new aduh95\HTMLGenerator\Document('My title', 'en');

// If you want PHP to render you HTML in a way a human can read it
// Default is compressed
$doc->getDOMDocument()->formatOutput = true;

// Add attribute array-like
$doc->getHead()->appendChild($doc->createElement('meta'))['charset'] = 'uft-8';
// Or add attribute jQuery-like
$doc->getHead()->link()->attr('rel', 'icon')->attr(['type'=>'image/png', 'href'=>'/asset/icon.png']);

$doc()->p()->text('<script>alert("no XSS!");</script>') // add XSS-protected text easily
    ()->p()->append() // add children to an element with a simple method call
        ()->b('You are looking for something, aren\'t you?') // Add text content
        ()->br() // Auto closing tags are handled
        ()->a(
            ['href'=>'http://google.fr/', 'alt'=>'Search the "web"'], // An other method to add attributes
            'YES, YOU CAN FIND IT!'
        )->data('user-color', 'red') // as in jQuery, you can set a "data-" attribute
        ()->br()
        ()->smaller(['class'=>'alert alert-info'])->text('This link is sponsored.')
        ()
    ()->p('I ♥ Rock\'n\'Roll!')
        ->attr('test', 4)
        ->data('HTMLCamelCaseDataInformation', 'valid') // Transform CamelCase dataset to snake_case to match W3C standard
;

// List shortcut
$list = $doc()->ul();
$list[] = $doc->createTextNode('First <item>');
$list[] = $doc->createElement('b')->text('second one');
$list->append('third one');
$list->append()
    ()->li('fourth one');

// Table shortcut
$table = $doc()->table();
$table[] = ['This', 'is', 'a', 'row'];
$table[] = [$doc->createElement('td')->attr('rowspan', 3)->text('another'), 'one'];
$table
    ->append(['data', 'in', 'the', 'row'])
    ->append([['multi', 'row'], ['so', 'easy']]);

// This line is optionnal, the document will be automatically output at the end of ths script
echo $doc;
?>
```

This will output:

```html
<!DOCTYPE html>
<html lang="en">
<head>
<title>My title</title>
<meta charset="uft-8">
<link rel="icon" type="image/png" href="/asset/icon.png">
</head>
<body>
<p>&lt;script&gt;alert("XSS!");&lt;/script&gt;</p>
<p><b>You are looking for something, aren't you?</b><br><a href="http://google.fr/" alt='Search the "web"' data-user-col
or="red">YES, YOU CAN FIND IT!</a><br><smaller class="alert alert-info">This link is sponsored.</smaller></p>
<p test="4" data-html-camel-case-data-information="valid">I &hearts; Rock'n'Roll!</p>
<ul>
<li>First &lt;item&gt;</li>
<li><b>second one</b></li>
<li>third one</li>
<li>fourth one</li>
</ul>
<table><tbody>
<tr>
<td>This</td>
<td>is</td>
<td>a</td>
<td>row</td>
</tr>
<tr>
<td rowspan="3">another</td>
<td>one</td>
</tr>
<tr>
<td>data</td>
<td>in</td>
<td>the</td>
<td>row</td>
</tr>
<tr>
<td>multi</td>
<td>row</td>
</tr>
<tr>
<td>so</td>
<td>easy</td>
</tr>
</tbody></table>
</body>
</html>
```


This project is inspired from [`airmanbzh/php-html-generator`](https://github.com/Airmanbzh/php-html-generator) and [`wa72/htmlpagedom`](https://github.com/wasinger/htmlpagedom).
