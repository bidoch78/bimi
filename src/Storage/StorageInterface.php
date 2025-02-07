<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Storage;

const PERMISSION_OCTAL = 1;
const PERMISSION_STRING = 2;

interface StorageInterface {

    public function getPath(): string;
    public function isDir(): bool;
    public function exists(): bool;
    public function isFile(): bool;
    public function isLink(): bool;
    public function isLocal(): bool;
    public function getName(): ?string;
    public function getBaseName(): ?string;
    public function getDirName(): ?string;
    public function getSize(): ?int;
    public function getModifyTime(): ?\DateTime;
    public function getCreationTime(): ?\DateTime;
    public function getAccessTime(): ?\DateTime;
    public function getGID(): ?int;
    public function getUID(): ?int;
    public function getPermission(int $format = PERMISSION_OCTAL): null|int|string;
    public function hasError(): bool;
    public function refresh(): void;
    public function reset(): void;

}