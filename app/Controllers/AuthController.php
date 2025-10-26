<?php namespace App\Controllers;

use App\Models\StaffModel;
use CodeIgniter\Controller;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    protected $staff;

    public function __construct()
    {
        $this->staff = new StaffModel();
    }

    public function showLogin()
    {
        $session = session();
        if ($session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->staff->where('username', $username)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Invalid username or password');
        }
        if (password_verify($password, $user['password'])) {
            $session = session();
            $session->set([
                'isLoggedIn' => true,
                'staff_id' => $user['id'],
                'staff_name' => $user['name'],
            ]);
            $key = getenv('JWT_SECRET') ?: 'secret-key';
            $payload = [
                'iss' => base_url(),
                'sub' => $user['id'],
                'name' => $user['name'],
                'iat' => time(),
                'exp' => time() + 3600 
            ];
            $jwt = JWT::encode($payload, $key, 'HS256');
            $session->set('jwt_token', $jwt);

            return redirect()->to('/dashboard');
        }

        return redirect()->back()->with('error', 'Invalid username or password');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
