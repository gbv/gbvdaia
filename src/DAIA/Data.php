<?php declare(strict_types=1);

namespace DAIA;

/**
 * Abstract base class of objects in the DAIA data model.
 * 
 * Magic methods __set and __get are used for type checking.
 *
 * See <https://purl.org/NET/DAIA#data-format>
 * @package DAIA
 */
class Data implements \JsonSerializable
{
    /**
     * Create a new DAIA object.
     *
     * Example: `$doc = new DAIA\Document(json_decode($json));`
     *
     * Unknown fields are silently ignored.
     */
    public function __construct(array $data=[])
    {
        foreach ($data as $field => $value) {
            if (property_exists($this, (string)$field)) {
                $this->__set($field, $value);
            }
        }
        foreach ($this as $field => $value) {
            if ($value === NULL and $this->fieldRequired($field)) {
                throw new \LogicException(get_class($this)."->$field is required");
            }
        }
    }

    /**
     * Get a field.
     *
     * @throws LogicException if the field does not exist.
     */
    public function __get($field) {
        if (property_exists($this, $field)) {
            return $this->$field;
        } else {
            throw new \LogicException(get_class($this)."->$field does not exist");
        }
    }

    /**
     * Set a field.
     *
     * @throws LogicException if the field does not exist
     * @throws InvalidArgumentException if the value is of wrong type
     */
    public function __set($field, $value) {
        if (!property_exists($this, $field)) {
            throw new \LogicException(get_class($this)."->$field does not exist");
        }

        if ($value === NULL) {
            if ($this->fieldRequired($field)) {
                throw new \InvalidArgumentException(get_class($this)."->$field is required");
            } else {
               $this->$field = NULL;
               return;
            }
        }

        $method = 'set'.ucfirst($field);
        if (method_exists($this, $method)) {
            $this->$method($value);
            return;
        }

        $class = 'DAIA\\'.ucfirst($field);
        if (class_exists($class)) {
            $construct = static::build($class);
            if ($this->fieldRepeatable($field)) {
                if (is_array($value)) {
                    $value = array_map($construct, $value);
                } else {
                    throw new \InvalidArgumentException(get_class($this)."->$field must be array");
                }
            } else {
                $value = $construct($value);
            }
        } else {
            if (is_numeric($value)) {
                $value = (string)$value;
            } elseif (!is_string($value)) {
                throw new \InvalidArgumentException(get_class($this)."->$field must be string");
            }

            if ($field == 'id' && !Data::isURI($value)) {
                throw new \InvalidArgumentException(get_class($this)."->id must be URI");
            }

            if ($field == 'href' && !Data::isURL($value)) {
                throw new \InvalidArgumentException(get_class($this)."->href must be URL");
            }
        }

        $this->$field = $value;
    }

    private static function build($class) {
         return function ($object) use ($class) {
            if (!is_a($object, $class, True)) {
                $object = new $class($object);
            }
            return $object;
        };
    }

    public function __call($name, $arguments) {
        if (substr($name,0,3)=='add') {
            $field = strtolower(substr($name,3));
            if (property_exists($this, $field)) {                
                if (!$this->fieldRepeatable($field)) {
                    throw new \LogicException(get_class($this)."->$field is not repeatable");
                }
                $build = static::build('DAIA\\'.ucfirst($field));
                $this->$field[] = $build($arguments[0]);
                return;
            }
        }
        throw new \LogicException("Call to undefined method ".get_class($this)."->$name");
    }

    protected function fieldRequired($field): bool {
        return FALSE;
    }

    protected function fieldRepeatable($field): bool {
        return FALSE;
    }


    public static function isDatetime($value) {
        $format = \DateTime::W3C;
        $date = \DateTime::createFromFormat($value, $format);
        return $date && $date->format($format) == $value;
    }

    public static function isURL($value) {
        return 1; # TODO
    }

    public static function isURI($value) {
        return 1; # TODO
    }

    /**
     * Return the data to be serialized to JSON.
     */
    public function jsonSerialize()
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
