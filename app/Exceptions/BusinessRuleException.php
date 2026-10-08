<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a request is valid but violates a business rule
 * (e.g. approving an order that is no longer a draft).
 * Rendered as a redirect back with an error flash message.
 */
class BusinessRuleException extends RuntimeException {}
