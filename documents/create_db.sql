CREATE TABLE tb_categories (
    id_category SERIAL PRIMARY KEY,
    description VARCHAR(255),
    entrance BOOLEAN
);

CREATE TABLE tb_users (
    id_user SERIAL PRIMARY KEY,
    name VARCHAR(150),
    login VARCHAR(60),
    password VARCHAR(255)
);

CREATE TABLE tb_operations (
    id_operation SERIAL PRIMARY KEY,
    value FLOAT,
    date DATE,
    id_user INT,
    id_category INT,
    FOREIGN KEY (id_user) REFERENCES tb_users(id_user),
    FOREIGN KEY (id_category) REFERENCES tb_categories(id_category)
);