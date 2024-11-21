<?php

// namespace Src\Tests;

use PHPUnit\Framework\TestCase;
use Src\Controllers\UserController;
use Src\Models\User;

class UserTest extends TestCase
{
    private $mockUser;
    private $controller;

    protected function setUp(): void
    {
        $this->mockUser = $this->createMock(User::class);
        $this->controller = new UserController($this->mockUser);
    }

    public function testListReturnsUsers()
    {
        $expectedUsers = [
            ['id' => 1, 'name' => 'Fernanda Avanço', 'login' => 'fernandaavanco'],
            ['id' => 2, 'name' => 'Felipe Valota', 'login' => 'felipevalota']
        ];
        $this->mockUser->method('list')->willReturn($expectedUsers);

        $this->expectOutputString(json_encode($expectedUsers));
        $this->controller->list();
        $this->assertEquals(200, http_response_code());
    }

    public function testEmptyListReturnsUsers()
    {
        $this->mockUser->method('list')->willReturn([]);

        $this->expectOutputString(json_encode(["message" => "Nenhum usuário encontrado."]));
        $this->controller->list();
        $this->assertEquals(404, http_response_code());
    }

    public function testCreateUserSuccess()
    {
        $input = ['name' => 'Fernanda Avanço', 'login' => 'fernandaavanco', 'password' => 'senha'];

        $this->mockUser->expects($this->once())
            ->method('create')
            ->with($input['name'], $input['login'], $this->stringContains('$2y$'));

        $this->expectOutputString(json_encode(["message" => "Usuário criado com sucesso."]));
        $this->controller->create($input);
        $this->assertEquals(201, http_response_code());
    }

    public function testCreateUserWithIncompleteData()
    {
        $input = ['name' => 'Fernanda Avanço', 'login' => 'fernandaavanco'];

        $this->expectOutputString(json_encode(["message" => "Dados incompletos."]));
        $this->controller->create($input);
        $this->assertEquals(400, http_response_code());
    }

    public function testGetByIdUserFound()
    {
        $id = 1;
        $expectedUser = ['id' => 1, 'name' => 'Fernanda Avanço', 'login' => 'fernandaavanco'];

        $this->mockUser->method('getById')->with($id)->willReturn($expectedUser);

        $this->expectOutputString(json_encode($expectedUser));
        $this->controller->getById($id);
        $this->assertEquals(200, http_response_code());
    }

    public function testGetByIdUserNotFound()
    {
        $id = -1;
        $this->mockUser->method('getById')->with($id)->willReturn(null);

        $this->expectOutputString(json_encode(["message" => "Usuário não encontrado."]));
        $this->controller->getById($id);
        $this->assertEquals(404, http_response_code());
    }

    public function testGetByIdUserInvalid()
    {
        $id = 'id inválido';
        $this->mockUser->method('getById')->with($id)->willReturn(null);

        $this->expectOutputString(json_encode(["message" => "ID inválido."]));
        $this->controller->getById($id);
        $this->assertEquals(400, http_response_code());
    }

    public function testUpdateUserSuccess()
    {
        $input = ["name" => "Fernanda Avanço", "login" => "fernandaavanco"];
        $this->mockUser
            ->expects($this->once())
            ->method('update')
            ->with(1, "Fernanda Avanço", "fernandaavanco")
            ->willReturn(1);

        $this->expectOutputString(json_encode(["message" => "Usuário atualizado com sucesso."]));
        $this->controller->update($input, 1);
    }

    public function testUpdateUserWithIncompleteData()
    {
        $input = ['name' => 'Fernanda Avanço', 'login' => 'fernandaavanco'];

        $this->expectOutputString(json_encode(["message" => "Dados incompletos."]));
        $this->controller->update($input, '');
        $this->assertEquals(400, http_response_code());
    }

    public function testUpdateUserThatDoesNotExist()
    {
        $id = -1;

        $input = ['name' => 'Fernanda Avanço', 'login' => 'fernandaavanco'];

        $this->expectOutputString(json_encode(["message" => "Usuário não encontrado para atualização."]));
        $this->controller->update($input, $id);
        $this->assertEquals(404, http_response_code());
    }

    public function testUpdateUserWithInvalidId()
    {
        $id = 'id inválido';

        $input = ['name' => 'Fernanda Avanço', 'login' => 'fernandaavanco'];

        $this->expectOutputString(json_encode(["message" => "ID inválido."]));
        $this->controller->update($input, $id);
        $this->assertEquals(400, http_response_code());
    }

    public function testDeleteUserSuccess()
    {
        $id = 1;
        $this->mockUser->method('delete')->with($id)->willReturn(1);

        $this->expectOutputString(json_encode(["message" => "Usuário deletado com sucesso."]));
        $this->controller->delete($id);
        $this->assertEquals(200, http_response_code());
    }

    public function testDeleteUserNotFound()
    {
        $id = -1;
        $this->mockUser->method('delete')->with($id)->willReturn(0);

        $this->expectOutputString(json_encode(["message" => "Usuário não encontrado para exclusão."]));
        $this->controller->delete($id);
        $this->assertEquals(404, http_response_code());
    }

    public function testDeleteUserInvalidId()
    {
        $id = 'id inválido';
        $this->mockUser->method('delete')->with($id)->willReturn(0);

        $this->expectOutputString(json_encode(["message" => "ID inválido."]));
        $this->controller->delete($id);
        $this->assertEquals(400, http_response_code());
    }
}
