<?php declare(strict_types=1);

namespace DAIA;

/**
 * A DAIA Response body.
 *
 * See <https://purl.org/NET/DAIA#daia-response>
 * @package DAIA
 */
class Response extends HTTPResponse
{
    protected $document = [];
    protected $institution;
    protected $timestamp;

    public function __construct(array $data=[])
    {
        $this->timestamp = date("c", time());
        parent::__construct($data);
    }

    public function getStatusCode(): int
    {
        return 200;
    }

    protected function fieldRepeatable($field): bool {
        return $field == 'document';
    }

    protected function setTimestamp($value) {
        if (Data::isDatetime($value)) {
            $this->timestamp = $value;
        }
    }
}
