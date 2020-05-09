<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Validator\Constraint;

use ArrayAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Traversable;

class RecursiveCollectionValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof RecursiveCollection) {
            throw new UnexpectedTypeException($constraint, RecursiveCollection::class);
        }

        if (null === $value) {
            return;
        }

        if (is_array($value) === false && ($value instanceof Traversable === false || $value instanceof ArrayAccess === false)) {
            throw new UnexpectedValueException($value, 'array|(Traversable&ArrayAccess)');
        }

        $context = $this->context;

        /** @var Optional|Required $collectionConstraint */
        foreach ($constraint->fields as $path => $collectionConstraint) {
            $data = $this->getData($path, $value);
            if ($data === null) {
                if ($collectionConstraint instanceof Required) {
                    $context->buildViolation('fails')
                        ->atPath($path)
                        ->setParameter('{{ field }}', $this->formatValue($value))
                        ->setInvalidValue(null)
                        ->setCode(Collection::MISSING_FIELD_ERROR)
                        ->addViolation();
                }
                continue;
            }

            foreach ($data as $val) {
                $context->getValidator()
                    ->inContext($context)
                    ->atPath($path)
                    ->validate($val, $collectionConstraint->constraints);
            }
        }
    }

    private function getData(string $path, array $data): ?array
    {
        $cursor = $data;
        foreach (explode('.', $path) as $key) {
            if (is_array($cursor) === false || array_key_exists($key, $cursor) === false) {
                return null;
            }

            $cursor = $cursor[$key];
        }

        return [$cursor];
    }
}
