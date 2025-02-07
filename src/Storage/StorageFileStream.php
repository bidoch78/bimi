<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Storage;

use Psr\Http\Message\StreamInterface;

class StorageFileStream implements StreamInterface {

    //Based on guzzle stream class
    //https://github.com/guzzle/streams/blob/master/src/Stream.php

    private int $_currentPosition = 0;
    private mixed $_resource = null;
    private ?array $_metadata = null;
    private ?int $_size = null;

    public function __construct($stream, $options = [])
    {
        if (!is_resource($stream)) throw new \InvalidArgumentException('Stream must be a resource');

        if (isset($options['size'])) $this->_size = $options['size'];
      
        $this->_metadata = isset($options['metadata']) ? $options['metadata']: [];

        $this->attach($stream);

    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString(): string {
        if (!$this->_resource || !$this->isRead()) return "";
        $this->rewind(0);
        $content = stream_get_contents($this->stream);
        return $content === false ? "" : $content;
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close(): void {
        if (is_resource($this->_resource)) fclose($this->_resource);
        $this->detach();
    }

    public function attach($resource) {
        $this->_resource = $resource;
        $this->_metadata = array_merge($this->_metadata, stream_get_meta_data($resource));

        $this->_metadata["is_readable"] = isset(self::$readWriteHash['read'][$this->_metadata['mode']]);
        $this->_metadata["is_writable"] = isset(self::$readWriteHash['write'][$this->_metadata['mode']]);
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach() {
       $this->_currentPosition = 0;
       $this->_resource = null;
       $this->_metadata = null;
       $this->_size = null;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize(): ?int {
      
        if ($this->_size !== null) return $this->_size;

        if (!$this->_resource) return null;

        // Clear the stat cache if the stream has a URI
        if (isset($this->_metadata["uri"])) clearstatcache(true, $this->_metadata["uri"]);

        $stats = fstat($this->_resource);
        if (isset($stats['size'])) $this->_size = $stats['size'];

        return $this->_size;

    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell(): int {
        return $this->_resource ? ftell($this->_resource) : false;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof(): bool {
        return !$this->_resource || feof($this->_resource);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable(): bool {
        return isset($this->_metadata['seekable']) ? $this->_metadata['seekable'] : false;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek(int $offset, int $whence = SEEK_SET): void {

    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind(): void {
        if ($this->_currentPosition && !$this->isSeekable()) throw new \RuntimeException('Stream not seekable');
        $this->_currentPosition = 0;
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable(): bool {
        return isset($this->_metadata['is_writable']) ? $this->_metadata['is_writable'] : false;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable(): bool {
        return isset($this->_metadata['is_readable']) ? $this->_metadata['is_readable'] : false;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read(int $length): string {

    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write(string $string): int {
        
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents(): string {
        if (!$this->isReadable()) throw new \RuntimeException('Stream not readable');
        $content = stream_get_contents($this->_resource);
        if (!$content) throw new \RuntimeException('Error error occurs while reading');
        return $content;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string|null $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata(?string $key = null) {

        if (!$this->_metadata) return $key ? null : [];
        if (!$key) return $this->_metadata;
        return isset($this->_metadata[$key]) ? $this->_metadata[$key] : null;

    }

    /** @var array Hash of readable and writable stream types */
    private static $readWriteHash = [
        'read' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true,
            'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a+' => true,
        ],
        'write' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true,
            'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true,
            'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true,
        ],
    ];

}