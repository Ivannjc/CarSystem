<?php
include '../entity/Car.php';

class AgentCarController
{
    private $car;

    public function __construct()
    {
        $this->car = new Car();
    }

    public function getCarListings($created_by, $filters = [], $role_id)
    {
        // Pass $role_id to Car model to determine whether to apply created_by filter
        return $this->car->getAllCarsAgent($created_by, $filters, $role_id);
    }


    public function addCar($make, $model, $year, $color, $price, $description, $created_by)
    {
        return $this->car->addCar($make, $model, $year, $color, $price, $description, $created_by);
    }

    public function updateCarListings($prices, $descriptions)
    {
        foreach ($prices as $carId => $price) {
            $description = isset($descriptions[$carId]) ? $descriptions[$carId] : '';
            $this->car->updateCar($carId, $price, $description);
        }
    }

    public function deleteCar($car_id)
    {
        return $this->car->deleteCar($car_id);
    }

    public function incrementViewCount($car_id)
    {
        return $this->car->incrementViewCount($car_id);
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $carController = new AgentCarController();
    $created_by = $_SESSION['email']; // Assuming 'email' is stored in session

    if (isset($_POST['add'])) {
        $make = $_POST['make'];
        $model = $_POST['model'];
        $year = $_POST['year'];
        $color = $_POST['color'];
        $price = $_POST['price'];
        $description = $_POST['description'];

        // Add car using the controller and session email as created_by
        if ($carController->addCar($make, $model, $year, $color, $price, $description, $created_by)) {
            header("Location: ../boundary/agentCarListings.php?message=Car added successfully");
        } else {
            header("Location: ../boundary/agentAddCarListings.php?message=Failed to add car");
        }
        exit;
    }

    if (isset($_POST['update'])) {
        $prices = $_POST['price'];
        $descriptions = $_POST['description'];
        $carController->updateCarListings($prices, $descriptions);
        header("Location: ../boundary/agentCarListings.php?message=Car listings updated successfully");
        exit;
    }

    // Check if a delete request has been made
    if (isset($_POST['delete']) && isset($_POST['delete'])) {
        $carId = $_POST['delete'];  // 'delete' now contains the car_id
        $carController->deleteCar($carId); // Calls the deleteCar method
        header("Location: ../boundary/agentCarListings.php?message=Car listing deleted successfully");
        exit;
    }

    // Check if a view_description request has been made
    if (isset($_POST['view_description'])) {
        $car_id = $_POST['car_id']; // Get the car_id from the request

        // Create an instance of the Car class
        $carModel = new Car();

        // Increment view count for the car
        $carModel->incrementViewCount($car_id);

        // Get the car's description
        $description = $carModel->getCarDescription($car_id);

        // Return the description in JSON format
        echo json_encode(['description' => $description]);
        exit;
    }
}
