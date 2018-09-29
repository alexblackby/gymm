<?php

namespace AppBundle\Util;

use Symfony\Component\Form\FormInterface;

/**
 * Формирует массив с перечислением ошибок валидации для отображения в REST.
 *
 * Known issues: отображает ошибки только самой формы и полей первого уровня вложенности
 */
class RestFormErrors
{
    private $errors;

    public function __construct(FormInterface $form)
    {
        $this->parseFormErrors($form);
    }

    private function parseFormErrors(FormInterface $form, int $recursionLevel = 1)
    {
        // Пути к полям, в которых произошли ошибки, формируются следующим образом
        // 1) ошибки самой формы верхнего уровня (такие как не валидный CSRF-токен) - отображаются по пути "request"
        // 2) ощибки полей верхней формы - путь равен имени поля, например: "title"
        if ($recursionLevel == 1) {
            $path = 'request';
        }
        if ($recursionLevel == 2) {
            $path = $form->getName();
        }
        if ($recursionLevel > 2) {
            // при необходимости - тут можно реализовать обработку ошибок полей форм с большей вложенностью.
            return;
        }

        foreach ($form->getErrors() as $error) {
            $this->errors[] = [
                "propertyPath" => $path,
                "message" => $error->getMessage()
            ];
        }

        foreach ($form->all() as $child) {
            if ($child instanceof FormInterface) {
                $this->parseFormErrors($child, $recursionLevel + 1);
            }
        }
    }

    public function toArray($message = "Validation error")
    {
        return [
            "message" => $message,
            "errors" => $this->errors
        ];
    }
}