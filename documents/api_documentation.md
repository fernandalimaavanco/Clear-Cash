# Documentação das Rotas ClearCash

## Rota de Login

### Login
- **Método:** POST
- **Endpoint:** `/login`
- **Descrição:** Realiza o login e retorna o token do usuário.
- **Autenticação:** Não necessária
- **Exemplo de JSON de Entrada:**

  ```json
  {
    "name": "FERNANDA AVANÇO",
    "login": "fefa"
  }
  ```

## Rotas de Usuários

### Criar Usuário
- **Método:** POST
- **Endpoint:** `/users`
- **Descrição:** Cria um novo usuário.
- **Autenticação:** Não necessária
- **Exemplo de JSON de Entrada:**

  ```json
  {
    "name": "FERNANDA AVANÇO",
    "login": "fefa"
  }
  ```

### Listar Usuários
- **Método:** GET
- **Endpoint:** `/users`
- **Descrição:** Lista todos os usuários.
- **Autenticação:** Necessária

### Obter Usuário por ID
- **Método:** GET
- **Endpoint:** `/users/{userId}`
- **Descrição:** Retorna os dados de um usuário específico.
- **Parâmetros de URL:**
  - `userId`: ID do usuário
- **Autenticação:** Necessária

### Atualizar Usuário
- **Método:** PUT
- **Endpoint:** `/users/{userId}`
- **Descrição:** Atualiza os dados de um usuário específico.
- **Parâmetros de URL:**
  - `userId`: ID do usuário
- **Exemplo de JSON de Entrada:**

  ```json
  {
    "name": "FERNANDA AVANÇO",
    "login": "fefa"
  }
  ```
- **Autenticação:** Necessária

### Excluir Usuário
- **Método:** DELETE
- **Endpoint:** `/users/{userId}`
- **Descrição:** Exclui um usuário específico.
- **Parâmetros de URL:**
  - `userId`: ID do usuário
- **Autenticação:** Necessária

---

## Rotas de Categorias

### Criar Categoria
- **Método:** POST
- **Endpoint:** `/categories`
- **Descrição:** Cria uma nova categoria.
- **Exemplo de JSON de Entrada:**

  ```json
  {
    "description": "Salário",
    "entrance": "true"
  }
  ```
- **Autenticação:** Necessária

### Listar Categorias
- **Método:** GET
- **Endpoint:** `/categories`
- **Descrição:** Lista todas as categorias.
- **Autenticação:** Necessária

### Obter Categoria por ID
- **Método:** GET
- **Endpoint:** `/categories/{categoryId}`
- **Descrição:** Retorna os dados de uma categoria específica.
- **Parâmetros de URL:**
  - `categoryId`: ID da categoria
- **Autenticação:** Necessária

### Atualizar Categoria
- **Método:** PUT
- **Endpoint:** `/categories/{categoryId}`
- **Descrição:** Atualiza os dados de uma categoria específica.
- **Parâmetros de URL:**
  - `categoryId`: ID da categoria
- **Exemplo de JSON de Entrada:**

  ```json
  {
    "description": "Salário",
    "entrance": "true"
  }
  ```
- **Autenticação:** Necessária

### Excluir Categoria
- **Método:** DELETE
- **Endpoint:** `/categories/{categoryId}`
- **Descrição:** Exclui uma categoria específica.
- **Parâmetros de URL:**
  - `categoryId`: ID da categoria
- **Autenticação:** Necessária

---

## Rotas de Operações

### Criar Operação
- **Método:** POST
- **Endpoint:** `/operations`
- **Descrição:** Cria uma nova operação.
- **Exemplo de JSON de Entrada:**

  ```json
  {
    "value": 20,
    "date": "2024-10-10",
    "id_category": 6
  }
  ```
- **Autenticação:** Necessária

### Listar Operações
- **Método:** GET
- **Endpoint:** `/operations`
- **Descrição:** Lista todas as operações.
- **Autenticação:** Necessária

### Obter Operação por ID
- **Método:** GET
- **Endpoint:** `/operations/{operationId}`
- **Descrição:** Retorna os dados de uma operação específica.
- **Parâmetros de URL:**
  - `operationId`: ID da operação
- **Autenticação:** Necessária

### Atualizar Operação
- **Método:** PUT
- **Endpoint:** `/operations/{operationId}`
- **Descrição:** Atualiza os dados de uma operação específica.
- **Parâmetros de URL:**
  - `operationId`: ID da operação
- **Exemplo de JSON de Entrada:**

  ```json
  {
    "value": 20,
    "date": "2024-10-10",
    "id_category": 6
  }
  ```
- **Autenticação:** Necessária

### Excluir Operação
- **Método:** DELETE
- **Endpoint:** `/operations/{operationId}`
- **Descrição:** Exclui uma operação específica.
- **Parâmetros de URL:**
  - `operationId`: ID da operação
- **Autenticação:** Necessária
```