<?php 

// Define the path to the .env file
$envFilePath = '../.env';

// Check if the .env file exists
if (file_exists($envFilePath)) {
    // Read the contents of the .env file
    $envFileContents = file_get_contents($envFilePath);
    //var_dump($envFileContents);
    // Split the file contents into lines
    $envLines = explode("\n", $envFileContents);
   // var_dump($envLines);
    // Iterate through each line
    foreach ($envLines as $line) {
        // Ignore lines that start with '#' (comments) or are empty
        if (empty($line) || strpos($line, '#') === 0 || strlen(trim($line)) < 3) {
            continue;
        }
		//echo "line = " . $line . "<br/>";
        // Split the line into key and value based on the '=' character
        list($key, $value) = explode('=', $line, 2);

        // Trim whitespace and remove surrounding quotes from values
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        // Set the environment variable (use putenv for the current request)
       // putenv("$key=$value");

        // Optionally, you can also add variables to the $_ENV superglobal
        $_ENV[$key] = $value;
		
    }
}else{
    \CustomErrorHandler::triggerInternalError("No Environment File Please check in path ");
}