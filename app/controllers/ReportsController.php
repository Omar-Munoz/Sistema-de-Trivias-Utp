<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\ReportService;

final class ReportsController extends Controller
{
  public function excel(): void
  {
    $this->requireStaff();
    (new ReportService())->exportExcelProgress();
  }
}
