<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Exception;

use RuntimeException;

final class AuthenticationFailed extends RuntimeException
{
    public static function afterAuthentication(): self
    {
        return new self('The authentication failed because the auth token is still null after authentication was performed.');
    }

    public static function invalidReponse(string $invalidField): self
    {
        return new self(sprintf('The authentication failed because the expected response field "%s" is either missing or invalid.', $invalidField));
    }

    public static function withRequestFailed(RequestFailed $exception): self
    {
        return new self('The authentication failed with an exception.', previous: $exception);
    }
}
