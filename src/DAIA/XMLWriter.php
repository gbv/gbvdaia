<?php declare(strict_types=1);

namespace DAIA;

/**
 * Write DAIA data in the DAIA/XML format (DAIA 0.5)
 *
 *     $writer = new XMLWriter();
 *     $writer->write($response)->outputMemory();
 *
 * @package DAIA
 */
class XMLWriter extends \XMLWriter
{
    public function __construct()
    {
        foreach(class_parents($this) as $ancestor) {
            if(method_exists($ancestor, "__construct")) {
                eval($ancestor."::__construct();");
                break;
            }
        }
        $this->openMemory();
        $this->startDocument('1.0', 'UTF-8');
        $this->setIndent(true);
    }

    public function write(Data $data): XMLWriter {
        $name = strtolower(substr(strrchr(get_class($data), '\\'), 1));
        $attr = [];
        $children = [];
        $text = 'label';
        
        if ($name == 'response') {
            $name = 'daia';
            $attr['xmlns'] = "http://ws.gbv.de/daia/";
            $attr['timestamp'] = $data->timestamp;
            $attr['version'] = '0.5';
            $children = array_merge([$data->institution], $data->document ?? []);
        } else if ($name == 'document') {
            $children = $data->item ?? [];
        } else if ($name == 'item') {
            $children = array_merge(
                [$data->department, $data->storage, $data->label],
                $data->available ?? [],
                $data->unavailable ?? []
            );
        } else if ($name == 'available') {
            $children = $data->limitation ?? [];
        } else if ($name == 'unavailable') {
            $children = $data->limitation ?? [];
        } else if ($name == 'errorresponse') {
            $name = 'daia';
            $response = new Response();
            $attr['xmlns'] = "http://ws.gbv.de/daia/";
            $attr['version'] = '0.5';
            $children = ["$data"];
            $text = "message";
        }

        $this->startElement($name);

        foreach (['id','href','part','service','delay','expected','queue'] as $name) {
            if (isset($data->$name)) $attr[$name] = $data->$name;
        }
       
        foreach ($attr as $name => $value) {
            $this->writeAttribute($name, $value);
        }
        
        foreach ($children as $child) {
            if (is_object($child)) {
                $this->write($child);
            } else if (isset($child)) {
                $this->startElement($text);
                $this->text($child);
                $this->endElement();
            }
        }

        if (isset($data->content)) {
            $this->text($data->content);
        }

        $this->endElement();

        return $this;
    }
}
