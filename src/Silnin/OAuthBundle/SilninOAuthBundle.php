<?php

namespace Silnin\OAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SilninOAuthBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSOAuthServerBundle';
    }
}
