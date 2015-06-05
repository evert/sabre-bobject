<?php

namespace Sabre\BObject;

use Sabre\VObject\Document;
use Sabre\VObject\Component;
use Sabre\VObject\Property;

class Serializer {

    protected $stream;

    function serialize(Document $component) {

        $this->stream = fopen('php://temp','r+');

        $this->serializeDocument(
            $component
        );

        rewind($this->stream);
        return stream_get_contents($this->stream);

    }

    function serializeDocument(Document $component) {

        $documentType = $component->getDocumentType();
        $formatVersion = Constants::FORMAT_VERSION;
        $this->writeByte(
            $formatVersion << 4 | $documentType
        );
        $this->serializeComponent($component);

    }

    function serializeComponent(Component $component) {

        $this->writeString(strtoupper($component->name));
        $properties = array();
        $components = array();


        foreach($component->children as $child) {
            if ($child instanceof Component) {
                $components[] = $child;
            } else {
                $properties[] = $child;
            }
        }

        $this->writeJson(count($properties));
        foreach($properties as $child) {
            $this->serializeProperty($child);
        }
        $this->writeJson(count($components));
        foreach($components as $child) {
            $this->serializeComponent($child);
        }

    }
    function serializeProperty(Property $property) {

        $parameters = array();

        foreach($property->parameters as $parameter) {
            if ($parameter->name === 'VALUE') {
                continue;
            }
            $parameters[strtoupper($parameter->name)] = $parameter->jsonSerialize();
        }
        // In jCard, we need to encode the property-group as a separate 'group'
        // parameter.
        if ($property->group) {
            $parameters['GROUP'] = $this->group;
        }

        $this->writeString(strtoupper($property->name));
        $this->writeObject((object)$parameters);
        $this->writeString($property->getValueType());
        $this->writeJson($property->getJsonValue());

    }

    function writeJson($val) {

        switch(gettype($val)) {

            case 'array' :
                $this->writeByte(Constants::TYPE_ARRAY);
                $this->writeArray($val);
                break;
            case 'string' :
                $this->writeByte(Constants::TYPE_STRING);
                $this->writeString($val);
                break;
            case 'object' :
                $this->writeByte(Constants::TYPE_OBJECT);
                $this->writeObject($val);
                break;
            case 'integer' :
                if ($val < 16) {
                    $this->writeByte(Constants::TYPE_HALFBYTE | $val << 4);
                } elseif ($val <= 0xff) {
                    $this->writeByte(Constants::TYPE_BYTE);
                    $this->writeByte($val);
                } elseif ($val <= 0xffff) {
                    $this->writeByte(Constants::TYPE_WORD);
                    fwrite($this->stream, pack('s', $val));
                } else {
                    $this->writeByte(Constants::TYPE_INTEGER);
                    $this->writeInt($val);
                }
                break;
            default :
                throw new \Exception('Alert! type ' . gettype($val) . ' not supported!');

        }

    }

    function writeByte($val) {

        fwrite($this->stream, chr($val)); 

    }

    function writeInt($val) {

        $highMap = 0xffffffff00000000; 
        $lowMap = 0x00000000ffffffff; 
        $higher = ($val & $highMap) >>32; 
        $lower = $val & $lowMap; 
        $packed = pack('NN', $higher, $lower); 
        fwrite($this->stream, $packed);

        //list($higher, $lower) = array_values(unpack('N2', $packed)); 
        //$originalValue = $higher << 32 | $lower; 

    }

    function writeArray(array $array) {
    
        $this->writeJson(count($array));
        foreach($array as $item) {
            $this->writeJson($item);
        }

    }

    function writeString($string) {

        if (($index = array_search($string, Constants::$dictionary)) !== false) {
            $stored = 0x01;
            $this->writeByte(
                ($index << 1) | $stored
            ); 
        } else {
            $stored = 0x00;
            $length = strlen($string);
            $this->writeByte(
                ($length << 1) | $stored
            );
            
            fwrite($this->stream, $string);
            Constants::$dictionary[] = $string; 

        }

    }

    function writeObject($obj) {

        $vars = get_object_vars($obj);
        $this->writeJson(count($vars));
        foreach($vars as $key=>$value) {

            $this->writeString($key);
            $this->writeJson($value);

        } 

    }

}
