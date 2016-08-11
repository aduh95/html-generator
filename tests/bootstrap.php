<?php
/**
 * Defining constants used for tests
 * @author aduh95
 */

namespace aduh95\HTMLGenerator;

const DIRECTORY_TEST = __DIR__;

namespace aduh95\HTMLGenerator\Document;

const DIRECTORY_TEST = \aduh95\HTMLGenerator\DIRECTORY_TEST.DIRECTORY_SEPARATOR.'Document';

const MINIMAL_STRING_VALUE = DIRECTORY_TEST.DIRECTORY_SEPARATOR.'minimalStringValue.html';
const FRENCH_STRING_VALUE  = DIRECTORY_TEST.DIRECTORY_SEPARATOR.'frenchStringValue.html';
const HTML4_STRING_VALUE   = DIRECTORY_TEST.DIRECTORY_SEPARATOR.'HTML4StringValue.html';

const HEAD_MODIFIED_HTML = DIRECTORY_TEST.DIRECTORY_SEPARATOR.'headModified.html';
const BODY_MODIFIED_HTML = DIRECTORY_TEST.DIRECTORY_SEPARATOR.'bodyModified.html';

namespace aduh95\HTMLGenerator\Table;

const DIRECTORY_TEST = \aduh95\HTMLGenerator\DIRECTORY_TEST.DIRECTORY_SEPARATOR.'Table';

const WHOLE_TABLE_HTML = DIRECTORY_TEST.DIRECTORY_SEPARATOR.'wholeTable.html';
