<?php

namespace Tallify;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class Question
{
    /**
     * Ask the user a confirmation question
     *
     * @param string $question
     * @param Application $that
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return boolean
     */
    public function confirm($question, $that, InputInterface $input, OutputInterface $output)
    {
        $helper = $that->getHelperSet()->get('question');
        $question = new ConfirmationQuestion(
            $question,
            false
        );

        return $helper->ask($input, $output, $question);
    }
}
