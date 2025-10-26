<?php namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Services;

class JWTAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getServer('HTTP_AUTHORIZATION');
        if(!$authHeader){
            return Services::response()->setStatusCode(401)->setJSON(['error'=>'No token provided']);
        }
        [$type, $token] = explode(' ', $authHeader);
        if($type !== 'Bearer' || !$token){
            return Services::response()->setStatusCode(401)->setJSON(['error'=>'Invalid token']);
        }
        try {
            $key = getenv('JWT_SECRET') ?: 'secret-key';
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            // you can set identity to request or session
            // $request->identity = $decoded;
        } catch (\Exception $e) {
            return Services::response()->setStatusCode(401)->setJSON(['error'=>'Token invalid: '.$e->getMessage()]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
