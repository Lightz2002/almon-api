<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

final class TransactionTypeEnum extends Enum
{
  const Income = 'income';
  const Expense = 'expense';
}
