<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SqlPlaygroundSeeder extends Seeder
{
    public function run()
    {
        // Insert categories
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Electronics', 'description' => 'Electronic devices and accessories'],
            ['id' => 2, 'name' => 'Smartphones', 'description' => 'Mobile phones and accessories', 'parent_id' => 1],
            ['id' => 3, 'name' => 'Laptops', 'description' => 'Portable computers', 'parent_id' => 1],
            ['id' => 4, 'name' => 'Clothing', 'description' => 'Apparel and fashion items'],
            ['id' => 5, 'name' => 'Men\'s Clothing', 'description' => 'Clothing for men', 'parent_id' => 4],
            ['id' => 6, 'name' => 'Women\'s Clothing', 'description' => 'Clothing for women', 'parent_id' => 4],
            ['id' => 7, 'name' => 'Books', 'description' => 'Books and educational materials'],
            ['id' => 8, 'name' => 'Home & Garden', 'description' => 'Home improvement and garden supplies'],
        ]);

        // Insert products
        DB::table('products')->insert([
            ['id' => 1, 'name' => 'iPhone 14 Pro', 'description' => 'Latest Apple smartphone', 'price' => 999.99, 'stock_quantity' => 50, 'category_id' => 2, 'brand' => 'Apple'],
            ['id' => 2, 'name' => 'Samsung Galaxy S23', 'description' => 'Android smartphone', 'price' => 899.99, 'stock_quantity' => 75, 'category_id' => 2, 'brand' => 'Samsung'],
            ['id' => 3, 'name' => 'MacBook Pro', 'description' => '16-inch laptop', 'price' => 2499.99, 'stock_quantity' => 25, 'category_id' => 3, 'brand' => 'Apple'],
            ['id' => 4, 'name' => 'Dell XPS 13', 'description' => 'Ultra-portable laptop', 'price' => 1299.99, 'stock_quantity' => 30, 'category_id' => 3, 'brand' => 'Dell'],
            ['id' => 5, 'name' => 'Men\'s T-Shirt', 'description' => 'Cotton t-shirt', 'price' => 29.99, 'stock_quantity' => 100, 'category_id' => 5, 'brand' => 'Nike'],
            ['id' => 6, 'name' => 'Women\'s Dress', 'description' => 'Summer dress', 'price' => 79.99, 'stock_quantity' => 60, 'category_id' => 6, 'brand' => 'Zara'],
            ['id' => 7, 'name' => 'Programming Book', 'description' => 'Learn to code', 'price' => 49.99, 'stock_quantity' => 40, 'category_id' => 7, 'brand' => 'O\'Reilly'],
            ['id' => 8, 'name' => 'Garden Hose', 'description' => '50ft garden hose', 'price' => 39.99, 'stock_quantity' => 20, 'category_id' => 8, 'brand' => 'Home Depot'],
        ]);

        // Insert orders
        DB::table('orders')->insert([
            ['id' => 1, 'customer_name' => 'John Doe', 'customer_email' => 'john@example.com', 'order_date' => '2025-01-15', 'status' => 'delivered', 'total_amount' => 1029.98],
            ['id' => 2, 'customer_name' => 'Jane Smith', 'customer_email' => 'jane@example.com', 'order_date' => '2025-01-16', 'status' => 'shipped', 'total_amount' => 2499.99],
            ['id' => 3, 'customer_name' => 'Bob Johnson', 'customer_email' => 'bob@example.com', 'order_date' => '2025-01-17', 'status' => 'processing', 'total_amount' => 109.98],
            ['id' => 4, 'customer_name' => 'Alice Brown', 'customer_email' => 'alice@example.com', 'order_date' => '2025-01-18', 'status' => 'pending', 'total_amount' => 899.99],
            ['id' => 5, 'customer_name' => 'Charlie Wilson', 'customer_email' => 'charlie@example.com', 'order_date' => '2025-01-19', 'status' => 'delivered', 'total_amount' => 1349.98],
        ]);

        // Insert order items
        DB::table('order_items')->insert([
            ['order_id' => 1, 'product_id' => 1, 'quantity' => 1, 'unit_price' => 999.99, 'total_price' => 999.99],
            ['order_id' => 1, 'product_id' => 5, 'quantity' => 1, 'unit_price' => 29.99, 'total_price' => 29.99],
            ['order_id' => 2, 'product_id' => 3, 'quantity' => 1, 'unit_price' => 2499.99, 'total_price' => 2499.99],
            ['order_id' => 3, 'product_id' => 6, 'quantity' => 1, 'unit_price' => 79.99, 'total_price' => 79.99],
            ['order_id' => 3, 'product_id' => 5, 'quantity' => 1, 'unit_price' => 29.99, 'total_price' => 29.99],
            ['order_id' => 4, 'product_id' => 2, 'quantity' => 1, 'unit_price' => 899.99, 'total_price' => 899.99],
            ['order_id' => 5, 'product_id' => 4, 'quantity' => 1, 'unit_price' => 1299.99, 'total_price' => 1299.99],
            ['order_id' => 5, 'product_id' => 7, 'quantity' => 1, 'unit_price' => 49.99, 'total_price' => 49.99],
        ]);

        // Insert sample questions
        DB::table('questions')->insert([
            [
                'title' => 'Basic SELECT Query',
                'description' => 'Write a query to retrieve all products.',
                'instructions' => 'Use a SELECT statement to get all columns from the products table.',
                'expected_sql' => 'SELECT * FROM products;',
                'expected_result' => '8 rows containing all product information',
                'difficulty' => 'easy',
                'category' => 'Basic Queries',
                'points' => 5,
                'hint' => 'Use SELECT * FROM table_name to get all columns',
                'hint_penalty' => 1
            ],
            [
                'title' => 'Product Count by Category',
                'description' => 'Count how many products are in each category.',
                'instructions' => 'Write a query that shows category names and the count of products in each category.',
                'expected_sql' => 'SELECT c.name, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id, c.name;',
                'expected_result' => 'Category names with their product counts',
                'difficulty' => 'medium',
                'category' => 'Aggregation',
                'points' => 10,
                'hint' => 'Use JOIN to connect categories and products, then GROUP BY and COUNT',
                'hint_penalty' => 2
            ],
            [
                'title' => 'Top Customers by Order Value',
                'description' => 'Find customers with the highest total order amounts.',
                'instructions' => 'Write a query to show customer names and their total order amounts, ordered by total amount descending.',
                'expected_sql' => 'SELECT customer_name, SUM(total_amount) as total_orders FROM orders GROUP BY customer_name ORDER BY total_orders DESC;',
                'expected_result' => 'Customers ordered by their total purchase amounts',
                'difficulty' => 'medium',
                'category' => 'Aggregation',
                'points' => 15,
                'hint' => 'Use GROUP BY customer and SUM total_amount, then ORDER BY',
                'hint_penalty' => 3
            ]
        ]);
    }
}