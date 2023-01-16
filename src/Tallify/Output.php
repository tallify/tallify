<?php

namespace Tallify;

use function Termwind\render;

class Output
{
    public function oneLiner($message)
    {
        return render("<div>$message</div>");
    }
    /**
     * Output single italicised terminal message.
     * @param string $message
     * @param string $level
     * @param string $classes
     *
     * @return render
     */
    public function italicSingle($message, $level, $classes = null)
    {
        $level = $this->getLevelMessage($level);

        return render("
            <div class='flex mb-1'>
                $level
                <em class='ml-1 $classes'>$message</em>
            </div>
        ");
    }

    /**
     * Output multiple italicised terminal message with no level.
     * @param string $message
     *
     * @return render
     */
    public function singleNoLevel($messages)
    {
        return render("
            <div class='mb-1'>
                $messages
            </div>
        ");
    }

    /**
     * Get the Output level.
     * @param string $level
     *
     * @return string
     */
    public function getLevelMessage($level)
    {
        switch ($level) {
            case 'error':
                return "<div class='px-1 font-bold text-white bg-red-600'>ERROR!</div>";
                break;
            case 'success':
                return "<div class='px-1 font-bold text-white bg-green-600'>SUCCESS!</div>";
                break;
            case 'required':
                return "<div class='px-1 font-bold text-white bg-purple-400'>ACTION REQUIRED!</div>";
                break;
            case 'warning':
                return "<div class='px-1 font-bold text-white bg-orange-600'>WARNING!</div>";
                break;
            case 'info':
                return "<div class='px-1 font-bold bg-blue-600'>INFO!</div>";
                break;

            default:
                return "<div class='px-1 font-bold text-white bg-green-600'>SUCCESS!</div>";
                break;
        }
    }
}
