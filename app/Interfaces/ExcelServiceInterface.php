<?php
namespace App\Interfaces;

use Spatie\SimpleExcel\SimpleExcelWriter;

interface ExcelServiceInterface extends ServiceInterface
{
	public function excel(): SimpleExcelWriter;
}