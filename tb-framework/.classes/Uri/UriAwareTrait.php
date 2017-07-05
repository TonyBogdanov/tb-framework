<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Uri;

/**
 * Adds uri awareness.
 *
 * Trait UriAwareTrait
 * @package TB\Uri
 */
trait UriAwareTrait
{
    /**
     * @return Uri
     */
    public function getUri()
    {
        return $this->get('tb.uri.uri');
    }
}