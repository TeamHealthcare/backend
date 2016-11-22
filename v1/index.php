<?php

//including the required files
require_once '../include/DbOperation.php';
require '../libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

/* *
 * NOTE:  This adds a user to the database via POST whose Content-Type:  application/json.
 *        This does *NOT* use directly take advantage of the SLIM framework
 * URL: http://localhost/Services/v1/adduser
 * Parameters: employeenumber, jobtitle, password, employeename
 * Method: POST
 * */
$app->post('/adduser', function () use ($app) {

    // TODO:  Continue integrating, but not have to rely on hard-coded constants for form names
    // verifyRequiredParams(array('employeenumber', 'jobtitle', 'password', 'employeename'));
    $data = json_decode(file_get_contents("php://input"));
    $response = array();

    $employeeNumber = $data->employeenumber;
    $jobTitle = $data->jobtitle;
    $password = $data->password;
    $employeeName = $data->employeename;

    $pdb = new DbOperation();
    $res = $pdb->addUser((int)$employeeNumber, $jobTitle, $password, $employeeName);

    if ($res == 0) {
        $response["error"] = true;
        $response["message"] = "An error occurred while adding a new user";
        echoResponse(400, $response);
    }

    $response["error"] = false;
    $response["message"] = "User successfully added";
    return echoResponse(201, $response);

});

/*
 * URL: http://localhost/Services/v1/carriers
 * Parameters: none
 * Authorization: Put API Key in Request Header
 * Method: GET
 * */
$app->get('/carriers', function() use ($app){

    $response = initializeResponseObject();
    $db = new DbOperation();
    $response['payload'] = $db->getAllCarriers();
    echoResponse(200, $response);
});

/*
 * URL: http://localhost/Services/v1/patients
 * Parameters: none
 * Authorization: Put API Key in Request Header
 * Method: GET
 * */
$app->get('/patients', function() use ($app){

    $response = initializeResponseObject();
    $db = new DbOperation();
    $response['payload'] = $db->getAllPatients();
    echoResponse(200, $response);
});

/* *
 * URL: http://localhost/Services/v1/patient/<patient_id>
 * Parameters: none
 * Authorization: Put API Key in Request Header
 * Method: GET
 * */
$app->get('/patient/:id', function($patient_id) use ($app){

    $response = initializeResponseObject();
    $db = new DbOperation();
    $response['payload'] = $db->getPatientById($patient_id);
    echoResponse(200,$response);
});

/* *
 * NOTE:  This adds a user to the database via POST whose Content-Type:  application/json.
 *        This does *NOT* use directly take advantage of the SLIM framework
 *
 * URL: http://localhost/Services/v1/addepatient
 * Parameters: $_POST[] parameters
 * Method: POST
 * */
$app->post('/addepatient', function () use ($app) {

    // TODO:  Continue integrating, but not have to rely on hard-coded constants for form names
    // verifyRequiredParams(array('employeenumber', 'jobtitle', 'password', 'employeename'));
    $data = json_decode(file_get_contents("php://input"));
    $response = array();

    $patientName = $data->patientname;
    $phoneNumber = $data->phonenumber;
    $address = $data->address;
    $city = $data->city;
    $state= $data->state;
    $zipCode = $data->zipcode;
    $insuranceCarrierId = $data->insurancecarrierid;
    $dateOfBirth = $data->dateofbirth;
    $gender = $data->gender;
    $physician = $data->physician;


    $pdb = new DbOperation();
    $res = $pdb->addElectronicPatient($patientName, $phoneNumber, $address, $city, $state, $zipCode, (int)$insuranceCarrierId, $dateOfBirth, $gender, $physician);

    if ($res == 0) {
        $response["error"] = true;
        $response["message"] = "An error occurred while adding a new user";
        echoResponse(400, $response);
    }

    $response["error"] = false;
    $response["message"] = "Patient successfully added - WORKING";
    return echoResponse(201, $response);
});

/* *
 * URL: http://localhost/Services/v1/updatepatient/<patient_id>
 * Parameters: none
 * Authorization: Put API Key in Request Header
 * Method: PUT
 * */
$app->put('/updatepatient/:id', function($patientid) use ($app){

    $data = json_decode(file_get_contents("php://input"));
    $response = array();

    $patientName = $data->patientname;
    $phoneNumber = $data->phonenumber;
    $address = $data->address;
    $city = $data->city;
    $state= $data->state;
    $zipCode = $data->zipcode;
    $insuranceCarrierId = $data->insurancecarrierid;
    $dateOfBirth = $data->dateofbirth;
    $gender = $data->gender;
    $physician = $data->physician;

    $db = new DbOperation();
    $result = $db->updateElectronicPatient($patientName, $phoneNumber, $address, $city, $state, $zipCode, $insuranceCarrierId, $dateOfBirth, $gender, $physician, $patientid);

    if ($result){
        $response['error'] = false;
        $response['message'] = "Assignment submitted successfully";
    } else {
        $response['error'] = true;
        $response['message'] = "Could not submit assignment";
    }
    echoResponse(200,$response);
});

/*
 * URL: http://localhost/Services/v1/getPatientsForDropdown
 * Parameters: none
 * Authorization: Put API Key in Request Header
 * Method: GET
 * */
$app->get('/getPatientsForDropdown', function() use ($app) {
    $response = initializeResponseObject();
    $db = new DbOperation();
    $response['payload'] = $db->getPatientsForDropdown();
    echoResponse(200, $response);
});

/*
 * URL: http://localhost/Services/v1/getMedicalEncounters
 * Parameters: none
 * Authorization: Put API Key in Request Header
 * Method: GET
 * */
$app->get('/getMedicalEncounters', function() use ($app) {
    $response = initializeResponseObject();
    $db = new DbOperation();
    $response['payload'] = $db->getMedicalEncounters();
    echoResponse(200, $response);
});

/* *
 * URL: http://localhost/Services/v1/medicalencounter/<medicalencounter_id>
 * Parameters: none
 * Authorization: Put API Key in Request Header
 * Method: GET
 * */
$app->get('/medicalencounter/:id', function($medicalencounterid) use ($app){

    $response = initializeResponseObject();
    $db = new DbOperation();
    $response['payload'] = $db->getMedicalEncounterById($medicalencounterid);
    echoResponse(200,$response);
});

/* *
 * NOTE:  This adds a user to the database via POST whose Content-Type:  application/json.
 *        This does *NOT* use directly take advantage of the SLIM framework
 *
 * URL: http://localhost/Services/v1/addepatient
 * Parameters: $_POST[] parameters
 * Method: POST
 * */
$app->post('/addepatient', function () use ($app) {

    // TODO:  Continue integrating, but not have to rely on hard-coded constants for form names
    // verifyRequiredParams(array('employeenumber', 'jobtitle', 'password', 'employeename'));
    $data = json_decode(file_get_contents("php://input"));
    $response = array();

    $patientName = $data->patientname;
    $phoneNumber = $data->phonenumber;
    $address = $data->address;
    $city = $data->city;
    $state= $data->state;
    $zipCode = $data->zipcode;
    $insuranceCarrierId = $data->insurancecarrierid;
    $dateOfBirth = $data->dateofbirth;
    $gender = $data->gender;
    $physician = $data->physician;


    $pdb = new DbOperation();
    $res = $pdb->addElectronicPatient($patientName, $phoneNumber, $address, $city, $state, $zipCode, (int)$insuranceCarrierId, $dateOfBirth, $gender, $physician);

    if ($res == 0) {
        $response["error"] = true;
        $response["message"] = "An error occurred while adding a new user";
        echoResponse(400, $response);
    }

    $response["error"] = false;
    $response["message"] = "Patient successfully added - WORKING";
    return echoResponse(201, $response);
});

/* *
 * NOTE:  This adds a user to the database via POST whose Content-Type:  application/json.
 *        This does *NOT* use directly take advantage of the SLIM framework
 *
 * URL: http://localhost/Services/v1/addencounter
 * Parameters: $_POST[] parameters
 * Method: POST
 * */
$app->post('/addencounter', function () use ($app) {

    // TODO:  Continue integrating, but not have to rely on hard-coded constants for form names
    // verifyRequiredParams(array('employeenumber', 'jobtitle', 'password', 'employeename'));
    $data = json_decode(file_get_contents("php://input"));
    $response = array();

    $encounterDate = $data->encounterdate;
    $complaint = $data->complaint;
    $vitalSigns = $data->vitalsigns;
    $notes = $data->notes;
    $pharmacyOrder = $data->pharmacyorder;
    $diagnosis = $data->diagnosis;
    $treatmentPlan = $data->treatmentplan;
    $referral = $data->referral;
    $followupNotes = $data->followupnotes;
    $patientId = $data->patientid;


    $pdb = new DbOperation();
    $res = $pdb->addMedicalEncounter($encounterDate, $complaint, $vitalSigns, $notes, $pharmacyOrder, $diagnosis, $treatmentPlan, $referral, $followupNotes, $patientId);

    if ($res == 0) {
        $response["error"] = true;
        $response["message"] = "An error occurred while adding a new user";
        echoResponse(400, $response);
    }

    $response["error"] = false;
    $response["message"] = "Encounter successfully added - WORKING";
    return echoResponse(201, $response);
});

/* *
 * URL: http://localhost/Services/v1/updateencounter/<medicalencounterid>
 * Parameters: none
 * Authorization: Put API Key in Request Header
 * Method: PUT
 * */
$app->put('/updateencounter/:id', function($medicalencounterid) use ($app){

    $data = json_decode(file_get_contents("php://input"));
    $response = array();

    $encounterDate = $data->encounterdate;
    $complaint = $data->complaint;
    $vitalSigns = $data->vitalsigns;
    $notes = $data->notes;
    $pharmacyOrder = $data->pharmacyorder;
    $diagnosis = $data->diagnosis;
    $treatmentPlan = $data->treatmentplan;
    $referral = $data->referral;
    $followupNotes = $data->followupnotes;
    $patientId = $data->patientid;

    $db = new DbOperation();
    $result = $db->updateMedicalEncounter($encounterDate, $complaint, $vitalSigns, $notes, $pharmacyOrder, $diagnosis, $treatmentPlan, $referral, $followupNotes, $patientId, $medicalencounterid);

    if ($result){
        $response['error'] = false;
        $response['message'] = "Assignment submitted successfully";
    } else {
        $response['error'] = true;
        $response['message'] = "Could not submit assignment";
    }
    echoResponse(200,$response);
});

/*
 * Private methods....
 * ----------------------------------------------------------------------------------------------------
 */
function initializeResponseObject() {
    return array('error' => false, 'payload' => array());
}

function echoResponse($status_code, $response)
{
    $app = \Slim\Slim::getInstance();
    $app->status($status_code);
    $app->contentType('application/json');
    echo json_encode($response);
}

function verifyRequiredParams($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = $_REQUEST;

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Hey, there are Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(400, $response);
        $app->stop();
    }
}

function authenticateStudent(\Slim\Route $route)
{
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    if (isset($headers['Authorization'])) {
        $db = new DbOperation();
        $api_key = $headers['Authorization'];
        if (!$db->isValidStudent($api_key)) {
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoResponse(401, $response);
            $app->stop();
        }
    } else {
        $response["error"] = true;
        $response["message"] = "1.  Yo, dawgy dawg.  Not good...";
        echoResponse(400, $response);
        $app->stop();
    }
}

function authenticateFaculty(\Slim\Route $route)
{
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
    if (isset($headers['Authorization'])) {
        $db = new DbOperation();
        $api_key = $headers['Authorization'];
        if (!$db->isValidFaculty($api_key)) {
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoResponse(401, $response);
            $app->stop();
        }
    } else {
        $response["error"] = true;
        $response["message"] = "2.  Api key is misssing";
        echoResponse(400, $response);
        $app->stop();
    }
}

$app->run();