<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Visibility_Expression_Or
 *
 * An or ( || ) compare expression.
 */
class TB_Visibility_Expression_Or extends TB_Visibility_Expression
{
    /**
     * First compare expression.
     *
     * @var TB_Visibility_Expression
     */
    protected $left;

    /**
     * Second compare expression.
     *
     * @var TB_Visibility_Expression
     */
    protected $right;

    /**
     * TB_Visibility_Expression_Equals constructor.
     *
     * @param TB_Visibility_Expression $left
     * @param TB_Visibility_Expression $right
     */
    public function __construct(TB_Visibility_Expression $left, TB_Visibility_Expression $right)
    {
        $this->setLeft($left);
        $this->setRight($right);
    }

    /**
     * @inheritDoc
     */
    public function getNotation($context = null)
    {
        return 'or:' . $this->getLeft()->getNotation($context) . $this->getRight()->getNotation($context);
    }

    /**
     * @return TB_Visibility_Expression
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param TB_Visibility_Expression $left
     * @return $this
     */
    public function setLeft($left)
    {
        $this->left = $left;
        return $this;
    }

    /**
     * @return TB_Visibility_Expression
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param TB_Visibility_Expression $right
     * @return $this
     */
    public function setRight($right)
    {
        $this->right = $right;
        return $this;
    }
}