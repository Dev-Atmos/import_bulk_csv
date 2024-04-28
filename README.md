# README

## Introduction
This repository contains a PHP script and an HTML form for importing bulk data from a CSV file into a MySQL database. The PHP script processes the uploaded CSV file and inserts the data into the specified MySQL table.

## Prerequisites
- PHP installed on your server (PHP 7.0 or higher recommended)
- MySQL server
- Web server (e.g., Apache, Nginx)
- Knowledge of basic PHP and MySQL

## Getting Started
1. Clone this repository to your local machine or download the files directly.
2. Ensure that your web server, PHP, and MySQL are properly configured and running.
3. Create a MySQL database and table to store the imported data. You can use the provided SQL script `database.sql` to create the necessary table structure.
4. Configure the database connection parameters in the PHP script (`index.php`).
5. Upload the HTML and PHP files to your web server.

## Usage
1. Access the HTML form (`index.html`) through a web browser.
2. Choose the CSV file containing the data you want to import.
3. Click the "Upload" button to submit the form.
4. The PHP script (`import_temp_bulk.php`) will process the uploaded file, format the data, and insert it into the MySQL database.
5. The script will display the total number of records imported and various aggregated amounts (e.g., Due Amount, Paid Amount) as a result of the import process.

## Technical Details (PHP)
- The PHP script uses the `$_FILES` superglobal to handle file uploads.
- It checks if the request method is POST and if a file was uploaded successfully.
- Uploaded files are moved to a specified directory (`uploads/org_bulks/`) on the server.
- The script sets custom memory and execution time limits to handle potentially large CSV files.
- Data from the CSV file is parsed using `fgetcsv()` and formatted using the `formatData()` function.
- Batch processing is implemented to insert data in chunks to improve performance.
- Prepared statements are used to prevent SQL injection and enhance security.
- Error handling is implemented using try-catch blocks to handle exceptions gracefully.

## Notes
- Make sure the PHP script has write permissions to the directory where uploaded files are stored.
- Ensure that the directory specified for storing uploaded files exists and is accessible.
- Verify that the MySQL user used in the script has the necessary permissions to insert data into the specified table.

## Dependencies
- Bootstrap v5.0.2 (CSS and JS)
- jQuery v3.7.1

## Author
[Your Name or Organization]

## License
This project is licensed under the [MIT License](LICENSE).
