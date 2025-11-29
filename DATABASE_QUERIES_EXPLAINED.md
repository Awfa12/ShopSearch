# Understanding Database Queries in Laravel

This document explains how database queries work in Laravel, focusing on the difference between individual inserts and batch inserts, and why performance matters.

---

## Table of Contents

1. [How Laravel Interacts with Databases](#how-laravel-interacts-with-databases)
2. [Individual Inserts (Eloquent)](#individual-inserts-eloquent)
3. [Batch Inserts (Raw SQL)](#batch-inserts-raw-sql)
4. [Performance Comparison](#performance-comparison)
5. [When to Use Each Approach](#when-to-use-each-approach)
6. [Real-World Example: Seeding 50,000 Products](#real-world-example-seeding-50000-products)

---

## How Laravel Interacts with Databases

Laravel provides two main ways to interact with databases:

### 1. Eloquent ORM (Object-Relational Mapping)
- **What it is:** A layer that translates PHP objects to database operations
- **Example:** `Product::create([...])`
- **Pros:** Clean, readable code; automatic relationships; model events
- **Cons:** More overhead; slower for bulk operations

### 2. Query Builder / Raw SQL
- **What it is:** Direct database queries without the ORM layer
- **Example:** `DB::table('products')->insert([...])`
- **Pros:** Faster; more control; better for bulk operations
- **Cons:** No model events; no automatic relationships

---

## Individual Inserts (Eloquent)

### How It Works

When you use `Product::create()`:

```php
Product::create([
    'name' => 'Red T-Shirt',
    'price' => 29.99,
    'category_id' => 1,
]);
```

**What happens behind the scenes:**

1. **Laravel creates a new Product model instance**
2. **Fills the model with data** (respects `$fillable`)
3. **Validates data** (if validation rules exist)
4. **Fires "creating" event** (if model observers exist)
5. **Executes SQL query:**
   ```sql
   INSERT INTO products (name, price, category_id, created_at, updated_at) 
   VALUES ('Red T-Shirt', 29.99, 1, '2025-11-29 10:00:00', '2025-11-29 10:00:00')
   ```
6. **Fires "created" event** (if model observers exist)
7. **Returns the model instance**

### The Problem: One Query Per Record

If you create 1000 products:

```php
for ($i = 0; $i < 1000; $i++) {
    Product::create([...]); // Each call = 1 database query
}
```

**Result:**
- 1000 separate `INSERT` queries
- 1000 network round-trips to database
- 1000 transaction operations
- 1000 model event fires

**Time taken:** ~30-60 seconds for 1000 products

---

## Batch Inserts (Raw SQL)

### How It Works

When you use `DB::table()->insert()`:

```php
DB::table('products')->insert([
    [
        'name' => 'Red T-Shirt',
        'price' => 29.99,
        'category_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Blue Jeans',
        'price' => 49.99,
        'category_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    // ... 998 more products
]);
```

**What happens behind the scenes:**

1. **Laravel builds a single SQL query:**
   ```sql
   INSERT INTO products (name, price, category_id, created_at, updated_at) 
   VALUES 
       ('Red T-Shirt', 29.99, 1, '2025-11-29 10:00:00', '2025-11-29 10:00:00'),
       ('Blue Jeans', 49.99, 1, '2025-11-29 10:00:00', '2025-11-29 10:00:00'),
       -- ... 998 more rows
   ```
2. **Sends ONE query to database**
3. **Database inserts all rows in one operation**
4. **Returns true/false (no model instances)**

### The Advantage: One Query for Many Records

If you insert 1000 products:

```php
$products = [];
for ($i = 0; $i < 1000; $i++) {
    $products[] = [...]; // Build array in memory
}
DB::table('products')->insert($products); // ONE database query
```

**Result:**
- 1 `INSERT` query (with 1000 rows)
- 1 network round-trip to database
- 1 transaction operation
- No model events

**Time taken:** ~1-2 seconds for 1000 products

---

## Performance Comparison

### Creating 1,000 Products

| Method | Queries | Time | Network Trips |
|--------|---------|------|---------------|
| `Product::create()` (loop) | 1,000 | ~30-60 sec | 1,000 |
| `DB::table()->insert()` (batch) | 1 | ~1-2 sec | 1 |

### Creating 50,000 Products

| Method | Queries | Time | Network Trips |
|--------|---------|------|---------------|
| `Product::factory(50000)->create()` | 50,000 | ~25-50 min | 50,000 |
| `DB::table()->insert()` (chunked) | 50 | ~5-10 min | 50 |

**Performance difference:** 5-10x faster with batch inserts!

---

## Why Batch Inserts Are Faster

### 1. **Network Overhead**

**Individual inserts:**
```
Application → Database: INSERT product 1
Database → Application: OK
Application → Database: INSERT product 2
Database → Application: OK
... (repeated 1000 times)
```

**Batch insert:**
```
Application → Database: INSERT products 1-1000
Database → Application: OK
```

**Saves:** 999 network round-trips!

### 2. **Database Optimization**

Databases are optimized for batch operations:
- Single transaction (faster than 1000 transactions)
- Better query planning
- Reduced locking overhead
- Better use of indexes

### 3. **No Model Overhead**

Eloquent models have overhead:
- Model instantiation
- Event firing
- Relationship loading
- Attribute casting
- Mass assignment protection

Raw queries skip all of this.

---

## When to Use Each Approach

### Use Eloquent (`Product::create()`) When:

✅ **Creating a few records** (1-10)
- Code is cleaner
- Need model events (e.g., send email when product created)
- Need relationships automatically loaded
- Need validation

**Example:**
```php
// User creates a product through admin panel
Product::create([
    'name' => $request->name,
    'price' => $request->price,
]);
// Fires events, sends notifications, etc.
```

### Use Batch Insert (`DB::table()->insert()`) When:

✅ **Creating many records** (100+)
- Performance is critical
- Don't need model events
- Don't need relationships
- Seeding test data

**Example:**
```php
// Seeding 50,000 products
$products = [];
for ($i = 0; $i < 50000; $i++) {
    $products[] = [...];
}
DB::table('products')->insert($products);
```

---

## Real-World Example: Seeding 50,000 Products

### Our ProductSeeder Approach

```php
// Get existing categories and brands (2 queries)
$categories = Category::pluck('id')->toArray();
$brands = Brand::pluck('id')->toArray();

// Generate products in memory (no database yet)
$products = [];
for ($i = 0; $i < 1000; $i++) {
    $products[] = $this->generateProductData($categories, $brands);
}

// Insert 1000 products in ONE query
DB::table('products')->insert($products);

// Repeat 50 times = 50 queries total
```

**Total queries:**
- 2 queries (get categories, get brands)
- 50 queries (insert 1000 products each)
- **Total: 52 queries**

**Time: ~5-10 minutes**

---

### If We Used Eloquent Instead

```php
// This would be VERY slow
for ($i = 0; $i < 50000; $i++) {
    Product::factory()->create();
}
```

**Total queries:**
- 50,000 queries (one per product)
- **Total: 50,000 queries**

**Time: ~25-50 minutes**

---

## Understanding SQL Queries

### Individual Insert Query

```sql
-- Query 1
INSERT INTO products (name, price, category_id, created_at, updated_at) 
VALUES ('Product 1', 29.99, 1, NOW(), NOW());

-- Query 2
INSERT INTO products (name, price, category_id, created_at, updated_at) 
VALUES ('Product 2', 39.99, 1, NOW(), NOW());

-- ... (repeated 49,998 more times)
```

**Database executes:** 50,000 separate operations

---

### Batch Insert Query

```sql
-- Single query with 1000 rows
INSERT INTO products (name, price, category_id, created_at, updated_at) 
VALUES 
    ('Product 1', 29.99, 1, NOW(), NOW()),
    ('Product 2', 39.99, 1, NOW(), NOW()),
    ('Product 3', 49.99, 1, NOW(), NOW()),
    -- ... (997 more rows)
    ('Product 1000', 99.99, 1, NOW(), NOW());
```

**Database executes:** 1 operation with 1000 rows

---

## Chunking: Why We Insert 1000 at a Time

### The Problem with Inserting All at Once

```php
// This might work, but...
DB::table('products')->insert($all50000Products);
```

**Issues:**
- **Memory:** Building array of 50,000 products uses lots of RAM
- **Query size:** SQL query becomes huge (several MB)
- **Database limits:** Some databases have query size limits
- **Timeout:** Query might take too long and timeout

### The Solution: Chunking

```php
$chunkSize = 1000;
for ($i = 0; $i < 50000; $i += $chunkSize) {
    $products = [];
    // Build 1000 products
    for ($j = 0; $j < $chunkSize; $j++) {
        $products[] = [...];
    }
    // Insert 1000 at once
    DB::table('products')->insert($products);
}
```

**Benefits:**
- ✅ Lower memory usage
- ✅ Smaller queries (faster)
- ✅ Progress tracking possible
- ✅ Can resume if interrupted

---

## Database Query Execution Flow

### Eloquent Query Flow

```
Your Code: Product::create([...])
    ↓
Eloquent Model
    ↓
Query Builder
    ↓
PDO (PHP Data Objects)
    ↓
MySQL Driver
    ↓
Network (TCP/IP)
    ↓
MySQL Server
    ↓
Execute INSERT
    ↓
Return Result
    ↓
Back through all layers
    ↓
Return Model Instance
```

**Each layer adds overhead!**

---

### Raw Query Flow

```
Your Code: DB::table('products')->insert([...])
    ↓
Query Builder
    ↓
PDO (PHP Data Objects)
    ↓
MySQL Driver
    ↓
Network (TCP/IP)
    ↓
MySQL Server
    ↓
Execute INSERT
    ↓
Return Result
```

**Fewer layers = less overhead!**

---

## Key Takeaways

1. **Eloquent is convenient but slower** for bulk operations
2. **Batch inserts are much faster** for many records
3. **Use Eloquent for business logic** (few records, need events)
4. **Use batch inserts for seeding** (many records, no events needed)
5. **Chunk large batches** to avoid memory/query size issues
6. **One query with 1000 rows** is faster than 1000 queries with 1 row each

---

## Summary

- **Individual inserts:** `Product::create()` = 1 query per record
- **Batch inserts:** `DB::table()->insert([...])` = 1 query for many records
- **Performance:** Batch inserts are 5-10x faster for large datasets
- **Trade-off:** Batch inserts skip model events but are much faster
- **Best practice:** Use batch inserts for seeding, Eloquent for business logic

---

**Remember:** The database is optimized for batch operations. When you need to insert many records, batch inserts are your friend!

