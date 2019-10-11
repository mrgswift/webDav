<?php
/**
 * This file is part of the WebDav package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mrgswift\WebDav;

/**
 * Holds a single response describing the effect of a method on resource and/or its properties
 *
 * This class represents a <tt>response</tt> element and is used within a {@link MultiStatus} response
 * as defined in {@link http://www.ietf.org/rfc/rfc2518.txt RFC-2518}.
 *
 * @todo Add the optional additional href tags to comply with the RFC-2518
 *
 * @author tgicm <cmalfroy@tgi.fr>
 */
class Response
{
    /**
     * Uses a combination of <tt>href</tt> and <tt>status</tt> elements
     */
    const HREFSTATUS = 1;

    /**
     * Uses <tt>propstat</tt> elements
     */
    const PROPSTATUS = 2;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string URI of the associated resource
     */
    protected $href;

    /**
     * @var int The HTTP status code that applies to the entire response
     */
    protected $status;

    /**
     * @var PropertySet[] A list of resource properties, grouped by HTTP status code
     */
    protected $properties = array();

    /**
     * @var string An optional response description
     */
    protected $description;

    /**
     * @param string    $href        URI of the associated resource
     * @param int|array $status
     * @param string    $description An optional response description
     *
     * @throws \InvalidArgumentException When status is not a valid argument
     */
    public function __construct($href, $status = null, $description = null)
    {
        $this->href = $href;

        if (is_int($status)) {
            $this->status = $status;
        } elseif (is_array($status)) {
            foreach ($status as $statusCode => $properties) {
                $this->addProperties($properties, $statusCode);
            }
        } elseif ($status !== null) {
            throw new \InvalidArgumentException('Status is expected to be an integer or an array');
        }

        $this->type = is_int($status) ? self::HREFSTATUS : self::PROPSTATUS;

        if ($description !== null) {
            $this->description = (string)$description;
        }
    }

    /**
     * @return int Returns the response type as an integer
     * @see HREFSTATUS, PROPSTATUS
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string Returns the URI of the associated resource
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     *
     * This method can be used only with {@link HREFSTATUS} responses
     *
     * @return int Returns the HTTP status code that applies to the entire response
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function hasResource()
    {
        return $this->type == self::PROPSTATUS && $this->hasProperties();
    }

    /**
     * @return Resource
     */
    public function getResource()
    {
        $resource = null;

        if ($this->hasResource()) {
            $resource = new Resource($this->href, $this->getProperties());
        }

        return $resource;
    }

    /**
     * @param int $status
     * @return PropertySet
     */
    public function getProperties($status = 200)
    {
        return isset($this->properties[$status]) ? $this->properties[$status] : null;
    }

    /**
     * @param int $status
     * @return bool
     */
    public function hasProperties($status = 200)
    {
        return isset($this->properties[$status]);
    }

    /**
     * @param int $status
     * @return array
     */
    public function getPropertyNames($status = 200)
    {
        return isset($this->properties[$status]) ? $this->properties[$status]->getNames() : array();
    }

    /**
     * @return string Returns information suitable to be displayed to the user explaining
     * the nature of the response. This description may be NULL.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param array $properties
     * @param int   $status
     *
     * @return self Provides a fluent interface
     */
    public function addProperties(array $properties, $status = 200)
    {
        foreach ($properties as $property) {
            $this->addProperty($property, $status);
        }

        return $this;
    }

    /**
     * @param PropertyInterface $property
     * @param int               $status
     *
     * @return self Provides a fluent interface
     * @throws \LogicException when trying to add a property on a {@link HREFSTATUS} response
     */
    public function addProperty(PropertyInterface $property, $status = 200)
    {
        if ($this->type == self::HREFSTATUS) {
            throw new \LogicException(
                'Cannot add a property to a response which uses a combination of href and status elements'
            );
        }

        if (!isset($this->properties[$status])) {
            $this->properties[$status] = new PropertySet();
        }

        $this->properties[$status]->add($property);

        return $this;
    }
}
