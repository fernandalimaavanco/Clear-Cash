<?php

namespace Src\Utils;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

class JwtUtils
{
    private $secretKey;
    private $algorithm;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable("../../");
        $dotenv->load();

        $this->secretKey = $_ENV["JWT_SECRET_KEY"];
        $this->algorithm = 'HS256';
    }
    public function generateToken(array $payload, int $expiration = 3600): string
    {

        print_r($payload);
        $payload['iat'] = time();
        $payload['exp'] = time() + $expiration;

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }
    public function decodeToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secretKey, $this->algorithm));
        } catch (\Exception $e) {
            return null;
        }
    }
    public function validateToken(string $token): bool
    {
        return $this->decodeToken($token) !== null;
    }
}
