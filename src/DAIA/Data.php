<?php
declare(strict_types=1);

namespace DAIA;

/**
 * Abstract base class of objects in the DAIA data model.
 */
class Data implements \JsonSerializable
{

    public function __construct($data=[])
    {
        foreach ($this as $field => $value) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
    }

    /**
     * Returns a copy of the data structure to be serialized to JSON.
     *
     * @param boolean $root whether this is the root element
     */
    public function jsonSerialize($root=true)
    {
        $json = [];

        foreach ($this as $field => $value) {
            if (!is_null($value)) {
                $json[$field] = $value;
            }
        }

        // sort keys to get stable serialization
        ksort($json);

        return (object)$json;
    }
    
    /**
     * Serialize to JSON in string context for debugging.
     */
    public function __toString()
    {
        return json_encode($this, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Serialize to pretty-printed JSON.
     */
    public function json()
    {
        return json_encode($this, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
