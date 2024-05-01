<?php

namespace Piwik\Plugins\VipDetector\libs;

use Exception;

class NotFoundException extends Exception
{
    /**
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message = '', int $code = 422)
    {
        parent::__construct($message, $code);
        $this->message = $message;
        $this->code = $code;
    }
}
