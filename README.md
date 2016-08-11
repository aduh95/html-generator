#HTML Generator

This project is still under developpment, the API may change at any moment. Feel free to contribute or to raise an issue.

This project aims to generate valid and XSS-safe HTML from friendly PHP command. I'll try to implement [jQuery DOM manipulations](http://api.jquery.com/category/manipulation/) methods to make our life of web developper easier. The project is built on PHP's [DOM functions](http://php.net/manual/en/book.dom.php), though the performance are quite good.

```php
<?php
require 'vendor/autoload.php';

$doc = new aduh95\HTMLGenerator\Document('My title', 'en');

// Add attribute array-like
$doc->getHeadNode()->appendChild($doc->createElement('meta'))['charset'] = 'uft-8';

$doc()->text('Test') // add text easily
    ()->text('<script>alert("XSS!");</script>') // XSS protection
    ()->p()->append() // add children to an element with a simple method call
        ()->b('You are looking for something, aren\'t you?')
        ()->br()
        ()->a(['href'=>'http://google.fr/', 'alt'=>'Search the "web"'], 'YES, YOU CAN FIND IT!')
        () // shortcut for getParent
    ()->p('I ♥ Rock\'n\'Roll!')->attr('test', 4) // add attribute jQuery-like
;

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
<body>Test&lt;script&gt;alert("XSS!");&lt;/script&gt;<p><b>You are looking for something, aren't you?</b><br><a href="ht
tp://google.fr/" alt='Search the "web"'>YES, YOU CAN FIND IT!</a></p>
<p test="4">I &hearts; Rock'n'Roll!</p>
</body>
</html>
```


This project is inspired from [`airmanbzh/php-html-generator`](https://github.com/Airmanbzh/php-html-generator) and [`wa72/htmlpagedom`](https://github.com/wasinger/htmlpagedom).
