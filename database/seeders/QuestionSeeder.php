<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        // Easy Questions (Basic SELECT, WHERE, ORDER BY)
        DB::table('questions')->insert([
            [
                'title' => 'List All Products',
                'description' => 'Write a query to select all products with their names and prices.',
                'difficulty' => 'easy',
                'category' => 'basic_select',
                'expected_sql' => 'SELECT name, price FROM products;',
                'expected_laravel' => 'Product::select(\'name\', \'price\')->get();',
                'points' => 5,
                'created_at' => now(),
            ],
            [
                'title' => 'Find Expensive Products',
                'description' => 'Find all products with price greater than $100.',
                'difficulty' => 'easy',
                'category' => 'basic_where',
                'expected_sql' => 'SELECT * FROM products WHERE price > 100;',
                'expected_laravel' => 'Product::where(\'price\', \'>\', 100)->get();',
                'points' => 5,
                'created_at' => now(),
            ],
            [
                'title' => 'Sort Products by Price',
                'description' => 'Get all products ordered by price from highest to lowest.',
                'difficulty' => 'easy',
                'category' => 'basic_order',
                'expected_sql' => 'SELECT * FROM products ORDER BY price DESC;',
                'expected_laravel' => 'Product::orderBy(\'price\', \'desc\')->get();',
                'points' => 5,
                'created_at' => now(),
            ],
            [
                'title' => 'Count Total Products',
                'description' => 'Count how many products are in the database.',
                'difficulty' => 'easy',
                'category' => 'basic_aggregate',
                'expected_sql' => 'SELECT COUNT(*) as total_products FROM products;',
                'expected_laravel' => 'Product::count();',
                'points' => 5,
                'created_at' => now(),
            ],
            [
                'title' => 'Find Products by Brand',
                'description' => 'Find all Apple products.',
                'difficulty' => 'easy',
                'category' => 'basic_where',
                'expected_sql' => 'SELECT * FROM products WHERE brand = \'Apple\';',
                'expected_laravel' => 'Product::where(\'brand\', \'Apple\')->get();',
                'points' => 5,
                'created_at' => now(),
            ],
        ]);

        // Medium Questions (JOINs, GROUP BY, Subqueries)
        DB::table('questions')->insert([
            [
                'title' => 'Products with Categories',
                'description' => 'List all products with their category names.',
                'difficulty' => 'medium',
                'category' => 'joins',
                'expected_sql' => 'SELECT p.name as product_name, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id;',
                'expected_laravel' => 'Product::join(\'categories\', \'products.category_id\', \'=\', \'categories.id\')->select(\'products.name as product_name\', \'categories.name as category_name\')->get();',
                'points' => 10,
                'created_at' => now(),
            ],
            [
                'title' => 'Products per Category',
                'description' => 'Count how many products are in each category.',
                'difficulty' => 'medium',
                'category' => 'group_by',
                'expected_sql' => 'SELECT c.name, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id, c.name;',
                'expected_laravel' => 'Category::leftJoin(\'products\', \'categories.id\', \'=\', \'products.category_id\')->groupBy(\'categories.id\', \'categories.name\')->selectRaw(\'categories.name, COUNT(products.id) as product_count\')->get();',
                'points' => 15,
                'created_at' => now(),
            ],
            [
                'title' => 'Average Price by Category',
                'description' => 'Calculate the average price of products in each category.',
                'difficulty' => 'medium',
                'category' => 'group_by_aggregate',
                'expected_sql' => 'SELECT c.name, AVG(p.price) as avg_price FROM categories c JOIN products p ON c.id = p.category_id GROUP BY c.id, c.name;',
                'expected_laravel' => 'Category::join(\'products\', \'categories.id\', \'=\', \'products.category_id\')->groupBy(\'categories.id\', \'categories.name\')->selectRaw(\'categories.name, AVG(products.price) as avg_price\')->get();',
                'points' => 15,
                'created_at' => now(),
            ],
            [
                'title' => 'Orders with Customer Info',
                'description' => 'Show order ID, customer name, and total amount for all orders.',
                'difficulty' => 'medium',
                'category' => 'basic_join',
                'expected_sql' => 'SELECT id, customer_name, total_amount FROM orders;',
                'expected_laravel' => 'Order::select(\'id\', \'customer_name\', \'total_amount\')->get();',
                'points' => 10,
                'created_at' => now(),
            ],
            [
                'title' => 'Products Above Average Price',
                'description' => 'Find products that cost more than the average product price.',
                'difficulty' => 'medium',
                'category' => 'subquery',
                'expected_sql' => 'SELECT * FROM products WHERE price > (SELECT AVG(price) FROM products);',
                'expected_laravel' => 'Product::whereRaw(\'price > (SELECT AVG(price) FROM products)\')->get();',
                'points' => 15,
                'created_at' => now(),
            ],
        ]);

        // Hard Questions (Complex JOINs, Window Functions, CTEs)
        DB::table('questions')->insert([
            [
                'title' => 'Complete Order Details',
                'description' => 'Show order ID, customer name, product name, quantity, and total for each order item.',
                'difficulty' => 'hard',
                'category' => 'complex_joins',
                'expected_sql' => 'SELECT o.id as order_id, o.customer_name, p.name as product_name, oi.quantity, oi.total_price FROM orders o JOIN order_items oi ON o.id = oi.order_id JOIN products p ON oi.product_id = p.id;',
                'expected_laravel' => 'Order::join(\'order_items\', \'orders.id\', \'=\', \'order_items.order_id\')->join(\'products\', \'order_items.product_id\', \'=\', \'products.id\')->select(\'orders.id as order_id\', \'orders.customer_name\', \'products.name as product_name\', \'order_items.quantity\', \'order_items.total_price\')->get();',
                'points' => 20,
                'created_at' => now(),
            ],
            [
                'title' => 'Top Selling Products',
                'description' => 'Find the top 3 products by total quantity sold.',
                'difficulty' => 'hard',
                'category' => 'complex_aggregate',
                'expected_sql' => 'SELECT p.name, SUM(oi.quantity) as total_sold FROM products p JOIN order_items oi ON p.id = oi.product_id GROUP BY p.id, p.name ORDER BY total_sold DESC LIMIT 3;',
                'expected_laravel' => 'Product::join(\'order_items\', \'products.id\', \'=\', \'order_items.product_id\')->groupBy(\'products.id\', \'products.name\')->selectRaw(\'products.name, SUM(order_items.quantity) as total_sold\')->orderBy(\'total_sold\', \'desc\')->limit(3)->get();',
                'points' => 25,
                'created_at' => now(),
            ],
            [
                'title' => 'Category Revenue Analysis',
                'description' => 'Calculate total revenue for each category from completed orders.',
                'difficulty' => 'hard',
                'category' => 'complex_joins_aggregate',
                'expected_sql' => 'SELECT c.name as category, SUM(oi.total_price) as revenue FROM categories c JOIN products p ON c.id = p.category_id JOIN order_items oi ON p.id = oi.product_id JOIN orders o ON oi.order_id = o.id WHERE o.status = \'delivered\' GROUP BY c.id, c.name ORDER BY revenue DESC;',
                'expected_laravel' => 'Category::join(\'products\', \'categories.id\', \'=\', \'products.category_id\')->join(\'order_items\', \'products.id\', \'=\', \'order_items.product_id\')->join(\'orders\', \'order_items.order_id\', \'=\', \'orders.id\')->where(\'orders.status\', \'delivered\')->groupBy(\'categories.id\', \'categories.name\')->selectRaw(\'categories.name as category, SUM(order_items.total_price) as revenue\')->orderBy(\'revenue\', \'desc\')->get();',
                'points' => 30,
                'created_at' => now(),
            ],
            [
                'title' => 'Customer Order Statistics',
                'description' => 'For each customer, show total orders, total spent, and average order value.',
                'difficulty' => 'hard',
                'category' => 'advanced_aggregate',
                'expected_sql' => 'SELECT customer_name, customer_email, COUNT(*) as total_orders, SUM(total_amount) as total_spent, AVG(total_amount) as avg_order_value FROM orders GROUP BY customer_name, customer_email ORDER BY total_spent DESC;',
                'expected_laravel' => 'Order::groupBy(\'customer_name\', \'customer_email\')->selectRaw(\'customer_name, customer_email, COUNT(*) as total_orders, SUM(total_amount) as total_spent, AVG(total_amount) as avg_order_value\')->orderBy(\'total_spent\', \'desc\')->get();',
                'points' => 25,
                'created_at' => now(),
            ],
            [
                'title' => 'Products Never Ordered',
                'description' => 'Find products that have never been ordered.',
                'difficulty' => 'hard',
                'category' => 'advanced_joins',
                'expected_sql' => 'SELECT p.* FROM products p LEFT JOIN order_items oi ON p.id = oi.product_id WHERE oi.product_id IS NULL;',
                'expected_laravel' => 'Product::leftJoin(\'order_items\', \'products.id\', \'=\', \'order_items.product_id\')->whereNull(\'order_items.product_id\')->get();',
                'points' => 20,
                'created_at' => now(),
            ],
        ]);
    }
}