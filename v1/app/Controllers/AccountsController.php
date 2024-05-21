<?php

namespace Vanier\Api\Controllers;

use Fig\Http\Message\StatusCodeInterface as HttpCodes;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Helpers\JWTManager;
use Vanier\Api\Models\AccountsModel;

class AccountsController extends BaseController
{
    private $accounts_model = null;

    public function __construct()
    {
        $this->accounts_model = new AccountsModel();
    }
    public function handleCreateAccount(Request $request, Response $response)
    {
        $account_data = $request->getParsedBody();

        // 1) Verify if any information about the new account to be created was included in the 
        // request.
        if (empty($account_data)) {
            return $this->makeResponse($response, ['status' => 'error', 'message' => 'No data was provided in the request.'], 400);
        }

        $email = $account_data['email'];
        $existing_account = $this->accounts_model->isAccountExist($email);

        // 2) Data was provided, we attempt to create an account for the user.   
        if ($existing_account) {
            return $this->makeResponse($response, ['status' => 'error', 'message' => 'An account with this email already exists.'], 400);
        }

        $new_account_id = $this->accounts_model->createAccount($account_data);

        if (!$new_account_id) {
            // Failed to create the new account.
            return $this->makeResponse($response, ['status' => 'error', 'message' => 'Failed to create the new account.'], 500);
        }

        // 3) A new account has been successfully created. 
        // Prepare and return a response. 
        return $this->makeResponse($response, ['status' => 'success', 'message' => 'Account created successfully.'], 200);
    }

    public function handleGenerateToken(Request $request, Response $response, array $args)
    {
        $account_data = $request->getParsedBody();

        //-- 1) Reject the request if the request body is empty.
        if (empty($account_data)) {
            return $this->makeResponse($response, ['status' => 'error', 'message' => 'No data was provided in the request.'], 400);
        }

        $email = $account_data['email'];
        $password = $account_data['password'];

        //-- 2) Retrieve and validate the account credentials.
        $db_account = $this->accounts_model->isPasswordValid($email, $password);

        //-- 3) Is there an account matching the provided email address in the DB?
        if (!$db_account) {
            //-- 4 If the password is invalid --> prepare and return a response with a message indicating the 
            return $this->makeResponse($response, ['status' => 'error', 'message' => 'Invalid email or password.'], 401);
        }

        //-- 5) Valid account detected => Now, we return an HTTP response containing
        // the newly generated JWT.
        //-- 5.a): Prepare the private claims: user_id, email, and role.
        $private_claims = ['user_id' => $db_account['user_id'], 'email' => $db_account['email'], 'role' => $db_account['role']];

        $expires_in = time() + 600; // Expires in 10 min
        $jwt = JWTManager::generateJWT($private_claims, $expires_in);

        // 5.c) Prepare and return a response containing the jwt.
        return $this->makeResponse($response, ['status' => 'success', 'message' => 'Logged in successfully', 'token' => $jwt], 200);
    }
}
