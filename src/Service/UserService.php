<?php

namespace Service;

use Entity\User;
use Repository\UserRepository;
use Validator\UserValidator;
use Exception;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserValidator $validator
    ) {}

    public function createUser(array $data): User
    {
        $this->validator->validateCreate($data);
        // Проверяем, не существует ли пользователь с таким именем
        if ($this->userRepository->findByName($data['name'])) {
            throw new Exception('User with this name already exists');
        }

        $user = new User();
        $user->setName($data['name']);
        $user->setRole($data['role']);
        $user->setPasswordHash(password_hash($data['password'], PASSWORD_DEFAULT));

        $this->userRepository->save($user);
        return $user;
    }

    public function authenticate(array $data): ?User
    {
        $this->validator->validateLogin($data);
        $user = $this->userRepository->findByName($data['name']);
        if (!$user) {
            return null;
        }

        if (!password_verify($data['password'], $user->getPasswordHash())) {
            return null;
        }

        return $user;
    }
}