<?php

namespace Novuso\Common\Bundle\Collection;

use Doctrine\Common\Collections\ArrayCollection as BaseCollection;
use Novuso\Common\Domain\Model\Api\Collection;

/**
 * ArrayCollection is a doctrine array collection adapter
 *
 * @copyright Copyright (c) 2015, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
class ArrayCollection extends BaseCollection implements Collection
{
}
