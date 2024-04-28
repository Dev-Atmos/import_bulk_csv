<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

</head>

<body>
    <header>
    </header>
    <section class="row p-5">
        <main class="col-4 justify-content-center">
            <form action="import_temp_bulk.php" method="post" class="form" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="upload_temp" class="form-label">Import Bulk Data</label>
                    <input type="file" name="upload_temp" id="upload_temp" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </main>
    </section>
    <footer>
    </footer>
</body>

</html>

<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    try {
        if (isset($_FILES['upload_temp']) && $_FILES['upload_temp']['error'] == 0) {


            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', -1);

            // Database connection parameters
            $db_host = "127.0.0.1";
            $db_user = "root";
            $db_pass = "";
            $db_name = "icloudems";

            // Function to format CSV data
            function formatData($data)
            {

                array_splice($data, 0, 1);

                return [
                    date("Y-m-d", strtotime(trim($data[0]))),
                    trim($data[1]),
                    trim($data[2]),
                    trim($data[3]),
                    trim($data[4]),
                    trim($data[5]),
                    trim($data[6]),
                    trim($data[7]),
                    trim($data[8]),
                    trim($data[9]),
                    trim($data[10]),
                    trim($data[11]),
                    trim($data[12]),
                    trim($data[13]),
                    trim($data[14]),
                    trim($data[15]),
                    (float) trim($data[16]),
                    (float) trim($data[17]),
                    (float) trim($data[18]),
                    (float) trim($data[19]),
                    (float) trim($data[20]),
                    (float) trim($data[21]),
                    (float) trim($data[22]),
                    (float) trim($data[23]),
                    (float) trim($data[24]),
                    (string) trim($data[25]),
                ];
            }

            // Function to process uploaded CSV file
            function processUploadedFile($filePath)
            {
                global $db_host, $db_user, $db_pass, $db_name;

                // Connect to MySQL database
                $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
                if ($mysqli->connect_error) {
                    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
                }

                // Open the CSV file for reading
                if (($file = fopen($filePath, 'r')) !== false) {
                    $chunkSize = 100;
                    $batchData = [];
                    $recordImported = 0;
                    $totalDueAmt = 0;
                    $totalPaidAmt = 0;
                    $totalConcessionAmt = 0;
                    $totalScholarshipAmount = 0;
                    $totalReverseConcessionAmount = 0;
                    $totalWriteOffAmount = 0;
                    $totalAdjustedAmount = 0;
                    $totalRefundAmount = 0;
                    $totalFundTranCferAmount = 0;

                    while (($data = fgetcsv($file)) !== false) {
                        // Skip the first 6 rows
                        if ($recordImported < 6) {
                            $recordImported++;
                            continue;
                        }

                        // Format the data
                        $formattedData = formatData($data);

                        $totalDueAmt += $formattedData[16];
                        $totalPaidAmt += $formattedData[17];
                        $totalConcessionAmt += $formattedData[18];
                        $totalScholarshipAmount += $formattedData[19];
                        $totalReverseConcessionAmount += $formattedData[20];
                        $totalWriteOffAmount += $formattedData[21];
                        $totalAdjustedAmount += $formattedData[22];
                        $totalRefundAmount += $formattedData[23];
                        $totalFundTranCferAmount += $formattedData[24];

                        $batchData[] = $formattedData;

                        // If batch size reached, insert into database
                        if (count($batchData) >= $chunkSize) {
                            insertBatchData($mysqli, $batchData);
                            $recordImported += count($batchData);
                            $batchData = []; // Clear batch data
                        }
                    }

                    // Insert remaining data
                    if (!empty($batchData)) {
                        insertBatchData($mysqli, $batchData);
                        $recordImported += count($batchData);
                    }

                    // Close the CSV file
                    fclose($file);

                    // Close the database connection
                    $mysqli->close();

                    $result = array(
                        'TotalRecordImported' => $recordImported,
                        "DueAmount" => $totalDueAmt,
                        "PaidAmount" => $totalPaidAmt,
                        "ConcessionAmount" => $totalConcessionAmt,
                        "ScholarshipAmount" => $totalScholarshipAmount,
                        "ReverseConcessionAmount" => $totalReverseConcessionAmount,
                        "WriteOffAmount" => $totalWriteOffAmount,
                        "AdjustedAmount" => $totalAdjustedAmount,
                        "RefundAmount" => $totalRefundAmount,
                        "FundTranCferAmount" => $totalFundTranCferAmount,
                    );

                    echo "<pre>";
                    print_r($result);
                    echo "</pre>";
                } else {
                    echo "Error opening file.";
                }
            }

            // Function to insert batch data into MySQL table
            function insertBatchData($mysqli, $batchData)
            {
                // Get the types of the batch data values

                // Generate the placeholders for the prepared statement

                $placeholders = '(' . rtrim(str_repeat('?,', count($batchData[0])), ',') . ')';

                // Generate the SQL query
                $sql = "INSERT INTO temporary_completedata (
                `date`,
                `academic_year`,
                `session`,
                `alloted_category`,
                `voucher_type`,
                `voucher_no`,
                `roll_no`,
                `admno_uniqueid`,
                `status`,
                `fee_category`,
                `faculty`,
                `program`,
                `department`,
                `batch`,
                `receipt_no`,
                `fee_head`,
                `due_amount`,
                `paid_amount`,
                `concession_amount`,
                `scholarship_amount`,
                `reverse_concession_amount`,
                `write_off_amount`,
                `adjusted_amount`,
                `refund_amount`,
                `fund_trancfer_amount`,
                `remarks`) VALUES $placeholders";

                // Prepare the statement
                $stmt = $mysqli->prepare($sql);
                // $stmt->debugDumpParams();
                if (!$stmt) {
                    die('Error: ' . $mysqli->error);
                }

                // Generate the bind parameter types string
                $bindParams = '';
                foreach ($batchData[0] as $value) {
                    if (is_int($value)) {
                        $bindParams .= 'i'; // Integer type
                    } elseif (is_float($value)) {
                        $bindParams .= 'd'; // Double/Float type
                    } elseif (is_null($value) || empty($value)) {
                        $bindParams .= 's'; // String type for everything else
                    } else {
                        $bindParams .= 's'; // String type for everything else
                    }
                }

                // Bind parameters for each row and execute the statement
                foreach ($batchData as $rowData) {
                    $stmt->bind_param($bindParams, ...$rowData);

                    $stmt->execute();
                }

                // Close the statement
                $stmt->close();
            }

            // Process uploaded file
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                if (isset($_FILES["upload_temp"]) && $_FILES["upload_temp"]["error"] == 0) {
                    $extension = pathinfo($_FILES["upload_temp"]["name"], PATHINFO_EXTENSION);
                    $newFileName = "bulk_" . date("d-m-y_H_i_s") . "." . $extension;
                    $fileUploadPath = "uploads/org_bulks/" . $newFileName;

                    if (move_uploaded_file($_FILES['upload_temp']['tmp_name'], $fileUploadPath)) {
                        processUploadedFile($fileUploadPath);
                    } else {
                        echo "Error uploading file.";
                    }
                } else {
                    echo "File upload problem.";
                }
            }
        } else {
            throw new Exception("Error uploading file", 1);
        }
    } catch (\Exception $e) {
        die("" . $e->getMessage());
    }
}
