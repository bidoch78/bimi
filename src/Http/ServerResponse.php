<?php

namespace Bidoch78\Bimi\Http;

use Psr\Http\Message\ResponseInterface;

class ServerResponse implements ResponseInterface {

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface {

    }

    public function getStatusCode(): int {

    }

    public function getReasonPhrase(): string {

    }

    public function getProtocolVersion(): string {

    }

    public function withProtocolVersion(string $version): MessageInterface {

    }

    public function getHeaders(): array {

    }

    public function hasHeader(string $name): bool {

    }

    public function getHeaderLine(string $name): string {

    }

    public function withHeader(string $name, $value): MessageInterface {

    }

    public function withAddedHeader(string $name, $value): MessageInterface {

    }

    public function withoutHeader(string $name): MessageInterface {

    }

    public function getBody(): StreamInterface {

    }

    public function withBody(StreamInterface $body): MessageInterface {

    }

}