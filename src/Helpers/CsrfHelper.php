<?php

namespace MPOS\Helpers;

use EasyCSRF\EasyCSRF;

class CsrfHelper extends EasyCSRF
{
    // Please don't add characters but a-zA-Z0-9 in this field
    const CSRF_TOKEN_SESSION_NAME = 'token';

    public function getCurrentToken(string $key): ?string
    {
        return $this->session->get($this->session_prefix . $key);
    }
}
