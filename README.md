#HTML Generator

This project is still under developpment, although is very stable. Version `1.0.0` will be released soon. Feel free to contribute or to raise an issue.

This project aims to generate valid and XSS-safe HTML from friendly PHP commands. You can use some of the the [jQuery DOM manipulations](http://api.jquery.com/category/manipulation/) methods, because I missed some of them in PHP. The project is built on PHP's [DOM functions](http://php.net/manual/en/book.dom.php), though the performance are quite good.
The goal is to be able to "get rid of the `?>` closing tags", to improve readability, even for people who do not know PHP at all.

Here is an overview:

```php
<?php
require 'vendor/autoload.php';

$doc = new aduh95\HTMLGenerator\Document('My title', 'en');

// Add attribute array-like
$doc->getHeadNode()->appendChild($doc->createElement('meta'))['charset'] = 'uft-8';

$doc()->p()->text('<script>alert("XSS!");</script>') // add XSS-protected text easily
    ()->p()->append() // add children to an element with a simple method call
        ()->b('You are looking for something, aren\'t you?') // Add text content
        ()->br() // Auto closing tags are handled
        ()->a(
            ['href'=>'http://google.fr/', 'alt'=>'Search the "web"'], // An other method to add attributes
            'YES, YOU CAN FIND IT!'
        )->data('user-color', 'red')
        ()->br()
        ()->smaller(['class'=>'alert alert-info'])->text('This link is sponsored.')
        ()
    ()->p('I ♥ Rock\'n\'Roll!')
        ->attr('test', 4) // add attribute jQuery-like
        ->data('HTMLCamelCaseDataInformation', 'valid') // Transform CamelCase dataset to snake_case to match W3C standard
;

// If you want PHP to render you HTML in a way a human can read it
$doc->getDOMDocument()->formatOutput = true;

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
</head>
<body>
<p>&lt;script&gt;alert("XSS!");&lt;/script&gt;</p>
<p><b>You are looking for something, aren't you?</b><br><a href="http://google.fr/" alt='Search the "web"' data-user-color="red">YES, YOU CAN FIND IT!</a><br><smaller class="alert alert-info">This link is sponsored.</smaller></p>
<p test="4" data-html-camel-case-data-information="valid">I &hearts; Rock'n'Roll!</p>
</body>
</html>
```


This project is inspired from [`airmanbzh/php-html-generator`](https://github.com/Airmanbzh/php-html-generator) and [`wa72/htmlpagedom`](https://github.com/wasinger/htmlpagedom).
