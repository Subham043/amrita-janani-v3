<?php

namespace App\Abstracts;

use App\Interfaces\ExcelServiceInterface;
use Spatie\SimpleExcel\SimpleExcelWriter;

abstract class AbstractExcelService extends AbstractService implements ExcelServiceInterface
{
	abstract public function excel(): SimpleExcelWriter;
}
