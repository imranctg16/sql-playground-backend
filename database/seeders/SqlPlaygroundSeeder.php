<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SqlPlaygroundSeeder extends Seeder
{
    public function run()
    {
        // Insert categories - matching 01-schema.sql exactly
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Electronics', 'description' => 'Electronic devices and accessories', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Computers', 'description' => 'Desktop and laptop computers', 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Smartphones', 'description' => 'Mobile phones and accessories', 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Clothing', 'description' => 'Apparel for all ages', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Men\'s Clothing', 'description' => 'Clothing for men', 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Women\'s Clothing', 'description' => 'Clothing for women', 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Books', 'description' => 'Physical and digital books', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Programming Books', 'description' => 'Books about software development', 'parent_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'name' => 'Fiction', 'description' => 'Fiction novels and stories', 'parent_id' => 7, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert products - matching 01-schema.sql exactly
        DB::table('products')->insert([
            ['id' => 1, 'name' => 'MacBook Pro 16"', 'description' => 'High-performance laptop for professionals', 'price' => 2499.99, 'stock_quantity' => 15, 'category_id' => 2, 'brand' => 'Apple', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Dell XPS 13', 'description' => 'Ultrabook with excellent display', 'price' => 1299.99, 'stock_quantity' => 25, 'category_id' => 2, 'brand' => 'Dell', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'iPhone 15 Pro', 'description' => 'Latest flagship smartphone', 'price' => 999.99, 'stock_quantity' => 50, 'category_id' => 3, 'brand' => 'Apple', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Samsung Galaxy S24', 'description' => 'Android flagship phone', 'price' => 899.99, 'stock_quantity' => 40, 'category_id' => 3, 'brand' => 'Samsung', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Men\'s Casual Shirt', 'description' => 'Comfortable cotton shirt', 'price' => 29.99, 'stock_quantity' => 100, 'category_id' => 5, 'brand' => 'Uniqlo', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Women\'s Summer Dress', 'description' => 'Light fabric perfect for summer', 'price' => 59.99, 'stock_quantity' => 75, 'category_id' => 6, 'brand' => 'Zara', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Clean Code Book', 'description' => 'Essential reading for developers', 'price' => 39.99, 'stock_quantity' => 200, 'category_id' => 8, 'brand' => 'Prentice Hall', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'The Great Gatsby', 'description' => 'Classic American novel', 'price' => 12.99, 'stock_quantity' => 150, 'category_id' => 9, 'brand' => 'Scribner', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'name' => 'Gaming Mouse', 'description' => 'High-precision gaming mouse', 'price' => 79.99, 'stock_quantity' => 80, 'category_id' => 1, 'brand' => 'Logitech', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'Wireless Headphones', 'description' => 'Noise-cancelling headphones', 'price' => 299.99, 'stock_quantity' => 60, 'category_id' => 1, 'brand' => 'Sony', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert orders - matching 01-schema.sql exactly
        DB::table('orders')->insert([
            ['id' => 1, 'customer_name' => 'John Doe', 'customer_email' => 'john@example.com', 'order_date' => '2024-01-15', 'status' => 'delivered', 'total_amount' => 2529.98, 'shipping_address' => '123 Main St, New York, NY', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'customer_name' => 'Jane Smith', 'customer_email' => 'jane@example.com', 'order_date' => '2024-01-16', 'status' => 'shipped', 'total_amount' => 999.99, 'shipping_address' => '456 Oak Ave, Los Angeles, CA', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'customer_name' => 'Bob Johnson', 'customer_email' => 'bob@example.com', 'order_date' => '2024-01-17', 'status' => 'processing', 'total_amount' => 89.98, 'shipping_address' => '789 Pine Rd, Chicago, IL', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'customer_name' => 'Alice Brown', 'customer_email' => 'alice@example.com', 'order_date' => '2024-01-18', 'status' => 'pending', 'total_amount' => 172.97, 'shipping_address' => '321 Elm St, Houston, TX', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'customer_name' => 'Charlie Wilson', 'customer_email' => 'charlie@example.com', 'order_date' => '2024-01-19', 'status' => 'delivered', 'total_amount' => 1379.98, 'shipping_address' => '654 Maple Dr, Phoenix, AZ', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'customer_name' => 'Diana Lee', 'customer_email' => 'diana@example.com', 'order_date' => '2024-01-20', 'status' => 'cancelled', 'total_amount' => 59.99, 'shipping_address' => '987 Cedar Ln, Philadelphia, PA', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'customer_name' => 'Eve Davis', 'customer_email' => 'eve@example.com', 'order_date' => '2024-01-21', 'status' => 'shipped', 'total_amount' => 379.98, 'shipping_address' => '147 Birch Ave, San Antonio, TX', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'customer_name' => 'Frank Miller', 'customer_email' => 'frank@example.com', 'order_date' => '2024-01-22', 'status' => 'processing', 'total_amount' => 52.98, 'shipping_address' => '258 Willow St, San Diego, CA', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'customer_name' => 'Grace Taylor', 'customer_email' => 'grace@example.com', 'order_date' => '2024-01-23', 'status' => 'delivered', 'total_amount' => 2499.99, 'shipping_address' => '369 Spruce Rd, Dallas, TX', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'customer_name' => 'Henry Chen', 'customer_email' => 'henry@example.com', 'order_date' => '2024-01-24', 'status' => 'pending', 'total_amount' => 139.98, 'shipping_address' => '741 Poplar Ave, San Jose, CA', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert order items - matching 01-schema.sql exactly
        DB::table('order_items')->insert([
            ['order_id' => 1, 'product_id' => 1, 'quantity' => 1, 'unit_price' => 2499.99, 'total_price' => 2499.99, 'created_at' => now()],
            ['order_id' => 1, 'product_id' => 5, 'quantity' => 1, 'unit_price' => 29.99, 'total_price' => 29.99, 'created_at' => now()],
            ['order_id' => 2, 'product_id' => 3, 'quantity' => 1, 'unit_price' => 999.99, 'total_price' => 999.99, 'created_at' => now()],
            ['order_id' => 3, 'product_id' => 5, 'quantity' => 3, 'unit_price' => 29.99, 'total_price' => 89.97, 'created_at' => now()],
            ['order_id' => 4, 'product_id' => 7, 'quantity' => 3, 'unit_price' => 39.99, 'total_price' => 119.97, 'created_at' => now()],
            ['order_id' => 4, 'product_id' => 8, 'quantity' => 4, 'unit_price' => 12.99, 'total_price' => 51.96, 'created_at' => now()],
            ['order_id' => 5, 'product_id' => 2, 'quantity' => 1, 'unit_price' => 1299.99, 'total_price' => 1299.99, 'created_at' => now()],
            ['order_id' => 5, 'product_id' => 9, 'quantity' => 1, 'unit_price' => 79.99, 'total_price' => 79.99, 'created_at' => now()],
            ['order_id' => 7, 'product_id' => 10, 'quantity' => 1, 'unit_price' => 299.99, 'total_price' => 299.99, 'created_at' => now()],
            ['order_id' => 7, 'product_id' => 9, 'quantity' => 1, 'unit_price' => 79.99, 'total_price' => 79.99, 'created_at' => now()],
            ['order_id' => 8, 'product_id' => 8, 'quantity' => 2, 'unit_price' => 12.99, 'total_price' => 25.98, 'created_at' => now()],
            ['order_id' => 8, 'product_id' => 7, 'quantity' => 1, 'unit_price' => 39.99, 'total_price' => 39.99, 'created_at' => now()],
            ['order_id' => 9, 'product_id' => 1, 'quantity' => 1, 'unit_price' => 2499.99, 'total_price' => 2499.99, 'created_at' => now()],
            ['order_id' => 10, 'product_id' => 4, 'quantity' => 1, 'unit_price' => 899.99, 'total_price' => 899.99, 'created_at' => now()],
        ]);
    }
}