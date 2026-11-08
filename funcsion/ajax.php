<?php 
    function setWS()
    {
        $pageName = basename($_SERVER['PHP_SELF']);
        if($pageName == "control.php"){
            return;
        }
        echo "<script>
        const checkstatus = function() {
            fetch('./sse-server.php')
                .then(response => response.text())
                .then(data => {
                    // Process the data received from the server as needed
                    console.log('Server response:', data);

                    // Call checkstatus again for continuous fetching
                    checkstatus();
                })
                .catch(error => {
                    console.error('Error fetching data:', error);

                    // Retry after a delay (adjust as needed)
                    setTimeout(checkstatus, 1000);
                });
        };

        // Start the initial call to checkstatus
        checkstatus();
    </script>";
    }
    setWS();
 ?>