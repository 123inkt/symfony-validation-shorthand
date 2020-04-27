# Symfony Request Validation

A request validation component for Symfony, similar to the Laravel library.

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.1-8892BF)](https://php.net/)
[![Minimum Symfony Version](https://img.shields.io/badge/symfony-%3E%3D%204.4-brightgreen)](https://symfony.com/doc/current/validation.html)
![Run tests](https://github.com/123inkt/symfony-request-validation/workflows/Run%20tests/badge.svg)

## Installation

Include the library as dependency in your own project via: 

    composer require "PrinsFrank/SymfonyRequestValidation"

## How to use

Create a ValidatedRequest class which extends the `AbstractValidatedRequest`. See [Symfony Collection Constraint](https://symfony.com/doc/current/reference/constraints/Collection.html)

    class AddRemarkValidatedRequest extends AbstractValidatedRequest {        
        /**
         * @inheritDoc
         */
        public function getValidationRules(): ValidationRules {
            $rules = new ValidationRules();
            $rules->setQueryRules(new Collection(
                [
                    'fields' => ['message' => new NotBlank()]
                ]
            ));
            return $rules;
        }
    }
        
Register all `ValidatedRequest` classes as service in Symfony's `services.yaml`

    Your\Namespace\ValidatedRequest\:
        'resources': 'src/ValidatedRequest/*'
        
Add the `ValidatedRequest` to your `Controller`      
                    
    class AddRemarkController {
        
        /**
         * @route(...)
         */
        public function __invoke(AddRemarkValidatedRequest $request) {
        
        }        
    }   
    
### Custom violation handling

By default, the `AbstractValidatedRequest` will throw a `RequestValidationException`. This behaviour can be changed
by overwriting the `handleViolations` method.

    class AddRemarkValidatedRequest extends AbstractValidatedRequest {    
        ...
                            
        /**
         * @inheritDoc
         */
        public function handleViolations(ConstraintViolationList $violationList): void {
            // your own violation handling
        }
    }
                     

## About us

At 123inkt (Part of Digital Revolution B.V.), every day more than 30 developers are working on improving our internal ERP and our several shops. Do you want to join us? [We are looking for developers](https://www.123inkt.nl/page/werken_ict.html).
