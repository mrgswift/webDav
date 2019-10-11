<?php
/**
 * This file is part of the WebDav package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mrgswift\WebDav\Header;

/**
 * A <tt>Depth</tt> header
 *
 * @author tgicm <cmalfroy@tgi.fr>
 */
class DepthHeader
{
    /**
     * The infinity depth
     */
    const INFINITY = -1;

    /**
     * Parses the given <tt>Depth</tt> header and converts the depth value into an integer.
     *
     * Note that if the header equals to "Infinity", {@link INFINITY} is returned.
     *
     * @param string $str The value of the <tt>Depth</tt> header
     * @return int Return the depth value as an integer
     */
    public static function parse($str)
    {
        return strtolower($str) == 'infinity' ? self::INFINITY : (int)$str;
    }
}
