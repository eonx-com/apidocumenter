<?php
declare(strict_types=1);

namespace LoyaltyCorp\ApiDocumenter\SchemaBuilders;

use DateTime as BaseDatetime;
use DateTimeInterface;
use EoneoPay\Utils\DateTime;
use EoneoPay\Utils\UtcDateTime;
use LoyaltyCorp\ApiDocumenter\SchemaBuilders\Interfaces\OpenApiTypeResolverInterface;
use Symfony\Component\PropertyInfo\Type;

final class OpenApiTypeResolver implements OpenApiTypeResolverInterface
{
    /**
     * @noinspection MultipleReturnStatementsInspection
     *
     * {@inheritdoc}
     */
    public function resolvePropertyType(Type $type): array
    {
        static $dateTimeClasses = [
            BaseDatetime::class,
            DateTime::class,
            DateTimeInterface::class,
            UtcDateTime::class,
        ];

        if ($type->getBuiltinType() === Type::BUILTIN_TYPE_OBJECT &&
            \in_array($type->getClassName(), $dateTimeClasses, true)
        ) {
            return ['string', 'date-time'];
        }

        if ($type->getBuiltinType() === Type::BUILTIN_TYPE_INT) {
            return ['integer', 'int64'];
        }

        if ($type->getBuiltinType() === Type::BUILTIN_TYPE_FLOAT) {
            return ['number', 'float'];
        }

        if ($type->getBuiltinType() === Type::BUILTIN_TYPE_STRING) {
            return ['string', null];
        }

        if ($type->getBuiltinType() === Type::BUILTIN_TYPE_BOOL) {
            return ['boolean', null];
        }

        return [null, null];
    }
}
