<?php
/**
 * This file is part of the WebDav package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tgi\WebDav\Property;

/**
 * Specifies the nature of a resource
 *
 * @author tgicm <cmalfroy@tgi.fr>
 */
class ResourceType extends AbstractProperty
{
    /**
     * The collection resource type
     */
    const COLLECTION = 'collection';

    /**
     * Element name as described in the WebDAV XML elements definition
     */
    const TAGNAME = 'D:resourcetype';

    /**
     * @var array A set of resource types representing this property
     */
    protected $types;

    /**
     * @param string|array $type
     */
    public function __construct($type = array())
    {
        $this->setName(self::TAGNAME);
        $this->types = is_array($type) ? $type : array($type);
    }

    /**
     * @return array Returns a set of resource types representing this property
     */
    public function getValue()
    {
        return $this->types;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function is($type)
    {
        return in_array($type, $this->types);
    }

    /**
     * @return bool Returns true if the resource represents a collection resource
     */
    public function isCollection()
    {
        return $this->is(self::COLLECTION);
    }

    /**
     * @inheritdoc
     */
    public static function fromXml(\DOMElement $element, array $xmlNamespaces = array())
    {
        $value = array();

        foreach ($element->childNodes as $child) {
            if ($child->nodeType != XML_ELEMENT_NODE || $child->namespaceURI != 'DAV:') {
                continue;
            }

            $value[] = $child->localName;
        }

        return new self($value);
    }
}
