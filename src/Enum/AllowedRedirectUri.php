<?php

declare(strict_types=1);

namespace App\Enum;

enum AllowedRedirectUri: string
{
    case ClaudeAi = 'https://claude.ai/api/mcp/auth_callback';
}
