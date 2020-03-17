<?php

namespace Rubix\ML\NeuralNet\ActivationFunctions;

use Tensor\Matrix;
use InvalidArgumentException;

/**
 * ISRU
 *
 * The Inverse Square Root Unit is a sigmoid-shaped activation function that is
 * computationally efficient.
 *
 * References:
 * [1] B. Carlile et al. (2017). Improving Deep Learning by Inverse Square Root Linear
 * Units.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class ISRU implements ActivationFunction
{
    /**
     * The absolute value at which the output saturates.
     *
     * @var float
     */
    protected $alpha;

    /**
     * @param float $alpha
     * @throws \InvalidArgumentException
     */
    public function __construct(float $alpha = 1.0)
    {
        if ($alpha < 0.0) {
            throw new InvalidArgumentException('Alpha must be greater'
                . " than 0, $alpha given.");
        }

        $this->alpha = $alpha;
    }

    /**
     * Compute the output value.
     *
     * @param \Tensor\Matrix $z
     * @return \Tensor\Matrix
     */
    public function compute(Matrix $z) : Matrix
    {
        return $z->map([$this, '_compute']);
    }

    /**
     * @param float $z
     * @return float
     */
    public function _compute(float $z) : float
    {
        return $z / sqrt(1.0 + $this->alpha * $z ** 2);
    }

    /**
     * Calculate the derivative of the activation function at a given output.
     *
     * @param \Tensor\Matrix $z
     * @param \Tensor\Matrix $computed
     * @return \Tensor\Matrix
     */
    public function differentiate(Matrix $z, Matrix $computed) : Matrix
    {
        $derivative = [];
        
        foreach ($z->asArray() as $i => $rowZ) {
            $rowComputed = $computed[$i];

            $temp = [];

            foreach ($rowZ as $j => $valueZ) {
                $temp[] = $valueZ !== 0.0 ? ($rowComputed[$j] / $valueZ) ** 3 : 1.0;
            }

            $derivative[] = $temp;
        }

        return Matrix::quick($derivative);
    }
}
