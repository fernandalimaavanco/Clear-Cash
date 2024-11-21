<?php

// namespace Src\Tests;

use PHPUnit\Framework\TestCase;
use Src\Controllers\CategoryController;
use Src\Models\Category;

class CategoryTest extends TestCase
{
    private $mockDb;
    private $mockCategory;
    private $controller;

    protected function setUp(): void
    {

        $this->mockDb = $this->createMock(stdClass::class);

        $this->mockCategory = $this->createMock(Category::class);

        $this->controller = new CategoryController($this->mockCategory);

        $reflection = new ReflectionProperty(CategoryController::class, 'category');
        $reflection->setAccessible(true);
        $reflection->setValue($this->controller, $this->mockCategory);
    }

    public function testListCategoriesSuccess()
    {
        $expectedCategories = [
            ["id" => 1, "description" => "Category 1", "entrance" => true],
            ["id" => 2, "description" => "Category 2", "entrance" => false],
        ];

        $this->mockCategory
            ->expects($this->once())
            ->method('list')
            ->with(null)
            ->willReturn($expectedCategories);

        $this->expectOutputString(json_encode($expectedCategories));
        $this->controller->list();
    }

    public function testEmptyListCategoriesSuccess()
    {

        $this->mockCategory
            ->expects($this->once())
            ->method('list')
            ->with(null)
            ->willReturn([]);

        $this->expectOutputString(json_encode(["message" => "Nenhuma categoria encontrada."]));
        $this->controller->list();
    }

    public function testCreateCategorySuccess()
    {

        $data = ["description" => "New Category", "entrance" => true];
        $this->mockCategory
            ->expects($this->once())
            ->method('create')
            ->with("New Category", true)
            ->willReturn(true);

        $this->expectOutputString(json_encode(["message" => "Categoria criada com sucesso."]));
        $this->controller->create($data);
    }

    public function testGetByIdCategoryFound()
    {
        $category = ["id" => 1, "description" => "Category 1", "entrance" => true];

        $this->mockCategory
            ->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($category);

        $this->expectOutputString(json_encode($category));
        $this->controller->getById(1);
    }

    public function testGetByIdCategoryNotFound()
    {
        $id = -1;
        $this->mockCategory->method('getById')->with($id)->willReturn(null);

        $this->expectOutputString(json_encode(["message" => "Categoria não encontrada."]));
        $this->controller->getById($id);
        $this->assertEquals(404, http_response_code());
    }

    public function testGetByIdCategoryInvalid()
    {
        $id = 'id inválido';
        $this->mockCategory->method('getById')->with($id)->willReturn(null);

        $this->expectOutputString(json_encode(["message" => "ID inválido."]));
        $this->controller->getById($id);
        $this->assertEquals(400, http_response_code());
    }

    public function testUpdateCategorySuccess()
    {
        $data = ["description" => "Updated Category", "entrance" => true];
        $this->mockCategory
            ->expects($this->once())
            ->method('update')
            ->with(1, "Updated Category", true)
            ->willReturn(1);

        $this->expectOutputString(json_encode(["message" => "Categoria atualizada com sucesso."]));
        $this->controller->update($data, 1);
    }

    public function testUpdateCategoryWithIncompleteData()
    {
        $data = ["description" => "Updated Category", "entrance" => true];
        $this->expectOutputString(json_encode(["message" => "Dados incompletos."]));
        $this->controller->update($data, '');
        $this->assertEquals(400, http_response_code());
    }

    public function testUpdateCategoryThatDoesNotExist()
    {
        $id = -1;

        $data = ["description" => "Updated Category", "entrance" => true];

        $this->expectOutputString(json_encode(["message" => "Categoria não encontrada para atualização."]));
        $this->controller->update($data, $id);
        $this->assertEquals(404, http_response_code());
    }

    public function testUpdateCategoryWithInvalidId()
    {
        $id = 'id inválido';

        $data = ["description" => "Updated Category", "entrance" => true];

        $this->expectOutputString(json_encode(["message" => "ID inválido."]));
        $this->controller->update($data, $id);
        $this->assertEquals(400, http_response_code());
    }

    public function testDeleteCategorySuccess()
    {
        $this->mockCategory
            ->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(1);

        $this->expectOutputString(json_encode(["message" => "Categoria deletada com sucesso."]));
        $this->controller->delete(1);
    }

    public function testDeleteCategoryNotFound()
    {
        $id = -1;
        $this->mockCategory->method('delete')->with($id)->willReturn(0);

        $this->expectOutputString(json_encode(["message" => "Categoria não encontrada para exclusão."]));
        $this->controller->delete($id);
        $this->assertEquals(404, http_response_code());
    }

    public function testDeleteCategoryInvalidId()
    {
        $id = 'id inválido';
        $this->mockCategory->method('delete')->with($id)->willReturn(0);

        $this->expectOutputString(json_encode(["message" => "ID inválido."]));
        $this->controller->delete($id);
        $this->assertEquals(400, http_response_code());
    }
}
