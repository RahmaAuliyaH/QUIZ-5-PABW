ALTER TABLE orders
    ADD CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    ADD CONSTRAINT fk_orders_product FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE SET NULL;

ALTER TABLE orders
    ADD COLUMN status ENUM('pending','paid','shipped','cancelled') DEFAULT 'pending';

ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE orders ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE products
    ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_product_id ON orders(product_id);

ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL;
