<?php

use PHPUnit\Framework\TestCase;
use Src\Controllers\OperationController;
use Src\Models\Operation;
use Src\Controllers\CategoryController;
use Src\Utils\JwtUtils;

class OperationTest extends TestCase
{
    private $operationMock;
    private $jwtUtilsMock;
    private $categoryControllerMock;
    private $controller;

    protected function setUp(): void
    {
        $this->operationMock = $this->createMock(Operation::class);
        $this->jwtUtilsMock = $this->createMock(JwtUtils::class);
        $this->categoryControllerMock = $this->createMock(CategoryController::class);

        $this->controller = new OperationController($this->operationMock, $this->jwtUtilsMock, $this->categoryControllerMock);
    }

    public function testListWithValidData()
    {
        $_COOKIE['auth_token'] = 'valid_token';
        $_GET['description'] = 'Test description';

        $userId = 1;
        $operations = [
            ['id' => 1, 'description' => 'Test description', 'value' => 100.0]
        ];

        $this->jwtUtilsMock->method('decodeToken')->willReturn((object) ['id' => $userId]);
        $this->operationMock->method('list')->with($userId, 'Test description')->willReturn($operations);

        $this->expectOutputString(json_encode($operations, JSON_NUMERIC_CHECK));
        $this->controller->list();
        $this->assertEquals(200, http_response_code());
    }

    public function testCreateWithValidData()
    {
        $_COOKIE['auth_token'] = 'valid_token';
        $this->jwtUtilsMock->method('decodeToken')->willReturn((object) ['id' => 1]);

        $inputData = [
            'value' => 100.5,
            'id_category' => 2,
            'description' => 'Test Operation'
        ];

        $this->categoryControllerMock->method('exists')->with(2)->willReturn(true);
        $this->operationMock->expects($this->once())
            ->method('create')
            ->with('Test Operation', 100.5, 2, 1);

        $this->expectOutputString(json_encode(["message" => "Operação criada com sucesso."]));
        $this->controller->create($inputData);
        $this->assertEquals(201, http_response_code());
    }

    public function testCreateWithInvalidData()
    {
        $this->jwtUtilsMock->method('decodeToken')->willReturn((object) ['id' => 1]);

        $inputData = [
            'value' => 100.5,
            'id_category' => 2,
        ];

        $this->expectOutputString(json_encode(["message" => "Dados incompletos."]));
        $this->controller->create($inputData);
        $this->assertEquals(400, http_response_code());
    }

    public function testCreateWithInvalidValue()
    {
        $inputData = [
            'value' => "valor inválido",
            'id_category' => 2,
            'description' => 'Test Operation'
        ];

        $this->expectOutputString(json_encode(["message" => "O campo 'value' deve ser um número válido."]));
        $this->controller->create($inputData);
        $this->assertEquals(400, http_response_code());
    }

    public function testCreateWithNotFoundCategory()
    {
        $inputData = [
            'value' => 100.5,
            'id_category' => 99,
            'description' => 'Test Operation'
        ];

        $this->categoryControllerMock->method('exists')->with(99)->willReturn(false);

        $this->expectOutputString(json_encode(["message" => "Categoria inválida: o ID fornecido não corresponde a uma categoria existente."]));
        $this->controller->create($inputData);
        $this->assertEquals(400, http_response_code());
    }

    public function testCreateOperationWithInvalidCategory()
    {
        $inputData = [
            'value' => 100.5,
            'id_category' => 1.5,
            'description' => 'Test Operation'
        ];

        $this->expectOutputString(json_encode(["message" => "O campo 'id_category' deve ser um número inteiro válido."]));
        $this->controller->create($inputData);
        $this->assertEquals(400, http_response_code());
    }

    public function testUpdateOperationSuccess()
    {
        $_COOKIE['auth_token'] = 'valid_token';
        $this->jwtUtilsMock->method('decodeToken')->willReturn((object) ['id' => 1]);

        $inputData = [
            'value' => 100.5,
            'id_category' => 2,
            'description' => 'Test Operation'
        ];

        $this->operationMock->method('getById')->with(1)->willReturn(['id_user' => 1]);
        $this->categoryControllerMock->method('exists')->with(2)->willReturn(true);
        $this->operationMock->expects($this->once())
            ->method('update')
            ->with(1, 'Test Operation', 100.5, 2);

        $this->expectOutputString(json_encode(["message" => "Operação atualizada com sucesso."]));
        $this->controller->update($inputData, 1);
        $this->assertEquals(200, http_response_code());
    }

    public function testUpdateOperationWithNotFoundId()
    {
        $operationId = -1;

        $inputData = [
            'value' => 100.5,
            'id_category' => 2,
            'description' => 'Test Operation'
        ];

        $this->operationMock->method('getById')->with($operationId)->willReturn(false);

        $this->expectOutputString(json_encode(["message" => "Operação não encontrada para atualização."]));
        $this->controller->update($inputData, $operationId);
        $this->assertEquals(404, http_response_code());
    }

    public function testUpdateOperationWithNotFoundCategory()
    {
        $_COOKIE['auth_token'] = 'valid_token';
        $this->jwtUtilsMock->method('decodeToken')->willReturn((object) ['id' => 1]);

        $inputData = [
            'value' => 100.5,
            'id_category' => -1,
            'description' => 'Test Operation'
        ];

        $this->operationMock->method('getById')->with(1)->willReturn(['id_user' => 1]);
        $this->categoryControllerMock->method('exists')->with(-1)->willReturn(false);

        $this->expectOutputString(json_encode(["message" => "Categoria inválida: o ID fornecido não corresponde a uma categoria existente."]));
        $this->controller->update($inputData, 1);
        $this->assertEquals(400, http_response_code());
    }

    public function testGetByIdWithInvalidId()
    {
        $id = 'id inválido';

        $this->expectOutputString(json_encode(["message" => "ID inválido."]));
        $this->controller->getById($id);
        $this->assertEquals(400, http_response_code());
    }

    public function testGetByIdWithNotFoundId()
    {
        $_COOKIE['auth_token'] = 'valid_token';
        $this->jwtUtilsMock->method('decodeToken')->willReturn((object) ['id' => 1]);

        $id = -1;

        $this->expectOutputString(json_encode(["message" => "Operação não encontrada."]));
        $this->controller->getById($id);
        $this->assertEquals(404, http_response_code());
    }

    public function testDeleteSuccess()
    {
        $_COOKIE['auth_token'] = 'valid_token';
        $this->jwtUtilsMock->method('decodeToken')->willReturn((object) ['id' => 1]);

        $this->operationMock->method('delete')->with(1)->willReturn(1);

        $this->expectOutputString(json_encode(["message" => "Operação deletada com sucesso."]));
        $this->controller->delete(1);
        $this->assertEquals(200, http_response_code());
    }

    public function testDeleteWithInvalidId()
    {

        $id = 'id inválido';

        $this->expectOutputString(json_encode(["message" => "ID inválido."]));
        $this->controller->delete($id);
        $this->assertEquals(400, http_response_code());
    }

    public function testDeleteWithoutId()
    {

        $id = '';

        $this->expectOutputString(json_encode(["message" => "ID inválido."]));
        $this->controller->delete($id);
        $this->assertEquals(400, http_response_code());
    }

    public function testDeleteWithNotFoundId()
    {

        $id = -1;

        $this->expectOutputString(json_encode(["message" => "Operação não encontrada para exclusão."]));
        $this->controller->delete($id);
        $this->assertEquals(404, http_response_code());
    }
}
