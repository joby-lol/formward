<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward;

use Flatrr\FlatArrayInterface;

interface ContainerInterface extends FlatArrayInterface
{
    public function wrapContainerItems(bool $set = null) : bool;
}
