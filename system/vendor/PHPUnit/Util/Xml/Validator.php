<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util\Xml;

use function libxml_clear_errors;
use function libxml_get_errors;
use function libxml_use_internal_errors;
use DOMDocument;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Validator
{
    public function validate(DOMDocument $document, $xsdFilename)
    {
        $originalErrorHandling = libxml_use_internal_errors(true);

        $document->schemaValidate($xsdFilename);

        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($originalErrorHandling);

        return ValidationResult::fromArray($errors);
    }
}
