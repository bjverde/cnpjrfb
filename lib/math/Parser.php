<?php

namespace Math;

/**
 * Evaluate mathematical expression.
 *
 * @author Adrean Boyadzhiev (netforce) <adrean.boyadzhiev@gmail.com>
 */
class Parser
{
    /**
     * Lexer wich should tokenize the mathematical expression.
     *
     * @var Lexer
     */
    protected $lexer;

    /**
     * TranslationStrategy that should translate from infix
     * mathematical expression notation to reverse-polish 
     * mathematical expression notation.
     *
     * @var TranslationStrategy\TranslationStrategyInterface
     */
    protected $translationStrategy;
    
    /**
     * Array of key => value options.
     *
     * @var array 
     */
    private $options = array(
        'translationStrategy' => '\Math\TranslationStrategy\ShuntingYard',
    );

    /**
     * Create new Lexer wich can evaluate mathematical expression.
     * Accept array of configuration options, currently supports only 
     * one option "translationStrategy" => "Fully\Qualified\Classname".
     * Class represent by this options is responsible for translation
     * from infix mathematical expression notation to reverse-polish
     * mathematical expression notation.
     * 
     * <code>
     *  $options = array(
     *      'translationStrategy' => '\Math\TranslationStrategy\ShuntingYard'
     *  );
     * </code>
     * 
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->lexer = new Lexer();
        $this->options = array_merge($this->options, $options);
        $this->translationStrategy = new $this->options['translationStrategy']();
    }

    /**
     * Evaluate string representing mathematical expression.
     * 
     * @param string $expression
     * @return float
     */
    public function evaluate($expression)
    {
        $lexer = $this->getLexer();
        $tokens = $lexer->tokenize($expression);

        $translationStrategy = new \Math\TranslationStrategy\ShuntingYard();

        return $this->evaluateRPN($translationStrategy->translate($tokens));
    }

    /**
     * Evaluate array sequence of tokens in Reverse Polish notation (RPN)
     * representing mathematical expression.
     * 
     * @param array $expressionTokens
     * @return float
     * @throws \InvalidArgumentException
     */
    private function evaluateRPN(array $expressionTokens)
    {
        $stack = new \SplStack();

        foreach ($expressionTokens as $token) {
            $tokenValue = $token->getValue();
            if (is_numeric($tokenValue)) {
                $stack->push((float) $tokenValue);
                continue;
            }

            switch ($tokenValue) {
                case '+':
                    $stack->push($stack->pop() + $stack->pop());
                    break;
                case '-':
                    $n = $stack->pop();
                    $stack->push($stack->pop() - $n);
                    break;
                case '*':
                    $stack->push($stack->pop() * $stack->pop());
                    break;
                case '/':
                    $n = $stack->pop();
                    $stack->push($stack->pop() / $n);
                    break;
                case '%':
                    $n = $stack->pop();
                    $stack->push($stack->pop() % $n);
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Invalid operator detected: %s', $tokenValue));
                    break;
            }
        }

        return $stack->top();
    }

    /**
     * Return lexer.
     * 
     * @return Lexer
     */
    public function getLexer()
    {
        return $this->lexer;
    }
}
