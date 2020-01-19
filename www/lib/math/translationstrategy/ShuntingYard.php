<?php

namespace Math\TranslationStrategy;

use InvalidArgumentException;
use Math\Token;
use SplQueue;
use SplStack;

/**
 * Implementation of Shunting-Yard algorithm.
 * Used to translate infix mathematical expressions
 * to RPN mathematical expressions.
 *
 * @see http://en.wikipedia.org/wiki/Shunting-yard_algorithm
 * @author Adrean Boyadzhiev (netforce) <adrean.boyadzhiev@gmail.com>
 */
class ShuntingYard implements TranslationStrategyInterface
{
    /**
     * Operator stack
     *
     * @var \SplStack 
     */
    private $operatorStack;
    
    /**
     * Output queue
     *
     * @var \SplQueue 
     */
    private $outputQueue;

    /**
     * Translate array sequence of tokens from infix to 
     * Reverse Polish notation (RPN) which representing mathematical expression.
     * 
     * @param array $tokens Collection of Token intances
     * @return array Collection of Token intances
     * @throws InvalidArgumentException
     */
    public function translate(array $tokens)
    {
        $this->operatorStack = new SplStack();
        $this->outputQueue = new SplQueue();
        foreach($tokens as $token) {
            switch ($token->getType()) {
                case Token::T_OPERAND:
                    $this->outputQueue->enqueue($token);
                    break;
                case Token::T_OPERATOR:
                    $o1 = $token;
                    while($this->hasOperatorInStack()
                            && ($o2 = $this->operatorStack->top())
                            && $o1->hasLowerPriority($o2))
                    {
                        $this->outputQueue->enqueue($this->operatorStack->pop());
                    }

                    $this->operatorStack->push($o1);
                    break;
                case Token::T_LEFT_BRACKET:
                    $this->operatorStack->push($token);
                    break;
                case Token::T_RIGHT_BRACKET:
                    while((!$this->operatorStack->isEmpty()) && (Token::T_LEFT_BRACKET != $this->operatorStack->top()->getType())) {
                        $this->outputQueue->enqueue($this->operatorStack->pop());
                    }
                    if($this->operatorStack->isEmpty()) {
                        throw new InvalidArgumentException(sprintf('Mismatched parentheses: %s', implode(' ',$tokens)));
                    }
                    $this->operatorStack->pop();
                    break;
                default:
                    throw new InvalidArgumentException(sprintf('Invalid token detected: %s', $token));
                    break;
            }
        }
        while($this->hasOperatorInStack()) {
            $this->outputQueue->enqueue($this->operatorStack->pop());
        }

        if(!$this->operatorStack->isEmpty()) {
            throw new InvalidArgumentException(sprintf('Mismatched parenthesis or misplaced number: %s', implode(' ',$tokens)));
        }

        return iterator_to_array($this->outputQueue);
    }

    /**
     * Determine if there is operator token in operato stack
     * 
     * @return boolean
     */
    private function hasOperatorInStack()
    {
        $hasOperatorInStack = false;
        if(!$this->operatorStack->isEmpty()) {
            $top = $this->operatorStack->top();
            if(Token::T_OPERATOR == $top->getType()) {
                $hasOperatorInStack = true;
            }
        }

        return $hasOperatorInStack;
    }
}
