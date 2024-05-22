<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelController extends Controller
{
    public function upload(Request $request){
        // Validate the uploaded file
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);

        // Upload the Excel file
        $file = $request->file('excel_file');

        // Load the Excel file
        $spreadsheet = Excel::load($file)->getSpreadsheet();

        // Get the first sheet
        $firstSheet = $spreadsheet->getActiveSheet();
        $firstSheetData = $firstSheet->toArray();

        // Remove header row
        $headerRow = array_shift($firstSheetData);

        // Remove and rename headers
        $modifiedHeaderRow = $this->removeAndRenameHeaders($headerRow);

        // Replace the modified header row
        array_unshift($firstSheetData, $modifiedHeaderRow);

        // Calculate the required values for the Dashboard sheet
        $totalLoanAmount = 0;
        $totalLoanee = 0;
        $dueLoanAmount = 0;
        $totalDueLoanee = 0;

        foreach ($firstSheetData as $row) {
            $principal = $row['Principal'] ?? 0;
            $day = $row['Day'] ?? 0;

            // Calculate Total Loan Amount
            $totalLoanAmount += $principal;

            // Calculate Total Loanee
            $totalLoanee++;

            // Calculate Due Loan Amount if Day is greater than 36
            if ($day > 36) {
                $dueLoanAmount += $principal;
                $totalDueLoanee++;
            }
        }

        // Calculate Loan Due %
        $loanDuePercentage = ($totalLoanAmount != 0) ? ($dueLoanAmount * 100) / $totalLoanAmount : 0;

        // Create a new sheet named "Dashboard"
        $dashboardSheet = $spreadsheet->createSheet();
        $dashboardSheet->setTitle('Dashboard');

        // Populate data to the Dashboard sheet
        $dashboardSheet->fromArray([
            ['Particulars', 'Data'],
            ['Total Loan Amount', $totalLoanAmount],
            ['Total Loanee', $totalLoanee],
            ['Due Loan Amount', $dueLoanAmount],
            ['Total Due Loanee', $totalDueLoanee],
            ['Loan Due %', $loanDuePercentage],
        ], null, 'A1', true);

        // Prepare the response
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        });

        // Set response headers for downloading the file with dynamic filename
        $filename = 'Loan_Details_' . now()->format('Y-m-d') . '.xlsx'; // Example: Loan_Details_2023-05-25.xlsx
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        // Send the response
        return $response;
    }

    private function removeAndRenameHeaders($headerRow)
    {
        // Define headers to remove
        $headersToRemove = ["Branch", "Product Name", "Sch Type", "Purpose", "Disburse", "Overdue Principal Amount", "Overdue RI Amount", "Overdue Other Int. Amount", "Address", "Obligor", "Collector"];

        // Define header renaming mapping
        $headerRenameMapping = [
            "Outstanding Principal Amount" => "Principal",
            "Outstanding RI Amount" => "Interest",
            "Outstanding Other Int. Amount" => "Fine",
            "Outstanding Total Amount" => "Total Due",
            "Int. Rate" => "Int"
        ];

        // Remove specified headers
        $modifiedHeaderRow = array_diff($headerRow, $headersToRemove);

        // Rename headers
        foreach ($modifiedHeaderRow as &$header) {
            if (isset($headerRenameMapping[$header])) {
                $header = $headerRenameMapping[$header];
            }
        }

        return $modifiedHeaderRow;
    }
}
