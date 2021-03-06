<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Tokenizer\Token;

/**
 * Fixer for rules defined in PSR2 ¶4.4, ¶4.6.
 *
 * @author Kuanhung Chen <ericj.tw@gmail.com>
 */
class MethodArgumentSpaceFixer extends AbstractFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            // looking for start of brace and skip array
            if (!$token->equals('(') || $tokens[$index - 1]->isGivenKind(T_ARRAY)) {
                continue;
            }

            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

            // fix for method argument and method call
            for ($i = $endIndex - 1; $i > $index; --$i) {
                if (!$tokens[$i]->equals(',')) {
                    continue;
                }

                $this->fixSpace($tokens, $i);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * Method to insert space after comma and remove space before comma
     * @param  Tokens $tokens tokens to handle
     * @param  int    $index  index of token
     * @return none
     */
    public function fixSpace(Tokens $tokens, $index)
    {
        // remove space before comma if exist
        if ($tokens[$index - 1]->isWhitespace()) {
            $tokens[$index - 1]->clear();
        }

        // add space after comma if not exist
        if (!$tokens[$index + 1]->isWhitespace()) {
            $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma.';
    }
}
