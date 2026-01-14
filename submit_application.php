<!-- submit_application.php -->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $education = $_POST['education'];
    $school = $_POST['school'];
    $experience = $_POST['experience'];
    
    $resume = $_FILES['resume'];
    $resumeName = $resume['name'];
    $resumeTmpName = $resume['tmp_name'];
    $resumeSize = $resume['size'];
    $resumeError = $resume['error'];
    $resumeType = $resume['type'];

    $resumeExt = explode('.', $resumeName);
    $resumeActualExt = strtolower(end($resumeExt));
    $allowed = array('pdf', 'doc', 'docx');

    if (in_array($resumeActualExt, $allowed)) {
        if ($resumeError === 0) {
            if ($resumeSize < 1000000) { // Limit to 1MB
                // Email details
                $to = 'adetolafad@gmail.com';
                $subject = 'New Job Application';
                $message = "
                    <html>
                    <head>
                      <title>Job Application</title>
                    </head>
                    <body>
                      <h2>Application Details</h2>
                      <p><strong>First Name:</strong> $firstName</p>
                      <p><strong>Last Name:</strong> $lastName</p>
                      <p><strong>Date of Birth:</strong> $dateOfBirth</p>
                      <p><strong>Email:</strong> $email</p>
                      <p><strong>Phone:</strong> $phone</p>
                      <p><strong>Address:</strong> $address</p>
                      <p><strong>Education:</strong> $education</p>
                      <p><strong>School/University:</strong> $school</p>
                      <p><strong>Work Experience:</strong> $experience</p>
                    </body>
                    </html>
                ";
                
                // Boundary 
                $boundary = md5(time());

                // Headers
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
                $headers .= "From: $email\r\n";

                // Message Body
                $body = "--$boundary\r\n";
                $body .= "Content-Type: text/html; charset=UTF-8\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $body .= chunk_split(base64_encode($message));

                // Attachment
                $file = file_get_contents($resumeTmpName);
                $body .= "--$boundary\r\n";
                $body .= "Content-Type: $resumeType; name=\"$resumeName\"\r\n";
                $body .= "Content-Disposition: attachment; filename=\"$resumeName\"\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $body .= chunk_split(base64_encode($file));
                $body .= "--$boundary--";

                // Send Email
                if (mail($to, $subject, $body, $headers)) {
                    echo "Application submitted successfully!";
                } else {
                    echo "There was an error sending your application.";
                }
            } else {
                echo "Your file is too big!";
            }
        } else {
            echo "There was an error uploading your file!";
        }
    } else {
        echo "You cannot upload files of this type!";
    }
} else {
    echo "Invalid request method.";
}
?>
