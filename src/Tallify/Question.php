<?php

namespace Tallify;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class Question
{
    public function confirm($that, $question, InputInterface $input, OutputInterface $output)
    {
        $helper = $that->getHelperSet()->get('question');
        $question = new ConfirmationQuestion(
            $question,
            false
        );

        return $helper->ask($input, $output, $question);
    }
}
