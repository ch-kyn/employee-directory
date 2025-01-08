<?php
require_once('../includes/db.php');
require_once('../includes/session_check.php');

// output two versions of the 'Employee' table, and hide/show depending on which version you want to see by clicking on the list/grid button
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet" />
</head>
<body>
    <nav>
        <span class="logo">Employee Directory</span>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php?logout=true">Logout</a></li>
        </ul>
    </nav>
    
    <main>
            <h1>Employee List</h1>
            <div class="cont-row">
                <a href="create.php" class="add-button">Add Employee</a>
                <div class="cont-row">
                    <button class="list-btn btn"><img src="../svg/list.svg" alt="list icon"></button>
                    <button class="card-btn btn"><img src="../svg/grid.svg" alt="grid icon"></button>
                </div>    
            </div>
    
            <!-- list view version of the table, which loops through the $employees array to output the information as a row -->
            <div class="list-view">
                <div class="center">
                    <table>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Job Title</th>
                            <th>Department</th>
                            <th>Photo</th>
                            <th>Actions</th>
                        </tr>    

                        <?php
                        // prepared statement selecting the whole 'employees' table
                        $stmt = $conn->prepare("SELECT * FROM employees");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        $employees = []; // empty array to save the results to avoid having to execute the statement twice

                        while ($row = $result->fetch_assoc()) {
                            $employees[] = $row; // push each row to the $employees array
                        }

                        // call 'fetch_assoc()', return a row, and display it with its corresponding data in the database to the interface
                        foreach ($employees as $row) {
                            echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['age']}</td>
                                <td>{$row['job_title']}</td>
                                <td>{$row['department']}</td>
                                <td>";
                            // image is optional, so this checks if the 'photo_path' column is set first to avoid broken img tags
                            if (!empty($row['photo_path'])) {
                                echo "<img src='{$row['photo_path']}'>";
                            }
                            echo "</td>
                                <td><a href='edit.php?id={$row['id']}'>Edit</a> | <a href='#' onclick='confirmDelete(" . $row['id'] . ")'>Delete</a></td>
                                </tr>";
                        };
                        ?>
                    </table>
                </div>
            </div>

            <!-- card view version of the table, which loops through the $employees array to output the information as a card -->
            <div class="card-view hide">
                <div class="cont center">
                    <?php
                    foreach ($employees as $row) {
                        echo "<div class='card'>";

                        if (!empty($row['photo_path'])) {
                            echo "<img src='{$row['photo_path']}'>";
                        }

                        echo "<div class='card-info'><h2>{$row['name']}</h2>
                            <ul>
                                <li>Age: {$row['age']}</li>
                                <li>Job Title: {$row['job_title']}</li>
                                <li>Department: {$row['department']}</li>
                            </ul></div>
                            <span class='actions'><a href='edit.php?id={$row['id']}'>Edit</a> |
                            <a href='#' onclick='confirmDelete(" . $row['id'] . ")'>Delete</a></span>
                        </div>";
                    }

                    $stmt->close();
                    $conn->close();
                    ?>
                </div>
            </div>
    </main>

    <script>
        const listBtn = document.querySelector('.list-btn');
        const list = document.querySelector('.list-view');
        const cardBtn = document.querySelector('.card-btn');
        const card = document.querySelector('.card-view');

        // hide / show to make the view you click to show up, and hide the other alternative
        listBtn.addEventListener('click', () => {
            list.classList.add('show');
            card.classList.add('hide');
            list.classList.remove('hide');
            card.classList.remove('show');
        });

        cardBtn.addEventListener('click', () => {
            card.classList.add('show');
            list.classList.add('hide');
            card.classList.remove('hide');
            list.classList.remove('show');
        });

        // JS function that display a confirmation dialog before deleting an employee, and sends the user to 'delete.php' if clicked 'yes'
        function confirmDelete(employeeId) {
            if (confirm("Are you sure you want to delete this employee?")) {
                window.location.href = 'delete.php?id=' + employeeId;
            }
        }
    </script>
</body>
</html>
