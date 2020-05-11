<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Integration;

use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\IntegerNumber;
use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\RequestConstraint;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationRules;
use Symfony\Component\Validator\Constraints as Assert;

class ShowcaseTest
{
    public function testArrayValidation(): void
    {
        $rules = [
            'name.first_name' => 'required|min:6',
            'name.last_name'  => 'min:1',
            'email'           => 'required|email',
            'simple'          => 'required|min:5',
            'eye_color'       => 'required|enum:3,4',
            'file'            => 'required|file',
            'password'        => 'required|min:60',
            'tags?.*.slug'    => 'required|filled',
            'tags?.*.label'   => 'required|filled',
        ];

        $constraint = new Assert\Collection([
            // the keys correspond to the keys in the input array
            'name'      => new Assert\Collection([
                'first_name' => new Assert\Length(['min' => 6]),
                'last_name'  => new Assert\Optional(new Assert\Length(['min' => 1])),
            ]),
            'email'     => new Assert\Email(),
            'simple'    => new Assert\Length(['min' => 5]),
            'eye_color' => new Assert\Choice([3, 4]),
            'file'      => new Assert\File(),
            'password'  => new Assert\Length(['min' => 4]),
            'tags'      => new Assert\Optional([
                new Assert\Type('array'),
                new Assert\Count(['min' => 1]),
                new Assert\All([
                    new Assert\Collection([
                        'slug'  => [
                            new Assert\NotBlank(),
                            new Assert\Type(['type' => 'string'])
                        ],
                        'label' => [
                            new Assert\NotBlank(),
                        ],
                    ]),
                ]),
            ]),
        ]);
    }

    public function testTraversableValidation(): void
    {
        $rules = [
            '*.0' => 'required|min:6',
            '*.1' => 'required|integer|min:0',
            '*.2' => 'required|email|nullable',
            '*.3' => 'required|filled|min:20'
        ];

        $constraint = new Assert\All([
            new Assert\Collection([
                '0' => new Assert\Required([new Assert\Length(['min' => 6]), new Assert\NotNull()]),
                '1' => new Assert\Required([new IntegerNumber(), new Assert\GreaterThanOrEqual(0), new Assert\NotNull()]),
                '2' => new Assert\Required([new Assert\Email()]),
                '3' => new Assert\Required([new Assert\NotBlank(), new Assert\NotNull(), new Assert\Length(['min' => 20])]),
            ])
        ]);
    }

    public function testRequestValidation(): void
    {
        $rules           = [
            'query' => ['productId' => 'required|integer|min:0']
        ];
        $validationRules = new RequestValidationRules($rules);

        $constraint = new RequestConstraint([
            'query' => new Assert\Collection([
                'productId' => new Assert\Required([new IntegerNumber(), new Assert\NotNull(), new Assert\GreaterThanOrEqual(0)])
            ])
        ]);
    }
}
